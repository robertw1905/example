<?php

require_once('dompdf/dompdf_config.inc.php');
	echo "<pre>";
	extract($_REQUEST);
	parse_str($data, $data);
	print_r($data);

	$dompdf = new DOMPDF(); 
    $dompdf->load_html( postPDF($data) );
    $dompdf->render();
    $dompdf->stream("buggy.pdf");

    /**
     * Funcion que realiza el post sobre la vista 
     * que necesitamos para el reporte PDF
     */
	function postPDF($data)
	{
		//Inicia sesión cURL
		$ch = curl_init();

		// Generar una cadena de consulta codificada estilo URLa
		$param_ = http_build_query($data);

		//Proporcionar la dirección URL que se utilizará en la solicitud
		curl_setopt($ch, CURLOPT_URL,"http://localhost/pruebas/htmlPDF.php");

		//Proporcioar el motodoque se usará para dhacer la solicitud
		curl_setopt($ch, CURLOPT_POST, 1);

		//Especificar los datos en POST al servidor
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param_);

		//Variable donde se almacena la salida de la petición
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//Establece una sesión cURL
		$server_output = curl_exec ($ch);

		//Cierra una sesión cURL
		curl_close ($ch);

		//Se retorna la salida de la sesión
		return $server_output;
	}

/**
	Codigo en la vista

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <body>
     <?php
	     $data = $_REQUEST;
	     print_r($data['rut']);
     ?>
    </body>
</html>

*/
?>