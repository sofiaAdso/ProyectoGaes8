<?php
// Iniciar sesión
session_start();

// Incluir configuración de base de datos
require_once '../config/database.php';

// Configurar cabeceras
header('Content-Type: application/json');

// Manejar solicitudes
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Verificar si el usuario está autenticado
        if (isset($_SESSION['usuario_id'])) {
            // Obtener datos del usuario
            $sql = "SELECT id, nombre, email, rol, fecha_registro FROM usuarios WHERE id = ?";
            $usuario = fetchOne($sql, [$_SESSION['usuario_id']]);
            
            if ($usuario) {
                // Actualizar último acceso
                $updateSql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
                update($updateSql, [$_SESSION['usuario_id']]);
                
                echo json_encode([
                    'success' => true,
                    'usuario' => $usuario
                ]);
            } else {
                // Usuario no encontrado
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }
        } else {
            // No hay sesión activa
            echo json_encode([
                'success' => false,
                'message' => 'No hay sesión activa'
            ]);
        }
        break;
        
    case 'POST':
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            exit;
        }
        
        // Determinar acción
        $action = $data['action'] ?? '';
        
        switch ($action) {
            case 'login':
                // Validar datos
                if (empty($data['email']) || empty($data['password'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Email y contraseña son obligatorios'
                    ]);
                    exit;
                }
                
                // Buscar usuario por email
                $sql = "SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?";
                $usuario = fetchOne($sql, [$data['email']]);
                
                if (!$usuario) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Credenciales inválidas'
                    ]);
                    exit;
                }
                
                // Verificar contraseña
                if (!password_verify($data['password'], $usuario['password'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Credenciales inválidas'
                    ]);
                    exit;
                }
                
                // Iniciar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                
                // Actualizar último acceso
                $updateSql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
                update($updateSql, [$usuario['id']]);
                
                // Eliminar contraseña del resultado
                unset($usuario['password']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso',
                    'usuario' => $usuario,
                    'redirect' => 'index.html'
                ]);
                break;
                
            case 'register':
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
                $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
                $userId = insert($sql, [$data['nombre'], $data['email'], $passwordHash, $rol]);
                
                if ($userId) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registro exitoso',
                        'redirect' => 'login.html?registro=exitoso'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al registrar usuario'
                    ]);
                }
                break;
                
            case 'logout':
                // Cerrar sesión
                session_unset();
                session_destroy();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Sesión cerrada correctamente',
                    'redirect' => 'index.html?logout=success'
                ]);
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

