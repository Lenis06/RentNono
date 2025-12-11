<?php
include("../database/session.php");
include("../database/publicaciones.php");

$es_visitante = isset($_SESSION['rol']) && $_SESSION['rol'] === 'visitante';
$usuario_id = $_SESSION['id'] ?? null;

// Para la sección de publicaciones más visitadas, necesitamos verificar favoritos
include("../database/conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentNono | Inicio Usuario</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
    <link rel="stylesheet" href="../estilos/publicaciones.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    </style>
</head>
<body>

    <!-- BARRA DE NAVEGACION PRINCIPAL - CON MENÚ DESPLEGABLE -->
    <header class="main-header">
        <div class="container header-content">
            <h1 class="site-logo">
                <a href="ixusuario.php">RentNono</a>
            </h1>

            <nav class="main-nav">
                <ul>
                    <li><b class="btn-primary-small" href="ixusuario.php">Inicio</b></li>
                    <li><a href="erusuario.php">Explorar Propiedades</a></li>
                    
                    <!-- MENÚ DE USUARIO DESPLEGABLE -->
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
                                    
                                    <a href="ixusuario.php" class="dropdown-item">
                                        <i class="fas fa-home"></i> Mi Panel
                                    </a>
                                    
                                    <a href="../perfil.php" class="dropdown-item">
                                        <i class="fas fa-user-edit"></i> Editar Perfil
                                    </a>
                                    
                                    <?php if($es_visitante): ?>
                                        <a href="mis_favoritos.php" class="dropdown-item">
                                            <i class="fas fa-heart"></i> Mis Favoritos
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if(isset($_SESSION['google_id'])): ?>
                                        <a href="../database/cerrar_sesion_google.php" class="dropdown-item">
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
                                    
                                    <a href="../database/logout.php" class="dropdown-item dropdown-danger">
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
    
    <footer class="main-footer">
        <div class="container footer-content">
            <p>&copy; 2025 Rentnono. Todos los derechos reservados.</p>
            <ul class="footer-links">
                <li><a href="#">Términos y Condiciones</a></li>
                <li><a href="#">Política de Privacidad</a></li>
            </ul>
        </div>
    </footer>
    
    <script src="../script/login.js"></script>
    <script src="../script/infopub.js"></script>

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
        // FUNCIONES PARA MODALES (simplificadas)
        // ============================================
        function mostrarModalCambiarPassword() {
            alert('Para cambiar la contraseña, ve a la página de perfil desde el menú principal.');
        }

        function mostrarModalEliminarCuenta() {
            if (confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción es irreversible.')) {
                fetch('../database/eliminar_cuenta.php', {
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
                        alert('Cuenta eliminada. Redirigiendo...');
                        window.location.href = '../index.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
            }
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
                        const modalLogin = document.getElementById('modalFondoLogin');
                        if (modalLogin) {
                            modalLogin.style.display = 'flex';
                        }
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
                    fetch('../database/favoritos.php', {
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
                                    const currentCount = parseInt(favCount.textContent.match(/\d+/)[0] || 0);
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
                                    const currentCount = parseInt(favCount.textContent.match(/\d+/)[0] || 0);
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

            let url = "../database/publicaciones.php?ajax=1&" + params.join("&");

            fetch(url, {
                credentials: 'include' // 🔥 Enviar cookies de sesión
            })
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
            
            // Búsqueda por texto (mantengo esta funcionalidad de tu versión anterior)
            const btnBuscar = document.getElementById('btnBuscar');
            const inputBuscar = document.getElementById('buscar');
            
            if (btnBuscar && inputBuscar) {
                btnBuscar.addEventListener('click', function() {
                    const termino = inputBuscar.value.trim();
                    if (termino) {
                        window.location.href = `erusuario.php?buscar=${encodeURIComponent(termino)}`;
                    }
                });
                
                inputBuscar.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const termino = this.value.trim();
                        if (termino) {
                            window.location.href = `erusuario.php?buscar=${encodeURIComponent(termino)}`;
                        }
                    }
                });
            }
        });
    </script>

</body>
</html>