<?php
// test-db.php
require_once 'config.php';

try {
    // Intentar ejecutar una consulta simple
    $stmt = $pdo->query("SELECT 1");
    echo "<p style='color:green;'>Conexión a la base de datos exitosa!</p>";
    
    // Verificar si la tabla usuarios existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green;'>La tabla 'usuarios' existe.</p>";
    } else {
        echo "<p style='color:red;'>La tabla 'usuarios' no existe. Por favor, ejecuta el script SQL para crearla.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Error de conexión: " . $e->getMessage() . "</p>";
}
?>