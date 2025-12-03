<?php
include("conexion.php");

// Evitar errores si no hay datos
$publicaciones = [];

try {
    // --- Construir consulta base
    $sql = "SELECT * FROM propiedades WHERE 1=1";
    $params = [];

    // --- Filtros
    // Filtro por precio mínimo
if (!empty($_GET['precio_min'])) {
    $sql .= " AND precio >= :precio_min";
    $params[':precio_min'] = $_GET['precio_min'];
}

// Filtro por precio máximo
if (!empty($_GET['precio_max'])) {
    $sql .= " AND precio <= :precio_max";
    $params[':precio_max'] = $_GET['precio_max'];
}

    $filtros = [
        'operacion', 'tipo', 'estado', 'garaje',
        'precio_max', 'ambientes', 'dormitorios', 'sanitarios'
    ];

    foreach ($filtros as $f) {
        if (!empty($_GET[$f])) {
            if ($f === 'precio_max') {
                $sql .= " AND precio <= :$f";
            } else {
                $sql .= " AND $f = :$f";
            }
            $params[":$f"] = $_GET[$f];
        }
    }

    // --- Filtro de búsqueda (por título o descripción)
    if (!empty($_GET['busqueda'])) {
        $sql .= " AND (titulo LIKE :busqueda OR descripcion LIKE :busqueda)";
        $params[':busqueda'] = "%" . $_GET['busqueda'] . "%";
    }
    

    // --- Ejecutar consulta
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error al obtener publicaciones: " . $e->getMessage());
    $publicaciones = [];
}

// --- Si el archivo se llama mediante AJAX (fetch)
if (isset($_GET['ajax'])) {
    if (empty($publicaciones)) {
        echo "<p style='text-align:center;padding:20px;'>No existen datos que coinciden con su busqueda.</p>";
    } else {
        foreach ($publicaciones as $pub) {
            // Imagen por defecto si falta
            $imagen = !empty($pub['imagen'])
                ? '/RentNono/media/publicaciones/' . htmlspecialchars($pub['imagen'])
                : '/RentNono/media/publicaciones/noimage.png';

            echo '<div class="feature-item">';
            echo '  <div class="card" style="border:1px solid #ddd;border-radius:10px;overflow:hidden;box-shadow:0 2px 5px rgba(0,0,0,0.1);position:relative;">';

            // ❤️ Botón de favoritos (FUERA DEL <a>)
            echo '<div class="fav-btn">';
            echo '   <i class="fa-regular fa-heart btn-fav"
                        data-id="' . htmlspecialchars($pub['id']) . '"
                        onclick="handleFavClick(' . $pub['id'] . ', event)">
                    </i>';
            echo '</div>';


            // El link a la publicación ↓
            echo '  <a href="/RentNono/database/detalle_publicaciones.php?id=' . htmlspecialchars($pub['id']) . '">';  
            echo '      <img src="' . $imagen . '" alt="Imagen de propiedad" style="width:100%;height:200px;object-fit:cover;">';
            echo '      <div class="card-body" style="padding:10px;">';
            echo '          <h4 style="margin:0;font-size:18px;">' . htmlspecialchars($pub['titulo']) . '</h4>';
            echo '          <p style="margin:5px 0;color:#666;">' . htmlspecialchars($pub['descripcion']) . '</p>';
            echo '      </div>';
            echo '  </a>';

            echo '  </div>'; // card
            echo '</div>';  // feature-item


             //echo '          <p><strong>Tipo:</strong> ' . htmlspecialchars($pub['tipo']) . '</p>';
             //echo '          <p><strong>Operación:</strong> ' . htmlspecialchars($pub['operacion']) . '</p>';
             //echo '          <p><strong>Estado:</strong> ' . htmlspecialchars($pub['estado']) . '</p>';
            //echo '          <p><strong>Precio:</strong> $' . number_format($pub['precio'], 2, ',', '.') . '</p>';
        }
    }
    exit; // 👈 Detiene la ejecución aquí si se llamó por AJAX
}
?>
