<?php
require_once '../config.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] != 'admin_general' && $_SESSION['usuario_rol'] != 'admin_contenidos')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

// Obtener estadísticas
try {
    // Contar usuarios
    $stmtUsuarios = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $totalUsuarios = $stmtUsuarios->fetchColumn();
    
    // Contar lecciones
    $stmtLecciones = $pdo->query("SELECT COUNT(*) as total FROM lecciones");
    $totalLecciones = $stmtLecciones->fetchColumn();
    
    // Contar ejercicios
    $stmtEjercicios = $pdo->query("SELECT COUNT(*) as total FROM ejercicios");
    $totalEjercicios = $stmtEjercicios->fetchColumn();
    
    // Contar canciones
    $stmtCanciones = $pdo->query("SELECT COUNT(*) as total FROM canciones");
    $totalCanciones = $stmtCanciones->fetchColumn();
    
    // Preparar respuesta
    $stats = [
        'usuarios' => $totalUsuarios,
        'lecciones' => $totalLecciones,
        'ejercicios' => $totalEjercicios,
        'canciones' => $totalCanciones
    ];
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'stats' => $stats]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>

