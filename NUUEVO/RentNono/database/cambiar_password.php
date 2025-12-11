<?php
// database/cambiar_password.php
session_start();
include("conexion.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$user_id = $_SESSION['id'];
$user_rol = $_SESSION['rol'];
$password_actual = $_POST['password_actual'] ?? '';
$password_nueva = $_POST['password_nueva'] ?? '';

if (empty($password_actual) || empty($password_nueva)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

if (strlen($password_nueva) < 6) {
    echo json_encode(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres']);
    exit;
}

try {
    // Determinar la tabla según el rol
    $tabla = ($user_rol === 'visitante') ? 'usuario_visitante' : 'usuario_propietario';
    
    // Verificar contraseña actual
    $stmt = $conn->prepare("SELECT password FROM $tabla WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar contraseña actual (asumiendo que usas MD5)
    if (md5($password_actual) !== $user['password']) {
        echo json_encode(['success' => false, 'message' => 'Contraseña actual incorrecta']);
        exit;
    }
    
    // Actualizar contraseña
    $new_password_hash = md5($password_nueva);
    $update = $conn->prepare("UPDATE $tabla SET password = ? WHERE id = ?");
    $update->execute([$new_password_hash, $user_id]);
    
    // Registrar en logs
    $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
    $log->execute([
        ':id' => $user_id,
        ':nombre' => $_SESSION['nombre'],
        ':rol' => $user_rol,
        ':accion' => 'Cambió contraseña'
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Contraseña cambiada exitosamente']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>