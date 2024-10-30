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

$distritos = [
    [
        "id" => 1,
        "codigo" => "00",
        "nombre" => "Otro (Para Extranjeros)",
        "created_at" => "2024-10-22T22:39:55.000000Z",
        "updated_at" => "2024-10-22T22:39:55.000000Z",
        "municipios" => [
            [
                "id" => 1,
                "codigo" => "00",
                "nombre" => "Otro (Para extranjeros)",
                "departamento_id" => 1,
                "created_at" => "2024-10-22T22:39:58.000000Z",
                "updated_at" => "2024-10-22T22:39:58.000000Z",
                "distritos" => [
                    [
                        "id" => 1,
                        "nombre" => "Otro (Para extranjeros)",
                        "municipio_id" => 1,
                        "created_at" => "2024-10-22T22:40:06.000000Z",
                        "updated_at" => "2024-10-22T22:40:06.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 2,
        "codigo" => "01",
        "nombre" => "Ahuachapán",
        "created_at" => "2024-10-22T22:39:56.000000Z",
        "updated_at" => "2024-10-22T22:39:56.000000Z",
        "municipios" => [
            [
                "id" => 2,
                "codigo" => "13",
                "nombre" => "AHUACHAPAN NORTE",
                "departamento_id" => 2,
                "created_at" => "2024-10-22T22:39:58.000000Z",
                "updated_at" => "2024-10-22T22:39:58.000000Z",
                "distritos" => [
                    [
                        "id" => 2,
                        "nombre" => "Atiquizaya",
                        "municipio_id" => 2,
                        "created_at" => "2024-10-22T22:40:06.000000Z",
                        "updated_at" => "2024-10-22T22:40:06.000000Z",
                    ],
                    [
                        "id" => 3,
                        "nombre" => "El Refugio",
                        "municipio_id" => 2,
                        "created_at" => "2024-10-22T22:40:06.000000Z",
                        "updated_at" => "2024-10-22T22:40:06.000000Z",
                    ],
                    [
                        "id" => 4,
                        "nombre" => "San Lorenzo",
                        "municipio_id" => 2,
                        "created_at" => "2024-10-22T22:40:06.000000Z",
                        "updated_at" => "2024-10-22T22:40:06.000000Z",
                    ],
                    [
                        "id" => 5,
                        "nombre" => "Turín",
                        "municipio_id" => 2,
                        "created_at" => "2024-10-22T22:40:06.000000Z",
                        "updated_at" => "2024-10-22T22:40:06.000000Z",
                    ],
                ],
            ],
            [
                "id" => 3,
                "codigo" => "14",
                "nombre" => "AHUACHAPAN CENTRO",
                "departamento_id" => 2,
                "created_at" => "2024-10-22T22:39:59.000000Z",
                "updated_at" => "2024-10-22T22:39:59.000000Z",
                "distritos" => [
                    [
                        "id" => 6,
                        "nombre" => "Ahuachapán",
                        "municipio_id" => 3,
                        "created_at" => "2024-10-22T22:40:06.000000Z",
                        "updated_at" => "2024-10-22T22:40:06.000000Z",
                    ],
                    [
                        "id" => 7,
                        "nombre" => "Apaneca",
                        "municipio_id" => 3,
                        "created_at" => "2024-10-22T22:40:07.000000Z",
                        "updated_at" => "2024-10-22T22:40:07.000000Z",
                    ],
                    [
                        "id" => 8,
                        "nombre" => "Concepción de Ataco",
                        "municipio_id" => 3,
                        "created_at" => "2024-10-22T22:40:07.000000Z",
                        "updated_at" => "2024-10-22T22:40:07.000000Z",
                    ],
                    [
                        "id" => 9,
                        "nombre" => "Tacuba",
                        "municipio_id" => 3,
                        "created_at" => "2024-10-22T22:40:07.000000Z",
                        "updated_at" => "2024-10-22T22:40:07.000000Z",
                    ],
                ],
            ],
            [
                "id" => 4,
                "codigo" => "15",
                "nombre" => "AHUACHAPAN SUR",
                "departamento_id" => 2,
                "created_at" => "2024-10-22T22:39:59.000000Z",
                "updated_at" => "2024-10-22T22:39:59.000000Z",
                "distritos" => [
                    [
                        "id" => 10,
                        "nombre" => "Guaymango",
                        "municipio_id" => 4,
                        "created_at" => "2024-10-22T22:40:07.000000Z",
                        "updated_at" => "2024-10-22T22:40:07.000000Z",
                    ],
                    [
                        "id" => 11,
                        "nombre" => "Jujutla",
                        "municipio_id" => 4,
                        "created_at" => "2024-10-22T22:40:07.000000Z",
                        "updated_at" => "2024-10-22T22:40:07.000000Z",
                    ],
                    [
                        "id" => 12,
                        "nombre" => "San Francisco Menéndez",
                        "municipio_id" => 4,
                        "created_at" => "2024-10-22T22:40:07.000000Z",
                        "updated_at" => "2024-10-22T22:40:07.000000Z",
                    ],
                    [
                        "id" => 13,
                        "nombre" => "San Pedro Puxtla",
                        "municipio_id" => 4,
                        "created_at" => "2024-10-22T22:40:08.000000Z",
                        "updated_at" => "2024-10-22T22:40:08.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 3,
        "codigo" => "02",
        "nombre" => "Santa Ana",
        "created_at" => "2024-10-22T22:39:56.000000Z",
        "updated_at" => "2024-10-22T22:39:56.000000Z",
        "municipios" => [
            [
                "id" => 5,
                "codigo" => "14",
                "nombre" => "SANTA ANA NORTE",
                "departamento_id" => 3,
                "created_at" => "2024-10-22T22:39:59.000000Z",
                "updated_at" => "2024-10-22T22:39:59.000000Z",
                "distritos" => [
                    [
                        "id" => 14,
                        "nombre" => "Masahuat",
                        "municipio_id" => 5,
                        "created_at" => "2024-10-22T22:40:08.000000Z",
                        "updated_at" => "2024-10-22T22:40:08.000000Z",
                    ],
                    [
                        "id" => 15,
                        "nombre" => "Metapán",
                        "municipio_id" => 5,
                        "created_at" => "2024-10-22T22:40:08.000000Z",
                        "updated_at" => "2024-10-22T22:40:08.000000Z",
                    ],
                    [
                        "id" => 16,
                        "nombre" => "Santa Rosa Guachipilín",
                        "municipio_id" => 5,
                        "created_at" => "2024-10-22T22:40:08.000000Z",
                        "updated_at" => "2024-10-22T22:40:08.000000Z",
                    ],
                    [
                        "id" => 17,
                        "nombre" => "Texistepeque",
                        "municipio_id" => 5,
                        "created_at" => "2024-10-22T22:40:08.000000Z",
                        "updated_at" => "2024-10-22T22:40:08.000000Z",
                    ],
                ],
            ],
            [
                "id" => 6,
                "codigo" => "15",
                "nombre" => "SANTA ANA CENTRO",
                "departamento_id" => 3,
                "created_at" => "2024-10-22T22:39:59.000000Z",
                "updated_at" => "2024-10-22T22:39:59.000000Z",
                "distritos" => [
                    [
                        "id" => 18,
                        "nombre" => "Santa Ana",
                        "municipio_id" => 6,
                        "created_at" => "2024-10-22T22:40:08.000000Z",
                        "updated_at" => "2024-10-22T22:40:08.000000Z",
                    ],
                ],
            ],
            [
                "id" => 7,
                "codigo" => "16",
                "nombre" => "SANTA ANA ESTE",
                "departamento_id" => 3,
                "created_at" => "2024-10-22T22:39:59.000000Z",
                "updated_at" => "2024-10-22T22:39:59.000000Z",
                "distritos" => [
                    [
                        "id" => 19,
                        "nombre" => "Coatepeque",
                        "municipio_id" => 7,
                        "created_at" => "2024-10-22T22:40:08.000000Z",
                        "updated_at" => "2024-10-22T22:40:08.000000Z",
                    ],
                    [
                        "id" => 20,
                        "nombre" => "El Congo",
                        "municipio_id" => 7,
                        "created_at" => "2024-10-22T22:40:09.000000Z",
                        "updated_at" => "2024-10-22T22:40:09.000000Z",
                    ],
                ],
            ],
            [
                "id" => 8,
                "codigo" => "17",
                "nombre" => "SANTA ANA OESTE",
                "departamento_id" => 3,
                "created_at" => "2024-10-22T22:39:59.000000Z",
                "updated_at" => "2024-10-22T22:39:59.000000Z",
                "distritos" => [
                    [
                        "id" => 21,
                        "nombre" => "Candelaria de la Frontera",
                        "municipio_id" => 8,
                        "created_at" => "2024-10-22T22:40:09.000000Z",
                        "updated_at" => "2024-10-22T22:40:09.000000Z",
                    ],
                    [
                        "id" => 22,
                        "nombre" => "Chalchuapa",
                        "municipio_id" => 8,
                        "created_at" => "2024-10-22T22:40:09.000000Z",
                        "updated_at" => "2024-10-22T22:40:09.000000Z",
                    ],
                    [
                        "id" => 23,
                        "nombre" => "El Porvenir",
                        "municipio_id" => 8,
                        "created_at" => "2024-10-22T22:40:09.000000Z",
                        "updated_at" => "2024-10-22T22:40:09.000000Z",
                    ],
                    [
                        "id" => 24,
                        "nombre" => "San Antonio Pajonal",
                        "municipio_id" => 8,
                        "created_at" => "2024-10-22T22:40:09.000000Z",
                        "updated_at" => "2024-10-22T22:40:09.000000Z",
                    ],
                    [
                        "id" => 25,
                        "nombre" => "San Sebastián Salitrillo",
                        "municipio_id" => 8,
                        "created_at" => "2024-10-22T22:40:09.000000Z",
                        "updated_at" => "2024-10-22T22:40:09.000000Z",
                    ],
                    [
                        "id" => 26,
                        "nombre" => "Santiago de La Frontera",
                        "municipio_id" => 8,
                        "created_at" => "2024-10-22T22:40:10.000000Z",
                        "updated_at" => "2024-10-22T22:40:10.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 4,
        "codigo" => "03",
        "nombre" => "Sonsonate",
        "created_at" => "2024-10-22T22:39:56.000000Z",
        "updated_at" => "2024-10-22T22:39:56.000000Z",
        "municipios" => [
            [
                "id" => 9,
                "codigo" => "17",
                "nombre" => "SONSONATE NORTE",
                "departamento_id" => 4,
                "created_at" => "2024-10-22T22:40:00.000000Z",
                "updated_at" => "2024-10-22T22:40:00.000000Z",
                "distritos" => [
                    [
                        "id" => 27,
                        "nombre" => "Juayúa",
                        "municipio_id" => 9,
                        "created_at" => "2024-10-22T22:40:10.000000Z",
                        "updated_at" => "2024-10-22T22:40:10.000000Z",
                    ],
                    [
                        "id" => 28,
                        "nombre" => "Nahuizalco",
                        "municipio_id" => 9,
                        "created_at" => "2024-10-22T22:40:10.000000Z",
                        "updated_at" => "2024-10-22T22:40:10.000000Z",
                    ],
                    [
                        "id" => 29,
                        "nombre" => "Salcoatitán",
                        "municipio_id" => 9,
                        "created_at" => "2024-10-22T22:40:10.000000Z",
                        "updated_at" => "2024-10-22T22:40:10.000000Z",
                    ],
                    [
                        "id" => 30,
                        "nombre" => "Santa Catarina Masahuat",
                        "municipio_id" => 9,
                        "created_at" => "2024-10-22T22:40:10.000000Z",
                        "updated_at" => "2024-10-22T22:40:10.000000Z",
                    ],
                ],
            ],
            [
                "id" => 10,
                "codigo" => "18",
                "nombre" => "SONSONATE CENTRO",
                "departamento_id" => 4,
                "created_at" => "2024-10-22T22:40:00.000000Z",
                "updated_at" => "2024-10-22T22:40:00.000000Z",
                "distritos" => [
                    [
                        "id" => 31,
                        "nombre" => "Sonsonate",
                        "municipio_id" => 10,
                        "created_at" => "2024-10-22T22:40:10.000000Z",
                        "updated_at" => "2024-10-22T22:40:10.000000Z",
                    ],
                    [
                        "id" => 32,
                        "nombre" => "Sonzacate",
                        "municipio_id" => 10,
                        "created_at" => "2024-10-22T22:40:11.000000Z",
                        "updated_at" => "2024-10-22T22:40:11.000000Z",
                    ],
                    [
                        "id" => 33,
                        "nombre" => "Nahulingo",
                        "municipio_id" => 10,
                        "created_at" => "2024-10-22T22:40:11.000000Z",
                        "updated_at" => "2024-10-22T22:40:11.000000Z",
                    ],
                    [
                        "id" => 34,
                        "nombre" => "San Antonio del Monte",
                        "municipio_id" => 10,
                        "created_at" => "2024-10-22T22:40:11.000000Z",
                        "updated_at" => "2024-10-22T22:40:11.000000Z",
                    ],
                    [
                        "id" => 35,
                        "nombre" => "Santo Domingo de Guzmán",
                        "municipio_id" => 10,
                        "created_at" => "2024-10-22T22:40:11.000000Z",
                        "updated_at" => "2024-10-22T22:40:11.000000Z",
                    ],
                ],
            ],
            [
                "id" => 11,
                "codigo" => "19",
                "nombre" => "SONSONATE ESTE",
                "departamento_id" => 4,
                "created_at" => "2024-10-22T22:40:00.000000Z",
                "updated_at" => "2024-10-22T22:40:00.000000Z",
                "distritos" => [
                    [
                        "id" => 36,
                        "nombre" => "Izalco",
                        "municipio_id" => 11,
                        "created_at" => "2024-10-22T22:40:11.000000Z",
                        "updated_at" => "2024-10-22T22:40:11.000000Z",
                    ],
                    [
                        "id" => 37,
                        "nombre" => "Armenia",
                        "municipio_id" => 11,
                        "created_at" => "2024-10-22T22:40:11.000000Z",
                        "updated_at" => "2024-10-22T22:40:11.000000Z",
                    ],
                    [
                        "id" => 38,
                        "nombre" => "Caluco",
                        "municipio_id" => 11,
                        "created_at" => "2024-10-22T22:40:12.000000Z",
                        "updated_at" => "2024-10-22T22:40:12.000000Z",
                    ],
                    [
                        "id" => 39,
                        "nombre" => "San Julián",
                        "municipio_id" => 11,
                        "created_at" => "2024-10-22T22:40:12.000000Z",
                        "updated_at" => "2024-10-22T22:40:12.000000Z",
                    ],
                    [
                        "id" => 40,
                        "nombre" => "Cuisnahuat",
                        "municipio_id" => 11,
                        "created_at" => "2024-10-22T22:40:12.000000Z",
                        "updated_at" => "2024-10-22T22:40:12.000000Z",
                    ],
                    [
                        "id" => 41,
                        "nombre" => "Santa Isabel Ishuatán",
                        "municipio_id" => 11,
                        "created_at" => "2024-10-22T22:40:12.000000Z",
                        "updated_at" => "2024-10-22T22:40:12.000000Z",
                    ],
                ],
            ],
            [
                "id" => 12,
                "codigo" => "20",
                "nombre" => "SONSONATE OESTE",
                "departamento_id" => 4,
                "created_at" => "2024-10-22T22:40:00.000000Z",
                "updated_at" => "2024-10-22T22:40:00.000000Z",
                "distritos" => [
                    [
                        "id" => 42,
                        "nombre" => "Acajutla",
                        "municipio_id" => 12,
                        "created_at" => "2024-10-22T22:40:12.000000Z",
                        "updated_at" => "2024-10-22T22:40:12.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 5,
        "codigo" => "04",
        "nombre" => "Chalatenango",
        "created_at" => "2024-10-22T22:39:56.000000Z",
        "updated_at" => "2024-10-22T22:39:56.000000Z",
        "municipios" => [
            [
                "id" => 13,
                "codigo" => "34",
                "nombre" => "CHALATENANGO NORTE",
                "departamento_id" => 5,
                "created_at" => "2024-10-22T22:40:00.000000Z",
                "updated_at" => "2024-10-22T22:40:00.000000Z",
                "distritos" => [
                    [
                        "id" => 43,
                        "nombre" => "La Palma",
                        "municipio_id" => 13,
                        "created_at" => "2024-10-22T22:40:12.000000Z",
                        "updated_at" => "2024-10-22T22:40:12.000000Z",
                    ],
                    [
                        "id" => 44,
                        "nombre" => "San Ignacio",
                        "municipio_id" => 13,
                        "created_at" => "2024-10-22T22:40:13.000000Z",
                        "updated_at" => "2024-10-22T22:40:13.000000Z",
                    ],
                    [
                        "id" => 45,
                        "nombre" => "Citalá",
                        "municipio_id" => 13,
                        "created_at" => "2024-10-22T22:40:13.000000Z",
                        "updated_at" => "2024-10-22T22:40:13.000000Z",
                    ],
                ],
            ],
            [
                "id" => 14,
                "codigo" => "35",
                "nombre" => "CHALATENANGO CENTRO",
                "departamento_id" => 5,
                "created_at" => "2024-10-22T22:40:00.000000Z",
                "updated_at" => "2024-10-22T22:40:00.000000Z",
                "distritos" => [
                    [
                        "id" => 46,
                        "nombre" => "Nueva Concepción",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:13.000000Z",
                        "updated_at" => "2024-10-22T22:40:13.000000Z",
                    ],
                    [
                        "id" => 47,
                        "nombre" => "Tejutla",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:13.000000Z",
                        "updated_at" => "2024-10-22T22:40:13.000000Z",
                    ],
                    [
                        "id" => 48,
                        "nombre" => "La Reina",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:13.000000Z",
                        "updated_at" => "2024-10-22T22:40:13.000000Z",
                    ],
                    [
                        "id" => 49,
                        "nombre" => "Agua Caliente",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:13.000000Z",
                        "updated_at" => "2024-10-22T22:40:13.000000Z",
                    ],
                    [
                        "id" => 50,
                        "nombre" => "Dulce Nombre de María",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:14.000000Z",
                        "updated_at" => "2024-10-22T22:40:14.000000Z",
                    ],
                    [
                        "id" => 51,
                        "nombre" => "El Paraíso",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:14.000000Z",
                        "updated_at" => "2024-10-22T22:40:14.000000Z",
                    ],
                    [
                        "id" => 52,
                        "nombre" => "San Fernando",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:14.000000Z",
                        "updated_at" => "2024-10-22T22:40:14.000000Z",
                    ],
                    [
                        "id" => 53,
                        "nombre" => "San Francisco Morazán",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:14.000000Z",
                        "updated_at" => "2024-10-22T22:40:14.000000Z",
                    ],
                    [
                        "id" => 54,
                        "nombre" => "San Rafael",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:14.000000Z",
                        "updated_at" => "2024-10-22T22:40:14.000000Z",
                    ],
                    [
                        "id" => 55,
                        "nombre" => "Santa Rita",
                        "municipio_id" => 14,
                        "created_at" => "2024-10-22T22:40:14.000000Z",
                        "updated_at" => "2024-10-22T22:40:14.000000Z",
                    ],
                ],
            ],
            [
                "id" => 15,
                "codigo" => "36",
                "nombre" => "CHALATENANGO SUR",
                "departamento_id" => 5,
                "created_at" => "2024-10-22T22:40:01.000000Z",
                "updated_at" => "2024-10-22T22:40:01.000000Z",
                "distritos" => [
                    [
                        "id" => 56,
                        "nombre" => "Chalatenango",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:15.000000Z",
                        "updated_at" => "2024-10-22T22:40:15.000000Z",
                    ],
                    [
                        "id" => 57,
                        "nombre" => "Arcatao",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:15.000000Z",
                        "updated_at" => "2024-10-22T22:40:15.000000Z",
                    ],
                    [
                        "id" => 58,
                        "nombre" => "Azacualpa",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:15.000000Z",
                        "updated_at" => "2024-10-22T22:40:15.000000Z",
                    ],
                    [
                        "id" => 59,
                        "nombre" => "Comalapa",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:15.000000Z",
                        "updated_at" => "2024-10-22T22:40:15.000000Z",
                    ],
                    [
                        "id" => 60,
                        "nombre" => "Concepción Quezaltepeque",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:15.000000Z",
                        "updated_at" => "2024-10-22T22:40:15.000000Z",
                    ],
                    [
                        "id" => 61,
                        "nombre" => "El Carrizal",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:15.000000Z",
                        "updated_at" => "2024-10-22T22:40:15.000000Z",
                    ],
                    [
                        "id" => 62,
                        "nombre" => "La Laguna",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:16.000000Z",
                        "updated_at" => "2024-10-22T22:40:16.000000Z",
                    ],
                    [
                        "id" => 63,
                        "nombre" => "Nombre de Jesús",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:16.000000Z",
                        "updated_at" => "2024-10-22T22:40:16.000000Z",
                    ],
                    [
                        "id" => 64,
                        "nombre" => "Nueva Trinidad",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:16.000000Z",
                        "updated_at" => "2024-10-22T22:40:16.000000Z",
                    ],
                    [
                        "id" => 65,
                        "nombre" => "Ojos de Agua",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:16.000000Z",
                        "updated_at" => "2024-10-22T22:40:16.000000Z",
                    ],
                    [
                        "id" => 66,
                        "nombre" => "Potonico",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:16.000000Z",
                        "updated_at" => "2024-10-22T22:40:16.000000Z",
                    ],
                    [
                        "id" => 67,
                        "nombre" => "San Antonio de La Cruz",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:16.000000Z",
                        "updated_at" => "2024-10-22T22:40:16.000000Z",
                    ],
                    [
                        "id" => 68,
                        "nombre" => "San Antonio Los Ranchos",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:17.000000Z",
                        "updated_at" => "2024-10-22T22:40:17.000000Z",
                    ],
                    [
                        "id" => 69,
                        "nombre" => "San Francisco Lempa",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:17.000000Z",
                        "updated_at" => "2024-10-22T22:40:17.000000Z",
                    ],
                    [
                        "id" => 70,
                        "nombre" => "San Isidro Labrador",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:17.000000Z",
                        "updated_at" => "2024-10-22T22:40:17.000000Z",
                    ],
                    [
                        "id" => 71,
                        "nombre" => "San José Cancasque",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:17.000000Z",
                        "updated_at" => "2024-10-22T22:40:17.000000Z",
                    ],
                    [
                        "id" => 72,
                        "nombre" => "San Luis del Carmen",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:17.000000Z",
                        "updated_at" => "2024-10-22T22:40:17.000000Z",
                    ],
                    [
                        "id" => 73,
                        "nombre" => "San Miguel de Mercedes",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:17.000000Z",
                        "updated_at" => "2024-10-22T22:40:17.000000Z",
                    ],
                    [
                        "id" => 74,
                        "nombre" => "San Luis Los Flores",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:17.000000Z",
                        "updated_at" => "2024-10-22T22:40:17.000000Z",
                    ],
                    [
                        "id" => 75,
                        "nombre" => "Las Vueltas",
                        "municipio_id" => 15,
                        "created_at" => "2024-10-22T22:40:18.000000Z",
                        "updated_at" => "2024-10-22T22:40:18.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 6,
        "codigo" => "05",
        "nombre" => "La Libertad",
        "created_at" => "2024-10-22T22:39:57.000000Z",
        "updated_at" => "2024-10-22T22:39:57.000000Z",
        "municipios" => [
            [
                "id" => 16,
                "codigo" => "23",
                "nombre" => "LA LIBERTAD NORTE",
                "departamento_id" => 6,
                "created_at" => "2024-10-22T22:40:01.000000Z",
                "updated_at" => "2024-10-22T22:40:01.000000Z",
                "distritos" => [
                    [
                        "id" => 76,
                        "nombre" => "Quezaltepeque",
                        "municipio_id" => 16,
                        "created_at" => "2024-10-22T22:40:18.000000Z",
                        "updated_at" => "2024-10-22T22:40:18.000000Z",
                    ],
                    [
                        "id" => 77,
                        "nombre" => "San Matías",
                        "municipio_id" => 16,
                        "created_at" => "2024-10-22T22:40:18.000000Z",
                        "updated_at" => "2024-10-22T22:40:18.000000Z",
                    ],
                    [
                        "id" => 78,
                        "nombre" => "San Pablo Tacachico",
                        "municipio_id" => 16,
                        "created_at" => "2024-10-22T22:40:18.000000Z",
                        "updated_at" => "2024-10-22T22:40:18.000000Z",
                    ],
                ],
            ],
            [
                "id" => 17,
                "codigo" => "24",
                "nombre" => "LA LIBERTAD CENTRO",
                "departamento_id" => 6,
                "created_at" => "2024-10-22T22:40:01.000000Z",
                "updated_at" => "2024-10-22T22:40:01.000000Z",
                "distritos" => [
                    [
                        "id" => 79,
                        "nombre" => "San Juan Opico",
                        "municipio_id" => 17,
                        "created_at" => "2024-10-22T22:40:18.000000Z",
                        "updated_at" => "2024-10-22T22:40:18.000000Z",
                    ],
                    [
                        "id" => 80,
                        "nombre" => "Ciudad Arce",
                        "municipio_id" => 17,
                        "created_at" => "2024-10-22T22:40:18.000000Z",
                        "updated_at" => "2024-10-22T22:40:18.000000Z",
                    ],
                ],
            ],
            [
                "id" => 18,
                "codigo" => "25",
                "nombre" => "LA LIBERTAD OESTE",
                "departamento_id" => 6,
                "created_at" => "2024-10-22T22:40:01.000000Z",
                "updated_at" => "2024-10-22T22:40:01.000000Z",
                "distritos" => [
                    [
                        "id" => 81,
                        "nombre" => "Colón",
                        "municipio_id" => 18,
                        "created_at" => "2024-10-22T22:40:19.000000Z",
                        "updated_at" => "2024-10-22T22:40:19.000000Z",
                    ],
                    [
                        "id" => 82,
                        "nombre" => "Jayaque",
                        "municipio_id" => 18,
                        "created_at" => "2024-10-22T22:40:19.000000Z",
                        "updated_at" => "2024-10-22T22:40:19.000000Z",
                    ],
                    [
                        "id" => 83,
                        "nombre" => "Sacacoyo",
                        "municipio_id" => 18,
                        "created_at" => "2024-10-22T22:40:19.000000Z",
                        "updated_at" => "2024-10-22T22:40:19.000000Z",
                    ],
                    [
                        "id" => 84,
                        "nombre" => "Tepecoyo",
                        "municipio_id" => 18,
                        "created_at" => "2024-10-22T22:40:19.000000Z",
                        "updated_at" => "2024-10-22T22:40:19.000000Z",
                    ],
                    [
                        "id" => 85,
                        "nombre" => "Talnique",
                        "municipio_id" => 18,
                        "created_at" => "2024-10-22T22:40:19.000000Z",
                        "updated_at" => "2024-10-22T22:40:19.000000Z",
                    ],
                ],
            ],
            [
                "id" => 19,
                "codigo" => "26",
                "nombre" => "LA LIBERTAD ESTE",
                "departamento_id" => 6,
                "created_at" => "2024-10-22T22:40:01.000000Z",
                "updated_at" => "2024-10-22T22:40:01.000000Z",
                "distritos" => [
                    [
                        "id" => 86,
                        "nombre" => "Antiguo Cuscatlán",
                        "municipio_id" => 19,
                        "created_at" => "2024-10-22T22:40:19.000000Z",
                        "updated_at" => "2024-10-22T22:40:19.000000Z",
                    ],
                    [
                        "id" => 87,
                        "nombre" => "Huizúcar",
                        "municipio_id" => 19,
                        "created_at" => "2024-10-22T22:40:20.000000Z",
                        "updated_at" => "2024-10-22T22:40:20.000000Z",
                    ],
                    [
                        "id" => 88,
                        "nombre" => "Nuevo Cuscatlán",
                        "municipio_id" => 19,
                        "created_at" => "2024-10-22T22:40:20.000000Z",
                        "updated_at" => "2024-10-22T22:40:20.000000Z",
                    ],
                    [
                        "id" => 89,
                        "nombre" => "San José Villanueva",
                        "municipio_id" => 19,
                        "created_at" => "2024-10-22T22:40:20.000000Z",
                        "updated_at" => "2024-10-22T22:40:20.000000Z",
                    ],
                    [
                        "id" => 90,
                        "nombre" => "Zaragoza",
                        "municipio_id" => 19,
                        "created_at" => "2024-10-22T22:40:20.000000Z",
                        "updated_at" => "2024-10-22T22:40:20.000000Z",
                    ],
                ],
            ],
            [
                "id" => 20,
                "codigo" => "27",
                "nombre" => "LA LIBERTAD COSTA",
                "departamento_id" => 6,
                "created_at" => "2024-10-22T22:40:01.000000Z",
                "updated_at" => "2024-10-22T22:40:01.000000Z",
                "distritos" => [
                    [
                        "id" => 91,
                        "nombre" => "Chiltiupán",
                        "municipio_id" => 20,
                        "created_at" => "2024-10-22T22:40:20.000000Z",
                        "updated_at" => "2024-10-22T22:40:20.000000Z",
                    ],
                    [
                        "id" => 92,
                        "nombre" => "Jicalapa",
                        "municipio_id" => 20,
                        "created_at" => "2024-10-22T22:40:20.000000Z",
                        "updated_at" => "2024-10-22T22:40:20.000000Z",
                    ],
                    [
                        "id" => 93,
                        "nombre" => "La Libertad",
                        "municipio_id" => 20,
                        "created_at" => "2024-10-22T22:40:21.000000Z",
                        "updated_at" => "2024-10-22T22:40:21.000000Z",
                    ],
                    [
                        "id" => 94,
                        "nombre" => "Tamanique",
                        "municipio_id" => 20,
                        "created_at" => "2024-10-22T22:40:21.000000Z",
                        "updated_at" => "2024-10-22T22:40:21.000000Z",
                    ],
                    [
                        "id" => 95,
                        "nombre" => "Teotepeque",
                        "municipio_id" => 20,
                        "created_at" => "2024-10-22T22:40:21.000000Z",
                        "updated_at" => "2024-10-22T22:40:21.000000Z",
                    ],
                ],
            ],
            [
                "id" => 21,
                "codigo" => "28",
                "nombre" => "LA LIBERTAD SUR",
                "departamento_id" => 6,
                "created_at" => "2024-10-22T22:40:02.000000Z",
                "updated_at" => "2024-10-22T22:40:02.000000Z",
                "distritos" => [
                    [
                        "id" => 96,
                        "nombre" => "Comasagua",
                        "municipio_id" => 21,
                        "created_at" => "2024-10-22T22:40:21.000000Z",
                        "updated_at" => "2024-10-22T22:40:21.000000Z",
                    ],
                    [
                        "id" => 97,
                        "nombre" => "Santa Tecla",
                        "municipio_id" => 21,
                        "created_at" => "2024-10-22T22:40:21.000000Z",
                        "updated_at" => "2024-10-22T22:40:21.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 7,
        "codigo" => "06",
        "nombre" => "San Salvador",
        "created_at" => "2024-10-22T22:39:57.000000Z",
        "updated_at" => "2024-10-22T22:39:57.000000Z",
        "municipios" => [
            [
                "id" => 22,
                "codigo" => "20",
                "nombre" => "SAN SALVADOR NORTE",
                "departamento_id" => 7,
                "created_at" => "2024-10-22T22:40:02.000000Z",
                "updated_at" => "2024-10-22T22:40:02.000000Z",
                "distritos" => [
                    [
                        "id" => 98,
                        "nombre" => "Aguilares",
                        "municipio_id" => 22,
                        "created_at" => "2024-10-22T22:40:21.000000Z",
                        "updated_at" => "2024-10-22T22:40:21.000000Z",
                    ],
                    [
                        "id" => 99,
                        "nombre" => "El Paisnal",
                        "municipio_id" => 22,
                        "created_at" => "2024-10-22T22:40:22.000000Z",
                        "updated_at" => "2024-10-22T22:40:22.000000Z",
                    ],
                    [
                        "id" => 100,
                        "nombre" => "Guazapa",
                        "municipio_id" => 22,
                        "created_at" => "2024-10-22T22:40:22.000000Z",
                        "updated_at" => "2024-10-22T22:40:22.000000Z",
                    ],
                ],
            ],
            [
                "id" => 23,
                "codigo" => "21",
                "nombre" => "SAN SALVADOR OESTE",
                "departamento_id" => 7,
                "created_at" => "2024-10-22T22:40:02.000000Z",
                "updated_at" => "2024-10-22T22:40:02.000000Z",
                "distritos" => [
                    [
                        "id" => 101,
                        "nombre" => "Apopa",
                        "municipio_id" => 23,
                        "created_at" => "2024-10-22T22:40:22.000000Z",
                        "updated_at" => "2024-10-22T22:40:22.000000Z",
                    ],
                    [
                        "id" => 102,
                        "nombre" => "Nejapa",
                        "municipio_id" => 23,
                        "created_at" => "2024-10-22T22:40:22.000000Z",
                        "updated_at" => "2024-10-22T22:40:22.000000Z",
                    ],
                ],
            ],
            [
                "id" => 24,
                "codigo" => "22",
                "nombre" => "SAN SALVADOR ESTE",
                "departamento_id" => 7,
                "created_at" => "2024-10-22T22:40:02.000000Z",
                "updated_at" => "2024-10-22T22:40:02.000000Z",
                "distritos" => [
                    [
                        "id" => 103,
                        "nombre" => "Ilopango",
                        "municipio_id" => 24,
                        "created_at" => "2024-10-22T22:40:22.000000Z",
                        "updated_at" => "2024-10-22T22:40:22.000000Z",
                    ],
                    [
                        "id" => 104,
                        "nombre" => "San Martín",
                        "municipio_id" => 24,
                        "created_at" => "2024-10-22T22:40:22.000000Z",
                        "updated_at" => "2024-10-22T22:40:22.000000Z",
                    ],
                    [
                        "id" => 105,
                        "nombre" => "Soyapango",
                        "municipio_id" => 24,
                        "created_at" => "2024-10-22T22:40:23.000000Z",
                        "updated_at" => "2024-10-22T22:40:23.000000Z",
                    ],
                    [
                        "id" => 106,
                        "nombre" => "Tonacatepeque",
                        "municipio_id" => 24,
                        "created_at" => "2024-10-22T22:40:23.000000Z",
                        "updated_at" => "2024-10-22T22:40:23.000000Z",
                    ],
                ],
            ],
            [
                "id" => 25,
                "codigo" => "23",
                "nombre" => "SAN SALVADOR CENTRO",
                "departamento_id" => 7,
                "created_at" => "2024-10-22T22:40:02.000000Z",
                "updated_at" => "2024-10-22T22:40:02.000000Z",
                "distritos" => [
                    [
                        "id" => 107,
                        "nombre" => "Ayutuxtepeque",
                        "municipio_id" => 25,
                        "created_at" => "2024-10-22T22:40:23.000000Z",
                        "updated_at" => "2024-10-22T22:40:23.000000Z",
                    ],
                    [
                        "id" => 108,
                        "nombre" => "Mejicanos",
                        "municipio_id" => 25,
                        "created_at" => "2024-10-22T22:40:23.000000Z",
                        "updated_at" => "2024-10-22T22:40:23.000000Z",
                    ],
                    [
                        "id" => 109,
                        "nombre" => "San Salvador",
                        "municipio_id" => 25,
                        "created_at" => "2024-10-22T22:40:23.000000Z",
                        "updated_at" => "2024-10-22T22:40:23.000000Z",
                    ],
                    [
                        "id" => 110,
                        "nombre" => "Cuscatancingo",
                        "municipio_id" => 25,
                        "created_at" => "2024-10-22T22:40:23.000000Z",
                        "updated_at" => "2024-10-22T22:40:23.000000Z",
                    ],
                    [
                        "id" => 111,
                        "nombre" => "Ciudad Delgado",
                        "municipio_id" => 25,
                        "created_at" => "2024-10-22T22:40:24.000000Z",
                        "updated_at" => "2024-10-22T22:40:24.000000Z",
                    ],
                ],
            ],
            [
                "id" => 26,
                "codigo" => "24",
                "nombre" => "SAN SALVADOR SUR",
                "departamento_id" => 7,
                "created_at" => "2024-10-22T22:40:02.000000Z",
                "updated_at" => "2024-10-22T22:40:02.000000Z",
                "distritos" => [
                    [
                        "id" => 112,
                        "nombre" => "Panchimalco",
                        "municipio_id" => 26,
                        "created_at" => "2024-10-22T22:40:24.000000Z",
                        "updated_at" => "2024-10-22T22:40:24.000000Z",
                    ],
                    [
                        "id" => 113,
                        "nombre" => "Rosario de Mora",
                        "municipio_id" => 26,
                        "created_at" => "2024-10-22T22:40:24.000000Z",
                        "updated_at" => "2024-10-22T22:40:24.000000Z",
                    ],
                    [
                        "id" => 114,
                        "nombre" => "San Marcos",
                        "municipio_id" => 26,
                        "created_at" => "2024-10-22T22:40:24.000000Z",
                        "updated_at" => "2024-10-22T22:40:24.000000Z",
                    ],
                    [
                        "id" => 115,
                        "nombre" => "Santo Tomás",
                        "municipio_id" => 26,
                        "created_at" => "2024-10-22T22:40:24.000000Z",
                        "updated_at" => "2024-10-22T22:40:24.000000Z",
                    ],
                    [
                        "id" => 116,
                        "nombre" => "Santiago Texacuangos",
                        "municipio_id" => 26,
                        "created_at" => "2024-10-22T22:40:24.000000Z",
                        "updated_at" => "2024-10-22T22:40:24.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 8,
        "codigo" => "07",
        "nombre" => "Cuscatlán",
        "created_at" => "2024-10-22T22:39:57.000000Z",
        "updated_at" => "2024-10-22T22:39:57.000000Z",
        "municipios" => [
            [
                "id" => 27,
                "codigo" => "17",
                "nombre" => "CUSCATLAN NORTE",
                "departamento_id" => 8,
                "created_at" => "2024-10-22T22:40:02.000000Z",
                "updated_at" => "2024-10-22T22:40:02.000000Z",
                "distritos" => [
                    [
                        "id" => 117,
                        "nombre" => "Suchitoto",
                        "municipio_id" => 27,
                        "created_at" => "2024-10-22T22:40:25.000000Z",
                        "updated_at" => "2024-10-22T22:40:25.000000Z",
                    ],
                    [
                        "id" => 118,
                        "nombre" => "San José Guayabal",
                        "municipio_id" => 27,
                        "created_at" => "2024-10-22T22:40:25.000000Z",
                        "updated_at" => "2024-10-22T22:40:25.000000Z",
                    ],
                    [
                        "id" => 119,
                        "nombre" => "Oratorio de Concepción",
                        "municipio_id" => 27,
                        "created_at" => "2024-10-22T22:40:25.000000Z",
                        "updated_at" => "2024-10-22T22:40:25.000000Z",
                    ],
                    [
                        "id" => 120,
                        "nombre" => "San Bartolomé Perulapía",
                        "municipio_id" => 27,
                        "created_at" => "2024-10-22T22:40:25.000000Z",
                        "updated_at" => "2024-10-22T22:40:25.000000Z",
                    ],
                    [
                        "id" => 121,
                        "nombre" => "San Pedro Perulapán",
                        "municipio_id" => 27,
                        "created_at" => "2024-10-22T22:40:25.000000Z",
                        "updated_at" => "2024-10-22T22:40:25.000000Z",
                    ],
                ],
            ],
            [
                "id" => 28,
                "codigo" => "18",
                "nombre" => "CUSCATLAN SUR",
                "departamento_id" => 8,
                "created_at" => "2024-10-22T22:40:03.000000Z",
                "updated_at" => "2024-10-22T22:40:03.000000Z",
                "distritos" => [
                    [
                        "id" => 122,
                        "nombre" => "Cojutepeque",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:25.000000Z",
                        "updated_at" => "2024-10-22T22:40:25.000000Z",
                    ],
                    [
                        "id" => 123,
                        "nombre" => "San Rafael Cedros",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:26.000000Z",
                        "updated_at" => "2024-10-22T22:40:26.000000Z",
                    ],
                    [
                        "id" => 124,
                        "nombre" => "Candelaria",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:26.000000Z",
                        "updated_at" => "2024-10-22T22:40:26.000000Z",
                    ],
                    [
                        "id" => 125,
                        "nombre" => "Monte San Juan",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:26.000000Z",
                        "updated_at" => "2024-10-22T22:40:26.000000Z",
                    ],
                    [
                        "id" => 126,
                        "nombre" => "El Carmen",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:26.000000Z",
                        "updated_at" => "2024-10-22T22:40:26.000000Z",
                    ],
                    [
                        "id" => 127,
                        "nombre" => "San Cristóbal",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:26.000000Z",
                        "updated_at" => "2024-10-22T22:40:26.000000Z",
                    ],
                    [
                        "id" => 128,
                        "nombre" => "Santa Cruz Michapa",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:26.000000Z",
                        "updated_at" => "2024-10-22T22:40:26.000000Z",
                    ],
                    [
                        "id" => 129,
                        "nombre" => "San Ramón",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:26.000000Z",
                        "updated_at" => "2024-10-22T22:40:26.000000Z",
                    ],
                    [
                        "id" => 130,
                        "nombre" => "El Rosario",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:27.000000Z",
                        "updated_at" => "2024-10-22T22:40:27.000000Z",
                    ],
                    [
                        "id" => 131,
                        "nombre" => "Santa Cruz Analquito",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:27.000000Z",
                        "updated_at" => "2024-10-22T22:40:27.000000Z",
                    ],
                    [
                        "id" => 132,
                        "nombre" => "Tenancingo",
                        "municipio_id" => 28,
                        "created_at" => "2024-10-22T22:40:27.000000Z",
                        "updated_at" => "2024-10-22T22:40:27.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 9,
        "codigo" => "08",
        "nombre" => "La Paz",
        "created_at" => "2024-10-22T22:39:57.000000Z",
        "updated_at" => "2024-10-22T22:39:57.000000Z",
        "municipios" => [
            [
                "id" => 29,
                "codigo" => "23",
                "nombre" => "LA PAZ OESTE",
                "departamento_id" => 9,
                "created_at" => "2024-10-22T22:40:03.000000Z",
                "updated_at" => "2024-10-22T22:40:03.000000Z",
                "distritos" => [
                    [
                        "id" => 133,
                        "nombre" => "Cuyultitán",
                        "municipio_id" => 29,
                        "created_at" => "2024-10-22T22:40:27.000000Z",
                        "updated_at" => "2024-10-22T22:40:27.000000Z",
                    ],
                    [
                        "id" => 134,
                        "nombre" => "Olocuilta",
                        "municipio_id" => 29,
                        "created_at" => "2024-10-22T22:40:27.000000Z",
                        "updated_at" => "2024-10-22T22:40:27.000000Z",
                    ],
                    [
                        "id" => 135,
                        "nombre" => "San Juan Talpa",
                        "municipio_id" => 29,
                        "created_at" => "2024-10-22T22:40:27.000000Z",
                        "updated_at" => "2024-10-22T22:40:27.000000Z",
                    ],
                    [
                        "id" => 136,
                        "nombre" => "San Luis Talpa",
                        "municipio_id" => 29,
                        "created_at" => "2024-10-22T22:40:28.000000Z",
                        "updated_at" => "2024-10-22T22:40:28.000000Z",
                    ],
                    [
                        "id" => 137,
                        "nombre" => "San Pedro Masahuat",
                        "municipio_id" => 29,
                        "created_at" => "2024-10-22T22:40:28.000000Z",
                        "updated_at" => "2024-10-22T22:40:28.000000Z",
                    ],
                    [
                        "id" => 138,
                        "nombre" => "Tapalhuaca",
                        "municipio_id" => 29,
                        "created_at" => "2024-10-22T22:40:28.000000Z",
                        "updated_at" => "2024-10-22T22:40:28.000000Z",
                    ],
                    [
                        "id" => 139,
                        "nombre" => "San Francisco Chinameca",
                        "municipio_id" => 29,
                        "created_at" => "2024-10-22T22:40:28.000000Z",
                        "updated_at" => "2024-10-22T22:40:28.000000Z",
                    ],
                ],
            ],
            [
                "id" => 30,
                "codigo" => "24",
                "nombre" => "LA PAZ CENTRO",
                "departamento_id" => 9,
                "created_at" => "2024-10-22T22:40:03.000000Z",
                "updated_at" => "2024-10-22T22:40:03.000000Z",
                "distritos" => [
                    [
                        "id" => 140,
                        "nombre" => "El Rosario",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:28.000000Z",
                        "updated_at" => "2024-10-22T22:40:28.000000Z",
                    ],
                    [
                        "id" => 141,
                        "nombre" => "Jerusalén",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:28.000000Z",
                        "updated_at" => "2024-10-22T22:40:28.000000Z",
                    ],
                    [
                        "id" => 142,
                        "nombre" => "Mercedes La Ceiba",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:29.000000Z",
                        "updated_at" => "2024-10-22T22:40:29.000000Z",
                    ],
                    [
                        "id" => 143,
                        "nombre" => "Paraíso de Osorio",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:29.000000Z",
                        "updated_at" => "2024-10-22T22:40:29.000000Z",
                    ],
                    [
                        "id" => 144,
                        "nombre" => "San Antonio Masahuat",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:29.000000Z",
                        "updated_at" => "2024-10-22T22:40:29.000000Z",
                    ],
                    [
                        "id" => 145,
                        "nombre" => "San Emigdio",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:29.000000Z",
                        "updated_at" => "2024-10-22T22:40:29.000000Z",
                    ],
                    [
                        "id" => 146,
                        "nombre" => "San Juan Tepezontes",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:29.000000Z",
                        "updated_at" => "2024-10-22T22:40:29.000000Z",
                    ],
                    [
                        "id" => 147,
                        "nombre" => "San Luis La Herradura",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:29.000000Z",
                        "updated_at" => "2024-10-22T22:40:29.000000Z",
                    ],
                    [
                        "id" => 148,
                        "nombre" => "San Miguel Tepezontes",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:30.000000Z",
                        "updated_at" => "2024-10-22T22:40:30.000000Z",
                    ],
                    [
                        "id" => 149,
                        "nombre" => "San Pedro Nonualco",
                        "municipio_id" => 30,
                        "created_at" => "2024-10-22T22:40:30.000000Z",
                        "updated_at" => "2024-10-22T22:40:30.000000Z",
                    ],
                ],
            ],
            [
                "id" => 31,
                "codigo" => "25",
                "nombre" => "LA PAZ ESTE",
                "departamento_id" => 9,
                "created_at" => "2024-10-22T22:40:03.000000Z",
                "updated_at" => "2024-10-22T22:40:03.000000Z",
                "distritos" => [
                    [
                        "id" => 150,
                        "nombre" => "Santa María Ostuma",
                        "municipio_id" => 31,
                        "created_at" => "2024-10-22T22:40:30.000000Z",
                        "updated_at" => "2024-10-22T22:40:30.000000Z",
                    ],
                    [
                        "id" => 151,
                        "nombre" => "Santiago Nonualco",
                        "municipio_id" => 31,
                        "created_at" => "2024-10-22T22:40:30.000000Z",
                        "updated_at" => "2024-10-22T22:40:30.000000Z",
                    ],
                    [
                        "id" => 152,
                        "nombre" => "San Juan Nonualco",
                        "municipio_id" => 31,
                        "created_at" => "2024-10-22T22:40:30.000000Z",
                        "updated_at" => "2024-10-22T22:40:30.000000Z",
                    ],
                    [
                        "id" => 153,
                        "nombre" => "San Rafael Obrajuelo",
                        "municipio_id" => 31,
                        "created_at" => "2024-10-22T22:40:30.000000Z",
                        "updated_at" => "2024-10-22T22:40:30.000000Z",
                    ],
                    [
                        "id" => 154,
                        "nombre" => "Zacatecoluca",
                        "municipio_id" => 31,
                        "created_at" => "2024-10-22T22:40:31.000000Z",
                        "updated_at" => "2024-10-22T22:40:31.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 10,
        "codigo" => "09",
        "nombre" => "Cabañas",
        "created_at" => "2024-10-22T22:39:57.000000Z",
        "updated_at" => "2024-10-22T22:39:57.000000Z",
        "municipios" => [
            [
                "id" => 32,
                "codigo" => "10",
                "nombre" => "CABAÑAS OESTE",
                "departamento_id" => 10,
                "created_at" => "2024-10-22T22:40:03.000000Z",
                "updated_at" => "2024-10-22T22:40:03.000000Z",
                "distritos" => [
                    [
                        "id" => 155,
                        "nombre" => "Ilobasco",
                        "municipio_id" => 32,
                        "created_at" => "2024-10-22T22:40:31.000000Z",
                        "updated_at" => "2024-10-22T22:40:31.000000Z",
                    ],
                    [
                        "id" => 156,
                        "nombre" => "Tejutepeque",
                        "municipio_id" => 32,
                        "created_at" => "2024-10-22T22:40:31.000000Z",
                        "updated_at" => "2024-10-22T22:40:31.000000Z",
                    ],
                    [
                        "id" => 157,
                        "nombre" => "Jutiapa",
                        "municipio_id" => 32,
                        "created_at" => "2024-10-22T22:40:31.000000Z",
                        "updated_at" => "2024-10-22T22:40:31.000000Z",
                    ],
                    [
                        "id" => 158,
                        "nombre" => "Cinquera",
                        "municipio_id" => 32,
                        "created_at" => "2024-10-22T22:40:31.000000Z",
                        "updated_at" => "2024-10-22T22:40:31.000000Z",
                    ],
                ],
            ],
            [
                "id" => 33,
                "codigo" => "11",
                "nombre" => "CABAÑAS ESTE",
                "departamento_id" => 10,
                "created_at" => "2024-10-22T22:40:03.000000Z",
                "updated_at" => "2024-10-22T22:40:03.000000Z",
                "distritos" => [
                    [
                        "id" => 159,
                        "nombre" => "Sensuntepeque",
                        "municipio_id" => 33,
                        "created_at" => "2024-10-22T22:40:31.000000Z",
                        "updated_at" => "2024-10-22T22:40:31.000000Z",
                    ],
                    [
                        "id" => 160,
                        "nombre" => "Victoria",
                        "municipio_id" => 33,
                        "created_at" => "2024-10-22T22:40:32.000000Z",
                        "updated_at" => "2024-10-22T22:40:32.000000Z",
                    ],
                    [
                        "id" => 161,
                        "nombre" => "Dolores",
                        "municipio_id" => 33,
                        "created_at" => "2024-10-22T22:40:32.000000Z",
                        "updated_at" => "2024-10-22T22:40:32.000000Z",
                    ],
                    [
                        "id" => 162,
                        "nombre" => "Guacotecti",
                        "municipio_id" => 33,
                        "created_at" => "2024-10-22T22:40:32.000000Z",
                        "updated_at" => "2024-10-22T22:40:32.000000Z",
                    ],
                    [
                        "id" => 163,
                        "nombre" => "San Isidro",
                        "municipio_id" => 33,
                        "created_at" => "2024-10-22T22:40:32.000000Z",
                        "updated_at" => "2024-10-22T22:40:32.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 11,
        "codigo" => "10",
        "nombre" => "San Vicente",
        "created_at" => "2024-10-22T22:39:57.000000Z",
        "updated_at" => "2024-10-22T22:39:57.000000Z",
        "municipios" => [
            [
                "id" => 34,
                "codigo" => "14",
                "nombre" => "SAN VICENTE NORTE",
                "departamento_id" => 11,
                "created_at" => "2024-10-22T22:40:04.000000Z",
                "updated_at" => "2024-10-22T22:40:04.000000Z",
                "distritos" => [
                    [
                        "id" => 164,
                        "nombre" => "Apastepeque",
                        "municipio_id" => 34,
                        "created_at" => "2024-10-22T22:40:32.000000Z",
                        "updated_at" => "2024-10-22T22:40:32.000000Z",
                    ],
                    [
                        "id" => 165,
                        "nombre" => "Santa Clara",
                        "municipio_id" => 34,
                        "created_at" => "2024-10-22T22:40:32.000000Z",
                        "updated_at" => "2024-10-22T22:40:32.000000Z",
                    ],
                    [
                        "id" => 166,
                        "nombre" => "San Ildefonso",
                        "municipio_id" => 34,
                        "created_at" => "2024-10-22T22:40:33.000000Z",
                        "updated_at" => "2024-10-22T22:40:33.000000Z",
                    ],
                    [
                        "id" => 167,
                        "nombre" => "San Esteban Catarina",
                        "municipio_id" => 34,
                        "created_at" => "2024-10-22T22:40:33.000000Z",
                        "updated_at" => "2024-10-22T22:40:33.000000Z",
                    ],
                    [
                        "id" => 168,
                        "nombre" => "San Sebastián",
                        "municipio_id" => 34,
                        "created_at" => "2024-10-22T22:40:33.000000Z",
                        "updated_at" => "2024-10-22T22:40:33.000000Z",
                    ],
                    [
                        "id" => 169,
                        "nombre" => "San Lorenzo",
                        "municipio_id" => 34,
                        "created_at" => "2024-10-22T22:40:33.000000Z",
                        "updated_at" => "2024-10-22T22:40:33.000000Z",
                    ],
                    [
                        "id" => 170,
                        "nombre" => "Santo Domingo",
                        "municipio_id" => 34,
                        "created_at" => "2024-10-22T22:40:33.000000Z",
                        "updated_at" => "2024-10-22T22:40:33.000000Z",
                    ],
                ],
            ],
            [
                "id" => 35,
                "codigo" => "15",
                "nombre" => "SAN VICENTE SUR",
                "departamento_id" => 11,
                "created_at" => "2024-10-22T22:40:04.000000Z",
                "updated_at" => "2024-10-22T22:40:04.000000Z",
                "distritos" => [
                    [
                        "id" => 171,
                        "nombre" => "San Vicente",
                        "municipio_id" => 35,
                        "created_at" => "2024-10-22T22:40:33.000000Z",
                        "updated_at" => "2024-10-22T22:40:33.000000Z",
                    ],
                    [
                        "id" => 172,
                        "nombre" => "Guadalupe",
                        "municipio_id" => 35,
                        "created_at" => "2024-10-22T22:40:34.000000Z",
                        "updated_at" => "2024-10-22T22:40:34.000000Z",
                    ],
                    [
                        "id" => 173,
                        "nombre" => "Verapaz",
                        "municipio_id" => 35,
                        "created_at" => "2024-10-22T22:40:34.000000Z",
                        "updated_at" => "2024-10-22T22:40:34.000000Z",
                    ],
                    [
                        "id" => 174,
                        "nombre" => "Tepetitán",
                        "municipio_id" => 35,
                        "created_at" => "2024-10-22T22:40:34.000000Z",
                        "updated_at" => "2024-10-22T22:40:34.000000Z",
                    ],
                    [
                        "id" => 175,
                        "nombre" => "Tecoluca",
                        "municipio_id" => 35,
                        "created_at" => "2024-10-22T22:40:34.000000Z",
                        "updated_at" => "2024-10-22T22:40:34.000000Z",
                    ],
                    [
                        "id" => 176,
                        "nombre" => "San Cayetano Istepeque",
                        "municipio_id" => 35,
                        "created_at" => "2024-10-22T22:40:34.000000Z",
                        "updated_at" => "2024-10-22T22:40:34.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 12,
        "codigo" => "11",
        "nombre" => "Usulután",
        "created_at" => "2024-10-22T22:39:58.000000Z",
        "updated_at" => "2024-10-22T22:39:58.000000Z",
        "municipios" => [
            [
                "id" => 36,
                "codigo" => "24",
                "nombre" => "USULUTAN NORTE",
                "departamento_id" => 12,
                "created_at" => "2024-10-22T22:40:04.000000Z",
                "updated_at" => "2024-10-22T22:40:04.000000Z",
                "distritos" => [
                    [
                        "id" => 177,
                        "nombre" => "Santiago de María",
                        "municipio_id" => 36,
                        "created_at" => "2024-10-22T22:40:34.000000Z",
                        "updated_at" => "2024-10-22T22:40:34.000000Z",
                    ],
                    [
                        "id" => 178,
                        "nombre" => "Alegría",
                        "municipio_id" => 36,
                        "created_at" => "2024-10-22T22:40:35.000000Z",
                        "updated_at" => "2024-10-22T22:40:35.000000Z",
                    ],
                    [
                        "id" => 179,
                        "nombre" => "Berlín",
                        "municipio_id" => 36,
                        "created_at" => "2024-10-22T22:40:35.000000Z",
                        "updated_at" => "2024-10-22T22:40:35.000000Z",
                    ],
                    [
                        "id" => 180,
                        "nombre" => "Mercedes Umaña",
                        "municipio_id" => 36,
                        "created_at" => "2024-10-22T22:40:35.000000Z",
                        "updated_at" => "2024-10-22T22:40:35.000000Z",
                    ],
                    [
                        "id" => 181,
                        "nombre" => "Jucuapa",
                        "municipio_id" => 36,
                        "created_at" => "2024-10-22T22:40:35.000000Z",
                        "updated_at" => "2024-10-22T22:40:35.000000Z",
                    ],
                    [
                        "id" => 182,
                        "nombre" => "El Triunfo",
                        "municipio_id" => 36,
                        "created_at" => "2024-10-22T22:40:35.000000Z",
                        "updated_at" => "2024-10-22T22:40:35.000000Z",
                    ],
                    [
                        "id" => 183,
                        "nombre" => "Estanzuelas",
                        "municipio_id" => 36,
                        "created_at" => "2024-10-22T22:40:35.000000Z",
                        "updated_at" => "2024-10-22T22:40:35.000000Z",
                    ],
                    [
                        "id" => 184,
                        "nombre" => "San Buenaventura",
                        "municipio_id" => 36,
                        "created_at" => "2024-10-22T22:40:35.000000Z",
                        "updated_at" => "2024-10-22T22:40:35.000000Z",
                    ],
                    [
                        "id" => 185,
                        "nombre" => "Nueva Granada",
                        "municipio_id" => 36,
                        "created_at" => "2024-10-22T22:40:36.000000Z",
                        "updated_at" => "2024-10-22T22:40:36.000000Z",
                    ],
                ],
            ],
            [
                "id" => 37,
                "codigo" => "25",
                "nombre" => "USULUTAN ESTE",
                "departamento_id" => 12,
                "created_at" => "2024-10-22T22:40:04.000000Z",
                "updated_at" => "2024-10-22T22:40:04.000000Z",
                "distritos" => [
                    [
                        "id" => 186,
                        "nombre" => "Usulután",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:36.000000Z",
                        "updated_at" => "2024-10-22T22:40:36.000000Z",
                    ],
                    [
                        "id" => 187,
                        "nombre" => "Jucuarán",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:36.000000Z",
                        "updated_at" => "2024-10-22T22:40:36.000000Z",
                    ],
                    [
                        "id" => 188,
                        "nombre" => "San Dionisio",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:36.000000Z",
                        "updated_at" => "2024-10-22T22:40:36.000000Z",
                    ],
                    [
                        "id" => 189,
                        "nombre" => "Concepción Batres",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:36.000000Z",
                        "updated_at" => "2024-10-22T22:40:36.000000Z",
                    ],
                    [
                        "id" => 190,
                        "nombre" => "Santa María",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:36.000000Z",
                        "updated_at" => "2024-10-22T22:40:36.000000Z",
                    ],
                    [
                        "id" => 191,
                        "nombre" => "Ozatlán",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:37.000000Z",
                        "updated_at" => "2024-10-22T22:40:37.000000Z",
                    ],
                    [
                        "id" => 192,
                        "nombre" => "Tecapán",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:37.000000Z",
                        "updated_at" => "2024-10-22T22:40:37.000000Z",
                    ],
                    [
                        "id" => 193,
                        "nombre" => "Santa Elena",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:37.000000Z",
                        "updated_at" => "2024-10-22T22:40:37.000000Z",
                    ],
                    [
                        "id" => 194,
                        "nombre" => "California",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:37.000000Z",
                        "updated_at" => "2024-10-22T22:40:37.000000Z",
                    ],
                    [
                        "id" => 195,
                        "nombre" => "Ereguayquín",
                        "municipio_id" => 37,
                        "created_at" => "2024-10-22T22:40:37.000000Z",
                        "updated_at" => "2024-10-22T22:40:37.000000Z",
                    ],
                ],
            ],
            [
                "id" => 38,
                "codigo" => "26",
                "nombre" => "USULUTAN OESTE",
                "departamento_id" => 12,
                "created_at" => "2024-10-22T22:40:04.000000Z",
                "updated_at" => "2024-10-22T22:40:04.000000Z",
                "distritos" => [
                    [
                        "id" => 196,
                        "nombre" => "Jiquilisco",
                        "municipio_id" => 38,
                        "created_at" => "2024-10-22T22:40:37.000000Z",
                        "updated_at" => "2024-10-22T22:40:37.000000Z",
                    ],
                    [
                        "id" => 197,
                        "nombre" => "Puerto El Triunfo",
                        "municipio_id" => 38,
                        "created_at" => "2024-10-22T22:40:38.000000Z",
                        "updated_at" => "2024-10-22T22:40:38.000000Z",
                    ],
                    [
                        "id" => 198,
                        "nombre" => "San Agustín",
                        "municipio_id" => 38,
                        "created_at" => "2024-10-22T22:40:38.000000Z",
                        "updated_at" => "2024-10-22T22:40:38.000000Z",
                    ],
                    [
                        "id" => 199,
                        "nombre" => "San Francisco Javier",
                        "municipio_id" => 38,
                        "created_at" => "2024-10-22T22:40:38.000000Z",
                        "updated_at" => "2024-10-22T22:40:38.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 13,
        "codigo" => "12",
        "nombre" => "San Miguel",
        "created_at" => "2024-10-22T22:39:58.000000Z",
        "updated_at" => "2024-10-22T22:39:58.000000Z",
        "municipios" => [
            [
                "id" => 39,
                "codigo" => "21",
                "nombre" => "SAN MIGUEL NORTE",
                "departamento_id" => 13,
                "created_at" => "2024-10-22T22:40:04.000000Z",
                "updated_at" => "2024-10-22T22:40:04.000000Z",
                "distritos" => [
                    [
                        "id" => 200,
                        "nombre" => "Ciudad Barrios",
                        "municipio_id" => 39,
                        "created_at" => "2024-10-22T22:40:38.000000Z",
                        "updated_at" => "2024-10-22T22:40:38.000000Z",
                    ],
                    [
                        "id" => 201,
                        "nombre" => "Sesori",
                        "municipio_id" => 39,
                        "created_at" => "2024-10-22T22:40:38.000000Z",
                        "updated_at" => "2024-10-22T22:40:38.000000Z",
                    ],
                    [
                        "id" => 202,
                        "nombre" => "Nuevo Edén de San Juan",
                        "municipio_id" => 39,
                        "created_at" => "2024-10-22T22:40:38.000000Z",
                        "updated_at" => "2024-10-22T22:40:38.000000Z",
                    ],
                    [
                        "id" => 203,
                        "nombre" => "San Gerardo",
                        "municipio_id" => 39,
                        "created_at" => "2024-10-22T22:40:39.000000Z",
                        "updated_at" => "2024-10-22T22:40:39.000000Z",
                    ],
                    [
                        "id" => 204,
                        "nombre" => "San Luis de La Reina",
                        "municipio_id" => 39,
                        "created_at" => "2024-10-22T22:40:39.000000Z",
                        "updated_at" => "2024-10-22T22:40:39.000000Z",
                    ],
                    [
                        "id" => 205,
                        "nombre" => "Carolina",
                        "municipio_id" => 39,
                        "created_at" => "2024-10-22T22:40:39.000000Z",
                        "updated_at" => "2024-10-22T22:40:39.000000Z",
                    ],
                    [
                        "id" => 206,
                        "nombre" => "San Antonio del Mosco",
                        "municipio_id" => 39,
                        "created_at" => "2024-10-22T22:40:39.000000Z",
                        "updated_at" => "2024-10-22T22:40:39.000000Z",
                    ],
                ],
            ],
            [
                "id" => 40,
                "codigo" => "22",
                "nombre" => "SAN MIGUEL CENTRO",
                "departamento_id" => 13,
                "created_at" => "2024-10-22T22:40:05.000000Z",
                "updated_at" => "2024-10-22T22:40:05.000000Z",
                "distritos" => [
                    [
                        "id" => 207,
                        "nombre" => "Chapeltique",
                        "municipio_id" => 40,
                        "created_at" => "2024-10-22T22:40:39.000000Z",
                        "updated_at" => "2024-10-22T22:40:39.000000Z",
                    ],
                    [
                        "id" => 208,
                        "nombre" => "San Miguel",
                        "municipio_id" => 40,
                        "created_at" => "2024-10-22T22:40:39.000000Z",
                        "updated_at" => "2024-10-22T22:40:39.000000Z",
                    ],
                    [
                        "id" => 209,
                        "nombre" => "Comacarán",
                        "municipio_id" => 40,
                        "created_at" => "2024-10-22T22:40:40.000000Z",
                        "updated_at" => "2024-10-22T22:40:40.000000Z",
                    ],
                    [
                        "id" => 210,
                        "nombre" => "Ulúazapa",
                        "municipio_id" => 40,
                        "created_at" => "2024-10-22T22:40:40.000000Z",
                        "updated_at" => "2024-10-22T22:40:40.000000Z",
                    ],
                    [
                        "id" => 211,
                        "nombre" => "Moncagua",
                        "municipio_id" => 40,
                        "created_at" => "2024-10-22T22:40:40.000000Z",
                        "updated_at" => "2024-10-22T22:40:40.000000Z",
                    ],
                    [
                        "id" => 212,
                        "nombre" => "Quelepa",
                        "municipio_id" => 40,
                        "created_at" => "2024-10-22T22:40:40.000000Z",
                        "updated_at" => "2024-10-22T22:40:40.000000Z",
                    ],
                    [
                        "id" => 213,
                        "nombre" => "Chirilagua",
                        "municipio_id" => 40,
                        "created_at" => "2024-10-22T22:40:40.000000Z",
                        "updated_at" => "2024-10-22T22:40:40.000000Z",
                    ],
                ],
            ],
            [
                "id" => 41,
                "codigo" => "23",
                "nombre" => "SAN MIGUEL OESTE",
                "departamento_id" => 13,
                "created_at" => "2024-10-22T22:40:05.000000Z",
                "updated_at" => "2024-10-22T22:40:05.000000Z",
                "distritos" => [
                    [
                        "id" => 214,
                        "nombre" => "Chinameca",
                        "municipio_id" => 41,
                        "created_at" => "2024-10-22T22:40:40.000000Z",
                        "updated_at" => "2024-10-22T22:40:40.000000Z",
                    ],
                    [
                        "id" => 215,
                        "nombre" => "Nueva Guadalupe",
                        "municipio_id" => 41,
                        "created_at" => "2024-10-22T22:40:41.000000Z",
                        "updated_at" => "2024-10-22T22:40:41.000000Z",
                    ],
                    [
                        "id" => 216,
                        "nombre" => "Lolotique",
                        "municipio_id" => 41,
                        "created_at" => "2024-10-22T22:40:41.000000Z",
                        "updated_at" => "2024-10-22T22:40:41.000000Z",
                    ],
                    [
                        "id" => 217,
                        "nombre" => "San Jorge",
                        "municipio_id" => 41,
                        "created_at" => "2024-10-22T22:40:41.000000Z",
                        "updated_at" => "2024-10-22T22:40:41.000000Z",
                    ],
                    [
                        "id" => 218,
                        "nombre" => "San Rafael Oriente",
                        "municipio_id" => 41,
                        "created_at" => "2024-10-22T22:40:41.000000Z",
                        "updated_at" => "2024-10-22T22:40:41.000000Z",
                    ],
                    [
                        "id" => 219,
                        "nombre" => "El Tránsito",
                        "municipio_id" => 41,
                        "created_at" => "2024-10-22T22:40:41.000000Z",
                        "updated_at" => "2024-10-22T22:40:41.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 14,
        "codigo" => "13",
        "nombre" => "Morazán",
        "created_at" => "2024-10-22T22:39:58.000000Z",
        "updated_at" => "2024-10-22T22:39:58.000000Z",
        "municipios" => [
            [
                "id" => 42,
                "codigo" => "27",
                "nombre" => "MORAZAN NORTE",
                "departamento_id" => 14,
                "created_at" => "2024-10-22T22:40:05.000000Z",
                "updated_at" => "2024-10-22T22:40:05.000000Z",
                "distritos" => [
                    [
                        "id" => 220,
                        "nombre" => "Arambala",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:41.000000Z",
                        "updated_at" => "2024-10-22T22:40:41.000000Z",
                    ],
                    [
                        "id" => 221,
                        "nombre" => "Cacaopera",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:42.000000Z",
                        "updated_at" => "2024-10-22T22:40:42.000000Z",
                    ],
                    [
                        "id" => 222,
                        "nombre" => "Corinto",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:42.000000Z",
                        "updated_at" => "2024-10-22T22:40:42.000000Z",
                    ],
                    [
                        "id" => 223,
                        "nombre" => "El Rosario",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:42.000000Z",
                        "updated_at" => "2024-10-22T22:40:42.000000Z",
                    ],
                    [
                        "id" => 224,
                        "nombre" => "Joateca",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:42.000000Z",
                        "updated_at" => "2024-10-22T22:40:42.000000Z",
                    ],
                    [
                        "id" => 225,
                        "nombre" => "Jocoaitique",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:42.000000Z",
                        "updated_at" => "2024-10-22T22:40:42.000000Z",
                    ],
                    [
                        "id" => 226,
                        "nombre" => "Meanguera",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:42.000000Z",
                        "updated_at" => "2024-10-22T22:40:42.000000Z",
                    ],
                    [
                        "id" => 227,
                        "nombre" => "Perquín",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:43.000000Z",
                        "updated_at" => "2024-10-22T22:40:43.000000Z",
                    ],
                    [
                        "id" => 228,
                        "nombre" => "San Fernando",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:43.000000Z",
                        "updated_at" => "2024-10-22T22:40:43.000000Z",
                    ],
                    [
                        "id" => 229,
                        "nombre" => "San Isidro",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:43.000000Z",
                        "updated_at" => "2024-10-22T22:40:43.000000Z",
                    ],
                    [
                        "id" => 230,
                        "nombre" => "Torola",
                        "municipio_id" => 42,
                        "created_at" => "2024-10-22T22:40:43.000000Z",
                        "updated_at" => "2024-10-22T22:40:43.000000Z",
                    ],
                ],
            ],
            [
                "id" => 43,
                "codigo" => "28",
                "nombre" => "MORAZAN SUR",
                "departamento_id" => 14,
                "created_at" => "2024-10-22T22:40:05.000000Z",
                "updated_at" => "2024-10-22T22:40:05.000000Z",
                "distritos" => [
                    [
                        "id" => 231,
                        "nombre" => "Chilanga",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:43.000000Z",
                        "updated_at" => "2024-10-22T22:40:43.000000Z",
                    ],
                    [
                        "id" => 232,
                        "nombre" => "Delicias de Concepción",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:43.000000Z",
                        "updated_at" => "2024-10-22T22:40:43.000000Z",
                    ],
                    [
                        "id" => 233,
                        "nombre" => "El Divisadero",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:43.000000Z",
                        "updated_at" => "2024-10-22T22:40:43.000000Z",
                    ],
                    [
                        "id" => 234,
                        "nombre" => "Gualococti",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:44.000000Z",
                        "updated_at" => "2024-10-22T22:40:44.000000Z",
                    ],
                    [
                        "id" => 235,
                        "nombre" => "Guatajiagua",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:44.000000Z",
                        "updated_at" => "2024-10-22T22:40:44.000000Z",
                    ],
                    [
                        "id" => 236,
                        "nombre" => "Jocoro",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:44.000000Z",
                        "updated_at" => "2024-10-22T22:40:44.000000Z",
                    ],
                    [
                        "id" => 237,
                        "nombre" => "Lolotiquillo",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:44.000000Z",
                        "updated_at" => "2024-10-22T22:40:44.000000Z",
                    ],
                    [
                        "id" => 238,
                        "nombre" => "Osicala",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:44.000000Z",
                        "updated_at" => "2024-10-22T22:40:44.000000Z",
                    ],
                    [
                        "id" => 239,
                        "nombre" => "San Carlos",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:44.000000Z",
                        "updated_at" => "2024-10-22T22:40:44.000000Z",
                    ],
                    [
                        "id" => 240,
                        "nombre" => "San Francisco Gotera",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:45.000000Z",
                        "updated_at" => "2024-10-22T22:40:45.000000Z",
                    ],
                    [
                        "id" => 241,
                        "nombre" => "San Simón",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:45.000000Z",
                        "updated_at" => "2024-10-22T22:40:45.000000Z",
                    ],
                    [
                        "id" => 242,
                        "nombre" => "Sensemara",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:45.000000Z",
                        "updated_at" => "2024-10-22T22:40:45.000000Z",
                    ],
                    [
                        "id" => 243,
                        "nombre" => "Sociedad",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:45.000000Z",
                        "updated_at" => "2024-10-22T22:40:45.000000Z",
                    ],
                    [
                        "id" => 244,
                        "nombre" => "Yamabal",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:45.000000Z",
                        "updated_at" => "2024-10-22T22:40:45.000000Z",
                    ],
                    [
                        "id" => 245,
                        "nombre" => "Yoloaiquín",
                        "municipio_id" => 43,
                        "created_at" => "2024-10-22T22:40:45.000000Z",
                        "updated_at" => "2024-10-22T22:40:45.000000Z",
                    ],
                ],
            ],
        ],
    ],
    [
        "id" => 15,
        "codigo" => "14",
        "nombre" => "La Unión",
        "created_at" => "2024-10-22T22:39:58.000000Z",
        "updated_at" => "2024-10-22T22:39:58.000000Z",
        "municipios" => [
            [
                "id" => 44,
                "codigo" => "19",
                "nombre" => "LA UNION NORTE",
                "departamento_id" => 15,
                "created_at" => "2024-10-22T22:40:05.000000Z",
                "updated_at" => "2024-10-22T22:40:05.000000Z",
                "distritos" => [
                    [
                        "id" => 246,
                        "nombre" => "Anamorós",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:46.000000Z",
                        "updated_at" => "2024-10-22T22:40:46.000000Z",
                    ],
                    [
                        "id" => 247,
                        "nombre" => "Bolívar",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:46.000000Z",
                        "updated_at" => "2024-10-22T22:40:46.000000Z",
                    ],
                    [
                        "id" => 248,
                        "nombre" => "Concepción de Oriente",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:46.000000Z",
                        "updated_at" => "2024-10-22T22:40:46.000000Z",
                    ],
                    [
                        "id" => 249,
                        "nombre" => "El Sauce",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:46.000000Z",
                        "updated_at" => "2024-10-22T22:40:46.000000Z",
                    ],
                    [
                        "id" => 250,
                        "nombre" => "Lislique",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:46.000000Z",
                        "updated_at" => "2024-10-22T22:40:46.000000Z",
                    ],
                    [
                        "id" => 251,
                        "nombre" => "Nueva Esparta",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:46.000000Z",
                        "updated_at" => "2024-10-22T22:40:46.000000Z",
                    ],
                    [
                        "id" => 252,
                        "nombre" => "Pasquina",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:47.000000Z",
                        "updated_at" => "2024-10-22T22:40:47.000000Z",
                    ],
                    [
                        "id" => 253,
                        "nombre" => "Polorós",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:47.000000Z",
                        "updated_at" => "2024-10-22T22:40:47.000000Z",
                    ],
                    [
                        "id" => 254,
                        "nombre" => "San José La Fuente",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:47.000000Z",
                        "updated_at" => "2024-10-22T22:40:47.000000Z",
                    ],
                    [
                        "id" => 255,
                        "nombre" => "Santa Rosa de Lima",
                        "municipio_id" => 44,
                        "created_at" => "2024-10-22T22:40:47.000000Z",
                        "updated_at" => "2024-10-22T22:40:47.000000Z",
                    ],
                ],
            ],
            [
                "id" => 45,
                "codigo" => "20",
                "nombre" => "LA UNION SUR",
                "departamento_id" => 15,
                "created_at" => "2024-10-22T22:40:05.000000Z",
                "updated_at" => "2024-10-22T22:40:05.000000Z",
                "distritos" => [
                    [
                        "id" => 256,
                        "nombre" => "Conchagua",
                        "municipio_id" => 45,
                        "created_at" => "2024-10-22T22:40:47.000000Z",
                        "updated_at" => "2024-10-22T22:40:47.000000Z",
                    ],
                    [
                        "id" => 257,
                        "nombre" => "El Carmen",
                        "municipio_id" => 45,
                        "created_at" => "2024-10-22T22:40:47.000000Z",
                        "updated_at" => "2024-10-22T22:40:47.000000Z",
                    ],
                    [
                        "id" => 258,
                        "nombre" => "Intipucá",
                        "municipio_id" => 45,
                        "created_at" => "2024-10-22T22:40:48.000000Z",
                        "updated_at" => "2024-10-22T22:40:48.000000Z",
                    ],
                    [
                        "id" => 259,
                        "nombre" => "La Unión",
                        "municipio_id" => 45,
                        "created_at" => "2024-10-22T22:40:48.000000Z",
                        "updated_at" => "2024-10-22T22:40:48.000000Z",
                    ],
                    [
                        "id" => 260,
                        "nombre" => "Meanguera del Golfo",
                        "municipio_id" => 45,
                        "created_at" => "2024-10-22T22:40:48.000000Z",
                        "updated_at" => "2024-10-22T22:40:48.000000Z",
                    ],
                    [
                        "id" => 261,
                        "nombre" => "San Alejo",
                        "municipio_id" => 45,
                        "created_at" => "2024-10-22T22:40:48.000000Z",
                        "updated_at" => "2024-10-22T22:40:48.000000Z",
                    ],
                    [
                        "id" => 262,
                        "nombre" => "Yayantique",
                        "municipio_id" => 45,
                        "created_at" => "2024-10-22T22:40:48.000000Z",
                        "updated_at" => "2024-10-22T22:40:48.000000Z",
                    ],
                    [
                        "id" => 263,
                        "nombre" => "Yucuaiquín",
                        "municipio_id" => 45,
                        "created_at" => "2024-10-22T22:40:48.000000Z",
                        "updated_at" => "2024-10-22T22:40:48.000000Z",
                    ],
                ],
            ],
        ],
    ],
];


function buscarDepartamentoYMunicipio($array, $codigoDepartamento, $codigoMunicipio) {
    $direccion = "";
    // Buscar el departamento por código
    foreach ($array as $departamento) {
        if ($departamento['codigo'] === $codigoDepartamento) {
            $direccion .= $departamento['nombre'] . ", ";
            // Buscar el municipio dentro del departamento
            foreach ($departamento['municipios'] as $municipio) {
                if ($municipio['codigo'] === $codigoMunicipio) {
                    $direccion .= $municipio['nombre'];
                }
            }
        }
    }
    return $direccion;
}


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
    global $distritos;

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

    if ($documentoArray["receptor"]["direccion"] != null){
        $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["receptor"]["direccion"]["departamento"], $documentoArray["receptor"]["direccion"]["municipio"]);
        $direccion = $cod_distrito. " ". $documentoArray["receptor"]["direccion"]["complemento"];
    }
    else{
        $direccion = "";
    }
    $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["emisor"]["direccion"]["departamento"], $documentoArray["emisor"]["direccion"]["municipio"]);
    $direccion_emisor = $cod_distrito. " ". $documentoArray["emisor"]["direccion"]["complemento"];

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
        $direccion_emisor,
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
    global $distritos;

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

    if ($documentoArray["receptor"]["direccion"] != null){
        $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["receptor"]["direccion"]["departamento"], $documentoArray["receptor"]["direccion"]["municipio"]);
        $direccion = $cod_distrito. " ". $documentoArray["receptor"]["direccion"]["complemento"];
    }
    else{
        $direccion = "";
    }
    $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["emisor"]["direccion"]["departamento"], $documentoArray["emisor"]["direccion"]["municipio"]);
    $direccion_emisor = $cod_distrito. " ". $documentoArray["emisor"]["direccion"]["complemento"];

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
        $direccion_emisor,
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
    global $distritos;

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

    if ($documentoArray["sujetoExcluido"]["direccion"] != null){
        $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["sujetoExcluido"]["direccion"]["departamento"], $documentoArray["sujetoExcluido"]["direccion"]["municipio"]);
        $direccion = $cod_distrito. " ". $documentoArray["sujetoExcluido"]["direccion"]["complemento"];
    }
    else{
        $direccion = "";
    }
    $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["emisor"]["direccion"]["departamento"], $documentoArray["emisor"]["direccion"]["municipio"]);
    $direccion_emisor = $cod_distrito. " ". $documentoArray["emisor"]["direccion"]["complemento"];

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
        $direccion_emisor,
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
    global $distritos;

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

    if ($documentoArray["receptor"]["direccion"] != null){
        $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["receptor"]["direccion"]["departamento"], $documentoArray["receptor"]["direccion"]["municipio"]);
        $direccion = $cod_distrito. " ". $documentoArray["receptor"]["direccion"]["complemento"];
    }
    else{
        $direccion = "";
    }
    $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["emisor"]["direccion"]["departamento"], $documentoArray["emisor"]["direccion"]["municipio"]);
    $direccion_emisor = $cod_distrito. " ". $documentoArray["emisor"]["direccion"]["complemento"];

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
        $direccion_emisor,
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
    global $distritos;

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

    if ($documentoArray["receptor"]["direccion"] != null){
        $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["receptor"]["direccion"]["departamento"], $documentoArray["receptor"]["direccion"]["municipio"]);
        $direccion = $cod_distrito. " ". $documentoArray["receptor"]["direccion"]["complemento"];
    }
    else{
        $direccion = "";
    }
    $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["emisor"]["direccion"]["departamento"], $documentoArray["emisor"]["direccion"]["municipio"]);
    $direccion_emisor = $cod_distrito. " ". $documentoArray["emisor"]["direccion"]["complemento"];

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
        $direccion_emisor,
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
    global $distritos;

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

    $cod_distrito = buscarDepartamentoYMunicipio($distritos, $documentoArray["emisor"]["direccion"]["departamento"], $documentoArray["emisor"]["direccion"]["municipio"]);
    $direccion_emisor = $cod_distrito. " ". $documentoArray["emisor"]["direccion"]["complemento"];

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
        $direccion_emisor,
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