<?php
// database/eliminar_cuenta.php
session_start();
include("conexion.php");

header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id']) || !isset($_SESSION['rol'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Obtener datos de la solicitud
$input = json_decode(file_get_contents('php://input'), true);
$tipo = $input['tipo'] ?? 'temporal'; // 'permanente' o 'temporal'
$accion = $input['accion'] ?? '';

if ($accion !== 'eliminar') {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    exit;
}

$user_id = $_SESSION['id'];
$user_rol = $_SESSION['rol'];
$user_nombre = $_SESSION['nombre'];

try {
    if ($tipo === 'permanente') {
        // ELIMINACIÓN DEFINITIVA
        
        if ($user_rol === 'visitante') {
            // Eliminar visitante
            $stmt = $conn->prepare("DELETE FROM usuario_visitante WHERE id = ?");
            $stmt->execute([$user_id]);
            
            // Eliminar favoritos del visitante
            $stmt = $conn->prepare("DELETE FROM favoritos WHERE usuario_id = ?");
            $stmt->execute([$user_id]);
            
        } elseif ($user_rol === 'propietario') {
            // Primero, eliminar publicaciones del propietario
            $stmt = $conn->prepare("DELETE FROM publicaciones WHERE propietario_id = ?");
            $stmt->execute([$user_id]);
            
            // Eliminar propietario
            $stmt = $conn->prepare("DELETE FROM usuario_propietario WHERE id = ?");
            $stmt->execute([$user_id]);
        }
        
        // Registrar en logs
        $log = $conn->prepare("INSERT INTO logs_actividad (usuario_nombre, rol, accion) VALUES (:nombre, :rol, :accion)");
        $log->execute([
            ':nombre' => $user_nombre,
            ':rol' => $user_rol,
            ':accion' => 'Eliminó cuenta permanentemente'
        ]);
        
        // Destruir sesión
        session_destroy();
        
        echo json_encode(['success' => true, 'message' => 'Cuenta eliminada permanentemente']);
        
    } else {
        // DESACTIVACIÓN TEMPORAL
        
        if ($user_rol === 'visitante') {
            $stmt = $conn->prepare("UPDATE usuario_visitante SET estado = 0 WHERE id = ?");
            $stmt->execute([$user_id]);
        } elseif ($user_rol === 'propietario') {
            $stmt = $conn->prepare("UPDATE usuario_propietario SET estado = 0 WHERE id = ?");
            $stmt->execute([$user_id]);
            
            // También desactivar publicaciones
            $stmt = $conn->prepare("UPDATE publicaciones SET estado = 0 WHERE propietario_id = ?");
            $stmt->execute([$user_id]);
        }
        
        // Registrar en logs
        $log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, usuario_nombre, rol, accion) VALUES (:id, :nombre, :rol, :accion)");
        $log->execute([
            ':id' => $user_id,
            ':nombre' => $user_nombre,
            ':rol' => $user_rol,
            ':accion' => 'Desactivó cuenta temporalmente'
        ]);
        
        // Destruir sesión
        session_destroy();
        
        echo json_encode(['success' => true, 'message' => 'Cuenta desactivada temporalmente']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>