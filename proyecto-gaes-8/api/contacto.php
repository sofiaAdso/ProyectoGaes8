<?php
// Incluir configuración de base de datos
require_once '../config/database.php';

// Configurar cabeceras
header('Content-Type: application/json');

// Manejar solicitudes
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos inválidos'
    ]);
    exit;
}

// Validar datos
if (empty($data['nombre']) || empty($data['email']) || empty($data['asunto']) || empty($data['mensaje'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios'
    ]);
    exit;
}

// Insertar mensaje
$sql = "INSERT INTO mensajes (nombre, email, asunto, mensaje) VALUES (?, ?, ?, ?)";
$result = insert($sql, [$data['nombre'], $data['email'], $data['asunto'], $data['mensaje']]);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Mensaje enviado correctamente',
        'redirect' => 'index.html'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al enviar mensaje'
    ]);
}
?>

