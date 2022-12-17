<?php
    require_once "modelos/put.modelo.php";
    class put_controlador{

        /*=======================================================
        Peticion POST para editar datos de cualquier tabla)
        =======================================================*/
        static public function put_datos($tabla, $datos, $id, $nombreid){
            $respuesta = put_modelo::put_datos($tabla, $datos, $id, $nombreid);
            $return = new put_controlador();
            $return -> fnc_respuesta($respuesta);
        }

        /*=======================================================
        Respuestas del controlador
        =======================================================*/
        public function fnc_respuesta($respuesta){

            if (!empty($respuesta)) {
                $json = array(
                    'estado' => 200,
                    'resultado' => $respuesta
                );
            }else{
                $json = array(
                    'estado' => 404,
                    'resultado' => "No encontrado.",
                    'metodo' => "PUT"
                );
            }
            
            echo json_encode($json, http_response_code($json['estado']));
        }

        

    }

?>