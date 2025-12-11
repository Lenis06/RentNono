<?php
include("conexion.php");
include("session.php");

if (isset($_POST['iniciarSesion'])) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $password_hash = md5($password); // Convertir a md5 para comparar

    // Primero verificamos si el correo existe
    $correo_existe = false;
    $contrasena_correcta = false;
    $usuario_encontrado = null;
    $tipo_usuario = '';
    
    // 🔹 Buscamos en usuario_visitante
    $stmt = $conn->prepare("SELECT id, nombre, correo, password, COALESCE(estado, 1) as estado FROM usuario_visitante
                            WHERE correo = :correo");
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $correo_existe = true;
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario['password'] === $password_hash) {
            $contrasena_correcta = true;
            $usuario_encontrado = $usuario;
            $tipo_usuario = 'visitante';
        }
    }
    
    // 🔹 Buscamos en usuario_propietario
    if (!$contrasena_correcta) {
        $stmt2 = $conn->prepare("SELECT id, nombre, correo, password, COALESCE(estado, 1) as estado FROM usuario_propietario
                                 WHERE correo = :correo");
        $stmt2->bindParam(':correo', $correo);
        $stmt2->execute();

        if ($stmt2->rowCount() === 1) {
            $correo_existe = true;
            $usuario = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario['password'] === $password_hash) {
                $contrasena_correcta = true;
                $usuario_encontrado = $usuario;
                $tipo_usuario = 'propietario';
            }
        }
    }
    
    // 🔹 Buscamos en usuario_admin
    if (!$contrasena_correcta) {
        $stmt3 = $conn->prepare("SELECT id, nombre, correo, password_hash, COALESCE(estado, 1) as estado FROM usuario_admin
                                 WHERE correo = :correo");
        $stmt3->bindParam(':correo', $correo);
        $stmt3->execute();

        if ($stmt3->rowCount() === 1) {
            $correo_existe = true;
            $usuario = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $usuario['password_hash'])) {
                $contrasena_correcta = true;
                $usuario_encontrado = $usuario;
                $tipo_usuario = 'admin';
            }
        }
    }
    
    // Manejo de errores específicos
    if (!$correo_existe) {
        header("Location: ../index.php?error=no_existe");
        exit();
    }
    
    if (!$contrasena_correcta) {
        header("Location: ../index.php?error=contrasena_incorrecta");
        exit();
    }
    
    // ✅ Usuario encontrado y contraseña correcta
    if ($usuario_encontrado['estado'] == 0) {
        header("Location: ../index.php?error=inactivo");
        exit;
    }
    
    // Configurar sesión según tipo de usuario
    if ($tipo_usuario === 'visitante') {
        $_SESSION['id'] = $usuario_encontrado['id'];
        $_SESSION['nombre'] = $usuario_encontrado['nombre'];
        $_SESSION['correo'] = $usuario_encontrado['correo'];
        $_SESSION['rol'] = 'visitante';
        
        $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
        $log->execute([
            ':id' => $_SESSION['id'],
            ':nombre' => $_SESSION['nombre'],
            ':rol' => 'visitante',
            ':accion' => 'Inicio de sesión'
        ]);

        header("Location: ../usuario_visitante/ixusuario.php");
        exit;
        
    } elseif ($tipo_usuario === 'propietario') {
        $_SESSION['id'] = $usuario_encontrado['id'];
        $_SESSION['nombre'] = $usuario_encontrado['nombre'];
        $_SESSION['correo'] = $usuario_encontrado['correo'];
        $_SESSION['rol'] = 'propietario';

        $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
        $log->execute([
            ':id' => $_SESSION['id'],
            ':nombre' => $_SESSION['nombre'],
            ':rol' => 'propietario',
            ':accion' => 'Inicio de sesión'
        ]);

        header("Location: ../usuario_propietario/index_propietario.php");
        exit;
        
    } elseif ($tipo_usuario === 'admin') {
        $_SESSION['admin_id'] = $usuario_encontrado['id'];
        $_SESSION['admin_nombre'] = $usuario_encontrado['nombre'];
        $_SESSION['rol'] = 'admin';

        $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
        $log->execute([
            ':id' => $_SESSION['admin_id'],
            ':nombre' => $_SESSION['admin_nombre'],
            ':rol' => 'admin',
            ':accion' => 'Inicio de sesión'
        ]);

        header("Location: ../admin/indexadmin.php");
        exit;
    }
}
?>