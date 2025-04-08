<?php
require_once '../config.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] != 'admin_general')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

// Obtener un usuario específico
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, email, rol, biografia, avatar, fecha_registro, ultimo_acceso FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'usuario' => $usuario]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}

// Listar usuarios
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
        
        $query = "SELECT id, nombre, email, rol, fecha_registro, ultimo_acceso FROM usuarios ORDER BY fecha_registro DESC";
        
        if ($limit > 0) {
            $query .= " LIMIT " . $limit;
        }
        
        $stmt = $pdo->query($query);
        $usuarios = $stmt->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'usuarios' => $usuarios]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}

// Actualizar usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $id = intval($_POST['id'] ?? 0);
    $nombre = limpiarDato($_POST['nombre'] ?? '');
    $email = limpiarDato($_POST['email'] ?? '');
    $rol = limpiarDato($_POST['rol'] ?? '');
    $biografia = limpiarDato($_POST['biografia'] ?? '');
    
    // Inicializar variables de error
    $errores = [];
    $response = ['success' => false, 'errores' => []];
    
    // Validar nombre
    if (empty($nombre)) {
        $errores['nombre'] = "El nombre es obligatorio";
    }
    
    // Validar email
    if (empty($email)) {
        $errores['email'] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "Formato de correo electrónico inválido";
    } else {
        // Verificar si el email ya existe (excepto para el usuario actual)
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->rowCount() > 0) {
            $errores['email'] = "Este correo electrónico ya está registrado por otro usuario";
        }
    }
    
    // Validar rol
    if (empty($rol)) {
        $errores['rol'] = "El rol es obligatorio";
    } elseif (!in_array($rol, ['aprendiz', 'admin_general', 'admin_contenidos'])) {
        $errores['rol'] = "Rol no válido";
    }
    
    // Si no hay errores, actualizar el usuario
    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ?, biografia = ? WHERE id = ?");
            $resultado = $stmt->execute([$nombre, $email, $rol, $biografia, $id]);
            
            if ($resultado) {
                // Respuesta exitosa
                $response = [
                    'success' => true, 
                    'message' => 'Usuario actualizado correctamente',
                    'redirect' => 'usuarios.html'
                ];
            } else {
                $errores['general'] = "Error al actualizar el usuario";
            }
        } catch (PDOException $e) {
            $errores['general'] = "Error en la base de datos: " . $e->getMessage();
        }
    }
    
    // Si hay errores, incluirlos en la respuesta
    if (!empty($errores)) {
        $response['errores'] = $errores;
    }
    
    // Enviar respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Eliminar usuario
if ($_SERVER["REQUEST_METHOD"] == "DELETE" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // No permitir eliminar al propio usuario
    if ($id == $_SESSION['usuario_id']) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propio usuario']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $resultado = $stmt->execute([$id]);
        
        if ($resultado) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}
?>

