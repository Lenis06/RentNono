<?php
require_once '../database/conexion.php';

// Aprobación o rechazo
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'] === 'aprobar' ? 1 : 0;

    $update = $conn->prepare("UPDATE opiniones SET aprobado = :accion WHERE id = :id");
    $update->bindParam(':accion', $accion, PDO::PARAM_INT);
    $update->bindParam(':id', $id, PDO::PARAM_INT);
    $update->execute();
}

// Traer todas las reseñas
$stmt = $conn->query("SELECT o.*, p.titulo 
                      FROM opiniones o 
                      JOIN propiedades p ON o.propiedad_id = p.id 
                      ORDER BY o.fecha DESC");
$reseñas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Reseñas</title>
<style>
body { font-family: Arial; background: #f8f9fa; padding: 20px; }
table { width: 100%; border-collapse: collapse; background: white; }
th, td { padding: 10px; border-bottom: 1px solid #ccc; }
th { background: #eee; }
button { padding: 5px 10px; cursor: pointer; border: none; border-radius: 5px; }
.btn-aprobar { background: #28a745; color: white; }
.btn-rechazar { background: #dc3545; color: white; }
</style>
</head>
<body>

<h1>Panel de Reseñas</h1>

<table>
<tr>
<th>Propiedad</th>
<th>Comentario</th>
<th>Rating</th>
<th>Fecha</th>
<th>Estado</th>
<th>Acción</th>
</tr>

<?php foreach ($reseñas as $r): ?>
<tr>
<td><?= htmlspecialchars($r['titulo']) ?></td>
<td><?= htmlspecialchars($r['comentario']) ?></td>
<td><?= $r['rating'] ?> ⭐</td>
<td><?= $r['fecha'] ?></td>
<td><?= $r['aprobado'] ? "✅ Aprobada" : "⏳ Pendiente" ?></td>
<td>
  <?php if (!$r['aprobado']): ?>
    <a href="?accion=aprobar&id=<?= $r['id'] ?>"><button class="btn-aprobar">Aprobar</button></a>
  <?php else: ?>
    <a href="?accion=rechazar&id=<?= $r['id'] ?>"><button class="btn-rechazar">Rechazar</button></a>
  <?php endif; ?>
</td>
</tr>
<?php endforeach; ?>

</table>
</body>
</html>
