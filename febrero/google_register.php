<?php
// google_register.php

session_start();

// Verificar si hay datos de Google en sesión
if (!isset($_SESSION['google_user_data'])) {
    header("Location: index.php");
    exit;
}

$google_data = $_SESSION['google_user_data'];
$email = $google_data['email'];
$name = $google_data['name'];
$google_id = $google_data['google_id'];

// Si el usuario ya eligió un tipo
if (isset($_POST['tipo_cuenta'])) {
    include("database/conexion.php");
    
    $tipo = $_POST['tipo_cuenta'];
    
    if ($tipo === 'propietario') {
        // Registrar como propietario
        $sql = "INSERT INTO usuario_propietario (nombre, correo, google_id, estado, password) 
                VALUES (:nombre, :correo, :google_id, 'activo', 'google_auth')";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([
            ':nombre' => $name,
            ':correo' => $email,
            ':google_id' => $google_id
        ])) {
            $usuario_id = $conn->lastInsertId();
            
            $_SESSION['id'] = $usuario_id;
            $_SESSION['nombre'] = $name;
            $_SESSION['correo'] = $email;
            $_SESSION['rol'] = 'propietario';
            $_SESSION['tipo_usuario'] = 'propietario';
            $_SESSION['google_login'] = true;
            
            unset($_SESSION['google_user_data']);
            header("Location: usuario_propietario/index_propietario.php");
            exit;
        }
        
    } elseif ($tipo === 'visitante') {
        // Registrar como visitante
        $sql = "INSERT INTO usuario_visitante (nombre, correo, google_id, estado, password) 
                VALUES (:nombre, :correo, :google_id, 'activo', 'google_auth')";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([
            ':nombre' => $name,
            ':correo' => $email,
            ':google_id' => $google_id
        ])) {
            $usuario_id = $conn->lastInsertId();
            
            $_SESSION['id'] = $usuario_id;
            $_SESSION['nombre'] = $name;
            $_SESSION['correo'] = $email;
            $_SESSION['rol'] = 'visitante';
            $_SESSION['tipo_usuario'] = 'visitante';
            $_SESSION['google_login'] = true;
            
            unset($_SESSION['google_user_data']);
            header("Location: usuario_visitante/ixusuario.php");
            exit;
        }
    }
    
    // Si llegamos aquí, hubo error
    $error = "Error al crear la cuenta. Intenta nuevamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completar Registro - RentNono</title>
    <link rel="stylesheet" href="estilos/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
        }
        
        .register-box {
            background: white;
            border-radius: 12px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #4CAF50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }
        
        .user-details h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .user-details p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .account-type {
            margin: 30px 0;
        }
        
        .type-option {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .type-option:hover {
            border-color: #4CAF50;
            background: #f8f9fa;
        }
        
        .type-option.selected {
            border-color: #4CAF50;
            background: #e8f5e9;
        }
        
        .type-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #4CAF50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .type-content h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .type-content p {
            margin: 0;
            color: #666;
            font-size: 13px;
            line-height: 1.4;
        }
        
        input[type="radio"] {
            display: none;
        }
        
        .btn-continue {
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
        }
        
        .btn-continue:hover {
            background: #45a049;
        }
        
        .btn-continue:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }
        
        .google-badge {
            display: inline-flex;
            align-items: center;
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 20px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
        
        .google-badge i {
            color: #DB4437;
            margin-right: 8px;
        }
        
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="google-badge">
                <i class="fab fa-google"></i>
                Conectado con Google
            </div>
            
            <h2 style="margin-bottom: 10px;">Completar Registro</h2>
            <p style="color: #666; margin-bottom: 30px;">Elige el tipo de cuenta que deseas crear</p>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($name, 0, 1)); ?>
                </div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($name); ?></h3>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>
            
            <form method="POST" id="registerForm">
                <div class="account-type">
                    <label class="type-option" for="type_propietario">
                        <input type="radio" id="type_propietario" name="tipo_cuenta" value="propietario">
                        <div class="type-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="type-content">
                            <h4>Propietario</h4>
                            <p>Para usuarios que desean publicar y administrar propiedades en alquiler.</p>
                        </div>
                    </label>
                    
                    <label class="type-option" for="type_visitante">
                        <input type="radio" id="type_visitante" name="tipo_cuenta" value="visitante">
                        <div class="type-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="type-content">
                            <h4>Visitante</h4>
                            <p>Para usuarios que buscan propiedades para alquilar o comprar.</p>
                        </div>
                    </label>
                </div>
                
                <button type="submit" class="btn-continue" id="btnContinue" disabled>
                    <i class="fas fa-check-circle"></i> Continuar
                </button>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const options = document.querySelectorAll('.type-option');
            const btnContinue = document.getElementById('btnContinue');
            
            options.forEach(option => {
                option.addEventListener('click', function() {
                    // Remover selección anterior
                    options.forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    
                    // Marcar como seleccionado
                    this.classList.add('selected');
                    
                    // Seleccionar radio button
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    
                    // Habilitar botón
                    btnContinue.disabled = false;
                });
            });
            
            // Manejar envío del formulario
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                const selected = document.querySelector('input[name="tipo_cuenta"]:checked');
                if (!selected) {
                    e.preventDefault();
                    alert('Por favor, selecciona un tipo de cuenta');
                }
            });
        });
    </script>
</body>
</html>