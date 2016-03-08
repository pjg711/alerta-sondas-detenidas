<?php
/**
 * Seedclima Project :: Based on Kite Box CMS engine developed by Jaxolotl Design
 *
 * File: FieldclimateSync.php
 *
 * Kite Box Eukaryotes V6.x: An Evolution Jump on Content Management
 * Eukaryotes is an open source Advanced Multilanguage Content Manager
 *
 * Graphic Design, MySQL & PHP Developer: Copyright (c) 2009-2010 Javier Valderrama
 *
 * @package       Cron
 * @author        Javier Valderrama  <javier@jaxolotl-design.com>
 * @link          http://www.jaxolotl-design.com
 * @copyright     2009-2010 Javier Valderrama
 * @version       1.0
 * @filesource
 */

/**
 * FieldclimateSync Class
 *
 * Retrieves from Fieldclimate remote Json Interface Platform  all Station and Sensor information
 * for the given username and password and save it into the local Database.
 *
 * This class is called on site start up to feed the database and then
 * called with cron procedures to update it regularly.
 */
class FieldclimateSync
{

    /**
     * This is the main Remote URI to make the requests
     * test url on http://fieldclimate.com/cidiweb.php
     *
     * @var string
     */
    private $_request_remote_uri = "http://fieldclimate.com/api/index.php";
    /** @var String */
    private $_userName;
    /** @var String */
    private $_password;
    /** @var String */
    private $_stationName;
    /** @var Integer */
    private $_groupCode;
    /** @var Boolean */
    private $_debugMode = false;
    /** @var Integer */
    private $_rowCount;
    /** @var DateTime Format Y-m-dTH:i:s */
    private $_dateFrom;
    /** @var Integer */
    private $_sortType;
    /** @var String */
    private $_outputType;
    /** @var DateTime Format Y-m-dTH:i:s */
    private $_forcedStartingDate;
    /** @var Array $loadedSensors */
    private $_loadedSensors = array();

    /**
     *
     * @param String $userName
     * @param String $password
     * @param DateTime $dateFrom  Format Y-m-dTH:i:s
     * @param Integer $rowCount
     * @param String $outputType (print|log)
     * @param DateTime $forcedStartingDate Format Y-m-dTH:i:s
     */
    public function __construct($userName, $password, $dateFrom, $rowCount = 500, $outputType = false, $forcedStartingDate = false)
    {
        $this->_userName = urlencode($userName);
        $this->_password = urlencode($password);
        $this->_dateFrom = $dateFrom;
        $this->_rowCount = (int) $rowCount;
        $this->_outputType = $outputType;
        $this->_forcedStartingDate = $forcedStartingDate;
    }

    /**
     * This is the main CURL execution function to retrieve data from remote URI
     *
     * @param  string $remote_uri   The remote uri string to request
     * @return string               The result string retrieved
     *
     * @see                          http://php.net/manual/en/function.curl-setopt.php
     */
    private function _executeFcCurl($remote_uri)
    {
        /**
         * Create curl resource handler
         * @var resource
         */

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
        curl_setopt($ch, CURLOPT_URL, $remote_uri);

        /**
         * TRUE to return the transfer as a string of the return value
         * of curl_exec() instead of outputting it out directly.
         */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        /**
         * Assign the curl_exec() output string to $remote_string
         * @var mixed
         */
        $remoteString = curl_exec($ch);

        /**
         * Handle exeptions
         */
        if(!$remoteString)
        {

            throw new Exception("HTTP code returned is greater than or equal to 400");
        }else
        {
            $remoteString2Json = json_decode($remoteString);
            if(function_exists('json_last_error'))
            {
                switch(json_last_error()):
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
        if(isset($jsonObject->faultcode))
        {
            throw new Exception("<blockquote><strong>CIDIData Fault code</strong> - " . $jsonObject->faultcode
                    . "<br><strong>CIDIData Fault actor</strong> - " . $jsonObject->faultactor
                    . "<br><strong>CIDIData Fault string</strong> - " . $jsonObject->faultstring
                    . "<br><strong>CIDIData Fault detail</strong> - " . $jsonObject->faultdetail
                    . ((isset($this->_stationName)) ? ("<br><strong>Requested Station</strong> - " . $this->_stationName ) : (""))
                    . (($jsonObject->faultcode == 5) ? ("<br><strong>Username or Password may be wrong</strong>" ) : (""))
                    . "</blockquote>"
            );
        }else
        {
            return $jsonObject;
        }
    }

    /**
     * put your comment there...
     *
     * @param mixed $retrieve_what
     */
    private function _buildCurlRequest($retrieve_what)
    {
        switch ($retrieve_what):
            case'Get_All_Stations': // CIDIStationList_GetNext non implemented yet
                $remoteUri = $this->_request_remote_uri
                        . "?action=CIDIStationList3_GetFirst"
                        . "&user_name=" . $this->_userName
                        . "&user_passw=" . $this->_password
                        . "&row_count=" . $this->_rowCount
                        . ($this->_debugMode ? "&debug=" . $this->_debugMode : '')
                        . (($this->_dateFrom) ? ("&dt_from=" . $this->_dateFrom) : (''))
                ;
                break;
            
            case'Get_Station_Info':
                $remoteUri = $this->_request_remote_uri
                        . "?action=CIDIStationConfig2_Get"
                        . "&user_name=" . $this->_userName
                        . "&user_passw=" . $this->_password
                        . "&station_name=" . $this->_stationName
                        . ($this->_debugMode ? "&debug=" . $this->_debugMode : '')
                ;
                break;
            
            case'Get_Station_Sensors':
                $remoteUri = $this->_request_remote_uri
                        . "?action=CIDIStationSensors3_Get"
                        . "&user_name=" . $this->_userName
                        . "&user_passw=" . $this->_password
                        . "&station_name=" . $this->_stationName
                ;
                break;

            case'Get_Data_From_Start':
                $remoteUri = $this->_request_remote_uri
                        . "?action=CIDIStationData2_GetFromDate"
                        . "&user_name=" . $this->_userName
                        . "&user_passw=" . $this->_password
                        . "&station_name=" . $this->_stationName
                        . "&row_count=" . $this->_rowCount
                ;
                break;

            case'Get_Data_From_Date':

                $remoteUri = $this->_request_remote_uri
                        . "?action=CIDIStationData3_GetFromDate"
                        . "&user_name=" . $this->_userName
                        . "&user_passw=" . $this->_password
                        . "&station_name=" . $this->_stationName
                        . "&row_count=" . $this->_rowCount
                        . "&dt_from=" . $this->_dateFrom
                        . "&show_user_app=1"
                ;
                break;
        endswitch;
        return $remoteUri;
    }

    /**
     *
     * @param Mixed $contents
     */
    private function _proccessOutput($contents)
    {
        switch($this->_outputType)
        {
            case'print':
                echo "\n";
                print_r($contents);
                ob_flush();
                flush();
                break;
        }
    }

    /**
     * @param mixed $f_name
     * @return int
     */
    private function _stationExists($f_name)
    {
        $query = "
            SELECT
                `row_id`
            FROM
                `" . DBT_PREFIX . "station_info`
            WHERE
                `f_name` = '{$f_name}'
            LIMIT
                1
            ";
        sql_select($query, $results);
        return mysql_num_rows($results);
    }

    /**

     * @param mixed $f_stationCode
     * @return mixed last_read_time(timestamp) or false
     */
    private function _stationSensorsDataHistoryExists($f_stationCode)
    {
        $query = "
            SELECT
                MAX(`last_read_time`) as 'last_read_time'
            FROM
                `" . DBT_PREFIX . "station_data_retrieve_info`
            WHERE
                `f_station_code` = {$f_stationCode}
            AND
                `last_read_time_success` = 1
            ";
        sql_select($query, $results);
        if(mysql_num_rows($results) > 0)
        {
            $row = mysql_fetch_array($results);
            return $row['last_read_time'];
        }else
        {
            return false;
        }
    }

    /**
     * @param mixed $f_stationCode
     * @param mixed $f_sensorCh
     * @param mixed $f_sensorCode
     * @return int
     */
    private function _sensorExists($f_stationCode, $f_sensor_code, $f_sensor_ch)
    {
        $query = "
            SELECT
                `row_id`
            FROM
                `" . DBT_PREFIX . "sensor_info`
            WHERE
                `f_station_code` = '{$f_stationCode}'
            AND
                `f_sensor_ch` = '{$f_sensor_ch}'
            AND
                `f_sensor_code` = '{$f_sensor_code}'
            LIMIT 1";
        sql_select($query, $results);
        return mysql_num_rows($results);
    }

    /**
     * put your comment there...
     *
     * @param mixed $stationInformationArray
     */
    private function _addStation($stationInformationArray)
    {
        $fiedsList = array(
                'f_station_code', 'f_date', 'f_dev_id', 'f_name', 'f_descr', 'f_info', 'f_uid',
                'f_status', 'f_create_time', 'f_master_name', 'f_date_min', 'f_date_max', 'f_date_last_down',
                'f_date_sens', 'f_date_data', 'f_date_conf', 'f_measure_int', 'f_data_int', 'f_timezone',
                'f_latitude', 'f_longitude', 'f_altitude', 'f_hw_ver_major', 'f_hw_ver_minor', 'f_sw_ver_major',
                'f_sw_ver_minor', 'f_sms_warn_numbers', 'f_sms_warn_values', 'f_gsm_mcc', 'f_gsm_mnc', 'f_gprs_apn',
                'f_gprs_user_id', 'f_gprs_passw', 'f_sernum', 'f_date_comm', 'f_user_station_name', 'f_user_name',
                'custom_name', 'custom_desc', 'custom_image', 'enable_station', 'show_in_home', 'priority', 'last_editor'
        );
        foreach($fiedsList as $key)
        {
            if(!isset($stationInformationArray->{$key}))
            {
                $stationInformationArray->{$key} = NULL;
            }else
            {
                switch ($key):
                    case'f_latitude':
                    case'f_longitude':
                        if (!empty($stationInformationArray->{$key})) {
                            $stationInformationArray->{$key} = recoverLatLongData($stationInformationArray->{$key}, str_replace("f_", "", $key));
                        }
                        break;
                    default:
                        break;
                endswitch;
            }
        }
        $newStation = new Station(
            null,
            $stationInformationArray->f_station_code,
            $stationInformationArray->f_date,
            $stationInformationArray->f_dev_id,
            $stationInformationArray->f_name,
            $stationInformationArray->f_descr,
            $stationInformationArray->f_info,
            $stationInformationArray->f_uid,
            $stationInformationArray->f_status,
            $stationInformationArray->f_create_time,
            $stationInformationArray->f_master_name,
            $stationInformationArray->f_date_min,
            $stationInformationArray->f_date_max,
            $stationInformationArray->f_date_last_down,
            $stationInformationArray->f_date_sens,
            $stationInformationArray->f_date_data,
            $stationInformationArray->f_date_conf,
            $stationInformationArray->f_measure_int,
            $stationInformationArray->f_data_int,
            $stationInformationArray->f_timezone,
            $stationInformationArray->f_latitude,
            $stationInformationArray->f_longitude,
            $stationInformationArray->f_altitude,
            $stationInformationArray->f_hw_ver_major,
            $stationInformationArray->f_hw_ver_minor,
            $stationInformationArray->f_sw_ver_major,
            $stationInformationArray->f_sw_ver_minor,
            $stationInformationArray->f_sms_warn_numbers,
            $stationInformationArray->f_sms_warn_values,
            $stationInformationArray->f_gsm_mcc,
            $stationInformationArray->f_gsm_mnc,
            $stationInformationArray->f_gprs_apn,
            $stationInformationArray->f_gprs_user_id,
            $stationInformationArray->f_gprs_passw,
            $stationInformationArray->f_sernum,
            $stationInformationArray->f_date_comm,
            $stationInformationArray->f_user_station_name,
            $stationInformationArray->f_user_name,
            $custom_name = null,
            $custom_desc = null,
            $custom_image = null,
            1,
            (($stationInformationArray->f_latitude && $stationInformationArray->f_longitude)?(1):(0)),
            $priority = null,
            $last_update_date = null,
            $last_edition_time = null,
            $last_editor = null);

        if($newStation->save())
        {
            return $stationInformationArray->f_name . " added";
        }else
        {
            return $stationInformationArray->f_name . " ERROR SAVING DATA";
        }
    }

    /**
     * put your comment there...
     *
     * @param mixed $sensorInformationArray
     */
    private function _addSensor($sensorInformationArray)
    {
        $fiedsList = array(
                'f_station_code', 'f_sensor_ch', 'f_sensor_code', 'f_chain_code',
                'f_group_code', 'f_unit_code', 'f_name', 'f_unit', 'f_div', 'f_mul',
                'f_val_neg', 'f_val_log', 'f_val_last', 'f_val_sum', 'f_val_aver',
                'f_val_min', 'f_val_max', 'f_val_time', 'f_val_user', 'f_create_time',
                'f_val_axilary', 'f_user_app', 'f_color', 'f_sensor_user_name', 'f_user_unit_code',
                'graph_type', 'min_expected', 'max_expected', 'custom_name', 'custom_desc', 'custom_image',
                'enable_sensor', 'chilling_hours_related', 'degrees_day_related', 'priority', 'last_editor'
        );
        foreach($fiedsList as $key)
        {
            if(!isset($sensorInformationArray->{$key}))
            {
                $sensorInformationArray->{$key} = NULL;
            }else
            {
                // continue
            }
        }
        /**
         * Fix, ETo debe mostrarse como SUM y no como AVER como declara el server remoto
         */
        if($sensorInformationArray->f_sensor_code == 1201)
        {
            $sensorInformationArray->f_val_aver = 0;
            $sensorInformationArray->f_val_sum = 1;
        }
        $newSensor = new Sensor(
            0,
            $sensorInformationArray->f_station_code,
            $sensorInformationArray->f_sensor_ch,
            $sensorInformationArray->f_sensor_code,
            $sensorInformationArray->f_chain_code,
            $sensorInformationArray->f_group_code,
            $sensorInformationArray->f_unit_code,
            $sensorInformationArray->f_name,
            $sensorInformationArray->f_unit,
            $sensorInformationArray->f_div,
            $sensorInformationArray->f_mul,
            $sensorInformationArray->f_val_neg,
            $sensorInformationArray->f_val_log,
            $sensorInformationArray->f_val_last,
            $sensorInformationArray->f_val_sum,
            $sensorInformationArray->f_val_aver,
            $sensorInformationArray->f_val_min,
            $sensorInformationArray->f_val_max,
            $sensorInformationArray->f_val_time,
            $sensorInformationArray->f_val_user,
            $sensorInformationArray->f_create_time,
            $sensorInformationArray->f_val_axilary,
            $sensorInformationArray->f_user_app,
            $sensorInformationArray->f_color,
            $sensorInformationArray->f_sensor_user_name,
            $sensorInformationArray->f_user_unit_code,
            $graph_type = null,
            $min_expected = null,
            $max_expected = null,
            $custom_name = null,
            $custom_desc = null,
            $custom_image = null,
            $enable_sensor = 1,
            $chilling_hours_related = ((preg_match("/air[\s| ]+temp/i", $sensorInformationArray->f_name)) ? (1) : (NULL)),
            $degrees_day_related = ((preg_match("/air[\s| ]+temp/i", $sensorInformationArray->f_name)) ? (1) : (NULL)),
            $wind_rose_related = ((preg_match("/wind/i", $sensorInformationArray->f_name)) ? (1) : (NULL)),
            $priority = null,
            $last_edition_time = null,
            $last_editor = ''
        );
        if($newSensor->save())
        {
            return $sensorInformationArray->f_station_code . "_" . $sensorInformationArray->f_sensor_code . "_" . $sensorInformationArray->f_sensor_ch . " added";
        }else
        {
            return $sensorInformationArray->f_station_code . "_" . $sensorInformationArray->f_sensor_code . "_" . $sensorInformationArray->f_sensor_ch . " ERROR SAVING DATA";
        }
    }
    /**
     * put your comment there...
     *
     * @param mixed $sensorInformationArray
     */
    private function _updateSensorDetails($sensorInformationArray)
    {
        // 'f_color' => NULL ,
        $fiedsList = array(
            'f_station_code' => NULL,
            'f_sensor_ch' => NULL,
            'f_sensor_code' => NULL,
            'f_chain_code' => NULL,
            'f_group_code' => NULL,
            'f_unit_code' => NULL,
            'f_name' => NULL,
            'f_unit' => NULL,
            'f_div' => NULL,
            'f_mul' => NULL,
            'f_val_neg' => NULL,
            'f_val_log' => NULL,
            'f_val_last' => NULL,
            'f_val_sum' => NULL,
            'f_val_aver' => NULL,
            'f_val_min' => NULL,
            'f_val_max' => NULL,
            'f_val_time' => NULL,
            'f_val_user' => NULL,
            'f_create_time' => NULL,
            'f_val_axilary' => NULL,
            'f_user_app' => NULL,
            'f_sensor_user_name' => NULL,
            'f_user_unit_code' => NULL,
        );
        
        $updateString = "";
        foreach($fiedsList as $key => $val)
        {
            if(!isset($sensorInformationArray->{$key}))
            {
                $sensorInformationArray->{$key} = NULL;
            }else
            {
                // continue
            }
   
            /**
             * Fix, ETo debe mostrarse como SUM y no como AVER como declara el server remoto
             */
            if($sensorInformationArray->f_sensor_code == 1201)
            {
                $sensorInformationArray->f_val_aver = 0;
                $sensorInformationArray->f_val_sum = 1;
            }
            $updateString .= "\n `{$key}` =  " . check_null_val(process_plain_text($sensorInformationArray->{$key})) . " ,";
        }
        if(trim($updateString))
        {
            $query = "
                UPDATE
                    `" . DBT_PREFIX . "sensor_info`
                SET
                    {$updateString}
                    `last_update_date` =   UNIX_TIMESTAMP()
                WHERE
                    `f_station_code` =  '{$sensorInformationArray->f_station_code}'
                AND
                    `f_sensor_code` =  '{$sensorInformationArray->f_sensor_code}'
                AND
                    `f_sensor_ch` =  '{$sensorInformationArray->f_sensor_ch}'
                LIMIT 1
                ";
            sql_select($query, $results);
            return $sensorInformationArray->f_station_code . "_" . $sensorInformationArray->f_sensor_code . "_" . $sensorInformationArray->f_sensor_ch . " updated";
        }else
        {
            return false;
        }
    }

    /**
     * put your comment there...
     *
     * @param mixed $stationInformationArray
     */
    private function _updateStationDetails($stationInformationArray)
    {
        //'f_latitude' => NULL, 'f_longitude' => NULL, 'f_altitude' => NULL, /* DO NOT UPDATE THIS */
        $fiedsList = array(
            'f_station_code' => NULL,
            'f_date' => NULL,
            'f_dev_id' => NULL,
            'f_name' => NULL,
            'f_descr' => NULL,
            'f_info' => NULL,
            'f_uid' => NULL,
            'f_status' => NULL,
            'f_create_time' => NULL,
            'f_master_name' => NULL,
            'f_date_min' => NULL,
            'f_date_max' => NULL,
            'f_date_last_down' => NULL,
            'f_date_sens' => NULL,
            'f_date_data' => NULL,
            'f_date_conf' => NULL,
            'f_measure_int' => NULL,
            'f_data_int' => NULL,
            'f_timezone' => NULL,
            'f_hw_ver_major' => NULL,
            'f_hw_ver_minor' => NULL,
            'f_sw_ver_major' => NULL,
            'f_sw_ver_minor' => NULL,
            'f_sms_warn_numbers' => NULL,
            'f_sms_warn_values' => NULL,
            'f_gsm_mcc' => NULL,
            'f_gsm_mnc' => NULL,
            'f_gprs_apn' => NULL,
            'f_gprs_user_id' => NULL,
            'f_gprs_passw' => NULL,
            'f_sernum' => NULL,
            'f_date_comm' => NULL,
            'f_user_station_name' => NULL,
            'f_user_name' => NULL,
        );
        $updateString = "";
        foreach($fiedsList as $key => $val)
        {
            if(isset($stationInformationArray->{$key}))
            {
                switch ($key):
                    case'f_latitude':
                    case'f_longitude':
                        if(!empty($stationInformationArray->{$key}))
                        {
                            $stationInformationArray->{$key} = recoverLatLongData($stationInformationArray->{$key}, str_replace("f_", "", $key));
                        }
                        break;
                    default:
                        break;
                endswitch;
                $updateString .= "\n `{$key}` =  " . check_null_val(process_plain_text($stationInformationArray->{$key})) . " ,";
            }
        }
        if(trim($updateString))
        {
            $query = "
                UPDATE
                    `" . DBT_PREFIX . "station_info`
                SET
                    {$updateString}
                    `last_update_date` =   UNIX_TIMESTAMP()
                WHERE
                    `f_name` =  '{$stationInformationArray->f_name}'
                LIMIT
                    1
                ";

            sql_select($query, $results);

            return $stationInformationArray->f_name . " updated";
        }else
        {
            return false;
        }
    }

    /**
     * put your comment there...
     * @todo  eliminar WHERE `f_dev_id` ='1'
     * @todo porque la ETo la envìa en el mismo grupo pero con f_station_code diferente?
     */
    private function _runSensorsUpdate()
    {
        $proccessedData = array();
        $stationSensorsList = array();

        $query = "
            SELECT
                `f_station_code`,
                `f_name`
            FROM
                `" . DBT_PREFIX . "station_info`
	    WHERE
		`enable_station` = 1
            ";

        sql_select($query, $results);

        /**
         * build all stations sensors list array
         */
        while($row = mysql_fetch_object($results))
        {
            $this->_stationName = $row->f_name;

            $remote_uri = $this->_buildCurlRequest('Get_Station_Sensors');
            $this->_proccessOutput($remote_uri);
            $remoteJson = $this->_executeFcCurl($remote_uri);

            $stationSensorsList[$row->f_station_code] = $remoteJson->ReturnDataSet;
        }

        foreach($stationSensorsList as $stationCode => $sensorsList)
        {
            foreach($sensorsList as $key => $sensorDetails)
            {
                $sensorDetails->f_station_code = $stationCode;
                if($this->_sensorExists($sensorDetails->f_station_code, $sensorDetails->f_sensor_code, $sensorDetails->f_sensor_ch))
                {
                    // UPDATE
                    $proccessedData[] = $this->_updateSensorDetails($sensorDetails);
                }else
                {
                    // ADD
                    $addSensor = $this->_addSensor($sensorDetails);
                    $proccessedData[] = $addSensor;
                    $this->_proccessOutput($addSensor);
                }
            }
        }
        unset($stationSensorsList);
        return $proccessedData;
    }

    /**
     *  SI la estaciòn no existe la crea sinò la actualiza con los datos que hayan cambiado
     * @return Array
     */
    private function _runStationsUpdate()
    {
        $proccessedData = array();

        $remote_uri = $this->_buildCurlRequest('Get_All_Stations');
        $remoteJson = $this->_executeFcCurl($remote_uri);

        foreach($remoteJson->ReturnDataSet as $key => $val)
        {

            $this->_stationName = $val->f_name;

            $remote_uri2 = $this->_buildCurlRequest('Get_Station_Info');
            $remoteJson2 = $this->_executeFcCurl($remote_uri2);

            foreach($remoteJson2->ReturnDataSet[0] as $k => $v)
            {
                $remoteJson->ReturnDataSet[$key]->{$k} = $v;
            }
        }
        foreach($remoteJson->ReturnDataSet as $key => $val)
        {
            if($this->_stationExists($remoteJson->ReturnDataSet[$key]->f_name)) 
            {
                // UPDATE
                /**
                 * @todo verificar USAR clase para hacer update
                 */
                $proccessedData[] = $this->_updateStationDetails($remoteJson->ReturnDataSet[$key]);
            }else
            {
                // ADD
                $addStation = $this->_addStation($remoteJson->ReturnDataSet[$key]);
                $proccessedData[] = $addStation;

                $this->_proccessOutput($addStation);
            }
        }
        unset($remoteJson);
        return $proccessedData;
    }

    /**
     * put your comment there...
     *
     * @param mixed $f_station_code
     * @param mixed $max_date
     * @param mixed $data_retrieved
     * @todo Solve memory usage problem
     */
    private function _updateSensorsData($f_station_code, $max_date, $data_retrieved)
    {
        $max_date_limit = preg_replace("#[A-Z]#i", " ", $max_date);
        $get_from_date = $data_retrieved->ReturnParams->dt_from;
        $next_date_row = $data_retrieved->ReturnParams->dt_to;
        $this->_proccessOutput("Memory get usage - " . number_format(memory_get_usage()) );
        foreach ($data_retrieved->ReturnDataSet as $key => $val)
        {
            /**
             * @todo ver como mejorar la performance
             */
            if($val->f_date < '2011-01-01 00:00:01')
            {
                $this->_proccessOutput("skip sensors lecture " . $val->f_date );
                continue;
            }
            $this->_proccessOutput("processing sensors lecture ". $val->f_date );
            $this->_buildInsertSensorDataQuery($val, $f_station_code);
            unset($data_retrieved->ReturnDataSet[$key]);
        }
        unset($data_retrieved);
        return $next_date_row;
    }

    /**
     *
     * @param <type> $sensorDataByDatetime
     * @param <type> $f_station_code
     */
    private function _buildInsertSensorDataQuery($sensorDataByDatetime, $f_station_code)
    {
        $f_date = $sensorDataByDatetime->f_date;
        $sensor = false;
        $sensorId = false;
        $lastSensorDataRetrieveInfoTable = '';
        foreach($sensorDataByDatetime as $key => $val) 
        {
            if(preg_match("#sens_#i", $key)) 
            {
                $fields = explode("_", $key);
                /* Cargar Sensor */
                if(!$sensor || $sensor->getId() != $sensorId) 
                {
                    if(array_key_exists($f_station_code .'_'. $fields['3'] .'_'.$fields['2'], $this->_loadedSensors))
                    {
                        $sensor = $this->_loadedSensors[$f_station_code .'_'. $fields['3'] .'_'.$fields['2']];
                    }else 
                    {
                        $sensor = Sensor::load(false, false, $f_station_code, $fields['3'], $fields['2']);
                        $this->_loadedSensors[$f_station_code .'_'. $fields['3'] .'_'.$fields['2']] = $sensor;
                    }
                 }
                /* Si no se puede cargar el sensor, saltar una iteracion */
                if(!$sensor)
                {
                    continue;
                }

                /**
                 * IF NO DATA THEN CONTINUE
                 */
                if(check_null_val(process_plain_text($val)) == 'NULL')
                {
                    continue;
                }
                /**
                 * Value is out of range?
                 */
                $mixExpected = (is_numeric($sensor->getMinExpected()) ? $sensor->getMinExpected() : false);
                $maxExpected = (is_numeric($sensor->getMaxExpected()) ? $sensor->getMaxExpected() : false);

                if($mixExpected !== false && $maxExpected !== false)
                {
                    if($val >= $mixExpected && $val <= $maxExpected)
                    {
                        $outOfRange = 0;
                    }else
                    {
                        $outOfRange = 1;
                    }
                }else
                {
                    $outOfRange = 0;
                }
                /**
                 * Update retrieved sensors data info
                 */
                $tableSuffix = date('Y_m', strtotime($f_date));
                $currentSensorDataRetrieveInfoTable =  DBT_PREFIX . "sensor_data_retrieve_info_{$tableSuffix}";
		
                if($lastSensorDataRetrieveInfoTable != $currentSensorDataRetrieveInfoTable)
                {
                    $createTableQuery =  "
                    CREATE TABLE IF NOT EXISTS `{$currentSensorDataRetrieveInfoTable}` (
                        `row_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                        `f_station_code` int(11) unsigned NOT NULL,
                        `f_sensor_type` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
                        `f_sensor_code` int(11) unsigned NOT NULL,
                        `f_sensor_ch` int(11) unsigned NOT NULL,
                        `f_read_time` int(11) unsigned NOT NULL,
                        `f_write_time` int(11) unsigned NOT NULL,
                        `f_data_retrieved` decimal(15,4) NOT NULL,
                        `f_unit` int(11) DEFAULT NULL,
                        `out_of_range` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'si la lectura està fuera de los parametros asignados por el admin en los detalles del sensor',
                        `wind_direction_vector` decimal(15,4) DEFAULT NULL,
                        PRIMARY KEY (`row_id`),
                        UNIQUE KEY `station_code_sensor_type_sensor_code_sensor_ch_read_time` (`f_station_code`,`f_sensor_type`,`f_sensor_code`,`f_sensor_ch`,`f_read_time`),
                        KEY `f_station_code` (`f_station_code`),
                        KEY `f_sensor_code` (`f_sensor_code`),
                        KEY `f_read_time` (`f_read_time`),
                        KEY `out_of_range` (`out_of_range`),
                        KEY `f_sensor_type` (`f_sensor_type`),
                        KEY `wind_direction_vector` (`wind_direction_vector`),
                        KEY `f_read_time_combined` (`f_read_time`,`f_station_code`,`f_sensor_code`,`f_sensor_type`)
                        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
                        ";
                    if(!sql_select($createTableQuery, $createTableQueryResults))
                    {
                        return false;
                    }
                    usleep(50);
                }
                /**
                 * Fix, ETo debe mostrarse como SUM y no como AVER como declara el server remoto
                 */
                $fields['1'] = (($sensor->getSensorCode() == 1201)?('sum'):($fields['1']));
                $queryString = "
                    INSERT IGNORE INTO
                        `{$currentSensorDataRetrieveInfoTable}`
                    SET
                        `f_station_code` = {$sensor->getStationCode()},
                        `f_sensor_type` = " . check_null_val(process_plain_text($fields['1'])) . ",
                        `f_sensor_code` = {$sensor->getSensorCode()},
                        `f_sensor_ch` = {$sensor->getSensorCh()},
                        `f_read_time` = " . (int) strtotime($f_date) . ",
                        `f_write_time` = UNIX_TIMESTAMP(),
                        `f_data_retrieved` = " . check_null_val(process_plain_text($val)) . ",
                        `f_unit` = '" . process_plain_text($sensor->getUnit()) . "',
                        `out_of_range` = {$outOfRange},
                        `wind_direction_vector` = " . (($sensor->isWindDirectionSensor()) ? (deg2cpd($val, true)) : ('NULL')) . "
                        "
                ;
                if(sql_select($queryString, $results))
                {
                    /**
                     * Update retrieved info index
                     */
                    $updateStationDataRetrieveInfo = "
                        INSERT IGNORE INTO
                            `" . DBT_PREFIX . "station_data_retrieve_info`
                        SET
                            `f_station_code` = " . process_plain_text($f_station_code) . ",
                            `last_read_time` = " . strtotime($f_date) . ",
                            `last_read_time_success` = 1
                        ";
                    sql_select($updateStationDataRetrieveInfo, $results);
                    usleep(1000);
                }else
                {
                    $this->_proccessOutput("something goes wrong with function _buildInsertSensorDataQuery()");
                }
            }
        }
        unset($sensorDataByDatetime);
    }
    /**
     * put your comment there...
     *
     */
    private function _runSensorDataUpdate()
    {
        $proccessedData = array();
        $query = "
            SELECT
                `f_station_code`,`f_name`
            FROM
                `" . DBT_PREFIX . "station_info`
            WHERE
                `enable_station` = 1
            ";
        sql_select($query, $results);
        while($row = mysql_fetch_object($results))
        {
            if($this->_forcedStartingDate != false)
            {
                $getFromDate = $this->_forcedStartingDate;
            }else
            {
                $stationSensorsDataHistoryExists = $this->_stationSensorsDataHistoryExists($row->f_station_code);
                if($stationSensorsDataHistoryExists)
                {
                    $stationSensorsDataHistoryExists = $stationSensorsDataHistoryExists - FIELDCLIMATE_DOWNLOAD_ROLLBACK;
                }else
                {
                    $stationSensorsDataHistoryExists = strtotime('1 months ago midnight');
                }
                $getFromDate = date('Y-m-d\TH:i:s', $stationSensorsDataHistoryExists);
            }
            if($getFromDate)
            {
                $this->_proccessOutput("Station "  . $row->f_station_code . " updating from " . $getFromDate); //date(DATE_ATOM,$stationSensorsDataHistoryExists)
                $this->_stationName = $row->f_name;
                $this->_dateFrom = $getFromDate;
                $remote_uri = $this->_buildCurlRequest('Get_Data_From_Date');
                $remoteJson = $this->_executeFcCurl($remote_uri);
                $this->_proccessOutput($remote_uri);
                if(isset($remoteJson->ReturnParams->max_date) && $remoteJson->ReturnParams->max_date >= $getFromDate)
                {
                    $max_date = $remoteJson->ReturnParams->max_date;
                    $next_date_row = $this->_updateSensorsData($row->f_station_code, $remoteJson->ReturnParams->max_date, $remoteJson);
                    while((strtotime(preg_replace("#[A-Z]#i", " ", $next_date_row)) < strtotime(preg_replace("#[A-Z]#i", " ", $max_date))) && ($next_date_row !== false))
                    {
                        $this->_dateFrom = $next_date_row;
                        $remote_uri = $this->_buildCurlRequest('Get_Data_From_Date');
                        $remoteJson = $this->_executeFcCurl($remote_uri);
                        $next_date_row = $this->_updateSensorsData($row->f_station_code, $remoteJson->ReturnParams->max_date, $remoteJson);
                    }
                }else
                {
                    $proccessedData[] = $remoteJson;
                }
            }else
            {
                die('no starting date date assigned');
            }
        }
        //return $out;
    }

    /**
     *
     * @param String $selectFunction  runStationsUpdate|runSensorsUpdate|runSensorDataUpdate
     * @return <type>
     */
    public function runSyncro($selectFunction)
    {
        try
        {
            switch ($selectFunction)
            {
                case'runStationsUpdate':
                    /* Funciona correctamente 2010-10-22 */
                    $remoteString = $this->_runStationsUpdate();
                    break;
                case'runSensorsUpdate':
                    /* Funciona correctamente 2010-10-22 */
                    $remoteString = $this->_runSensorsUpdate();
                    break;
                case'runSensorDataUpdate':
                default:
                    /* Funciona ? */
                    $remoteString = $this->_runSensorDataUpdate();
                    break;
            }
            return $remoteString;
        }catch(Exception $ex)
        {
            Exception_hander(array('ex' => $ex, 'requested_uri' => $this->_request_remote_uri));
            return false;
        }
    }
}
