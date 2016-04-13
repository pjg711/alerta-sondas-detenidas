<?php

/**
 * Description of class_reports
 *
 * @author pablo
 */
class Reports 
{
    private $id;
    private $userid;
    private $informe;
    private $fecha;
    /**
     * 
     * @param type $id
     * @param type $userid
     * @param type $informe
     * @param type $fecha
     */
    public function __construct($id='',$userid='',$informe='',$fecha='')
    {
        $this->id=$id;
        $this->userid=$userid;
        $this->informe=$informe;
        $this->fecha=$fecha;
    }
    
    /**
     * Funcion para armar el xml con la info de las sondas
     * @param array $sondas
     * @return boolean
     */
    private function analizo_sondas(Array $sondas)
    {
        if(!is_array($sondas)) 
        {
            echo "ERROR! sondas no es un array.\n";
            return false;
        }
        $cadena="";
        $q_sondas_cantidad=array();
        $q_sondas_comunicacion=array();
        $archivo_comunicacion=array();
        foreach($sondas as $key => $sonda)
        {
            if(isset($sonda["type"]))
            {
                if($sonda["type"]=="file")
                {
                    $partes=explode("-",$key);
                    if(substr($key,-4)==".txt")
                    {
                        // archivo con informacion de sonda detenida ya estan descargados en carpeta temp
                        if(count($partes)==3)
                        {
                            //fecha es AAMMDD
                            $anio=2000+intval(substr($partes[1],0,2));
                            $mes=intval(substr($partes[1],2,2));
                            $dia=intval(substr($partes[1],-2));
                            //hora es HHMMSS
                            $hora=intval(substr($partes[2],0,2));
                            $minu=intval(substr($partes[2],2,2));
                            $segu=intval(substr($partes[2],4,2));
                            //
                            $fecha=mktime($hora,$minu,$segu,$mes,$dia,$anio);
                            //
                            $archivo_comunicacion=array("fecha"=>date("r",$fecha),"mkfecha"=>$fecha,"archivo"=>$key);
                            $q_sondas_comunicacion[$partes[0]][]=$archivo_comunicacion;
                            sort($q_sondas_comunicacion[$partes[0]]);
                        }
                    }
                    if(substr($key,-4)==".esp")
                    {
                        if(count($partes)==4)
                        {   
                            // es sonda
                            $agrego=array("archivo"=>$key,"sonda"=>$partes[0],"fecha"=>$partes[2]);
                            $sonda=array_merge($sonda,$agrego);
                            //$partes[0] contiene el nombre de la sonda
                            $q_sondas[$partes[0]][]=$sonda;
                            if(!isset($q_sondas_cantidad[$partes[0]])) $q_sondas_cantidad[$partes[0]]=0;
                            $q_sondas_cantidad[$partes[0]]++;
                        }
                    }
                }
            }
        }
        $cadena.="<?xml version=\"1.0\" encoding=\"UTF-8\"?><sondas><cantidad_sondas>".count($q_sondas_cantidad)."</cantidad_sondas>";
        $sonda_fuera=0;
        foreach($q_sondas_cantidad as $key => $cantidad)
        {
            //$key contiene el nombre de la sonda
            $cadena.="<sonda>";
            // fuera de fecha?
            $contenido_archivo_comunicacion2="";
            $fecha_comunicacion2="";
            if($this->fecha_vencida($q_sondas[$key][count($q_sondas[$key])-1]))
            {
                $sonda_fuera++;
                $cadena.="<fuera_fecha>Si</fuera_fecha>";
                // busco archivo txt
                if(isset($q_sondas_comunicacion[$key]))
                {
                    // hay archivo txt
                    $archivo_comunicacion2=$q_sondas_comunicacion[$key][count($q_sondas_comunicacion[$key])-1]['archivo'];
                    $fecha_comunicacion2=$q_sondas_comunicacion[$key][count($q_sondas_comunicacion[$key])-1]['mkfecha'];
                    if(file_exists("temp/".$archivo_comunicacion2))
                    {
                        $contenido_archivo_comunicacion2=$archivo_comunicacion2;
                    }
                }
            }else
            {
                $cadena.="<fuera_fecha>No</fuera_fecha>";
            }
            $cadena.="<nombre>{$key}</nombre>";
            $cadena.="<nro_archivos>{$cantidad}</nro_archivos>";
            $cadena.="<ultima_fecha>{$q_sondas[$key][count($q_sondas[$key])-1]['fecha']}</ultima_fecha>";
            $cadena.="<mas_info>{$contenido_archivo_comunicacion2}</mas_info>";
            $cadena.="<fecha_mas_info>{$fecha_comunicacion2}</fecha_mas_info>";
            $cadena.="</sonda>";
        }
        $cadena.="<cantidad_sondas_fuera_fecha>{$sonda_fuera}</cantidad_sondas_fuera_fecha></sondas>";
        return trim($cadena);
    }
    /**
     * 
     */
    public static function getAll()
    {
        $query=" 
            SELECT  reports.`id` AS id_informe,
                    reports.`informe` AS informe,
                    reports.`fecha` AS fecha,
                    users.`id` AS userid,
                    users.`enable_user` AS activo,
                    users.`create_time` AS create_time,
                    users.`username` AS usuario,
                    users.`password` AS password,
                    users.`server` AS servidor,
                    users.`remotedir` AS directorio_remoto,
                    users.`is_admin` AS is_admin,
                    users.`usertype` AS tipo_usuario,
                    users.`mails` AS mails
            FROM    `informes` AS reports,
                    `users` AS users
            WHERE   reports.`userid`={$userid}
            ORDER BY `fecha` DESC";
        
    }
    /**
     * 
     * @param type $userid
     * @return \Reports|boolean
     */
    public static function load($userid=0)
    {
        if($userid==0) return false;
        $query=" 
            SELECT  reports.`id` AS id_informe,
                    reports.`informe` AS informe,
                    reports.`fecha` AS fecha,
                    users.`id` AS userid,
                    users.`enable_user` AS activo,
                    users.`create_time` AS create_time,
                    users.`username` AS usuario,
                    users.`password` AS password,
                    users.`server` AS servidor,
                    users.`remotedir` AS directorio_remoto,
                    users.`is_admin` AS is_admin,
                    users.`usertype` AS tipo_usuario,
                    users.`mails` AS mails
            FROM    `reports` AS reports,
                    `users` AS users
            WHERE   reports.`userid`={$userid}
            ORDER BY `fecha` DESC";
        settype($response, 'array');
        if(sql_select($query, $results))
        {
            while($row = $results->fetch(PDO::FETCH_ASSOC))
            {
                $n_report = new Reports($row['id'],$row['userid'],$row['informe'],$row['fecha']);
                $response[$row['userid']] = $n_report;
            }
            return $response;
        }
        return false;
    }
    /**
     * 
     * @param type $userid
     */
    public function listado_informes($userid=0)
    {
        $query_select="  
            SELECT  informes.`id` AS id_informe,
                    informes.`informe` AS informe,
                    informes.`fecha` AS fecha,
                    usuarios.`id` AS userid,
                    usuarios.`enable_user` AS activo,
                    usuarios.`create_time` AS create_time,
                    usuarios.`username` AS usuario,
                    usuarios.`password` AS password,
                    usuarios.`server` AS servidor,
                    usuarios.`remotedir` AS directorio_remoto,
                    usuarios.`is_admin` AS is_admin,
                    usuarios.`usertype` AS tipo_usuario,
                    usuarios.`mails` AS mails";
        if($userid==0)
        {
            // es admin y muestro todos los informes
            $query_demas="
                FROM    `informes` AS informes, 
                        `users` AS usuarios
                WHERE   informes.`userid`=usuarios.`id`
                ORDER BY `fecha` DESC";
        }else
        {
            $query_demas="
                FROM    `informes` AS informes,
                        `users` AS usuarios
                WHERE   informes.`userid`={$userid}
                ORDER BY `fecha` DESC";
        }
        $query="$query_select $query_demas";
        // muestro tabla con informes para el usuario logeado
        $informes=array();
        if(sql_select($query, $results))
        {
            while($registro = $results->fetch(PDO::FETCH_ASSOC))
            {
                $informes[]=$registro;
            }
            if(!is_null($informes))
            {
                echo $this->informes_sondas_detenidas($informes);
            }else
            {
                echo "No hay informes que mostrar<br>";
            }
        }
        unset($results);
    }
    /**
     * 
     * @param type $id_informe
     * @return boolean
     */
    public static function borrar_informe($id_informe=0)
    {
        if($id_informe==0) return false;
        $query="DELETE FROM `informes`
                WHERE `id`={$id_informe}";
        if(!sql_select($query, $results))
        {
            return false;
        }
        unset($results);
        return true;
    }
    /**
     * 
     * @param type $userid
     * @return boolean
     */
    public static function borrar_informes_todos($userid=0)
    {
        if($userid) return false;
        $query="  DELETE FROM `informes` 
                WHERE `userid`={$userid}";
        if(!sql_select($query, $results))
        {
            return false;
        }
        unset($results);
        return true;
    }
    /*
     * Informes sondas detenidas
     * 
     */
    public function informes_sondas_detenidas($informes)
    {
        // para todos los informes
        $cadena= "
            <br><br><br>
            <h1>Listado de informes de sondas detenidas</h1>
            <table class=\"table table-striped table-hover table-bordered table-condensed\">
                <tr>
                    <th class=\"text-right\">
                        <a class=\"link-tabla\" href=\"javascript:borrar_todos();\" title=\"Borrar todos\">
                            <i class=\"fa fa-trash\"></i>&nbsp;&nbsp;&nbsp;
                        </a>
                    </th>";
        if($_SESSION['is_admin'])
        {
            $cadena.="  <th>Usuario</th>";
        }
        $cadena.="      <th>Fecha</th>
                </tr>";    
        foreach($informes as $informe)
        {
            $cadena.="
                <tr>
                    <td align=\"right\">
                        <a class=\"link-tabla\" href=\"javascript:mostrar_ocultar('informe_{$informe['id_informe']}');\" title=\"Ver informe\">
                            <i class=\"fa fa-eye\"></i>
                        </a>&nbsp;&nbsp;
                        <a class=\"link-tabla\" href=\"javascript:borrar_informe('{$informe['id_informe']}');\" title=\"Borrar informe\">
                            <i class=\"fa fa-trash-o\"></i>
                        </a>&nbsp;&nbsp;
                    </td>";
            if($_SESSION['is_admin'])
            {
                $cadena.="
                    <td>{$informe['usuario']}</td>";
            }
            $cadena.="  <td>{$informe['fecha']}</td>
                </tr>
                <tr>";
            if($_SESSION['is_admin'])
            {
                $cadena.="<td colspan=\"3\">";
            }else
            {
                $cadena.="<td colspan=\"2\">";
            }
            $cadena.="      <div id=\"informe_{$informe['id_informe']}\" style=\"display:none\">";
            if($texto_informe=$this->presento_informe(trim($informe['informe'])))
            {
                $cadena.=$texto_informe;
            }
            $cadena.="      </div>
                    </td>
                </tr>
            </table>";
        }
        return $cadena;
    }
    /**
     * 
     * @param type $xml_informe
     * @return string|boolean
     */
    private function presento_informe($xml_informe='')
    {
        //convierto xml en html
        $xml_informe2= html_entity_decode($xml_informe);
        $dom = new DOMDocument;
        $dom->loadXML($xml_informe2);
        if(!$dom)
        {
            echo 'Error en el xml';
            return false;
        }
        $s = simplexml_import_dom($dom);
        if($s->cantidad_sondas==0) return false;
        $cadena="<table id='tabla-informe'>
                <tr>
                    <th>nombre</th>
                    <th align=\"center\">nro. archivos</th>
                    <th align=\"center\">ultima fecha</th>
                    <th align=\"center\"><i class=\"fa fa-info-circle\"></i></th>
                </tr>";
        foreach($s as $sonda)
        {
            if(count($sonda)<>0)
            {
                if($sonda->fuera_fecha=='Si')
                {
                    $cadena.="<tr bgcolor=\"#D49590\">";
                }else
                {
                    $cadena.="<tr bgcolor=\"#A6D490\">";
                }
                $cadena.="  <td>{$sonda->nombre}</td>
                            <td align=\"center\">{$sonda->nro_archivos}</td>
                            <td align=\"center\">".$this->proceso_fecha($sonda->ultima_fecha)."</td>";
                if($sonda->mas_info<>"")
                {
                    $cadena.=
                        "<td align=\"center\">
                            <div style=\"display:block\">
                                <a href=\"javascript:mostrar_ocultar('sonda_{$sonda->nombre}');\" title=\"M&aacute;s informaci&oacute;n\">
                                    <i class=\"fa fa-info\"></i>
                                </a>
                            </div>
                        </td>
                    </tr>";
                    if($sonda->fuera_fecha=='Si')
                    {
                        $cadena.="<tr bgcolor=\"#D49590\">";
                    }else
                    {
                        $cadena.="<tr bgcolor=\"#A6D490\">";
                    }
                    $contenido_archivo=str_replace("\n","<br>",file_get_contents("temp/".$sonda->mas_info));
                    $cadena.="
                        <td colspan=\"4\">
                            <div id=\"sonda_{$sonda->nombre}\" style='display:none'>
                                Archivo  :{$sonda->mas_info}<br>";
                    if($sonda->fecha_mas_info<>"")
                    {
                        //$cadena.="Fecha    :".date("d-m-Y H:i:s",$sonda->fecha_mas_info)."<br>";
                        $fecha=intval($sonda->fecha_mas_info);
                        $cadena.="Fecha    :".date("d-m-Y H:i:s",$fecha)."<br>";
                    }
                    $cadena.="  Contenido:<br><hr><div id=\"contenido-txt\">{$contenido_archivo}<hr></div><br>
                            </div>
                        </td>
                    </tr>";
                }else
                {
                    $cadena.="
                        <td align=\"center\">
                            <div style=\"display:block\">
                                <a href=\"javascript:;\" title=\"Sin informaci&oacute;n\">
                                    <i class=\"fa fa-ban\"></i>
                                </a>
                            </div>                    
                        </td>";
                }
                $cadena.="</tr>";
            }
        }
        $cadena.="</table>";
        unset($dom);
        return $cadena;
    }
    /**
     * 
     * @param type $argv
     * @param type $lo_guardo
     * @return boolean
     */
    public static function hago_informes($argv,$lo_guardo=false)
    {
        if(!isset($argv[1]))
        {
            echo "ERROR! No existe el usuario.\n";
            return false;
        }
        $usuario=CCStrip($argv[1]);
        if($usuario=="todos")
        {
            $query="SELECT  *
                    FROM    `users` 
                    WHERE   `activo`=1 AND 
                            `usertype`='ftp'";
        }else
        {
            $query="SELECT  *
                    FROM    `users` 
                    WHERE   `enable_user`=1 AND 
                            `usertype`='ftp' AND 
                            `username`='{$usuario}' LIMIT 1";
        }
        if(sql_select($query, $results))
        {
            if($results->rowCount()==0)
            {
                echo "ERROR! ".$usuario." no corresponde con un usuario cargado en el sistema.\n";
            }
            while($registro = $results->fetch(PDO::FETCH_ASSOC))
            {
                hago_informe($registro,$lo_guardo);
            }
        }
        unset($results);
    }
    /**
     * Funcion principal que hace el informe de sondas detenidas
     * @param array $registro
     * @param type $lo_guardo
     */
    private function hago_informe(Array $registro, $lo_guardo=false)
    {
        $servidor=trim(utf8_decode($registro['servidor']));
        $usuario=trim(utf8_decode($registro['usuario']));
        $password=trim(utf8_decode($registro['password']));
        $directorio=trim(utf8_decode($registro['directorio_remoto']));
        $emails=explode(",",$registro['mails']);
        if($obj_ftp=new FTP($servidor,$usuario,$password,$directorio))
        {
            // hago el informe
            if($informe=$this->analizo_sondas($obj_ftp->get_listado()))
            {
                if($lo_guardo)
                {
                    // y lo guardo en la base de datos
                    $fecha_actual=date("Y-m-d H:i:s");
                    $query="INSERT INTO `reports` 
                                (`userid`,`informe`,`fecha`) 
                            VALUES 
                                ({$registro['id']},'{$informe}','{$fecha_actual}')";
                    if(sql_select($query, $results))
                    {
                        // envio mails
                        envio_emails($informe,$usuario,$fecha_actual,$emails);
                    }
                }
            }else
            {
                echo "ERROR! Hubo algún problema en la creación del informe.\n";
            }
        }
        unset($results);
    }    
}
?>