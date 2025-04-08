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
        // Verificar si se solicita una lección específica
        if (isset($_GET['id'])) {
            // Obtener lección por ID
            $sql = "SELECT l.*, n.nombre as nivel_nombre, c.nombre as categoria_nombre, u.nombre as autor 
                    FROM lecciones l 
                    JOIN niveles n ON l.nivel_id = n.id 
                    JOIN categorias c ON l.categoria_id = c.id 
                    JOIN usuarios u ON l.autor_id = u.id 
                    WHERE l.id = ?";
            $leccion = fetchOne($sql, [$_GET['id']]);
            
            if ($leccion) {
                echo json_encode([
                    'success' => true,
                    'leccion' => $leccion
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lección no encontrada'
                ]);
            }
        } else {
            // Obtener todas las lecciones
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
            
            $sql = "SELECT l.id, l.titulo, l.descripcion, n.nombre as nivel_nombre, c.nombre as categoria_nombre, 
                    u.nombre as autor, l.fecha_creacion 
                    FROM lecciones l 
                    JOIN niveles n ON l.nivel_id = n.id 
                    JOIN categorias c ON l.categoria_id = c.id 
                    JOIN usuarios u ON l.autor_id = u.id 
                    ORDER BY l.fecha_creacion DESC";
            
            if ($limit > 0) {
                $sql .= " LIMIT " . $limit;
            }
            
            $lecciones = fetchAll($sql);
            
            echo json_encode([
                'success' => true,
                'lecciones' => $lecciones
            ]);
        }
        break;
        
    case 'POST':
        // Crear nueva lección
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['titulo']) || empty($data['descripcion']) || empty($data['contenido']) || 
            empty($data['nivel_id']) || empty($data['categoria_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ]);
            exit;
        }
        
        // Insertar lección
        $sql = "INSERT INTO lecciones (titulo, descripcion, contenido, nivel_id, categoria_id, autor_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $result = insert($sql, [
            $data['titulo'], 
            $data['descripcion'], 
            $data['contenido'], 
            $data['nivel_id'], 
            $data['categoria_id'], 
            $_SESSION['usuario_id']
        ]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Lección creada correctamente',
                'id' => $result
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al crear lección'
            ]);
        }
        break;
        
    case 'PUT':
        // Actualizar lección existente
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id']) || empty($data['titulo']) || empty($data['descripcion']) || 
            empty($data['contenido']) || empty($data['nivel_id']) || empty($data['categoria_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ]);
            exit;
        }
        
        // Actualizar lección
        $sql = "UPDATE lecciones SET titulo = ?, descripcion = ?, contenido = ?, nivel_id = ?, 
                categoria_id = ?, fecha_actualizacion = NOW() WHERE id = ?";
        $result = update($sql, [
            $data['titulo'], 
            $data['descripcion'], 
            $data['contenido'], 
            $data['nivel_id'], 
            $data['categoria_id'], 
            $data['id']
        ]);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Lección actualizada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar lección'
            ]);
        }
        break;
        
    case 'DELETE':
        // Eliminar lección
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de lección requerido'
            ]);
            exit;
        }
        
        // Eliminar lección
        $sql = "DELETE FROM lecciones WHERE id = ?";
        $result = delete($sql, [$data['id']]);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Lección eliminada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar lección'
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

