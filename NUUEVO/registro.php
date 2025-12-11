<?php
include("conexion.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

/* ===============================
   REGISTRO DE USUARIO PROPIETARIO
================================ */
if (isset($_POST['enviarRegistroPropietario'])) {

    $nombre     = trim($_POST['nombre']);
    $sexo       = $_POST['sexo'];
    $dni        = trim($_POST['dni']);
    $correo     = trim($_POST['correo']);
    $telefono   = trim($_POST['telefono']);
    
    // Validaciones básicas
    if (empty($nombre) || empty($correo) || empty($dni) || empty($telefono)) {
        header("Location: ../index.php?error=campos_vacios");
        exit;
    }
    
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../index.php?error=correo_invalido");
        exit;
    }
    
    // Verificar si el correo ya existe
    try {
        // Verificar en visitantes
        $stmt = $conn->prepare("SELECT id FROM usuario_visitante WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header("Location: ../index.php?correo_existente=true");
            exit;
        }
        
        // Verificar en propietarios
        $stmt = $conn->prepare("SELECT id FROM usuario_propietario WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header("Location: ../index.php?correo_existente=true");
            exit;
        }
        
        // Verificar en admin
        $stmt = $conn->prepare("SELECT id FROM usuario_admin WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header("Location: ../index.php?correo_existente=true");
            exit;
        }

    } catch (PDOException $e) {
        header("Location: ../index.php?error=verificacion_correo");
        exit;
    }

    // Generar contraseña temporal
    $password_temporal = "123456";
    $password_hash = md5($password_temporal);

    try {
        $sql = "INSERT INTO usuario_propietario 
                (nombre, sexo, dni, correo, telefono, password)
                VALUES 
                (:nombre, :sexo, :dni, :correo, :telefono, :password)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre'     => $nombre,
            ':sexo'       => $sexo,
            ':dni'        => $dni,
            ':correo'     => $correo,
            ':telefono'   => $telefono,
            ':password'   => $password_hash
        ]);

        $usuario_id = $conn->lastInsertId();

        // Enviar correo con contraseña temporal
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rentnono.oficial@gmail.com';
            $mail->Password = 'ppig yvpn oaps lfec';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->setFrom('rentnono.oficial@gmail.com', 'RentNono');
            $mail->addAddress($correo, $nombre);
            
            $mail->isHTML(true);
            $mail->Subject = '¡Bienvenido a RentNono! - Tu cuenta ha sido creada';
            $mail->CharSet = 'UTF-8';
            
            // Crear enlace de cambio de contraseña
            $enlace_cambio = "http://" . $_SERVER['HTTP_HOST'] . "/RentNono/database/cambiar_contrasena_registro.php?email=" . urlencode($correo);
            
            $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #82b16d; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background-color: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
                    .button { display: inline-block; background-color: #82b16d; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .credenciales { background-color: #fff; border: 2px dashed #82b16d; padding: 15px; margin: 20px 0; border-radius: 5px; }
                    .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>RentNono</h1>
                        <p>Tu plataforma de alquileres en Nonogasta</p>
                    </div>
                    <div class='content'>
                        <h2>¡Hola " . htmlspecialchars($nombre) . "!</h2>
                        <p>Tu cuenta como propietario ha sido creada exitosamente en RentNono.</p>
                        
                        <div class='credenciales'>
                            <h3>Tus credenciales de acceso:</h3>
                            <p><strong>Correo:</strong> " . htmlspecialchars($correo) . "</p>
                            <p><strong>Contraseña temporal:</strong> <code>$password_temporal</code></p>
                            <p><em>Por seguridad, te recomendamos cambiar tu contraseña ahora.</em></p>
                        </div>
                        
                        <p style='text-align: center;'>
                            <a href='$enlace_cambio' class='button'>Cambiar Mi Contraseña</a>
                        </p>
                        
                        <p>O también puedes iniciar sesión directamente con la contraseña temporal:</p>
                        <p style='text-align: center;'>
                            <a href='http://" . $_SERVER['HTTP_HOST'] . "/RentNono/' class='button'>Iniciar Sesión en RentNono</a>
                        </p>
                        
                        <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                        
                        <div class='footer'>
                            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                            <p>&copy; 2025 RentNono. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            $mail->AltBody = "¡Hola $nombre!\n\nTu cuenta como propietario ha sido creada exitosamente en RentNono.\n\nTus credenciales de acceso:\nCorreo: $correo\nContraseña temporal: $password_temporal\n\nPara mayor seguridad, cambia tu contraseña en: $enlace_cambio\n\nO inicia sesión directamente en: http://" . $_SERVER['HTTP_HOST'] . "/RentNono/\n\nSaludos,\nEquipo RentNono";
            
            if ($mail->send()) {
                // Registrar en logs
                $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
                $log->execute([
                    ':id' => $usuario_id,
                    ':nombre' => $nombre,
                    ':rol' => 'propietario',
                    ':accion' => 'Registro exitoso'
                ]);
                
                // Redirigir directamente al panel del propietario
                session_start();
                $_SESSION['id'] = $usuario_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['correo'] = $correo;
                $_SESSION['rol'] = 'propietario';
                
                header("Location: ../usuario_propietario/index_propietario.php");
                exit;
                
            } else {
                // Si falla el correo, igual registrar al usuario y redirigir
                session_start();
                $_SESSION['id'] = $usuario_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['correo'] = $correo;
                $_SESSION['rol'] = 'propietario';
                
                header("Location: ../usuario_propietario/index_propietario.php?correo=error");
                exit;
            }

        } catch (Exception $e) {
            // Si falla el correo, igual registrar al usuario
            session_start();
            $_SESSION['id'] = $usuario_id;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['correo'] = $correo;
            $_SESSION['rol'] = 'propietario';
            
            header("Location: ../usuario_propietario/index_propietario.php?correo=error");
            exit;
        }

    } catch (PDOException $e) {
        header("Location: ../index.php?error=registro&detalle=" . urlencode($e->getMessage()));
        exit;
    }
}


/* ===============================
   REGISTRO DE USUARIO VISITANTE
================================ */
if (isset($_POST['enviarRegistroVisitante'])) {

    $nombre   = trim($_POST['nombre']);
    $correo   = trim($_POST['correo']);
    
    // Validaciones básicas
    if (empty($nombre) || empty($correo)) {
        header("Location: ../index.php?error=campos_vacios");
        exit;
    }
    
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../index.php?error=correo_invalido");
        exit;
    }
    
    // Verificar si el correo ya existe
    try {
        // Verificar en visitantes
        $stmt = $conn->prepare("SELECT id FROM usuario_visitante WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header("Location: ../index.php?correo_existente=true");
            exit;
        }
        
        // Verificar en propietarios
        $stmt = $conn->prepare("SELECT id FROM usuario_propietario WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header("Location: ../index.php?correo_existente=true");
            exit;
        }
        
        // Verificar en admin
        $stmt = $conn->prepare("SELECT id FROM usuario_admin WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header("Location: ../index.php?correo_existente=true");
            exit;
        }

    } catch (PDOException $e) {
        header("Location: ../index.php?error=verificacion_correo");
        exit;
    }

    // Generar contraseña temporal
    $password_temporal = "123456";
    $password_hash = md5($password_temporal);

    try {
        $sql = "INSERT INTO usuario_visitante (nombre, correo, password)
                VALUES (:nombre, :correo, :password)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombre'   => $nombre,
            ':correo'   => $correo,
            ':password' => $password_hash
        ]);

        $usuario_id = $conn->lastInsertId();

        // Enviar correo con contraseña temporal
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rentnono.oficial@gmail.com';
            $mail->Password = 'ppig yvpn oaps lfec';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->setFrom('rentnono.oficial@gmail.com', 'RentNono');
            $mail->addAddress($correo, $nombre);
            
            $mail->isHTML(true);
            $mail->Subject = '¡Bienvenido a RentNono! - Tu cuenta ha sido creada';
            $mail->CharSet = 'UTF-8';
            
            // Crear enlace de cambio de contraseña
            $enlace_cambio = "http://" . $_SERVER['HTTP_HOST'] . "/RentNono/database/cambiar_contrasena_registro.php?email=" . urlencode($correo);
            
            $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #82b16d; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background-color: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
                    .button { display: inline-block; background-color: #82b16d; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    .credenciales { background-color: #fff; border: 2px dashed #82b16d; padding: 15px; margin: 20px 0; border-radius: 5px; }
                    .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>RentNono</h1>
                        <p>Tu plataforma de alquileres en Nonogasta</p>
                    </div>
                    <div class='content'>
                        <h2>¡Hola " . htmlspecialchars($nombre) . "!</h2>
                        <p>Tu cuenta como visitante ha sido creada exitosamente en RentNono.</p>
                        
                        <div class='credenciales'>
                            <h3>Tus credenciales de acceso:</h3>
                            <p><strong>Correo:</strong> " . htmlspecialchars($correo) . "</p>
                            <p><strong>Contraseña temporal:</strong> <code>$password_temporal</code></p>
                            <p><em>Por seguridad, te recomendamos cambiar tu contraseña ahora.</em></p>
                        </div>
                        
                        <p style='text-align: center;'>
                            <a href='$enlace_cambio' class='button'>Cambiar Mi Contraseña</a>
                        </p>
                        
                        <p>O también puedes iniciar sesión directamente con la contraseña temporal:</p>
                        <p style='text-align: center;'>
                            <a href='http://" . $_SERVER['HTTP_HOST'] . "/RentNono/' class='button'>Iniciar Sesión en RentNono</a>
                        </p>
                        
                        <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                        
                        <div class='footer'>
                            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                            <p>&copy; 2025 RentNono. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            $mail->AltBody = "¡Hola $nombre!\n\nTu cuenta como visitante ha sido creada exitosamente en RentNono.\n\nTus credenciales de acceso:\nCorreo: $correo\nContraseña temporal: $password_temporal\n\nPara mayor seguridad, cambia tu contraseña en: $enlace_cambio\n\nO inicia sesión directamente en: http://" . $_SERVER['HTTP_HOST'] . "/RentNono/\n\nSaludos,\nEquipo RentNono";
            
            if ($mail->send()) {
                // Registrar en logs
                $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
                $log->execute([
                    ':id' => $usuario_id,
                    ':nombre' => $nombre,
                    ':rol' => 'visitante',
                    ':accion' => 'Registro exitoso'
                ]);
                
                // Redirigir directamente al panel del visitante
                session_start();
                $_SESSION['id'] = $usuario_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['correo'] = $correo;
                $_SESSION['rol'] = 'visitante';
                
                header("Location: ../usuario_visitante/ixusuario.php");
                exit;
                
            } else {
                // Si falla el correo, igual registrar al usuario y redirigir
                session_start();
                $_SESSION['id'] = $usuario_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['correo'] = $correo;
                $_SESSION['rol'] = 'visitante';
                
                header("Location: ../usuario_visitante/ixusuario.php?correo=error");
                exit;
            }

        } catch (Exception $e) {
            // Si falla el correo, igual registrar al usuario
            session_start();
            $_SESSION['id'] = $usuario_id;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['correo'] = $correo;
            $_SESSION['rol'] = 'visitante';
            
            header("Location: ../usuario_visitante/ixusuario.php?correo=error");
            exit;
        }

    } catch (PDOException $e) {
        header("Location: ../index.php?error=registro&detalle=" . urlencode($e->getMessage()));
        exit;
    }
}
?>