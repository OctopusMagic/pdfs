<?php
require 'vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

function generate_uri($url){
    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($url)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(300)
        ->margin(2)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->validateResult(false)
        ->build();

    return $result->getDataUri();
}