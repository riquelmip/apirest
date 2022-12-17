<?php
    require_once "conexion.php";
    class get_modelo{

        /*=======================================================
        Peticiones GET sin filtro (where)
        =======================================================*/
        static public function obtener_datos($tabla, $seleccion, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            $seleccion_array = explode(",", $seleccion);
            //Validar existencia de la tabla y de las columnas
            if (empty(conexion::obtener_columnas_tabla($tabla, $seleccion_array))) {
                return null;
            }
            //Sin ordenar y sin limitar
            $sql = "SELECT $seleccion FROM $tabla";

            //Ordenar datos sin limitar
            if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit == null && $finlimit == null) {
                $sql = "SELECT $seleccion FROM $tabla ORDER BY $ordenarpor $ordenarmodo";
            }

            //Ordenar y limitar datos
            if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit != null && $finlimit != null) {
                $sql = "SELECT $seleccion FROM $tabla ORDER BY $ordenarpor $ordenarmodo LIMIT $iniciolimit, $finlimit";
            }

            //Limitar datos sin ordenar
            if ($ordenarpor == null && $ordenarmodo == null && $iniciolimit != null && $finlimit != null) {
                $sql = "SELECT $seleccion FROM $tabla LIMIT $iniciolimit, $finlimit";
            }

            $stmt = conexion::conectar()->prepare($sql);
            try {
                $stmt -> execute();
            } catch (PDOException $e) {
                return null;
            }
            return $stmt -> fetchAll(PDO::FETCH_CLASS);
        }

        /*=======================================================
        Peticiones GET con filtro (where)
        =======================================================*/
        static public function obtener_datos_filtro($tabla, $seleccion, $enlace, $igual, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            
            //Validar existencia de la tabla y de las columnas
            $enlace_array = explode(",", $enlace);
            $seleccion_array = explode(",", $seleccion);
            foreach ($enlace_array as $key => $value) {
                array_push($seleccion_array, $value);
            }
            $seleccion_array = array_unique($seleccion_array);
            
            
            if (empty(conexion::obtener_columnas_tabla($tabla, $seleccion_array))) {
                return null;
            }
            //---------------------------------------------------------------------------
            
            $igual_array = explode(",", $igual);
            $enlace_texto = "";

            if (count($enlace_array) > 1) {
                foreach ($enlace_array as $key => $value) {
                    if ($key > 0) {
                        $enlace_texto .= "AND ".$value. " = :" .$value." ";
                    }
                }
            }

            //Sin ordenar y sin limitar
            $sql = "SELECT $seleccion FROM $tabla WHERE $enlace_array[0] = :$enlace_array[0] $enlace_texto";

            //Ordenar datos sin limitar
            if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit == null && $finlimit == null) {
                $sql = "SELECT $seleccion FROM $tabla WHERE $enlace_array[0] = :$enlace_array[0] $enlace_texto ORDER BY $ordenarpor $ordenarmodo";
            }

            //Ordenar y limitar datos
            if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit != null && $finlimit != null) {
                $sql = "SELECT $seleccion FROM $tabla WHERE $enlace_array[0] = :$enlace_array[0] $enlace_texto ORDER BY $ordenarpor $ordenarmodo LIMIT $iniciolimit, $finlimit";
            }

            //Limitar datos sin ordenar
            if ($ordenarpor == null && $ordenarmodo == null && $iniciolimit != null && $finlimit != null) {
                $sql = "SELECT $seleccion FROM $tabla WHERE $enlace_array[0] = :$enlace_array[0] $enlace_texto LIMIT $iniciolimit, $finlimit";
            }

            $stmt = conexion::conectar()->prepare($sql);
            foreach ($enlace_array as $key => $value) {
                $stmt -> bindParam(":".$value, $igual_array[$key], PDO::PARAM_STR);
            }
            
            try {
                $stmt -> execute();
            } catch (PDOException $e) {
                return null;
            }
            return $stmt -> fetchAll(PDO::FETCH_CLASS);
        }

        /*=======================================================
        Peticiones GET sin filtro (where) entre tablas relacionadas
        =======================================================*/
        static public function obtener_rel_datos($rel, $tipo, $seleccion, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            $rel_array = explode(",", $rel);
            $tipo_array = explode(",", $tipo);
            $innerjoin_texto = "";

            if (count($rel_array) > 1) { //si viene mas de 1 tabla
                foreach ($rel_array as $key => $value) {
                    //Validar existencia de la tabla
                    if (empty(conexion::obtener_columnas_tabla($value, ["*"]))) {
                        return null;
                    }

                    if ($key > 0) {
                        $innerjoin_texto .= "INNER JOIN ".$value." ON ".$rel_array[0].".id_".$tipo_array[$key]."_".$tipo_array[0]." = ".$value.".id_".$tipo_array[$key];
                    }
                }
            

                //Sin ordenar y sin limitar
                $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto";

                //Ordenar datos sin limitar
                if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit == null && $finlimit == null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto ORDER BY $ordenarpor $ordenarmodo";
                }

                //Ordenar y limitar datos
                if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit != null && $finlimit != null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto ORDER BY $ordenarpor $ordenarmodo LIMIT $iniciolimit, $finlimit";
                }

                //Limitar datos sin ordenar
                if ($ordenarpor == null && $ordenarmodo == null && $iniciolimit != null && $finlimit != null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto LIMIT $iniciolimit, $finlimit";
                }

                $stmt = conexion::conectar()->prepare($sql);
                try {
                    $stmt -> execute();
                } catch (PDOException $e) {
                    return null;
                }
                
                return $stmt -> fetchAll(PDO::FETCH_CLASS);
            }else{
                return null;
            }
        }

        /*=======================================================
        Peticiones GET con filtro (where) entre tablas relacionadas
        =======================================================*/
        static public function obtener_rel_datos_filtro($rel, $tipo, $seleccion, $enlace, $igual, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            $enlace_array = explode(",", $enlace);
            

            //Organizamos los filtros
            
            $igual_array = explode(",", $igual);
            $enlace_texto = "";

            if (count($enlace_array) > 1) {
                foreach ($enlace_array as $key => $value) {
                    if ($key > 0) {
                        $enlace_texto .= "AND ".$value. " = :" .$value." ";
                    }
                }
            }

            //Organizamos las relaciones
            $rel_array = explode(",", $rel);
            $tipo_array = explode(",", $tipo);
            $innerjoin_texto = "";

            if (count($rel_array) > 1) { //si viene mas de 1 tabla
                foreach ($rel_array as $key => $value) {
                    //Validar existencia de la tabla
                    if (empty(conexion::obtener_columnas_tabla($value, ["*"]))) {
                        return null;
                    }
                    if ($key > 0) {
                        $innerjoin_texto .= "INNER JOIN ".$value." ON ".$rel_array[0].".id_".$tipo_array[$key]."_".$tipo_array[0]." = ".$value.".id_".$tipo_array[$key];
                    }
                }
            

                //Sin ordenar y sin limitar
                $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace_array[0] = :$enlace_array[0] $enlace_texto";

                //Ordenar datos sin limitar
                if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit == null && $finlimit == null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace_array[0] = :$enlace_array[0] $enlace_texto ORDER BY $ordenarpor $ordenarmodo";
                }

                //Ordenar y limitar datos
                if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit != null && $finlimit != null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace_array[0] = :$enlace_array[0] $enlace_texto ORDER BY $ordenarpor $ordenarmodo LIMIT $iniciolimit, $finlimit";
                }

                //Limitar datos sin ordenar
                if ($ordenarpor == null && $ordenarmodo == null && $iniciolimit != null && $finlimit != null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace_array[0] = :$enlace_array[0] $enlace_texto LIMIT $iniciolimit, $finlimit";
                }

                $stmt = conexion::conectar()->prepare($sql);
                foreach ($enlace_array as $key => $value) {
                    $stmt -> bindParam(":".$value, $igual_array[$key], PDO::PARAM_STR);
                }
                try {
                    $stmt -> execute();
                } catch (PDOException $e) {
                    return null;
                }
                return $stmt -> fetchAll(PDO::FETCH_CLASS);
            }else{
                return null;
            }
        }

        /*=======================================================
        Peticiones GET para el buscador sin relaciones
        =======================================================*/
        static public function obtener_datos_buscador($tabla, $seleccion, $enlace, $buscar, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            //Validar existencia de la tabla y columnas
            $enlace_array = explode(",", $enlace);
            $seleccion_array = explode(",", $seleccion);

            foreach ($enlace_array as $key => $value) {
                array_push($seleccion_array, $value);
            }
            $seleccion_array = array_unique($seleccion_array);

            if (empty(conexion::obtener_columnas_tabla($tabla, $seleccion_array))) {
                return null;
            }

            
            $buscar_array = explode(",", $buscar);
            $enlace_texto = "";

            if (count($enlace_array) > 1) {
                foreach ($enlace_array as $key => $value) {
                    if ($key > 0) {
                        $enlace_texto .= "AND ".$value. " = :" .$value." ";
                    }
                }
            }

            //Sin ordenar y sin limitar
            $sql = "SELECT $seleccion FROM $tabla WHERE $enlace_array[0] LIKE '%$buscar_array[0]%' $enlace_texto";

            //Ordenar datos sin limitar
            if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit == null && $finlimit == null) {
                $sql = "SELECT $seleccion FROM $tabla WHERE $enlace_array[0] LIKE '%$buscar_array[0]%' $enlace_texto ORDER BY $ordenarpor $ordenarmodo";
            }

            //Ordenar y limitar datos
            if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit != null && $finlimit != null) {
                $sql = "SELECT $seleccion FROM $tabla WHERE $enlace_array[0] LIKE '%$buscar_array[0]%' $enlace_texto ORDER BY $ordenarpor $ordenarmodo LIMIT $iniciolimit, $finlimit";
            }

            //Limitar datos sin ordenar
            if ($ordenarpor == null && $ordenarmodo == null && $iniciolimit != null && $finlimit != null) {
                $sql = "SELECT $seleccion FROM $tabla WHERE $enlace_array[0] LIKE '%$buscar_array[0]%' $enlace_texto LIMIT $iniciolimit, $finlimit";
            }

            $stmt = conexion::conectar()->prepare($sql);
            foreach ($enlace_array as $key => $value) {
                if ($key > 0) {
                    $stmt -> bindParam(":".$value, $buscar_array[$key], PDO::PARAM_STR);
                } 
            }
            try {
                $stmt -> execute();
            } catch (PDOException $e) {
                return null;
            }
            return $stmt -> fetchAll(PDO::FETCH_CLASS);
        }

        /*=======================================================
        Peticiones GET para el buscador con relaciones
        =======================================================*/
        static public function obtener_rel_datos_buscador($rel, $tipo, $seleccion, $enlace, $buscar, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit){
            //Validar existencia de la tabla y de las columnas
            $enlace_array = explode(",", $enlace);
           

            //---------------------------------------------------------------------------

            //Organizamos los filtros
            $buscar_array = explode(",", $buscar);
            $enlace_texto = "";

            if (count($enlace_array) > 1) {
                foreach ($enlace_array as $key => $value) {
                    //Validar existencia de la tabla
                    if (empty(conexion::obtener_columnas_tabla($value, ["*"]))) {
                        return null;
                    }
                    if ($key > 0) {
                        $enlace_texto .= "AND ".$value. " = :" .$value." ";
                    }
                }
            }

            //Organizamos las relaciones
            $rel_array = explode(",", $rel);
            $tipo_array = explode(",", $tipo);
            $innerjoin_texto = "";

            if (count($rel_array) > 1) { //si viene mas de 1 tabla
                foreach ($rel_array as $key => $value) {
                    if ($key > 0) {
                        $innerjoin_texto .= "INNER JOIN ".$value." ON ".$rel_array[0].".id_".$tipo_array[$key]."_".$tipo_array[0]." = ".$value.".id_".$tipo_array[$key];
                    }
                }
            

                //Sin ordenar y sin limitar
                $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace_array[0] LIKE '%$buscar_array[0]%' $enlace_texto";

                //Ordenar datos sin limitar
                if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit == null && $finlimit == null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace_array[0] LIKE '%$buscar_array[0]%' $enlace_texto ORDER BY $ordenarpor $ordenarmodo";
                }

                //Ordenar y limitar datos
                if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit != null && $finlimit != null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace_array[0] LIKE '%$buscar_array[0]%' $enlace_texto ORDER BY $ordenarpor $ordenarmodo LIMIT $iniciolimit, $finlimit";
                }

                //Limitar datos sin ordenar
                if ($ordenarpor == null && $ordenarmodo == null && $iniciolimit != null && $finlimit != null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace_array[0] LIKE '%$buscar_array[0]%' $enlace_texto LIMIT $iniciolimit, $finlimit";
                }

                $stmt = conexion::conectar()->prepare($sql);
                foreach ($enlace_array as $key => $value) {
                    if ($key > 0) {
                        $stmt -> bindParam(":".$value, $buscar_array[$key], PDO::PARAM_STR);
                    } 
                }
                try {
                    $stmt -> execute();
                } catch (PDOException $e) {
                    return null;
                }
                return $stmt -> fetchAll(PDO::FETCH_CLASS);
            }else{
                return null;
            }
        }

        /*=======================================================
        Peticiones GET con rangos
        =======================================================*/
        static public function obtener_datos_rango($tabla, $seleccion, $enlace, $desde, $hasta, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit, $filtro, $en){
            //Validar existencia de la tabla y de las columnas
            $enlace_array = explode(",", $enlace);

            if ($filtro != null) {
                $filtro_array = explode(",", $filtro);
            }else{
                $filtro_array = array();
            }


           
            $seleccion_array = explode(",", $seleccion);
            foreach ($enlace_array as $key => $value) {
                array_push($seleccion_array, $value);
            }
            foreach ($filtro_array as $key => $value) {
                array_push($seleccion_array, $value);
            }
            $seleccion_array = array_unique($seleccion_array);
            
            if (empty(conexion::obtener_columnas_tabla($tabla, ["*"]))) {
                return null;
            }
            //---------------------------------------------------------------------------

            $filtro_texto = "";
            if ($filtro != null && $en != null) {
                $filtro_texto = 'AND '.$filtro.' IN ('.$en.')';
            }
            //Sin ordenar y sin limitar
            $sql = "SELECT $seleccion FROM $tabla WHERE $enlace BETWEEN '$desde' AND '$hasta' $filtro_texto";

            //Ordenar datos sin limitar
            if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit == null && $finlimit == null) {
                $sql = "SELECT $seleccion FROM $tabla WHERE $enlace BETWEEN '$desde' AND '$hasta' $filtro_texto ORDER BY $ordenarpor $ordenarmodo";
            }

            //Ordenar y limitar datos
            if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit != null && $finlimit != null) {
                $sql = "SELECT $seleccion FROM $tabla WHERE $enlace BETWEEN '$desde' AND '$hasta' $filtro_texto ORDER BY $ordenarpor $ordenarmodo LIMIT $iniciolimit, $finlimit";
            }

            //Limitar datos sin ordenar
            if ($ordenarpor == null && $ordenarmodo == null && $iniciolimit != null && $finlimit != null) {
                $sql = "SELECT $seleccion FROM $tabla WHERE $enlace BETWEEN '$desde' AND '$hasta' $filtro_texto LIMIT $iniciolimit, $finlimit";
            }

            $stmt = conexion::conectar()->prepare($sql);
            try {
                $stmt -> execute();
            } catch (PDOException $e) {
                return null;
            }
            return $stmt -> fetchAll(PDO::FETCH_CLASS);
        }

         /*=======================================================
        Peticiones GET con rangos en tablas relacionadas
        =======================================================*/
        static public function obtener_rel_datos_rango($rel, $tipo, $seleccion, $enlace, $desde, $hasta, $ordenarpor, $ordenarmodo, $iniciolimit, $finlimit, $filtro, $en){
             //Validar existencia de la tabla y de las columnas
             $enlace_array = explode(",", $enlace);
            

             //---------------------------------------------------------------------------

            $filtro_texto = "";
            if ($filtro != null && $en != null) {
                $filtro_texto = 'AND '.$filtro.' IN ('.$en.')';
            }


            $rel_array = explode(",", $rel);
            $tipo_array = explode(",", $tipo);
            $innerjoin_texto = "";

            if (count($rel_array) > 1) { //si viene mas de 1 tabla
                foreach ($rel_array as $key => $value) {
                    //Validar existencia de la tabla
                    if (empty(conexion::obtener_columnas_tabla($value, ["*"]))) {
                        return null;
                    }
                    if ($key > 0) {
                        $innerjoin_texto .= "INNER JOIN ".$value." ON ".$rel_array[0].".id_".$tipo_array[$key]."_".$tipo_array[0]." = ".$value.".id_".$tipo_array[$key];
                    }
                }
           
            
                
                //Sin ordenar y sin limitar
                $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace BETWEEN '$desde' AND '$hasta' $filtro_texto";

                //Ordenar datos sin limitar
                if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit == null && $finlimit == null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace BETWEEN '$desde' AND '$hasta' $filtro_texto ORDER BY $ordenarpor $ordenarmodo";
                }

                //Ordenar y limitar datos
                if ($ordenarpor != null && $ordenarmodo != null && $iniciolimit != null && $finlimit != null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace BETWEEN '$desde' AND '$hasta' $filtro_texto ORDER BY $ordenarpor $ordenarmodo LIMIT $iniciolimit, $finlimit";
                }

                //Limitar datos sin ordenar
                if ($ordenarpor == null && $ordenarmodo == null && $iniciolimit != null && $finlimit != null) {
                    $sql = "SELECT $seleccion FROM $rel_array[0] $innerjoin_texto WHERE $enlace BETWEEN '$desde' AND '$hasta' $filtro_texto LIMIT $iniciolimit, $finlimit";
                }

                $stmt = conexion::conectar()->prepare($sql);
                try {
                    $stmt -> execute();
                } catch (PDOException $e) {
                    return null;
                }
                return $stmt -> fetchAll(PDO::FETCH_CLASS);
            }else{
                return null;
            }
        }


    }

?>