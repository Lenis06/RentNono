<?php
ob_start();
include("database/session.php");
include("database/publicaciones.php");
include("login.php");

// Verificar si hay que completar registro después de Google login
if (isset($_GET['completar_registro']) && $_GET['completar_registro'] == 'true' && isset($_SESSION['google_user_data'])) {
    // El JavaScript en login.php manejará esto
}

if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'visitante') {
        header("Location: usuario_visitante/ixusuario.php");
        exit;
    } elseif ($_SESSION['rol'] === 'propietario') {
        header("Location: usuario_propietario/index_propietario.php");
        exit;
    }
}

// Verificar si el usuario está logueado como visitante
$es_visitante = isset($_SESSION['rol']) && $_SESSION['rol'] === 'visitante';
$usuario_id = $_SESSION['id'] ?? null;

// Para la sección de publicaciones más visitadas, necesitamos verificar favoritos
include("database/conexion.php");
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentNono | Inicio</title>
    <link rel="stylesheet" href="estilos/estilo.css">
    <link rel="stylesheet" href="estilos/publicaciones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2d9a66f09.js" crossorigin="anonymous"></script>
    <style>
        /* ESTILOS PARA EL MENÚ DE USUARIO - AGREGADOS DIRECTAMENTE */
        .user-menu-container {
            position: relative;
            margin-left: 15px;
        }

        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            border: 2px solid #82b16d;
            border-radius: 30px;
            padding: 6px 15px;
            color: #333;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .user-dropdown-btn:hover {
            background-color: #f5f9f4;
            border-color: #5d8b4a;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e0e0e0;
        }

        .user-avatar-placeholder {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #82b16d, #5d8b4a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            border: 2px solid #e0e0e0;
        }

        .user-name {
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-arrow {
            font-size: 12px;
            transition: transform 0.3s ease;
            color: #666;
        }

        .user-dropdown-btn:hover .dropdown-arrow {
            color: #82b16d;
        }

        .user-dropdown.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        /* MENÚ DESPLEGABLE */
        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 280px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            border: 1px solid #e0e0e0;
        }

        .user-dropdown.active .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 20px;
            background: linear-gradient(135deg, #f9fbf8, #f0f5ee);
            border-radius: 12px 12px 0 0;
            border-bottom: 1px solid #e8f0e3;
        }

        .dropdown-header strong {
            display: block;
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
        }

        .user-email {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            word-break: break-all;
        }

        .user-role {
            display: inline-block;
            background: #82b16d;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dropdown-divider {
            height: 1px;
            background: #f0f0f0;
            margin: 8px 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: #444;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
            border-bottom: 1px solid #f9f9f9;
        }

        .dropdown-item:last-child {
            border-bottom: none;
            border-radius: 0 0 12px 12px;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #82b16d;
            padding-left: 25px;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            color: #777;
            font-size: 16px;
        }

        .dropdown-item:hover i {
            color: #82b16d;
        }

        .dropdown-danger {
            color: #dc3545 !important;
        }

        .dropdown-danger:hover {
            background-color: #fff5f5 !important;
            color: #c82333 !important;
        }

        .dropdown-danger i {
            color: #dc3545 !important;
        }

        /* Modal personalizado para acciones */
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

        /* Estilo para alertas */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 3000;
            max-width: 350px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>
</head>
<body>

    <!-- BARRA DE NAVEGACION PRINCIPAL -->
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
 
    <main>
        <!--SECCION DE PRESENTACION-->
        <section class="hero-section">
            <div class="hero-text-content">
                <h2>Encontrá tu hogar en Nonogasta</h2>
                <p>Una plataforma simple e intuitiva para que alquiles y des en alquiler tus objetos y propiedades de 
                    forma segura y eficiente.</p>              
        
        <!-- 🔍 BUSCADOR POR PRECIO -->
        <section class="buscador-precio container" style="margin-top:30px;">
            <h3>Filtrar por precio</h3>

            <div style="display:flex; gap:15px; align-items:center; flex-wrap:wrap;">
                <div>
                    <label>Precio mínimo</label>
                    <input type="number" id="precio_min" placeholder="Ej: 100000" style="padding:8px;">
                </div>

                <div>
                    <label>Precio máximo</label>
                    <input type="number" id="precio_max" placeholder="Ej: 300000" style="padding:8px;">
                </div>

                <button id="btnFiltrar" style="padding:10px 20px; cursor:pointer; background:#2d6cdf; border:none; color:white; border-radius:5px;">
                    Aplicar filtros
                </button>

                <button id="btnReset" style="padding:10px 20px; cursor:pointer; background:#777; border:none; color:white; border-radius:5px;">
                    Reiniciar
                </button>
            </div>
        </section>

        <section class="features-section container" style="margin-top:20px;">
            <h3>Publicaciones</h3>
            <div class="features-grid" id="gridIndex"></div>
            <p id="mensajeVacio" style="display:none; text-align:center; padding:20px;">
                No existen publicaciones en ese rango de precio.
            </p>
        </section>
        </section>
    </main>
    
    <!--PIE DE PAGINA-->
    <footer class="main-footer">
        <div class="container footer-content">
            <p>&copy; 2025 Rentnono. Todos los derechos reservados.</p>
            <ul class="footer-links">
                <li><a href="#">Términos y Condiciones</a></li>
                <li><a href="#">Política de Privacidad</a></li>
            </ul>
        </div>
    </footer>

    <!-- MODALES PARA ACCIONES DEL USUARIO -->
    <div id="modalCambiarPassword" class="custom-modal">
        <div class="custom-modal-content">
            <span class="custom-modal-close" onclick="cerrarModal('modalCambiarPassword')">&times;</span>
            <h3><i class="fas fa-key"></i> Cambiar Contraseña</h3>
            
            <form id="formCambiarPassword" onsubmit="return cambiarPasswordSubmit(event)">
                <div style="text-align: left; margin: 20px 0;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Contraseña actual:</label>
                    <div style="position: relative;">
                        <input type="password" id="currentPassword" placeholder="Ingresa tu contraseña actual" 
                               required style="width: 100%; padding: 10px 40px 10px 15px; border: 1px solid #ddd; border-radius: 5px;">
                        <i class="fas fa-lock" style="position: absolute; right: 15px; top: 12px; color: #999;"></i>
                    </div>
                </div>
                
                <div style="text-align: left; margin: 20px 0;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nueva contraseña:</label>
                    <div style="position: relative;">
                        <input type="password" id="newPassword" placeholder="Mínimo 6 caracteres" 
                               required minlength="6" style="width: 100%; padding: 10px 40px 10px 15px; border: 1px solid #ddd; border-radius: 5px;">
                        <i class="fas fa-lock" style="position: absolute; right: 15px; top: 12px; color: #999;"></i>
                    </div>
                </div>
                
                <div style="text-align: left; margin: 20px 0;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Confirmar nueva contraseña:</label>
                    <div style="position: relative;">
                        <input type="password" id="confirmPassword" placeholder="Repite la nueva contraseña" 
                               required style="width: 100%; padding: 10px 40px 10px 15px; border: 1px solid #ddd; border-radius: 5px;">
                        <i class="fas fa-lock" style="position: absolute; right: 15px; top: 12px; color: #999;"></i>
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

    <div id="modalEliminarCuenta" class="custom-modal">
        <div class="custom-modal-content">
            <span class="custom-modal-close" onclick="cerrarModal('modalEliminarCuenta')">&times;</span>
            <div class="custom-modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h3>¿Eliminar cuenta permanentemente?</h3>
            
            <div style="background: #fff5f5; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: left;">
                <p><strong>Esta acción es irreversible y eliminará:</strong></p>
                <ul style="margin-left: 20px; margin-bottom: 15px;">
                    <li>Tu perfil de usuario</li>
                    <li>Tus publicaciones (si eres propietario)</li>
                    <li>Tus favoritos (si eres visitante)</li>
                    <li>Todos tus datos personales</li>
                </ul>
                
                <p style="margin-top: 15px; font-weight: 600;">
                    ¿Estás seguro de querer eliminar tu cuenta?
                </p>
            </div>
            
            <div class="custom-modal-buttons">
                <button onclick="eliminarCuentaDefinitivamente()" 
                        class="custom-modal-btn custom-modal-btn-danger">
                    <i class="fas fa-trash"></i> Sí, eliminar cuenta
                </button>
                
                <button onclick="cerrarModal('modalEliminarCuenta')" 
                        class="custom-modal-btn custom-modal-btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px; color: #666;">
                <i class="fas fa-info-circle"></i> Si prefieres, puedes 
                <a href="#" onclick="mostrarModalDesactivarCuenta()" style="color: #82b16d; font-weight: 500;">desactivar tu cuenta temporalmente</a>
            </p>
        </div>
    </div>

    <div id="modalDesactivarCuenta" class="custom-modal">
        <div class="custom-modal-content">
            <span class="custom-modal-close" onclick="cerrarModal('modalDesactivarCuenta')">&times;</span>
            <div style="font-size: 50px; color: #ffc107; margin: 20px 0;">
                <i class="fas fa-pause-circle"></i>
            </div>
            
            <h3>Desactivar Cuenta Temporalmente</h3>
            
            <div style="background: #fff9e6; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: left;">
                <p><strong>Tu cuenta será desactivada temporalmente:</strong></p>
                <ul style="margin-left: 20px; margin-bottom: 15px;">
                    <li>No podrás iniciar sesión</li>
                    <li>Tus publicaciones se ocultarán</li>
                    <li>Podrás reactivar tu cuenta en cualquier momento</li>
                    <li>Tus datos se conservarán</li>
                </ul>
                
                <p style="margin-top: 15px; font-weight: 600;">
                    ¿Deseas desactivar tu cuenta temporalmente?
                </p>
            </div>
            
            <div class="custom-modal-buttons">
                <button onclick="desactivarCuentaTemporalmente()" 
                        class="custom-modal-btn" style="background: #ffc107; color: #000;">
                    <i class="fas fa-pause"></i> Sí, desactivar cuenta
                </button>
                
                <button onclick="cerrarModal('modalDesactivarCuenta')" 
                        class="custom-modal-btn custom-modal-btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- CONTENEDOR PARA ALERTAS -->
    <div id="alertContainer" class="alert-container"></div>
    
    <!--HABILITA VENTANAS FLOTANTES DE LOGIN Y REGISTRO-->
    <script src="script/login.js"></script>
    <script src="script/infopub.js"></script>

    <script>
        // Variables para control de sesión
        const estaLogueado = <?php echo isset($_SESSION['id']) ? 'true' : 'false'; ?>;
        const esVisitante = <?php echo $es_visitante ? 'true' : 'false'; ?>;
        
        // 🌟 Contenedores para filtros
        const gridIndex = document.getElementById("gridIndex");
        const mensajeVacio = document.getElementById("mensajeVacio");

        // Inputs
        const precioMin = document.getElementById("precio_min");
        const precioMax = document.getElementById("precio_max");

        const btnFiltrar = document.getElementById("btnFiltrar");
        const btnReset = document.getElementById("btnReset");

        // ============================================
        // MANEJO DEL MENÚ DESPLEGABLE DE USUARIO
        // ============================================
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

        // ============================================
        // FUNCIONES PARA MODALES
        // ============================================
        function mostrarModalCambiarPassword() {
            document.getElementById('modalCambiarPassword').style.display = 'flex';
            document.querySelector('.user-dropdown').classList.remove('active');
        }

        function mostrarModalEliminarCuenta() {
            document.getElementById('modalEliminarCuenta').style.display = 'flex';
            document.querySelector('.user-dropdown').classList.remove('active');
        }

        function mostrarModalDesactivarCuenta() {
            cerrarModal('modalEliminarCuenta');
            document.getElementById('modalDesactivarCuenta').style.display = 'flex';
        }

        function cerrarModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Cerrar modales al hacer clic fuera
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('custom-modal')) {
                e.target.style.display = 'none';
            }
        });

        // ============================================
        // FUNCIÓN PARA MOSTRAR ALERTAS
        // ============================================
        function mostrarAlerta(mensaje, tipo = 'info', duracion = 5000) {
            const alertContainer = document.getElementById('alertContainer');
            const alertId = 'alert-' + Date.now();
            
            const alert = document.createElement('div');
            alert.className = `alert alert-${tipo}`;
            alert.id = alertId;
            alert.innerHTML = `
                <i class="fas ${tipo === 'success' ? 'fa-check-circle' : tipo === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${mensaje}</span>
            `;
            
            alertContainer.appendChild(alert);
            
            // Auto-eliminar después de la duración
            setTimeout(() => {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    alertElement.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => {
                        alertElement.remove();
                    }, 300);
                }
            }, duracion);
        }

        // ============================================
        // CAMBIAR CONTRASEÑA
        // ============================================
        function cambiarPasswordSubmit(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Validaciones
            if (!currentPassword) {
                mostrarAlerta('Debes ingresar tu contraseña actual', 'error');
                return false;
            }
            
            if (newPassword.length < 6) {
                mostrarAlerta('La nueva contraseña debe tener al menos 6 caracteres', 'error');
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                mostrarAlerta('Las contraseñas no coinciden', 'error');
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
                    mostrarAlerta('Contraseña cambiada exitosamente', 'success');
                    cerrarModal('modalCambiarPassword');
                    // Limpiar formulario
                    document.getElementById('formCambiarPassword').reset();
                } else {
                    mostrarAlerta(data.message || 'Error al cambiar contraseña', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión', 'error');
            });
            
            return false;
        }

        // ============================================
        // ELIMINAR CUENTA
        // ============================================
        function eliminarCuentaDefinitivamente() {
            fetch('database/eliminar_cuenta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    accion: 'eliminar',
                    tipo: 'permanente'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta('Cuenta eliminada permanentemente. Redirigiendo...', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    mostrarAlerta(data.message || 'Error al eliminar cuenta', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión', 'error');
            });
        }

        function desactivarCuentaTemporalmente() {
            fetch('database/eliminar_cuenta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    accion: 'eliminar',
                    tipo: 'temporal'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta('Cuenta desactivada. Redirigiendo...', 'success');
                    setTimeout(() => {
                        window.location.href = 'database/logout.php';
                    }, 2000);
                } else {
                    mostrarAlerta(data.message || 'Error al desactivar cuenta', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión', 'error');
            });
        }

        // ============================================
        // FUNCIONES PARA FAVORITOS (EXISTENTES)
        // ============================================
        // 🔄 Función para agregar eventos a favoritos
        function agregarEventosFavoritos() {
            document.querySelectorAll('.fav-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const idPublicacion = this.dataset.id;
                    
                    if (!estaLogueado || !esVisitante) {
                        // Abrir ventana de login si no está logueado
                        document.getElementById('modalFondoLogin').style.display = 'flex';
                        return;
                    }
                    
                    // Toggle visual del botón
                    this.classList.toggle('active');
                    this.classList.add('animating');
                    
                    // Cambiar icono
                    const icon = this.querySelector('i');
                    if (this.classList.contains('active')) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                    }
                    
                    // Enviar petición al servidor
                    fetch('database/favoritos.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `accion=toggle&id_publicacion=${idPublicacion}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error:', data.error);
                            // Revertir visualmente si hay error
                            this.classList.toggle('active');
                            icon.classList.toggle('fa-regular');
                            icon.classList.toggle('fa-solid');
                        } else {
                            // Actualizar contador de favoritos
                            const card = this.closest('.pub-card');
                            const favCount = card.querySelector('.fav-count');
                            
                            if (data.accion === 'agregado') {
                                if (favCount) {
                                    const currentCount = parseInt(favCount.textContent.match(/\d+/)[0]);
                                    favCount.innerHTML = `<i class="fas fa-heart"></i> ${currentCount + 1}`;
                                } else {
                                    // Crear contador si no existe
                                    const newCount = document.createElement('span');
                                    newCount.className = 'fav-count';
                                    newCount.innerHTML = `<i class="fas fa-heart"></i> 1`;
                                    card.prepend(newCount);
                                }
                            } else {
                                if (favCount) {
                                    const currentCount = parseInt(favCount.textContent.match(/\d+/)[0]);
                                    if (currentCount - 1 <= 0) {
                                        favCount.remove();
                                    } else {
                                        favCount.innerHTML = `<i class="fas fa-heart"></i> ${currentCount - 1}`;
                                    }
                                }
                            }
                        }
                    })
                    .catch(err => console.error('Error:', err))
                    .finally(() => {
                        setTimeout(() => {
                            this.classList.remove('animating');
                        }, 800);
                    });
                });
            });
            
            // Agregar eventos a los enlaces de las publicaciones
            document.querySelectorAll('.publicacion-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!e.target.closest('.fav-btn')) {
                        window.location.href = this.href;
                    }
                });
            });
        }

        // 🔄 Función para cargar publicaciones vía AJAX (filtros)
        function cargarPublicacionesFiltradas() {
            let params = [];
            if (precioMin.value) params.push("precio_min=" + encodeURIComponent(precioMin.value));
            if (precioMax.value) params.push("precio_max=" + encodeURIComponent(precioMax.value));

            let url = "database/publicaciones.php?ajax=1&" + params.join("&");

            fetch(url)
                .then(res => res.text())
                .then(html => {
                    gridIndex.innerHTML = html;
                    
                    // Agregar eventos a los botones de favorito después de cargar
                    agregarEventosFavoritos();
                    
                    // Efecto visual
                    gridIndex.style.opacity = 0;
                    setTimeout(() => {
                        gridIndex.style.opacity = 1;
                        gridIndex.style.transition = 'opacity 0.4s ease';
                    }, 50);

                    if (html.trim() === "" || html.includes("No existen")) {
                        mensajeVacio.style.display = "block";
                    } else {
                        mensajeVacio.style.display = "none";
                    }
                })
                .catch(err => console.error('Error:', err));
        }

        // ▶️ Botón "Aplicar filtros"
        if (btnFiltrar) {
            btnFiltrar.addEventListener("click", cargarPublicacionesFiltradas);
        }

        // 🔄 Botón "Reiniciar"
        if (btnReset) {
            btnReset.addEventListener("click", () => {
                if (precioMin) precioMin.value = "";
                if (precioMax) precioMax.value = "";
                cargarPublicacionesFiltradas();
            });
        }

        // ▶️ Agregar eventos a los favoritos de la sección estática
        document.addEventListener("DOMContentLoaded", function() {
            agregarEventosFavoritos();
            
            // Cargar publicaciones filtradas al iniciar
            if (gridIndex) {
                cargarPublicacionesFiltradas();
            }
        });
    </script>

</body>
</html>