<?php
include("conexion.php");

if (isset($_POST['iniciarSesion'])) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $password_hash = md5($password);
    
    // Intentar iniciar sesión como propietario primero
    $sql_propietario = "SELECT * FROM usuario_propietario WHERE correo = :correo AND password = :password";
    $stmt_prop = $conn->prepare($sql_propietario);
    $stmt_prop->execute([':correo' => $correo, ':password' => $password_hash]);
    
    if ($stmt_prop->rowCount() > 0) {
        $usuario = $stmt_prop->fetch(PDO::FETCH_ASSOC);
        
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
    
    // Si no es propietario, intentar como visitante
    $sql_visitante = "SELECT * FROM usuario_visitante WHERE correo = :correo AND password = :password";
    $stmt_vis = $conn->prepare($sql_visitante);
    $stmt_vis->execute([':correo' => $correo, ':password' => $password_hash]);
    
    if ($stmt_vis->rowCount() > 0) {
        $usuario = $stmt_vis->fetch(PDO::FETCH_ASSOC);
        
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
    
    // Si no encuentra en ninguna tabla
    header("Location: ../index.php?error=1");
    exit;
}
?>
