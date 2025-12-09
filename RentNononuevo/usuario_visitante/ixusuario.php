<?php
include("../database/session.php");
include("../database/publicaciones.php");
$es_visitante = isset($_SESSION['rol']) && $_SESSION['rol'] === 'visitante';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentNono | Inicio</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
    <link rel="stylesheet" href="../estilos/publicaciones.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/a2d9a66f09.js" crossorigin="anonymous"></script>
</head>
<body>

    <!-- BARRA DE NAVEGACION PRINCIPAL -->
    <header class="main-header">
        <div class="container header-content">
            <h1 class="site-logo">
                <?php if(isset($_SESSION['nombre'])): ?>
                    <a href="ixusuario.php">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></a>
                <?php else: ?>
                    <a href="ixusuario.php">RentNono</a>
                <?php endif; ?>
            </h1>

            <nav class="main-nav">
                <ul>
                    <li><b href="#" class="btn-primary-small" href="ixusuario.php">Inicio</b></li>
                    <li><a href="erusuario.php">Explorar Propiedades</a></li>
                    
                    <?php if(isset($_SESSION['nombre'])): ?>
                    <?php if($es_visitante): ?>
                        <li><a href="mis_favoritos.php">Mis Favoritos</a></li>
                    <?php endif; ?>   
                        <li><a href="../database/logout.php">Cerrar sesión</a></li>
                    <?php else: ?>
                        <a id="abrirLogin" class="btn-iniciar-sesion">Iniciar sesión</a>
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
                <a href="erusuario.php" class="btn-primary-large">Alquilar</a>
                <a href="erusuario.php" class="btn-primary-large">Comprar</a>
                <a href="#" class="btn-primary-large">Vender</a>
                <section class="features-section container">
                    <div class="features-grid">
                        <div class="feature-item">
                            <p>Accede como usuario registrado y podrás comentar</p>
                        </div>
                        <div class="feature-item">
                            <p>Contáctate con el propietario</p>
                        </div>
                        <div class="feature-item">
                            <p>Crea tu lista de favoritos</p>
                        </div>
                    </div>
                </section>            
            </div>
            <div class="search-box">
                <input list="opciones" type="text" id="buscar" placeholder="Escribe para buscar...">
                <datalist id="opciones">
                    <option value="Casa en alquiler">
                    <option value="Departamento en venta">
                    <option value="Terreno en Nonogasta">
                    <option value="Oficina comercial">
                    <option value="Cabaña turística">
                </datalist>
                <button type="button" class="icon" id="btnBuscar">🔍</button>
            </div>
        </section>

        <!--SECCION DE PUBLICACIONES-->
        <section class="features-section container">
            <h3>Publicaciones más visitadas</h3>
            <div class="features-grid" id="gridPublicaciones">
                <?php if (count($publicaciones) > 0): ?>
                    <?php foreach ($publicaciones as $pub): 
                        // Verificar si esta publicación está en favoritos
                        $esFavorito = false;
                        $totalFav = 0;
                        
                        if ($es_visitante) {
                            include("../database/conexion.php");
                            $checkFav = $conn->prepare("SELECT id FROM favoritos WHERE usuario_id = ? AND propiedad_id = ?");
                            $checkFav->execute([$_SESSION['id'], $pub['id']]);
                            $esFavorito = $checkFav->rowCount() > 0;
                            
                            $countFav = $conn->prepare("SELECT COUNT(*) as total FROM favoritos WHERE propiedad_id = ?");
                            $countFav->execute([$pub['id']]);
                            $totalFav = $countFav->fetch(PDO::FETCH_ASSOC)['total'];
                        }
                    ?>
                        <div class="feature-item pub-card">
                            <?php if ($totalFav > 0): ?>
                                <span class="fav-count"><i class="fas fa-heart"></i> <?= $totalFav ?></span>
                            <?php endif; ?>
                            
                            <button class="fav-btn <?= $esFavorito ? 'active' : '' ?>" data-id="<?= $pub['id'] ?>">
                                <i class="fa-<?= $esFavorito ? 'solid' : 'regular' ?> fa-heart"></i>
                            </button>
                            
                            <a href="../detalle_publicaciones.php?id=<?= $pub['id'] ?>" class="publicacion-link">
                                <img src="/RentNono/media/publicaciones/<?php echo htmlspecialchars($pub['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($pub['titulo']); ?>">
                                <h4><?php echo htmlspecialchars($pub['titulo']); ?></h4>
                                <p><?php echo htmlspecialchars(substr($pub['descripcion'], 0, 100)); ?>...</p>
                                <p><strong>Precio:</strong> $<?php echo number_format($pub['precio'], 2); ?></p>
                                <p><small>Click para ver detalles</small></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-results">No hay publicaciones disponibles.</p>
                <?php endif; ?>
            </div>
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estaLogueado = <?php echo isset($_SESSION['id']) ? 'true' : 'false'; ?>;
            const esVisitante = <?php echo $es_visitante ? 'true' : 'false'; ?>;
            
            // Manejar clicks en botones de favorito
            document.querySelectorAll('.fav-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const idPublicacion = this.dataset.id;
                    
                    if (!estaLogueado || !esVisitante) {
                        // Abrir login si no está registrado
                        const modalLogin = document.getElementById('modalFondoLogin');
                        if (modalLogin) {
                            modalLogin.style.display = 'flex';
                        }
                        return;
                    }
                    
                    // Toggle visual
                    this.classList.toggle('active');
                    this.classList.add('animating');
                    
                    const icon = this.querySelector('i');
                    if (this.classList.contains('active')) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                    }
                    
                    // Enviar al servidor
                    fetch('../database/favoritos.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `accion=toggle&id_publicacion=${idPublicacion}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error:', data.error);
                            // Revertir
                            this.classList.toggle('active');
                            icon.classList.toggle('fa-regular');
                            icon.classList.toggle('fa-solid');
                        } else {
                            // Actualizar contador
                            const favCount = this.closest('.pub-card').querySelector('.fav-count');
                            if (favCount) {
                                const currentCount = parseInt(favCount.textContent.match(/\d+/)[0]);
                                if (data.accion === 'agregado') {
                                    favCount.innerHTML = `<i class="fas fa-heart"></i> ${currentCount + 1}`;
                                } else {
                                    if (currentCount - 1 <= 0) {
                                        favCount.remove();
                                    } else {
                                        favCount.innerHTML = `<i class="fas fa-heart"></i> ${currentCount - 1}`;
                                    }
                                }
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        this.classList.toggle('active');
                        icon.classList.toggle('fa-regular');
                        icon.classList.toggle('fa-solid');
                    })
                    .finally(() => {
                        setTimeout(() => {
                            this.classList.remove('animating');
                        }, 800);
                    });
                });
            });
            
            // Hacer cards clickeables (excepto botón favorito)
            document.querySelectorAll('.publicacion-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!e.target.closest('.fav-btn')) {
                        window.location.href = this.href;
                    }
                });
            });
            
            // Búsqueda
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