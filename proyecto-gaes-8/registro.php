<?php
require_once 'config.php';

// Inicializar variables de error
$errores = [];
$response = ['success' => false, 'errores' => []];

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario (desde POST o JSON)
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input) {
        // Si los datos vienen como JSON
        $nombre = limpiarDato($input['name'] ?? '');
        $email = limpiarDato($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirmPassword'] ?? '';
        $isAdmin = isset($input['isAdmin']) ? (bool)$input['isAdmin'] : false;
    } else {
        // Si los datos vienen como POST normal
        $nombre = limpiarDato($_POST['name'] ?? '');
        $email = limpiarDato($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm-password'] ?? '';
        $isAdmin = isset($_POST['is-admin']) ? true : false;
    }
    
    // Determinar el rol basado en el checkbox
    $rol = $isAdmin ? 'admin_general' : 'aprendiz';
    
    // Validar nombre
    if (empty($nombre)) {
        $errores['name'] = "El nombre es obligatorio";
    } elseif (strlen($nombre) < 3) {
        $errores['name'] = "El nombre debe tener al menos 3 caracteres";
    }
    
    // Validar email
    if (empty($email)) {
        $errores['email'] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "Formato de correo electrónico inválido";
    } else {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errores['email'] = "Este correo electrónico ya está registrado";
        }
    }
    
    // Validar contraseña
    if (empty($password)) {
        $errores['password'] = "La contraseña es obligatoria";
    } elseif (strlen($password) < 6) {
        $errores['password'] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    // Validar confirmación de contraseña
    if ($password !== $confirmPassword) {
        $errores['confirmPassword'] = "Las contraseñas no coinciden";
    }
    
    // Si no hay errores, registrar al usuario
    if (empty($errores)) {
        try {
            // Hash de la contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar usuario en la base de datos
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
            $resultado = $stmt->execute([$nombre, $email, $password_hash, $rol]);
            
            if ($resultado) {
                // Respuesta exitosa
                $response = [
                    'success' => true, 
                    'message' => 'Registro exitoso. Ahora puedes iniciar sesión.',
                    'redirect' => 'login.html'
                ];
            } else {
                $errores['general'] = "Error al registrar el usuario";
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

