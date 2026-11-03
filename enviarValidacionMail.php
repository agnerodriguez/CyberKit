<?php
// Desactivar buffer para ver errores directamente si se ejecuta en navegador
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=UTF-8");

include("conexion.php");

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

// Leer JSON recibido por POST
$json_data = file_get_contents('php://input');
$datos = json_decode($json_data, true);

$id_detalleCompra = (int)($datos['id_detalleCompra'] ?? $_GET['id_detalleCompra'] ?? 0);

if ($id_detalleCompra <= 0) {
    http_response_code(400);
    echo json_encode([
        "error" => "ID de compra no válido o ausente."
    ]);
    exit;
}

$sql = "SELECT 
            dc.id_detalleCompra,
            dc.subtotal,
            p.nombre AS producto_nombre,
            u.nombre AS usuario_nombre,
            u.email AS usuario_email,
            e.nombre AS estado_nombre
        FROM detalleCompra dc
        INNER JOIN productos p ON dc.id_producto = p.id_producto
        INNER JOIN usuarios u ON dc.id_usuario = u.id_usuario
        INNER JOIN estadopago e ON dc.id_estado = e.id_estado
        WHERE dc.id_detalleCompra = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_detalleCompra);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $emailUsuario = $fila['usuario_email'];
    $nombreUsuario = $fila['usuario_nombre'];
    $productoNombre = $fila['producto_nombre'];
    $subtotal = $fila['subtotal'];
    $estadoNombre = $fila['estado_nombre'];

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'recytech.support@gmail.com';
        $mail->Password   = 'ucha mqaw vkeo kltt'; // Contraseña de aplicación
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('recytech.support@gmail.com', 'RecyTech');
        $mail->addAddress($emailUsuario, $nombreUsuario);

        $mail->isHTML(true);
        $mail->Subject = 'Estado de tu compra - RecyTech';
        $mail->Body    = '
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"></head>
        <body style="font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 20px;">
        <div style="max-width: 500px; background-color: #ffffff; padding: 20px; border-radius: 8px; margin: 0 auto;">
            <h2 style="color: #0d8a42;">Notificación de Compra</h2>
            <p>Hola <b>' . htmlspecialchars($nombreUsuario) . '</b>,</p>
            <p>Te informamos sobre el estado actualizado de tu orden de compra <b>#' . $id_detalleCompra . '</b>:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><b>Producto:</b></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($productoNombre) . '</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><b>Total:</b></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">$' . number_format($subtotal, 2) . '</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><b>Estado:</b></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold; color: #0d8a42;">' . htmlspecialchars($estadoNombre) . '</td>
            </tr>
            </table>
        </div>
        </body>
        </html>';

        $mail->send();

        http_response_code(200);
        echo json_encode(["mensaje" => "Correo enviado exitosamente al usuario."]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "error" => "No se pudo enviar el correo de notificación.",
            "detallesTecnicos" => $mail->ErrorInfo
        ]);
    }
} else {
    http_response_code(404);
    echo json_encode([
        "error" => "La compra solicitada no existe."
    ]);
}
$stmt->close();
$conexion->close();
?>