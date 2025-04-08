<?php
require_once '../config.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] != 'admin_general' && $_SESSION['usuario_rol'] != 'admin_contenidos')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

// Obtener una lección específica
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("SELECT l.*, c.nombre as categoria_nombre, n.nombre as nivel_nombre 
                               FROM lecciones l 
                               JOIN categorias c ON l.categoria_id = c.id 
                               JOIN nivel_dificultad n ON l.nivel_id = n.id 
                               WHERE l.id = ?");
        $stmt->execute([$id]);
        $leccion = $stmt->fetch();
        
        if ($leccion) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'leccion' => $leccion]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Lección no encontrada']);
        }
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario (desde POST o JSON)
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input) {
        // Si los datos vienen como JSON
        $id = intval($input['id'] ?? 0);
        $titulo = limpiarDato($input['titulo'] ?? '');
        $descripcion = limpiarDato($input['descripcion'] ?? '');
        $contenido = $input['contenido'] ?? '';
        $nivel = limpiarDato($input['nivel'] ?? '');
        $categoria = limpiarDato($input['categoria'] ?? '');
    } else {
        // Si los datos vienen como POST normal
        $id = intval($_POST['id'] ?? 0);
        $titulo = limpiarDato($_POST['titulo'] ?? '');
        $descripcion = limpiarDato($_POST['descripcion'] ?? '');
        $contenido = $_POST['contenido'] ?? '';
        $nivel = limpiarDato($_POST['nivel'] ?? '');
        $categoria = limpiarDato($_POST['categoria'] ?? '');
    }
    
    // Inicializar variables de error
    $errores = [];
    $response = ['success' => false, 'errores' => []];
    
    // Validar título
    if (empty($titulo)) {
        $errores['titulo'] = "El título es obligatorio";
    }
    
    // Validar descripción
    if (empty($descripcion)) {
        $errores['descripcion'] = "La descripción es obligatoria";
    }
    
    // Validar contenido
    if (empty($contenido) || $contenido === "<p><br></p>") {
        $errores['contenido'] = "El contenido es obligatorio";
    }
    
    // Validar nivel
    if (empty($nivel)) {
        $errores['nivel'] = "El nivel es obligatorio";
    }
    
    // Validar categoría
    if (empty($categoria)) {
        $errores['categoria'] = "La categoría es obligatoria";
    }
    
    // Si no hay errores, guardar la lección
    if (empty($errores)) {
        try {
            // Obtener IDs de nivel y categoría
            $stmtNivel = $pdo->prepare("SELECT id FROM nivel_dificultad WHERE nombre = ?");
            $stmtNivel->execute([$nivel]);
            $nivelId = $stmtNivel->fetchColumn();
            
            $stmtCategoria = $pdo->prepare("SELECT id FROM categorias WHERE nombre = ?");
            $stmtCategoria->execute([$categoria]);
            $categoriaId = $stmtCategoria->fetchColumn();
            
            if (!$nivelId) {
                $errores['nivel'] = "Nivel no válido";
            }
            
            if (!$categoriaId) {
                $errores['categoria'] = "Categoría no válida";
            }
            
            if (empty($errores)) {
                if ($id > 0) {
                    // Actualizar lección existente
                    $stmt = $pdo->prepare("UPDATE lecciones SET 
                                          titulo = ?, 
                                          descripcion = ?, 
                                          contenido = ?, 
                                          nivel_id = ?, 
                                          categoria_id = ?, 
                                          fecha_actualizacion = NOW() 
                                          WHERE id = ?");
                    $resultado = $stmt->execute([$titulo, $descripcion, $contenido, $nivelId, $categoriaId, $id]);
                    $mensaje = "Lección actualizada correctamente";
                } else {
                    // Insertar nueva lección
                    $stmt = $pdo->prepare("INSERT INTO lecciones 
                                          (titulo, descripcion, contenido, fecha_creacion, nivel_id, categoria_id, usuario_id) 
                                          VALUES (?, ?, ?, NOW(), ?, ?, ?)");
                    $resultado = $stmt->execute([$titulo, $descripcion, $contenido, $nivelId, $categoriaId, $_SESSION['usuario_id']]);
                    $mensaje = "Lección creada correctamente";
                }
                
                if ($resultado) {
                    // Respuesta exitosa
                    $response = [
                        'success' => true, 
                        'message' => $mensaje,
                        'redirect' => '../admin-panel.html'
                    ];
                } else {
                    $errores['general'] = "Error al guardar la lección";
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

// Listar todas las lecciones
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $stmt = $pdo->query("SELECT l.*, c.nombre as categoria_nombre, n.nombre as nivel_nombre, u.nombre as autor 
                             FROM lecciones l 
                             JOIN categorias c ON l.categoria_id = c.id 
                             JOIN nivel_dificultad n ON l.nivel_id = n.id 
                             JOIN usuarios u ON l.usuario_id = u.id 
                             ORDER BY l.fecha_creacion DESC");
        $lecciones = $stmt->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'lecciones' => $lecciones]);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
}
?>

