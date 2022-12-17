<?php
    require_once "conexion.php";
    class post_modelo{

        /*=======================================================
        Peticion POST para crear datos de cualquier tabla)
        =======================================================*/
        static public function post_datos($tabla, $datos){

            
            //Creando sentencia
            $columnas = "";
            $parametros = "";

            foreach ($datos as $key => $value) {
                $columnas .= $key . ",";
                $parametros .= ":" . $key . ",";
            }

            $columnas = substr($columnas, 0, -1); //quitando la ultima coma
            $parametros = substr($parametros, 0, -1); //quitando la ultima coma

            $sql = "INSERT INTO $tabla($columnas) VALUES($parametros)";
            $obj_conexion = conexion::conectar();
            //Enlazando los parametros
            $stmt = $obj_conexion->prepare($sql);
            foreach ($datos as $key => $value) {
                $stmt -> bindParam(":".$key, $datos[$key], PDO::PARAM_STR);
            }

            if ($stmt -> execute()) {
                $respuesta = array(
                                "idcreado" => $obj_conexion->lastInsertId(),
                                "estado" => true,
                                "respuesta" => "Los datos se insertaron correctamente!"              
                                );
                return $respuesta;
            }else{
                return $obj_conexion->errorInfo();
            }
            
        }
        

        


    }

?>