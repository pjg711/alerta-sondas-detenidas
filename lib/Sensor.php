<?php
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
                `" . DBT_PREFIX . "sensor_info`
            WHERE
                {$whereCondition}

            LIMIT 1";

            if($rowcount=sql_select($sqlQuery, $result))
            if($rowcount > 0) 
            {
                settype($response, 'array');
                while ($sensorInfo = mysql_fetch_assoc($result))
                {
                    $loadedDataArray = $sensorInfo;
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
            SELECT
                *
            FROM
                `" . DBT_PREFIX . "sensor_info`
                {$whereCondition} 
            ORDER BY
                `priority`,`custom_name`,`f_sensor_user_name`
            ";
        if($rowcount=sql_select($sqlQuery, $result))
        {
            if($rowcount > 0)
            {
                while($sensorInfo = mysql_fetch_assoc($result))
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
                `" . DBT_PREFIX . "sensor_info`
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

