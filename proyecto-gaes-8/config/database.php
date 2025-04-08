<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'guitarmaster');

// Crear conexión
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Establecer charset
    $conn->set_charset("utf8");
    
    return $conn;
}

// Función para ejecutar consultas
function executeQuery($sql, $params = []) {
    $conn = getConnection();
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $types = '';
        $bindParams = [];
        
        // Determinar tipos de parámetros
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $bindParams[] = $param;
        }
        
        // Añadir tipos como primer elemento
        array_unshift($bindParams, $types);
        
        // Bind parameters
        call_user_func_array([$stmt, 'bind_param'], $bindParams);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para obtener un solo registro
function fetchOne($sql, $params = []) {
    $result = executeQuery($sql, $params);
    return $result->fetch_assoc();
}

// Función para obtener múltiples registros
function fetchAll($sql, $params = []) {
    $result = executeQuery($sql, $params);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para insertar registros
function insert($sql, $params = []) {
    $conn = getConnection();
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $types = '';
        $bindParams = [];
        
        // Determinar tipos de parámetros
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $bindParams[] = $param;
        }
        
        // Añadir tipos como primer elemento
        array_unshift($bindParams, $types);
        
        // Bind parameters
        call_user_func_array([$stmt, 'bind_param'], $bindParams);
    }
    
    $stmt->execute();
    $insertId = $conn->insert_id;
    $stmt->close();
    $conn->close();
    
    return $insertId;
}

// Función para actualizar registros
function update($sql, $params = []) {
    $conn = getConnection();
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $types = '';
        $bindParams = [];
        
        // Determinar tipos de parámetros
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $bindParams[] = $param;
        }
        
        // Añadir tipos como primer elemento
        array_unshift($bindParams, $types);
        
        // Bind parameters
        call_user_func_array([$stmt, 'bind_param'], $bindParams);
    }
    
    $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    
    return $affectedRows;
}

// Función para eliminar registros
function delete($sql, $params = []) {
    return update($sql, $params);
}
?>

