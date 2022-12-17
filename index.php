<?php 

    /*=======================================================
    Mostrar errores
    =======================================================*/
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', "C:/xampp/htdocs/apirest/php_error_log");

    /*=======================================================
    CORS
    =======================================================*/
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('content-type: application/json; charset=utf-8');
    
    /*=======================================================
    Requerimientos
    =======================================================*/
    require_once "modelos/conexion.php";
    require_once "controladores/rutas.controlador.php";
    $index = new rutas_controlador();
    $index -> index();
?>