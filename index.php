<?php
include_once 'pdf_generator.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Get the raw POST data
    $rawPostData = file_get_contents("php://input");

    // Decode the raw POST data from JSON
    $jsonData = json_decode($rawPostData, true);

    // Check and retrieve 'documento' and 'selloRecibido'
    if (isset($jsonData['documento']) && is_string($jsonData['documento'])) {
        // Decode the 'documento' field
        $documentoArray = json_decode($jsonData['documento'], true);

        // Retrieve 'selloRecibido'
        $selloRecibido = isset($jsonData['selloRecibido']) ? $jsonData['selloRecibido'] : null;

        // Now, $documentoArray is an associative array of the 'documento' JSON string
        // and $selloRecibido contains the 'selloRecibido' value

        // Example: Print the arrays and value to see their structure
        if($_GET['documento'] == "01")
            generate_factura($documentoArray, $selloRecibido);
        else if($_GET['documento'] == "03")
            generate_ccf($documentoArray, $selloRecibido);
        else if($_GET['documento'] == "05")
            generate_nc($documentoArray, $selloRecibido);
        else if($_GET['documento'] == "07")
            generate_cre($documentoArray, $selloRecibido);
        else if($_GET['documento'] == "11")
            generate_fex($documentoArray, $selloRecibido);
        else if($_GET['documento'] == "14")
            generate_fse($documentoArray, $selloRecibido);
    } else {
        // Handle the error if 'documento' is not set or not a valid JSON string
        echo "Invalid or missing 'documento' field";
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Método no permitido";
}