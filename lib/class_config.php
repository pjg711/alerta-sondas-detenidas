<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config_Station
 *
 * @author pablo
 */
class Config_Station
{
    private $userid;        // userid de la tabla usuarios
    private $f_station_code;    // f_station_code 
    private $activa;
    private $sensores;          // array con los sensores seleccionados
    private $periodo;           // opcion de descargar con fecha de inicio y fecha final
    private $periodo_fecha_inicial;      //
    private $periodo_fecha_final;
    private $tipo_archivo;
    private $separador;
    private $encabezado;
    private $nombre_archivo;
    //
    private $error;
    
    function __construct($userid='',$f_station_code='',$activa,$sensores='',$periodo='',
                        $periodo_fecha_inicial='',$periodo_fecha_final='',$tipo_archivo='',
                        $separador='',$encabezado='',$archivo='') 
    {
        if($sensores=='')
        {
            $this->sensores=array();
        }else
        {
            $this->sensores=$sensores;
        }
        $this->userid=$userid;
        $this->f_station_code=$f_station_code;
        $this->activa=(int)$activa;
        $this->periodo=$periodo;
        $this->periodo_fecha_inicial=$periodo_fecha_inicial;
        $this->periodo_fecha_final=$periodo_fecha_final;
        $this->tipo_archivo=$tipo_archivo;
        $this->separador=$separador;
        $this->encabezado=$encabezado;
        $this->nombre_archivo=$archivo;
    }
    public function getIdUsuario()
    {
        return $this->userid;
    }
    public function getStationCode()
    {
        return $this->f_station_code;
    }
    public function getActiva()
    {
        if($this->activa==1) return true;
        return false;
    }
    public function getPeriodo()
    {
        return $this->periodo;
    }
    public function getPeriodoFechaInicial()
    {
        return $this->periodo_fecha_inicial;
    }
    public function getPeriodoFechaFinal()
    {
        return $this->periodo_fecha_final;
    }
    public function getTipoArchivo()
    {
        return $this->tipo_archivo;
    }
    public function getSeparador()
    {
        return $this->separador;
    }
    public function getEncabezado()
    {
        return $this->encabezado;
    }
    public function getNombreArchivo()
    {
        return $this->nombre_archivo;
    }
    public function getSensores()
    {
        return $this->sensores;
    }
    public static function load($userid="",$f_station_code="",$fromArrayValues=false)
    {
        if(is_array($fromArrayValues))
        {
            $loadedDataArray = $fromArrayValues;
        }else
        {
            $query="  SELECT  `id`,
                            `userid`,
                            `f_station_code`,
                            `enable`,
                            `info`
                    FROM    `configurations`
                    WHERE   `f_station_code`={$f_station_code} AND 
                            `userid`={$userid}";
            $loadedDataArray="";
            if(sql_select($query, $results))
            {
                if($results->rowCount() > 0)
                {
                    while($configInfo = $results->fetch(PDO::FETCH_ASSOC))
                    {
                        $loadedDataArray = $configInfo;
                    }
                }
            }
        }
        if(is_array($loadedDataArray) && count($loadedDataArray) > 0)
        {
            if(isset($loadedDataArray['info']))
            {
                $config=json_decode($loadedDataArray['info'],true);
                if(isset($loadedDataArray['activa']))
                {
                    $activa=$loadedDataArray['activa'];
                }else
                {
                    $activa=1;
                }
                // sensores
                $sensores=array();
                foreach($config as $key_cfg=>$cfg)
                {
                    if($cfg=="seleccionado")
                    {
                        // es sensor selecionado
                        $partes=explode("_",$key_cfg);
                        if(count($partes)==3)
                        {
                            $sensores[]=$partes[1]."_".$partes[2];
                        }
                    }
                }
                if(isset($config['periodo']))
                {
                    $periodo=$config['periodo'];
                }else
                {
                    $periodo="";
                }
                if(isset($config['periodo_fecha_inicial']))
                {
                    $periodo_fecha_inicial=$config['periodo_fecha_inicial'];
                }else
                {
                    $periodo_fecha_inicial="";
                }
                if(isset($config['periodo_fecha_final']))
                {
                    $periodo_fecha_final=$config['periodo_fecha_final'];
                }else
                {
                    $periodo_fecha_final="";
                }
                if(isset($config['tipo_archivo']))
                {
                    $tipo_archivo=$config['tipo_archivo'];
                }else
                {
                    $tipo_archivo="";
                }
                if(isset($config['separador']))
                {
                    $separador=$config['separador'];
                }else
                {
                    $separador="";
                }
                if(isset($config['encabezado']))
                {
                    $encabezado=$config['encabezado'];
                }else
                {
                    $encabezado="";
                }
                if(isset($config['archivo']))
                {
                    $archivo=$config['archivo'];
                }else
                {
                    $archivo="";
                }
                $q_config = new Config_Station($userid,$f_station_code,$activa,$sensores,$periodo,
                        $periodo_fecha_inicial,$periodo_fecha_final,$tipo_archivo,$separador,
                        $encabezado,$archivo);
                return $q_config;
            }
        }
        $q_config = new Config_Station($userid,$f_station_code,"1");
        return $q_config;
    }
    public function getError()
    {
        return $this->error;
    }
    public static function update()
    {
        if(!isset($_POST['activar']) OR $_POST['activar']=='off')
        {
            $activa=0;
        }else
        {
            $activa=1;
        }
        $info= json_encode($_POST);
        if(isset($_POST['userid']))
        {
            $userid=  req('userid');
        }
        if(isset($_POST['f_station_code']))
        {
            $f_station_code=  req('f_station_code');
        }
        if(isset($userid) AND isset($f_station_code))
        {
            // primero verifico que exista
            $query="SELECT  `id`
                    FROM    `configurations`
                    WHERE   `userid`={$userid} AND
                            `f_station_code`={$f_station_code}";
            if(!sql_select($query, $results))
            {
                mensaje("No se pudo consultar la base de datos","","error");
                return false;
            }
            if($results->rowCount() > 0)
            {
                unset($results);
                // lo actualizo
                $query="UPDATE  `configurations`
                        SET     `info`='{$info}',
                                `enable`={$activa}
                        WHERE   `userid`={$userid} AND
                                `f_station_code`={$f_station_code}";
                if(!sql_select($query, $results))
                {
                    mensaje("No se pudo actualizar los datos de la estacion","","error");
                    return false;
                }
            }else
            {
                unset($results);
                // inserto estacion
                $query="INSERT INTO `configurations` 
                            (`userid`,`f_station_code`,`enable`,`info`)
                        VALUES 
                            ({$id_usuario},{$f_station_code},{$activa},'{$info}')";
                if(!sql_select($query,$results))
                {
                    echo "ERROR! No se pudo insertar los datos de la estacion";
                    return false;
                }
            }
            return true;
        }
        echo "ERROR! No esta definido el usuario y/o la estaci&oacute;n";
        return false;
    }
}