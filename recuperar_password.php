<?php
session_start();
include("database/conexion.php");

// Si ya está logueado, redirigir al cambio de contraseña normal
if (isset($_SESSION['id'])) {
    header("Location: cambiar_password.php");
    exit;
}

$token = $_GET['token'] ?? '';
$mensaje = '';
$tipo_mensaje = ''; // success, error, info
$mostrar_formulario = false;

if (empty($token)) {
    $mensaje = "Token de recuperación no válido";
    $tipo_mensaje = "error";
} else {
    try {
        // Verificar token
        $sql = "SELECT * FROM tokens_recuperacion 
                WHERE token = :token 
                AND usado = 0 
                AND expiracion > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':token' => $token]);
        
        if ($stmt->rowCount() > 0) {
            $token_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $mostrar_formulario = true;
            
            // Procesar cambio de contraseña
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nueva_password'])) {
                $nueva_password = $_POST['nueva_password'];
                $confirmar_password = $_POST['confirmar_password'];
                
                if (empty($nueva_password) || empty($confirmar_password)) {
                    $mensaje = "Ambos campos son requeridos";
                    $tipo_mensaje = "error";
                } elseif ($nueva_password !== $confirmar_password) {
                    $mensaje = "Las contraseñas no coinciden";
                    $tipo_mensaje = "error";
                } elseif (strlen($nueva_password) < 6) {
                    $mensaje = "La contraseña debe tener al menos 6 caracteres";
                    $tipo_mensaje = "error";
                } else {
                    // Actualizar contraseña
                    $password_hash = md5($nueva_password);
                    $tabla = ($token_data['tipo_usuario'] === 'propietario') 
                           ? 'usuario_propietario' 
                           : 'usuario_visitante';
                    
                    $update_sql = "UPDATE $tabla SET password = :password WHERE id = :id";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->execute([
                        ':password' => $password_hash,
                        ':id' => $token_data['usuario_id']
                    ]);
                    
                    // Marcar token como usado
                    $token_sql = "UPDATE tokens_recuperacion SET usado = 1 WHERE id = :id";
                    $token_stmt = $conn->prepare($token_sql);
                    $token_stmt->execute([':id' => $token_data['id']]);
                    
                    // Obtener datos del usuario para iniciar sesión automática
                    $user_sql = "SELECT nombre, correo FROM $tabla WHERE id = :id";
                    $user_stmt = $conn->prepare($user_sql);
                    $user_stmt->execute([':id' => $token_data['usuario_id']]);
                    $usuario = $user_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Iniciar sesión automáticamente
                    if ($usuario) {
                        $_SESSION['id'] = $token_data['usuario_id'];
                        $_SESSION['nombre'] = $usuario['nombre'];
                        $_SESSION['correo'] = $usuario['correo'];
                        $_SESSION['rol'] = $token_data['tipo_usuario'];
                        $_SESSION['tipo_usuario'] = $token_data['tipo_usuario'];
                    }
                    
                    $mensaje = "¡Contraseña cambiada exitosamente! Has iniciado sesión automáticamente.";
                    $tipo_mensaje = "success";
                    $mostrar_formulario = false;
                    
                    // Redireccionar después de 3 segundos
                    echo '<script>
                        setTimeout(function() {
                            window.location.href = "' . ($token_data['tipo_usuario'] === 'propietario' ? '../usuario_propietario/index_propietario.php' : '../usuario_visitante/ixusuario.php') . '";
                        }, 3000);
                    </script>';
                }
            }
        } else {
            $mensaje = "El enlace de recuperación ha expirado o ya fue usado.";
            $tipo_mensaje = "error";
        }
        
    } catch (PDOException $e) {
        $mensaje = "Error en el sistema. Intente más tarde.";
        $tipo_mensaje = "error";
        error_log("Error recuperación password: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - RentNono</title>
    <link rel="stylesheet" href="estilos/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .password-recovery {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
        }
        
        .recovery-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        
        .recovery-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .recovery-header i {
            font-size: 48px;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        
        .recovery-header h1 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .recovery-header p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }
        
        .mensaje-recovery {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        
        .mensaje-recovery.success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .mensaje-recovery.error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .mensaje-recovery.info {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        
        .strength-bar {
            height: 4px;
            background: #ddd;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s;
        }
        
        .strength-weak { background: #f44336; }
        .strength-medium { background: #ff9800; }
        .strength-strong { background: #4CAF50; }
        
        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .auto-redirect {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="password-recovery">
        <div class="recovery-container">
            <div class="recovery-header">
                <i class="fas fa-lock"></i>
                <h1>Nueva Contraseña</h1>
                <p>Crea una nueva contraseña para tu cuenta</p>
            </div>
            
            <?php if ($mensaje): ?>
                <div class="mensaje-recovery <?= $tipo_mensaje ?>">
                    <i class="fas fa-<?= $tipo_mensaje == 'success' ? 'check-circle' : ($tipo_mensaje == 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                    <?= $mensaje ?>
                    <?php if ($tipo_mensaje == 'success' && !$mostrar_formulario): ?>
                        <div class="auto-redirect">
                            <i class="fas fa-spinner fa-spin"></i> Redirigiendo en 3 segundos...
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($mostrar_formulario): ?>
            <form method="POST" class="modal-formulario">
                <div class="input-grupo">
                    <label for="nueva_password">
                        <i class="fas fa-key"></i> Nueva Contraseña *
                    </label>
                    <div class="password-container">
                        <input type="password" id="nueva_password" name="nueva_password" 
                               placeholder="Mínimo 6 caracteres" required minlength="6">
                        <button type="button" class="btn-ver-password" data-target="nueva_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div id="strengthText">Seguridad: <span id="strengthLevel">Baja</span></div>
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthBar"></div>
                        </div>
                    </div>
                </div>
                
                <div class="input-grupo">
                    <label for="confirmar_password">
                        <i class="fas fa-key"></i> Confirmar Nueva Contraseña *
                    </label>
                    <div class="password-container">
                        <input type="password" id="confirmar_password" name="confirmar_password" 
                               placeholder="Repite la nueva contraseña" required minlength="6">
                        <button type="button" class="btn-ver-password" data-target="confirmar_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="passwordMatch" style="font-size: 12px; margin-top: 5px;"></div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Cambiar Contraseña
                </button>
            </form>
            <?php endif; ?>
            
            <a href="../index.php" class="login-link">
                <i class="fas fa-arrow-left"></i> Volver al inicio
            </a>
        </div>
    </div>
    
    <script>
    // Mostrar/ocultar contraseña
    document.querySelectorAll('.btn-ver-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Validar fortaleza de contraseña
    const nuevaPassword = document.getElementById('nueva_password');
    const confirmarPassword = document.getElementById('confirmar_password');
    const strengthText = document.getElementById('strengthLevel');
    const strengthBar = document.getElementById('strengthBar');
    const passwordMatch = document.getElementById('passwordMatch');
    
    if (nuevaPassword) {
        nuevaPassword.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Longitud
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            
            // Complejidad
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Actualizar UI
            let level = '';
            let width = '0%';
            let colorClass = '';
            
            if (strength <= 2) {
                level = 'Débil';
                width = '33%';
                colorClass = 'strength-weak';
            } else if (strength <= 4) {
                level = 'Media';
                width = '66%';
                colorClass = 'strength-medium';
            } else {
                level = 'Fuerte';
                width = '100%';
                colorClass = 'strength-strong';
            }
            
            strengthText.textContent = level;
            strengthBar.style.width = width;
            strengthBar.className = 'strength-fill ' + colorClass;
        });
    }
    
    // Verificar que las contraseñas coincidan
    if (nuevaPassword && confirmarPassword) {
        function checkPasswordMatch() {
            if (confirmarPassword.value && nuevaPassword.value) {
                if (confirmarPassword.value === nuevaPassword.value) {
                    passwordMatch.innerHTML = '<i class="fas fa-check" style="color: #4CAF50;"></i> Las contraseñas coinciden';
                    passwordMatch.style.color = '#4CAF50';
                } else {
                    passwordMatch.innerHTML = '<i class="fas fa-times" style="color: #f44336;"></i> Las contraseñas no coinciden';
                    passwordMatch.style.color = '#f44336';
                }
            } else {
                passwordMatch.textContent = '';
            }
        }
        
        nuevaPassword.addEventListener('input', checkPasswordMatch);
        confirmarPassword.addEventListener('input', checkPasswordMatch);
    }
    </script>
</body>
</html>