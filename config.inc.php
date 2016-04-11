<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
define ('DEBUG',false);
// idioma por defecto
define ('IDIOMA_POR_DEFECTO','ES');
// Define custom session name
define('SESSION_NAME',"cifasis_");
session_name(SESSION_NAME);
#############################################
###           ENVIRONMENT
#
// Any Session or Cookie will use this named constant as prefix
define('LOCAL_IDENTIFIER',"CIFASIS");
// SCRIPT VERSION
define('SCRIPT_VERSION',"V 1.1.0");
// pagina de errores por defecto
define('DEFAULT_ERROR_PAGE',"./html/index1.php");
// Defines the a padding string for password criptography
define('HASH_PADDING_STRING',"c00ck13m0nst3r");
// Save the session ID on a constant
define('CURRENT_SESSION_ID',session_id());
// Defines the Default charset for all php/html documents and e-mail; also used to determinate the db connection charset
define('DEFAULT_CHARSET',"utf8"); //iso-8859-1     utf-8
define('DEFAULT_COLLATION',"utf8_general_ci");
// used for cart orders (the 12:00:00 am of yesterday)
define('TODAY_STARTING_DATE',date('d-m-Y H:i:s', mktime(12,0,0, date('m'), date('d') -1, date('Y'))));
// JUST Today date
define('TODAY',date('d-m-Y', mktime(0,0,0, date('m'), date('d'), date('Y'))));
// cuando no encuentra una función va aca
//define('ERROR_FUNCION',"./html/error.php");
// www root
define('WWW_ROOT',$_SERVER['HTTP_HOST']);
// define archivos root
define('PATH_ROOT',dirname(__FILE__));
// librerias
//define('PATH_LIB',"lib");
// Estilo definido
define('ESTILO_SELECCIONADO',"original");
//
define('MIMETYPE',"24x24");
// carpeta archivos temporales
define('TEMPORALES',PATH_ROOT."/temp/");
// mail de soporte
define('MAIL_SOPORTE',"soporte@cifasis-conicet.gov.ar");
//define ('LOGIN_ST',true);
define('ID_GRUPO_SOPORTE',"12");
#############################################
###           ENVIRONMENT
#
//$ID_PAIS_CASTELLANO=array("12","13","30","31","46","49","50","51","59","60","62","67","71","90","96","98","109","152","160","168","169","177","181","203","226","230");
// SCRIPT_FILENAME
define('DEFINED_SCRIPT_FILENAME',basename($_SERVER['SCRIPT_FILENAME']));//DEFINED_SCRIPT_FILENAME
// HTTP_USER_AGENT
define('DEFINED_HTTP_USER_AGENT',$_SERVER['HTTP_USER_AGENT']);//LOCAL_HTTP_USER_AGENT
// HTTP_USER_AGENT
define('DEFINED_HTTP_REFERER',((isset($_SERVER['HTTP_REFERER'])) ? ($_SERVER['HTTP_REFERER']) : ""));
// REMOTE_HOST
define('DEFINED_REMOTE_HOST',((isset($_SERVER['REMOTE_HOST'])) ? ($_SERVER['REMOTE_HOST']) : "unavailable"));
// REMOTE ADDRESS
define('DEFINED_REMOTE_ADDR',$_SERVER['REMOTE_ADDR']);
// HTTP_X_FORWARDED_FOR
define('X_FORWARDED_FOR',((isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? ($_SERVER['HTTP_X_FORWARDED_FOR']) : "unavailable"));
// ALLOWED FILE MANAGER UPLOAD EXTENSIONS
define('ALLOWED_UPDN_FILES','gif,jpg,jpeg,png,doc,pdf,xls,txt,rar,zip,swf,flv,rtf,mp3');
// iamgenes permitidas
define('ALLOWED_IMAGES_FILES','gif,jpg,jpeg,png');
// document_root
define('DOCUMENT_ROOT',$_SERVER['DOCUMENT_ROOT']);
// anio creacion del Instituto
define('ANIO_CREACION','2008');
// directorio donde se guardan los archivos subidos
define('FILESDIR',"/home/pablo/filesdir/");
// servidor de autenticacion authhost
define('AUTHHOST',"{mail.cifasis-conicet.gov.ar:110/pop3/notls}");
#
#############################################
###           DB SETTINGS
#
define('DBT_PREFIX', 'cifasis_'); // prefix for custom database table names (MUST BE LOWERCASE)
define('DB_SERVER','localhost');
define('DB_SERVER_USERNAME', 'cifasiswww');
define('DB_SERVER_PASSWORD', 'cifasiswww@#@#');
define('DB_DATABASE', 'cifasis_actual_17062014');
define('USE_PCONNECT', 'true'); // use persistent connections?
define('ALLOW_DB_ERROR_PRINT', true); // do you want to send DBerror  to the browser during development ?
define('ADMIN_ELR_EMAIL', "pablo.ju.garcia@gmail.com"); // Email destination address for error log report
#
#############################################
###           MENUS
#
define('MENU_ARRIBA',0);
define('MENU_IZQUIERDA',1);
define('MENU_DERECHA',2);
define('MENU_ABAJO',3);
##
#############################################
###       ERROR NOTIFICATIONS TO ADMIN
#
  /*
  I any error happens on DB connection flow
  the system will send an e-mail to the $email_to address
  write an error log
  and redirect the usert to the DEFAULT_ERROR_PAGE page

  This behaviour occurs once for User Session
  to avoid multiple e-mail for user
  */
function error_notification_to_admin($message)
{
	if($message) 
	{
	    $email_to = ADMIN_ELR_EMAIL;
	    $email_subject = " Errors at ".WWW_ROOT;
	    $headers = "From: errors@".WWW_ROOT."";
	    $semi_rand = md5(time());
	    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		settype($qstring,"string");
		foreach($_REQUEST as $key => $val)
        {
			$key = preg_replace(array("#>#","#<#"),array("&gt;","&lt;"),$key);
			$val = preg_replace(array("#>#","#<#"),array("&gt;","&lt;"),$val);
			$qstring .= "<br>".$key."=".$val;
		}
	    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
	    $email_message = "This is a multi-part message in MIME format.\n\n"
    		."--{$mime_boundary}\n"
    		."Content-Type:text/html; charset=\"".DEFAULT_CHARSET."\"\n"
    		."Content-Transfer-Encoding: 8bit\n\n"
    		."\n<strong> Errors at ".WWW_ROOT. ":</strong><br>"
    		."\nDate: ".date('Y-m-d H:i:s')
    		."<br>\nRemote Host: ".DEFINED_REMOTE_HOST
    		."<br>\nRemote Address: ".DEFINED_REMOTE_ADDR
    		."<br><br>\n".$message."<br>"
    		."<br><br>QUERY STRING: ".DEFINED_SCRIPT_FILENAME
    		.$qstring."<br><br>" . "\n\n";
    	$complete_error_log_message ="\n\n######### New LOG ###################################################"
    		. "\nDate: ".date('Y-m-d H:i:s')
    		. "\nRemote Host: ".DEFINED_REMOTE_HOST
    		. "\nRemote Address: ".DEFINED_REMOTE_ADDR
    		. "\n".strip_tags(str_replace("<br>","\n",$message))
    		. "\n\nQUERY STRING: ".DEFINED_SCRIPT_FILENAME
    		.((DEFINED_REMOTE_ADDR === "127.0.0.1") ? $qstring : "");
    	error_log($complete_error_log_message, 3, ERROR_LOGS_PATH.'error_log.txt');
     	// NOTIFY BY E_MAIL ONCE IF NOT BYPASSED BY ADMIN
		@mail($email_to, $email_subject, $email_message, $headers);
    	// PRINT ON SCREEN
    	if((DEFINED_REMOTE_ADDR === "127.0.0.1")&&(ALLOW_DB_ERROR_PRINT === true)) 
    	{
      		echo nl2br($complete_error_log_message);
    	}else 
    	{
      		header('Location: '.DEFAULT_ERROR_PAGE);
    	}
    	die();
	}else 
	{
    	if((DEFINED_REMOTE_ADDR === "127.0.0.1")&&(ALLOW_DB_ERROR_PRINT === true)) 
    	{
			echo nl2br($complete_error_log_message);
    	}else 
    	{
			header('Location: '.DEFAULT_ERROR_PAGE);
    	}
    	die();
  	}
}
#############################################
###         ENCRYPTATION
#
function encrypt($string,$pepper = "?") 
{
	$pepper = $pepper."%@Q;u:(à^*9[q-u_o!t";
	if((version_compare(PHP_VERSION, '5.1.2',">="))&&(in_array("sha256",hash_algos()))) 
	{
		return hash("sha256", HASH_PADDING_STRING.$string.$pepper);
	} else 
	{
		return md5(HASH_PADDING_STRING.$string.$pepper);
	}
}
#############################################
###         DATABASE CONNECTION
#
  	// See above constants settings
  	// DB_SERVER
  	// DB_SERVER_USERNAME
  	// DB_SERVER_PASSWORD
  	// DB_DATABASE
	// USE_PCONNECT
	// OPEN DATABASE CONNECTION
if(!($db_link = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_DATABASE.';charset=utf8', DB_SERVER_USERNAME, DB_SERVER_PASSWORD)))
{
	echo "Could not connect to ".DB_DATABASE."<br>";
	return true;
}
function sql_select($query, &$rv)
{
	global $db_link;
	$query=preg_replace("/\r\n|\r/", chr(32), $query);
	if (DEFAULT_CHARSET == "utf8" OR DEFAULT_CHARSET == "utf-8")
	{
		$db_link->query("SET NAMES 'utf8'");
	}
	$rv = $db_link->prepare($query);
	if (!$rv->execute())
	{
		return false;
	}
	if($last_id=$db_link->lastInsertId())
	{
		return $last_id;
	}
	return true;	
}
function sql_select1($query, &$rv)
{
	global $db_link;
	//$query=preg_replace("/\r\n|\r/", chr(32), $query);
	if (DEFAULT_CHARSET == "utf8" OR DEFAULT_CHARSET == "utf-8")
	{
		$db_link->query("SET NAMES 'utf8'");
	}
	$rv = $db_link->prepare($query);
	if (!$rv->execute())
	{
		return false;
	}
	if($last_id=$db_link->lastInsertId())
	{
		return $last_id;
	}
	return true;
}
function CCGetFromPost($parameter_name, $default_value = "") 
{
    return isset($_POST[$parameter_name]) ? CCStrip($_POST[$parameter_name]) : $default_value;
}
function CCGetFromGet($parameter_name, $default_value = "") 
{
    return isset($_GET[$parameter_name]) ? CCStrip($_GET[$parameter_name]) : $default_value;
}
function CCStrip($value) 
{
	if(get_magic_quotes_gpc() != 0) 
	{
	    if(is_array($value))  
			foreach($value as $key=>$val)
				$value[$key] = stripslashes($val);
		else
			$value = stripslashes($value);
  	}
	return $value;
}

/* **************************************
*   SIMPLE LOGS IN LOG FILE logs/log.html  *
*************************************** */
function writeLog($archivo,$varName,$var) 
{
	if ($archivo=="")
	{
		$archivo=PATH_ROOT."/_error_logs/syslog.html";
	}
	$today = getdate();
	$log="$today[year]|$today[mon]|$today[mday] - $today[hours]:$today[minutes]:$today[seconds]-\n-$varName\n-";
	$log .=print_r($var,True);
	$fp = fopen($archivo, 'a+');
	fwrite($fp, "\n<pre>\nBeginLog\n");
	fwrite($fp, $log);
	fwrite($fp, "\nEndLog\n</pre>\n");
}
/**
 * 
 * @param unknown $val
 * @return number
 */
function return_bytes($val) 
{
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) 
	{
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}
/**
 * 
 */
 function CFG($config="")
 {
 	$idiom = (!isset($_SESSION['idioma'])) ? trim(strtolower(IDIOMA_POR_DEFECTO)) : trim(strtolower($_SESSION['idioma']));
	$sql="	SELECT 	`config_".$idiom."` AS `que_config` 
			FROM 	`".SESSION_NAME."idiomas`
			WHERE 	`config`='".$config."'";
	if(!sql_select($sql,$consulta))
	{
		writeLog(ARCHIVO_LOG,"sql", $sql);
		writeLog(ARCHIVO_LOG,"ERROR CFG", $consulta->errorInfo());
		return false;
	}
	$config=$consulta->fetch(PDO::FETCH_ASSOC);
	return utf8_decode($config['que_config']);
}
/**
 * 
 */
function mensaje($texto)
{
	echo "<script type=\"text/javascript\">";
	echo "alert(\"".utf8_decode($texto)."\");";
	echo "</script>";
}
/**
 * 
 * @param type $string
 * @return type
 */
function stripAccents($string="")
{
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}
?>