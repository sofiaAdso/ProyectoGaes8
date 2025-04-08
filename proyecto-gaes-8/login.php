<?php
require_once 'config.php';

// Inicializar variables de error
$errores = [];
$response = ['success' => false, 'errores' => []];

// Procesar el formulario cuando se envía
if ($_SERVER["METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener datos del formulario (desde POST o JSON)
  $input = json_decode(file_get_contents('php://input'), true);
  if ($input) {
      // Si los datos vienen como JSON
      $email = isset($input['email']) ? trim($input['email']) : '';
      $password = isset($input['password']) ? $input['password'] : '';
  } else {
      // Si los datos vienen como POST normal
      $email = isset($_POST['email']) ? trim($_POST['email']) : '';
      $password = isset($_POST['password']) ? $_POST['password'] : '';
  }
  
  // Validar email
  if (empty($email)) {
      $errores['email'] = "El correo electrónico es obligatorio";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errores['email'] = "Formato de correo electrónico inválido";
  }
  
  // Validar contraseña
  if (empty($password)) {
      $errores['password'] = "La contraseña es obligatoria";
  }
  
  // Si no hay errores, verificar credenciales
  if (empty($errores)) {
      try {
          // Buscar usuario por email
          $stmt = $pdo->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
          $stmt->execute([$email]);
          $usuario = $stmt->fetch();
          
          if ($usuario && password_verify($password, $usuario['password'])) {
              // Credenciales correctas, iniciar sesión
              $_SESSION['usuario_id'] = $usuario['id'];
              $_SESSION['usuario_nombre'] = $usuario['nombre'];
              $_SESSION['usuario_email'] = $usuario['email'];
              $_SESSION['usuario_rol'] = $usuario['rol'];
              
              // Actualizar último acceso
              $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
              $stmt->execute([$usuario['id']]);
              
              // Determinar redirección según el rol
              $redirect = 'index.html'; // Por defecto, redirigir al inicio
              if ($usuario['rol'] == 'admin_general' || $usuario['rol'] == 'admin_contenidos') {
                  $redirect = 'admin-panel.html'; // Administradores van al panel
              }
              
              // Respuesta exitosa
              $response = [
                  'success' => true, 
                  'message' => 'Inicio de sesión exitoso',
                  'redirect' => $redirect,
                  'usuario' => [
                      'id' => $usuario['id'],
                      'nombre' => $usuario['nombre'],
                      'email' => $usuario['email'],
                      'rol' => $usuario['rol']
                  ]
              ];
          } else {
              $errores['general'] = "Correo electrónico o contraseña incorrectos";
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
?>

