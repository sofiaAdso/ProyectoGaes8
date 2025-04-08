<?php
require_once 'config.php';

// Verificar si hay una sesión activa
if (isset($_SESSION['usuario_id'])) {
    // Si se solicita como JSON (para uso en JavaScript)
    if (isset($_GET['format']) && $_GET['format'] === 'json') {
        header('Content-Type: application/json');
        echo json_encode([
            'autenticado' => true,
            'usuario' => [
                'id' => $_SESSION['usuario_id'],
                'nombre' => $_SESSION['usuario_nombre'],
                'email' => $_SESSION['usuario_email'],
                'rol' => $_SESSION['usuario_rol']
            ]
        ]);
        exit;
    }
    
    // Si se solicita como HTML (para visualización)
    echo "<h2>Información de sesión:</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    echo "<p>Usuario autenticado: " . $_SESSION['usuario_nombre'] . "</p>";
    echo "<p>Rol: " . $_SESSION['usuario_rol'] . "</p>";
    
    echo "<p><a href='logout.php'>Cerrar sesión</a></p>";
} else {
    // Si se solicita como JSON
    if (isset($_GET['format']) && $_GET['format'] === 'json') {
        header('Content-Type: application/json');
        echo json_encode(['autenticado' => false]);
        exit;
    }
    
    // Si se solicita como HTML
    echo "<p>No hay sesión de usuario activa</p>";
    echo "<p><a href='login.html'>Iniciar sesión</a></p>";
}
?>

