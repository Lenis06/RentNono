<?php
include("../database/session.php");
include("../database/conexion.php");

// Verificar que el usuario sea visitante
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'visitante') {
    header("Location: ../index.php");
    exit;
}

// Obtener favoritos del usuario
$stmt = $conn->prepare("
    SELECT p.*, f.fecha_agregado 
    FROM favoritos f 
    JOIN propiedades p ON f.propiedad_id = p.id 
    WHERE f.usuario_id = ? 
    ORDER BY f.fecha_agregado DESC
");
$stmt->execute([$_SESSION['id']]);
$favoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Favoritos | RentNono</title>
    <link rel="stylesheet" href="../estilos/estilo.css">
    <link rel="stylesheet" href="../estilos/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2d9a66f09.js" crossorigin="anonymous"></script>
</head>
<body>

<header class="main-header">
    <div class="container header-content">
        <h1 class="site-logo">
            <a href="ixusuario.php">Mis Favoritos</a>
        </h1>

        <nav class="main-nav">
            <ul>
                <li><a href="ixusuario.php">Inicio</a></li>
                <li><a href="erusuario.php">Explorar Propiedades</a></li>
                <li><b class="btn-primary-small" href="mis_favoritos.php">Mis Favoritos</b></li>
                <li><a href="../database/logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container favoritos-section">
    <h2>Mis Propiedades Favoritas</h2>
    
    <?php if (count($favoritos) > 0): ?>
        <div class="favoritos-grid">
            <?php foreach ($favoritos as $fav): ?>
                <div class="feature-item">
                    <button class="btn-fav active" data-id="<?= $fav['id'] ?>">
                        <i class="fa-solid fa-heart"></i>
                    </button>
                    <a href="../detalle_publicaciones.php?id=<?= $fav['id'] ?>" class="publicacion-link">
                        <img src="/Rentnono/media/publicaciones/<?= htmlspecialchars($fav['imagen']) ?>" alt="<?= htmlspecialchars($fav['titulo']) ?>">
                        <h4><?= htmlspecialchars($fav['titulo']) ?></h4>
                        <p><?= htmlspecialchars(substr($fav['descripcion'], 0, 100)) ?>...</p>
                        <p><strong>Precio:</strong> $<?= number_format($fav['precio'], 2) ?></p>
                        <p><small>Agregado: <?= date('d/m/Y', strtotime($fav['fecha_agregado'])) ?></small></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="favorito-vacio">
            <i class="fa-regular fa-heart"></i>
            <h3>No tienes propiedades favoritas</h3>
            <p>Explora nuestras propiedades y haz clic en el corazón para guardarlas aquí.</p>
            <a href="erusuario.php" class="btn-primary">Explorar Propiedades</a>
        </div>
    <?php endif; ?>
</main>

<footer class="main-footer">
    <div class="container footer-content">
        <p>&copy; 2025 RentNono. Todos los derechos reservados.</p>
    </div>
</footer>

<script>
// Script para manejar favoritos en esta página
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-fav').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const idPublicacion = this.dataset.id;
            const card = this.closest('.feature-item');
            
            // Animación
            this.classList.add('animating');
            
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
                if (data.success) {
                    setTimeout(() => {
                        // Remover el elemento de la vista
                        card.style.opacity = '0';
                        card.style.transform = 'translateX(20px)';
                        setTimeout(() => {
                            card.remove();
                            
                            // Si no quedan favoritos, mostrar mensaje
                            if (document.querySelectorAll('.feature-item').length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }, 300);
                }
            })
            .catch(err => console.error('Error:', err));
        });
    });
});
</script>

</body>
</html>