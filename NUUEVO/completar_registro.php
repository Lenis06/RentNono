<?php
session_start();
include("conexion.php");

// Verificar si hay datos de Google en sesión o en POST
if (!isset($_SESSION['google_user_data']) && (!isset($_POST['google_id']) || empty($_POST['google_id']))) {
    header("Location: ../index.php?error=no_google_data");
    exit;
}

// Obtener datos - prioridad: POST > SESSION
$google_id = $_POST['google_id'] ?? $_SESSION['google_user_data']['google_id'] ?? '';
$nombre = trim($_POST['nombre'] ?? $_SESSION['google_user_data']['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? $_SESSION['google_user_data']['correo'] ?? '');
$tipo = $_POST['tipo'] ?? '';
$telefono = trim($_POST['telefono'] ?? '');
$foto = $_SESSION['google_user_data']['foto'] ?? '';

// Variables adicionales según tipo
$sexo = $_POST['sexo'] ?? '';
$dni = trim($_POST['dni'] ?? '');

// Validaciones básicas
if (empty($tipo) || empty($google_id) || empty($nombre) || empty($correo)) {
    header("Location: ../index.php?error=datos_incompletos");
    exit;
}

// Para propietario, el teléfono es obligatorio
if ($tipo == 'propietario' && empty($telefono)) {
    header("Location: ../index.php?error=telefono_requerido&tipo=propietario");
    exit;
}

try {
    // Verificar si el correo o google_id ya existen
    $usuario_existente = null;
    $tabla_existente = '';
    
    // Buscar por google_id primero (más específico)
    $stmt = $conn->prepare("SELECT id, nombre, correo, estado FROM usuario_visitante WHERE google_id = ?");
    $stmt->execute([$google_id]);
    if ($stmt->rowCount() > 0) {
        $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);
        $tabla_existente = 'visitante';
    } else {
        $stmt = $conn->prepare("SELECT id, nombre, correo, estado FROM usuario_propietario WHERE google_id = ?");
        $stmt->execute([$google_id]);
        if ($stmt->rowCount() > 0) {
            $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);
            $tabla_existente = 'propietario';
        }
    }
    
    // Si no existe por google_id, buscar por correo
    if (!$usuario_existente) {
        $stmt = $conn->prepare("SELECT id, nombre, correo, estado FROM usuario_visitante WHERE correo = ?");
        $stmt->execute([$correo]);
        if ($stmt->rowCount() > 0) {
            $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);
            $tabla_existente = 'visitante';
        } else {
            $stmt = $conn->prepare("SELECT id, nombre, correo, estado FROM usuario_propietario WHERE correo = ?");
            $stmt->execute([$correo]);
            if ($stmt->rowCount() > 0) {
                $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);
                $tabla_existente = 'propietario';
            }
        }
    }
    
    // Si el usuario ya existe
    if ($usuario_existente) {
        // Si ya tiene una cuenta pero de diferente tipo
        if ($tabla_existente != $tipo) {
            header("Location: ../index.php?error=tipo_cuenta_conflicto&tipo_existente=" . $tabla_existente);
            exit;
        }
        
        // Si está inactivo
        if ($usuario_existente['estado'] == 0) {
            header("Location: ../index.php?error=usuario_inactivo");
            exit;
        }
        
        // Actualizar datos de Google
        if ($tipo == 'visitante') {
            $update = $conn->prepare("UPDATE usuario_visitante SET google_id = ?, nombre = ?, foto = ? WHERE id = ?");
        } else {
            $update = $conn->prepare("UPDATE usuario_propietario SET google_id = ?, nombre = ?, foto = ? WHERE id = ?");
        }
        $update->execute([$google_id, $nombre, $foto, $usuario_existente['id']]);
        
        $usuario_id = $usuario_existente['id'];
    } else {
        // USUARIO NUEVO - Crear registro
        if ($tipo == 'visitante') {
            $sql = "INSERT INTO usuario_visitante (nombre, correo, google_id, foto, estado, rol) 
                    VALUES (?, ?, ?, ?, 1, 'visitante')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $correo, $google_id, $foto]);
        } else {
            // Para propietario
            $sql = "INSERT INTO usuario_propietario (nombre, sexo, dni, correo, telefono, google_id, foto, estado, rol) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1, 'propietario')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nombre, $sexo, $dni, $correo, $telefono, $google_id, $foto]);
        }
        
        $usuario_id = $conn->lastInsertId();
    }
    
    // INICIAR SESIÓN
    $_SESSION['id'] = $usuario_id;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['correo'] = $correo;
    $_SESSION['google_id'] = $google_id;
    $_SESSION['rol'] = $tipo;
    $_SESSION['foto'] = $foto;
    
    // Limpiar datos de Google de la sesión
    unset($_SESSION['google_user_data']);
    
    // Registrar en logs
    $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
    $log->execute([
        ':id' => $usuario_id,
        ':nombre' => $nombre,
        ':rol' => $tipo,
        ':accion' => 'Registro/Login con Google'
    ]);
    
    // Redirigir según el tipo
    if ($tipo == 'visitante') {
        header("Location: ../usuario_visitante/ixusuario.php");
    } else {
        header("Location: ../usuario_propietario/index_propietario.php");
    }
    exit;
    
} catch (PDOException $e) {
    // Error detallado para debugging
    error_log("Error en completar_registro.php: " . $e->getMessage());
    header("Location: ../index.php?error=registro_google&detalle=" . urlencode($e->getMessage()));
    exit;
}
?>