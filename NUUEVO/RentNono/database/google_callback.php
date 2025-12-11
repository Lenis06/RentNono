<?php
// Iniciar output buffering para evitar errores de headers
ob_start();
session_start();
include("conexion.php");

// Configuración de Google - REEMPLAZA CON TUS DATOS REALES
$google_client_id = '24939222054-j2nhbalkqbqk0hivb51kidq5duacpglk.apps.googleusercontent.com'; // Reemplazar con tu Client ID real
$google_client_secret = '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'; // Reemplazar con tu Client Secret real
$google_redirect_uri = 'http://localhost/RentNono/database/google_callback.php';

// Verificar que tenemos el código de autorización
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    try {
        // 1. Intercambiar código por token de acceso
        $token_url = "https://oauth2.googleapis.com/token";
        $token_data = [
            'code' => $code,
            'client_id' => $google_client_id,
            'client_secret' => $google_client_secret,
            'redirect_uri' => $google_redirect_uri,
            'grant_type' => 'authorization_code'
        ];
        
        // Usar cURL que es más confiable
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $token_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($token_response === FALSE) {
            throw new Exception('Error al conectar con Google para obtener token');
        }
        
        $token_info = json_decode($token_response, true);
        
        if (!isset($token_info['access_token'])) {
            // Mostrar error detallado
            echo "<!DOCTYPE html>
            <html>
            <head>
                <title>Error en Login - RentNono</title>
                <style>
                    body { font-family: Arial; padding: 40px; text-align: center; }
                    .error { background: #ffeaea; padding: 20px; border-radius: 10px; color: #c00; }
                    .debug { background: #f5f5f5; padding: 15px; margin-top: 20px; text-align: left; font-family: monospace; }
                </style>
            </head>
            <body>
                <h2>Error en el proceso de login</h2>
                <div class='error'>
                    <h3>No se pudo obtener el token de acceso de Google</h3>
                    <p>Código HTTP: $http_code</p>
                </div>
                <div class='debug'>
                    <h4>Respuesta de Google:</h4>
                    <pre>" . htmlspecialchars($token_response) . "</pre>
                </div>
                <p><a href='../index.php'>Volver al inicio</a></p>
            </body>
            </html>";
            exit;
        }
        
        // 2. Obtener información del usuario con el access token
        $userinfo_url = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $token_info['access_token'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userinfo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userinfo_response = curl_exec($ch);
        curl_close($ch);
        
        if ($userinfo_response === FALSE) {
            throw new Exception('Error al obtener información del usuario');
        }
        
        $user_info = json_decode($userinfo_response, true);
        
        if (!isset($user_info['id'])) {
            echo "<!DOCTYPE html>
            <html>
            <head>
                <title>Error en Login - RentNono</title>
            </head>
            <body>
                <h2>Error: Datos de usuario incompletos</h2>
                <pre>" . htmlspecialchars($userinfo_response) . "</pre>
                <p><a href='../index.php'>Volver al inicio</a></p>
            </body>
            </html>";
            exit;
        }
        
        // 3. Extraer datos del usuario
        $google_id = $user_info['id'];
        $nombre = $user_info['name'] ?? 'Usuario Google';
        $correo = $user_info['email'] ?? '';
        $foto = $user_info['picture'] ?? '';
        
        // 4. Verificar si el usuario ya existe
        $usuario_existente = null;
        $tipo_usuario = '';
        
        // Buscar en visitantes
        $stmt = $conn->prepare("SELECT id, nombre, correo, google_id, estado, rol FROM usuario_visitante WHERE google_id = ? OR correo = ?");
        $stmt->execute([$google_id, $correo]);
        
        if ($stmt->rowCount() > 0) {
            $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);
            $tipo_usuario = 'visitante';
        } else {
            // Buscar en propietarios
            $stmt = $conn->prepare("SELECT id, nombre, correo, google_id, estado, rol FROM usuario_propietario WHERE google_id = ? OR correo = ?");
            $stmt->execute([$google_id, $correo]);
            
            if ($stmt->rowCount() > 0) {
                $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);
                $tipo_usuario = 'propietario';
            }
        }
        
        if ($usuario_existente) {
            // Usuario existente - Verificar estado
            if ($usuario_existente['estado'] == 0) {
                header("Location: ../index.php?error=usuario_inactivo");
                exit;
            }
            
            // Actualizar información si es necesario
            if ($tipo_usuario === 'visitante') {
                $update = $conn->prepare("UPDATE usuario_visitante SET google_id = ?, nombre = ?, foto = ? WHERE id = ?");
            } else {
                $update = $conn->prepare("UPDATE usuario_propietario SET google_id = ?, nombre = ?, foto = ? WHERE id = ?");
            }
            $update->execute([$google_id, $nombre, $foto, $usuario_existente['id']]);
            
            // Iniciar sesión
            $_SESSION['id'] = $usuario_existente['id'];
            $_SESSION['nombre'] = $nombre;
            $_SESSION['correo'] = $correo;
            $_SESSION['google_id'] = $google_id;
            $_SESSION['rol'] = $usuario_existente['rol'];
            $_SESSION['foto'] = $foto;
            
            // Registrar en logs
            $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
            $log->execute([
                ':id' => $usuario_existente['id'],
                ':nombre' => $nombre,
                ':rol' => $usuario_existente['rol'],
                ':accion' => 'Inicio de sesión con Google'
            ]);
            
            // Redirigir según el rol
            ob_end_clean(); // Limpiar buffer antes de redirección
            
            if ($usuario_existente['rol'] === 'visitante') {
                header("Location: ../usuario_visitante/ixusuario.php");
            } else {
                header("Location: ../usuario_propietario/index_propietario.php");
            }
            exit;
            
        } else {
            // Usuario nuevo - Guardar datos en sesión y redirigir a completar registro
            $_SESSION['google_user_data'] = [
                'google_id' => $google_id,
                'nombre' => $nombre,
                'correo' => $correo,
                'foto' => $foto
            ];
            
            // Redirigir a completar registro
            ob_end_clean(); // Limpiar buffer antes de redirección
            header("Location: ../index.php?completar_registro=true");
            exit;
        }
        
    } catch (Exception $e) {
        // Mostrar error detallado
        ob_end_clean();
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Error en Login - RentNono</title>
            <style>
                body { font-family: Arial; padding: 40px; }
                .error { background: #ffeaea; padding: 20px; border-radius: 10px; color: #c00; }
            </style>
        </head>
        <body>
            <h2>Error en el proceso de login</h2>
            <div class='error'>
                <h3>" . htmlspecialchars($e->getMessage()) . "</h3>
                <p>Por favor, intenta nuevamente.</p>
            </div>
            <p><a href='../index.php'>Volver al inicio</a></p>
        </body>
        </html>";
        exit;
    }
    
} else {
    // No hay código, redirigir al inicio
    ob_end_clean();
    header("Location: ../index.php?error=no_code");
    exit;
}
?>