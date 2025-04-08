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
        // Verificar si se solicita una canción específica
        if (isset($_GET['id'])) {
            // Obtener canción por ID
            $sql = "SELECT c.*, n.nombre as nivel_nombre, u.nombre as autor 
                    FROM canciones c 
                    JOIN niveles n ON c.nivel_id = n.id 
                    JOIN usuarios u ON c.autor_id = u.id 
                    WHERE c.id = ?";
            $cancion = fetchOne($sql, [$_GET['id']]);
            
            if ($cancion) {
                echo json_encode([
                    'success' => true,
                    'cancion' => $cancion
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Canción no encontrada'
                ]);
            }
        } else {
            // Obtener todas las canciones
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
            
            $sql = "SELECT c.id, c.titulo, c.artista, n.nombre as nivel_nombre, 
                    u.nombre as autor, c.fecha_creacion 
                    FROM canciones c 
                    JOIN niveles n ON c.nivel_id = n.id 
                    JOIN usuarios u ON c.autor_id = u.id 
                    ORDER BY c.fecha_creacion DESC";
            
            if ($limit > 0) {
                $sql .= " LIMIT " . $limit;
            }
            
            $canciones = fetchAll($sql);
            
            echo json_encode([
                'success' => true,
                'canciones' => $canciones
            ]);
        }
        break;
        
    case 'POST':
        // Crear nueva canción
        // Verificar si es multipart/form-data o application/json
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        
        if (strpos($contentType, 'multipart/form-data') !== false) {
            // Procesar formulario con archivos
            $titulo = $_POST['titulo'] ?? '';
            $artista = $_POST['artista'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $video_url = $_POST['video_url'] ?? '';
            $nivel_id = $_POST['nivel'] ?? '';
            
            // Validar datos
            if (empty($titulo) || empty($artista) || empty($descripcion) || empty($nivel_id)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Todos los campos son obligatorios'
                ]);
                exit;
            }
            
            // Procesar tablatura si se ha subido
            $tablaturaPath = null;
            if (isset($_FILES['tablatura']) && $_FILES['tablatura']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../uploads/tablaturas/';
                
                // Crear directorio si no existe
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generar nombre único
                $fileName = uniqid() . '_' . basename($_FILES['tablatura']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                // Mover archivo
                if (move_uploaded_file($_FILES['tablatura']['tmp_name'], $uploadFile)) {
                    $tablaturaPath = 'uploads/tablaturas/' . $fileName;
                }
            }
            
            // Insertar canción
            $sql = "INSERT INTO canciones (titulo, artista, descripcion, video_url, nivel_id, tablatura, autor_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $result = insert($sql, [
                $titulo, 
                $artista, 
                $descripcion, 
                $video_url, 
                $nivel_id, 
                $tablaturaPath, 
                $_SESSION['usuario_id']
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Canción creada correctamente',
                    'id' => $result
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al crear canción'
                ]);
            }
        } else {
            // Procesar JSON
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validar datos
            if (empty($data['titulo']) || empty($data['artista']) || empty($data['descripcion']) || 
                empty($data['nivel_id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Todos los campos son obligatorios'
                ]);
                exit;
            }
            
            // Insertar canción
            $sql = "INSERT INTO canciones (titulo, artista, descripcion, video_url, nivel_id, autor_id) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $result = insert($sql, [
                $data['titulo'], 
                $data['artista'], 
                $data['descripcion'], 
                $data['video_url'] ?? '', 
                $data['nivel_id'], 
                $_SESSION['usuario_id']
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Canción creada correctamente',
                    'id' => $result
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al crear canción'
                ]);
            }
        }
        break;
        
    case 'PUT':
        // Actualizar canción existente
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id']) || empty($data['titulo']) || empty($data['artista']) || 
            empty($data['descripcion']) || empty($data['nivel_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ]);
            exit;
        }
        
        // Actualizar canción
        $sql = "UPDATE canciones SET titulo = ?, artista = ?, descripcion = ?, video_url = ?, 
                nivel_id = ?, fecha_actualizacion = NOW() WHERE id = ?";
        $result = update($sql, [
            $data['titulo'], 
            $data['artista'], 
            $data['descripcion'], 
            $data['video_url'] ?? '', 
            $data['nivel_id'], 
            $data['id']
        ]);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Canción actualizada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar canción'
            ]);
        }
        break;
        
    case 'DELETE':
        // Eliminar canción
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de canción requerido'
            ]);
            exit;
        }
        
        // Eliminar canción
        $sql = "DELETE FROM canciones WHERE id = ?";
        $result = delete($sql, [$data['id']]);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Canción eliminada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar canción'
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

