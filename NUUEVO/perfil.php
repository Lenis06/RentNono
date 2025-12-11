<?php
// perfil.php
session_start();
include("database/conexion.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['id'];
$user_rol = $_SESSION['rol'];
$mensaje = '';
$error = '';

// Obtener datos del usuario según su rol
if ($user_rol === 'visitante') {
    $stmt = $conn->prepare("SELECT * FROM usuario_visitante WHERE id = ?");
    $stmt->execute([$user_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $tipo_usuario = 'visitante';
} elseif ($user_rol === 'propietario') {
    $stmt = $conn->prepare("SELECT * FROM usuario_propietario WHERE id = ?");
    $stmt->execute([$user_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $tipo_usuario = 'propietario';
} else {
    header("Location: index.php");
    exit;
}

// Procesar actualización del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    
    // Validaciones básicas
    if (empty($nombre)) {
        $error = "El nombre es obligatorio";
    } else {
        try {
            // Preparar datos según tipo de usuario
            if ($user_rol === 'visitante') {
                $sql = "UPDATE usuario_visitante SET nombre = ?, telefono = ? WHERE id = ?";
                $params = [$nombre, $telefono, $user_id];
            } elseif ($user_rol === 'propietario') {
                $sexo = $_POST['sexo'] ?? '';
                $dni = trim($_POST['dni'] ?? '');
                
                $sql = "UPDATE usuario_propietario SET nombre = ?, sexo = ?, dni = ?, telefono = ? WHERE id = ?";
                $params = [$nombre, $sexo, $dni, $telefono, $user_id];
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            
            // Actualizar datos en sesión
            $_SESSION['nombre'] = $nombre;
            
            // Registrar en logs
            $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
            $log->execute([
                ':id' => $user_id,
                ':nombre' => $nombre,
                ':rol' => $user_rol,
                ':accion' => 'Actualizó perfil'
            ]);
            
            $mensaje = "Perfil actualizado correctamente";
            
        } catch (PDOException $e) {
            $error = "Error al actualizar el perfil: " . $e->getMessage();
        }
    }
}

// Obtener estadísticas del usuario
$estadisticas = [];
if ($user_rol === 'visitante') {
    // Estadísticas para visitante
    $stmt = $conn->prepare("SELECT COUNT(*) as total_favoritos FROM favoritos WHERE usuario_id = ?");
    $stmt->execute([$user_id]);
    $estadisticas['favoritos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_favoritos'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total_consultas FROM consultas WHERE usuario_id = ?");
    $stmt->execute([$user_id]);
    $estadisticas['consultas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_consultas'];
    
} elseif ($user_rol === 'propietario') {
    // Estadísticas para propietario
    $stmt = $conn->prepare("SELECT COUNT(*) as total_publicaciones FROM publicaciones WHERE propietario_id = ?");
    $stmt->execute([$user_id]);
    $estadisticas['publicaciones'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_publicaciones'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total_consultas FROM consultas WHERE propietario_id = ?");
    $stmt->execute([$user_id]);
    $estadisticas['consultas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_consultas'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total_vistas FROM publicaciones WHERE propietario_id = ?");
    $stmt->execute([$user_id]);
    $estadisticas['vistas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_vistas'] ?? 0;
}

// Obtener última actividad
$stmt = $conn->prepare("SELECT accion, fecha FROM logs_actividad WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 5");
$stmt->execute([$user_id]);
$ultimas_actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - RentNono</title>
    <link rel="stylesheet" href="estilos/estilo.css">
    <link rel="stylesheet" href="estilos/publicaciones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <style>
        /* ESTILOS ESPECÍFICOS PARA PERFIL */
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #82b16d, #5d8b4a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
        }
        
        .profile-avatar-upload {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px;
            text-align: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .profile-avatar-upload:hover {
            background: rgba(0,0,0,0.9);
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            font-size: 32px;
            color: #333;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .profile-email {
            color: #666;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .profile-role {
            display: inline-block;
            background: #82b16d;
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }
        
        .profile-stats {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            min-width: 100px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #82b16d;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* CONTENIDO PRINCIPAL */
        .profile-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }
        
        @media (max-width: 768px) {
            .profile-content {
                grid-template-columns: 1fr;
            }
        }
        
        /* FORMULARIO DE EDICIÓN */
        .edit-profile-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            font-size: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }
        
        .form-group .input-with-icon {
            position: relative;
        }
        
        .form-group .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            border-color: #82b16d;
            outline: none;
            box-shadow: 0 0 0 3px rgba(130, 177, 109, 0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }
        
        .btn-save {
            background: #82b16d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-save:hover {
            background: #5d8b4a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(93, 139, 74, 0.2);
        }
        
        .btn-cancel {
            background: #f8f9fa;
            color: #666;
            border: 2px solid #e0e0e0;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-cancel:hover {
            background: #e9ecef;
            color: #333;
        }
        
        /* PANEL LATERAL */
        .profile-sidebar {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            height: fit-content;
        }
        
        .sidebar-section {
            margin-bottom: 30px;
        }
        
        .sidebar-section h4 {
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-section h4 i {
            color: #82b16d;
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            padding: 12px 0;
            border-bottom: 1px solid #f8f8f8;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f0f5ee;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #82b16d;
            font-size: 14px;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-text {
            font-size: 14px;
            color: #444;
            margin-bottom: 3px;
        }
        
        .activity-time {
            font-size: 12px;
            color: #999;
        }
        
        /* TARJETA DE CUENTA */
        .account-card {
            background: linear-gradient(135deg, #f9fbf8, #f0f5ee);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e8f0e3;
        }
        
        .account-info {
            margin-bottom: 15px;
        }
        
        .account-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .account-info-label {
            color: #666;
        }
        
        .account-info-value {
            color: #333;
            font-weight: 500;
        }
        
        .account-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .account-btn {
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .account-btn-primary {
            background: #82b16d;
            color: white;
        }
        
        .account-btn-primary:hover {
            background: #5d8b4a;
        }
        
        .account-btn-secondary {
            background: #f8f9fa;
            color: #666;
            border: 1px solid #e0e0e0;
        }
        
        .account-btn-secondary:hover {
            background: #e9ecef;
            color: #333;
        }
        
        /* ALERTAS */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .profile-stats {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-save,
            .btn-cancel {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* BREADCRUMB */
        .breadcrumb {
            background: #f8f9fa;
            padding: 15px 0;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        
        .breadcrumb ul {
            display: flex;
            list-style: none;
            gap: 10px;
            padding: 0 20px;
            margin: 0;
        }
        
        .breadcrumb li {
            font-size: 14px;
            color: #666;
        }
        
        .breadcrumb li a {
            color: #82b16d;
            text-decoration: none;
        }
        
        .breadcrumb li a:hover {
            text-decoration: underline;
        }
        
        .breadcrumb li:not(:last-child)::after {
            content: "›";
            margin-left: 10px;
        }
        
        /* MODALES */
        .custom-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .custom-modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }

        .custom-modal-icon {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .custom-modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .custom-modal-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .custom-modal-btn-danger {
            background: #dc3545;
            color: white;
        }

        .custom-modal-btn-danger:hover {
            background: #c82333;
        }

        .custom-modal-btn-secondary {
            background: #6c757d;
            color: white;
        }

        .custom-modal-btn-secondary:hover {
            background: #5a6268;
        }

        .custom-modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .custom-modal-close:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <!-- BARRA DE NAVEGACION PRINCIPAL (COPIADA DE INDEX.PHP) -->
    <header class="main-header">
        <div class="container header-content">
            <h1 class="site-logo">
                <a href="index.php">RentNono</a>
            </h1>

            <nav class="main-nav">
                <ul>
                    <li><b href="#" class="btn-primary-small" href="index.php">Inicio</b></li>
                    <li><a href="explorador.php">Explorar Propiedades</a></li>
                    
                    <!-- MENÚ DE USUARIO O BOTÓN INICIAR SESIÓN -->
                    <?php if(isset($_SESSION['nombre'])): ?>
                        <li class="user-menu-container">
                            <div class="user-dropdown">
                                <button class="user-dropdown-btn">
                                    <?php if(!empty($_SESSION['foto'])): ?>
                                        <img src="<?php echo htmlspecialchars($_SESSION['foto']); ?>" 
                                             alt="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" 
                                             class="user-avatar">
                                    <?php else: ?>
                                        <div class="user-avatar-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                                </button>
                                
                                <div class="user-dropdown-menu">
                                    <div class="dropdown-header">
                                        <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>
                                        <span class="user-email"><?php echo htmlspecialchars($_SESSION['correo']); ?></span>
                                        <span class="user-role"><?php echo htmlspecialchars(ucfirst($_SESSION['rol'])); ?></span>
                                    </div>
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    <a href="<?php echo ($_SESSION['rol'] === 'visitante') ? 'usuario_visitante/ixusuario.php' : 'usuario_propietario/index_propietario.php'; ?>" 
                                       class="dropdown-item">
                                        <i class="fas fa-home"></i> Mi Panel
                                    </a>
                                    
                                    <a href="perfil.php" class="dropdown-item">
                                        <i class="fas fa-user-edit"></i> Editar Perfil
                                    </a>
                                    
                                    <?php if(isset($_SESSION['google_id'])): ?>
                                        <a href="database/cerrar_sesion_google.php" class="dropdown-item">
                                            <i class="fas fa-sync-alt"></i> Cambiar Cuenta Google
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="#" class="dropdown-item" onclick="mostrarModalCambiarPassword()">
                                        <i class="fas fa-key"></i> Cambiar Contraseña
                                    </a>
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    <a href="#" class="dropdown-item dropdown-danger" onclick="mostrarModalEliminarCuenta()">
                                        <i class="fas fa-user-slash"></i> Eliminar Cuenta
                                    </a>
                                    
                                    <a href="database/logout.php" class="dropdown-item dropdown-danger">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <li>
                            <a id="abrirLogin" class="btn-iniciar-sesion">Iniciar sesión</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
 
    <main class="profile-container">
        <!-- BREADCRUMB -->
        <div class="breadcrumb">
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="<?php echo $user_rol === 'visitante' ? 'usuario_visitante/ixusuario.php' : 'usuario_propietario/index_propietario.php'; ?>">Mi Panel</a></li>
                <li>Mi Perfil</li>
            </ul>
        </div>
        
        <!-- ALERTAS -->
        <?php if ($mensaje): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <!-- ENCABEZADO DEL PERFIL -->
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if (!empty($usuario['foto'])): ?>
                    <img src="<?php echo htmlspecialchars($usuario['foto']); ?>" alt="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                <?php else: ?>
                    <div class="profile-avatar-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['google_id'])): ?>
                    <div class="profile-avatar-upload" onclick="mostrarInfoAvatar()">
                        <i class="fas fa-camera"></i> Google
                    </div>
                <?php else: ?>
                    <div class="profile-avatar-upload" onclick="mostrarModalCambiarAvatar()">
                        <i class="fas fa-camera"></i> Cambiar
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="profile-info">
                <h1 class="profile-name"><?php echo htmlspecialchars($usuario['nombre']); ?></h1>
                <p class="profile-email"><?php echo htmlspecialchars($usuario['correo']); ?></p>
                <span class="profile-role"><?php echo htmlspecialchars(ucfirst($user_rol)); ?></span>
                
                <?php if (isset($_SESSION['google_id'])): ?>
                    <p style="color: #666; font-size: 14px; margin-top: 5px;">
                        <i class="fab fa-google" style="color: #4285f4;"></i> Cuenta vinculada con Google
                    </p>
                <?php endif; ?>
                
                <div class="profile-stats">
                    <?php if ($user_rol === 'visitante'): ?>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $estadisticas['favoritos']; ?></div>
                            <div class="stat-label">Favoritos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $estadisticas['consultas']; ?></div>
                            <div class="stat-label">Consultas</div>
                        </div>
                    <?php elseif ($user_rol === 'propietario'): ?>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $estadisticas['publicaciones']; ?></div>
                            <div class="stat-label">Publicaciones</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $estadisticas['consultas']; ?></div>
                            <div class="stat-label">Consultas</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo $estadisticas['vistas']; ?></div>
                            <div class="stat-label">Vistas</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- CONTENIDO PRINCIPAL -->
        <div class="profile-content">
            <!-- FORMULARIO DE EDICIÓN -->
            <div class="edit-profile-form">
                <form method="POST" action="" id="formPerfil">
                    <div class="form-section">
                        <h3><i class="fas fa-user-edit"></i> Información Personal</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre">Nombre completo *</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="nombre" name="nombre" 
                                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>" 
                                           required maxlength="100">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="correo">Correo electrónico</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" disabled style="background: #f8f9fa;">
                                </div>
                                <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
                                    <i class="fas fa-info-circle"></i> El correo no se puede modificar
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" id="telefono" name="telefono" 
                                           value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>"
                                           placeholder="Ej: 3825 40-7398" maxlength="13">
                                </div>
                            </div>
                            
                            <?php if ($user_rol === 'propietario'): ?>
                                <div class="form-group">
                                    <label for="dni">DNI (opcional)</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-id-card"></i>
                                        <input type="text" id="dni" name="dni" 
                                               value="<?php echo htmlspecialchars($usuario['dni'] ?? ''); ?>"
                                               maxlength="8" pattern="[0-9]{7,8}" placeholder="Número de DNI">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($user_rol === 'propietario'): ?>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="sexo">Sexo</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-venus-mars"></i>
                                        <select id="sexo" name="sexo">
                                            <option value="" <?php echo empty($usuario['sexo']) ? 'selected' : ''; ?>>Seleccionar</option>
                                            <option value="masculino" <?php echo ($usuario['sexo'] ?? '') == 'masculino' ? 'selected' : ''; ?>>Masculino</option>
                                            <option value="femenino" <?php echo ($usuario['sexo'] ?? '') == 'femenino' ? 'selected' : ''; ?>>Femenino</option>
                                            <option value="otro" <?php echo ($usuario['sexo'] ?? '') == 'otro' ? 'selected' : ''; ?>>Otro</option>
                                            <option value="prefiero_no_decirlo" <?php echo ($usuario['sexo'] ?? '') == 'prefiero_no_decirlo' ? 'selected' : ''; ?>>Prefiero no decirlo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="<?php echo $user_rol === 'visitante' ? 'usuario_visitante/ixusuario.php' : 'usuario_propietario/index_propietario.php'; ?>" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- PANEL LATERAL -->
            <div class="profile-sidebar">
                <!-- INFORMACIÓN DE LA CUENTA -->
                <div class="sidebar-section">
                    <h4><i class="fas fa-user-cog"></i> Cuenta</h4>
                    <div class="account-card">
                        <div class="account-info">
                            <div class="account-info-item">
                                <span class="account-info-label">Estado:</span>
                                <span class="account-info-value" style="color: #28a745;">
                                    <i class="fas fa-check-circle"></i> Activa
                                </span>
                            </div>
                            <div class="account-info-item">
                                <span class="account-info-label">Registrado desde:</span>
                                <span class="account-info-value">
                                    <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'] ?? date('Y-m-d'))); ?>
                                </span>
                            </div>
                            <div class="account-info-item">
                                <span class="account-info-label">Autenticación:</span>
                                <span class="account-info-value">
                                    <?php echo isset($_SESSION['google_id']) ? 'Google' : 'Email/Password'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="account-actions">
                            <?php if (!isset($_SESSION['google_id'])): ?>
                                <a href="#" class="account-btn account-btn-primary" onclick="mostrarModalCambiarPassword()">
                                    <i class="fas fa-key"></i> Cambiar Contraseña
                                </a>
                            <?php endif; ?>
                            
                            <a href="database/logout.php" class="account-btn account-btn-secondary">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- ÚLTIMAS ACTIVIDADES -->
                <div class="sidebar-section">
                    <h4><i class="fas fa-history"></i> Actividad Reciente</h4>
                    <ul class="activity-list">
                        <?php if (empty($ultimas_actividades)): ?>
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-info"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-text">Sin actividad registrada</div>
                                </div>
                            </li>
                        <?php else: ?>
                            <?php foreach ($ultimas_actividades as $actividad): ?>
                                <li class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-<?php echo strpos($actividad['accion'], 'Inicio') !== false ? 'sign-in-alt' : 
                                                           (strpos($actividad['accion'], 'Actualizó') !== false ? 'edit' : 
                                                           (strpos($actividad['accion'], 'Consulta') !== false ? 'search' : 'bell')); ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text"><?php echo htmlspecialchars($actividad['accion']); ?></div>
                                        <div class="activity-time"><?php echo date('d/m H:i', strtotime($actividad['fecha'])); ?></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>
    
    <!-- PIE DE PÁGINA (SIMPLIFICADO) -->
    <footer class="main-footer" style="margin-top: 50px;">
        <div class="container footer-content">
            <p>&copy; 2025 Rentnono. Todos los derechos reservados.</p>
            <ul class="footer-links">
                <li><a href="#">Términos y Condiciones</a></li>
                <li><a href="#">Política de Privacidad</a></li>
            </ul>
        </div>
    </footer>
    
    <!-- MODALES -->
    <div id="modalCambiarPassword" class="custom-modal" style="display: none;">
        <div class="custom-modal-content">
            <span class="custom-modal-close" onclick="cerrarModal('modalCambiarPassword')">&times;</span>
            <h3><i class="fas fa-key"></i> Cambiar Contraseña</h3>
            
            <form id="formCambiarPassword" onsubmit="return cambiarPasswordSubmit(event)">
                <div style="text-align: left; margin: 20px 0;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Contraseña actual:</label>
                    <div style="position: relative;">
                        <input type="password" id="currentPasswordModal" placeholder="Ingresa tu contraseña actual" 
                               required style="width: 100%; padding: 12px 40px 12px 15px; border: 2px solid #ddd; border-radius: 5px;">
                        <i class="fas fa-lock" style="position: absolute; right: 15px; top: 14px; color: #999;"></i>
                    </div>
                </div>
                
                <div style="text-align: left; margin: 20px 0;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nueva contraseña:</label>
                    <div style="position: relative;">
                        <input type="password" id="newPasswordModal" placeholder="Mínimo 6 caracteres" 
                               required minlength="6" style="width: 100%; padding: 12px 40px 12px 15px; border: 2px solid #ddd; border-radius: 5px;">
                        <i class="fas fa-lock" style="position: absolute; right: 15px; top: 14px; color: #999;"></i>
                    </div>
                </div>
                
                <div style="text-align: left; margin: 20px 0;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Confirmar nueva contraseña:</label>
                    <div style="position: relative;">
                        <input type="password" id="confirmPasswordModal" placeholder="Repite la nueva contraseña" 
                               required style="width: 100%; padding: 12px 40px 12px 15px; border: 2px solid #ddd; border-radius: 5px;">
                        <i class="fas fa-lock" style="position: absolute; right: 15px; top: 14px; color: #999;"></i>
                    </div>
                </div>
                
                <div class="custom-modal-buttons">
                    <button type="submit" class="custom-modal-btn" style="background: #82b16d; color: white;">
                        <i class="fas fa-save"></i> Cambiar Contraseña
                    </button>
                    <button type="button" class="custom-modal-btn custom-modal-btn-secondary" onclick="cerrarModal('modalCambiarPassword')">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="modalCambiarAvatar" class="custom-modal" style="display: none;">
        <div class="custom-modal-content">
            <span class="custom-modal-close" onclick="cerrarModal('modalCambiarAvatar')">&times;</span>
            <h3><i class="fas fa-camera"></i> Cambiar Foto de Perfil</h3>
            
            <div style="text-align: center; margin: 20px 0;">
                <div style="width: 150px; height: 150px; border-radius: 50%; background: #f8f9fa; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 50px; color: #ccc;">
                    <i class="fas fa-user"></i>
                </div>
                
                <p style="color: #666; margin-bottom: 20px;">
                    Para cambiar tu foto de perfil, debes actualizarla en tu cuenta de Google.
                </p>
                
                <a href="https://myaccount.google.com/photos" target="_blank" class="account-btn account-btn-primary" style="display: inline-flex; text-decoration: none;">
                    <i class="fab fa-google"></i> Ir a Google Photos
                </a>
            </div>
        </div>
    </div>
    
    <!-- SCRIPT PARA MANEJO DEL FORMULARIO -->
    <script>
        // Función para mostrar modal de cambio de contraseña
        function mostrarModalCambiarPassword() {
            document.getElementById('modalCambiarPassword').style.display = 'flex';
        }
        
        // Función para mostrar info del avatar (para usuarios Google)
        function mostrarInfoAvatar() {
            alert('Tu foto de perfil se sincroniza automáticamente con tu cuenta de Google. Para cambiarla, actualízala en Google Photos.');
        }
        
        // Función para mostrar modal de cambio de avatar
        function mostrarModalCambiarAvatar() {
            document.getElementById('modalCambiarAvatar').style.display = 'flex';
        }
        
        // Función para cerrar modales
        function cerrarModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Cerrar modales al hacer clic fuera
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('custom-modal')) {
                e.target.style.display = 'none';
            }
        });
        
        // Formatear teléfono automáticamente
        function formatearTelefono(input) {
            let valor = input.value.replace(/\D/g, "");
            if (valor.length > 4 && valor.length <= 6) {
                valor = valor.replace(/(\d{4})(\d+)/, "$1 $2");
            } else if (valor.length > 6) {
                valor = valor.replace(/(\d{4})(\d{2})(\d{0,4})/, "$1 $2-$3");
            }
            input.value = valor.trim();
        }
        
        // Aplicar formato al teléfono
        const telefonoInput = document.getElementById('telefono');
        if (telefonoInput) {
            telefonoInput.addEventListener("input", function() {
                formatearTelefono(this);
            });
            
            // Formatear valor inicial si existe
            if (telefonoInput.value) {
                formatearTelefono(telefonoInput);
            }
        }
        
        // Validación del formulario
        document.getElementById('formPerfil').addEventListener('submit', function(e) {
            let valido = true;
            
            // Validar nombre
            const nombre = document.getElementById('nombre');
            if (!nombre.value.trim()) {
                alert('El nombre es obligatorio');
                nombre.focus();
                valido = false;
            }
            
            // Validar DNI si es propietario y tiene valor
            const dniInput = document.getElementById('dni');
            if (dniInput && dniInput.value.trim()) {
                const dni = dniInput.value.trim();
                if (!/^\d{7,8}$/.test(dni)) {
                    alert('El DNI debe tener 7 u 8 dígitos numéricos');
                    dniInput.focus();
                    valido = false;
                }
            }
            
            if (!valido) {
                e.preventDefault();
            }
        });
        
        // Función para cambiar contraseña
        function cambiarPasswordSubmit(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPasswordModal').value;
            const newPassword = document.getElementById('newPasswordModal').value;
            const confirmPassword = document.getElementById('confirmPasswordModal').value;
            
            // Validaciones
            if (!currentPassword) {
                alert('Debes ingresar tu contraseña actual');
                return false;
            }
            
            if (newPassword.length < 6) {
                alert('La nueva contraseña debe tener al menos 6 caracteres');
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            // Enviar al servidor
            fetch('database/cambiar_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `password_actual=${encodeURIComponent(currentPassword)}&password_nueva=${encodeURIComponent(newPassword)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Contraseña cambiada exitosamente');
                    cerrarModal('modalCambiarPassword');
                    // Limpiar formulario
                    document.getElementById('formCambiarPassword').reset();
                } else {
                    alert(data.message || 'Error al cambiar contraseña');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            });
            
            return false;
        }
        
        // Mostrar mensajes de alerta temporalmente
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
        
        // Manejo del menú desplegable de usuario
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdown = document.querySelector('.user-dropdown');
            
            if (userDropdown) {
                const dropdownBtn = userDropdown.querySelector('.user-dropdown-btn');
                const dropdownMenu = userDropdown.querySelector('.user-dropdown-menu');
                
                // Alternar menú al hacer clic
                dropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('active');
                });
                
                // Cerrar menú al hacer clic fuera
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target)) {
                        userDropdown.classList.remove('active');
                    }
                });
                
                // Cerrar menú al hacer clic en un elemento
                dropdownMenu.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A') {
                        setTimeout(() => {
                            userDropdown.classList.remove('active');
                        }, 300);
                    }
                });
                
                // Cerrar menú con tecla ESC
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        userDropdown.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>