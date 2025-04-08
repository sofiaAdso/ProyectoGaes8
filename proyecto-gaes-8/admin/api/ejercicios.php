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
        // Verificar si se solicita un ejercicio específico
        if (isset($_GET['id'])) {
            // Obtener ejercicio por ID
            $sql = "SELECT e.*, n.nombre as nivel_nombre, u.nombre as autor 
                    FROM ejercicios e 
                    JOIN niveles n ON e.nivel_id = n.id 
                    JOIN usuarios u ON e.autor_id = u.id 
                    WHERE e.id = ?";
            $ejercicio = fetchOne($sql, [$_GET['id']]);
            
            if ($ejercicio) {
                echo json_encode([
                    'success' => true,
                    'ejercicio' => $ejercicio
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Ejercicio no encontrado'
                ]);
            }
        } else {
            // Obtener todos los ejercicios
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
            
            $sql = "SELECT e.id, e.titulo, e.tipo, n.nombre as nivel_nombre, 
                    u.nombre as autor, e.fecha_creacion 
                    FROM ejercicios e 
                    JOIN niveles n ON e.nivel_id = n.id 
                    JOIN usuarios u ON e.autor_id = u.id 
                    ORDER BY e.fecha_creacion DESC";
            
            if ($limit > 0) {
                $sql .= " LIMIT " . $limit;
            }
            
            $ejercicios = fetchAll($sql);
            
            echo json_encode([
                'success' => true,
                'ejercicios' => $ejercicios
            ]);
        }
        break;
        
    case 'POST':
        // Crear nuevo ejercicio
        // Verificar si es multipart/form-data o application/json
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        
        if (strpos($contentType, 'multipart/form-data') !== false) {
            // Procesar formulario con archivos
            $titulo = $_POST['titulo'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $instrucciones = $_POST['instrucciones'] ?? '';
            $nivel_id = $_POST['nivel'] ?? '';
            $tipo = $_POST['tipo'] ?? '';
            
            // Validar datos
            if (empty($titulo) || empty($descripcion) || empty($instrucciones) || 
                empty($nivel_id) || empty($tipo)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Todos los campos son obligatorios'
                ]);
                exit;
            }
            
            // Procesar partitura si se ha subido
            $partituraPath = null;
            if (isset($_FILES['partitura']) && $_FILES['partitura']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../uploads/partituras/';
                
                // Crear directorio si no existe
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generar nombre único
                $fileName = uniqid() . '_' . basename($_FILES['partitura']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                // Mover archivo
                if (move_uploaded_file($_FILES['partitura']['tmp_name'], $uploadFile)) {
                    $partituraPath = 'uploads/partituras/' . $fileName;
                }
            }
            
            // Insertar ejercicio
            $sql = "INSERT INTO ejercicios (titulo, descripcion, instrucciones, nivel_id, tipo, partitura, autor_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $result = insert($sql, [
                $titulo, 
                $descripcion, 
                $instrucciones, 
                $nivel_id, 
                $tipo, 
                $partituraPath, 
                $_SESSION['usuario_id']
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Ejercicio creado correctamente',
                    'id' => $result
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al crear ejercicio'
                ]);
            }
        } else {
            // Procesar JSON
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validar datos
            if (empty($data['titulo']) || empty($data['descripcion']) || empty($data['instrucciones']) || 
                empty($data['nivel_id']) || empty($data['tipo'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Todos los campos son obligatorios'
                ]);
                exit;
            }
            
            // Insertar ejercicio
            $sql = "INSERT INTO ejercicios (titulo, descripcion, instrucciones, nivel_id, tipo, autor_id) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $result = insert($sql, [
                $data['titulo'], 
                $data['descripcion'], 
                $data['instrucciones'], 
                $data['nivel_id'], 
                $data['tipo'], 
                $_SESSION['usuario_id']
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Ejercicio creado correctamente',
                    'id' => $result
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al crear ejercicio'
                ]);
            }
        }
        break;
        
    case 'PUT':
        // Actualizar ejercicio existente
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id']) || empty($data['titulo']) || empty($data['descripcion']) || 
            empty($data['instrucciones']) || empty($data['nivel_id']) || empty($data['tipo'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ]);
            exit;
        }
        
        // Actualizar ejercicio
        $sql = "UPDATE ejercicios SET titulo = ?, descripcion = ?, instrucciones = ?, nivel_id = ?, 
                tipo = ?, fecha_actualizacion = NOW() WHERE id = ?";
        $result = update($sql, [
            $data['titulo'], 
            $data['descripcion'], 
            $data['instrucciones'], 
            $data['nivel_id'], 
            $data['tipo'], 
            $data['id']
        ]);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Ejercicio actualizado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar ejercicio'
            ]);
        }
        break;
        
    case 'DELETE':
        // Eliminar ejercicio
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de ejercicio requerido'
            ]);
            exit;
        }
        
        // Eliminar ejercicio
        $sql = "DELETE FROM ejercicios WHERE id = ?";
        $result = delete($sql, [$data['id']]);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Ejercicio eliminado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar ejercicio'
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

