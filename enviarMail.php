<?php
header("Content-Type: text/html; charset=UTF-8");

$url_login = 'localhost/CyberKit/inicioSesion.php';

require 'conexion.php';
mysqli_set_charset($conexion, "utf8mb4");

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailForm = trim($_POST['email'] ?? '');

    if (empty($emailForm) || !filter_var($emailForm, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Debe ingresar un email válido.";
        exit;
    }

    // TRAER DATOS DE BBDD (consulta preparada, sin concatenar)
    $stmt = mysqli_prepare($conexion, "SELECT nombre, email FROM usuarios WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $emailForm);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $datos = mysqli_fetch_assoc($resultado);
        $emailUsuario = $datos['email'];
        $nombre = $datos['nombre'];

        // Clave provisional: los 8 primeros caracteres del email (sin dominio)
        $provisional = substr($emailUsuario, 0, 8);
        $contraEncriptada = password_hash($provisional, PASSWORD_BCRYPT);

        $stmtReset = mysqli_prepare($conexion, "UPDATE usuarios SET contrasena = ? WHERE email = ?");
        mysqli_stmt_bind_param($stmtReset, "ss", $contraEncriptada, $emailUsuario);
        mysqli_stmt_execute($stmtReset);

        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'recytech.support@gmail.com';
            $mail->Password   = ' ucha mqaw vkeo kltt'; // pasala a variable de entorno
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->Timeout    = 15;

            $mail->setFrom('recytech.support@gmail.com', 'recytech');
            $mail->addAddress($emailUsuario, $nombre);

            $mail->isHTML(true);
            $mail->Subject = 'Reestablecer Contraseña - recytech';
            $mail->Body = '
            <!DOCTYPE html>
              <html lang="es">
              <head><meta charset="UTF-8"></head>
              <body style="margin:0;padding:0;background-color:#f4f6f9;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#f4f6f9;padding:30px 10px;">
                  <tr>
                    <td align="center">
                      <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width:520px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.08);">
                        <tr>
                          <td style="background-color:rgba(106,0,184,0.35);padding:25px 20px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:20px;letter-spacing:1px;font-weight:700;">
                              RECYTECH
                            </h1>
                            <p style="color:#f3e8ff;margin:5px 0 0 0;font-size:13px;">Recuperación de acceso</p>
                          </td>
                        </tr>
                        <tr>
                          <td style="padding:30px 25px;color:#333333;">
                            <h2 style="color:#2c3e50;font-size:18px;margin-top:0;margin-bottom:15px;">
                              ¡Hola, ' . htmlspecialchars($nombre) . '!
                            </h2>
                            <p style="font-size:14px;line-height:1.6;color:#555555;margin-bottom:20px;">
                              Hemos procesado tu solicitud de restablecimiento de contraseña correctamente.
                            </p>
                            <div style="background-color:#f7f0fd;border-left:4px solid #6a00b8;padding:15px;border-radius:4px;margin-bottom:25px;">
                              <p style="margin:0;font-size:13px;color:#4a0080;">
                                <b>Tu nueva clave de acceso provisional es:</b>
                              </p>
                              <p style="margin:8px 0 0 0;font-size:18px;font-weight:bold;color:#6a00b8;letter-spacing:1px;">
                                ' . htmlspecialchars($provisional) . '
                              </p>
                              <p style="margin:5px 0 0 0;font-size:11px;color:#7b2fbf;">
                                (Los primeros 8 caracteres de tu email, sin el @)
                              </p>
                            </div>
                            <p style="font-size:14px;line-height:1.6;color:#555555;margin-bottom:0;">
                              Ingresá a la plataforma con tu email y esta clave provisional.
                            </p>
                            <p style="font-size:12px;line-height:1.5;color:#777777;margin-top:20px;margin-bottom:0;">
                              <b>Recomendación de seguridad:</b> Te sugerimos cambiar esta contraseña provisional una vez ingreses a la plataforma desde tu perfil.
                            </p>
                          </td>
                        </tr>
                        <tr>
                          <td style="background-color:#f9fafb;padding:15px 25px;border-top:1px solid #edf2f7;text-align:center;">
                            <p style="margin:0;font-size:11px;color:#9aa0a6;">
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
        } catch (Exception $e) {
            error_log("Error PHPMailer: " . $mail->ErrorInfo);
            http_response_code(500);
            echo "Error al enviar el correo. Intente más tarde.";
        }
    } else {
        http_response_code(404);
        echo "No existe un usuario registrado con el email ingresado.";
    }
} else {
    http_response_code(405);
    echo "Método no permitido.";
}

$conexion->close();