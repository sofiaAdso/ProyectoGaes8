<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'guitarmaster';
$username = 'root';  // Cambia esto según tu configuración
$password = '';      // Cambia esto según tu configuración

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
  // Configurar el modo de error para que lance excepciones
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // Configurar el modo de obtención por defecto
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error de conexión: " . $e->getMessage());
}

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Función para limpiar datos de entrada
function limpiarDato($dato) {
  $dato = trim($dato);
  $dato = stripslashes($dato);
  $dato = htmlspecialchars($dato);
  return $dato;
}
?>

