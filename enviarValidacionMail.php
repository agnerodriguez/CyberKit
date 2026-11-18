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
require_once 'fpdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Convierte texto UTF-8 a ISO-8859-1 para compatibilidad con FPDF (tildes y ñ)
 */
function textoPDF($texto) {
    return mb_convert_encoding((string)$texto, 'ISO-8859-1', 'UTF-8');
}

function generarReciboPDF($datos) {
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->SetMargins(20, 20, 20);
    $pdf->SetAutoPageBreak(false);
    $pdf->AddPage();

    // 1. LOGO (arriba a la izquierda)
    $rutaLogo = 'iconos/logoEmpresa.png';
    if (file_exists($rutaLogo)) {
        $pdf->Image($rutaLogo, 20, 18, 38);
    }

    // TÍTULO Y FECHA (arriba a la derecha)
    $pdf->SetFont('Arial', 'B', 22);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(90, 22);
    $pdf->Cell(100, 10, textoPDF('RECIBO DE PAGO'), 0, 1, 'R');

    // Formatear fecha a dd/mm/aaaa
    $fechaFormateada = !empty($datos['fecha']) ? date('d/m/Y', strtotime($datos['fecha'])) : date('d/m/Y');

    $pdf->SetXY(90, 34);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(68, 7, textoPDF('Fecha: '), 0, 0, 'R');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(32, 7, textoPDF($fechaFormateada), 0, 1, 'R');

    // INFORMACIÓN DEL CLIENTE
    $pdf->SetXY(20, 68);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 8, textoPDF('INFORMACIÓN DEL CLIENTE'), 0, 1, 'L');

    // Fila Nombre
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(91, 184, 42); // Verde #5BB82A
    $pdf->Cell(26, 7, textoPDF('Nombre:'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetTextColor(30, 30, 30);
    $pdf->Cell(0, 7, textoPDF($datos['usuario_nombre']), 0, 1, 'L');

    // Fila E-mail
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(91, 184, 42); // Verde #5BB82A
    $pdf->Cell(26, 7, textoPDF('E-mail'), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetTextColor(30, 30, 30);
    $pdf->Cell(0, 7, textoPDF($datos['usuario_email']), 0, 1, 'L');

    // TABLA DE DETALLE (Ancho total: 170mm -> 80 + 30 + 30 + 30)
    $pdf->SetXY(20, 102);
    $pdf->SetFillColor(255, 78, 0);    // Naranja #FF4E00
    $pdf->SetTextColor(255, 255, 255); // Texto blanco
    $pdf->SetFont('Arial', 'B', 10);

    $pdf->Cell(80, 10, textoPDF('  PRODUCTO'), 0, 0, 'L', true);
    $pdf->Cell(30, 10, textoPDF('CANTIDAD'), 0, 0, 'C', true);
    $pdf->Cell(30, 10, textoPDF('ESTADO'), 0, 0, 'C', true);
    $pdf->Cell(30, 10, textoPDF('TOTAL  '), 0, 1, 'R', true);

    // Fila de datos del producto
    $subtotalFormateado = '$' . number_format((float)$datos['subtotal'], 2, ',', '.');
    $yFila = $pdf->GetY() + 3;
    $pdf->SetY($yFila);
    $pdf->SetTextColor(30, 30, 30);
    $pdf->SetFont('Arial', '', 10);

    $pdf->Cell(80, 10, textoPDF('  ' . $datos['producto_nombre']), 0, 0, 'L');
    $pdf->Cell(30, 10, textoPDF($datos['cantidad']), 0, 0, 'C');
    $pdf->Cell(30, 10, textoPDF($datos['estado_nombre']), 0, 0, 'C');
    $pdf->Cell(30, 10, textoPDF($subtotalFormateado . '  '), 0, 1, 'R');

    // Línea inferior debajo del producto
    $yLinea = $pdf->GetY() + 3;
    $pdf->SetDrawColor(50, 50, 50);
    $pdf->SetLineWidth(0.3);
    $pdf->Line(20, $yLinea, 190, $yLinea);

    // 5. RECUADRO TOTAL (alineado a la derecha)
    $yTotal = $yLinea + 12;
    $pdf->SetXY(125, $yTotal);
    $pdf->SetFillColor(255, 165, 128); // Salmón claro #FFA580
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(30, 11, textoPDF('  TOTAL'), 0, 0, 'L', true);
    $pdf->Cell(35, 11, textoPDF($subtotalFormateado . '  '), 0, 1, 'R', true);

    // 6. PIE DE PÁGINA (línea y mensaje final)
    $pdf->SetDrawColor(50, 50, 50);
    $pdf->SetLineWidth(0.3);
    $pdf->Line(20, 255, 190, 255);

    $pdf->SetXY(20, 258);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(170, 6, textoPDF('¡Gracias por tu compra!'), 0, 0, 'R');

    // Devuelve el contenido binario del PDF en memoria
    return $pdf->Output('S');
}

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
        $esPagado = ((int)$fila['id_estado'] === 1);

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

            // Configurar contenido y adjunto según el estado del pago
            if ($esPagado) {
                $pdfBinario = generarReciboPDF($fila);
                $nombrePdf = "Recibo_Pago_RecyTech_" . str_pad($id_detalleCompra, 6, "0", STR_PAD_LEFT) . ".pdf";
                $mail->addStringAttachment($pdfBinario, $nombrePdf, 'base64', 'application/pdf');

                $mail->Subject = '¡Pago Aprobado! - Recibo de Pago #' . $id_detalleCompra . ' - RecyTech';
                $mensajePrincipal = 'Te informamos que tu comprobante ha sido verificado y tu pago ha sido <b>aprobado</b> exitosamente. Adjunto a este correo encontrarás tu <b>Recibo de Pago en PDF</b>.';
                $etiquetaMonto = 'Total pagado:';
                $bgCaja = '#f0fdf4';
                $bordeCaja = '#0d8a42';
                $colorTituloCaja = '#166534';
                $colorEstado = '#15803d';
                $mensajePie = 'Gracias por confiar en nosotros.';
            } else {
                $mail->Subject = 'Estado de Pago: Impago - Orden #' . $id_detalleCompra . ' - RecyTech';
                $mensajePrincipal = 'Te informamos que el pago correspondiente a tu orden figura como <b>impago</b> o no pudo ser aprobado tras la verificación. Por favor, revisa tu comprobante o comunícate con nosotros para regularizar la operación.';
                $etiquetaMonto = 'Total a pagar:';
                $bgCaja = '#fef2f2';
                $bordeCaja = '#dc2626';
                $colorTituloCaja = '#991b1b';
                $colorEstado = '#b91c1c';
                $mensajePie = 'Si ya realizaste el pago o crees que se trata de un error, por favor contáctanos.';
            }

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
                            ' . $mensajePrincipal . '
                          </p>
                          <div style="background-color: ' . $bgCaja . '; border-left: 4px solid ' . $bordeCaja . '; padding: 15px; border-radius: 4px; margin-bottom: 25px;">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: ' . $colorTituloCaja . ';">
                              <b>Detalles de la orden #' . $id_detalleCompra . ':</b>
                            </p>
                            <p style="margin: 4px 0; font-size: 14px; color: #333333;">
                              <b>Producto:</b> ' . htmlspecialchars($productoNombre) . ' (x' . $cantidad . ')
                            </p>
                            <p style="margin: 4px 0; font-size: 14px; color: #333333;">
                              <b>' . $etiquetaMonto . '</b> $' . number_format($subtotal, 2, ',', '.') . '
                            </p>
                            <p style="margin: 4px 0; font-size: 14px; color: ' . $colorEstado . ';">
                              <b>Estado actual:</b> ' . htmlspecialchars($estadoNombre) . '
                            </p>
                          </div>
                          <p style="font-size: 12px; line-height: 1.5; color: #777777; margin-bottom: 0;">
                            ' . $mensajePie . '
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
            echo json_encode([
                "mensaje" => $esPagado 
                    ? "Correo de pago aprobado y recibo PDF enviados exitosamente." 
                    : "Correo de notificación de estado impago enviado exitosamente."
            ]);

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