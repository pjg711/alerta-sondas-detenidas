<?php
define('DEFAULT_CHARSET',"utf8"); //iso-8859-1     utf-8
// datos para la conexion a la base de datos 
define('SERVIDOR','localhost');
define('BASE_DATOS','zu000339_sondas');
define('USUARIO','zu000339_sondas');
define('PASSWORD','DAnu56fuso');
//
class BD
{
    public $num_registros=0;
    
    function __construct()
    {
        global $db_link;
        $this->num_registros=0;
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
        if(!$rv->execute())
        {
            return false;
        }
        if($last_id=$db_link->lastInsertId())
        {
            return $last_id;
        }
        if($rv->rowCount())
        {
            $this->num_registros=$rv->rowCount();
        }
        return true;
    }
}