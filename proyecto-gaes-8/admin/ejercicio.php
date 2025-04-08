<?php
require_once '../config.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] != 'admin_general' && $_SESSION['usuario_rol'] != 'admin_contenidos')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

// Obtener un ejercicio específico
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("SELECT e.*, n.nombre as nivel_nombre 
                               FROM ejercicios e 
                               JOIN nivel_dificultad n ON e.nivel_id = n.id 
                               WHERE e.id = ?");
        $stmt->execute([$id]);
        $ejercicio = $stmt->fetch();
        
        if ($ejercicio) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'ejercicio' => $ejercicio]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ejercicio no encontrado']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inicializar variables de error
    $errores = [];
    $response = ['success' => false, 'errores' => []];
    
    // Obtener datos del formulario
    $id = intval($_POST['id'] ?? 0);
    $titulo = limpiarDato($_POST['titulo'] ?? '');
    $descripcion = limpiarDato($_POST['descripcion'] ?? '');
    $instrucciones = $_POST['instrucciones'] ?? '';
    $nivel = limpiarDato($_POST['nivel'] ?? '');
    $tipo = limpiarDato($_POST['tipo'] ?? '');
    
    // Validar título
    if (empty($titulo)) {
        $errores['titulo'] = "El título es obligatorio";
    }
    
    // Validar descripción
    if (empty($descripcion)) {
        $errores['descripcion'] = "La descripción es obligatoria";
    }
    
    // Validar instrucciones
    if (empty($instrucciones) || $instrucciones === "<p><br></p>") {
        $errores['instrucciones'] = "Las instrucciones son obligatorias";
    }
    
    // Validar nivel
    if (empty($nivel)) {
        $errores['nivel'] = "El nivel es obligatorio";
    }
    
    // Validar tipo
    if (empty($tipo)) {
        $errores['tipo'] = "El tipo de ejercicio es obligatorio";
    }
    
    // Manejar la carga de archivos
    $partitura = null;
    if (isset($_FILES['partitura']) && $_FILES['partitura']['error'] == 0) {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $filename = $_FILES['partitura']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errores['partitura'] = "Formato de archivo no permitido. Use PDF, JPG o PNG.";
        } else {
            $upload_dir = '../uploads/partituras/';
            
            // Crear directorio si no existe
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = uniqid() . '.' . $ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['partitura']['tmp_name'], $destination)) {
                $partitura = 'uploads/partituras/' . $new_filename;
            } else {
                $errores['partitura'] = "Error al subir el archivo.";
            }
        }
    }
    
    // Si no hay errores, guardar el ejercicio
    if (empty($errores)) {
        try {
            // Obtener ID de nivel
            $stmtNivel = $pdo->prepare("SELECT id FROM nivel_dificultad WHERE nombre = ?");
            $stmtNivel->execute([$nivel]);
            $nivelId = $stmtNivel->fetchColumn();
            
            if (!$nivelId) {
                $errores['nivel'] = "Nivel no válido";
            } else {
                if ($id > 0) {
                    // Actualizar ejercicio existente
                    if ($partitura) {
                        // Si hay una nueva partitura
                        $stmt = $pdo->prepare("UPDATE ejercicios SET 
                                              titulo = ?, 
                                              descripcion = ?, 
                                              instrucciones = ?, 
                                              partitura = ?, 
                                              nivel_id = ?, 
                                              tipo = ?, 
                                              fecha_actualizacion = NOW() 
                                              WHERE id = ?");
                        $resultado = $stmt->execute([$titulo, $descripcion, $instrucciones, $partitura, $nivelId, $tipo, $id]);
                    } else {
                        // Si no hay nueva partitura
                        $stmt = $pdo->prepare("UPDATE ejercicios SET 
                                              titulo = ?, 
                                              descripcion = ?, 
                                              instrucciones = ?, 
                                              nivel_id = ?, 
                                              tipo = ?, 
                                              fecha_actualizacion = NOW() 
                                              WHERE id = ?");
                        $resultado = $stmt->execute([$titulo, $descripcion, $instrucciones, $nivelId, $tipo, $id]);
                    }
                    $mensaje = "Ejercicio actualizado correctamente";
                } else {
                    // Insertar nuevo ejercicio
                    $stmt = $pdo->prepare("INSERT INTO ejercicios 
                                          (titulo, descripcion, instrucciones, partitura, fecha_creacion, nivel_id, tipo, usuario_id) 
                                          VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)");
                    $resultado = $stmt->execute([$titulo, $descripcion, $instrucciones, $partitura, $nivelId, $tipo, $_SESSION['usuario_id']]);
                    $mensaje = "Ejercicio creado correctamente";
                }
                
                if ($resultado) {
                    // Respuesta exitosa
                    $response = [
                        'success' => true, 
                        'message' => $mensaje,
                        'redirect' => '../admin-panel.html'
                    ];
                } else {
                    $errores['general'] = "Error al guardar el ejercicio";
                }
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

// Listar todos los ejercicios
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $stmt = $pdo->query("SELECT e.*, n.nombre as nivel_nombre, u.nombre as autor 
                             FROM ejercicios e 
                             JOIN nivel_dificultad n ON e.nivel_id = n.id 
                             JOIN usuarios u ON e.usuario_id = u.id 
                             ORDER BY e.fecha_creacion DESC");
        $ejercicios = $stmt->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'ejercicios' => $ejercicios]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}
?>

