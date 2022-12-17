<?php
    require_once "conexion.php";
    require_once "get.modelo.php";
    class put_modelo{

        /*=======================================================
        Peticion POST para editar datos de cualquier tabla
        =======================================================*/
        static public function put_datos($tabla, $datos, $id, $nombreid){

            //Validar si el id existe
            $existe_id = get_modelo::obtener_datos_filtro($tabla, $nombreid, $nombreid, $id, null, null, null, null);
            if (empty($existe_id)) {
                return null;
            }else{
                //Creando sentencia
                $sentencia_set = "";

                foreach ($datos as $key => $value) {
                    $sentencia_set .= $key." = :".$key. ",";
                }

                $sentencia_set = substr($sentencia_set, 0, -1); //quitando la ultima coma

                $sql = "UPDATE $tabla SET $sentencia_set WHERE $nombreid = :$nombreid";

                $obj_conexion = conexion::conectar();
                $stmt = $obj_conexion->prepare($sql);

                foreach ($datos as $key => $value) {
                    $stmt -> bindParam(":".$key, $datos[$key], PDO::PARAM_STR);
                }
                $stmt -> bindParam(":".$nombreid, $id, PDO::PARAM_STR);

                if ($stmt -> execute()) {
                    $respuesta = array(
                                    "ideditado" => $id,
                                    "estado" => true,
                                    "respuesta" => "Los datos se editaron correctamente!"              
                                    );
                    return $respuesta;
                }else{
                    return $obj_conexion->errorInfo();
                }
            

            }
            
        }
        

        


    }

?>