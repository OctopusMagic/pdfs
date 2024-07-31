<?php

function generate_link($codGen, $fechaEmi, $ambiente = "01"){
    return "https://admin.factura.gob.sv/consultaPublica?ambiente=$ambiente&codGen=$codGen&fechaEmi=$fechaEmi";
}