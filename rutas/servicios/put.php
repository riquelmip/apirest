<?php
    require_once "modelos/conexion.php";
    require_once "controladores/put.controlador.php";

    if (isset($_GET['id']) && isset($_GET['nombreid'])) {
        $datos = array();
        parse_str(file_get_contents('php://input'), $datos);
        //print_r($datos);return;

        /*=======================================================
        Separar propiedades del arreglo
        =======================================================*/
        $columnas = array();
        foreach (array_keys($datos) as $key => $value) {
            array_push($columnas, $value);
        }
        array_push($columnas, $_GET['nombreid']);
        $columnas = array_unique($columnas);
        /*=======================================================
        Validar tabla y columnas
        =======================================================*/
        if (empty(conexion::obtener_columnas_tabla($tabla, $columnas))) {
            $json = array(
                'estado' => 404,
                'resultado' => "Error: los campos en el formulario no coinciden con los de la base de datos."
            );

            echo json_encode($json, http_response_code($json['estado']));
            return;
        }

            /*=======================================================
            Peticion PUT para usuarios 
            =======================================================*/

            if (isset($_GET["token"])) {
                /*=======================================================
                Peticion PUT para usuarios no autorizados
                =======================================================*/
                if (isset($_GET["token"]) == "no" && isset($_GET["excepcion"])) {
                    /*=======================================================
                    Validar tabla y columnas
                    =======================================================*/
                    $columnas_excepcion = array($_GET["excepcion"]);
                    if (empty(conexion::obtener_columnas_tabla($tabla, $columnas_excepcion))) {
                        $json = array(
                            'estado' => 404,
                            'resultado' => "Error: los campos en el formulario no coinciden con los de la base de datos."
                        );

                        echo json_encode($json, http_response_code($json['estado']));
                        return;
                    }
                    /*=======================================================
                    Solicitando respuesta del controlador para editar datos de cualquier tabla
                    =======================================================*/
                    $respuesta = new put_controlador();
                    $respuesta -> put_datos($tabla, $datos, $_GET['id'], $_GET['nombreid']);

                }else{
                    /*=======================================================
                    Peticion PUT para usuarios autorizados
                    =======================================================*/
                    $tabla2 = $_GET['tabla'] ?? "usuario";
                    $sufijo = $_GET['sufijo'] ?? "usuario";

                    $validar_token = conexion::validar_token($_GET["token"], $tabla2, $sufijo);
                    if ($validar_token == "ok") {
                        /*=======================================================
                        Solicitando respuesta del controlador para editar datos de cualquier tabla
                        =======================================================*/
                        $respuesta = new put_controlador();
                        $respuesta -> put_datos($tabla, $datos, $_GET['id'], $_GET['nombreid']);
                    }

                    /*=======================================================
                    ERROR cuando el token ha expirado
                    =======================================================*/
                    if ($validar_token == "expirado"){
                        $json = array(
                            'estado' => 303,
                            'resultado' => "Error: El token está expirado."
                        );
            
                        echo json_encode($json, http_response_code($json['estado']));
                        return;
                    }

                    /*=======================================================
                    ERROR cuando el token no coincide en bd
                    =======================================================*/
                    if ($validar_token == "no_auth"){
                        $json = array(
                            'estado' => 400,
                            'resultado' => "Error: El token no está autorizado para hacer solicitud."
                        );
            
                        echo json_encode($json, http_response_code($json['estado']));
                        return;
                    }

                }
                
                
            /*=======================================================
            ERROR cuando se solicita token para la accion
            =======================================================*/
            }else{
                $json = array(
                    'estado' => 400,
                    'resultado' => "Error: Autorización requerida."
                );
    
                echo json_encode($json, http_response_code($json['estado']));
                return;
            }
        
        
        
    }
?> 