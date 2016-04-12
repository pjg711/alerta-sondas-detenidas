<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Station
 *
 * @author pablo
 */
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
     * @param type $userid is integer
     * @param type $server is string
     * @param type $database is string
     * @param type $username is string 
     * @param type $password is string
     * @return array de objetos Station
     */
    //public static function getAll(IMETOS $BD, $userid=0,$server="",$database="",$username="",$password="")
    public static function getAll(IMETOS $BD)
    {
        $query="SELECT
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
                    `seedclima_station_info`";
        settype($response, 'array');
        if($BD->sql_select($query, $result))
        {
            if($BD->getRowCount() > 0)
            {
                while($stationInfo = $result->fetch(PDO::FETCH_ASSOC))
                {
                    //echo "pase por aca 3a--->{$stationInfo['f_station_code']}<br>";
                    $station = Station::load($BD, $stationInfo['f_station_code']);
                    $response[$stationInfo['f_station_code']]= $station;
                }
                return $response;
            }
        }
        return false;
    }

    /**
     *
     * @param Integer $f_station_code
     * @param Array $fromArrayValues
     * @return Station
     */
    public static function load(IMETOS $BD, $f_station_code, $fromArrayValues = false)
    {
        if(is_array($fromArrayValues))
        {
            $loadedDataArray = $fromArrayValues;
        }else
        {
            $query = "
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
            if($BD->sql_select($query, $result))
            {
                if($BD->getRowCount() > 0)
                {
                    settype($response, 'array');
                    while($stationInfo = $result->fetch(PDO::FETCH_ASSOC))
                    {
                        //echo "bien<br>";
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
            $station->_setLastDataRetrievedTime($BD);
            $station->_setStatusReport();
            return $station;
        }else
        {
            return false;
        }
    }

    /**
     * 
     * @return type
     */
    public function getRowId()
    {
        return $this->_rowId;
    }

    /**
     * 
     * @return type
     */
    public function getStationCode()
    {
        return $this->_fStationCode;
    }

    /**
     * 
     * @return type
     */
    public function getDate()
    {
        return $this->_fDate;
    }

    /**
     * 
     * @return type
     */
    public function getDevId()
    {
        return $this->_fDevId;
    }

    /**
     * 
     * @return type
     */
    public function getFName()
    {
        return $this->_fName;
    }

    /**
     * 
     * @return type
     */
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

    /**
     * 
     * @return type
     */
    public function getDescr()
    {
        return $this->_fDescr;
    }

    /**
     * 
     * @return type
     */
    public function getInfo()
    {
        return $this->_fInfo;
    }

    /**
     * 
     * @return type
     */
    public function getUid()
    {
        return $this->_fUid;
    }

    /**
     * 
     * @return type
     */
    public function getStatus()
    {
        return $this->_fStatus;
    }

    /**
     * 
     * @return type
     */
    public function getCreateTime()
    {
        return $this->_fCreateTime;
    }

    /**
     * 
     * @return type
     */
    public function getMasterName()
    {
        return $this->_fMasterName;
    }

    /**
     * 
     * @return type
     */
    public function getDateMin()
    {
        return $this->_fDateMin;
    }

    /**
     * 
     * @return type
     */
    public function getDateMax()
    {
        return $this->_fDateMax;
    }

    /**
     * 
     * @return type
     */
    public function getDateLastDown()
    {
        return $this->_fDateLastDown;
    }

    /**
     * 
     * @return type
     */
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

    /**
     * 
     * @return type
     */
    public function getGprsPassw()
    {
        return $this->_fGprsPassw;
    }

    /**
     * 
     * @return type
     */
    public function getSernum()
    {
        return $this->_fSernum;
    }

    /**
     * 
     * @return type
     */
    public function getDateComm()
    {
        return $this->_fDateComm;
    }

    /**
     * 
     * @return type
     */
    public function getUserStationName()
    {
        return $this->_fUserStationName;
    }

    /**
     * 
     * @return type
     */
    public function getUserName()
    {
        return $this->_fUserName;
    }

    /**
     * 
     * @return type
     */
    public function getCustomName()
    {
        return $this->_customName;
    }

    /**
     * 
     * @return type
     */
    public function getCustomDesc()
    {
        return $this->_customDesc;
    }

    /**
     * 
     * @return type
     */
    public function getCustomImage()
    {
        return $this->_customImage ? $this->_customImage : 'default_station.jpg';
    }

    /**
     * 
     * @return type
     */
    public function isEnabled()
    {
        return $this->_enableStation;
    }

    /**
     * 
     * @return type
     */
    public function getShowInHome()
    {
        return $this->_showInHome;
    }

    /**
     * 
     * @return type
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * 
     * @return type
     */
    public function getLastUpdateDate()
    {
        return $this->_lastUpdateDate;
    }

    /**
     * 
     * @return type
     */
    public function getLastEditionTime()
    {
        return $this->_lastEditionTime;
    }

    /**
     * 
     * @return type
     */
    public function getLastEditor()
    {
        return $this->_lastEditor;
    }

    /**
     * 
     * @return type
     */
    public function getStationStatusReport()
    {
        return $this->_statusReport;
    }
    /**
     * 
     * @return type
     */
    public function getStationStatus()
    {
        return $this->_status;
    }
    
    /**
     * 
     */
    private function _setLastDataRetrievedTime(IMETOS $BD)
    {
        //echo "pase por aca 4<br>";
        //$BD=new IMETOS();
        $query = "
            SELECT
                MAX(`last_read_time`)  as 'last_read_time'
            FROM
                `seedclima_station_data_retrieve_info`
            WHERE
                `f_station_code` = {$this->getStationCode()}";
        if($BD->sql_select($query, $results))
        {
            if($BD->getRowCount() > 0)
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
    //public function loadSensors($status = null, Array $filterById = null)
    public function loadSensors(IMETOS $BD, Array $filterById = null)
    {
        $this->_sensorsList['enabled'] = Sensor::getAll($BD, $this->getStationCode(), null, null, 1, $filterById);
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
        $query = "
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

        return sql_select($query, $results);
    }
    /**
     * 
     * @return type
     */
    public function __toString()
    {
        return $this->getName();
    }
    
    public function export()
    {
        
    }

}