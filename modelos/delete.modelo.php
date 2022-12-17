<?php
    require_once "conexion.php";
    require_once "get.modelo.php";
    class delete_modelo{

        /*=======================================================
        Peticion POST para editar datos de cualquier tabla
        =======================================================*/
        static public function delete_datos($tabla, $id, $nombreid){

            //Validar si el id existe
            $existe_id = get_modelo::obtener_datos_filtro($tabla, $nombreid, $nombreid, $id, null, null, null, null);
            if (empty($existe_id)) {
                return null;
            }else{
                //Creando sentencia

                $sql = "DELETE FROM $tabla WHERE $nombreid = :$nombreid";

                $obj_conexion = conexion::conectar();
                $stmt = $obj_conexion->prepare($sql);
                $stmt -> bindParam(":".$nombreid, $id, PDO::PARAM_STR);

                if ($stmt -> execute()) {
                    $respuesta = array(
                                    "ideliminado" => $id,
                                    "respuesta" => "Los datos se eliminaron correctamente!"              
                                    );
                    return $respuesta;
                }else{
                    return $obj_conexion->errorInfo();
                }
            

            }
            
        }
        

        


    }

?>