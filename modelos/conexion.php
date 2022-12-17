<?php
    require_once "get.modelo.php";

    class conexion{

        /*=======================================================
        Informacion de la base de datos
        =======================================================*/
        static public function informacion_db(){
            $infoDB = array(
                            "host" => "localhost",
                            "database" => "apirest_auth",
                            "user" => "root",
                            "pass" => "",
                            "charset" => "utf8"
                        );
            return $infoDB;
        }

        /*=======================================================
        API KEY
        =======================================================*/
        static public function apikey(){
            //La api key es una cadena entre numeros y letras de 30 caracteres
            return "zFKx44EAt44NXx6gZBHkLqTrAzeBKS";
        }

        /*=======================================================
        Acceso publico a tablas
        =======================================================*/
        static public function acceso_publico(){
            //$tablas = ["usuarios", "roles"]; //listado de tablas que seran publicas
            $tablas = [];
            return $tablas;
        }

        /*=======================================================
        Conexion a la base de datos
        =======================================================*/
        static public function conectar(){
            $cadena_conexion = "mysql:host=".conexion::informacion_db()["host"].";dbname=".conexion::informacion_db()["database"].";charset=".conexion::informacion_db()["charset"];
            try{
                $link = new PDO($cadena_conexion, conexion::informacion_db()["user"], conexion::informacion_db()["pass"]);
                $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //echo "conexión exitosa";
            }catch(PDOException $e){
                die("Error: ".$e->getMessage());
            }
            return $link;
        }

        /*=======================================================
        Validar existencia de una tabla en la base de datos
        =======================================================*/
        static public function obtener_columnas_tabla($tabla, $columnas){
            
            $basededatos = conexion::informacion_db()['database']; //obtenemos el nombre de la base
            //tareamos las columnas de la tabla si existe
            $validar_tabla = conexion::conectar()
                            ->query("SELECT COLUMN_NAME AS obj FROM information_schema.columns WHERE table_schema = '$basededatos' AND table_name = '$tabla'")
                            ->fetchAll(PDO::FETCH_OBJ);
            //validamos si la tabla existe
            if (empty($validar_tabla)) {
                return null;
            }else{
                //si el primer indice trae un * quiere decir que la peticion es de columnas globales
                
                if ($columnas[0] == "*") {
                    array_shift($columnas); //quitamos el asterisco
                }
                //validamos si las columnas existen
                $sumatoria_columnas = 0;
                foreach ($validar_tabla as $key => $value) {
                    $sumatoria_columnas += in_array($value->obj, $columnas);
                }

                return $sumatoria_columnas == count($columnas) ? $validar_tabla : null;
            }
        }

        /*=======================================================
        Generar token de auntenticacion
        =======================================================*/
        static public function token_jwt($id, $email){
            $tiempo = time();
            
            //Creando array del token
            $token = array(
                        "iat" => $tiempo, //Tiempo en que inicia el token
                        "exp" => $tiempo + (60*60*24), //Tiempo de expiracion del token 1 dia (60s*60m*24h)
                        "data" => [
                                    "id" => $id,
                                    "email" => $email
                                  ]
                        );

            

           //JWT::decode($jwt, $llave, array('HS256'));
            return $token;

        }

         /*=======================================================
        Validar token de seguridad
        =======================================================*/
        static public function validar_token($token, $tabla, $sufijo){
            /*=======================================================
            Traemos el usuario de acuerdo al token
            =======================================================*/
            $usuario = get_modelo::obtener_datos_filtro($tabla, "token_exp_".$sufijo, "token_".$sufijo, $token, null,null, null, null);
            if(!empty($usuario)){
                /*=======================================================
                Validamos que el token no haya expirado
                =======================================================*/
                $tiempo_actual = time();
                if ($tiempo_actual < $usuario[0]->{'token_exp_'.$sufijo}) {
                    return "ok";
                }else{
                    return "expirado";
                }
            }else{
                return "no_auth";
            }
           

        }

        


    }

?>