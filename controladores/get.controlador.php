<?php
    require_once "modelos/get.modelo.php";
    class get_controlador{

        /*=======================================================
        Peticiones GET sin filtro (where)
        =======================================================*/
        static public function obtener_datos($tabla, $seleccion, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            $respuesta = get_modelo::obtener_datos($tabla, $seleccion, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);
            $return = new get_controlador();
            $return -> fnc_respuesta($respuesta);
        }

        /*=======================================================
        Peticiones GET con filtro (where)
        =======================================================*/
        static public function obtener_datos_filtro($tabla, $seleccion, $enlace, $igual, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            $respuesta = get_modelo::obtener_datos_filtro($tabla, $seleccion, $enlace, $igual, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);
            $return = new get_controlador();
            $return -> fnc_respuesta($respuesta);
        }

        /*=======================================================
        Peticiones GET sin filtro (where) entre tablas relacionadas
        =======================================================*/
        static public function obtener_rel_datos($rel, $tipo, $seleccion, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            $respuesta = get_modelo::obtener_rel_datos($rel, $tipo, $seleccion, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);
            $return = new get_controlador();
            $return -> fnc_respuesta($respuesta);
        }

        /*=======================================================
        Peticiones GET con filtro (where) entre tablas relacionadas
        =======================================================*/
        static public function obtener_rel_datos_filtro($rel, $tipo, $seleccion, $enlace, $igual, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            $respuesta = get_modelo::obtener_rel_datos_filtro($rel, $tipo, $seleccion, $enlace, $igual, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);
            $return = new get_controlador();
            $return -> fnc_respuesta($respuesta);
        }

        /*=======================================================
        Peticiones GET para el buscador sin relaciones
        =======================================================*/
        static public function obtener_datos_buscador($tabla, $seleccion, $enlace, $buscar, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            $respuesta = get_modelo::obtener_datos_buscador($tabla, $seleccion, $enlace, $buscar, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);
            $return = new get_controlador();
            $return -> fnc_respuesta($respuesta);
        }

        /*=======================================================
        Peticiones GET para el buscador con relaciones
        =======================================================*/
        static public function obtener_rel_datos_buscador($rel, $tipo, $seleccion, $enlace, $buscar, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            $respuesta = get_modelo::obtener_rel_datos_buscador($rel, $tipo, $seleccion, $enlace, $buscar, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit);
            $return = new get_controlador();
            $return -> fnc_respuesta($respuesta);
        }

        /*=======================================================
        Peticiones GET con rangos
        =======================================================*/
        static public function obtener_datos_rango($tabla, $seleccion, $enlace, $desde, $hasta, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit, $filtro, $en){
            $respuesta = get_modelo::obtener_datos_rango($tabla, $seleccion, $enlace, $desde, $hasta, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit, $filtro, $en);
            $return = new get_controlador();
            $return -> fnc_respuesta($respuesta);
        }

        /*=======================================================
        Peticiones GET con rangos en tablas relacionadas
        =======================================================*/
        static public function obtener_rel_datos_rango($rel, $tipo, $seleccion, $enlace, $desde, $hasta, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit, $filtro, $en){
            $respuesta = get_modelo::obtener_rel_datos_rango($rel, $tipo, $seleccion, $enlace, $desde, $hasta, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit, $filtro, $en);
            $return = new get_controlador();
            $return -> fnc_respuesta($respuesta);
        }

        /*=======================================================
        Respuestas del controlador
        =======================================================*/
        public function fnc_respuesta($respuesta){

            if (!empty($respuesta)) {
                $json = array(
                    'estado' => 200,
                    'total' => count($respuesta),
                    'resultado' => $respuesta
                );
            }else{
                $json = array(
                    'estado' => 404,
                    'resultado' => "No encontrado.",
                    'metodo' => "GET"
                );
            }
            
            echo json_encode($json, http_response_code($json['estado']));
        }

    }

?>