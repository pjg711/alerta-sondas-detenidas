<?php

/**
 * Description of class_log
 *
 * @author pablo
 */
class Log 
{
	private $userid;    // id usuario que hace la consulta
	private $ipAddress; // direccion ip del cliente
	private $action;    // tipo de accion, por ejem
	private $url;       // url del navegador
	private $info;      // info contiene otra informacion relevante del log
    private $file;      // nombre de archivo para la exportacion
	/**
	 *
	 */
	function __construct($userid=0, $ipAddress='', $action='', $url='', $info='')
	{
		$this->userid=$userid;
		$this->ipAddress=$ipAddress;
		$this->action=$action;
		$this->url=$url;
		$this->info=$info;
        if($action=='export_data')
        {
            $this->file = basename($info);
        }else
        {
            $this->file = "";
        }
	}
    
    public function getUserId()
    {
        return $this->userid;
    }
    
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
    
    public function getAction()
    {
        return $this->action;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function getInfo()
    {
        return $this->info;
    }
    
    public function getFile()
    {
        return $this->file;
    }
    /**
     * 
     * @param type $id
     */
    public static function load($id = '', $fromArrayValues = false)
    {
        if(empty($id)) return false;
        $loadedDataArray=false;
        if(is_array($fromArrayValues))
        {
            $loadedDataArray = $fromArrayValues;
        }else
        {
            $query="
                SELECT  `id`,`time`,`userid`,`ipAddress`,`action`,`url`,`info`
                FROM    `" . SESSION_NAME . "log`
                WHERE   `id`={$id} LIMIT 1";
            if(sql_select($query,$results))
            {
                settype($response, 'array');
                while($logInfo = $results->fetch(PDO::FETCH_ASSOC))
                {
                    $loadedDataArray = $logInfo;
                }
            }
        }
        if(is_array($loadedDataArray) && count($loadedDataArray) > 0)
        {
            $nlog = new Log( $loadedDataArray['userid'],
                             $loadedDataArray['ipAddress'],
                             $loadedDataArray['action'],
                             $loadedDataArray['url'],
                             $loadedDataArray['info']
                            );
            return $nlog;
        }else
        {
            return false;
        }
    }
    
	/**
	 *
	 */
	//function buscar_log_id_persona()
    public static function search($id='',$userid='',$action='')
	{
        $where='';
        if(!empty($id))
        {
            $where.=" log.`id`={$id} AND";
        }
        if(!empty($userid))
        {
            $where.=" log.`userid`={$userid} AND";
        }
        if(!empty($action))
        {
            $where.=" log.`action`='{$action}' AND";
        }
        if(substr($where,-3)=="AND") $where=substr($where,0,-3);
        //
		$logs=array();
		$query="
			SELECT	log.`id` AS id,
                    log.`time` AS time,
                    log.`userid` AS userid,
                    log.`ipAddress` AS ipAddress,
                    log.`action` AS action,
                    log.`url` AS url,
                    log.`info` AS info
			FROM	`".SESSION_NAME."log` log
            WHERE   {$where}";
        //echo "query-->{$query}<br>";
        if(!sql_select($query, $results)) return false;
        settype($response, 'array');
        while($row=$results->fetch(PDO::FETCH_ASSOC))
        {
            $nlog= Log::load($row['id']);
            $response[]=$nlog;
        }
        return $response;
	}
	/**
	 *
	 */
	public static function add($userid='',$action='',$info='')
	{
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
        {
            $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        }
        $url = get_current_url();
		$sql="
			INSERT INTO `".SESSION_NAME."log` (`time`,`userid`,`ipAddress`,`action`,`url`,`info`)
			VALUES	(".time().",".$userid.",'".$ipAddress."','".$action."','".$url."','".$info."')";
		if($id=sql_select($sql,$consulta))
        {
            return $id;
        }
		return false;
	}
	/**
	 *
	 */
	public static function delete($id)
	{
		$sql="
			DELETE FROM `".SESSION_NAME."log`
            WHERE `id`=".$id;
		sql_select($sql,$consulta);
		if (!$consulta) return false;
		return true;
	}
   
    
}
