<?php
/**
 * 
 */
class IMETOS
{
    public $dblink;
    
    private $userid;
    private $server_mysql;
    private $database_mysql;
    private $user_mysql;
    private $passw_mysql;
    //
    private $rowCount=0;
    //
    function __construct($userid=0,$server="",$database="",$username="",$password="")
    {
        $this->userid=$userid;
        $this->server_mysql=$server;
        $this->user_mysql=$username;
        $this->passw_mysql=$password;
        $this->database_mysql=$database;
        //
        if($server=="" OR $database=="" OR $username=="" OR $password=="")
        {
            // busca datos de conexion
            echo "<pre>";
            echo "server_mysql----->{$this->server_mysql}<br>";
            echo "database_mysql--->{$this->database_mysql}<br>";
            echo "user_mysql------->{$this->user_mysql}<br>";
            echo "pass_mysql------->{$this->passw_mysql}<br>";
            echo "</pre>";
            //
            $query="SELECT  `id`,`username`,`password`,`server`,`database`
                    FROM    `users`
                    WHERE   `usertype`='mysql' AND
                            `userid`={$userid}";
            echo "query---->{$query}<br>";
            if(sql_select($query,$results))
            {
                if($connection = $results->fetch(PDO::FETCH_ASSOC))
                {
                    $this->server_mysql=$connection['server'];
                    $this->database_mysql=$connection['database'];
                    $this->user_mysql=$connection['username'];
                    $this->passw_mysql=$connection['password'];
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
    public function getId()
    {
        return $this->userid;
    }
    public function getUser()
    {
        return $this->user_mysql;
    }
    public function getPass()
    {
        return $this->passw_mysql;
    }
    public function getDatabase()
    {
        return $this->database_mysql;
    }
    public function getServer()
    {
        return $this->server_mysql;
    }
    public function getRowCount()
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

	public function StationDataGetMinMaxDate()
    {
		$web=self::FIELDCLIMATE."CIDIStationData/GetMinMaxDate?user_name=".str_replace(chr(32),"%20",$this->user_name)."&user_passw=".str_replace(chr(32),"%20",$this->user_passw)."&station_name=".$this->station_name."&debug=0";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
	}

	public function GetLast()
    {
		$web=self::FIELDCLIMATE."CIDIStationData/GetLast?user_name=".str_replace(chr(32),"%20",$this->user_name)."&user_passw=".str_replace(chr(32),"%20",$this->user_passw)."&station_name=".$this->station_name."&row_count=".$this->num_row."&group_code=0&debug=0";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
	}

	public function StationDataGetFromDate()
    {
		$web=self::FIELDCLIMATE."CIDIStationData/GetFromDate?user_name=".str_replace(chr(32),"%20",$this->user_name)."&user_passw=".str_replace(chr(32),"%20",$this->user_passw)."&station_name=".$this->station_name."&row_count=".$this->num_row."&group_code=0&dt_from=".$this->dt_from."&debug=0";
        if(!$matriz=$this->curl_answer($web))
        {
            return false;
        }
        return $matriz;
    }

	public function StationDataGetNext()
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
?>