<?php
/** Clase Nusoap */
/** Luego de descargar la libreria Nusoap hacer uso solo de la carpeta lib*/
include_once 'lib/nusoap.php';

/** Instancia del servidor  */
$servicio = new nusoap_server();

/** Namespace */
$ns = "urn:miserviciowsdl";

/** Configuracion del Servicio */
$servicio->configureWSDL("Servicio Web - Nuevos Casos ",$ns);

/** Seteando NameSpace */
$servicio->schemaTargetNamespace = $ns;

// Parametros de entrada
$servicio->wsdl->addComplexType('Ruts',
								'complexType',
								'struct',
								'all',
								'',
								array(
								'name' => array('name' => 'name', 'type' => 'xsd:string')));

// Parametros de salida
$servicio->wsdl->addComplexType('Salida_wsNuevosCasos',
								'complexType',
								'struct',
								'all',
								'',
								array(
								'name' => array('name' => 'name', 'type' => 'xsd:string')));

$servicio->register("wsNuevosCasos", array('tipo_cliente' => 'xsd:integer', 'ruts' => 'tns:Ruts'), array('return' => 'xsd:string'), $ns );

function wsNuevosCasos($tipo_cliente, $ruts)
{
	$resultado = "El cliente es tipo " . $tipo_cliente . " y los rut son : \n";
	foreach ($ruts['name'] as $key => $value) {
		$resultado .= "Rut : ". $value. "\n";
	}
	return $resultado;
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$servicio->service($HTTP_RAW_POST_DATA);
/*
<name xsi:type="xsd:string">25049205-7</name>
<name xsi:type="xsd:string">15339625-6</name>
<name xsi:type="xsd:string">6968382-7</name>
*/
?>