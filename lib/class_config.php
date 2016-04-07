<?php
/**
 * Description of Config_Station
 *
 * @author pablo
 */
class Config_Station
{
    private $userid;                // userid de la tabla usuarios
    private $f_station_code;        // f_station_code 
    private $enable;
    private $sensores;              // array con los sensores seleccionados
    private $periodo;               // opcion de descargar con fecha de inicio y fecha final
    private $periodo_fecha_inicial; //
    private $periodo_mkfecha_inicial;
    private $periodo_fecha_final;   //
    private $periodo_mkfecha_final;
    private $periodo_dias;          //
    private $tipo_archivo;
    private $separador;
    private $encabezado;
    private $nombre_archivo;
    //
    private $error;
    /**
     * 
     * @param type $userid
     * @param type $f_station_code
     * @param type $enable
     * @param type $sensores
     * @param type $periodo
     * @param type $periodo_fecha_inicial
     * @param type $periodo_fecha_final
     * @param type $periodo_dias
     * @param type $tipo_archivo
     * @param type $separador
     * @param type $encabezado
     * @param type $archivo
     */
    function __construct($userid='',$f_station_code='',
                        $enable='',$sensores='',$periodo='',
                        $periodo_fecha_inicial='',
                        $periodo_fecha_final='',
                        $periodo_dias='',
                        $tipo_archivo='',
                        $separador='',
                        $encabezado='',
                        $archivo='') 
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
        $this->enable=(int)$enable;
        $this->periodo=$periodo;
        $this->periodo_fecha_inicial=$periodo_fecha_inicial;
        $this->periodo_fecha_final=$periodo_fecha_final;
        $this->periodo_dias=$periodo_dias;
        $this->tipo_archivo=$tipo_archivo;
        $this->separador=$separador;
        $this->encabezado=$encabezado;
        $this->nombre_archivo=$archivo;
        if($periodo_fecha_inicial<>"")
        {
            $partes=explode('/',$periodo_fecha_inicial);
            if(count($partes)==3)
            {
                $this->periodo_mkfecha_inicial=mktime(0, 0, 0, (int)$partes[1], (int)$partes[0], (int)$partes[2]);
            }else
            {
                $this->periodo_mkfecha_inicial=0;
            }
        }
        if($periodo_fecha_final<>"")
        {
            $partes=explode('/',$periodo_fecha_final);
            if(count($partes)==3)
            {
                $this->periodo_mkfecha_final=mktime(0, 0, 0, (int)$partes[1], (int)$partes[0], (int)$partes[2]);
            }else
            {
                $this->periodo_mkfecha_final=0;
            }
        }
    }
    /**
     * 
     * @return type
     */
    public function getIdUsuario()
    {
        return $this->userid;
    }
    /**
     * 
     * @return type
     */
    public function getStationCode()
    {
        return $this->f_station_code;
    }
    /**
     * 
     * @return boolean
     */
    public function getEnable()
    {
        if($this->enable==1) return true;
        return false;
    }
    /**
     * 
     * @return type
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }
    /**
     * 
     * @return type
     */
    public function getPeriodoFechaInicial()
    {
        return $this->periodo_fecha_inicial;
    }
    /**
     * 
     * @return type
     */
    public function getPeriodoMkFechaInicial()
    {
        return $this->periodo_mkfecha_inicial;
    }
    /**
     * 
     * @return type
     */
    public function getPeriodoFechaFinal()
    {
        return $this->periodo_fecha_final;
    }
    /**
     * 
     * @return type
     */
    public function getPeriodoMkFechaFinal()
    {
        return $this->periodo_mkfecha_final;
    }
    /**
     * 
     * @return type
     */
    public function getPeriodoDias()
    {
        return $this->periodo_dias;
    }
    /**
     * 
     * @return type
     */
    public function getTipoArchivo()
    {
        return $this->tipo_archivo;
    }
    /**
     * 
     * @return type
     */
    public function getSeparador()
    {
        return $this->separador;
    }
    /**
     * 
     * @return type
     */
    public function getSeparador2()
    {
        $separador2=  json_decode(SEPARADORES2);
        return $separador2[$this->separador];
        /*
        switch($this->separador)
        {
            case 'punto_coma':
                return chr(59);
                break;
            case 'coma':
                return chr(44);
                break;
            case 'espacio':
                return chr(32);
                break;
            case 'tab':
            default:
                return chr(9);
                break;
        }
         * 
         */
    }
    /**
     * 
     * @return type
     */
    public function getEncabezado()
    {
        return $this->encabezado;
    }
    /**
     * 
     * @return type
     */
    public function getNombreArchivo()
    {
        return $this->nombre_archivo;
    }
    /**
     * 
     * @return type
     */
    public function getSensores()
    {
        return $this->sensores;
    }
    /**
     * 
     * @param type $userid
     * @param type $f_station_code
     * @param type $fromArrayValues
     * @return \Config_Station
     */
    public static function load($userid="",$f_station_code="",$fromArrayValues=false)
    {
        if(is_array($fromArrayValues))
        {
            $loadedDataArray = $fromArrayValues;
        }else
        {
            $query="SELECT  `id`,
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
                if(isset($loadedDataArray['enable']))
                {
                    $enable=$loadedDataArray['enable'];
                }else
                {
                    $enable=0;
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
                if(isset($config['fecha_inicial']))
                {
                    $periodo_fecha_inicial=$config['fecha_inicial'];
                    if($periodo_fecha_inicial<>"")
                    {
                        $partes=explode('/',$periodo_fecha_inicial);
                        if(count($partes)==3)
                        {
                            $periodo_mkfecha_inicial=mktime(0, 0, 0, (int)$partes[1], (int)$partes[0], (int)$partes[2]);
                        }else
                        {
                            $periodo_mkfecha_inicial=0;
                        }
                    }
                }else
                {
                    $periodo_fecha_inicial="";
                }
                if(isset($config['fecha_final']))
                {
                    $periodo_fecha_final=$config['fecha_final'];
                    if($periodo_fecha_final<>"")
                    {
                        $partes=explode('/',$periodo_fecha_final);
                        if(count($partes)==3)
                        {
                            $periodo_mkfecha_final=mktime(0, 0, 0, (int)$partes[1], (int)$partes[0], (int)$partes[2]);
                        }else
                        {
                            $periodo_mkfecha_final=0;
                        }
                    }
                }else
                {
                    $periodo_fecha_final="";
                }
                if(isset($config['periodo_dias']))
                {
                    $periodo_dias=$config['periodo_dias'];
                }else
                {
                    $periodo_dias="";
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
                $q_config = new Config_Station($userid,$f_station_code,$enable,$sensores,$periodo,
                        $periodo_fecha_inicial,
                        $periodo_fecha_final,
                        $periodo_dias,$tipo_archivo,$separador,$encabezado,$archivo);
                return $q_config;
            }
        }
        $q_config = new Config_Station($userid,$f_station_code,"0");
        return $q_config;
    }
    /**
     * 
     * @return type
     */
    public function getError()
    {
        return $this->error;
    }
    /**
     * 
     * @return boolean
     */
    public function update()
    {
        if(!isset($_POST['enable']) OR $_POST['enable']=='off')
        {
            //estacion desactivada
            $enable=0;
        }else
        {
            $enable=1;
            if(!$validacion=$this->validacion())
            {
                error_log(date('Y-m-d H:i:s').chr(9).$validacion,3,ERROR_LOG);
                $this->error=$validacion;
                return false;
            }
        }
        //
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
                                `enable`={$enable}
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
                            ({$userid},{$f_station_code},{$enable},'{$info}')";
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
    /**
     * 
     * @param IMETOS $BD
     * @param type $f_station_code is string
     */
    public function runQuery(IMETOS $BD, Station $station)
    {
        $f_station_code=$station->getStationCode();
        // periodo a descargar
        // valores: periodo, mes_actual, todos, fijo
        echo "f_station_code--->{$f_station_code}<br>";
        switch($this->getPeriodo())
        {
            case 'periodo':
                // fecha inicial 
                $fecha_inicial=$this->getPeriodoMkFechaInicial();
                $fecha_final=$this->getPeriodoMkFechaFinal();
                $date_start= new DateTime(date('Y-m-d',$fecha_inicial));
                $date_final= new DateTime(date('Y-m-d',$fecha_final));
                break;
                //
            case 'mes_actual':
                // mes actual
                $mes_actual=date('n');
                $anio_actual=date('Y');
                $days_number=cal_days_in_month(CAL_GREGORIAN, $mes_actual, $anio_actual);
                $fecha_inicial=mktime(0,0,0,$mes_actual,1,$anio_actual);
                $date_start= new DateTime(date('Y-m-d',$fecha_inicial));
                $fecha_final=mktime(0,0,0,$mes_actual,$days_number,$anio_actual);
                $date_final= new DateTime(date('Y-m-d',$fecha_final));
                break;
                //
            case 'todos':
                // todos los datos
                $query = "SELECT MIN(`f_read_time`) as min,
                                 MAX(`f_read_time`) as max  
                          FROM `seedclima_sensor_data_retrieve_stats_day`
                          WHERE `f_station_code`={$this->f_station_code}";
                if(!$BD->sql_select($query, $results))
                {
                    error_log(date('Y-m-d H:i:s').chr(9)."ERROR. No se puede determinar minimo y maximo en las fechas",3,ERROR_LOG);
                }
                if($min_max=$results->fetch(PDO::FETCH_ASSOC))
                {
                    $date_start= new DateTime(date('Y-m-d',$min_max['min']));
                    $date_final= new DateTime(date('Y-m-d',$min_max['max']));
                }else
                {
                    $date_start= new DateTime(date('Y-m-d'));
                    $date_final= new DateTime(date('Y-m-d'));
                }
                break;
                //
            case 'fijo':
                // periodo fijo en dias
                $periodo_dias=$this->getPeriodoDias();
                $date_final= new DateTime(date('Y-m-d'));
                $date_start= new DateTime(date('Y-m-d'));
                $date_start->sub(new DateInterval('P'.$periodo_dias.'D'));
                break;
        }
        $date_start2 = $date_start->format('Y-m-d');
        $date_final2 = $date_final->format('Y-m-d');
        //
        $date_start3 = $date_start->getTimestamp();
        $date_final3 = $date_final->getTimestamp();
        //
        // sensores
        $query=array();
        $enca1="{$this->getSeparador2()}";
        $enca2="fecha{$this->getSeparador2()}";
        foreach($this->sensores as $key_sensor => $sensor)
        {
            $select='`f_read_time`,';
            $where="`f_station_code`={$f_station_code} AND (`f_read_time`>={$date_start3} AND `f_read_time`<={$date_final3}) AND";
            // sensor contiene code_ch
            $partes=explode("_",$sensor);
            if(count($partes)==2)
            {
                $f_sensor_code=$partes[0];
                $f_sensor_ch=$partes[1];
                $qsensor = $station->getSensor($f_sensor_code, $f_sensor_ch,1);
                $enca1.="{$qsensor->getSensorUserName()}{$this->getSeparador2()}";
                if($qsensor->getValMin())
                {
                    $select.='`min`,';
                    $enca2.="min{$this->getSeparador2()}";
                }
                if($qsensor->getValMax())
                {
                    $select.='`max`,';
                    $enca2.="max{$this->getSeparador2()}";
                }
                if($qsensor->getValSum())
                {
                    $select.='`sum`,';
                    $enca2.="sum{$this->getSeparador2()}";
                }
                if($qsensor->getValAver())
                {
                    $select.='`aver`,';
                    $enca2.="aver{$this->getSeparador2()}";
                }
                if($qsensor->getValLast())
                {
                    $select.='`last`,';
                    $enca2.="last{$this->getSeparador2()}";
                }
                $where.=" `f_sensor_code`={$f_sensor_code} AND `f_sensor_ch`={$f_sensor_ch}";
            }
            if(substr($select,-1==','))
            {
                $select=substr($select,0,-1);
            }
            if(substr($where,-3)=='AND')
            {
                // saco el AND
                $where=substr($where,0,-3);
            }
            // hago la consulta
            $query[$sensor]="SELECT {$select}
                    FROM `seedclima_sensor_data_retrieve_stats_day` 
                    WHERE {$where}
                    ORDER BY `f_read_time` ASC";
        }
        if(substr($enca1,-(strlen($this->getSeparador2()))==$this->getSeparador2()))
        {
            $enca1=substr($enca1,0,-(strlen($this->getSeparador2())));
        }
        if(substr($enca2,-(strlen($this->getSeparador2()))==$this->getSeparador2()))
        {
            $enca2=substr($enca2,0,-(strlen($this->getSeparador2())));
        }
        return array($query,$enca1,$enca2);
    }
    /**
     * 
     */
    private function validacion()
    {
        $validacion="";
        // debo validar los campos
        // {"userid":"4","f_station_code":"164","enable":"on",
        // "sensor_17921_771":"seleccionado","sensor_17921_770":"seleccionado",
        // "periodo":"periodo",
        // "fecha_inicial":"10/05/2015","fecha_final":"11/05/2016","periodo_dias":"30",
        // "tipo_archivo":"csv","separador":"coma",
        // "encabezado":"si",
        // "archivo":"prueba.csv","save_config":""}
        if(!in_array("seleccionado",$_POST))
        {
            $validacion.="Seleccione alg&uacute;n sensor para exportar\n";
        }
        if(isset($_POST['periodo']))
        {
            switch($_POST['periodo'])
            {
                case 'periodo':
                    if(!isset($_POST['fecha_incial']) OR !isset($_POST['fecha_final']))
                    {
                        $validacion.="No se definieron las fechas de inicio y final\n";
                    }
                    if(isset($_POST['fecha_inicial']))
                    {
                        // fecha valida
                        if(strlen($_POST['fecha_inicial'])==0)
                        {
                            $validacion.="Debe completar la fecha de inicio del período.\n";
                        }
                        if(strlen($_POST['fecha_final'])==0)
                        {
                            $validacion.="Debe completar la fecha de fin del período.\n";
                        }
                        if(!$this->valido_fechas($_POST['fecha_inicial'],$_POST['fecha_final']))
                        {
                            $validacion.="La fecha final no puede ser anterior a la fecha inicial del período.\n";
                        }
                    }
                    break;
                    //
                case 'fijo':
                    if(!isset($_POST['periodo_dias']))
                    {
                        $validacion.="No esta definido el período en días.\n";
                    }else
                    {
                        $periodo_dias=intval($_POST['periodo_dias']);
                        if($periodo_dias<1 OR $periodo_dias>365)
                        {
                            $validacion.="El período en días debe ser mayor a 0 y menor a 366 días.\n";
                        }
                    }
                    break;
                    
            }
        }
        if(!isset($_POST['tipo_archivo']))
        {
            $validacion.="El tipo de archivo no esta definido.\n";
            
        }
        if(!empty($validacion))
        {
            echo "pase por aca<br>";
            echo $validacion."<br>";
            $this->error=$validacion;
            return false;
        }
        return true;
    }
}