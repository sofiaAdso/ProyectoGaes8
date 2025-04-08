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
        // Verificar si se solicita un usuario específico
        if (isset($_GET['id'])) {
            // Obtener usuario por ID
            $sql = "SELECT id, nombre, email, rol, biografia, avatar, fecha_registro, ultimo_acceso FROM usuarios WHERE id = ?";
            $usuario = fetchOne($sql, [$_GET['id']]);
            
            if ($usuario) {
                echo json_encode([
                    'success' => true,
                    'usuario' => $usuario
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }
        } else {
            // Obtener todos los usuarios
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
            
            $sql = "SELECT id, nombre, email, rol, fecha_registro, ultimo_acceso FROM usuarios ORDER BY fecha_registro DESC";
            
            if ($limit > 0) {
                $sql .= " LIMIT " . $limit;
            }
            
            $usuarios = fetchAll($sql);
            
            echo json_encode([
                'success' => true,
                'usuarios' => $usuarios
            ]);
        }
        break;
        
    case 'POST':
        // Crear nuevo usuario
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['nombre']) || empty($data['email']) || empty($data['password'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ]);
            exit;
        }
        
        // Verificar si el email ya existe
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $existente = fetchOne($sql, [$data['email']]);
        
        if ($existente) {
            echo json_encode([
                'success' => false,
                'message' => 'El email ya está registrado'
            ]);
            exit;
        }
        
        // Hashear contraseña
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Determinar rol (por defecto 'aprendiz')
        $rol = $data['rol'] ?? 'aprendiz';
        
        // Insertar usuario
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, biografia) VALUES (?, ?, ?, ?, ?)";
        $userId = insert($sql, [
            $data['nombre'], 
            $data['email'], 
            $passwordHash, 
            $rol,
            $data['biografia'] ?? ''
        ]);
        
        if ($userId) {
            echo json_encode([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'id' => $userId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al crear usuario'
            ]);
        }
        break;
        
    case 'PUT':
        // Actualizar usuario existente
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id']) || empty($data['nombre']) || empty($data['email'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ]);
            exit;
        }
        
        // Verificar si el email ya existe (si es diferente al actual)
        $sql = "SELECT email FROM usuarios WHERE id = ?";
        $usuario = fetchOne($sql, [$data['id']]);
        
        if ($usuario && $usuario['email'] !== $data['email']) {
            $sql = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
            $existente = fetchOne($sql, [$data['email'], $data['id']]);
            
            if ($existente) {
                echo json_encode([
                    'success' => false,
                    'message' => 'El email ya está registrado por otro usuario'
                ]);
                exit;
            }
        }
        
        // Preparar consulta
        $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, biografia = ?";
        $params = [
            $data['nombre'], 
            $data['email'], 
            $data['rol'] ?? 'aprendiz',
            $data['biografia'] ?? ''
        ];
        
        // Si se proporciona una nueva contraseña, actualizarla
        if (!empty($data['password'])) {
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            $sql .= ", password = ?";
            $params[] = $passwordHash;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $data['id'];
        
        // Actualizar usuario
        $result = update($sql, $params);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar usuario'
            ]);
        }
        break;
        
    case 'DELETE':
        // Eliminar usuario
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar datos
        if (empty($data['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de usuario requerido'
            ]);
            exit;
        }
        
        // No permitir eliminar al propio usuario
        if ($data['id'] == $_SESSION['usuario_id']) {
            echo json_encode([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ]);
            exit;
        }
        
        // Eliminar usuario
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $result = delete($sql, [$data['id']]);
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar usuario'
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

