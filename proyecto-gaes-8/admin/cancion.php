<?php
require_once '../config.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] != 'admin_general' && $_SESSION['usuario_rol'] != 'admin_contenidos')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

// Obtener una canción específica
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("SELECT c.*, n.nombre as nivel_nombre 
                               FROM canciones c 
                               JOIN nivel_dificultad n ON c.nivel_id = n.id 
                               WHERE c.id = ?");
        $stmt->execute([$id]);
        $cancion = $stmt->fetch();
        
        if ($cancion) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'cancion' => $cancion]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Canción no encontrada']);
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
    $artista = limpiarDato($_POST['artista'] ?? '');
    $descripcion = limpiarDato($_POST['descripcion'] ?? '');
    $video_url = limpiarDato($_POST['video_url'] ?? '');
    $nivel = limpiarDato($_POST['nivel'] ?? '');
    
    // Validar título
    if (empty($titulo)) {
        $errores['titulo'] = "El título es obligatorio";
    }
    
    // Validar artista
    if (empty($artista)) {
        $errores['artista'] = "El artista es obligatorio";
    }
    
    // Validar descripción
    if (empty($descripcion)) {
        $errores['descripcion'] = "La descripción es obligatoria";
    }
    
    // Validar nivel
    if (empty($nivel)) {
        $errores['nivel'] = "El nivel es obligatorio";
    }
    
    // Manejar la carga de tablatura
    $tablatura = null;
    if (isset($_FILES['tablatura']) && $_FILES['tablatura']['error'] == 0) {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $filename = $_FILES['tablatura']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errores['tablatura'] = "Formato de archivo no permitido. Use PDF, JPG o PNG.";
        } else {
            $upload_dir = '../uploads/tablaturas/';
            
            // Crear directorio si no existe
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = uniqid() . '.' . $ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['tablatura']['tmp_name'], $destination)) {
                $tablatura = 'uploads/tablaturas/' . $new_filename;
            } else {
                $errores['tablatura'] = "Error al subir el archivo.";
            }
        }
    }
    
    // Si no hay errores, guardar la canción
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
                    // Actualizar canción existente
                    if ($tablatura) {
                        // Si hay una nueva tablatura
                        $stmt = $pdo->prepare("UPDATE canciones SET 
                                              titulo = ?, 
                                              artista = ?, 
                                              descripcion = ?, 
                                              tablatura = ?, 
                                              video_url = ?, 
                                              nivel_id = ? 
                                              WHERE id = ?");
                        $resultado = $stmt->execute([$titulo, $artista, $descripcion, $tablatura, $video_url, $nivelId, $id]);
                    } else {
                        // Si no hay nueva tablatura
                        $stmt = $pdo->prepare("UPDATE canciones SET 
                                              titulo = ?, 
                                              artista = ?, 
                                              descripcion = ?, 
                                              video_url = ?, 
                                              nivel_id = ? 
                                              WHERE id = ?");
                        $resultado = $stmt->execute([$titulo, $artista, $descripcion, $video_url, $nivelId, $id]);
                    }
                    $mensaje = "Canción actualizada correctamente";
                } else {
                    // Insertar nueva canción
                    $stmt = $pdo->prepare("INSERT INTO canciones 
                                          (titulo, artista, descripcion, tablatura, video_url, fecha_creacion, nivel_id, usuario_id) 
                                          VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
                    $resultado = $stmt->execute([$titulo, $artista, $descripcion, $tablatura, $video_url, $nivelId, $_SESSION['usuario_id']]);
                    $mensaje = "Canción creada correctamente";
                }
                
                if ($resultado) {
                    // Respuesta exitosa
                    $response = [
                        'success' => true, 
                        'message' => $mensaje,
                        'redirect' => '../admin-panel.html'
                    ];
                } else {
                    $errores['general'] = "Error al guardar la canción";
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

// Listar todas las canciones
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $stmt = $pdo->query("SELECT c.*, n.nombre as nivel_nombre, u.nombre as autor 
                             FROM canciones c 
                             JOIN nivel_dificultad n ON c.nivel_id = n.id 
                             JOIN usuarios u ON c.usuario_id = u.id 
                             ORDER BY c.fecha_creacion DESC");
        $canciones = $stmt->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'canciones' => $canciones]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}
?>

