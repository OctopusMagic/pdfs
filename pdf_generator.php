<?php
include_once 'qrcode_generator.php';
include_once 'link_generator.php';
include_once 'rtf_generator.php';

use Dompdf\Dompdf;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Accessing variables
$imagen = $_ENV['LOGO'] ?? getenv('LOGO');

$tipoDte = [
    "01" => "Factura",
    "03" => "Crédito Fiscal",
    "05" => "Nota de Crédito",
    "07" => "Comprobante de Retención",
    "11" => "Factura de Exportación",
    "14" => "Factura de Sujeto Excluido",
];

$tipoDoc = [
    1 => "Físico",
    2 => "Electrónico",
];

$unidadDeMedida = [
    "09" => "Kilómetro cuadrado",
    "10" => "Hectárea",
    "11" => "Manzana",
    "12" => "Acre",
    "13" => "Metro cuadrado",
    "14" => "Yarda cuadrada",
    "15" => "Vara cuadrada",
    "16" => "Pie cuadrado",
    "17" => "Pulgada cuadrada",
    "18" => "Metro cúbico",
    "19" => "Yarda cúbica",
    "20" => "Barril",
    "21" => "Pie cúbico",
    "22" => "Galón",
    "23" => "Litro",
    "24" => "Botella",
    "25" => "Pulgada cúbica",
    "26" => "Mililitro",
    "27" => "Onza fluida",
    "29" => "Tonelada métrica",
    "30" => "Tonelada",
    "31" => "Quintal métrico",
    "32" => "Quintal",
    "33" => "Arroba",
    "34" => "Kilogramo",
    "35" => "Libra troy",
    "36" => "Libra",
    "37" => "Onza troy",
    "38" => "Onza",
    "39" => "Gramo",
    "40" => "Miligramo",
    "42" => "Megawatt",
    "43" => "Kilowatt",
    "44" => "Watt",
    "45" => "Megavoltio-amperio",
    "46" => "Kilovoltio-amperio",
    "47" => "Voltio-amperio",
    "49" => "Gigawatt-hora",
    "50" => "Megawatt-hora",
    "51" => "Kilowatt-hora",
    "52" => "Watt-hora",
    "53" => "Kilovoltio",
    "54" => "Voltio",
    "55" => "Millar",
    "56" => "Medio millar",
    "57" => "Ciento",
    "58" => "Docena",
    "59" => "Unidad",
    "99" => "Otra"
];

$condicionOperacion = [
    "1" => "Contado",
    "2" => "A Crédito",
    "3" => "Otro",
];


function upload_files($codGen, $output, $documentoArray, $ticketOutput = null)
{
    $client = new \GuzzleHttp\Client();

    try {

        $multiPartData = [
            [
                'name' => 'codGen',
                'contents' => $codGen
            ],
            [
                'name' => 'pdf',
                'contents' => $output,
                'filename' => "$codGen.pdf"
            ],
            [
                'name' => 'json', 
                'contents' => json_encode($documentoArray),
                'filename' => "$codGen.json"
            ]
        ];

        if ($ticketOutput) {
            $multiPartData[] = [
                'name' => 'ticket',
                'contents' => $ticketOutput,
                'filename' => $codGen . "_ticket.pdf"
            ];
        }

        $response = $client->post('http://dashboard.octopus.local/upload-files', [
            'multipart' => $multiPartData
        ]);

        $body = $response->getBody();
        $result = json_decode($body, true);

        echo json_encode([
            "pdfUrl" => $result['pdfPath'],
            "jsonUrl" => $result['jsonPath'],
            "rtfUrl" => $result['ticketPath'] ?? ''
        ]);
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        echo $e->getResponse()->getBody()->getContents();
    }
}


function generate_factura($documentoArray, $selloRecibido)
{
    $ambiente = $documentoArray["identificacion"]["ambiente"];
    $codGen = $documentoArray["identificacion"]["codigoGeneracion"];
    $fechaEmi = $documentoArray["identificacion"]["fecEmi"];
    $uri = generate_uri(generate_link($codGen, $fechaEmi, $ambiente));

    global $tipoDte;
    global $unidadDeMedida;
    global $condicionOperacion;
    global $imagen;

    $dte = $tipoDte[$documentoArray["identificacion"]["tipoDte"]];
    $type = pathinfo($imagen, PATHINFO_EXTENSION);
    $data = file_get_contents($imagen);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    $template = file_get_contents('templates/html/factura_electronica.html');
    $codigos = [
        "{{codGen}}",
        "{{dte}}",
        "{{logo}}",
        "{{uri}}",
        "{{numeroControl}}",
        "{{selloRecepcion}}",
        "{{fecEmi}}",
        "{{horEmi}}",
        "{{nombreEmisor}}",
        "{{nitEmisor}}",
        "{{nrcEmisor}}",
        "{{descActividadEmisor}}",
        "{{direccionEmisor}}",
        "{{telefonoEmisor}}",
        "{{correoEmisor}}",
        "{{nombreReceptor}}",
        "{{nitReceptor}}",
        "{{nrcReceptor}}",
        "{{actividadReceptor}}",
        "{{direccionReceptor}}",
        "{{telefonoReceptor}}",
        "{{correoReceptor}}",
        "{{cuerpoItems}}",
        "{{totalLetras}}",
        "{{condicionOperacion}}",
        "{{sumaTotalOperaciones}}",
        "{{descuentos}}",
        "{{subTotal}}",
        "{{tributos}}",
        "{{ivaRete1}}",
        "{{reteRenta}}",
        "{{montoTotalOperacion}}",
        "{{totalNoGravado}}",
        "{{totalPagar}}",
        "{{apendices}}"
    ];

    if ($documentoArray["receptor"]["direccion"] != null)
        $direccion = $documentoArray["receptor"]["direccion"]["complemento"];
    else
        $direccion = "";

    $items = "";

    foreach ($documentoArray["cuerpoDocumento"] as $item) {
        $items .= "<tr>
                    <td>" . $item["numItem"] . "</td>
                    <td>" . $item["codigo"] . "</td>
                    <td>" . $item["cantidad"] . "</td>
                    <td>" . $unidadDeMedida[$item["uniMedida"]] . "</td>
                    <td>" . $item["descripcion"] . "</td>
                    <td>$" . number_format($item["precioUni"], 2) . "</td>
                    <td>$" . number_format($item["montoDescu"], 2) . "</td>
                    <td>$" . number_format($item["ventaNoSuj"], 2) . "</td>
                    <td>$" . number_format($item["ventaExenta"], 2) . "</td>
                    <td>$" . number_format($item["ventaGravada"], 2) . "</td>
                </tr>";
    }

    $descuento = "";
    if ($documentoArray["resumen"]["totalDescu"] > 0) {
        $descuento .= "<tr>
            <td>Total Descuentos: </td>
            <td>$" . number_format($documentoArray["resumen"]["totalDescu"], 2) . "</td>
        </tr>";
    }

    $tributos = "";
    if ($documentoArray["resumen"]["tributos"] != null) {
        foreach ($documentoArray["resumen"]["tributos"] as $tributo) {
            $tributos .= "<tr>
                        <td>" . $tributo["descripcion"] . ": </td>
                        <td>$" . number_format($tributo["valor"], 2) . "</td>
                    </tr>";
        }
    }

    $apendices = "";
    if ($documentoArray['apendice'] != null) {
        $counter = 0;
        $totalApendices = count($documentoArray['apendice']);
        foreach ($documentoArray['apendice'] as $apendice) {
            if ($counter % 2 == 0) {
                $apendices .= "<tr>";
            }
            // Check if it's the last element and if the total is odd
            if ($counter == $totalApendices - 1 && $totalApendices % 2 != 0) {
                $apendices .= "<td colspan='2'>" . $apendice["etiqueta"] . "</td>
                <td colspan='2'>" . $apendice["valor"] . "</td>";
            } else {
                $apendices .= "<td>" . $apendice["etiqueta"] . "</td>
                <td>" . $apendice["valor"] . "</td>";
            }
            if ($counter % 2 != 0 || $counter == $totalApendices - 1) {
                $apendices .= "</tr>";
            }
            $counter++;
        }
    }


    $valores = [
        $codGen,
        $dte,
        $base64,
        $uri,
        $documentoArray["identificacion"]["numeroControl"],
        $selloRecibido,
        $documentoArray["identificacion"]["fecEmi"],
        $documentoArray["identificacion"]["horEmi"],
        $documentoArray["emisor"]["nombre"],
        $documentoArray["emisor"]["nit"],
        $documentoArray["emisor"]["nrc"],
        $documentoArray["emisor"]["descActividad"],
        $documentoArray["emisor"]["direccion"]["complemento"],
        $documentoArray["emisor"]["telefono"],
        $documentoArray["emisor"]["correo"],
        $documentoArray["receptor"]["nombre"],
        $documentoArray["receptor"]["numDocumento"],
        $documentoArray["receptor"]["nrc"],
        $documentoArray["receptor"]["descActividad"],
        $direccion,
        $documentoArray["receptor"]["telefono"],
        $documentoArray["receptor"]["correo"],
        $items,
        $documentoArray["resumen"]["totalLetras"],
        $condicionOperacion[$documentoArray["resumen"]["condicionOperacion"]],
        "$" . number_format($documentoArray["resumen"]["subTotalVentas"], 2),
        $descuento,
        "$" . number_format($documentoArray["resumen"]["subTotal"], 2),
        $tributos,
        "$" . number_format($documentoArray["resumen"]["ivaRete1"], 2),
        "$" . number_format($documentoArray["resumen"]["reteRenta"], 2),
        "$" . number_format($documentoArray["resumen"]["montoTotalOperacion"], 2),
        "$" . number_format($documentoArray["resumen"]["totalNoGravado"], 2),
        "$" . number_format($documentoArray["resumen"]["totalPagar"], 2),
        $apendices
    ];

    $template = str_replace($codigos, $valores, $template);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($template);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    // Generate the ticket
    $ticketOutput = generate_ticket($documentoArray);

    upload_files($codGen, $output, $documentoArray, $ticketOutput);
}


function generate_ccf($documentoArray, $selloRecibido)
{
    $ambiente = $documentoArray["identificacion"]["ambiente"];
    $codGen = $documentoArray["identificacion"]["codigoGeneracion"];
    $fechaEmi = $documentoArray["identificacion"]["fecEmi"];
    $uri = generate_uri(generate_link($codGen, $fechaEmi, $ambiente));

    global $tipoDte;
    global $unidadDeMedida;
    global $condicionOperacion;
    global $imagen;

    $dte = $tipoDte[$documentoArray["identificacion"]["tipoDte"]];

    $type = pathinfo($imagen, PATHINFO_EXTENSION);
    $data = file_get_contents($imagen);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    $template = file_get_contents('templates/html/credito_fiscal.html');
    $codigos = [
        "{{codGen}}",
        "{{dte}}",
        "{{logo}}",
        "{{uri}}",
        "{{numeroControl}}",
        "{{selloRecepcion}}",
        "{{fecEmi}}",
        "{{horEmi}}",
        "{{nombreEmisor}}",
        "{{nitEmisor}}",
        "{{nrcEmisor}}",
        "{{descActividadEmisor}}",
        "{{direccionEmisor}}",
        "{{telefonoEmisor}}",
        "{{correoEmisor}}",
        "{{nombreReceptor}}",
        "{{nitReceptor}}",
        "{{nrcReceptor}}",
        "{{actividadReceptor}}",
        "{{direccionReceptor}}",
        "{{telefonoReceptor}}",
        "{{correoReceptor}}",
        "{{cuerpoItems}}",
        "{{totalLetras}}",
        "{{condicionOperacion}}",
        "{{sumaTotalOperaciones}}",
        "{{descuentos}}",
        "{{subTotal}}",
        "{{tributos}}",
        "{{ivaRete1}}",
        "{{reteRenta}}",
        "{{montoTotalOperacion}}",
        "{{totalNoGravado}}",
        "{{totalPagar}}",
        "{{apendices}}"
    ];

    if ($documentoArray["receptor"]["direccion"] != null)
        $direccion = $documentoArray["receptor"]["direccion"]["complemento"];
    else
        $direccion = "";

    $items = "";

    foreach ($documentoArray["cuerpoDocumento"] as $item) {
        $items .= "<tr>
                    <td>" . $item["numItem"] . "</td>
                    <td>" . $item["codigo"] . "</td>
                    <td>" . $item["cantidad"] . "</td>
                    <td>" . $unidadDeMedida[$item["uniMedida"]] . "</td>
                    <td>" . $item["descripcion"] . "</td>
                    <td>$" . number_format($item["precioUni"], 2) . "</td>
                    <td>$" . number_format($item["montoDescu"], 2) . "</td>
                    <td>$" . number_format($item["ventaNoSuj"], 2) . "</td>
                    <td>$" . number_format($item["ventaExenta"], 2) . "</td>
                    <td>$" . number_format($item["ventaGravada"], 2) . "</td>
                </tr>";
    }

    $descuento = "";
    if ($documentoArray["resumen"]["totalDescu"] > 0) {
        $descuento .= "<tr>
            <td>Total Descuentos: </td>
            <td>$" . number_format($documentoArray["resumen"]["totalDescu"], 2) . "</td>
        </tr>";
    }

    $tributos = "";
    if ($documentoArray["resumen"]["tributos"] != null) {
        foreach ($documentoArray["resumen"]["tributos"] as $tributo) {
            $tributos .= "<tr>
                        <td>" . $tributo["descripcion"] . ": </td>
                        <td>$" . number_format($tributo["valor"], 2) . "</td>
                    </tr>";
        }
    }

    $apendices = "";
    if ($documentoArray['apendice'] != null) {
        $counter = 0;
        $totalApendices = count($documentoArray['apendice']);
        foreach ($documentoArray['apendice'] as $apendice) {
            if ($counter % 2 == 0) {
                $apendices .= "<tr>";
            }
            // Check if it's the last element and if the total is odd
            if ($counter == $totalApendices - 1 && $totalApendices % 2 != 0) {
                $apendices .= "<td colspan='2'>" . $apendice["etiqueta"] . "</td>
                <td colspan='2'>" . $apendice["valor"] . "</td>";
            } else {
                $apendices .= "<td>" . $apendice["etiqueta"] . "</td>
                <td>" . $apendice["valor"] . "</td>";
            }
            if ($counter % 2 != 0 || $counter == $totalApendices - 1) {
                $apendices .= "</tr>";
            }
            $counter++;
        }
    }

    $valores = [
        $codGen,
        $dte,
        $base64,
        $uri,
        $documentoArray["identificacion"]["numeroControl"],
        $selloRecibido,
        $documentoArray["identificacion"]["fecEmi"],
        $documentoArray["identificacion"]["horEmi"],
        $documentoArray["emisor"]["nombre"],
        $documentoArray["emisor"]["nit"],
        $documentoArray["emisor"]["nrc"],
        $documentoArray["emisor"]["descActividad"],
        $documentoArray["emisor"]["direccion"]["complemento"],
        $documentoArray["emisor"]["telefono"],
        $documentoArray["emisor"]["correo"],
        $documentoArray["receptor"]["nombre"],
        $documentoArray["receptor"]["nit"],
        $documentoArray["receptor"]["nrc"],
        $documentoArray["receptor"]["descActividad"],
        $direccion,
        $documentoArray["receptor"]["telefono"],
        $documentoArray["receptor"]["correo"],
        $items,
        $documentoArray["resumen"]["totalLetras"],
        $condicionOperacion[$documentoArray["resumen"]["condicionOperacion"]],
        "$" . number_format($documentoArray["resumen"]["subTotalVentas"], 2),
        $descuento,
        "$" . number_format($documentoArray["resumen"]["subTotal"], 2),
        $tributos,
        "$" . number_format($documentoArray["resumen"]["ivaRete1"], 2),
        "$" . number_format($documentoArray["resumen"]["reteRenta"], 2),
        "$" . number_format($documentoArray["resumen"]["montoTotalOperacion"], 2),
        "$" . number_format($documentoArray["resumen"]["totalNoGravado"], 2),
        "$" . number_format($documentoArray["resumen"]["totalPagar"], 2),
        $apendices
    ];

    $template = str_replace($codigos, $valores, $template);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($template);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    // Generate the ticket
    $ticketOutput = generate_ticket($documentoArray);

    upload_files($codGen, $output, $documentoArray, $ticketOutput);
}


function generate_fse($documentoArray, $selloRecibido)
{
    $ambiente = $documentoArray["identificacion"]["ambiente"];
    $codGen = $documentoArray["identificacion"]["codigoGeneracion"];
    $fechaEmi = $documentoArray["identificacion"]["fecEmi"];
    $uri = generate_uri(generate_link($codGen, $fechaEmi, $ambiente));

    global $tipoDte;
    global $unidadDeMedida;
    global $condicionOperacion;
    global $imagen;

    $dte = $tipoDte[$documentoArray["identificacion"]["tipoDte"]];

    $type = pathinfo($imagen, PATHINFO_EXTENSION);
    $data = file_get_contents($imagen);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    $template = file_get_contents('templates/html/sujeto_excluido.html');
    $codigos = [
        "{{codGen}}",
        "{{dte}}",
        "{{logo}}",
        "{{uri}}",
        "{{numeroControl}}",
        "{{selloRecepcion}}",
        "{{fecEmi}}",
        "{{horEmi}}",
        "{{nombreEmisor}}",
        "{{nitEmisor}}",
        "{{nrcEmisor}}",
        "{{descActividadEmisor}}",
        "{{direccionEmisor}}",
        "{{telefonoEmisor}}",
        "{{correoEmisor}}",
        "{{nombreReceptor}}",
        "{{nitReceptor}}",
        "{{actividadReceptor}}",
        "{{direccionReceptor}}",
        "{{telefonoReceptor}}",
        "{{correoReceptor}}",
        "{{cuerpoItems}}",
        "{{totalLetras}}",
        "{{condicionOperacion}}",
        "{{sumaTotalOperaciones}}",
        "{{subTotal}}",
        "{{ivaRete1}}",
        "{{reteRenta}}",
        "{{totalPagar}}",
    ];

    if ($documentoArray["sujetoExcluido"]["direccion"] != null)
        $direccion = $documentoArray["sujetoExcluido"]["direccion"]["complemento"];
    else
        $direccion = "";

    $items = "";

    foreach ($documentoArray["cuerpoDocumento"] as $item) {
        $items .= "<tr>
                    <td>" . $item["numItem"] . "</td>
                    <td>" . $item["codigo"] . "</td>
                    <td>" . $item["cantidad"] . "</td>
                    <td>" . $unidadDeMedida[$item["uniMedida"]] . "</td>
                    <td>" . $item["descripcion"] . "</td>
                    <td>$" . number_format($item["precioUni"], 2) . "</td>
                    <td>$" . number_format($item["montoDescu"], 2) . "</td>
                    <td>$" . number_format($item["compra"], 2) . "</td>
                </tr>";
    }

    $valores = [
        $codGen,
        $dte,
        $base64,
        $uri,
        $documentoArray["identificacion"]["numeroControl"],
        $selloRecibido,
        $documentoArray["identificacion"]["fecEmi"],
        $documentoArray["identificacion"]["horEmi"],
        $documentoArray["emisor"]["nombre"],
        $documentoArray["emisor"]["nit"],
        $documentoArray["emisor"]["nrc"],
        $documentoArray["emisor"]["descActividad"],
        $documentoArray["emisor"]["direccion"]["complemento"],
        $documentoArray["emisor"]["telefono"],
        $documentoArray["emisor"]["correo"],
        $documentoArray["sujetoExcluido"]["nombre"],
        $documentoArray["sujetoExcluido"]["numDocumento"],
        $documentoArray["sujetoExcluido"]["descActividad"],
        $direccion,
        $documentoArray["sujetoExcluido"]["telefono"],
        $documentoArray["sujetoExcluido"]["correo"],
        $items,
        $documentoArray["resumen"]["totalLetras"],
        $condicionOperacion[$documentoArray["resumen"]["condicionOperacion"]],
        "$" . number_format($documentoArray["resumen"]["totalCompra"], 2),
        "$" . number_format($documentoArray["resumen"]["subTotal"], 2),
        "$" . number_format($documentoArray["resumen"]["ivaRete1"], 2),
        "$" . number_format($documentoArray["resumen"]["reteRenta"], 2),
        "$" . number_format($documentoArray["resumen"]["totalPagar"], 2)
    ];

    $template = str_replace($codigos, $valores, $template);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($template);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    // Generate the ticket
    $ticketOutput = generate_ticket($documentoArray);
    upload_files($codGen, $output, $documentoArray, $ticketOutput);
}


function generate_cre($documentoArray, $selloRecibido)
{
    $ambiente = $documentoArray["identificacion"]["ambiente"];
    $codGen = $documentoArray["identificacion"]["codigoGeneracion"];
    $fechaEmi = $documentoArray["identificacion"]["fecEmi"];
    $uri = generate_uri(generate_link($codGen, $fechaEmi, $ambiente));

    global $tipoDte;
    global $tipoDoc;
    global $unidadDeMedida;
    global $condicionOperacion;
    global $imagen;

    $dte = $tipoDte[$documentoArray["identificacion"]["tipoDte"]];
    $type = pathinfo($imagen, PATHINFO_EXTENSION);
    $data = file_get_contents($imagen);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    $template = file_get_contents('templates/html/comprobante_retencion.html');
    $codigos = [
        "{{codGen}}",
        "{{dte}}",
        "{{logo}}",
        "{{uri}}",
        "{{numeroControl}}",
        "{{selloRecepcion}}",
        "{{fecEmi}}",
        "{{horEmi}}",
        "{{nombreEmisor}}",
        "{{nitEmisor}}",
        "{{nrcEmisor}}",
        "{{descActividadEmisor}}",
        "{{direccionEmisor}}",
        "{{telefonoEmisor}}",
        "{{correoEmisor}}",
        "{{nombreReceptor}}",
        "{{nitReceptor}}",
        "{{nrcReceptor}}",
        "{{actividadReceptor}}",
        "{{direccionReceptor}}",
        "{{telefonoReceptor}}",
        "{{correoReceptor}}",
        "{{cuerpoItems}}",
        "{{totalIVAretenidoLetras}}",
        "{{totalSujetoRetencion}}",
        "{{totalIVAretenido}}",
    ];

    if ($documentoArray["receptor"]["direccion"] != null)
        $direccion = $documentoArray["receptor"]["direccion"]["complemento"];
    else
        $direccion = "";

    $items = "";

    foreach ($documentoArray["cuerpoDocumento"] as $item) {
        $items .= "<tr>
            <td>" . $item["numItem"] . "</td>
            <td>" . $tipoDte[$item["tipoDte"]] . "</td>
            <td>" . $item["numDocumento"] . "</td>
            <td>" . $item["fechaEmision"] . "</td>
            <td>" . $item["descripcion"] . "</td>
            <td>$" . number_format($item["montoSujetoGrav"], 2) . "</td>
            <td>$" . number_format($item["ivaRetenido"], 2) . "</td> 
        </tr>";
    }

    $valores = [
        $codGen,
        $dte,
        $base64,
        $uri,
        $documentoArray["identificacion"]["numeroControl"],
        $selloRecibido,
        $documentoArray["identificacion"]["fecEmi"],
        $documentoArray["identificacion"]["horEmi"],
        $documentoArray["emisor"]["nombre"],
        $documentoArray["emisor"]["nit"],
        $documentoArray["emisor"]["nrc"],
        $documentoArray["emisor"]["descActividad"],
        $documentoArray["emisor"]["direccion"]["complemento"],
        $documentoArray["emisor"]["telefono"],
        $documentoArray["emisor"]["correo"],
        $documentoArray["receptor"]["nombre"],
        $documentoArray["receptor"]["numDocumento"],
        $documentoArray["receptor"]["nrc"],
        $documentoArray["receptor"]["descActividad"],
        $direccion,
        $documentoArray["receptor"]["telefono"],
        $documentoArray["receptor"]["correo"],
        $items,
        $documentoArray["resumen"]["totalIVAretenidoLetras"],
        "$" . number_format($documentoArray["resumen"]["totalSujetoRetencion"], 2),
        "$" . number_format($documentoArray["resumen"]["totalIVAretenido"], 2),
    ];

    $template = str_replace($codigos, $valores, $template);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($template);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    upload_files($codGen, $output, $documentoArray);
}


function generate_nc($documentoArray, $selloRecibido)
{
    $ambiente = $documentoArray["identificacion"]["ambiente"];
    $codGen = $documentoArray["identificacion"]["codigoGeneracion"];
    $fechaEmi = $documentoArray["identificacion"]["fecEmi"];
    $uri = generate_uri(generate_link($codGen, $fechaEmi, $ambiente));

    global $tipoDte;
    global $tipoDoc;
    global $unidadDeMedida;
    global $condicionOperacion;
    global $imagen;

    $dte = $tipoDte[$documentoArray["identificacion"]["tipoDte"]];
    $type = pathinfo($imagen, PATHINFO_EXTENSION);
    $data = file_get_contents($imagen);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    $template = file_get_contents('templates/html/nota_credito.html');
    $codigos = [
        "{{codGen}}",
        "{{dte}}",
        "{{logo}}",
        "{{uri}}",
        "{{numeroControl}}",
        "{{selloRecepcion}}",
        "{{fecEmi}}",
        "{{horEmi}}",
        "{{nombreEmisor}}",
        "{{nitEmisor}}",
        "{{nrcEmisor}}",
        "{{descActividadEmisor}}",
        "{{direccionEmisor}}",
        "{{telefonoEmisor}}",
        "{{correoEmisor}}",
        "{{nombreReceptor}}",
        "{{nitReceptor}}",
        "{{nrcReceptor}}",
        "{{actividadReceptor}}",
        "{{direccionReceptor}}",
        "{{telefonoReceptor}}",
        "{{correoReceptor}}",
        "{{cuerpoItems}}",
        "{{totalLetras}}",
        "{{condicionOperacion}}",
        "{{sumaTotalOperaciones}}",
        "{{descuentos}}",
        "{{subTotal}}",
        "{{tributos}}",
        "{{ivaRete1}}",
        "{{reteRenta}}",
        "{{montoTotalOperacion}}",
        "{{documentosRelacionados}}",
    ];

    if ($documentoArray["receptor"]["direccion"] != null)
        $direccion = $documentoArray["receptor"]["direccion"]["complemento"];
    else
        $direccion = "";

    $items = "";

    foreach ($documentoArray["cuerpoDocumento"] as $item) {
        $items .= "<tr>
                    <td>" . $item["numItem"] . "</td>
                    <td>" . $item["codigo"] . "</td>
                    <td>" . $item["cantidad"] . "</td>
                    <td>" . $unidadDeMedida[$item["uniMedida"]] . "</td>
                    <td>" . $item["descripcion"] . "</td>
                    <td>$" . number_format($item["precioUni"], 2) . "</td>
                    <td>$" . number_format($item["montoDescu"], 2) . "</td>
                    <td>$" . number_format($item["ventaNoSuj"], 2) . "</td>
                    <td>$" . number_format($item["ventaExenta"], 2) . "</td>
                    <td>$" . number_format($item["ventaGravada"], 2) . "</td>
                </tr>";
    }

    $documentosRelacionados = "";

    foreach ($documentoArray["documentoRelacionado"] as $documento) {
        $documentosRelacionados .= "<tr>
            <td>" . $tipoDte[$documento["tipoDocumento"]] . "</td>
            <td>" . $documento["numeroDocumento"] . "</td>
            <td>" . $documento["fechaEmision"] . "</td>
        </tr>";
    }

    $descuento = "";
    if ($documentoArray["resumen"]["totalDescu"] > 0) {
        $descuento .= "<tr>
            <td>Total Descuentos: </td>
            <td>$" . number_format($documentoArray["resumen"]["totalDescu"], 2) . "</td>
        </tr>";
    }

    $tributos = "";
    if ($documentoArray["resumen"]["tributos"] != null) {
        foreach ($documentoArray["resumen"]["tributos"] as $tributo) {
            $tributos .= "<tr>
                        <td>" . $tributo["descripcion"] . ": </td>
                        <td>$" . number_format($tributo["valor"], 2) . "</td>
                    </tr>";
        }
    }

    $valores = [
        $codGen,
        $dte,
        $base64,
        $uri,
        $documentoArray["identificacion"]["numeroControl"],
        $selloRecibido,
        $documentoArray["identificacion"]["fecEmi"],
        $documentoArray["identificacion"]["horEmi"],
        $documentoArray["emisor"]["nombre"],
        $documentoArray["emisor"]["nit"],
        $documentoArray["emisor"]["nrc"],
        $documentoArray["emisor"]["descActividad"],
        $documentoArray["emisor"]["direccion"]["complemento"],
        $documentoArray["emisor"]["telefono"],
        $documentoArray["emisor"]["correo"],
        $documentoArray["receptor"]["nombre"],
        $documentoArray["receptor"]["nit"],
        $documentoArray["receptor"]["nrc"],
        $documentoArray["receptor"]["descActividad"],
        $direccion,
        $documentoArray["receptor"]["telefono"],
        $documentoArray["receptor"]["correo"],
        $items,
        $documentoArray["resumen"]["totalLetras"],
        $condicionOperacion[$documentoArray["resumen"]["condicionOperacion"]],
        "$" . number_format($documentoArray["resumen"]["subTotalVentas"], 2),
        $descuento,
        "$" . number_format($documentoArray["resumen"]["subTotal"], 2),
        $tributos,
        "$" . number_format($documentoArray["resumen"]["ivaRete1"], 2),
        "$" . number_format($documentoArray["resumen"]["reteRenta"], 2),
        "$" . number_format($documentoArray["resumen"]["montoTotalOperacion"], 2),
        $documentosRelacionados
    ];

    $template = str_replace($codigos, $valores, $template);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($template);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    upload_files($codGen, $output, $documentoArray);
}


function generate_fex($documentoArray, $selloRecibido)
{
    $ambiente = $documentoArray["identificacion"]["ambiente"];
    $codGen = $documentoArray["identificacion"]["codigoGeneracion"];
    $fechaEmi = $documentoArray["identificacion"]["fecEmi"];
    $uri = generate_uri(generate_link($codGen, $fechaEmi, $ambiente));

    global $tipoDte;
    global $tipoDoc;
    global $unidadDeMedida;
    global $condicionOperacion;
    global $imagen;

    $dte = $tipoDte[$documentoArray["identificacion"]["tipoDte"]];

    $type = pathinfo($imagen, PATHINFO_EXTENSION);
    $data = file_get_contents($imagen);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    $template = file_get_contents('templates/html/factura_exportacion.html');
    $codigos = [
        "{{codGen}}",
        "{{dte}}",
        "{{logo}}",
        "{{uri}}",
        "{{codGen}}",
        "{{numeroControl}}",
        "{{selloRecepcion}}",
        "{{fecEmi}}",
        "{{horEmi}}",
        "{{nombreEmisor}}",
        "{{nitEmisor}}",
        "{{nrcEmisor}}",
        "{{descActividadEmisor}}",
        "{{direccionEmisor}}",
        "{{telefonoEmisor}}",
        "{{correoEmisor}}",
        "{{recintoFiscal}}",
        "{{regimen}}",
        "{{nombreReceptor}}",
        "{{documentoReceptor}}",
        "{{actividadReceptor}}",
        "{{paisReceptor}}",
        "{{direccionReceptor}}",
        "{{cuerpoItems}}",
        "{{totalLetras}}",
        "{{descIncoterms}}",
        "{{condicionOperacion}}",
        "{{apendice}}",
        "{{sumaTotalOperaciones}}",
        "{{descuentos}}",
        "{{seguro}}",
        "{{flete}}",
        "{{montoTotalOperacion}}",
        "{{TotalNoGravado}}",
        "{{TotalGeneral}}",
    ];

    $items = "";

    foreach ($documentoArray['cuerpoDocumento'] as $item) {
        $items .= "<tr>
            <td>" . $item["numItem"] . "</td>
            <td>" . $item["codigo"] . "</td>
            <td>" . $item["cantidad"] . "</td>
            <td>" . $unidadDeMedida[$item["uniMedida"]] . "</td>
            <td>" . $item["descripcion"] . "</td>
            <td>$" . number_format($item["precioUni"], 2) . "</td>
            <td>$" . number_format($item["montoDescu"], 2) . "</td>
            <td>$" . number_format($item["noGravado"], 2) . "</td>
            <td>$" . number_format($item["ventaGravada"], 2) . "</td>
        </tr>";
    }

    $descuento = "";
    if ($documentoArray["resumen"]["totalDescu"] > 0) {
        $descuento .= "<tr>
            <td>Total Descuentos: </td>
            <td>$" . number_format($documentoArray["resumen"]["totalDescu"], 2) . "</td>
        </tr>";
    }

    $apendices = "";
    foreach ($documentoArray['apendice'] as $apendice) {
        $apendices .= "<tr>
            <td>" . $apendice["etiqueta"] . "</td>
            <td>" . $apendice["valor"] . "</td>
        </tr>";
    }

    $valores = [
        $codGen,
        $dte,
        $base64,
        $uri,
        $codGen,
        $documentoArray["identificacion"]["numeroControl"],
        $selloRecibido,
        $documentoArray["identificacion"]["fecEmi"],
        $documentoArray["identificacion"]["horEmi"],
        $documentoArray["emisor"]["nombre"],
        $documentoArray["emisor"]["nit"],
        $documentoArray["emisor"]["nrc"],
        $documentoArray["emisor"]["descActividad"],
        $documentoArray["emisor"]["direccion"]["complemento"],
        $documentoArray["emisor"]["telefono"],
        $documentoArray["emisor"]["correo"],
        $documentoArray["emisor"]["recintoFiscal"],
        $documentoArray["emisor"]["regimen"],
        $documentoArray["receptor"]["nombre"],
        $documentoArray["receptor"]["numDocumento"],
        $documentoArray["receptor"]["descActividad"],
        $documentoArray["receptor"]["nombrePais"],
        $documentoArray["receptor"]["complemento"],
        $items,
        $documentoArray["resumen"]["totalLetras"],
        $documentoArray["resumen"]["descIncoterms"],
        $condicionOperacion[$documentoArray["resumen"]["condicionOperacion"]],
        $apendices,
        "$" . number_format($documentoArray["resumen"]["totalGravada"], 2),
        $descuento,
        "$" . number_format($documentoArray["resumen"]["seguro"], 2),
        "$" . number_format($documentoArray["resumen"]["flete"], 2),
        "$" . number_format($documentoArray["resumen"]["montoTotalOperacion"], 2),
        "$" . number_format($documentoArray["resumen"]["totalNoGravado"], 2),
        "$" . number_format($documentoArray["resumen"]["totalPagar"], 2)
    ];

    $template = str_replace($codigos, $valores, $template);

    $dompdf = new Dompdf();
    $dompdf->loadHtml($template);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    upload_files($codGen, $output, $documentoArray);
}