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
        $nombre = limpiarDato($input['nombre'] ?? '');
        $email = limpiarDato($input['email'] ?? '');
        $asunto = limpiarDato($input['asunto'] ?? '');
        $mensaje = limpiarDato($input['mensaje'] ?? '');
    } else {
        // Si los datos vienen como POST normal
        $nombre = limpiarDato($_POST['nombre'] ?? '');
        $email = limpiarDato($_POST['email'] ?? '');
        $asunto = limpiarDato($_POST['asunto'] ?? '');
        $mensaje = limpiarDato($_POST['mensaje'] ?? '');
    }
    
    // Validar nombre
    if (empty($nombre)) {
        $errores['nombre'] = "El nombre es obligatorio";
    }
    
    // Validar email
    if (empty($email)) {
        $errores['email'] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "Formato de correo electrónico inválido";
    }
    
    // Validar asunto
    if (empty($asunto)) {
        $errores['asunto'] = "El asunto es obligatorio";
    }
    
    // Validar mensaje
    if (empty($mensaje)) {
        $errores['mensaje'] = "El mensaje es obligatorio";
    }
    
    // Si no hay errores, guardar el mensaje
    if (empty($errores)) {
        try {
            // Insertar mensaje en la base de datos
            $stmt = $pdo->prepare("INSERT INTO contacto_mensajes (nombre, email, asunto, mensaje, fecha) VALUES (?, ?, ?, ?, NOW())");
            $resultado = $stmt->execute([$nombre, $email, $asunto, $mensaje]);
            
            if ($resultado) {
                // Respuesta exitosa
                $response = [
                    'success' => true, 
                    'message' => 'Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.',
                    'redirect' => 'index.html'
                ];
            } else {
                $errores['general'] = "Error al enviar el mensaje";
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

