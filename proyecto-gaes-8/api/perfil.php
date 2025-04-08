<?php
// Iniciar sesión
session_start();

// Incluir configuración de base de datos
require_once '../config/database.php';

// Configurar cabeceras
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No hay sesión activa',
        'redirect' => 'login.html'
    ]);
    exit;
}

// Manejar solicitudes
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Obtener datos del perfil
        $sql = "SELECT id, nombre, email, rol, biografia, avatar, fecha_registro FROM usuarios WHERE id = ?";
        $usuario = fetchOne($sql, [$_SESSION['usuario_id']]);
        
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
        break;
        
    case 'POST':
        // Determinar acción
        $action = $_POST['action'] ?? '';
        
        if (empty($action) && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            // Si es JSON, obtener datos
            $data = json_decode(file_get_contents('php://input'), true);
            $action = $data['action'] ?? '';
        }
        
        switch ($action) {
            case 'update_profile':
                // Actualizar perfil
                $nombre = $_POST['nombre'] ?? '';
                $email = $_POST['email'] ?? '';
                $biografia = $_POST['biografia'] ?? '';
                
                // Validar datos
                if (empty($nombre) || empty($email)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Nombre y email son obligatorios'
                    ]);
                    exit;
                }
                
                // Verificar si el email ya existe (si es diferente al actual)
                if ($email !== $_SESSION['usuario_email']) {
                    $sql = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
                    $existente = fetchOne($sql, [$email, $_SESSION['usuario_id']]);
                    
                    if ($existente) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'El email ya está registrado por otro usuario'
                        ]);
                        exit;
                    }
                }
                
                // Procesar avatar si se ha subido
                $avatarPath = null;
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../uploads/avatars/';
                    
                    // Crear directorio si no existe
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Generar nombre único
                    $fileName = uniqid() . '_' . basename($_FILES['avatar']['name']);
                    $uploadFile = $uploadDir . $fileName;
                    
                    // Mover archivo
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                        $avatarPath = 'uploads/avatars/' . $fileName;
                    }
                }
                
                // Actualizar usuario
                $sql = "UPDATE usuarios SET nombre = ?, email = ?, biografia = ?";
                $params = [$nombre, $email, $biografia];
                
                if ($avatarPath) {
                    $sql .= ", avatar = ?";
                    $params[] = $avatarPath;
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $_SESSION['usuario_id'];
                
                $result = update($sql, $params);
                
                if ($result !== false) {
                    // Actualizar datos de sesión
                    $_SESSION['usuario_nombre'] = $nombre;
                    $_SESSION['usuario_email'] = $email;
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Perfil actualizado correctamente',
                        'redirect' => 'perfil.html'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar perfil'
                    ]);
                }
                break;
                
            case 'change_password':
                // Cambiar contraseña
                $data = json_decode(file_get_contents('php://input'), true);
                
                $passwordActual = $data['password_actual'] ?? '';
                $passwordNuevo = $data['password_nuevo'] ?? '';
                $passwordConfirmar = $data['password_confirmar'] ?? '';
                
                // Validar datos
                if (empty($passwordActual) || empty($passwordNuevo) || empty($passwordConfirmar)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Todos los campos son obligatorios'
                    ]);
                    exit;
                }
                
                if ($passwordNuevo !== $passwordConfirmar) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Las contraseñas no coinciden'
                    ]);
                    exit;
                }
                
                // Verificar contraseña actual
                $sql = "SELECT password FROM usuarios WHERE id = ?";
                $usuario = fetchOne($sql, [$_SESSION['usuario_id']]);
                
                if (!$usuario || !password_verify($passwordActual, $usuario['password'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Contraseña actual incorrecta'
                    ]);
                    exit;
                }
                
                // Hashear nueva contraseña
                $passwordHash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
                
                // Actualizar contraseña
                $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
                $result = update($sql, [$passwordHash, $_SESSION['usuario_id']]);
                
                if ($result !== false) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Contraseña actualizada correctamente',
                        'redirect' => 'perfil.html'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar contraseña'
                    ]);
                }
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Acción no válida'
                ]);
                break;
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

