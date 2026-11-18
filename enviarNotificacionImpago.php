<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require 'conexion.php';
mysqli_set_charset($conexion, "utf8mb4");

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonRecibido = file_get_contents('php://input');
    $datos = json_decode($jsonRecibido, true);
    
    $id_detalleCompra = (int)($datos['id_detalleCompra'] ?? 0);

    if ($id_detalleCompra <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "ID de compra no válido."]);
        exit;
    }

    $sql = "SELECT 
                dc.id_detalleCompra,
                dc.cantidad,
                dc.subtotal,
                dc.fecha,
                dc.id_estado,
                p.nombre AS producto_nombre,
                u.nombre AS usuario_nombre,
                u.email AS usuario_email,
                e.nombre AS estado_nombre
            FROM detallecompra dc
            INNER JOIN productos p ON dc.id_producto = p.id_producto
            INNER JOIN usuarios u ON dc.id_usuario = u.id_usuario
            INNER JOIN estadopago e ON dc.id_estado = e.id_estado
            WHERE dc.id_detalleCompra = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_detalleCompra);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        // Verificar que el estado sea Impago (id_estado = 3)
        if ((int)$fila['id_estado'] !== 3) {
            http_response_code(400);
            echo json_encode(["error" => "El pago no se encuentra en estado Impago."]);
            exit;
        }

        $emailUsuario = $fila['usuario_email'];
        $nombreUsuario = $fila['usuario_nombre'];
        $productoNombre = $fila['producto_nombre'];
        $cantidad = $fila['cantidad'];
        $subtotal = $fila['subtotal'];
        $estadoNombre = $fila['estado_nombre'];

        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'recytech.support@gmail.com';
            $mail->Password = 'ucha mqaw vkeo kltt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('recytech.support@gmail.com', 'RecyTech');
            $mail->addAddress($emailUsuario, $nombreUsuario);

            $mail->isHTML(true);
            $mail->Subject = 'Actualización de Pago: Marcado como Impago - RecyTech';
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="es">
            <head><meta charset="UTF-8"></head>
            <body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;">
              <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 30px 10px;">
                <tr>
                  <td align="center">
                    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 520px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                      <tr>
                        <td style="background-color: #ffffff; padding: 25px 20px; text-align: center;">
                          <h1 style="color: #050404; margin: 0; font-size: 20px; letter-spacing: 1px; font-weight: 700;">
                            RecyTech - Notificación de Pago
                          </h1>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 30px 25px; color: #333333;">
                          <h2 style="color: #2c3e50; font-size: 18px; margin-top: 0; margin-bottom: 15px;">
                            ¡Hola, ' . htmlspecialchars($nombreUsuario) . '!
                          </h2>
                          <p style="font-size: 14px; line-height: 1.6; color: #555555; margin-bottom: 20px;">
                            Te informamos que el estado de tu compra ha sido actualizado a <b>impago</b>. Por favor, verifica el comprobante enviado o ponte en contacto con nuestro soporte.
                          </p>
                          <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 15px; border-radius: 4px; margin-bottom: 25px;">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #991b1b;">
                              <b>Detalles de la orden #' . $id_detalleCompra . ':</b>
                            </p>
                            <p style="margin: 4px 0; font-size: 14px; color: #333333;">
                              <b>Producto:</b> ' . htmlspecialchars($productoNombre) . ' (x' . $cantidad . ')
                            </p>
                            <p style="margin: 4px 0; font-size: 14px; color: #333333;">
                              <b>Total:</b> $' . number_format($subtotal, 2, ',', '.') . '
                            </p>
                            <p style="margin: 4px 0; font-size: 14px; color: #b91c1c;">
                              <b>Estado actual:</b> ' . htmlspecialchars($estadoNombre) . '
                            </p>
                          </div>
                          <p style="font-size: 12px; line-height: 1.5; color: #777777; margin-bottom: 0;">
                            Gracias por confiar en nosotros.
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
            echo json_encode(["mensaje" => "Correo de notificación de impago enviado exitosamente."]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "No se pudo enviar el correo.", "detallesTecnicos" => $mail->ErrorInfo]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "La compra no existe."]);
    }
    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido."]);
}
$conexion->close();
?>