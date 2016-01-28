<?php
define('DEFAULT_CHARSET',"utf8"); //iso-8859-1     utf-8
// datos para la conexion a la base de datos 
define('SERVIDOR','localhost');
define('BASE_DATOS','alerta_sondas');
define('USUARIO','alerta');
define('PASSWORD','alertasondas932');
//
class BD
{
    function __construct($id=NULL)
    {
        global $db_link;
        if(!($db_link = new PDO('mysql:host='.SERVIDOR.';dbname='.BASE_DATOS.';charset=utf8',USUARIO,PASSWORD)))
        {
            echo "No es posible conectar con la base de datos ".$base_datos."<br>";
            return true;
        }
    }
    public function sql_select($query, &$rv)
    {
        global $db_link;
        $query=preg_replace("/\r\n|\r/", chr(32), $query);
        if (DEFAULT_CHARSET == "utf8" OR DEFAULT_CHARSET == "utf-8")
        {
            $db_link->query("SET NAMES 'utf8'");
        }
        $rv = $db_link->prepare($query);
        if (!$rv->execute())
        {
            return false;
        }
        if($last_id=$db_link->lastInsertId())
        {
            return $last_id;
        }
        return true;
    }
    public function getEnumOptions($table, $field) 
    {
        global $db_link;
        $finalResult = array(); 
        if(strlen(trim($table)) < 1) return false; 
        $query = "show columns from $table";
        //$result = mysql_query($query);
        $this->sql_select($query, $consulta);
        //while ($row = mysql_fetch_array($result))
        while($row = $consulta->fetch(PDO::FETCH_ASSOC))
        { 
            if($field != $row["Field"]) continue;
            //check if enum type 
            //if(ereg('enum.(.*).', $row['Type'], $match))
            if(preg_match('/enum.(.*)./', $row['Type'], $match))
            { 
                $opts = explode(',', $match[1]); 
                foreach ($opts as $item) 
                    $finalResult[] = substr($item, 1, strlen($item)-2); 
            } 
            else 
                return false;
        }
        return $finalResult; 
    }     
    /*
    public function insertar_usuario($usuario,$password)
    {
        $sql="INSERT INTO `usuarios` ("
    }
     * 
     */
    /*
    public function insertar_informe($informe,$id_usuario)
    {
        $sql="INSERT INTO `informes` (`id_usuario`,`informe`,`fecha`) VALUES (".$id_usuario.",'".$informe."',".now().")";
        if($this->sql_select($sql,$consulta))
        {
            
        }
    }
     * 
     */
}