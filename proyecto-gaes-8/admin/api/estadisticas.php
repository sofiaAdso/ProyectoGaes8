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

// Obtener estadísticas
$stats = [];

// Contar usuarios
$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = fetchOne($sql);
$stats['usuarios'] = $result['total'];

// Contar lecciones
$sql = "SELECT COUNT(*) as total FROM lecciones";
$result = fetchOne($sql);
$stats['lecciones'] = $result['total'];

// Contar ejercicios
$sql = "SELECT COUNT(*) as total FROM ejercicios";
$result = fetchOne($sql);
$stats['ejercicios'] = $result['total'];

// Contar canciones
$sql = "SELECT COUNT(*) as total FROM canciones";
$result = fetchOne($sql);
$stats['canciones'] = $result['total'];

// Contar mensajes no leídos
$sql = "SELECT COUNT(*) as total FROM mensajes WHERE leido = 0";
$result = fetchOne($sql);
$stats['mensajes_no_leidos'] = $result['total'];

// Devolver estadísticas
echo json_encode([
    'success' => true,
    'stats' => $stats
]);
?>

