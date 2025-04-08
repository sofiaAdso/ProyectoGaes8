<?php
// Iniciar sesión
session_start();

// Incluir configuración de base de datos
require_once '../../config/database.php';

// Configurar cabeceras
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Acceso denegado',
        'redirect' => 'login.html'
    ]);
    exit;
}

// Obtener todos los niveles
$sql = "SELECT * FROM niveles ORDER BY id";
$niveles = fetchAll($sql);

echo json_encode([
    'success' => true,
    'niveles' => $niveles
]);
?>

