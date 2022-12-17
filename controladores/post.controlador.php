<?php
    require_once "modelos/get.modelo.php";
    require_once "modelos/post.modelo.php";
    require_once "modelos/put.modelo.php";
    require_once "modelos/conexion.php";
    require_once "vendor/autoload.php";
    use Firebase\JWT\JWT;

    class post_controlador{

        /*=======================================================
        Peticion POST para crear datos de cualquier tabla)
        =======================================================*/
        static public function post_datos($tabla, $datos){
            $respuesta = post_modelo::post_datos($tabla, $datos);
            $return = new post_controlador();
            $return -> fnc_respuesta($respuesta, null, null);
        }


        /*=======================================================
        Peticion POST para registrar usuarios
        =======================================================*/
        static public function post_registro_usuario($tabla, $datos, $sufijo){
            //print_r($datos);return;
            if (isset($datos["password_".$sufijo]) && $datos["password_".$sufijo] != null) {
                $encriptar = crypt($datos['password_'.$sufijo], '$2a$07$azybxcagsrp23425rpazybxcags098$'); //CRYPT_BLOWFISH
                $datos['password_'.$sufijo] = $encriptar;
                $respuesta = post_modelo::post_datos($tabla, $datos);
                $return = new post_controlador();
                $return -> fnc_respuesta($respuesta, null, $sufijo);
            }else{
                /*=======================================================
                Peticion POST para registrar usuarios desde APPS externas
                =======================================================*/
                $respuesta = post_modelo::post_datos($tabla, $datos);
                
                if (isset($respuesta['estado']) && $respuesta['estado'] == true){
                     //Validamos que el usuario exista
                    $existe_usuario = get_modelo::obtener_datos_filtro($tabla, "*", "email_".$sufijo, $datos['email_'.$sufijo], null, null, null, null);
                    
                    if (!empty($existe_usuario)) {
                        //Creamos el token
                        $token = conexion::token_jwt($existe_usuario[0]->{"id_".$sufijo}, $existe_usuario[0]->{"email_".$sufijo});
                        //generamos el token JWT
                        $llave = 'mi_contrasena_secreta_es_9101210381';
                        $jwt = JWT::encode($token, $llave, "HS256");
                        //Actualizamos la base de datos con el token
                        $datos_token = array(
                            "token_".$sufijo => $jwt,
                            "token_exp_".$sufijo => $token["exp"]
                        );

                        $actualizar_token = put_modelo::put_datos($tabla, $datos_token, $existe_usuario[0]->{"id_".$sufijo}, "id_".$sufijo);
                        

                        if (isset($actualizar_token['estado']) && $actualizar_token['estado'] == true) {
                            
                            $existe_usuario[0]->{"token_".$sufijo} = $jwt;
                            $existe_usuario[0]->{"token_exp_".$sufijo} = $token["exp"];
                            

                            $return = new post_controlador();
                            $return -> fnc_respuesta($existe_usuario, null, $sufijo);
                        }
                    }
                    
                }

            }
            
        }

        /*=======================================================
        Peticion POST para login de usuarios
        =======================================================*/
        static public function post_login_usuario($tabla, $datos, $sufijo){
            //Validamos que el usuario exista
            $existe_usuario = get_modelo::obtener_datos_filtro($tabla, "*", "email_".$sufijo, $datos['email_'.$sufijo], null, null, null, null);
            if (!empty($existe_usuario)) {
                if ($existe_usuario[0]->{"password_".$sufijo} != null) {
                    //encriptamos la contraseña
                    $encriptar = crypt($datos['password_'.$sufijo], '$2a$07$azybxcagsrp23425rpazybxcags098$'); //CRYPT_BLOWFISH

                    //Si la contraseña es correcta
                    if ($existe_usuario[0]->{"password_".$sufijo} == $encriptar) {
                        //Creamos el token
                        $token = conexion::token_jwt($existe_usuario[0]->{"id_".$sufijo}, $existe_usuario[0]->{"email_".$sufijo});
                        //generamos el token JWT
                        $llave = 'mi_contrasena_secreta_es_9101210381';
                        $jwt = JWT::encode($token, $llave, "HS256");
                        //Actualizamos la base de datos con el token
                        $datos_token = array(
                            "token_".$sufijo => $jwt,
                            "token_exp_".$sufijo => $token["exp"]
                        );

                        $actualizar_token = put_modelo::put_datos($tabla, $datos_token, $existe_usuario[0]->{"id_".$sufijo}, "id_".$sufijo);

                        if (isset($actualizar_token['estado']) && $actualizar_token['estado'] == true) {
                            $existe_usuario[0]->{"token_".$sufijo} = $jwt;
                            $existe_usuario[0]->{"token_exp_".$sufijo} = $token["exp"];

                            $return = new post_controlador();
                            $return -> fnc_respuesta($existe_usuario, null, $sufijo);
                        }
                    }else{
                        $respuesta = null;
                        $return = new post_controlador();
                        $return -> fnc_respuesta($respuesta, "Error: La contraseña es incorrecta", $sufijo);
                    }

                }else{
                    //Actualizamos el token para usuarios logueados con apps externas
                    $token = conexion::token_jwt($existe_usuario[0]->{"id_".$sufijo}, $existe_usuario[0]->{"email_".$sufijo});
                    //generamos el token JWT
                    $llave = 'mi_contrasena_secreta_es_9101210381';
                    $jwt = JWT::encode($token, $llave, "HS256");
                    //Actualizamos la base de datos con el token
                    $datos_token = array(
                        "token_".$sufijo => $jwt,
                        "token_exp_".$sufijo => $token["exp"]
                    );

                    $actualizar_token = put_modelo::put_datos($tabla, $datos_token, $existe_usuario[0]->{"id_".$sufijo}, "id_".$sufijo);

                    if (isset($actualizar_token['estado']) && $actualizar_token['estado'] == true) {
                        $existe_usuario[0]->{"token_".$sufijo} = $jwt;
                        $existe_usuario[0]->{"token_exp_".$sufijo} = $token["exp"];
                        
                        $return = new post_controlador();
                        $return -> fnc_respuesta($existe_usuario, null, $sufijo);
                        
                    }
                }
                
            }else{
                $respuesta = null;
                $return = new post_controlador();
                $return -> fnc_respuesta($respuesta, "Error: No existe el email", $sufijo);
            }
            
        }


        /*=======================================================
        Respuestas del controlador
        =======================================================*/
        public function fnc_respuesta($respuesta, $error, $sufijo){

            if (!empty($respuesta)) {
                //quitamos la contraseña de la respuesta
                
                if (isset($respuesta[0]->{"password_".$sufijo})) {
                    
                    unset($respuesta[0]->{"password_".$sufijo});
                    
                }
                $json = array(
                    'estado' => 200,
                    'resultado' => $respuesta
                );
            }else{
                if ($error != null) {
                    $json = array(
                        'estado' => 400,
                        'resultado' => $error
                    );
                }else{
                    $json = array(
                        'estado' => 404,
                        'resultado' => "No encontrado.",
                        'metodo' => "POST"
                    );
                }
                
            }
            
            echo json_encode($json, http_response_code($json['estado']));
        }

        

    }

?>