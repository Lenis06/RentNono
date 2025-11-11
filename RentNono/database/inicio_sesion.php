<?php
include("conexion.php");
include("session.php");

if (isset($_POST['iniciarSesion'])) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Primero buscamos en usuario_visitante
    $stmt = $conn->prepare("SELECT id, nombre, correo, rol FROM usuario_visitante 
                            WHERE correo = :correo AND password = :password");
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        // Es un usuario visitante
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['rol'] = $usuario['rol'];

        header("Location: ../usuario_visitante/ixusuario.php");
        exit;
    }

    // Si no lo encontró, buscamos en usuario_propietario
    $stmt2 = $conn->prepare("SELECT id, nombre, correo,rol FROM usuario_propietario 
                             WHERE correo = :correo AND password = :password");
    $stmt2->bindParam(':correo', $correo);
    $stmt2->bindParam(':password', $password);
    $stmt2->execute();

    if ($stmt2->rowCount() === 1) {
        // Es un propietario
        $usuario = $stmt2->fetch(PDO::FETCH_ASSOC);
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['rol'] = 'propietario';

        header("Location: ../usuario_propietario/index_propietario.php");
        exit;
    }

    // Si no se encontró en ninguna tabla
    header("Location: ../index.php?error=1");
    exit();
}
?>
