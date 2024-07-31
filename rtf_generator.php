<?php
include_once 'qrcode_generator.php';
include_once 'link_generator.php';

use Dompdf\Dompdf;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$s3Bucket = $_ENV['S3_BUCKET'] ?? getenv('S3_BUCKET');

function generate_ticket($documentoArray)
{
    $ambiente = $documentoArray['identificacion']['ambiente'];
    $nombreEmpresa = $documentoArray['emisor']['nombre'];
    $direccion = $documentoArray['emisor']['direccion']['complemento'];
    $codGen = $documentoArray["identificacion"]["codigoGeneracion"];
    $fechaEmi = $documentoArray["identificacion"]["fecEmi"];
    $numeroControl = $documentoArray["identificacion"]["numeroControl"];
    $tipoDte = $documentoArray["identificacion"]["tipoDte"];

    $receptorNumDocumento = $documentoArray["receptor"]["numDocumento"] ?? '';
    $receptorNombre = $documentoArray["receptor"]["nombre"] ?? '';
    $receptorCorreo = $documentoArray["receptor"]["correo"] ?? '';
    $receptorTelefono = $documentoArray["receptor"]["telefono"] ?? '';

    $totalPagar = "$" . number_format($documentoArray["resumen"]["totalPagar"], 2);

    $uri = generate_uri(generate_link($codGen, $fechaEmi, $ambiente));

    $template = file_get_contents('templates/html/plantilla_ticket.html');
    $template = str_replace('[[nombre_empresa]]', $nombreEmpresa, $template);
    $template = str_replace('[[direccion]]', $direccion, $template);
    $template = str_replace('[[codigoGeneracion]]', $codGen, $template);
    $template = str_replace('[[fecEmi]]', $fechaEmi, $template);
    $template = str_replace('[[numeroControl]]', $numeroControl, $template);

    $template = str_replace('[[receptorNumDocumento]]', $receptorNumDocumento, $template);
    $template = str_replace('[[receptorNombre]]', $receptorNombre, $template);
    $template = str_replace('[[receptorCorreo]]', $receptorCorreo, $template);
    $template = str_replace('[[receptorTelefono]]', $receptorTelefono, $template);

    $items = '';
    foreach ($documentoArray["cuerpoDocumento"] as $item) {
        $subtotal = $tipoDte == "14" ? $item['compra'] : $item['ventaGravada'];
        $codigo = $item['codigo'] ? htmlspecialchars($item['codigo']) : "";
        $descripcion = $item['descripcion'] ? htmlspecialchars($item['descripcion']) : "";

        $items .= '<tr>';
        $items .= '<td>' . $codigo . '<br>'.$descripcion.'</td>';
        $items .= '<td>' . htmlspecialchars($item['cantidad']) . '</td>';
        $items .= '<td>$' . number_format($item['precioUni'], 2) . '</td>';
        $items .= '<td>$' . number_format($subtotal, 2) . '</td>';
        $items .= '</tr>';
    }

    if (isset($documentoArray['resumen']['tributos'])) {
        foreach ($documentoArray['resumen']['tributos'] as $tributo) {
			$items .= '<tr>';
			$items .= '<td>'. htmlspecialchars($tributo['codigo']) . "<br>".htmlspecialchars($tributo['descripcion'])."</td>";
			$items .= '<td></td><td></td>';
			$items .= '<td>$'.number_format($tributo['valor'], 2).'</td>';
            $items .= '</tr>';
        }
    }

    $template = str_replace('[[detalleProductos]]', $items, $template);
    $template = str_replace('[[TotalPagar]]', $totalPagar, $template);
    // Replace [[codigoQR]] with the hexadecimal image data of the QR code
    $template = str_replace('[[codigoQR]]', $uri, $template);
    $template = str_replace('ó', "o", $template);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($template);
    // Papel de 80mm de ancho por 297mm de alto
    $dompdf->setPaper(array(0, 0, 227, 842), 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    return $output;

}