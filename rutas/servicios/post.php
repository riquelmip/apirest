<?php
    require_once "modelos/conexion.php";
    require_once "controladores/post.controlador.php";

    if (isset($_POST)) {
        /*=======================================================
        Separar propiedades del arreglo
        =======================================================*/
        $columnas = array();
        foreach (array_keys($_POST) as $key => $value) {
            array_push($columnas, $value);
        }
        
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

        $respuesta = new post_controlador();

        /*=======================================================
        Peticion POST para registro de usuario
        =======================================================*/
        if (isset($_GET['registrar']) && $_GET['registrar'] == true) {
            $sufijo = $_GET['sufijo'] ?? "usuario";
            /*=======================================================
            Solicitando respuesta del controlador para crear usuario
            =======================================================*/
            
            $respuesta -> post_registro_usuario($tabla, $_POST, $sufijo);

            /*=======================================================
            Peticion POST para login de usuario
            =======================================================*/
        }else if(isset($_GET['login']) && $_GET['login'] == true){
            $sufijo = $_GET['sufijo'] ?? "usuario";
            /*=======================================================
            Solicitando respuesta del controlador para login usuario
            =======================================================*/
            
            $respuesta -> post_login_usuario($tabla, $_POST, $sufijo);

            /*=======================================================
            Peticion POST para ingresar datos a cualquier tabla
            =======================================================*/
        }else{

            /*=======================================================
            Peticion POST para usuarios 
            =======================================================*/

            if (isset($_GET["token"])) {
                    /*=======================================================
                    Peticion POST para usuarios no autorizados
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
                    Solicitando respuesta del controlador para crear datos de cualquier tabla
                    =======================================================*/
                    $respuesta -> post_datos($tabla, $_POST);

                }else{
                    /*=======================================================
                    Peticion POST para usuarios autorizados
                    =======================================================*/
                    $tabla2 = $_GET['tabla'] ?? "usuario";
                    $sufijo = $_GET['sufijo'] ?? "usuario";

                    $validar_token = conexion::validar_token($_GET["token"], $tabla2, $sufijo);
                    if ($validar_token == "ok") {
                        /*=======================================================
                        Solicitando respuesta del controlador para crear datos de cualquier tabla
                        =======================================================*/
                        
                        $respuesta -> post_datos($tabla, $_POST);
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

        
        
    }
?> 