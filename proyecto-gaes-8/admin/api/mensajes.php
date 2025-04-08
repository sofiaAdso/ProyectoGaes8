<?php
// Iniciar sesión
session_start();

// Incluir configuración de base de datos
require_once '../../config/database.php';

// Configurar cabeceras
header('Content-Type: application/json');

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] !== 'admin_general' && $_SESSION['usuario_rol'] !== 'admin_contenidos')) {
    echo json_encode([
        'success' => false,
        'message' => 'Acceso denegado',
        'redirect' => 'login.html'
    ]);
    exit;
}

// Manejar solicitudes
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Verificar si se solicita un mensaje específico
        if (isset($_GET['id'])) {
            // Obtener mensaje por ID
            $sql = "SELECT * FROM mensajes WHERE id = ?";
            $mensaje = fetchOne($sql, [$_GET['id']]);
            
            if ($mensaje) {
                // Marcar como leído si no lo está
                if ($mensaje['leido'] == 0) {
                    $updateSql = "UPDATE mensajes SET leido = 1 WHERE id = ?";
                    update($updateSql, [$_GET['id']]);
                }
                
                echo json_encode([
                    'success' => true,
                    'mensaje' => $mensaje
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Mensaje no encontrado'
                ]);
            }
        } else {
            // Obtener todos los mensajes
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
            
            $sql = "SELECT * FROM mensajes ORDER BY fecha DESC";
            
            if ($limit > 0) {
                $sql .= " LIMIT " . $limit;
            }
            
            $mensajes = fetchAll($sql);
            
            echo json_encode([
                'success' => true,
                'mensajes' => $mensajes
            ]);
        }
        break;
        
    case 'PUT':
        // Actualizar mensaje (marcar como leído/no leído)
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de mensaje requerido'
            ]);
            exit;
        }
        
        $leido = isset($data['leido']) ? (int)$data['leido'] : 1;
        
        // Actualizar mensaje
        $sql = "UPDATE mensajes SET leido = ? WHERE id = ?";
        $result = update($sql, [$leido, $data['id']]);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Mensaje actualizado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar mensaje'
            ]);
        }
        break;
        
    case 'DELETE':
        // Eliminar mensaje
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de mensaje requerido'
            ]);
            exit;
        }
        
        // Eliminar mensaje
        $sql = "DELETE FROM mensajes WHERE id = ?";
        $result = delete($sql, [$data['id']]);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Mensaje eliminado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar mensaje'
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
        break;
}
?>

