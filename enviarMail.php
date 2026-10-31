<?php
// Texto plano: el JS lee la respuesta tal cual y la muestra en el modal
header("Content-Type: text/plain; charset=UTF-8");

$url_login = 'http://localhost/Cyberkit/iniciarSesion.php';

require 'conexion.php';
mysqli_set_charset($conexion, "utf8mb4");

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido.";
    exit;
}

$emailForm = trim($_POST['email'] ?? '');

if ($emailForm === '' || !filter_var($emailForm, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Debe ingresar un email válido.";
    exit;
}

// 1) Buscar el usuario
$stmt = mysqli_prepare($conexion, "SELECT nombre, email FROM usuarios WHERE email = ? LIMIT 1");
if (!$stmt) {
    http_response_code(500);
    echo "Error en la consulta: " . mysqli_error($conexion);
    exit;
}
mysqli_stmt_bind_param($stmt, "s", $emailForm);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (!$resultado || mysqli_num_rows($resultado) === 0) {
    http_response_code(404);
    echo "No existe un usuario registrado con ese email.";
    exit;
}

$datos        = mysqli_fetch_assoc($resultado);
$emailUsuario = $datos['email'];
$nombre       = $datos['nombre'];

// 2) Clave provisional = nombre
$contraEncriptada = password_hash($nombre, PASSWORD_BCRYPT);

$stmtReset = mysqli_prepare($conexion, "UPDATE usuarios SET contrasena = ? WHERE email = ?");
mysqli_stmt_bind_param($stmtReset, "ss", $contraEncriptada, $emailUsuario);
mysqli_stmt_execute($stmtReset);

if (mysqli_stmt_affected_rows($stmtReset) === 0) {
    http_response_code(500);
    echo "No se pudo actualizar la contraseña. Intente más tarde.";
    exit;
}

// 3) Enviar el correo
$mail = new PHPMailer(true);

try {
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'recytech@gmail.com';
    $mail->Password   = 'oytq mfud xkjc ebdl'; // mejor moverla a variable de entorno
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('recytech@gmail.com', 'recytech');
    $mail->addAddress($emailUsuario, $nombre);

    $mail->isHTML(true);
    $mail->Subject = 'Reestablecer Contraseña - recytech';
    $mail->Body = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
    </head>
    <body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
      <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 30px 10px;">
        <tr>
          <td align="center">
            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 520px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
              <tr>
                <td style="background-color: #0d8a42; padding: 25px 20px; text-align: center;">
                  <h1 style="color: #ffffff; margin: 0; font-size: 20px; letter-spacing: 1px; font-weight: 700;">
                    Instituto Superior de Formación Docente y Técnica N°31
                  </h1>
                  <p style="color: #e0f2e9; margin: 5px 0 0 0; font-size: 13px;">
                    Asociación Cooperadora
                  </p>
                </td>
              </tr>
              <tr>
                <td style="padding: 30px 25px; color: #333333;">
                  <h2 style="color: #2c3e50; font-size: 18px; margin-top: 0; margin-bottom: 15px;">
                    ¡Hola, ' . htmlspecialchars($nombre) . '!
                  </h2>
                  <p style="font-size: 14px; line-height: 1.6; color: #555555; margin-bottom: 20px;">
                    Hemos procesado tu solicitud de restablecimiento de contraseña correctamente.
                  </p>

                  <div style="background-color: #f0fdf4; border-left: 4px solid #0d8a42; padding: 15px; border-radius: 4px; margin-bottom: 25px;">
                    <p style="margin: 0; font-size: 13px; color: #166534;">
                      <b>Tu nueva clave de acceso provisional es:</b>
                    </p>
                    <p style="margin: 5px 0 0 0; font-size: 11px; color: #15803d;">
                      (Tu nombre, tal como lo registraste)
                    </p>
                  </div>

                  <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $url_login . '" style="background-color: #0d8a42; color: #ffffff; padding: 12px 28px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 6px; display: inline-block;">
                      Iniciar Sesión Ahora
                    </a>
                  </div>

                  <p style="font-size: 12px; line-height: 1.5; color: #777777; margin-bottom: 0;">
                    <b>Recomendación de seguridad:</b> Te sugerimos cambiar esta contraseña provisional una vez ingreses a la plataforma desde tu perfil.
                  </p>
                </td>
              </tr>
              <tr>
                <td style="background-color: #f9fafb; padding: 15px 25px; border-top: 1px solid #edf2f7; text-align: center;">
                  <p style="margin: 0; font-size: 11px; color: #9aa0a6;">
                    Si no solicitaste este cambio, por favor ponte en contacto con nuestro equipo de soporte.
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </body>
    </html>';

    $mail->send();
    http_response_code(200);
    echo "El correo de recuperación se envió correctamente.";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error al enviar el correo: " . $e->getMessage();
}

mysqli_close($conexion);