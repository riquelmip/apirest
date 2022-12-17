<?php
    require_once "modelos/delete.modelo.php";
    class delete_controlador{

        /*=======================================================
        Peticion POST para eliminar datos de cualquier tabla)
        =======================================================*/
        static public function delete_datos($tabla, $id, $nombreid){
            $respuesta = delete_modelo::delete_datos($tabla, $id, $nombreid);
            $return = new delete_controlador();
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
                    'metodo' => "DELETE"
                );
            }
            
            echo json_encode($json, http_response_code($json['estado']));
        }

        

    }

?>