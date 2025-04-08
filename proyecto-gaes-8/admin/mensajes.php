<?php
require_once '../config.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] != 'admin_general')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

// Obtener un mensaje específico
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        // Marcar como leído
        $stmtUpdate = $pdo->prepare("UPDATE contacto_mensajes SET leido = 1 WHERE id = ?");
        $stmtUpdate->execute([$id]);
        
        // Obtener el mensaje
        $stmt = $pdo->prepare("SELECT * FROM contacto_mensajes WHERE id = ?");
        $stmt->execute([$id]);
        $mensaje = $stmt->fetch();
        
        if ($mensaje) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'mensaje' => $mensaje]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Mensaje no encontrado']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}

// Eliminar un mensaje
if ($_SERVER["REQUEST_METHOD"] == "DELETE" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM contacto_mensajes WHERE id = ?");
        $resultado = $stmt->execute([$id]);
        
        if ($resultado) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Mensaje eliminado correctamente']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el mensaje']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}

// Listar todos los mensajes
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $stmt = $pdo->query("SELECT * FROM contacto_mensajes ORDER BY fecha DESC");
        $mensajes = $stmt->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'mensajes' => $mensajes]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}
?>

