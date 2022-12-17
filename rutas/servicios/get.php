<?php
    require_once "controladores/get.controlador.php";

    //$tabla = explode("?",$rutas_array[0]); //capturando solo el nombre de la tabla
    
    $seleccion = $_GET['seleccion'] ?? "*"; //si no esta declarado el parametro seleccion, sera *
    $ordenarpor = $_GET['ordenarpor'] ?? null;
    $ordenarmodo = $_GET['ordenarmodo'] ?? null;
    $iniciolimit = $_GET['iniciolimit'] ?? null;
    $finlimit = $_GET['finlimit'] ?? null;
    $filtro = $_GET['filtro'] ?? null;
    $en = $_GET['en'] ?? null;

    $respuesta = new get_controlador();
    
    /*=======================================================
     Peticiones GET con filtro (where)
    =======================================================*/
    if (isset($_GET['enlace']) && isset($_GET['igual']) && !isset($_GET['rel']) && !isset($_GET['tipo'])) {
        $respuesta -> obtener_datos_filtro($tabla, $seleccion, $_GET['enlace'], $_GET['igual'], $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);
    
    /*=======================================================
     Peticiones GET sin filtro (where) entre tablas relacionadas
    =======================================================*/
    }else if(isset($_GET['rel']) && isset($_GET['tipo']) && $table == "relaciones" && !isset($_GET['enlace']) && !isset($_GET['igual'])){

        $respuesta -> obtener_rel_datos($_GET['rel'], $_GET['tipo'], $seleccion, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);

    /*=======================================================
     Peticiones GET con filtro (where) entre tablas relacionadas
    =======================================================*/
    }else if(isset($_GET['rel']) && isset($_GET['tipo']) && $table == "relaciones" && isset($_GET['enlace']) && isset($_GET['igual'])){

        $respuesta -> obtener_rel_datos_filtro($_GET['rel'], $_GET['tipo'], $seleccion, $_GET['enlace'], $_GET['igual'], $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);

    /*=======================================================
     Peticiones GET para el buscador sin relaciones
    =======================================================*/
    }else if(!isset($_GET['rel']) && !isset($_GET['tipo']) && isset($_GET['enlace']) && isset($_GET['buscar'])){
        $respuesta -> obtener_datos_buscador($tabla, $seleccion, $_GET['enlace'], $_GET['buscar'], $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);
    
    /*=======================================================
     Peticiones GET para el buscador con relaciones
    =======================================================*/
    }else if(isset($_GET['rel']) && isset($_GET['tipo']) && $table == "relaciones" && isset($_GET['enlace']) && isset($_GET['buscar'])){
    
        $respuesta -> obtener_rel_datos_buscador($_GET['rel'], $_GET['tipo'], $seleccion, $_GET['enlace'], $_GET['buscar'], $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);

    /*=======================================================
     Peticiones GET con rangos
    =======================================================*/
    }else if(!isset($_GET['rel']) && !isset($_GET['tipo']) && isset($_GET['enlace']) && isset($_GET['desde']) && isset($_GET['hasta'])){
        $respuesta -> obtener_datos_rango($tabla, $seleccion, $_GET['enlace'], $_GET['desde'], $_GET['hasta'], $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit, $filtro, $en);

    /*=======================================================
     Peticiones GET con rangos en tablas relacionadas
    =======================================================*/
    }else if(isset($_GET['rel']) && isset($_GET['tipo']) && $table == "relaciones" && isset($_GET['enlace']) && isset($_GET['desde']) && isset($_GET['hasta'])){

        $respuesta -> obtener_rel_datos_rango($_GET['rel'], $_GET['tipo'], $seleccion, $_GET['enlace'], $_GET['desde'], $_GET['hasta'], $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit, $filtro, $en);

    /*=======================================================
     Peticiones GET sin filtro (where)
    =======================================================*/
    }else{
    
        $respuesta -> obtener_datos($tabla, $seleccion, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);
    }
    

?>