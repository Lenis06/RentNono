<?php

include("conexion.php");

/*REGISTRO USUARIO PROPIETARIO*/
if(isset($_POST['enviarRegistroPropietario'])) {
    $nombre = $_POST['nombre'];
    $sexo = $_POST['sexo'];
    $dni = $_POST['dni'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $pwd = $_POST['password'];

    $consultaSQL = $conn->query("INSERT INTO usuario_propietario(nombre, sexo, dni, correo, telefono, password) 
    VALUES ('$nombre','$sexo','$dni','$correo','$telefono','$pwd')");
    
    header("Location: index.php?registro=ok");
    exit;
}

<?php
include("conexion.php");

if (isset($_POST['registrar'])) {
    $nombre = $_POST['nombre'];
    $sexo = $_POST['sexo'];
    $dni = $_POST['dni'];
    $fecha_nac = $_POST['fecha_nac'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password'];

    try {
        $sql = "INSERT INTO usuario_propietario (nombre, sexo, dni, fecha_nac, correo, telefono, password)
                VALUES (:nombre, :sexo, :dni, :fecha_nac, :correo, :telefono, :password)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':sexo' => $sexo,
            ':dni' => $dni,
            ':fecha_nac' => $fecha_nac,
            ':correo' => $correo,
            ':telefono' => $telefono,
            ':password' => $password
        ]);

        header("Location: login.php?registro=ok");
        exit;
    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Propietario</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="contenedor-form">
        <h2>Crear cuenta de propietario</h2>
        <form action="" method="POST">
            <label>Nombre completo:</label>
            <input type="text" name="nombre" required>

            <label>Sexo:</label>
            <select name="sexo" required>
                <option value="Femenino">Femenino</option>
                <option value="Masculino">Masculino</option>
                <option value="Otro">Otro</option>
            </select>

            <label>DNI:</label>
            <input type="number" name="dni" required>

            <label>Fecha de nacimiento:</label>
            <input type="date" name="fecha_nac" required>

            <label>Correo electrónico:</label>
            <input type="email" name="correo" required>

            <label>Teléfono:</label>
            <input type="text" name="telefono" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit" name="registrar">Registrarse</button>
        </form>

        <p>¿Ya tenés cuenta? <a href="login.php">Iniciar sesión</a></p>
    </div>
</body>
</html>

/*REGISTRO USUARIO VISITANTE*/
if(isset($_POST['enviarRegistroVisitante'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $pwd = $_POST['password'];

    $consultaSQL = $conn->query("INSERT INTO usuario_visitante(nombre, correo, password) VALUES ('$nombre','$correo','$pwd')");
    
    // Después de insertar en la base de datos
    header("Location: ../usuario/ixusuario.php?registro=ok");
    exit();
}

?>
