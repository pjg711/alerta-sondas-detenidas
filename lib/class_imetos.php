<?php
// requisitos:
// CURL
//
class IMETOS
{
    public $dblink;
    
    private $server_mysql;
    private $database_mysql;
    private $user_mysql;
    private $passw_mysql;
    //
    private $rowCount=0;
    //
    function __construct($server=null,$database=null,$username=null,$password=null)
    {
        $this->server_mysql=$server;
        $this->user_mysql=$username;
        $this->passw_mysql=$password;
        $this->database_mysql=$database;
        //
        if(is_null($server) OR is_null($database) OR is_null($username) OR is_null($password))
        {
            if(isset($_SESSION['id_usuario']))
            {
                if($conexion=buscar_datos_conexion($_SESSION['id_usuario']))
                {
                    $this->server_mysql=$conexion['servidor'];
                    $this->database_mysql=$conexion['base_datos'];
                    $this->user_mysql=$conexion['usuario'];
                    $this->passw_mysql=$conexion['password'];
                }
            }
        }
        $this->rowCount=0;
        if(!($this->dblink = new PDO('mysql:host='.$this->server_mysql.';dbname='.$this->database_mysql.';charset=utf8',$this->user_mysql,$this->passw_mysql)))
        {
            echo "No es posible conectar con la base de datos ".$base_datos."<br>";
            return false;
        }
    }
    public function get_user()
    {
        return $this->user_mysql;
    }
    public function get_pass()
    {
        return $this->passw_mysql;
    }
    public function get_database()
    {
        return $this->database_mysql;
    }
    public function get_server()
    {
        return $this->server_mysql;
    }
    public function get_rowCount()
    {
        return $this->rowCount;
    }
    //
    public function sql_select($query, &$rv)
    {
        $query=preg_replace("/\r\n|\r/", chr(32), $query);
        if (DEFAULT_CHARSET == "utf8" OR DEFAULT_CHARSET == "utf-8")
        {
            $this->dblink->query("SET NAMES 'utf8'");
        }
        $rv = $this->dblink->prepare($query);
        if(!$rv->execute())
        {
            return false;
        }
        if($last_id=$this->dblink->lastInsertId())
        {
            return $last_id;
        }
        if($rv->rowCount())
        {
            $this->rowCount=$rv->rowCount();
        }
        return true;
    }
}
class Station
{
    /** @var */
    private $_rowId;	    //INT(11) UNSIGNED NOT NULL AUTOINCREMENT,
    /** @var */
    private $_fStationCode; //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fDate;	    //DATETIME NULL DEFAULT NULL,
    /** @var */
    private $_fDevId;       //INT(2) UNSIGNED NULL DEFAULT '0',
    /** @var */
    private $_fName;	    //VARCHAR(10) NULL DEFAULT NULL COMMENT 'nombre estacion ' COLLATE 'utf8unicodeci',
    /** @var */
    private $_fDescr;       //VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8unicodeci',
    /** @var */
    private $_fInfo;	    //VARCHAR(255) NULL DEFAULT NULL COMMENT 'version del firmware' COLLATE 'utf8unicodeci',
    /** @var */
    private $_fUid;         //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fStatus;      //VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8unicodeci',
    /** @var */
    private $_fCreateTime;  //DATETIME NULL DEFAULT NULL COMMENT 'Up time',
    /** @var */
    private $_fMasterName;  //VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8unicodeci',
    /** @var */
    private $_fDateMin;     //DATETIME NULL DEFAULT NULL,
    /** @var */
    private $_fDateMax;     //DATETIME NULL DEFAULT NULL,
    /** @var */
    private $_fDateLastDown;    //DATETIME NULL DEFAULT NULL,
    /** @var */
    private $_fDateSens;	//DATETIME NULL DEFAULT NULL,
    /** @var */
    private $_fDateData;	//DATETIME NULL DEFAULT NULL,
    /** @var */
    private $_fDateConf;	//DATETIME NULL DEFAULT NULL,
    /** @var */
    private $_fMeasureInt;      //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fDataInt;	 //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fTimezone;	//INT(10) NULL DEFAULT NULL,
    /** @var */
    private $_fLatitude;	//DECIMAL(12,6) NULL DEFAULT NULL,
    /** @var */
    private $_fLongitude;       //DECIMAL(12,6) NULL DEFAULT NULL,
    /** @var */
    private $_fAltitude;	//DECIMAL(12,10) NULL DEFAULT NULL,
    /** @var */
    private $_fHwVerMajor;      //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fHwVerMinor;      //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fSwVerMajor;      //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fSwVerMinor;      //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fSmsWarnNumbers;  //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fSmsWarnValues;   //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fGsmMcc;	  //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fGsmMnc;	  //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fGprsApn;	 //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fGprsUserId;      //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fGprsPassw;       //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fSernum;	  //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var */
    private $_fDateComm;	//DATETIME NULL DEFAULT NULL,
    /** @var */
    private $_fUserStationName; //TEXT NULL COLLATE 'utf8unicodeci',
    /** @var */
    private $_fUserName;	//TEXT NULL COLLATE 'utf8unicodeci',
    /** @var */
    private $_customName;       //TEXT NULL COMMENT 'nombre custom para el sitio' COLLATE 'utf8unicodeci',
    /** @var */
    private $_customDesc;       //TEXT NULL COLLATE 'utf8unicodeci',
    /** @var */
    private $_customImage;      //VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8unicodeci',
    /** @var */
    private $_enableStation;    //TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Determina si el usuario desea habilitar la visualizacion de los datos de la estacion',
    /** @var */
    private $_showInHome;       //TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
    /** @var */
    private $_priority;	 //INT(11) UNSIGNED NOT NULL DEFAULT '9999' COMMENT 'Determina el orden de visualización de las estaciones',
    /** @var */
    private $_lastUpdateDate;   //DATETIME NULL DEFAULT NULL,
    /** @var */
    private $_lastEditionTime;  //DATETIME NULL DEFAULT NULL,
    /** @var UnixTImestamp */
    private $_lastDataRetrievedTime;
    /** @var */
    private $_lastEditor;       //INT(11) UNSIGNED NULL DEFAULT NULL,
    /** @var Array */
    private $_sensorsList = array('enabled' => array(), 'disabled' => array());
    /** @var <type> */
    private $_statusReport;
    /** @var <type> */
    private $_status;
    /** @var Array */
    private $_instantSensorData;

    /**
     *
     * @param <type> $row_id
     * @param <type> $f_station_code
     * @param <type> $f_date
     * @param <type> $f_dev_id
     * @param <type> $f_name
     * @param <type> $f_descr
     * @param <type> $f_info
     * @param <type> $f_uid
     * @param <type> $f_status
     * @param <type> $f_create_time
     * @param <type> $f_master_name
     * @param <type> $f_date_min
     * @param <type> $f_date_max
     * @param <type> $f_date_last_down
     * @param <type> $f_date_sens
     * @param <type> $f_date_data
     * @param <type> $f_date_conf
     * @param <type> $f_measure_int
     * @param <type> $f_data_int
     * @param <type> $f_timezone
     * @param <type> $f_latitude
     * @param <type> $f_longitude
     * @param <type> $f_altitude
     * @param <type> $f_hw_ver_major
     * @param <type> $f_hw_ver_minor
     * @param <type> $f_sw_ver_major
     * @param <type> $f_sw_ver_minor
     * @param <type> $f_sms_warn_numbers
     * @param <type> $f_sms_warn_values
     * @param <type> $f_gsm_mcc
     * @param <type> $f_gsm_mnc
     * @param <type> $f_gprs_apn
     * @param <type> $f_gprs_user_id
     * @param <type> $f_gprs_passw
     * @param <type> $f_sernum
     * @param <type> $f_date_comm
     * @param <type> $f_user_station_name
     * @param <type> $f_user_name
     * @param <type> $custom_name
     * @param <type> $custom_desc
     * @param <type> $custom_image
     * @param <type> $enable_station
     * @param <type> $show_in_home
     * @param <type> $priority
     * @param <type> $last_update_date
     * @param <type> $last_edition_time
     * @param <type> $last_editor 
     */
    public function __construct($row_id = '', $f_station_code = '', $f_date = '', $f_dev_id = '', $f_name = '', $f_descr = '', $f_info = '', $f_uid = '', $f_status = '', $f_create_time = '', $f_master_name = '', $f_date_min = '', $f_date_max = '', $f_date_last_down = '', $f_date_sens = '', $f_date_data = '', $f_date_conf = '', $f_measure_int = '', $f_data_int = '', $f_timezone = '', $f_latitude = '', $f_longitude = '', $f_altitude = '', $f_hw_ver_major = '', $f_hw_ver_minor = '', $f_sw_ver_major = '', $f_sw_ver_minor = '', $f_sms_warn_numbers = '', $f_sms_warn_values = '', $f_gsm_mcc = '', $f_gsm_mnc = '', $f_gprs_apn = '', $f_gprs_user_id = '', $f_gprs_passw = '', $f_sernum = '', $f_date_comm = '', $f_user_station_name = '', $f_user_name = '', $custom_name = '', $custom_desc = '', $custom_image = '', $enable_station = '', $show_in_home = '', $priority = '', $last_update_date = '', $last_edition_time = '', $last_editor = '')
    {
        $this->_rowId = $row_id;
        $this->_fStationCode = $f_station_code;
        $this->_fDate = $f_date;
        $this->_fDevId = $f_dev_id;
        $this->_fName = $f_name;
        $this->_fDescr = $f_descr;
        $this->_fInfo = $f_info;
        $this->_fUid = $f_uid;
        $this->_fStatus = $f_status;
        $this->_fCreateTime = $f_create_time;
        $this->_fMasterName = $f_master_name;
        $this->_fDateMin = $f_date_min;
        $this->_fDateMax = $f_date_max;
        $this->_fDateLastDown = $f_date_last_down;
        $this->_fDateSens = $f_date_sens;
        $this->_fDateData = $f_date_data;
        $this->_fDateConf = $f_date_conf;
        $this->_fMeasureInt = $f_measure_int;
        $this->_fDataInt = $f_data_int;
        $this->_fTimezone = $f_timezone;
        $this->_fLatitude = $f_latitude;
        $this->_fLongitude = $f_longitude;
        $this->_fAltitude = $f_altitude;
        $this->_fHwVerMajor = $f_hw_ver_major;
        $this->_fHwVerMinor = $f_hw_ver_minor;
        $this->_fSwVerMajor = $f_sw_ver_major;
        $this->_fSwVerMinor = $f_sw_ver_minor;
        $this->_fSmsWarnNumbers = $f_sms_warn_numbers;
        $this->_fSmsWarnValues = $f_sms_warn_values;
        $this->_fGsmMcc = $f_gsm_mcc;
        $this->_fGsmMnc = $f_gsm_mnc;
        $this->_fGprsApn = $f_gprs_apn;
        $this->_fGprsUserId = $f_gprs_user_id;
        $this->_fGprsPassw = $f_gprs_passw;
        $this->_fSernum = $f_sernum;
        $this->_fDateComm = $f_date_comm;
        $this->_fUserStationName = $f_user_station_name;
        $this->_fUserName = $f_user_name;
        $this->_customName = $custom_name;
        $this->_customDesc = $custom_desc;
        $this->_customImage = $custom_image;
        $this->_enableStation = $enable_station;
        $this->_showInHome = $show_in_home;
        $this->_priority = $priority;
        $this->_lastUpdateDate = $last_update_date;
        $this->_lastEditionTime = $last_edition_time;
        $this->_lastEditor = $last_editor;
    }

    /**
     *
     * @param Integer $f_station_code
     * @param Array $fromArrayValues
     * @return Station
     */
    public static function load($f_station_code, $fromArrayValues = false)
    {
        if(is_array($fromArrayValues))
        {
            $loadedDataArray = $fromArrayValues;
        }else
        {
            $BD=new IMETOS();
            $sqlQuery = "
                SELECT
                    `row_id`,
                    `f_station_code`,
                    `f_date`,
                    `f_dev_id`,
                    `f_name`,
                    `f_descr`,
                    `f_info`,
                    `f_uid`,
                    `f_status`,
                    `f_create_time`,
                    `f_master_name`,
                    `f_date_min`,
                    `f_date_max`,
                    `f_date_last_down`,
                    `f_date_sens`,
                    `f_date_data`,
                    `f_date_conf`,
                    `f_measure_int`,
                    `f_data_int`,
                    `f_timezone`,
                    `f_latitude`,
                    `f_longitude`,
                    `f_altitude`,
                    `f_hw_ver_major`,
                    `f_hw_ver_minor`,
                    `f_sw_ver_major`,
                    `f_sw_ver_minor`,
                    `f_sms_warn_numbers`,
                    `f_sms_warn_values`,
                    `f_gsm_mcc`,
                    `f_gsm_mnc`,
                    `f_gprs_apn`,
                    `f_gprs_user_id`,
                    `f_gprs_passw`,
                    `f_sernum`,
                    `f_date_comm`,
                    `f_user_station_name`,
                    `f_user_name`,
                    `custom_name`,
                    `custom_desc`,
                    `custom_image`,
                    `enable_station`,
                    `show_in_home`,
                    `priority`,
                    `last_update_date`,
                    `last_edition_time`,
                    `last_editor`
                FROM
                    `seedclima_station_info`
                WHERE
                    `f_station_code` = {$f_station_code}
                LIMIT 1";
            if($BD->sql_select($sqlQuery, $result))
            {
                if($BD->get_rowCount() > 0)
                {
                    settype($response, 'array');
                    while($stationInfo = $result->fetch(PDO::FETCH_ASSOC))
                    {
                        $loadedDataArray = $stationInfo;
                    }
                }
            }
        }
        if(is_array($loadedDataArray) && count($loadedDataArray) > 0)
        {
            $station = new Station($loadedDataArray['row_id'],
			    $loadedDataArray['f_station_code'],
			    $loadedDataArray['f_date'],
			    $loadedDataArray['f_dev_id'],
			    $loadedDataArray['f_name'],
			    $loadedDataArray['f_descr'],
			    $loadedDataArray['f_info'],
			    $loadedDataArray['f_uid'],
			    $loadedDataArray['f_status'],
			    $loadedDataArray['f_create_time'],
			    $loadedDataArray['f_master_name'],
			    $loadedDataArray['f_date_min'],
			    $loadedDataArray['f_date_max'],
			    $loadedDataArray['f_date_last_down'],
			    $loadedDataArray['f_date_sens'],
			    $loadedDataArray['f_date_data'],
			    $loadedDataArray['f_date_conf'],
			    $loadedDataArray['f_measure_int'],
			    $loadedDataArray['f_data_int'],
			    $loadedDataArray['f_timezone'],
			    $loadedDataArray['f_latitude'],
			    $loadedDataArray['f_longitude'],
			    $loadedDataArray['f_altitude'],
			    $loadedDataArray['f_hw_ver_major'],
			    $loadedDataArray['f_hw_ver_minor'],
			    $loadedDataArray['f_sw_ver_major'],
			    $loadedDataArray['f_sw_ver_minor'],
			    $loadedDataArray['f_sms_warn_numbers'],
			    $loadedDataArray['f_sms_warn_values'],
			    $loadedDataArray['f_gsm_mcc'],
			    $loadedDataArray['f_gsm_mnc'],
			    $loadedDataArray['f_gprs_apn'],
			    $loadedDataArray['f_gprs_user_id'],
			    $loadedDataArray['f_gprs_passw'],
			    $loadedDataArray['f_sernum'],
			    $loadedDataArray['f_date_comm'],
			    $loadedDataArray['f_user_station_name'],
			    $loadedDataArray['f_user_name'],
			    $loadedDataArray['custom_name'],
			    $loadedDataArray['custom_desc'],
			    $loadedDataArray['custom_image'],
			    $loadedDataArray['enable_station'],
			    $loadedDataArray['show_in_home'],
			    $loadedDataArray['priority'],
			    $loadedDataArray['last_update_date'],
			    $loadedDataArray['last_edition_time'],
			    $loadedDataArray['last_editor']
            );
            $station->_setLastDataRetrievedTime();
            $station->_setStatusReport();
            return $station;
        }else
        {
            return false;
        }
    }

    /**  @return <type> */
    public function getRowId()
    {
        return $this->_rowId;
    }

    /**  @return <type> */
    public function getStationCode()
    {
        return $this->_fStationCode;
    }

    /**  @return <type> */
    public function getDate()
    {
        return $this->_fDate;
    }

    /**  @return <type> */
    public function getDevId()
    {
        return $this->_fDevId;
    }

    /**  @return <type> */
    public function getFName()
    {
        return $this->_fName;
    }

    /**  @return <type> */
    public function getName()
    {
        if($this->getCustomName())
        {
            $staionName = $this->getCustomName();
        }elseif($this->getUserStationName())
        {
            $staionName = $this->getUserStationName();
        }else
        {
            $staionName = $this->_fName;
        }
        return $staionName;
    }

    /**  @return <type> */
    public function getDescr()
    {
        return $this->_fDescr;
    }

    /**  @return <type> */
    public function getInfo()
    {
        return $this->_fInfo;
    }

    /**  @return <type> */
    public function getUid()
    {
        return $this->_fUid;
    }

    /**  @return <type> */
    public function getStatus()
    {
        return $this->_fStatus;
    }

    /**  @return <type> */
    public function getCreateTime()
    {
        return $this->_fCreateTime;
    }

    /**  @return <type> */
    public function getMasterName()
    {
        return $this->_fMasterName;
    }

    /**  @return <type> */
    public function getDateMin()
    {
        return $this->_fDateMin;
    }

    /**  @return <type> */
    public function getDateMax()
    {
        return $this->_fDateMax;
    }

    /**  @return <type> */
    public function getDateLastDown()
    {
        return $this->_fDateLastDown;
    }

    /**  @return <type> */
    public function getDateSens()
    {
        return $this->_fDateSens;
    }

    /**  @return <type> */
    public function getDateData()
    {
        return $this->_fDateData;
    }

    /**  @return <type> */
    public function getDateConf()
    {
        return $this->_fDateConf;
    }

    /**  @return <type> */
    public function getMeasureInt()
    {
        return $this->_fMeasureInt;
    }

    /**  @return <type> */
    public function getDataInt()
    {
        return $this->_fDataInt;
    }

    /**  @return <type> */
    public function getTimezone()
    {
        return $this->_fTimezone;
    }

    /**  @return <type> */
    public function getLatitude()
    {
        return $this->_fLatitude;
    }

    /**  @return <type> */
    public function getLongitude()
    {
        return $this->_fLongitude;
    }

    /**  @return <type> */
    public function getAltitude()
    {
        return $this->_fAltitude;
    }

    /**  @return <type> */
    public function getHwVerMajor()
    {
        return $this->_fHwVerMajor;
    }

    /**  @return <type> */
    public function getHwVerMinor()
    {
        return $this->_fHwVerMinor;
    }

    /**  @return <type> */
    public function getSwVerMajor()
    {
        return $this->_fSwVerMajor;
    }

    /**  @return <type> */
    public function getSwVerMinor()
    {
        return $this->_fSwVerMinor;
    }

    /**  @return <type> */
    public function getSmsWarnNumbers()
    {
        return $this->_fSmsWarnNumbers;
    }

    /**  @return <type> */
    public function getSmsWarnValues()
    {
        return $this->_fSmsWarnValues;
    }

    /**  @return <type> */
    public function getGsmMcc()
    {
        return $this->_fGsmMcc;
    }

    /**  @return <type> */
    public function getGsmMnc()
    {
        return $this->_fGsmMnc;
    }

    /**  @return <type> */
    public function getGprsApn()
    {
        return $this->_fGprsApn;
    }

    /**  @return <type> */
    public function getGprsUserId()
    {
        return $this->_fGprsUserId;
    }

    /**  @return <type> */
    public function getGprsPassw()
    {
        return $this->_fGprsPassw;
    }

    /**  @return <type> */
    public function getSernum()
    {
        return $this->_fSernum;
    }

    /**  @return <type> */
    public function getDateComm()
    {
        return $this->_fDateComm;
    }

    /**  @return <type> */
    public function getUserStationName()
    {
        return $this->_fUserStationName;
    }

    /**  @return <type> */
    public function getUserName()
    {
        return $this->_fUserName;
    }

    /**  @return <type> */
    public function getCustomName()
    {
        return $this->_customName;
    }

    /**  @return <type> */
    public function getCustomDesc()
    {
        return $this->_customDesc;
    }

    /**  @return <type> */
    public function getCustomImage()
    {
        return $this->_customImage ? $this->_customImage : 'default_station.jpg';
    }

    /**  @return <type> */
    public function isEnabled()
    {
        return $this->_enableStation;
    }

    /**  @return <type> */
    public function getShowInHome()
    {
        return $this->_showInHome;
    }

    /**  @return <type> */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**  @return <type> */
    public function getLastUpdateDate()
    {
        return $this->_lastUpdateDate;
    }

    /**  @return <type> */
    public function getLastEditionTime()
    {
        return $this->_lastEditionTime;
    }

    /**  @return <type> */
    public function getLastEditor()
    {
        return $this->_lastEditor;
    }

    /** @return <type> */
    public function getStationStatusReport()
    {
        return $this->_statusReport;
    }

    public function getStationStatus()
    {
        return $this->_status;
    }

    /**
     * 
     */
    private function _setLastDataRetrievedTime()
    {
        $BD=new IMETOS();
        $sqlQuery = "
            SELECT
                MAX(`last_read_time`)  as 'last_read_time'
            FROM
                `seedclima_station_data_retrieve_info`
            WHERE
                `f_station_code` = {$this->getStationCode()}";
        if($BD->sql_select($sqlQuery, $results))
        {
            if($BD->get_rowCount() > 0)
            {
                $row = $results->fetch(PDO::FETCH_ASSOC);
                $this->_lastDataRetrievedTime = $row['last_read_time'];
            }else
            {
                $this->_lastDataRetrievedTime = false;
            }
        }
    }

    /**
     *
     * @return Array|false 
     */
    public function instantSensorData(Array $filterById = null)
    {
        if(!$this->_lastDataRetrievedTime)
        {
            return false;
        }
		if(isset($filterById))
        {
            $filterById = implode(',',$filterById);
            $filterById = "`row_id` IN ({$filterById}) AND";
        }else
        {
            $filterById = '';
        }
    	$tableSuffix = date('Y_m', $this->_lastDataRetrievedTime);

        $query = "SELECT
                    `row_id`,
                    `f_sensor_type`,
                    `f_station_code`,
                    `f_sensor_code`,
                    `f_sensor_ch`,
                    `f_read_time`,
                    `f_data_retrieved`,
                    `f_unit`,
                    `out_of_range`,
                    `wind_direction_vector`
                FROM
                    `seedclima_sensor_data_retrieve_info_{$tableSuffix}`
                WHERE
                    `out_of_range` <> 1
                AND
                    `f_read_time` = (SELECT  MAX(`f_read_time`) FROM `seedclima_sensor_data_retrieve_info_{$tableSuffix}` WHERE `f_station_code` = {$this->_fStationCode} AND `out_of_range` <> 1)
                AND
                    `f_station_code` = '{$this->_fStationCode}'
                AND
                    `f_sensor_code` IN (SELECT `f_sensor_code` FROM `seedclima_sensor_info` WHERE {$filterById} `enable_sensor` = 1 AND `f_station_code` = {$this->_fStationCode})";
        if(sql_select($query, $results))
        {
            $response = false;
            while($row = mysql_fetch_object($results))
            {
                $response[] = $row;
            }
            $this->_instantSensorData = $response;
            return $this->_instantSensorData;
        }
    }

    /**
     *
     */
    private function _setStatusReport()
    {
        if(!$this->_lastDataRetrievedTime)
        {
            $this->_statusReport = 'offline';
            $this->_status = 'offline';
        }elseif($this->_lastDataRetrievedTime < strtotime('- 1 month'))
        {
            $this->_statusReport = 'No transmite desde el ' . date('d/m/Y H:i:s', $this->_lastDataRetrievedTime);
            $this->_status = 'ok';
        }elseif($this->_lastDataRetrievedTime < strtotime('- 3 day'))
        {
            $this->_statusReport = 'No transmite desde el ' . date('d/m/Y H:i:s', $this->_lastDataRetrievedTime);
            $this->_status = 'ok';
        }elseif($this->_lastDataRetrievedTime < strtotime('- 1 day'))
        {
            $this->_statusReport = 'No transmite desde el ' . date('d/m/Y H:i:s', $this->_lastDataRetrievedTime);
            $this->_status = 'ok';
        }else
        {
            $this->_statusReport = 'ok';
            $this->_status = 'ok';
        }
    }

    /**
     *
     * @param Integer $status 0|1
     * @param Array $filterById
     */
    public function loadSensors($status = null, Array $filterById = null)
    {
        switch($status)
        {
            case '1':
                $this->_sensorsList['enabled'] = Sensor::getAll($this->getStationCode(), null, null, 1, $filterById);
                break;
            case'0':
                $this->_sensorsList['disabled'] = Sensor::getAll($this->getStationCode(), null, null, 0, $filterById);
                break;
            default:
                $this->_sensorsList['enabled'] = Sensor::getAll($this->getStationCode(), null, null, 1, $filterById);
                $this->_sensorsList['disabled'] = Sensor::getAll($this->getStationCode(), null, null, 0, $filterById);
                break;
        }
    }

    /**
     *
     * @param Integer $status  0|1
     * @return Array
     */
    public function getAvailableSensors($status = null)
    {
        switch($status)
        {
            case '1':
                return $this->_sensorsList['enabled'];
                break;
            case'0':
                return $this->_sensorsList['disabled'];
                break;
            case'all':
                $all = array_merge($this->_sensorsList['enabled'], $this->_sensorsList['disabled']);
                return $all;
                break;
            default:
                return $this->_sensorsList;
                break;
        }
    }

    /**
     *
     * @param Integer $f_sensor_code
     * @param Integer $f_sensor_ch
     * @param Integer $status  0|1
     * @return Sensor or False
     */
    public function getSensor($f_sensor_code, $f_sensor_ch, $status = null)
    {
        $sensor = false;
        switch($status)
        {
            case '1':
                if(isset($this->_sensorsList['enabled'][$f_sensor_code . '_' . $f_sensor_ch]))
                {
                    $sensor = $this->_sensorsList['enabled'][$f_sensor_code . '_' . $f_sensor_ch];
                }
                break;
            case'0':
                if(isset($this->_sensorsList['disabled'][$f_sensor_code . '_' . $f_sensor_ch]))
                {
                    $sensor = $this->_sensorsList['disabled'][$f_sensor_code . '_' . $f_sensor_ch];
                }
                break;
        }
        return $sensor;
    }

    /**
     *
     * @return Boolean
     */
    public function save()
    {
        $sqlQuery = "
            INSERT IGNORE INTO
                `seedclima_station_info`
            SET
                `f_station_code` = " . check_null_val(process_plain_text($this->_fStationCode)) . ",
                `f_date` = " . check_null_val(process_plain_text($this->_fDate)) . ",
                `f_dev_id` = " . check_null_val(process_plain_text($this->_fDevId)) . ",
                `f_name` = " . check_null_val(process_plain_text($this->_fName)) . ",
                `f_descr` = " . check_null_val(process_plain_text($this->_fDescr)) . ",
                `f_info` = " . check_null_val(process_plain_text($this->_fInfo)) . ",
                `f_uid` = " . check_null_val(process_plain_text($this->_fUid)) . ",
                `f_status` = " . check_null_val(process_plain_text($this->_fStatus)) . ",
                `f_create_time` = " . check_null_val(process_plain_text($this->_fCreateTime)) . ",
                `f_master_name` = " . check_null_val(process_plain_text($this->_fMasterName)) . ",
                `f_date_min` = " . check_null_val(process_plain_text($this->_fDateMin)) . ",
                `f_date_max` = " . check_null_val(process_plain_text($this->_fDateMax)) . ",
                `f_date_last_down` = " . check_null_val(process_plain_text($this->_fDateLastDown)) . ",
                `f_date_sens` = " . check_null_val(process_plain_text($this->_fDateSens)) . ",
                `f_date_data` = " . check_null_val(process_plain_text($this->_fDateData)) . ",
                `f_date_conf` = " . check_null_val(process_plain_text($this->_fDateConf)) . ",
                `f_measure_int` = " . check_null_val(process_plain_text($this->_fMeasureInt)) . ",
                `f_data_int` = " . check_null_val(process_plain_text($this->_fDataInt)) . ",
                `f_timezone` = " . check_null_val(process_plain_text($this->_fTimezone)) . ",
                `f_latitude` = " . check_null_val(process_plain_text($this->_fLatitude)) . ",
                `f_longitude` = " . check_null_val(process_plain_text($this->_fLongitude)) . ",
                `f_altitude` = " . check_null_val(process_plain_text($this->_fAltitude)) . ",
                `f_hw_ver_major` = " . check_null_val(process_plain_text($this->_fHwVerMajor)) . ",
                `f_hw_ver_minor` = " . check_null_val(process_plain_text($this->_fHwVerMinor)) . ",
                `f_sw_ver_major` = " . check_null_val(process_plain_text($this->_fSwVerMajor)) . ",
                `f_sw_ver_minor` = " . check_null_val(process_plain_text($this->_fSwVerMinor)) . ",
                `f_sms_warn_numbers` = " . check_null_val(process_plain_text($this->_fSmsWarnNumbers)) . ",
                `f_sms_warn_values` = " . check_null_val(process_plain_text($this->_fSmsWarnValues)) . ",
                `f_gsm_mcc` = " . check_null_val(process_plain_text($this->_fGsmMcc)) . ",
                `f_gsm_mnc` = " . check_null_val(process_plain_text($this->_fGsmMnc)) . ",
                `f_gprs_apn` = " . check_null_val(process_plain_text($this->_fGprsApn)) . ",
                `f_gprs_user_id` = " . check_null_val(process_plain_text($this->_fGprsUserId)) . ",
                `f_gprs_passw` = " . check_null_val(process_plain_text($this->_fGprsPassw)) . ",
                `f_sernum` = " . check_null_val(process_plain_text($this->_fSernum)) . ",
                `f_date_comm` = " . check_null_val(process_plain_text($this->_fDateComm)) . ",
                `f_user_station_name` = " . check_null_val(process_plain_text($this->_fUserStationName)) . ",
                `f_user_name` = " . check_null_val(process_plain_text($this->_fUserName)) . ",
                `custom_name` = " . check_null_val(process_plain_text($this->_customName)) . ",
                `custom_desc` = " . check_null_val(process_plain_text($this->_customDesc)) . ",
                `custom_image` = " . check_null_val(process_plain_text($this->_customImage)) . ",
                `enable_station` = " . check_null_val(process_plain_text($this->_enableStation)) . ",
                `show_in_home` = " . check_null_val(process_plain_text($this->_showInHome)) . ",
                `priority` = " . check_null_val(process_plain_text($this->_priority)) . ",
                `last_update_date` = UNIX_TIMESTAMP(),
                `last_edition_time` = UNIX_TIMESTAMP(),
                `last_editor` = " . check_null_val(process_plain_text($this->_lastEditor)) . "
                 ";

        return sql_select($sqlQuery, $results);
    }

    public function __toString()
    {
        return $this->getName();
    }
}
class Sensor
{
    /** @var Integer */
    private $_rowId;
    /** @var Integer */
    private $_fStationCode;
    /** @var Integer */
    private $_fSensorCh;
    /** @var Integer */
    private $_fSensorCode;
    /** @var Integer */
    private $_fChainCode;
    /** @var Integer */
    private $_fGroupCode;
    /** @var Integer */
    private $_fUnitCode;
    /** @var String */
    private $_fName;
    /** @var String */
    private $_fUnit;
    /** @var Integer */
    private $_fDiv;
    /** @var Integer */
    private $_fMul;
    /** @var Integer 0|1 */
    private $_fValNeg;
    /** @var Integer 0|1 */
    private $_fValLog;
    /** @var Integer 0|1 */
    private $_fValLast;
    /** @var Integer 0|1 */
    private $_fValSum;
    /** @var Integer 0|1 */
    private $_fValAver;
    /** @var Integer 0|1 */
    private $_fValMin;
    /** @var Integer 0|1 */
    private $_fValMax;
    /** @var Integer 0|1 */
    private $_fValTime;
    /** @var Integer 0|1 */
    private $_fValUser;
    /** @var Datetime */
    private $_fCreateTime;
    /** @var String */
    private $_fValAxilary;
    /** @var String */
    private $_fUserApp;
    /** @var String */
    private $_fColor;
    /** @var String */
    private $_fSensorUserName;
    /** @var String */
    private $_fUserUnitCode;
    /** @var String */
    private $_graphType;
    /** @var Float */
    private $_minExpected;
    /** @var Integer 0|1 */
    private $_maxExpected;
    /** @var String */
    private $_customName;
    /** @var String */
    private $_customDesc;
    /** @var String */
    private $_customImage;
    /** @var Integer 0|1 */
    private $_enableSensor;
    /** @var Integer 0|1 */
    private $_chillingOursRelated;
    /** @var Integer 0|1 */
    private $_degreesDayRelated;
    /** @var Integer 0|1 */
    private $_windRoseRelated;
    /** @var Integer */
    private $_priority;
    /** @var Datetime */
    private $_lastEditionTime;
    /** @var Datetime */
    private $_lastUpdateDate;
    /** @var Integer */
    private $_lastEditor;
    /** @var String */
    private $_virtualSensorName = false;
    /** @var String */
    private $_virtualSensorKey;
    /** @var boolean */
    private $_isWindDirectionSensor;

    /**
     *
     * @param <type> $row_id
     * @param <type> $f_station_code
     * @param <type> $f_sensor_ch
     * @param <type> $f_sensor_code
     * @param <type> $f_chain_code
     * @param <type> $f_group_code
     * @param <type> $f_unit_code
     * @param <type> $f_name
     * @param <type> $f_unit
     * @param <type> $f_div
     * @param <type> $f_mul
     * @param <type> $f_val_neg
     * @param <type> $f_val_log
     * @param <type> $f_val_last
     * @param <type> $f_val_sum
     * @param <type> $f_val_aver
     * @param <type> $f_val_min
     * @param <type> $f_val_max
     * @param <type> $f_val_time
     * @param <type> $f_val_user
     * @param <type> $f_create_time
     * @param <type> $f_val_axilary
     * @param <type> $f_user_app
     * @param <type> $f_color
     * @param <type> $f_sensor_user_name
     * @param <type> $f_user_unit_code
     * @param <type> $graph_type
     * @param <type> $min_expected
     * @param <type> $max_expected
     * @param <type> $custom_name
     * @param <type> $custom_desc
     * @param <type> $custom_image
     * @param <type> $enable_sensor
     * @param <type> $chillingOursRelated
     * @param <type> $degreesDayRelated
     * @param <type> $windRoseRelated
     * @param <type> $priority
     * @param <type> $last_edition_time
     * * @param <type> $last_update_date
     * @param <type> $last_editor
     */
    public function __construct($row_id = '', $f_station_code = '', $f_sensor_ch = '', $f_sensor_code = '', $f_chain_code = '', $f_group_code = '', $f_unit_code = '', $f_name = '', $f_unit = '', $f_div = '', $f_mul = '', $f_val_neg = '', $f_val_log = '', $f_val_last = '', $f_val_sum = '', $f_val_aver = '', $f_val_min = '', $f_val_max = '', $f_val_time = '', $f_val_user = '', $f_create_time = '', $f_val_axilary = '', $f_user_app = '', $f_color = '', $f_sensor_user_name = '', $f_user_unit_code = '', $graph_type = '', $min_expected = '', $max_expected = '', $custom_name = '', $custom_desc = '', $custom_image = '', $enable_sensor = '', $chillingOursRelated = 0, $degreesDayRelated = 0, $windRoseRelated = 0, $priority = '', $last_edition_time = '', $last_update_date = '', $last_editor = '')
    {
        $this->_rowId = $row_id;
        $this->_fStationCode = $f_station_code;
        $this->_fSensorCh = $f_sensor_ch;
        $this->_fSensorCode = $f_sensor_code;
        $this->_fChainCode = $f_chain_code;
        $this->_fGroupCode = $f_group_code;
        $this->_fUnitCode = $f_unit_code;
        $this->_fName = $f_name;
        $this->_fUnit = Sensor::fixSensorUnit($f_unit);
        $this->_fDiv = $f_div;
        $this->_fMul = $f_mul;
        $this->_fValNeg = $f_val_neg;
        $this->_fValLog = $f_val_log;
        $this->_fValLast = $f_val_last;
        $this->_fValSum = $f_val_sum;
        $this->_fValAver = $f_val_aver;
        $this->_fValMin = $f_val_min;
        $this->_fValMax = $f_val_max;
        $this->_fValTime = $f_val_time;
        $this->_fValUser = $f_val_user;
        $this->_fCreateTime = $f_create_time;
        $this->_fValAxilary = $f_val_axilary;
        $this->_fUserApp = $f_user_app;
        $this->_fColor = $f_color;
        $this->_fSensorUserName = $f_sensor_user_name;
        $this->_fUserUnitCode = $f_user_unit_code;
        $this->_graphType = $graph_type;
        $this->_minExpected = $min_expected;
        $this->_maxExpected = $max_expected;
        $this->_customName = $custom_name;
        $this->_customDesc = $custom_desc;
        $this->_customImage = $custom_image;
        $this->_enableSensor = $enable_sensor;
        $this->_chillingOursRelated = $chillingOursRelated;
        $this->_degreesDayRelated = $degreesDayRelated;
        $this->_windRoseRelated = $windRoseRelated;
        $this->_priority = $priority;
        $this->_lastEditionTime = $last_edition_time;
        $this->_lastUpdateDate = $last_update_date;
        $this->_lastEditor = $last_editor;
        $this->_isWindDirectionSensor = ((preg_match("/wind[\s| ]+dir/i", $f_name)) ? (true) : (false));
    }

    public static function fixSensorUnit($rawUnit)
    {
        switch($rawUnit){
            case'C':
                return $rawUnit . '&deg;';
                break;
            case'W/mm':
                return 'W/m<sup>2</sup>';
                break;
            default:
                return $rawUnit;
                break;
        }
    }
    
    /**
     *
     * @param Integer $rowId
     * @param Array $fromArrayValues
     * @param Integer $stationCode
     * @param Integer $sensorCode
     * @param Integer $sensorCh
     * @return Sensor
     */
    public static function load($rowId, $fromArrayValues = false, $stationCode = false, $sensorCode = false, $sensorCh = false)
    {
        if($stationCode !== false && $sensorCh !== false && $sensorCode !== false)
        {
            $whereCondition = "
                    `f_station_code` = " . (int) $stationCode . "
                AND
                    `f_sensor_ch` = " . (int) $sensorCh . "
                AND
                    `f_sensor_code` = " . (int) $sensorCode . "
                    ";
        }else
        {
            $whereCondition = "
                    `row_id` = " . (int) $rowId;
        }
        if(is_array($fromArrayValues))
        {
            $loadedDataArray = $fromArrayValues;
        }else
        {
            $BD=new IMETOS();
            $sqlQuery = "
                SELECT
                    `row_id`,
                    `f_station_code`,
                    `f_sensor_ch` ,
                    `f_sensor_code`,
                    `f_chain_code`,
                    `f_group_code`,
                    `f_unit_code`,
                    `f_name`,
                    `f_unit`,
                    `f_div`,
                    `f_mul`,
                    `f_val_neg` ,
                    `f_val_log` ,
                    `f_val_last`,
                    `f_val_sum` ,
                    `f_val_aver`,
                    `f_val_min`,
                    `f_val_max`,
                    `f_val_time`,
                    `f_val_user`,
                    `f_create_time`,
                    `f_val_axilary`,
                    `f_user_app`,
                    `f_color`,
                    `f_sensor_user_name`,
                    `f_user_unit_code`,
                    `graph_type`,
                    `min_expected`,
                    `max_expected` ,
                    `custom_name`,
                    `custom_desc`,
                    `custom_image` ,
                    `enable_sensor` ,
                    `chilling_hours_related` ,
                    `degrees_day_related`,
                    `wind_rose_related`,
                    `priority`,
                    `last_edition_time`,
                    `last_update_date`,
                    `last_editor`
                FROM
                    `seedclima_sensor_info`
                WHERE
                    {$whereCondition}

                LIMIT 1";
            
            if($BD->sql_select($sqlQuery, $result))
            {
                if($BD->rowCount() > 0) 
                {
                    settype($response, 'array');
                    while ($sensorInfo = $result->fetch(PDO::FETCH_ASSOC))
                    {
                        $loadedDataArray = $sensorInfo;
                    }
                }
            }
        }
        if(is_array($loadedDataArray) && count($loadedDataArray) > 0)
        {
            $sensor = new Sensor(
                $loadedDataArray['row_id'],
                @$loadedDataArray['f_station_code'],
                @$loadedDataArray['f_sensor_ch'],
                @$loadedDataArray['f_sensor_code'],
                @$loadedDataArray['f_chain_code'],
                @$loadedDataArray['f_group_code'],
                @$loadedDataArray['f_unit_code'],
                @$loadedDataArray['f_name'],
                @$loadedDataArray['f_unit'],
                @$loadedDataArray['f_div'],
                @$loadedDataArray['f_mul'],
                @$loadedDataArray['f_val_neg'],
                @$loadedDataArray['f_val_log'],
                @$loadedDataArray['f_val_last'],
                @$loadedDataArray['f_val_sum'],
                @$loadedDataArray['f_val_aver'],
                @$loadedDataArray['f_val_min'],
                @$loadedDataArray['f_val_max'],
                @$loadedDataArray['f_val_time'],
                @$loadedDataArray['f_val_user'],
                @$loadedDataArray['f_create_time'],
                @$loadedDataArray['f_val_axilary'],
                @$loadedDataArray['f_user_app'],
                @$loadedDataArray['f_color'],
                @$loadedDataArray['f_sensor_user_name'],
                @$loadedDataArray['f_user_unit_code'],
                @$loadedDataArray['graph_type'],
                @$loadedDataArray['min_expected'],
                @$loadedDataArray['max_expected'],
                @$loadedDataArray['custom_name'],
                @$loadedDataArray['custom_desc'],
                @$loadedDataArray['custom_image'],
                @$loadedDataArray['enable_sensor'],
                @$loadedDataArray['chilling_hours_related'],
                @$loadedDataArray['degrees_day_related'],
                @$loadedDataArray['wind_rose_related'],
                @$loadedDataArray['priority'],
                @$loadedDataArray['last_edition_time'],
                @$loadedDataArray['last_update_date'],
                @$loadedDataArray['last_editor`']
            );
            return $sensor;
        }else
        {
            return false;
        }
    }

    /**
     *
     * @param Integer $filterByStationCode
     * @param Integer $filterBySensorCode
     * @param Integer $filterBySensorChannel
     * @param Integer $filterByStatus 0|1
     * @return Array
     */
    public static function getAll($filterByStationCode = null, $filterBySensorCode = null, $filterBySensorChannel = null, $filterByStatus = null, Array $filterById = null)
    {
        settype($response, 'array');
        settype($filters, 'array');
        if(isset($filterById))
        {
            $filterById = implode(',',$filterById);
            $filters[] = " `row_id` IN ({$filterById})";
        }
        if(isset($filterByStationCode))
        {
            $filters[] = " `f_station_code` = '{$filterByStationCode}'";
        }
        if(isset($filterBySensorCode))
        {
            $filters[] = " `f_sensor_code` = '{$filterBySensorCode}'";
        }
        if(isset($filterBySensorChannel))
        {
            $filters[] = " `f_sensor_ch` = '{$filterBySensorChannel}'";
        }
        if(isset($filterByStatus))
        {
            $filters[] = " `enable_sensor` = '{$filterByStatus}'";
        }
        $whereCondition = count($filters) > 0 ? 'WHERE ' . implode(' AND ', $filters) : '';
        $sqlQuery = "
            SELECT  *
            FROM    `seedclima_sensor_info`
            {$whereCondition} 
            ORDER BY
                `priority`,`custom_name`,`f_sensor_user_name`";
        $BD=new IMETOS();
        if($BD->sql_select($sqlQuery, $result))
        {
            if($BD->get_rowCount() > 0)
            {
                while($sensorInfo = $result->fetch(PDO::FETCH_ASSOC))
                {
                    $sensor = Sensor::load(null, $sensorInfo);
                    $response[$sensor->getSensorCode() . '_' . $sensor->getSensorCh()] = $sensor;
                }
            }
            return $response;
        }
        return false;
    }

    /** @return */
    public function getId()
    {
        return $this->_rowId;
    }

    /** @return */
    public function getRowId()
    {
        return $this->getId();
    }

    /** @return */
    public function getStationCode()
    {
        return $this->_fStationCode;
    }

    /** @return */
    public function getSensorCh()
    {
        return $this->_fSensorCh;
    }

    /** @return */
    public function getSensorCode()
    {
        return $this->_fSensorCode;
    }

    /** @return */
    public function getChainCode()
    {
        return $this->_fChainCode;
    }

    /** @return */
    public function getGroupCode()
    {
        return $this->_fGroupCode;
    }

    /** @return */
    public function getUnitCode()
    {
        return $this->_fUnitCode;
    }

    /** @return */
    public function getFName()
    {
        return $this->_fName;
    }

    /** @return */
    public function getName()
    {
        if($this->getVirtualSensorName())
        {
            $sensorName = $this->getVirtualSensorName();
        }elseif($this->getCustomName())
        {
            $sensorName = $this->getCustomName();
        }elseif($this->getSensorUserName())
        {
            $sensorName = $this->getSensorUserName();
        }else
        {
            $sensorName = $this->_fName;
        }
        return $sensorName;
    }

    /** @return */
    public function getUnit()
    {
        return $this->_fUnit;
    }

    /** @return */
    public function getDiv()
    {
        return $this->_fDiv;
    }

    /** @return */
    public function getMul()
    {
        return $this->_fMul;
    }

    /** @return */
    public function getValNeg()
    {
        return $this->_fValNeg;
    }

    /** @return */
    public function getValLog()
    {
        return $this->_fValLog;
    }

    /** @return */
    public function getValLast()
    {
        return $this->_fValLast;
    }

    /** @return */
    public function getValSum()
    {
        return $this->_fValSum;
    }

    /** @return */
    public function getValAver()
    {
        return $this->_fValAver;
    }

    /** @return */
    public function getValMin()
    {
        return $this->_fValMin;
    }

    /** @return */
    public function getValMax()
    {
        return $this->_fValMax;
    }

    /** @return */
    public function getValTime()
    {
        return $this->_fValTime;
    }

    /** @return */
    public function getValUser()
    {
        return $this->_fValUser;
    }

    /** @return */
    public function getCreateTime()
    {
        return $this->_fCreateTime;
    }

    /** @return */
    public function getValAxilary()
    {
        return $this->_fValAxilary;
    }

    /** @return */
    public function getUserApp()
    {
        return $this->_fUserApp;
    }

    /** @return */
    public function getColor()
    {
        return $this->_fColor;
    }

    /** @return */
    public function getSensorUserName()
    {
        return $this->_fSensorUserName;
    }

    /** @return */
    public function getUserUnitCode()
    {
        return $this->_fUserUnitCode;
    }

    /** @return */
    public function getGraphType()
    {
        if($this->_graphType)
        {
            return $this->_graphType;
        }else
        {
            if($this->getValSum())
            {
                return 'bar_3d';
            }elseif($this->getValAver() || $this->isWindDirectionSensor())
            {
                return 'area';
            }elseif($this->getValTime())
            {
                return 'timeLine';
            }elseif($this->getValLast())
            {
                return 'bar_glass';
            }else
            {
                return 'line';
            }
        }
    }

    /** @return */
    public function getMinExpected()
    {
        return $this->_minExpected;
    }

    /** @return */
    public function getMaxExpected()
    {
        return $this->_maxExpected;
    }

    /** @return */
    public function getCustomName()
    {
        return $this->_customName;
    }

    /** @return */
    public function getCustomDesc()
    {
        return $this->_customDesc;
    }

    /** @return */
    public function getCustomImage()
    {
        return $this->_customImage ? $this->_customImage : $this->getSensorCode() . '.png';
    }

    /** @return */
    public function isEnableSensor()
    {
        return $this->_enableSensor;
    }

    /** @return */
    public function isChillingRelatedSensor()
    {
        return $this->_chillingOursRelated;
    }

    /** @return */
    public function isDegreesDayRelatedSensor()
    {
        return $this->_degreesDayRelated;
    }

    /** @return */
    public function isWindRoseRelatedSensor()
    {
        return $this->_windRoseRelated;
    }

    /** @return */
    public function isWindDirectionSensor()
    {
        return $this->_isWindDirectionSensor;
    }

    /** @return */
    public function getPriority()
    {
        return $this->_priority;
    }

    /** @return */
    public function getLastEditionTime()
    {
        return $this->_lastEditionTime;
    }

    /** @return */
    public function getLastUpdateDate()
    {
        return $this->_lastUpdateDate;
    }

    /** @return */
    public function getLastEditor()
    {
        return $this->_lastEditor;
    }

    /**
     *
     * @param String $attributeName
     * @return Mixed 
     */
    public function getAvailableSensorAttribute($attributeName)
    {
        switch($attributeName)
        {
            case'row_id':
                return $this->getId();
                break;
            case'f_station_code':
                return $this->getStationCode();
                break;
            case'f_sensor_ch':
                return $this->getSensorCh();
                break;
            case'f_sensor_code':
                return $this->getSensorCode();
                break;
            case'f_chain_code':
                return $this->getChainCode();
                break;
            case'f_group_code':
                return $this->getGroupCode();
                break;
            case'f_unit_code':
                return $this->getUnitCode();
                break;
            case'f_name':
                return $this->getName();
                break;
            case'f_unit':
                return $this->getUnit();
                break;
            case'f_div':
                return $this->getDiv();
                break;
            case'f_mul':
                return $this->getMul();
                break;
            case'f_val_neg':
                return $this->getValNeg();
                break;
            case'f_val_log':
                return $this->getValLog();
                break;
            case'f_val_last':
                return $this->getValLast();
                break;
            case'f_val_sum':
                return $this->getValSum();
                break;
            case'f_val_aver':
                return $this->getValAver();
                break;
            case'f_val_min':
                return $this->getValMin();
                break;
            case'f_val_max':
                return $this->getValMax();
                break;
            case'f_val_time':
                return $this->getValTime();
                break;
            case'f_val_user':
                return $this->getValUser();
                break;
            case'f_create_time':
                return $this->getCreateTime();
                break;
            case'f_val_axilary':
                return $this->getValAxilary();
                break;
            case'f_user_app':
                return $this->getUserApp();
                break;
            case'f_color':
                return $this->getColor();
                break;
            case'f_sensor_user_name':
                return $this->getSensorUserName();
                break;
            case'f_user_unit_code':
                return $this->getUserUnitCode();
                break;
            case'graph_type':
                return $this->getGraphType();
                break;
            case'min_expected':
                return $this->getMinExpected();
                break;
            case'max_expected':
                return $this->getMaxExpected();
                break;
            case'custom_name':
                return $this->getCustomName();
                break;
            case'custom_desc':
                return $this->getCustomDesc();
                break;
            case'custom_image':
                return $this->getCustomImage();
                break;
            case'enable_sensor':
                return $this->isEnableSensor();
                break;
            case'chilling_hours_related':
                return $this->isChillingRelatedSensor();
                break;
            case'degrees_day_related':
                return $this->isDegreesDayRelatedSensor();
                break;
            case'wind_rose_related':
                return $this->isWindRoseRelatedSensor();
                break;
            case'priority':
                return $this->getPriority();
                break;
            case'last_edition_time':
                return $this->getLastEditionTime();
                break;
            case'last_update_date':
                return $this->getLastUpdateDate();
                break;
            case'last_editor':
                return $this->getLastEditor();
                break;
            case'virtual_sensor_name':
                return $this->getVirtualSensorName();
                break;
            default:
                return false;
                break;
        }
    }

    /** @return String */
    public function getVirtualSensorKey()
    {
        return $this->_virtualSensorKey;
    }

    /** @return String */
    public function getVirtualSensorName()
    {
        return $this->_virtualSensorName;
    }

    /** @param String */
    public function setVirtualSensorName($virtualSensorName)
    {
        $this->_virtualSensorName = $virtualSensorName;
    }

    /** @param String */
    public function setVirtualSensorUnit($virtualSensorUnit)
    {
        $this->_fUnit = $virtualSensorUnit;
    }

    /** @param String */
    public function setVirtualSensorKey($virtualSensorKey)
    {
        $this->_virtualSensorKey = $virtualSensorKey;
    }

    /** @param String */
    public function setSensorIcon($virtualSensorIcon)
    {
        $this->_customImage = $virtualSensorIcon;
    }

    /** @param String */
    public function setCustomSensorColor($hexDexColorCode)
    {
        $this->_fColor = str_replace('#', '', $hexDexColorCode);
    }

    /**
     * @return Boolean
     */
    public function save()
    {
        $sqlQuery = "
            INSERT IGNORE INTO
                `seedclima_sensor_info`
            SET
                `f_station_code` = " . check_null_val(process_plain_text($this->_fStationCode)) . ",
                `f_sensor_ch` = " . check_null_val(process_plain_text($this->_fSensorCh)) . ",
                `f_sensor_code` = " . check_null_val(process_plain_text($this->_fSensorCode)) . ",
                `f_chain_code` = " . check_null_val(process_plain_text($this->_fChainCode)) . ",
                `f_group_code` = " . check_null_val(process_plain_text($this->_fGroupCode)) . ",
                `f_unit_code` = " . check_null_val(process_plain_text($this->_fUnitCode)) . ",
                `f_name` = " . check_null_val(process_plain_text($this->_fName)) . ",
                `f_unit` = " . check_null_val(process_plain_text($this->_fUnit)) . ",
                `f_div` = " . check_null_val(process_plain_text($this->_fDiv)) . ",
                `f_mul` = " . check_null_val(process_plain_text($this->_fMul)) . ",
                `f_val_neg` = " . check_null_val(process_plain_text($this->_fValNeg)) . ",
                `f_val_log` = " . check_null_val(process_plain_text($this->_fValLog)) . ",
                `f_val_last` = " . check_null_val(process_plain_text($this->_fValLast)) . ",
                `f_val_sum` = " . check_null_val(process_plain_text($this->_fValSum)) . ",
                `f_val_aver` = " . check_null_val(process_plain_text($this->_fValAver)) . ",
                `f_val_min` = " . check_null_val(process_plain_text($this->_fValMin)) . ",
                `f_val_max` = " . check_null_val(process_plain_text($this->_fValMax)) . ",
                `f_val_time` = " . check_null_val(process_plain_text($this->_fValTime)) . ",
                `f_val_user` = " . check_null_val(process_plain_text($this->_fValUser)) . ",
                `f_create_time` = " . check_null_val(process_plain_text($this->_fCreateTime)) . ",
                `f_val_axilary` = " . check_null_val(process_plain_text($this->_fValAxilary)) . ",
                `f_user_app` = " . check_null_val(process_plain_text($this->_fUserApp)) . ",
                `f_color` = " . check_null_val(process_plain_text($this->_fColor)) . ",
                `f_sensor_user_name` = " . check_null_val(process_plain_text($this->_fSensorUserName)) . ",
                `f_user_unit_code` = " . check_null_val(process_plain_text($this->_fUserUnitCode)) . ",
                `graph_type` = " . check_null_val(process_plain_text($this->_graphType)) . ",
                `min_expected` = " . check_null_val(process_plain_text($this->_minExpected)) . ",
                `max_expected` = " . check_null_val(process_plain_text($this->_maxExpected)) . ",
                `custom_name` = " . check_null_val(process_plain_text($this->_customName)) . ",
                `custom_desc` = " . check_null_val(process_plain_text($this->_customDesc)) . ",
                `custom_image` = " . check_null_val(process_plain_text($this->_customImage)) . ",
                `enable_sensor` = " . check_null_val(process_plain_text($this->_enableSensor)) . ",
                `chilling_hours_related` = " . check_null_val(process_plain_text($this->_chillingOursRelated)) . ",
                `degrees_day_related` = " . check_null_val(process_plain_text($this->_degreesDayRelated)) . ",
                `wind_rose_related` = " . check_null_val(process_plain_text($this->_windRoseRelated)) . ",
                `priority` = " . check_null_val(process_plain_text($this->_priority)) . ",
                `last_edition_time` = UNIX_TIMESTAMP(),
                `last_update_date` = UNIX_TIMESTAMP(),
                `last_editor` = " . check_null_val(process_plain_text($this->_lastEditor)) . "
            ";
        return sql_select($sqlQuery, $results);
    }

    /**
     * Used when a clone is needed for Vitual Sensors related to hardwre sensors
     * @return copy of Sensor
     */
    function __clone()
    {
	
    }

    public function __toString()
    {
        return $this->getName();
    }
    
}
class JSON_IMETOS
{
    const FIELDCLIMATE = "http://fieldclimate.com/pikernel/api/";
    
	private $user_name;
	private $user_passw;
	private $station_name;
	private $num_row;
	private $dt_from;
	private $dt_to;
    
    private $error;
    
    function __construct($username, $password)
    {
        $this->error=false;
        $this->user_name=str_replace(chr(32),"%20",$username);
        $this->user_passw=str_replace(chr(32),"%20",$password);
        $web=self::FIELDCLIMATE."CIDIUser/GetInfo?user_name=".$this->user_name."&user_passw=".$this->user_passw;
        if($matriz=$this->curl_answer($web))
        {
            return true;
        }
    }
    
    public function set_user_name($valor="")
    {
        $this->user_name=str_replace(chr(32),"%20",$valor);
    }
    public function set_user_passw($valor="")
    {
        $this->user_passw=str_replace(chr(32),"%20",$valor);
    }
    public function set_station_name($valor="")
    {
        $this->station_name=str_replace(chr(32),"%20",$valor);
    }
    public function set_num_row($valor="")
    {
        $this->num_row=$valor;
    }
    public function set_dt_from($valor="")
    {
        $this->dt_from=str_replace(chr(32),"%20",$valor);
    }
    public function set_dt_to($valor="")
    {
        $this->dt_to=str_replace(chr(32),"%20",$valor);
    }
    public function get_error()
    {
        return $this->error;
    }
    
	public function StationListAll()
    {
		//listado de estaciones
		$web=self::FIELDCLIMATE."CIDIStationList/GetFirst?user_name=".str_replace(chr(32),"%20",$this->user_name)."&user_passw=".str_replace(chr(32),"%20",$this->user_passw)."&row_count=".$this->num_row."&sort_type=0&debug=0";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
	}

	public function StationSensors()
    {
		//listado de estaciones
		$web=self::FIELDCLIMATE."CIDIStationSensors/Get?user_name=".str_replace(chr(32),"%20",$this->user_name)."&user_passw=".str_replace(chr(32),"%20",$this->user_passw)."&station_name=".$this->station_name."&debug=0&show_user_app=1";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
	}

	public function StationDataGetFirst()
    {
		$web=self::FIELDCLIMATE."CIDIStationData/GetFirst?user_name=".$this->user_name."&user_passw=".$this->user_passw."&station_name=".$this->station_name."&row_count=".$this->num_row."&group_code=0&debug=0";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
	}

	function StationDataGetMinMaxDate()
    {
		$web=self::FIELDCLIMATE."CIDIStationData/GetMinMaxDate?user_name=".str_replace(chr(32),"%20",$this->user_name)."&user_passw=".str_replace(chr(32),"%20",$this->user_passw)."&station_name=".$this->station_name."&debug=0";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
	}

	function GetLast()
    {
		$web=self::FIELDCLIMATE."CIDIStationData/GetLast?user_name=".str_replace(chr(32),"%20",$this->user_name)."&user_passw=".str_replace(chr(32),"%20",$this->user_passw)."&station_name=".$this->station_name."&row_count=".$this->num_row."&group_code=0&debug=0";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
	}

	function StationDataGetFromDate()
    {
		$web=self::FIELDCLIMATE."CIDIStationData/GetFromDate?user_name=".str_replace(chr(32),"%20",$this->user_name)."&user_passw=".str_replace(chr(32),"%20",$this->user_passw)."&station_name=".$this->station_name."&row_count=".$this->num_row."&group_code=0&dt_from=".$this->dt_from."&debug=0";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
    }

	function StationDataGetNext()
    {
		$web=self::FIELDCLIMATE."CIDIStationData/GetNext?user_name=".$this->user_name."&user_passw=".$this->user_passw."&row_count=".$this->num_row."&station_name=".$this->station_name."&group_code=0&dt_to=".str_replace(chr(32),"%20",$this->dt_to)."&debug=0";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
	}
    
    private function curl_answer($web)
    {
		//$res = curl_init($web);
        $ch = curl_init();
        /**
         * TRUE to fail silently if the HTTP code returned is greater than or equal to 400.
         * The default behavior is to return the page normally, ignoring the code.
         */
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);

        /**
         * set the URL to fetch.
         * This can also be set when initializing a session with curl_init().
         */
        curl_setopt($ch, CURLOPT_URL, $web);

        /**
         * TRUE to return the transfer as a string of the return value
         * of curl_exec() instead of outputting it out directly.
         */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        

		$remoteString=curl_exec($ch);
        if (!$remoteString)
        {
            throw new Exception("HTTP code returned is greater than or equal to 400");
        }else
        {
            $remoteString2Json = json_decode($remoteString);
            if (function_exists('json_last_error'))
            {
                switch (json_last_error ()):
                    case JSON_ERROR_DEPTH:
                        throw new Exception("JSON_ERROR_DEPTH - Maximum stack depth exceeded <br>########{$remoteString }########");
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        throw new Exception("JSON_ERROR_CTRL_CHAR - Unexpected control character found <br>########{$remoteString }########");
                        break;
                    case JSON_ERROR_SYNTAX:
                        throw new Exception("JSON_ERROR_SYNTAX - Syntax error, malformed JSON <br>########{$remoteString }########");
                        break;
                    /*  IMPLEMENT THIS ON PHP 5.3.1 servers
                      case JSON_ERROR_UTF8:
                      throw new Exception("Malformed UTF-8 characters, possibly incorrectly encoded");
                      break;
                     */
                    case JSON_ERROR_NONE:
                        return $this->_handleLoginErrors($remoteString2Json);
                        break;
                endswitch;
            }else 
            {
                return $this->_handleLoginErrors($remoteString2Json);
            }
        }
        /**
         * Close curl resource to free up system resources
         */        
		curl_close($ch);
    }
    /**
     * @param mixed $jsonObject
     */
    private function _handleLoginErrors($jsonObject)
    {
        if (isset($jsonObject->faultcode))
        {
            /*
            throw new Exception("<blockquote><strong>CIDIData Fault code</strong> - ".$jsonObject->faultcode
                    . "<br><strong>CIDIData Fault actor</strong> - " . $jsonObject->faultactor
                    . "<br><strong>CIDIData Fault string</strong> - " . $jsonObject->faultstring
                    . "<br><strong>CIDIData Fault detail</strong> - " . $jsonObject->faultdetail
                    . ((isset($this->_stationName)) ? ("<br><strong>Requested Station</strong> - " . $this->_stationName ) : (""))
                    . (($jsonObject->faultcode == 5) ? ("<br><strong>Username or Password may be wrong</strong>" ) : (""))
                    . "</blockquote>"
            );
             * 
             */
            $this->error=true;
        }else
        {
            return $jsonObject;
        }
    }    
}
class Config_Station
{
    private $id_usuario;        // id_usuario de la tabla usuarios
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
    
    function __construct($id_usuario='',$f_station_code='',$activa,$sensores='',$periodo='',
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
        $this->id_usuario=$id_usuario;
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
        return $this->id_usuario;
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
    public static function load($id_usuario="",$f_station_code="",$fromArrayValues=false)
    {
        if(is_array($fromArrayValues))
        {
            $loadedDataArray = $fromArrayValues;
        }else
        {
            $sql="  SELECT  `id`,
                            `id_usuario`,
                            `f_station_code`,
                            `activa`,
                            `info`
                    FROM    `estaciones`
                    WHERE   `f_station_code`={$f_station_code} AND 
                            `id_usuario`={$id_usuario}";
            $loadedDataArray="";
            if(sql_select($sql, $consulta))
            {
                if($consulta->rowCount() > 0)
                {
                    while($configInfo = $consulta->fetch(PDO::FETCH_ASSOC))
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
                $q_config = new Config_Station($id_usuario,$f_station_code,$activa,$sensores,$periodo,
                        $periodo_fecha_inicial,$periodo_fecha_final,$tipo_archivo,$separador,
                        $encabezado,$archivo);
                return $q_config;
            }
        }
        $q_config = new Config_Station($id_usuario,$f_station_code,"1");
        return $q_config;
    }
}
?>