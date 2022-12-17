<?php 
    require_once "modelos/conexion.php";
    require_once "controladores/get.controlador.php";

    $url = !empty($_GET['url']) ? $_GET['url'] : '/'; 
    $rutas_array = explode("/", $url);
    $rutas_array = array_filter($rutas_array);
    //print_r($rutas_array);
    if (count($rutas_array) == 0) {
        $json = array(
            'estado' => 404,
            'resultado' => 'No encontrado'
        );
        echo json_encode($json, http_response_code($json['estado']));
        return;
    }
    
    /*=======================================================
    Cuando si se hace una peticion a la API
    =======================================================*/
    if (count($rutas_array) == 1 && isset($_SERVER['REQUEST_METHOD'])) {
        /*=======================================================
        VALIDAR LLAVE SECRETA API KEY
        =======================================================*/
        if (!isset(getallheaders()["apikey"]) || getallheaders()["apikey"] != conexion::apikey()) { //si no se envia el header de la api key o esta incorrecto
            if (in_array($tabla, conexion::acceso_publico()) == 0) { //si la tabla que viene en url NO esta incluida en el array de acceso publico
                $json = array(
                    'estado' => 400,
                    'resultado' => 'No tiene autorización para hacer la petición'
                );
                echo json_encode($json, http_response_code($json['estado']));
                return;
            }else{
                /*=======================================================
                Acceso publico
                =======================================================*/
                $respuesta = new get_controlador();
                $respuesta -> obtener_datos($tabla, "*", null, null, null, null);
                return;
            }
            
        }
        
        
        $tabla = $rutas_array[0];
        /*=======================================================
        PETICIONES GET
        =======================================================*/
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
           include "servicios/get.php";
        }

        /*=======================================================
        PETICIONES POST
        =======================================================*/
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            include "servicios/post.php";
        }

        /*=======================================================
        PETICIONES PUT
        =======================================================*/
        if ($_SERVER['REQUEST_METHOD'] == "PUT") {
            include "servicios/put.php";
        }

        /*=======================================================
        PETICIONES DELETE
        =======================================================*/
        if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
            include "servicios/delete.php";
        }
    }


?>