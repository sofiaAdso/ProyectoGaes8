<?php
// Mostrar todos los errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Parámetros de conexión
$host = 'localhost';
$dbname = 'guitar_master';
$username = 'root';
$password = '';

echo "<h2>Diagnóstico de conexión a la base de datos</h2>";

// Verificar si PHP tiene habilitada la extensión PDO
if (!extension_loaded('pdo')) {
    echo "<p style='color:red;'>ERROR: La extensión PDO no está habilitada en PHP.</p>";
    echo "<p>Solución: Habilita la extensión PDO en tu archivo php.ini.</p>";
    exit;
}

// Verificar si PHP tiene habilitado el driver PDO para MySQL
if (!extension_loaded('pdo_mysql')) {
    echo "<p style='color:red;'>ERROR: El driver PDO para MySQL no está habilitado en PHP.</p>";
    echo "<p>Solución: Habilita la extensión pdo_mysql en tu archivo php.ini.</p>";
    exit;
}

// Verificar si el servidor MySQL está en ejecución
echo "<p>Intentando conectar al servidor MySQL en $host...</p>";
try {
    // Intentar conectar solo al servidor, sin especificar la base de datos
    $pdo_server = new PDO("mysql:host=$host", $username, $password);
    echo "<p style='color:green;'>✓ Conexión al servidor MySQL exitosa.</p>";
    
    // Verificar si la base de datos existe
    $stmt = $pdo_server->query("SHOW DATABASES LIKE '$dbname'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green;'>✓ La base de datos '$dbname' existe.</p>";
        
        // Intentar conectar a la base de datos específica
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<p style='color:green;'>✓ Conexión a la base de datos '$dbname' exitosa.</p>";
            
            // Verificar si la tabla usuarios existe
            $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color:green;'>✓ La tabla 'usuarios' existe.</p>";
                
                // Mostrar estructura de la tabla
                $stmt = $pdo->query("DESCRIBE usuarios");
                $columnas = $stmt->fetchAll();
                
                echo "<h3>Estructura de la tabla 'usuarios':</h3>";
                echo "<table border='1'>";
                echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Predeterminado</th><th>Extra</th></tr>";
                
                foreach ($columnas as $columna) {
                    echo "<tr>";
                    echo "<td>" . $columna['Field'] . "</td>";
                    echo "<td>" . $columna['Type'] . "</td>";
                    echo "<td>" . $columna['Null'] . "</td>";
                    echo "<td>" . $columna['Key'] . "</td>";
                    echo "<td>" . $columna['Default'] . "</td>";
                    echo "<td>" . $columna['Extra'] . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
                
                // Mostrar usuarios registrados
                $stmt = $pdo->query("SELECT id, nombre, email, rol, fecha_registro FROM usuarios");
                $usuarios = $stmt->fetchAll();
                
                if (count($usuarios) > 0) {
                    echo "<h3>Usuarios registrados:</h3>";
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Fecha Registro</th></tr>";
                    
                    foreach ($usuarios as $usuario) {
                        echo "<tr>";
                        echo "<td>" . $usuario['id'] . "</td>";
                        echo "<td>" . $usuario['nombre'] . "</td>";
                        echo "<td>" . $usuario['email'] . "</td>";
                        echo "<td>" . $usuario['rol'] . "</td>";
                        echo "<td>" . $usuario['fecha_registro'] . "</td>";
                        echo "</tr>";
                    }
                    
                    echo "</table>";
                } else {
                    echo "<p>No hay usuarios registrados en la base de datos.</p>";
                    echo "<p>Puedes ejecutar el siguiente SQL para crear la estructura de la base de datos:</p>";
                    echo "<pre>" . file_get_contents('db.sql') . "</pre>";
                }
            } else {
                echo "<p style='color:red;'>✗ La tabla 'usuarios' no existe.</p>";
                echo "<p>Solución: Ejecuta el script SQL para crear la tabla:</p>";
                echo "<pre>" . file_get_contents('db.sql') . "</pre>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>✗ Error al conectar a la base de datos '$dbname': " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>✗ La base de datos '$dbname' no existe.</p>";
        echo "<p>Solución: Ejecuta el siguiente SQL para crear la base de datos:</p>";
        echo "<pre>CREATE DATABASE $dbname;</pre>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Error al conectar al servidor MySQL: " . $e->getMessage() . "</p>";
    echo "<h3>Posibles soluciones:</h3>";
    echo "<ol>";
    echo "<li>Verifica que el servidor MySQL esté en ejecución.</li>";
    echo "<li>Comprueba que el usuario '$username' tenga acceso al servidor.</li>";
    echo "<li>Si estás usando XAMPP/WAMP/MAMP, asegúrate de que el servicio MySQL esté iniciado.</li>";
    echo "<li>Verifica que los datos de conexión (host, usuario, contraseña) sean correctos.</li>";
    echo "</ol>";
}

// Información adicional para depuración
echo "<h3>Información del entorno:</h3>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>PDO Drivers disponibles: " . implode(", ", PDO::getAvailableDrivers()) . "</li>";
echo "<li>Sistema operativo: " . PHP_OS . "</li>";
echo "<li>Servidor web: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "</ul>";

echo "<h3>Pasos para solucionar problemas de conexión:</h3>";
echo "<ol>";
echo "<li>Verifica que el servidor MySQL esté en ejecución.</li>";
echo "<li>Asegúrate de que la base de datos 'guitar_master' exista.</li>";
echo "<li>Comprueba que el usuario 'root' tenga permisos para acceder a la base de datos.</li>";
echo "<li>Si usas una contraseña para el usuario 'root', actualiza el archivo config.php.</li>";
echo "<li>Ejecuta el script SQL (db.sql) para crear las tablas necesarias.</li>";
echo "</ol>";
?>

