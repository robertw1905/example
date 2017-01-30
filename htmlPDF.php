<?php
// ubicacion prueba
$geo_ubicacion[0] = "-33.4420004";
$geo_ubicacion[1] = "-70.6636011";

?>
<!DOCTYPE html>
<html>
    <head>
    	<title></title>
    	<style type="text/css" media="screen">
    		body{font-size:1.2em;}
    	</style>
        <!-- Minimapa -->
        <script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?key=AIzaSyD_AHXCSBqaEmh1CtsPcEMlLLeYkoLE4F8&libraries=places&callback=initMap' async defer></script>
        <!-- Minimapa -->
    	<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js" ></script>
        <!-- Exportar PDF -->
    	<script type="text/javascript">
    		function ExportaAnticipo()
    		{
    			var form = $("#form1").serialize();
    			window.location.href = '/pruebas/exportarPDF.php?data='+form;
    		}
    	</script>
        <!-- Exportar PDF -->
        <!-- Archivo CSV -->
    	<script type="text/javascript">
    		function validar_post()
    		{
    			var input = document.getElementById('uploadedfile');
    			var file = input.files[0];
    			if (typeof file === 'undefined')
    			{
    				return true;
    			}else{
    				if ( 
    					(file.size > <?=Upload_file::return_bytes(ini_get('post_max_size'))?>) ||
    					(file.size > <?=Upload_file::return_bytes(ini_get('upload_max_filesize'))?>)
    					)
    				{
    					alert("Tamaño del archivo supera la configuración de subida en el servidor")
    					return false;
    				}
    			}
    			return true
    		}
    	</script>
        <!-- Archivo CSV -->
        <!-- Minimapa -->
        <script type='text/javascript'>
                var geocoder;
                var map;
                var infowindow;
                var marker;
                var travel_mode;
                var directionsService;
                var directionsDisplay;
                var latitud;
                var longitud;
                var coordenadas = new Array();

                navigator.geolocation.getCurrentPosition(showPosition);

                function initMap() {
                        geocoder = new google.maps.Geocoder();
                        infowindow = new google.maps.InfoWindow();
                        travel_mode = google.maps.TravelMode.WALKING;
                        map = new google.maps.Map(document.getElementById('map'), {
                        //mapTypeControl: false,
                        center: {lat: <?=$geo_ubicacion[0]?>, lng: <?=$geo_ubicacion[1]?>},
                        navigationControl: true,
                        streetViewControl: true,
                        mapTypeControl: true,
                        rotateControl: true,
                        scaleControl: true,
                        zoomControl: true,
                        scrollwheel: true,
                        zoom: 13
                    });
                        marker = new google.maps.Marker({
                            position: {lat:<?=$geo_ubicacion[0]?>, lng:<?=$geo_ubicacion[1]?>},
                            map: map
                        });
                        coordenadas['des_lat'] = <?=$geo_ubicacion[0]?>;
                        coordenadas['des_log'] = <?=$geo_ubicacion[1]?>;

                    google.maps.event.addListener(map, 'click', function(){
                        closeInfoWindow();
                    });
                        directionsService = new google.maps.DirectionsService;
                        directionsDisplay = new google.maps.DirectionsRenderer;
                    directionsDisplay.setMap(map);

                    var go_input = document.getElementById('go-input');
                    var modes = document.getElementById('mode-selector');

                    map.controls[google.maps.ControlPosition.TOP_LEFT].push(modes);
                    map.controls[google.maps.ControlPosition.TOP_LEFT].push(go_input);

                    // Establece un elemento para cada 'radio button' y asi cambiar el filtro de modo de viaje
                    function setupClickListener(id, mode) {
                        var radioButton = document.getElementById(id);
                        radioButton.addEventListener('click', function() {
                            travel_mode = mode;
                        });
                    }
                    setupClickListener('changemode-walking', google.maps.TravelMode.WALKING);
                    setupClickListener('changemode-transit', google.maps.TravelMode.TRANSIT);
                    setupClickListener('changemode-driving', google.maps.TravelMode.DRIVING);

                    function expandViewportToFitPlace(map, place) {
                        if (place.geometry.viewport) {
                            map.fitBounds(place.geometry.viewport);
                        } else {
                            map.setCenter(place.geometry.location);
                            map.setZoom(17);
                        }
                    }

                    function closeInfoWindow()
                    {
                        infowindow.close();
                    }
                }

                /*
                    Funcion que crea la ruta entre dos puntos
                */
                function route(coordenadas, travel_mode,
                    directionsService, directionsDisplay)
                {
                    directionsService.route({
                        origin: new google.maps.LatLng(coordenadas['ori_lat'], coordenadas['ori_log']),
                        destination: new google.maps.LatLng(coordenadas['des_lat'], coordenadas['des_log']),
                        travelMode: travel_mode
                    }, function(response, status) {
                        if (status === google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(response);
                        } else {
                            window.alert('Directions request failed due to ' + status);
                        }
                    });
                }

                /*
                    Funcion que asigna las coordenadas de la posision en variables
                */
                function showPosition(position)
                {
                    latitud = position.coords.latitude;
                    longitud = position.coords.longitude;
                    coordenadas['ori_lat'] = latitud;
                    coordenadas['ori_log'] = longitud;
                }           

                /*
                    Funcion que solicita las coordenadas de origen y enruta a destino
                */
                function show()
                {
                    //var ubicacion = document.getElementById('ubicacion');
                    navigator.geolocation.getCurrentPosition(showPosition);
                    route(coordenadas, travel_mode, directionsService, directionsDisplay);
                }

                /*
                    Funcion que obtiene la direccion a partir de las coordenadas
                    Parametros de entrada latitud , longitud
                    Retorno Void
                */
                function codeLatLng(latitud, longitud)
                {
                    var latlng = new google.maps.LatLng(latitud, longitud);
                    geocoder.geocode({'latLng': latlng}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                      if (results[0]) {
                        map.fitBounds(results[0].geometry.viewport);
                                marker.setMap(map);
                                marker.setPosition(latlng);
                        //$('#'+input).val(results[0].formatted_address);
                        infowindow.setContent(results[0].formatted_address);
                        infowindow.open(map, marker);
                        google.maps.event.addListener(marker, 'click', function(){
                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(map, marker);
                        });
                      } else {
                        alert('No results found');
                      }
                    } else {
                      alert('Geocoder failed due to: ' + status);
                    }
                    });
                }
        </script>
        <!-- Minimapa -->
        <!-- Minimapa -->
        <style>
                html, body {
                    height: 100%;
                    margin: 0;
                    padding: 0;
                }
                #map {
                    height: 100%;
                }
                .controls {
                    margin-top: 10px;
                    border: 1px solid transparent;
                    border-radius: 2px 0 0 2px;
                    box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    height: 32px;
                    outline: none;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
                }

                #origin-input,
                #destination-input {
                    background-color: #fff;
                    font-family: Roboto;
                    font-size: 15px;
                    font-weight: 300;
                    margin-left: 12px;
                    padding: 0 11px 0 13px;
                    text-overflow: ellipsis;
                    width: 200px;
                }

                #origin-input:focus,
                #destination-input:focus {
                    border-color: #4d90fe;
                }

                #mode-selector {
                    color: #fff;
                    background-color: #4d90fe;
                    margin-left: 12px;
                    padding: 5px 11px 0px 11px;
                }

                #mode-selector label {
                    font-family: Roboto;
                    font-size: 13px;
                    font-weight: 300;
                }

                #go-input {
                    margin-left: 12px;
                }
        </style>
        <!-- Minimapa -->
    </head>
    <body>
    <h3>Exportar PDF</h3>
    <hr>
    	<form name="form1" id="form1">
    		<input type="text" name="rut" value="25.049.205-7">
    		<input type="button" name="expPDF" value="Exportar PDF" onclick="ExportaAnticipo()">	
    	</form>
    <h3>Web Service</h3>
    <hr>
    <form>
        <a href="ws/servicio.php">Web Service</a>   
    </form>
    <h3>Minimapa</h3>
    <hr>
            <div id='mode-selector' class='controls'>
                <input type='radio' name='type' id='changemode-walking' checked='checked'>
                <label for='changemode-walking'>Caminado</label>

                <input type='radio' name='type' id='changemode-transit'>
                <label for='changemode-transit'>Transporte Publico</label>

                <input type='radio' name='type' id='changemode-driving'>
                <label for='changemode-driving'>Manejando</label>
            </div>

            <input id='go-input' class='controls' type=image src='llegar.png' width='32' height='32' onclick='show()'>

            <center><div id='map' style='width:500px;height:300px;'></div></center>

    <h3>Archivo CSV</h3>
    <hr>
    	<form enctype="multipart/form-data" action="" method="POST">
    		<input type="hidden" name="MAX_FILE_SIZE" value="8388608" />
    		<input name="uploadedfile" id="uploadedfile" type="file">
    		<input type="submit" value="Subir archivo" onclick="return validar_post();">
    	</form> 

    </body>
</html>

<?php
//
//  Inicio de Php
//
if (!empty($_FILES))
{
    $upfile = new Upload_file($_FILES, "uploads/", array("csv"));

    if ($upfile->inicio())
    {
        $upfile->readFile();
        //$upfile->upload();
    }
        echo $upfile->msg;
}

/**
 * Clase para subir archivos con 
 * 
 */

class Upload_file
{
    protected $file;
    protected $target_path;
    public $msg;
    protected $format;

    function __construct($file, $target_path = "", $format = array())
    {
        $this->file = $file;
        $this->target_path = $target_path;
        $this->msg = "";
        $this->format = $format;
    }

    public function inicio()
    {
        return $this->validar();
    }

    protected function validar()
    {
        $error = $this->file["uploadedfile"]["error"];

        switch ($error) {
            case UPLOAD_ERR_OK:
                    $uploadedfileload="true";
                    $uploadedfile_size=$this->file['uploadedfile']['size'];
                    if ($uploadedfile_size > $this->return_bytes(ini_get('post_max_size')))
                    {
                        $this->msg = $this->msg."El archivo es mayor que ".ini_get('post_max_size').", debes reducirlo antes de subirlo<BR>";
                        $uploadedfileload="false";
                        return false;
                    }

                    $partes_ruta = pathinfo(basename($this->file["uploadedfile"]["name"]));

                    if (!in_array($partes_ruta['extension'], $this->format, true))
                    {
                        $this->msg = $this->msg." Tu archivo tiene que ser CSV. Otros archivos no son permitidos<BR>";
                        $uploadedfileload="false";
                        unset($this->file);
                        return false;
                    }
                    if($uploadedfileload=="true")
                    {
                        return true;
                    }
                break;
            case UPLOAD_ERR_INI_SIZE:
                    $this->msg = $this->msg."El fichero subido excede la directiva upload_max_filesize de php.ini.";
                    return false;
                break;
            case UPLOAD_ERR_FORM_SIZE:
                    $this->msg = $this->msg."El fichero subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML";
                    return false;
                break;
            case UPLOAD_ERR_PARTIAL:
                    $this->msg = $this->msg."El fichero fue sólo parcialmente subido.";
                    return false;
                break;
            case UPLOAD_ERR_NO_FILE:
                    $this->msg = $this->msg."No se subió ningún fichero.";
                    return false;
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                    $this->msg = $this->msg."Falta la carpeta temporal. Introducido en PHP 5.0.3.";
                    return false;
                break;
            case UPLOAD_ERR_CANT_WRITE:
                    $this->msg = $this->msg."No se pudo escribir el fichero en el disco. Introducido en PHP 5.1.0.";
                    return false;
                break;
            case UPLOAD_ERR_EXTENSION:
                    $this->msg = $this->msg."Una extensión de PHP detuvo la subida de ficheros.";
                    return false;
                break;
            default:
                    
                break;
        }

        if ($error == UPLOAD_ERR_OK)
        {
            
        }else{
            $this->msg = $this->msg."Seleccione un archivo";
            return false;
        }
    }

    public function upload()
    {
        $tmp_name = $this->file["uploadedfile"]["tmp_name"];
        $name = basename($this->file["uploadedfile"]["name"]);
        
        $partes_ruta = pathinfo($name);
        $name = $partes_ruta['filename'].time().".".$partes_ruta['extension'];

        $this->target_path = $this->target_path . $name; 

        if(move_uploaded_file($tmp_name, $this->target_path)) 
        {
            $this->msg = "<span style='color:green;'>El archivo ". $name . " ha sido subido satisfactoriamente</span><br>";
            unset($this->file);
        }else{
            $this->msg = "No se pudo subir el archivo";
        }
    }

    public function readFile()
    {
        $registros = array();
        if (($fichero = fopen($this->file['uploadedfile']['name'], "r")) !== FALSE)
        {
            // Lee los nombres de los campos
            $nombres_campos = fgetcsv($fichero, filesize(basename($this->file["uploadedfile"]["name"])), ",", "\"", "\"");
            $num_campos = count($nombres_campos);
            // Lee los registros
            while (($datos = fgetcsv($fichero, filesize(basename($this->file["uploadedfile"]["name"])), ",", "\"", "\"")) !== FALSE)
            {
                // Crea un array asociativo con los nombres y valores de los campos
                for ($icampo = 0; $icampo < $num_campos; $icampo++)
                {
                    $registro[$nombres_campos[$icampo]] = $datos[$icampo];
                }
                // Añade el registro leido al array de registros
                $registros[] = $registro;
            }
            fclose($fichero);

            echo "Leidos " . count($registros) . " registros\n<br>";

            foreach ($nombres_campos as $key => $value)
            {
                for ($i = 0; $i < count($registros); $i++)
                {
                    echo "Rut: " . $registros[$i][$value] . "\n<br>";
                }
            }
        }
    }

    public function return_bytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            // El modificador 'G' está disponble desde PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }
}