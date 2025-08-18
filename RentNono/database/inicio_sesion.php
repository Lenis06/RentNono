<?php
// Este archivo valida los datos ingresados en la ventana flotante de iniciar sesion y si esos datos
// exisiten en la base de datos, inicia la sesion

include("conexion.php");
include("session.php");

if (isset($_POST['iniciarSesion'])) {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, nombre, correo FROM usuario_visitante 
                            WHERE correo = :correo AND password = :password");
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['correo'] = $usuario['correo'];

        header("Location: ../index.php");
        exit;
    } else {
        header("Location: ../index.php?error=1");
        exit();
    }
 
}

?>
