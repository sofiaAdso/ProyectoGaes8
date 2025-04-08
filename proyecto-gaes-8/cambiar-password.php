<?php
require_once 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario (desde POST o JSON)
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input) {
        // Si los datos vienen como JSON
        $passwordActual = $input['password_actual'] ?? '';
        $passwordNuevo = $input['password_nuevo'] ?? '';
        $passwordConfirmar = $input['password_confirmar'] ?? '';
    } else {
        // Si los datos vienen como POST normal
        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordNuevo = $_POST['password_nuevo'] ?? '';
        $passwordConfirmar = $_POST['password_confirmar'] ?? '';
    }
    
    // Inicializar variables de error
    $errores = [];
    $response = ['success' => false, 'errores' => []];
    
    // Validar contraseña actual
    if (empty($passwordActual)) {
        $errores['password_actual'] = "La contraseña actual es obligatoria";
    }
    
    // Validar nueva contraseña
    if (empty($passwordNuevo)) {
        $errores['password_nuevo'] = "La nueva contraseña es obligatoria";
    } elseif (strlen($passwordNuevo) < 6) {
        $errores['password_nuevo'] = "La nueva contraseña debe tener al menos 6 caracteres";
    }
    
    // Validar confirmación de contraseña
    if ($passwordNuevo !== $passwordConfirmar) {
        $errores['password_confirmar'] = "Las contraseñas no coinciden";
    }
    
    // Si no hay errores, verificar la contraseña actual y actualizar
    if (empty($errores)) {
        try {
            // Obtener la contraseña actual del usuario
            $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['usuario_id']]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($passwordActual, $usuario['password'])) {
                // Contraseña actual correcta, actualizar a la nueva
                $passwordHash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                $resultado = $stmt->execute([$passwordHash, $_SESSION['usuario_id']]);
                
                if ($resultado) {
                    // Respuesta exitosa
                    $response = [
                        'success' => true, 
                        'message' => 'Contraseña actualizada correctamente',
                        'redirect' => 'perfil.html'
                    ];
                } else {
                    $errores['general'] = "Error al actualizar la contraseña";
                }
            } else {
                $errores['password_actual'] = "La contraseña actual es incorrecta";
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
?>

