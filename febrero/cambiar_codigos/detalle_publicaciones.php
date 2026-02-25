<?php
require_once "database/session.php";
require_once "database/conexion.php";
include "login.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<h2 style="text-align:center; margin-top:50px;">ID de publicación no válido.</h2>');
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM propiedades WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$pub = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pub) {
    die('<h2 style="text-align:center; margin-top:50px;">Publicación no encontrada.</h2>');
}

$stmtVisita = $conn->prepare("UPDATE propiedades SET visitas = visitas + 1 WHERE id = ?");
$stmtVisita->execute([$id]);

$imagen = !empty($pub['imagen']) 
    ? "media/publicaciones/" . htmlspecialchars($pub['imagen'])
    : "media/publicaciones/noimage.png";


$resenas = [];

// Primero obtenemos las reseñas
$stmtRes = $conn->prepare("
    SELECT * FROM opiniones 
    WHERE propiedad_id = :id
    ORDER BY fecha DESC
");
$stmtRes->bindParam(':id', $id);
$stmtRes->execute();
$opiniones = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

// Para cada reseña, buscamos el nombre en la tabla correspondiente
foreach ($opiniones as $opinion) {
    $usuario_id = $opinion['usuario_id'];
    $nombre_usuario = "Usuario"; // Nombre por defecto
    
    // Buscar en usuario_visitante
    $stmt = $conn->prepare("SELECT nombre FROM usuario_visitante WHERE id = ?");
    $stmt->execute([$usuario_id]);
    if ($row = $stmt->fetch()) {
        $nombre_usuario = $row['nombre'];
    } else {
        // Buscar en usuario_propietario
        $stmt = $conn->prepare("SELECT nombre FROM usuario_propietario WHERE id = ?");
        $stmt->execute([$usuario_id]);
        if ($row = $stmt->fetch()) {
            $nombre_usuario = $row['nombre'];
        } else {
            // Buscar en usuario_admin
            $stmt = $conn->prepare("SELECT nombre FROM usuario_admin WHERE id = ?");
            $stmt->execute([$usuario_id]);
            if ($row = $stmt->fetch()) {
                $nombre_usuario = $row['nombre'];
            }
        }
    }
    
    // Agregar el nombre a la reseña
    $opinion['usuario_nombre'] = $nombre_usuario;
    $resenas[] = $opinion;
}

// Promedio de calificaciones
$promedio = 0;
if (count($resenas) > 0) {
    $suma = array_sum(array_column($resenas, 'rating'));
    $promedio = round($suma / count($resenas), 1);
}

// Verificar si el usuario ya opinó (solo si está logueado)
$yaOpino = false;
if (isset($_SESSION['id'])) {
    $checkOpinion = $conn->prepare("SELECT id FROM opiniones WHERE propiedad_id = ? AND usuario_id = ?");
    $checkOpinion->execute([$id, $_SESSION['id']]);
    $yaOpino = $checkOpinion->rowCount() > 0;
}

// Mensajes de feedback
$mensajeExito = isset($_GET['resena']) && $_GET['resena'] == 'ok';
$mensajeError = isset($_GET['resena']) && $_GET['resena'] == 'error';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pub['titulo']) ?> | RentNono</title>
    <link rel="stylesheet" href="estilos/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f6fa;
        }
        
        .detalle-container {
            max-width: 1000px;
            margin: 0 auto 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .detalle-imagen {
            width: 100%;
            height: 450px;
            object-fit: cover;
        }
        
        .detalle-body {
            padding: 30px;
        }
        
        .detalle-titulo {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .detalle-ubicacion {
            color: #666;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .detalle-ubicacion i {
            color: #4CAF50;
            margin-right: 5px;
        }
        
        .detalle-precio {
            font-size: 32px;
            color: #2b9348;
            font-weight: bold;
            margin-bottom: 25px;
        }
        
        .detalle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .detalle-item {
            display: flex;
            flex-direction: column;
        }
        
        .detalle-item strong {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .detalle-item span {
            color: #333;
            font-size: 16px;
            font-weight: 500;
        }
        
        .descripcion {
            margin: 30px 0;
            line-height: 1.8;
            color: #555;
        }
        
        .descripcion h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .mapa {
            margin: 30px 0;
        }
        
        .mapa h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .mapa iframe {
            width: 100%;
            height: 350px;
            border-radius: 10px;
            border: none;
        }
        
        /* Estilos para reseñas */
        .rating-container {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            margin-top: 40px;
        }
        
        .rating-container h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .promedio-resenas {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding: 15px;
            background: white;
            border-radius: 10px;
        }
        
        .promedio-numero {
            font-size: 36px;
            font-weight: bold;
            color: #333;
        }
        
        .promedio-estrellas {
            color: #ffc107;
            font-size: 24px;
        }
        
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 5px;
            margin: 15px 0;
        }
        
        .rating input {
            display: none;
        }
        
        .rating label {
            font-size: 35px;
            color: #ddd;
            cursor: pointer;
            transition: 0.2s;
        }
        
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffc107;
            transform: scale(1.1);
        }
        
        .rating input:checked ~ label {
            color: #ffc107;
        }
        
        textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: vertical;
            font-family: inherit;
            margin: 15px 0;
            font-size: 15px;
        }
        
        textarea:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        
        .btn-enviar {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: 0.2s;
        }
        
        .btn-enviar:hover {
            background: #45a049;
        }
        
        .resenas-lista {
            margin-top: 30px;
        }
        
        .resena-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .resena-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .resena-usuario {
            font-weight: 600;
            color: #333;
        }
        
        .resena-usuario i {
            color: #4CAF50;
            margin-right: 5px;
        }
        
        .resena-rating {
            color: #ffc107;
        }
        
        .resena-fecha {
            color: #999;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .resena-comentario {
            color: #666;
            line-height: 1.6;
            margin-top: 10px;
        }
        
        .login-required {
            text-align: center;
            padding: 30px;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .login-required i {
            font-size: 40px;
            color: #856404;
            margin-bottom: 15px;
        }
        
        .login-required p {
            color: #856404;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .login-required a {
            background: #4CAF50;
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            transition: 0.2s;
        }
        
        .login-required a:hover {
            background: #45a049;
        }
        
        .fecha-publicacion {
            color: #999;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .mensaje-flotante {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        
        .mensaje-exito {
            background: #4CAF50;
        }
        
        .mensaje-error {
            background: #f44336;
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
        
        .ya-opino {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        
        .ya-opino i {
            margin-right: 10px;
        }
        
        .visitas {
            display: inline-block;
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            color: #666;
            margin-left: 10px;
        }
        
        .visitas i {
            color: #4CAF50;
            margin-right: 5px;
        }

                /* Estilo para el botón de login en reseñas */
        .btn-login-resena {
            background: #82b16d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 5px rgba(33, 150, 243, 0.3);
            width: auto;
            margin: 10px 0;
        }
        
        .btn-login-resena:hover {
            background: #1976D2;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(33, 150, 243, 0.4);
        }
        
        .btn-login-resena i {
            font-size: 18px;
        }
        
        /* Mejorar el contenedor del formulario */
        .rating-container form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .rating-container form textarea {
            width: 100%;
            margin: 20px 0;
            border: 2px solid #e0e0e0;
            transition: border-color 0.3s;
        }
        
        .rating-container form textarea:focus {
            border-color: #82b16d;
        }
        
        /* Centrar el botón cuando no hay sesión */
        .rating-container form:has(.btn-login-resena) {
            align-items: center;
            text-align: center;
            background: #f8f9fa;
            border: 2px dashed #ccc;
        }
        
        .rating-container form:has(.btn-login-resena) .rating {
            justify-content: center;
            width: 100%;
        }
        
        .rating-container form:has(.btn-login-resena) textarea {
            background: #f5f5f5;
        }
        
        /* Animación sutil para el formulario cuando no hay sesión */
        .rating-container form:has(.btn-login-resena) {
            transition: all 0.3s ease;
        }
        
        .rating-container form:has(.btn-login-resena):hover {
            border-color: #82b16d;
            background: #f0f4f8;
        }

        /* ===== ESTILOS DEL HEADER IGUAL AL INDEX ===== */
        .main-header {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .site-logo {
            font-size: 1.5em;
        }

        .site-logo a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .main-nav ul {
            display: flex;
            list-style: none;
            gap: 20px;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .main-nav ul li {
            list-style: none;
        }

        .main-nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .main-nav ul li a:hover {
            color: #82b16d;  /* Verde claro del index */
        }

        /* Botón iniciar sesión - MISMO COLOR QUE EL INDEX */
        .btn-iniciar-sesion {
            background: #82b16d;  /* Verde claro como en el index */
            color: white !important;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-iniciar-sesion:hover {
            background: #6d9c5a;  /* Verde un poco más oscuro para hover */
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(130, 177, 109, 0.3);
        }

        /* Eliminar estilos anteriores que puedan interferir */
        .main-nav a:not(.btn-iniciar-sesion) {
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Mensajes flotantes -->
    <?php if ($mensajeExito): ?>
        <div class="mensaje-flotante mensaje-exito">
            <i class="fas fa-check-circle"></i> ¡Reseña guardada con éxito!
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.mensaje-flotante')?.remove();
            }, 3000);
        </script>
    <?php endif; ?>
    
    <?php if ($mensajeError): ?>
        <div class="mensaje-flotante mensaje-error">
            <i class="fas fa-exclamation-circle"></i> Error al guardar la reseña
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.mensaje-flotante')?.remove();
            }, 3000);
        </script>
    <?php endif; ?>

    <header class="main-header">
        <div class="container header-content">
            <h1 class="site-logo">
                <a href="javascript:history.back()">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </h1>
            <nav class="main-nav">
                <ul>
                    <?php if(isset($_SESSION['nombre'])): ?>
                        <li><a href="database/logout.php">Cerrar sesión</a></li>
                    <?php else: ?>
                        <a id="abrirLogin" class="btn-iniciar-sesion">Iniciar sesión</a>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>


    <main class="container">
        <div class="detalle-container">
            <img src="<?= $imagen ?>" alt="<?= htmlspecialchars($pub['titulo']) ?>" class="detalle-imagen">
            
            <div class="detalle-body">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h1 class="detalle-titulo"><?= htmlspecialchars($pub['titulo']) ?></h1>
                    <span class="visitas">
                        <i class="fas fa-eye"></i> <?= number_format($pub['visitas'] ?? 0) ?> visitas
                    </span>
                </div>
                
                <div class="detalle-ubicacion">
                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($pub['direccion'] ?: $pub['ubicacion']) ?>
                </div>
                
                <div class="detalle-precio">$<?= number_format($pub['precio'], 2, ',', '.') ?></div>
                
                <div class="detalle-grid">
                    <div class="detalle-item">
                        <strong><i class="fas fa-home"></i> Tipo:</strong>
                        <span><?= htmlspecialchars($pub['tipo']) ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong><i class="fas fa-exchange-alt"></i> Operación:</strong>
                        <span><?= htmlspecialchars($pub['operacion']) ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong><i class="fas fa-building"></i> Estado:</strong>
                        <span><?= htmlspecialchars($pub['estado']) ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong><i class="fas fa-vector-square"></i> Superficie:</strong>
                        <span><?= htmlspecialchars($pub['superficie']) ?> m²</span>
                    </div>
                    <div class="detalle-item">
                        <strong><i class="fas fa-door-open"></i> Ambientes:</strong>
                        <span><?= htmlspecialchars($pub['ambientes']) ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong><i class="fas fa-bed"></i> Dormitorios:</strong>
                        <span><?= htmlspecialchars($pub['dormitorios']) ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong><i class="fas fa-bath"></i> Baños:</strong>
                        <span><?= htmlspecialchars($pub['sanitarios']) ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong><i class="fas fa-car"></i> Garaje:</strong>
                        <span><?= htmlspecialchars($pub['garaje']) ?></span>
                    </div>
                    <div class="detalle-item">
                        <strong><i class="fas fa-calendar-check"></i> Disponibilidad:</strong>
                        <span><?= htmlspecialchars($pub['disponibilidad']) ?></span>
                    </div>
                </div>
                
                <div class="mapa">
                    <h3><i class="fas fa-map"></i> Ubicación</h3>
                    <iframe
                        src="https://www.google.com/maps?q=<?= urlencode($pub['direccion'] ?: $pub['ubicacion']) ?>&output=embed"
                        allowfullscreen
                        loading="lazy">
                    </iframe>
                </div>
                
                <div class="descripcion">
                    <h3><i class="fas fa-align-left"></i> Descripción</h3>
                    <p><?= nl2br(htmlspecialchars($pub['descripcion'])) ?></p>
                </div>
                
<!-- SECCIÓN DE RESEÑAS -->
<div class="rating-container">
    <h2><i class="fas fa-star"></i> Reseñas de la propiedad</h2>
    
    <?php if (count($resenas) > 0): ?>
        <div class="promedio-resenas">
            <span class="promedio-numero"><?= $promedio ?></span>
            <span class="promedio-estrellas">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star" style="color: <?= $i <= $promedio ? '#ffc107' : '#ddd' ?>"></i>
                <?php endfor; ?>
            </span>
            <span style="color: #666;">(<?= count($resenas) ?> reseñas)</span>
        </div>
    <?php endif; ?>
    
    <!-- FORMULARIO PARA NUEVA RESEÑA - SIEMPRE VISIBLE -->
    <h3 style="margin-bottom: 15px;">Deja tu opinión</h3>
    
    <?php if (isset($_SESSION['id']) && $yaOpino): ?>
        <!-- Usuario registrado que ya opinó -->
        <div class="ya-opino">
            <i class="fas fa-check-circle"></i>
            Ya has opinado sobre esta propiedad. ¡Gracias por tu contribución!
        </div>
    <?php else: ?>
        <!-- Formulario visible para TODOS (registrados que no opinaron y no registrados) -->
        <form method="POST" action="database/guardar_opinion.php" id="formResena" 
              <?php if (!isset($_SESSION['id'])): ?>onsubmit="return false;"<?php endif; ?>>
            <input type="hidden" name="propiedad_id" value="<?= $id ?>">
            
            <div class="rating">
            <input type="radio" name="rating" id="star5" value="5">
            <label for="star5"></label>

            <input type="radio" name="rating" id="star4" value="4">
            <label for="star4"></label>

            <input type="radio" name="rating" id="star3" value="3">
            <label for="star3"></label>

            <input type="radio" name="rating" id="star2" value="2">
            <label for="star2"></label>

            <input type="radio" name="rating" id="star1" value="1">
            <label for="star1"></label>
            </div>
            
            <textarea name="comentario" placeholder="¿Qué te pareció esta propiedad?" required rows="4"></textarea>
            
            <?php if (isset($_SESSION['id'])): ?>
                <!-- Usuario registrado: botón normal -->
                <button type="submit" class="btn-enviar">
                    <i class="fas fa-paper-plane"></i> Enviar Reseña
                </button>
            <?php else: ?>
                <!-- Usuario no registrado -->
                <div style="width: 100%; text-align: center; margin-bottom: 15px; color: #666;">
                    <i class="fas fa-info-circle" style="color: #82b16d;"></i> 
                    Inicia sesión para compartir tu opinión
                </div>
                <button type="button" onclick="abrirLoginYRecordar(<?= $id ?>)" class="btn-login-resena">
                    <i class="fas fa-sign-in-alt"></i> Iniciar sesión para opinar
                </button>
            <?php endif; ?>
        </form>
    <?php endif; ?>
    
    <!-- LISTA DE RESEÑAS -->
    <div class="resenas-lista">
        <h3 style="margin-bottom: 20px;">
            <i class="fas fa-comments"></i> 
            Opiniones de usuarios
        </h3>
        
        <?php if (count($resenas) > 0): ?>
            <?php foreach ($resenas as $resena): ?>
                <div class="resena-item">
                    <div class="resena-header">
                        <span class="resena-usuario">
                            <i class="fas fa-user-circle"></i> 
                            <?= htmlspecialchars($resena['usuario_nombre'] ?? 'Usuario') ?>
                        </span>
                        <span class="resena-rating">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="fa<?= $i <= $resena['rating'] ? 's' : 'r' ?> fa-star" style="color: <?= $i <= $resena['rating'] ? '#ffc107' : '#ddd' ?>"></i>
                            <?php endfor; ?>
                        </span>
                    </div>
                    <p class="resena-comentario"><?= nl2br(htmlspecialchars($resena['comentario'])) ?></p>
                    <div class="resena-fecha">
                        <i class="far fa-calendar-alt"></i> 
                        <?= date('d/m/Y', strtotime($resena['fecha'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; color: #999; padding: 40px;">
                <i class="fas fa-star" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <p style="font-size: 16px;">No hay reseñas aún. ¡Sé el primero en opinar!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
                
                <div class="fecha-publicacion">
                    <i class="far fa-calendar-alt"></i> Publicado el: <?= date('d/m/Y', strtotime($pub['fecha_publicacion'])) ?>
                </div>
            </div>
        </div>
    </main>
    <script>
</script>
<script>
// Función unificada para abrir login - respeta la definición original
window.abrirLoginYRecordar = function(idPropiedad) {
    // Guardamos en sessionStorage que queríamos opinar
    sessionStorage.setItem('intencionResena', 'true');
    sessionStorage.setItem('propiedadId', idPropiedad);
    
    // Feedback visual
    const boton = event ? event.currentTarget : null;
    if (boton) {
        boton.style.transform = 'scale(0.95)';
        setTimeout(() => boton.style.transform = 'scale(1)', 200);
    }
    
    // USAR LA FUNCIÓN ORIGINAL DEL MODAL (definida en login.php)
    if (typeof window.abrirLogin === 'function') {
        window.abrirLogin(); // Esta es la función correcta que abre el modal
    } else {
        // Fallback: buscar el botón de login y hacer click
        const loginBtn = document.querySelector('.btn-iniciar-sesion, [onclick="abrirLogin()"]');
        if (loginBtn) {
            loginBtn.click();
        } else {
            // Último recurso: redirigir
            window.location.href = 'index.php?login=required&redirect=' + encodeURIComponent(window.location.href);
        }
    }
};

// Verificar si venimos de un login exitoso
document.addEventListener('DOMContentLoaded', function() {
    const intentoResena = sessionStorage.getItem('intencionResena');
    const propiedadId = sessionStorage.getItem('propiedadId');
    
    <?php if (isset($_SESSION['id'])): ?>
        if (intentoResena === 'true' && propiedadId == <?= $id ?>) {
            sessionStorage.removeItem('intencionResena');
            sessionStorage.removeItem('propiedadId');
            
            const formulario = document.getElementById('formResena');
            if (formulario) {
                formulario.scrollIntoView({ behavior: 'smooth' });
                formulario.style.transition = 'box-shadow 0.3s';
                formulario.style.boxShadow = '0 0 0 3px rgba(76, 175, 80, 0.5)';
                setTimeout(() => formulario.style.boxShadow = 'none', 2000);
            }
        }
    <?php endif; ?>
});

// NO redefinir window.abrirLogin aquí - respetar la de login.php
console.log('Función abrirLogin disponible:', typeof window.abrirLogin === 'function');
</script>
<script src="script/login.js"></script>
</body>
</html>