<?php
include("conexion.php");

if (isset($_POST['iniciarSesion'])) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $password_md5 = md5($password);
    
    // ============================================
    // INTENTAR INICIAR SESIÓN COMO PROPIETARIO
    // ============================================
    $sql_propietario = "SELECT * FROM usuario_propietario WHERE correo = :correo";
    $stmt_prop = $conn->prepare($sql_propietario);
    $stmt_prop->execute([':correo' => $correo]);
    
    if ($stmt_prop->rowCount() > 0) {
        $usuario = $stmt_prop->fetch(PDO::FETCH_ASSOC);
        
        // VERIFICACIÓN MÚLTIPLE DE CONTRASEÑA
        $password_valida = false;
        
        // 1. Verificar MD5 completo (32 caracteres)
        if ($password_md5 === $usuario['password']) {
            $password_valida = true;
        }
        // 2. Verificar texto plano (para usuarios antiguos)
        elseif ($password === $usuario['password']) {
            $password_valida = true;
        }
        // 3. Verificar MD5 truncado (30 caracteres)
        elseif (strlen($usuario['password']) === 30 && substr($password_md5, 0, 30) === $usuario['password']) {
            $password_valida = true;
        }
        
        if ($password_valida) {
            if ($usuario['estado'] === 'inactivo') {
                header("Location: ../index.php?error=inactivo");
                exit;
            }
            
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario['correo'];
            $_SESSION['rol'] = 'propietario';
            $_SESSION['tipo_usuario'] = 'propietario';
            
            header("Location: ../usuario_propietario/index_propietario.php");
            exit;
        }
    }
    
    // ============================================
    // SI NO ES PROPIETARIO, INTENTAR COMO VISITANTE
    // ============================================
    $sql_visitante = "SELECT * FROM usuario_visitante WHERE correo = :correo";
    $stmt_vis = $conn->prepare($sql_visitante);
    $stmt_vis->execute([':correo' => $correo]);
    
    if ($stmt_vis->rowCount() > 0) {
        $usuario = $stmt_vis->fetch(PDO::FETCH_ASSOC);
        
        // VERIFICACIÓN MÚLTIPLE DE CONTRASEÑA (LO MISMO QUE ARRIBA)
        $password_valida = false;
        
        // 1. Verificar MD5 completo (32 caracteres)
        if ($password_md5 === $usuario['password']) {
            $password_valida = true;
        }
        // 2. Verificar texto plano (para usuarios antiguos)
        elseif ($password === $usuario['password']) {
            $password_valida = true;
        }
        // 3. Verificar MD5 truncado (30 caracteres)
        elseif (strlen($usuario['password']) === 30 && substr($password_md5, 0, 30) === $usuario['password']) {
            $password_valida = true;
        }
        
        if ($password_valida) {
            if ($usuario['estado'] === 'inactivo') {
                header("Location: ../index.php?error=inactivo");
                exit;
            }
            
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario['correo'];
            $_SESSION['rol'] = 'visitante';
            $_SESSION['tipo_usuario'] = 'visitante';
            
            header("Location: ../usuario_visitante/ixusuario.php");
            exit;
        }
    }
    
    // ============================================
    // SI NO ENCUENTRA EN NINGUNA TABLA O CONTRASEÑA INCORRECTA
    // ============================================
    header("Location: ../index.php?error=1");
    exit;
}
?>