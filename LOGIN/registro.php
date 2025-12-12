<?php
include("conexion.php");
include("config_correos.php");

/* ================================
   REGISTRO DE USUARIO PROPIETARIO
================================ */
if (isset($_POST['registrar_propietario'])) {
    $nombre = trim($_POST['nombre']);
    $dni = trim($_POST['dni']);
    $correo = trim($_POST['correo']);
    $sexo = $_POST['sexo'];
    $telefono = trim($_POST['telefono'] ?? '');
    
    // Verificar si el correo ya existe
    $sql_check = "SELECT id FROM usuario_propietario WHERE correo = :correo";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([':correo' => $correo]);
    
    if ($stmt_check->rowCount() > 0) {
        $_SESSION['error_registro'] = "El correo ya está registrado";
        header("Location: ../index.php?error=correo_existe");
        exit;
    }
    
    // Verificar si el DNI ya existe
    $sql_check_dni = "SELECT id FROM usuario_propietario WHERE dni = :dni";
    $stmt_check_dni = $conn->prepare($sql_check_dni);
    $stmt_check_dni->execute([':dni' => $dni]);
    
    if ($stmt_check_dni->rowCount() > 0) {
        $_SESSION['error_registro'] = "El DNI ya está registrado";
        header("Location: ../index.php?error=dni_existe");
        exit;
    }
    
    // Generar contraseña temporal
    $password_temporal = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
    $password_hash = md5($password_temporal);
    
    // Insertar en la base de datos
    $sql = "INSERT INTO usuario_propietario (nombre, dni, correo, sexo, telefono, password, estado) 
            VALUES (:nombre, :dni, :correo, :sexo, :telefono, :password, 'activo')";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([
        ':nombre' => $nombre,
        ':dni' => $dni,
        ':correo' => $correo,
        ':sexo' => $sexo,
        ':telefono' => $telefono,
        ':password' => $password_hash
    ])) {
        $usuario_id = $conn->lastInsertId();
        
        // Enviar correo de bienvenida
        enviarCorreoBienvenida($correo, $nombre, $password_temporal, 'propietario');
        
        // Iniciar sesión automáticamente
        $_SESSION['id'] = $usuario_id;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['correo'] = $correo;
        $_SESSION['rol'] = 'propietario';
        $_SESSION['tipo_usuario'] = 'propietario';
        $_SESSION['password_temporal'] = $password_temporal;
        $_SESSION['registro_exitoso'] = true;
        $_SESSION['usuario_nombre'] = $nombre;
        
        // Redireccionar
        header("Location: ../usuario_propietario/index_propietario.php");
        exit;
    } else {
        $_SESSION['error_registro'] = "Error al registrar";
        header("Location: ../index.php?error=registro_fallido");
        exit;
    }
}

/* ================================
   REGISTRO DE USUARIO VISITANTE
================================ */
if (isset($_POST['registrar_visitante'])) {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    
    // Verificar si el correo ya existe
    $sql_check = "SELECT id FROM usuario_visitante WHERE correo = :correo";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([':correo' => $correo]);
    
    if ($stmt_check->rowCount() > 0) {
        $_SESSION['error_registro'] = "El correo ya está registrado";
        header("Location: ../index.php?error=correo_existe");
        exit;
    }
    
    // Generar contraseña temporal
    $password_temporal = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
    $password_hash = md5($password_temporal);
    
    // Insertar en la base de datos
    $sql = "INSERT INTO usuario_visitante (nombre, correo, password, estado) 
            VALUES (:nombre, :correo, :password, 'activo')";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([
        ':nombre' => $nombre,
        ':correo' => $correo,
        ':password' => $password_hash
    ])) {
        $usuario_id = $conn->lastInsertId();
        
        // Enviar correo de bienvenida
        enviarCorreoBienvenida($correo, $nombre, $password_temporal, 'visitante');
        
        // Iniciar sesión automáticamente
        $_SESSION['id'] = $usuario_id;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['correo'] = $correo;
        $_SESSION['rol'] = 'visitante';
        $_SESSION['tipo_usuario'] = 'visitante';
        $_SESSION['password_temporal'] = $password_temporal;
        $_SESSION['registro_exitoso'] = true;
        $_SESSION['usuario_nombre'] = $nombre;
        
        // Redireccionar
        header("Location: ../usuario_visitante/ixusuario.php");
        exit;
    } else {
        $_SESSION['error_registro'] = "Error al registrar";
        header("Location: ../index.php?error=registro_fallido");
        exit;
    }
}

/* ================================
   RECUPERACIÓN DE CONTRASEÑA
================================ */
if (isset($_POST['recuperar_password'])) {
    $correo = $_POST['correo'];
    
    header('Content-Type: application/json');
    
    try {
        // Buscar en ambas tablas
        $sql_propietario = "SELECT id, nombre FROM usuario_propietario WHERE correo = :correo AND estado = 'activo'";
        $stmt_prop = $conn->prepare($sql_propietario);
        $stmt_prop->execute([':correo' => $correo]);
        
        $sql_visitante = "SELECT id, nombre FROM usuario_visitante WHERE correo = :correo AND estado = 'activo'";
        $stmt_vis = $conn->prepare($sql_visitante);
        $stmt_vis->execute([':correo' => $correo]);
        
        if ($stmt_prop->rowCount() > 0) {
            $usuario = $stmt_prop->fetch(PDO::FETCH_ASSOC);
            $tipo = 'propietario';
        } elseif ($stmt_vis->rowCount() > 0) {
            $usuario = $stmt_vis->fetch(PDO::FETCH_ASSOC);
            $tipo = 'visitante';
        } else {
            echo json_encode([
                'success' => false,
                'message' => '❌ El correo no está registrado en nuestro sistema.'
            ]);
            exit;
        }
        
        // Crear tabla de tokens si no existe
        $create_table = "
        CREATE TABLE IF NOT EXISTS tokens_recuperacion (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            tipo_usuario VARCHAR(20) NOT NULL,
            token VARCHAR(64) NOT NULL UNIQUE,
            expiracion DATETIME NOT NULL,
            usado BOOLEAN DEFAULT FALSE,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($create_table);
        
        // Generar token
        $token = bin2hex(random_bytes(32));
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Guardar token
        $sql_token = "INSERT INTO tokens_recuperacion (usuario_id, tipo_usuario, token, expiracion) 
                      VALUES (:usuario_id, :tipo_usuario, :token, :expiracion)";
        $stmt_token = $conn->prepare($sql_token);
        $stmt_token->execute([
            ':usuario_id' => $usuario['id'],
            ':tipo_usuario' => $tipo,
            ':token' => $token,
            ':expiracion' => $expiracion
        ]);
        
        // Enviar correo
        $enlace = "http://" . $_SERVER['HTTP_HOST'] . "/rentnono/recuperar_password.php?token=" . $token;
        
        if (enviarCorreoRecuperacion($correo, $usuario['nombre'], $enlace)) {
            echo json_encode([
                'success' => true,
                'message' => '✅ Se ha enviado un enlace de recuperación a tu correo.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '❌ Error al enviar el correo. Intenta nuevamente.'
            ]);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => '❌ Error en el sistema. Intenta más tarde.'
        ]);
        error_log("Error recuperación: " . $e->getMessage());
    }
    exit;
}

/* ================================
   FUNCIONES DE CORREOS
================================ */

function enviarCorreoRecuperacion($destinatario, $nombre, $enlace) {
    return enviarCorreoRentNono(
        $destinatario, 
        $nombre, 
        'Recuperación de contraseña - RentNono', 
        plantillaRecuperacion($nombre, $enlace),
        "Hola $nombre,\n\nPara recuperar tu contraseña en RentNono, visita:\n$enlace\n\nEste enlace expira en 1 hora."
    );
}

function enviarCorreoBienvenida($correo, $nombre, $password_temporal, $tipo_usuario) {
    $mensajeHTML = plantillaBienvenida($nombre, $password_temporal, $tipo_usuario);
    $mensajeTexto = "¡Hola $nombre!\n\nTu registro como $tipo_usuario ha sido exitoso.\n\nTu contraseña temporal es: $password_temporal\n\nGuarda esta contraseña y cámbiala en tu primera sesión.\n\n¡Gracias por unirte a RentNono!";
    
    return enviarCorreoRentNono(
        $correo, 
        $nombre, 
        '¡Bienvenido a RentNono!', 
        $mensajeHTML, 
        $mensajeTexto
    );
}
?>