<?php
class User
{
    private $id;
    private $is_admin;
    private $user_name;
    private $mails_imetos;
    private $enable_user;
    //
    private $id_ftp;
    private $user_ftp;
    private $passw_ftp;
    private $server_ftp;
    private $remotedir_ftp;
    private $mails_ftp;
    private $enable_ftp;
    //
    private $id_mysql;
    private $user_mysql;
    private $passw_mysql;
    private $server_mysql;
    private $database_mysql;
    private $enable_mysql;
    
    private $error;
    
    function __construct($id=0,$is_admin=0,$username='',$mails_imetos='',$enable_user='',
            $id_ftp=0,$user_ftp='',$passw_ftp='',$server_ftp='',$remotedir_ftp='',$mails_ftp='',$enable_ftp=0,
            $id_mysql=0,$user_mysql='',$passw_mysql='',$server_mysql='',$database_mysql='',$enable_mysql='') 
    {
        $this->id=$id;
        $this->is_admin=$is_admin;
        $this->user_name=$username;
        $this->mails_imetos=$mails_imetos;
        $this->enable_user=$enable_user;
        //
        $this->id_ftp=$id_ftp;
        $this->user_ftp=$user_ftp;
        $this->passw_ftp=$passw_ftp;
        $this->server_ftp=$server_ftp;
        $this->remotedir_ftp=$remotedir_ftp;
        $this->mails_ftp=$mails_ftp;
        $this->enable_ftp=$enable_ftp;
        //
        $this->id_mysql=$id_mysql;
        $this->user_mysql=$user_mysql;
        $this->passw_mysql=$passw_mysql;
        $this->server_mysql=$server_mysql;
        $this->database_mysql=$database_mysql;
        $this->enable_mysql=$enable_mysql;
        
        $this->error="";
    }
    public function getError()
    {
        return $this->error;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getIsAdmin()
    {
        if(isset($_SESSION['is_admin'])) return $_SESSION['is_admin'];
        return $this->is_admin;
    }
    public function getUsername()
    {
        return $this->user_name;
    }
    public function getEmails()
    {
        return $this->mails_imetos;
    }
    public function getEnableUser()
    {
        return $this->enable_user;
    }
    //
    public function getIdFTP()
    {
        return $this->id_ftp;
    }
    public function getUserFTP()
    {
        return $this->user_ftp;
    }
    public function getPasswFTP()
    {
        return $this->passw_ftp;
    }
    public function getServerFTP()
    {
        return $this->server_ftp;
    }
    public function getRemoteDirFTP()
    {
        return $this->remotedir_ftp;
    }
    public function getEmailsFTP()
    {
        return $this->mails_ftp;
    }
    public function getEnableFTP()
    {
        return $this->enable_ftp;
    }
    //
    public function getIdMySQL()
    {
        return $this->id_mysql;
    }
    public function getUserMySQL()
    {
        return $this->user_mysql;
    }
    public function getPasswMySQL()
    {
        return $this->passw_mysql;
    }
    public function getServerMySQL()
    {
        return $this->server_mysql;
    }
    public function getDatabaseMySQL()
    {
        return $this->database_mysql;
    }
    public function getEnableMySQL()
    {
        return $this->enable_mysql;
    }
    public function ingreso($action="index.php") 
	{
		?>
        <br>
        <h1><?=TITULO;?></h1>
        <form id="frmIngreso" name="frmIngreso" method="post" action="<?=$action;?>">
            <input type="hidden" name="usar_imap" value="1">
            <br>
            <table id="tabla-ingreso">
                <tr>
                	<td colspan="2" align="center">
                        <img src="./img/alerta.png">
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td align="right">Usuario:&nbsp;</td>
                    <td align="left"><input name="usuario" type="text" id="usuario" size="25" maxlength="70"/></td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td align="right">Contrase&ntilde;a:&nbsp;</td>
                    <td align="left"><input name="password" type="password" id="password" size="25" maxlength="50"/></td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td colspan="2" align="center">
                        <button type="submit">
                            <i class="fa fa-sign-in"></i>&nbsp;Ingresar
                        </button>
                    </td>
                </tr>
            </table>
        </form>
		<?php		
	}
    
    public function verificar($usuario,$password)
    {
        // primero verifico que el usuario este en el sistema
        $query="  SELECT  `id`,`username`,`password`,`is_admin`
                FROM    `users` 
                WHERE   `username`='{$usuario}' AND 
                        `usertype`='imetos' LIMIT 1";
        if(sql_select($query, $consulta))
        {
            if($registro=$consulta->fetch(PDO::FETCH_ASSOC))
            {
                if(!AUTENTICAR)
                {
                    // sin autenticar
                    $_SESSION['user_login_session']=true;
                    $_SESSION['userid']=$registro['id'];
                    $_SESSION['user_active']=$registro['username'];
                    $_SESSION['password']=$registro['password'];
                    $_SESSION['is_admin']=$registro['is_admin'];
                    return true;
                }else
                {
                    // luego verifico el login en iMetos
                    $iMetos=new JSON_IMETOS($usuario,$password);
                    if(!$iMetos->get_error() OR !AUTENTICAR)
                    {
                        // bien
                        $_SESSION['user_login_session']=true;
                        $_SESSION['userid']=$registro['id'];
                        $_SESSION['user_active']=$registro['username'];
                        $_SESSION['password']=$registro['password'];
                        $_SESSION['is_admin']=$registro['is_admin'];
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    public function getLoginSession() 
	{
        if(isset($_SESSION['user_login_session']))
        {
			if ($_SESSION['user_login_session']) return true;
        }
        return false;
    }
    
    public function sesion_iniciada()
    {
		if($this->getLoginSession())
		{
			echo "
            <div id=\"sesion-iniciada\">
                <table>
                    <tr>
                        <td>
                            Usted se ha identificado como:
                            <b>".utf8_encode($_SESSION['user_active'])."</b>
                        </td>
                    </tr>";
            /*
            echo "  <tr>
                        <td align=\"right\">
                            <a class=\"sesion-iniciada\" href=\"#\" onclick=\"mostrar_ocultar('configurar-usuario')\"><i class=\"fa fa-user-md\"></i>&nbsp;Configurar usuario</a>
                        </td>
                    </tr>";
             * 
             */
            echo "  <tr>
                        <td align=\"right\">
                            <a class=\"sesion-iniciada\" href=\"index.php?cerrar_sesion\"><i class=\"fa fa-sign-out\"></i>&nbsp;Cerrar sesion</a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id=\"configurar-usuario\" style=\"display:none\">
                <h3>Cambiar contraseña</h3>
                <table>
                    <tr>
                        <td>Ingrese la contraseña actual:</td>
                    </tr>
                    <tr>
                        <td>
                            <input type=\"text\" name=\"password_anterior\" value=\"\">
                        </td>
                    </tr>
                    <tr>
                        <td>Ingrese la contraseña nueva:</td>
                    </tr>
                    <tr>
                        <td>
                            <input type=\"text\" name=\"password_nuevo\" value=\"\">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            
                        </td>
                    </tr>
                </table>
            </div>";
		}
    }

    public function formulario_crear()
    {
        if(isset($_POST['comprobar']))
        {
            if(isset($_POST['username']))
            {
                $username=  CCGetFromPost("username");
            }
            if(isset($_POST['password']))
            {
                $password=  CCGetFromPost("password");
            }
            if(isset($_POST['servidor']))
            {
                $servidor=  CCGetFromPost("servidor");
            }
            if(isset($_POST['directorio']))
            {
                $directorio=  CCGetFromPost("directorio");
            }
            if(isset($_POST['mails']))
            {
                $mails=  CCGetFromPost("mails");
            }
        }else
        {
            $username="";
            $password="";
            $servidor="";
            $directorio="";
            $mails="";
        }
        // agregar nuevo informe
        echo "
            <br><br><br><br><br>
            <a class=\"nuevo-usuario\" href=\"javascript:mostrar_ocultar('nuevo_usuario');\"><i class=\"fa fa-user-plus\"></i>&nbsp;Nuevo usuario iMetos</a>
            <table id='tabla-opciones-general'>
                <tr>
                    <td>
                        <div id=\"nuevo_usuario\" style=\"display:none\">
                            <form name=\"nuevo_informe\" method=\"post\" action=\"index.php\">
                                <table id=\"tabla-nuevo-usuario\">
                                    <tr>
                                        <td>Usuario:</td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"text\" name=\"usuario\" value=\"{$username}\" size=\"70\" maxlength=\"255\"></td>
                                    </tr>
                                    <tr>
                                        <td>Password:</td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"password\" name=\"password\" value=\"{$password}\" size=\"70\" maxlength=\"255\"></td>
                                    </tr>
                                    <tr>
                                        <td>Servidor FTP:</td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"text\" name=\"servidor\" value=\"{$servidor}\" size=\"70\" maxlength=\"1000\"></td>
                                    </tr>
                                    <tr>
                                        <td>Directorio remoto:</td>
                                    </tr>
                                    <tr>
                                        <td><input type=\"text\" name=\"directorio\" value=\"{$directorio}\" size=\"70\" maxlength=\"1000\"></td>
                                    </tr>
                                    <tr>
                                        <td>Mails: (separados por coma)</td>
                                    </tr>
                                    <tr>
                                    <td><input type=\"text\" name=\"mails\" value=\"{$mails}\" size=\"70\" maxlength=\"1000\"></td>
                                    </tr>
                                    <tr><td>&nbsp;</td></tr>
                                    <tr>
                                        <td align=\"right\">
                                            <input type=\"reset\" name=\"cancelar\" value=\"Cancelar\" onclick=\"mostrar_nuevo_informe()\">&nbsp;&nbsp;
                                            <input type=\"submit\" name=\"comprobar\" value=\"Comprobar conexión\">&nbsp;&nbsp;
                                            <input type=\"submit\" name=\"alta_usuario\" value=\"Agregar usuario\">
                                        </td>
                                    </tr>
                                </table>        
                            </form>
                        </div>
                    </td>
                </tr>
            </table>";
    }
    
    public static function getAll()
    {
        //cargar todos los usuarios
        $query="    SELECT  `id`
                    FROM    `users`
                    WHERE   `usertype`='imetos'";
        if(!sql_select($query,$results))
        {
            $this->error="No se pudo leer la tabla Users";
            return false;
        }
        settype($response, 'array');
        while($row=$results->fetch(PDO::FETCH_ASSOC))
        {
            $user= User::load($row['id']);
            $response[$row['id']]=$user;
        }
        return $response;
    }

    public static function load($userid=0, $fromArrayValues = false)
    {
        // cargo usuario 
        if(is_array($fromArrayValues))
        {
            $loadedDataArray = $fromArrayValues;
        }else
        {
            $query="SELECT  `id`,
                            `enable_user`,
                            `create_time`,
                            `username`,
                            `mails`,
                            `is_admin`
                    FROM    `users`
                    WHERE   `usertype`='imetos' AND
                            `id`={$userid} LIMIT 1";
            if(!sql_select($query, $results))
            {
                unset($results);
                $this->error="No se pudo leer la lista de usuarios";
                return false;
            }
            if($user = $results->fetch(PDO::FETCH_ASSOC))
            {
                // ahora busco usuarios ftp
                $query="SELECT  `id`,
                                `enable_user`,
                                `create_time`,
                                `username`,
                                `password`,
                                `server`,
                                `remotedir`,
                                `mails`
                        FROM    `users`
                        WHERE   `usertype`='ftp' AND
                                `userid`={$user['id']} LIMIT 1";
                if(sql_select($query, $results2))
                {
                    if($user_ftp=$results2->fetch(PDO::FETCH_ASSOC))
                    {
                        $user['ftp']=$user_ftp;
                    }
                }
                // ahora verifico el usuario mysql 
                $query="SELECT  `id`,
                                `enable_user`,
                                `create_time`,
                                `username`,
                                `password`,
                                `server`,
                                `database`
                        FROM    `users` 
                        WHERE   `usertype`='mysql' AND 
                                `userid`={$user['id']} LIMIT 1";
                //echo "query mysql--->".$query."<br>";
                if(sql_select($query, $results3))
                {
                    if($user_mysql=$results3->fetch(PDO::FETCH_ASSOC))
                    {
                        $user['mysql']=$user_mysql;
                    }
                }
            }
            unset($results);
            unset($results2);
            unset($results3);
            //
            $n_user= new User($user['id'],
                    $user['is_admin'],
                    $user['username'],
                    $user['mails'],
                    $user['enable_user'],
                    $user['ftp']['id'],
                    $user['ftp']['username'],
                    $user['ftp']['password'],
                    $user['ftp']['server'],
                    $user['ftp']['remotedir'],
                    $user['ftp']['mails'],
                    $user['ftp']['enable_user'],
                    $user['mysql']['id'],
                    $user['mysql']['username'],
                    $user['mysql']['password'],
                    $user['mysql']['server'],
                    $user['mysql']['database'],
                    $user['mysql']['enable_user']);
            return $n_user;
        }
    }
    
    public function listar($is_admin=false, $userid=0)
    {
        $enum_tipos_usuarios=getEnumOptions('usuarios', 'usertype');
        if($users=User::getAll())
        {
            echo "
                <h1>Listado de usuarios iMetos</h1>
                <table class=\"table table-striped table-hover table-bordered table-condensed\">
                    <tr>    
                        <th>&nbsp;</th>
                        <th>Usuario</th>
                        <th>Mails</th>
                    </tr>";
            foreach($users as $user)
            {
                echo "
                    <tr>
                        <td align=\"center\">
                            <a class=\"link-tabla\" href=\"javascript:borrar_usuario('{$user->getId()}');\">
                                <i class=\"fa fa-trash\"></i>
                            </a>&nbsp;";
                if($user->getEnableFTP())
                {
                    echo "  <a class=\"link-tabla\" href=\"javascript:realizar_informe('{$user->getUserFTP()}');\" title=\"Revisar sondas detenidas\">
                                <i class=\"fa fa-terminal\"></i>
                            </a>&nbsp;&nbsp;";
                }
                echo "      <a class=\"link-tabla\" href=\"javascript:mostrar_ocultar('conf_usuario_{$user->getId()}');\" title=\"Editar usuario\">
                                <i class=\"fa fa-user\"></i>
                            </a>&nbsp;&nbsp;";
                echo "      <a class=\"link-tabla\" href=\"javascript:mostrar_ocultar('conf_csv_{$user->getId()}');\" title=\"Configuraci&oacute;n de estaciones\">
                                <i class=\"fa fa-pencil\"></i>
                            </a>";
                echo "  </td>
                        <td>{$user->getUsername()}</td>
                        <td>{$user->getEmails()}</td>
                    </tr>
                    <tr>
                        <td colspan=\"6\">";
                /*
                if($_SESSION['seleccion_usuario']=='conf_usuario_'.$usuario['id'])
                {

                }
                 * 
                 */
                echo "      <div id=\"conf_usuario_{$user->getId()}\" style=\"display:none\">
                                <div class=\"container\">
                                    <div class=\"row\">
                                        <div class=\"col-md-12\" style=\"text-align:center\">
                                            <h2>Editar usuario</h2>
                                        </div>
                                    </div>
                                    <div class=\"row\">
                                        <div class=\"col-md-12\">
                                            <form name=\"editar_usuario\" method=\"post\" action=\"index.php\">
                                            <input type=\"hidden\" name=\"userid\" value=\"{$user->getId()}\">";
                if($user->getEnableFTP())
                {
                    echo "                  <input type=\"hidden\" name=\"id_ftp\" value=\"{$user->getIdFTP()}\">";
                }
                if(isset($usuario['mysql'][0]['id']))
                {
                    echo "                  <input type=\"hidden\" name=\"id_mysql\" value=\"{$user->getIdMySQL()}\">";
                }
                echo "                      <table id=\"tabla-edicion-usuario\">
                                                <tr>
                                                    <td align=\"2\"><dt>Datos de cuenta Fieldclimate</dt></td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">Usuario iMetos:&nbsp;</td>
                                                    <td>
                                                        <input type=\"text\" name=\"usuario_imetos\" value=\"{$user->getUsername()}\" size=\"80\" maxlength=\"255\">&nbsp;
                                                    </td>
                                                </tr>";
                echo "                          <tr>
                                                    <td align=\"right\">
                                                        Mails para el env&iacute;o de informes de exportaci&oacute;n:&nbsp;<br>
                                                        <h6>Para varios mails sep&aacute;relos por coma</h6>
                                                    </td>
                                                    <td>
                                                        <textarea name=\"mails\" rows=\"3\" cols=\"80\">{$user->getEmails()}</textarea>
                                                    </td>
                                                </tr>";
    /*                                    
                                            <tr>
                                                <td align=\"right\">Password iMetos:&nbsp;</td>
                                                <td>
                                                    <input type=\"text\" name=\"Password_imetos\" value=\"\" size=\"80\" maxlength=\"255\">&nbsp;
                                                    <button name=\"verificar_usuario_imetos\">Verificar</button>
                                                </td>
                                            </tr>";
    */
                //if(isset($usuario['ftp'][0]))
                if($user->getEnableFTP())
                {
                    echo "                      <tr>
                                                    <td colspan=\"2\"><hr></td>
                                                </tr>
                                                <tr>
                                                    <td align=\"2\">
                                                        <dt>Datos FTP para el informe de alerta</dt>
                                                        <button name=\"realizar_informe_ahora\">Verificar sondas detenidas</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">Usuario FTP:&nbsp;</td>
                                                    <td>
                                                        <input type=\"text\" name=\"usuario_ftp\" value=\"{$user->getUserFTP()}\" size=\"80\" maxlength=\"255\">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">Password FTP:&nbsp;</td>
                                                    <td>
                                                        <input type=\"password\" name=\"password_ftp\" value=\"{$user->getPasswFTP()}\" size=\"80\" maxlength=\"255\">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">Servidor FTP:&nbsp;</td>
                                                    <td>
                                                        <input type=\"text\" name=\"servidor_ftp\" value=\"{$user->getServerFTP()}\" size=\"80\" maxlength=\"1000\">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">Directorio remoto:&nbsp;</td>
                                                    <td>
                                                        <input type=\"text\" name=\"directorio_remoto\" value=\"{$user->getRemoteDirFTP()}\" size=\"80\" maxlength=\"1000\">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">
                                                        Mails para el env&iacute;o de alertas:&nbsp;<br>
                                                        <h6>Para varios mails sep&aacute;relos por coma</h6>
                                                    </td>
                                                    <td>
                                                        <textarea name=\"mails\" rows=\"3\" cols=\"80\">{$user->getEmailsFTP()}</textarea>
                                                    </td>
                                                </tr>";
                }
                // agrego usuario mysql
                if($user->getEnableMySQL())
                {
                    echo "                      <tr>
                                                    <td colspan=\"2\"><hr></td>
                                                </tr>
                                                <tr>
                                                    <td colspan=\"2\"><dt>Datos de conexión a la base de datos iMetos</dt></td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">Usuario Mysql:&nbsp;</td>
                                                    <td>
                                                        <input type=\"text\" name=\"usuario_mysql\" value=\"{$user->getUserMySQL()}\" size=\"80\" maxlength=\"255\">&nbsp;
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">Password Mysql:&nbsp;</td>
                                                    <td>
                                                        <input type=\"text\" name=\"password_mysql\" value=\"{$user->getPasswMySQL()}\" size=\"80\" maxlength=\"255\">&nbsp;
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">Base de datos Mysql:&nbsp;</td>
                                                    <td>
                                                        <input type=\"text\" name=\"base_datos_mysql\" value=\"{$user->getDatabaseMySQL()}\" size=\"80\" maxlength=\"255\">&nbsp;
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align=\"right\">Servidor Mysql:&nbsp;</td>
                                                    <td>
                                                        <input type=\"text\" name=\"servidor_mysql\" value=\"{$user->getServerMySQL()}\" size=\"80\" maxlength=\"255\">&nbsp;
                                                    </td>
                                                </tr>";
                }
                echo "                          <tr>
                                                    <td colspan=\"2\" align=\"right\">
                                                        <input type=\"reset\" name=\"cancelar_edicion\" value=\"Cancelar\" onclick=\"mostrar_ocultar('usuario_{$user->getId()}')\">&nbsp;
                                                        <input type=\"submit\" name=\"guardar_edicion_usuario\" value=\"Guardar edici&oacute;n\">
                                                    </td>
                                                </tr>
                                            </table>
                                            </form>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                            <div class=\"conf_csv\" id=\"conf_csv_{$user->getId()}\" style=\"display:none\">";
                // obtengo datos de conexion de la base de datos 
                //$conexion=$this->buscar_datos_conexion($usuario['id']);
                // si esta habilitado muestra info de estaciones
                if($user->getEnableMySQL())
                {
                    //$BD = new IMETOS($conexion['servidor'],$conexion['base_datos'],$conexion['usuario'],$conexion['password']);
                    //$BD = new IMETOS($user->getServerMySQL(),$user->getDatabaseMySQL(),$user->getUserMySQL(),$user->getPasswMySQL());
                    if($stations=Station::getAll($user->getServerMySQL(),$user->getDatabaseMySQL(),$user->getUserMySQL(),$user->getPasswMySQL()))
                    {
                        echo "  <div class=\"estaciones\" id=\"estaciones\">
                                    <div class=\"container\">
                                        <div class=\"row\">
                                            <div class=\"col-md-12\" style=\"text-align:center\">
                                                <h2>Configuraci&oacute;n de estaciones</h2>
                                            </div>
                                        </div>
                                        <div class=\"row\">
                                            <label class=\"col-xs-3 control-label\">Seleccione estaci&oacute;n:</label>
                                            <select class=\"form-control\" onChange=\"mostrar_ocultar('estacion_'+this.value,'info-sensores');\">";
                        foreach($stations as $station)
                        {
                            echo "              <option value=\"{$station->getStationCode()}\">{$station->getFName()} - {$station->getName()}</option>";
                        }
                        echo "              </select>
                                        </div>
                                    </div><!-- fin class container -->";
                        $con=0;
                        foreach($stations as $key_est => $station)
                        {
                            //$q_estacion = Station::load($estacion['f_station_code']);
                            //$q_estacion->loadSensors(1);
                            $station->loadSensors(1);
                            //$stationSensorsList = $q_estacion->getAvailableSensors();
                            $stationSensorsList = $station->getAvailableSensors();
                            //$q_configuracion = Config_Station::load($usuario['id'],$q_estacion->getStationCode());
                            $q_config = Config_Station::load($user->getId(),$station->getStationCode());
                            if($key_est == 0)
                            {
                                echo "      <div class=\"info-sensores\" id=\"estacion_{$station->getStationCode()}\" style=\"display:block\">";
                            }else
                            {
                                echo "      <div class=\"info-sensores\" id=\"estacion_{$station->getStationCode()}\" style=\"display:none\">";
                            }
                            echo "              <div class=\"container\">
                                                    <hr class=\"\">
                                                    <div class=\"row\">
                                                        <form class=\"form-horizontal\" role=\"form\" method=\"post\" action=\"index.php\">
                                                            <input type=\"hidden\" id=\"userid\" name=\"userid\" value=\"{$user->getId()}\">
                                                            <input type=\"hidden\" id=\"f_station_code\" name=\"f_station_code\" value=\"{$station->getStationCode()}\">
                                                            <div class=\"col-md-9\">";
                            if($q_config->getActiva())
                            {
                                // estacion activa
                                $disabled="";
                                $label="label-enabled";
                                echo "                          <input class=\"nadas\" type=\"checkbox\" id=\"activar\" name=\"activar\" checked=\"\" onclick=\"estacion_activa(this,'estacion_{$station->getStationCode()}');\">&nbsp;Activar Estaci&oacute;n";
                            }else
                            {
                                // estacion desactivada
                                $disabled="disabled";
                                $label="label-disabled";
                                echo "                          <input class=\"nadas\" type=\"checkbox\" id=\"activar\" name=\"activar\" onclick=\"estacion_activa(this,'estacion_{$station->getStationCode()}');\">&nbsp;Activar Estaci&oacute;n";
                            }
                            echo "                          </div>
                                                            <div class=\"col-md-12\">
                                                                <h3>{$station->getFName()} - {$station->getName()}</h3>
                                                            </div>";
                            echo "                          <div class=\"col-md-4\">
                                                                <div class=\"panel panel-default\">
                                                                    <div class=\"panel-heading\">
                                                                        <h3 class=\"\">Sensores</h3>
                                                                        <h4 class=\"\">Seleccione que sensores que quiere descargar</h4>
                                                                    </div>
                                                                    <div class=\"panel-body\">
                                                                        <input class=\"sensores\" type=\"checkbox\" id=\"sensor-todos-{$station->getStationCode()}\" name=\"{$station->getStationCode()}\" value=\"-9999\" onClick=\"seleccionar_sensores_todos('{$station->getStationCode()}');\"><label for=\"{$label}\" {$disabled}>&nbsp;Todos</label><br>";
                            foreach($stationSensorsList['enabled'] as $key_sensor => $sensor)
                            {
                                if(in_array($key_sensor,$q_configuracion->getSensores()))
                                {
                                    echo "                              <input class=\"sensores-todos\" type=\"checkbox\" id=\"sensor-{$station->getStationCode()}\" name=\"sensor_".$sensor->getSensorCode()."_".$sensor->getSensorCh()."\" value=\"seleccionado\" checked=\"\">&nbsp;<label for=\"{$label}\">{$sensor->getName()}</label><br>";
                                }else
                                {
                                    echo "                              <input class=\"sensores-todos\" type=\"checkbox\" id=\"sensor-{$station->getStationCode()}\" name=\"sensor_".$sensor->getSensorCode()."_".$sensor->getSensorCh()."\" value=\"seleccionado\">&nbsp;<label for=\"{$label}\">{$sensor->getName()}</label><br>";
                                }   
                            }
                            echo "                                  </div>
                                                                </div>
                                                            </div>";
                            echo "                          <div class=\"col-md-4\">
                                                                <div class=\"panel panel-default\">
                                                                    <div class=\"panel-heading\">
                                                                        <h3 class=\"\">Configuraci&oacute;n</h3>
                                                                    </div>
                                                                    <div class=\"panel-body\">";
                            // Periodo a descargar  
                            echo "                                      <div class=\"form-group\">
                                                                            <label for=\"{$label}\">Per&iacute;odo a descargar:</label><br>
                                                                            <label class=\"radio-inline\">";
                            if($q_configuracion->getPeriodo()=='periodo')
                            {
                                echo "                                          <input class=\"todos\" type=\"radio\" name=\"periodo\" id=\"descarga_periodo\" value=\"periodo\" checked=\"\" {$disabled}>&nbsp;<label for=\"{$label}\">Descarga de datos desde</label>";
                            }else
                            {
                                echo "                                          <input class=\"todos\" type=\"radio\" name=\"periodo\" id=\"descarga_periodo\" value=\"periodo\" {$disabled}>&nbsp;<label for=\"{$label}\">Descarga de datos desde</label>";
                            }
                            echo "                                          </label><br>
                                                                            <label for=\"{$label}\">Fecha inicial:&nbsp;</label><input type=\"text\" class=\"todos\" name=\"fecha_inicial\" id=\"fecha_inicial\" value=\"{$q_configuracion->getPeriodoFechaInicial()}\" size=\"8\" maxlength=\"8\" {$disabled}><label for=\"{$label}\">(dd/mm/aaaa)</label><br>
                                                                            <label for=\"{$label}\">Fecha final:&nbsp;</label><input type=\"text\" class=\"todos\" name=\"fecha_final\" id=\"fecha_final\" value=\"{$q_configuracion->getPeriodoFechaFinal()}\" size=\"8\" maxlength=\"8\" {$disabled}><label for=\"{$label}\">(dd/mm/aaaa)</label><br>
                                                                            <label class=\"radio-inline\">";
                            if($q_configuracion->getPeriodo()=='mes_actual')
                            {
                                echo "                                          <input class=\"todos\" type=\"radio\" name=\"periodo\" id=\"mes_actual\" value=\"mes_actual\" checked=\"\" {$disabled}>&nbsp;<label for=\"{$label}\">Mes actual</label>";
                            }else
                            {
                                echo "                                          <input class=\"todos\" type=\"radio\" name=\"periodo\" id=\"mes_actual\" value=\"mes_actual\" {$disabled}>&nbsp;<label for=\"{$label}\">Mes actual</label>";
                            }
                            echo "                                          </label>
                                                                            <br>
                                                                            <label class=\"radio-inline\">";
                            if($q_configuracion->getPeriodo()=='todos')
                            {
                                echo "                                          <input class=\"todos\" type=\"radio\" name=\"periodo\" id=\"todos2\" value=\"todos\" checked=\"\" {$disabled}>&nbsp;<label for=\"{$label}\">Desde el principio</label>";
                            }else
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" name=\"periodo\" id=\"todos2\" value=\"todos\" {$disabled}>&nbsp;<label for=\"{$label}\">Desde el principio</label>";
                            }
                            echo "                                      </label>
                                                                        <br>
                                                                        <label class=\"radio-inline\">";
                            if($q_configuracion->getPeriodo()=='')
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" name=\"periodo\" id=\"fijo\" value=\"fijo\" checked=\"\" {$disabled}>&nbsp;<label for=\"{$label}\">Per&iacute;odo fijo</label>";
                            }else
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" name=\"periodo\" id=\"fijo\" value=\"fijo\" {$disabled}>&nbsp;<label for=\"{$label}\">Per&iacute;odo fijo</label>";
                            }
                            echo "                                       </label>
                                                                    </div>";
                            // Tipo de archivo a exportar
                            echo "                                  <div class=\"form-group\">
                                                                        <label for=\"{$label}\">Exportar a tipo de archivo:</label><br>
                                                                        <label class=\"radio-inline\">";
                            if($q_configuracion->getTipoArchivo()=='txt')
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" name=\"tipo_archivo\" id=\"archivo_txt\" value=\"txt\" checked=\"\" {$disabled}><label for=\"{$label}\">TXT</label>";
                            }else
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" name=\"tipo_archivo\" id=\"archivo_txt\" value=\"txt\" {$disabled}><label for=\"{$label}\">TXT</label>";
                            }
                            echo "                                      </label>
                                                                        <label class=\"radio-inline\">";
                            if($q_configuracion->getTipoArchivo()=='csv')
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" name=\"tipo_archivo\" id=\"archivo_csv\" value=\"csv\" checked=\"\" {$disabled}><label for=\"{$label}\">CSV</label>";
                            }else
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" name=\"tipo_archivo\" id=\"archivo_csv\" value=\"csv\" {$disabled}><label for=\"{$label}\">CSV</label>";
                            }
                            echo "                                      </label>
                                                                    </div>";
                            // Separador de columnas
                            echo "                                  <div class=\"form-group\">
                                                                        <label for=\"{$label}\">Separar columnas por:</label><br>
                                                                        <label class=\"radio-inline\">";
                            if($q_configuracion->getSeparador()=='coma')
                            {
                                echo "                                     <input class=\"todos\" type=\"radio\" id=\"coma\" name=\"separador\" value=\"coma\" checked=\"\" {$disabled}>&nbsp;<label for=\"{$label}\">COMA</label>";
                            }else
                            {
                                echo "                                     <input class=\"todos\" type=\"radio\" id=\"coma\" name=\"separador\" value=\"coma\" {$disabled}>&nbsp;<label for=\"{$label}\">COMA</label>";
                            }
                            echo "                                      </label>
                                                                        <br>
                                                                        <label class=\"radio-inline\">";
                            if($q_configuracion->getSeparador()=='punto_coma')
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"punto_coma\" name=\"separador\" value=\"punto_coma\" checked=\"\" {$disabled}>&nbsp;<label for=\"{$label}\">PUNTO y COMA</label>";
                            }else
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"punto_coma\" name=\"separador\" value=\"punto_coma\" {$disabled}>&nbsp;<label for=\"{$label}\">PUNTO y COMA</label>";
                            }
                            echo "                                      </label>
                                                                        <br>
                                                                        <label class=\"radio-inline\">";
                            if($q_configuracion->getSeparador()=='tab')
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"tab\" name=\"separador\" value=\"tab\" checked=\"\" {$disabled}>&nbsp;<label for=\"{$label}\">TAB</label>";
                            }else
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"tab\" name=\"separador\" value=\"tab\" {$disabled}>&nbsp;<label for=\"{$label}\">TAB</label>";
                            }
                            echo "                                      </label>
                                                                        <br>
                                                                        <label class=\"radio-inline\">";
                            if($q_configuracion->getSeparador()=='espacio')
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"espacio\" name=\"separador\" value=\"espacio\" checked=\"\">&nbsp;<label for=\"{$label}\">ESPACIO</label>";
                            }else
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"espacio\" name=\"separador\" value=\"espacio\">&nbsp;<label for=\"{$label}\">ESPACIO</label>";
                            }

                            echo "                                       </label>
                                                                    </div>";
                            // Agregar encabezado                   
                            echo "                                  <div class=\"form-group\">
                                                                        <label for=\"{$label}\">Agregar encabezado:</label>
                                                                        <br>
                                                                        <label class=\"radio-inline\">";
                            if($q_configuracion->getEncabezado()=='si')
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"encabezado_si\" name=\"encabezado\" value=\"si\" checked=\"\" {$disabled}>&nbsp;<label for=\"{$label}\">S&iacute;</label>&nbsp;&nbsp;";
                            }else
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"encabezado_si\" name=\"encabezado\" value=\"si\" {$disabled}>&nbsp;<label for=\"{$label}\">S&iacute;</label>&nbsp;&nbsp;";
                            }
                            echo "                                      </label>
                                                                        <label class=\"radio-inline\">";
                            if($q_configuracion->getEncabezado()=='no')
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"encabezado_no\" name=\"encabezado\" value=\"no\" checked=\"\" {$disabled}>&nbsp;<label for=\"{$label}\">No</label>";
                            }else
                            {
                                echo "                                      <input class=\"todos\" type=\"radio\" id=\"encabezado_no\" name=\"encabezado\" value=\"no\" {$disabled}>&nbsp;<label for=\"{$label}\">No</label>";
                            }
                            echo "                                      </label>
                                                                    </div>";
                            // Nombre de archivo dde salida
                            echo "                                  <div class=\"form-group\">
                                                                        <label for=\"{$label}\">Nombre de archivo (sin extension):</label><br>
                                                                        <input class=\"todos\" type=\"text\" id=\"archivo\" name=\"archivo\" size=\"40\" maxlength=\"50\" {$disabled}>
                                                                    </div>";
                            echo "                              </div>
                                                            </div>
                                                        </div>";
                            /*
                            echo "                      <div class=\"col-md-4\">
                                                            <div class=\"panel panel-default\">
                                                                <div class=\"panel-heading\">
                                                                    <h2 class=\"\">Alertas</h2>
                                                                </div>
                                                                <div class=\"panel-body\">";
                            echo "                              </div>
                                                            </div>
                                                        </div>";
                            */
                            echo "                  </div> <!-- cierre de div row -->";
                            echo "                  <div class=\"row\">
                                                        <div class=\"col-md-12\">
                                                            <div class=\"pull-right\">
                                                                <button type=\"submit\" name=\"guardar_configuracion\" class=\"btn btn-default\">Guardar configuraci&oacute;n</button><br><br>
                                                            </div>
                                                        </div>
                                                    </div>";
                            echo "              </form>
                                            </div> <!-- cierre de div container -->
                                        </div> <!-- cierre de div info-sensores -->";
                        }
                    }
                }
                echo "      </div> <!-- cierre de div conf_csv -->
                        </td>
                    </tr>";
            }
            echo "</table>";
        }else
        {
            echo "No se pudo cargar los usuarios<br>";
        }
    }

    public function cargar_todos()
    {
        // primero busco el usuario imetos
        $usuarios=array();
        $query="    SELECT  * 
                    FROM    `users` 
                    WHERE   `usertype`='imetos'";
        if(!sql_select($query, $consulta))
        {
            unset($consulta);
            return false;
        }
        $con=0;
        while($usuario = $consulta->fetch(PDO::FETCH_ASSOC))
        {
            $usuarios[$con]=$usuario;
            // ahora busco usuarios ftp
            $query="  SELECT  * 
                    FROM    `users` 
                    WHERE   `usertype`='ftp' AND 
                            `userid`={$usuario['id']}";
            if(sql_select($query,$consulta2))
            {
                while($registro = $consulta2->fetch(PDO::FETCH_ASSOC))
                {
                    $usuarios[$con]['ftp'][]=$registro;
                }
            }
            $query="  SELECT  * 
                    FROM    `users` 
                    WHERE   `usertype`='mysql' AND 
                            `userid`={$usuario['id']}";
            if(sql_select($query,$consulta3))
            {
                while($registro = $consulta3->fetch(PDO::FETCH_ASSOC))
                {
                    $usuarios[$con]['mysql'][]=$registro;
                }
            }
            $con++;
        }
        unset($consulta);
        unset($consulta2);
        unset($consulta3);
        return $usuarios;
    }
    
    public function insertar()
    {
        // inserta usuario ftp
        $usuario_ftp=   CCGetFromPost('usuario');
        $password_ftp=  CCGetFromPost('password');
        $servidor_ftp=  CCGetFromPost('servidor');
        $directorio_remoto= CCGetFromPost('directorio');
        $mails=  CCGetFromPost('mails');
        $create_time=time();
        $query="  INSERT INTO `users` 
                    (`enable_user`,`create_time`,`username`,`password`,`server`,
                    `remotedir`,`is_admin`,`usertype`,`mails`) 
                VALUES (1,{$create_time},'{$usuario_ftp}','{$password_ftp}','{$servidor_ftp}','{$directorio_remoto}',0,'ftp','{$mails}')";
        if(!sql_select($query, $consulta))
        {
            unset($consulta);
            return false;
        }
        unset($consulta);
        return true;
    }
    
    public function actualizar()
    {
        $error=false;
        // imetos
        $userid=  CCGetFromPost("userid");
        $usuario=  CCGetFromPost("username"); // usuario iMetos
        // ftp
        $userid_ftp=CCGetFromPost("id_ftp");
        $usuario_ftp= CCGetFromPost("usuario_ftp"); // usuario ftp
        $password_ftp=  CCGetFromPost("password_ftp"); // password ftp
        $servidor_ftp= CCGetFromPost("servidor_ftp"); // servidor ftp
        $directorio_remoto=  CCGetFromPost("directorio_remoto"); 
        $mails=  CCGetFromPost("mails");
        // mysql
        $userid_mysql=  CCGetFromPost("id_mysql");
        $usuario_mysql=  CCGetFromPost("usuario_mysql");
        $password_mysql=  CCGetFromPost("password_mysql");
        $servidor_mysql=  CCGetFromPost("servidor_mysql");
        //
        $query="    UPDATE  `users`
                    SET     `username`='".$usuario."',
                        `password`='',
                        `server`='',
                        `directorio_remoto`='',
                        `is_admin`=0,
                        `usertype`='imetos',
                        `mails`=''
                    WHERE   `id`={$userid}";
        if(!sql_select($query, $consulta))
        {
            // hubo un error
            $error=true;
        }
        // ftp
        $query="  UPDATE  `users`
                SET     `username`='{$usuario_ftp}',
                        `password`='{$password_ftp}',
                        `server`='{$servidor_ftp}',
                        `remotedir`='{$directorio_remoto}',
                        `is_admin`=0,
                        `usertype`='ftp',
                        `mails`='{$mails}'
                WHERE   `id`={$userid_ftp}";
        if(!sql_select($query, $consulta2))
        {
            // hubo un error
            $error=true;
        }
        // mysql
        $query="  UPDATE  `users`
                SET     `username`='{$usuario_mysql}',
                        `password`='{$password_mysql}',
                        `server`='{$servidor_mysql}',
                        `remotedir`='',
                        `is_admin`=0,
                        `usertype`='mysql',
                        `mails`=''
                WHERE   `id`={$userid_mysql}";
        if(!sql_select($query, $consulta3))
        {
            // hubo un error
            $error=true;
        }
        if($error) return false;
        return true;
    }
            
    public function borrar_usuario($userid=0)
    {
        if($userid==0) return false;
        $query="  DELETE FROM `users` 
                WHERE `id`={$userid}";
        if(!sql_select($query, $consulta))
        {
            unset($consulta);
            return false;
        }
        unset($consulta);
        return true;
    }
    
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
        if(sql_select($query, $consulta))
        {
            while($registro = $consulta->fetch(PDO::FETCH_ASSOC))
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
        unset($consulta);
    }
    /*
    private function buscar_datos_conexion($userid)
    {
        $query="  SELECT  *
                FROM    `users`
                WHERE   `usertype`='mysql' AND
                        `userid`={$userid}";
        if(!sql_select($query,$consulta))
        {
            echo "ERROR! No se pudo determinar datos de conexion a la base de datos mysql";
            return false;
        }
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
     * 
     */   
    public function cerrar_sesion()
    {
        unset($_SESSION['user_login_session']);
        unset($_SESSION['password']);
        unset($_SESSION['userid']);
        unset($_SESSION['user_active']);
        unset($_SESSION['is_admin']);
        session_unset();
        session_destroy();
    }
    /*
     * Informes sondas detenidas
     * 
     */
    public function informes_sondas_detenidas($informes)
    {
        // para todos los informes
        $cadena= "
            <br><br>
            <!-- <button type=\"submit\" name=\"hago_informe\"><i class=\"fa fa-terminal\"></i>&nbsp;&nbsp;Realizar informe</button> -->
            <br>
            <h1>Listado de informes de sondas detenidas</h1>
            <table class=\"table table-striped table-hover table-bordered table-condensed\">
                <tr>
                    <th class=\"text-right\">
                        <a class=\"link-tabla\" href=\"javascript:borrar_todos();\" title=\"Borrar todos\">
                            <i class=\"fa fa-trash\"></i>&nbsp;&nbsp;&nbsp;
                        </a>
                    </th>";
        if($_SESSION['es_admin'])
        {
            $cadena.="  <th>Usuario</th>";
        }
        $cadena.="      <th>Fecha</th>
                </tr>";    
        foreach($informes as $informe)
        {
            $cadena.="<tr>
                    <td align=\"right\">
                        <a class=\"link-tabla\" href=\"javascript:mostrar_ocultar('informe_{$informe['id_informe']}');\" title=\"Ver informe\">
                            <i class=\"fa fa-eye\"></i>
                        </a>&nbsp;&nbsp;
                        <a class=\"link-tabla\" href=\"javascript:borrar_informe('{$informe['id_informe']}');\" title=\"Borrar informe\">
                            <i class=\"fa fa-trash-o\"></i>
                        </a>&nbsp;&nbsp;
                    </td>";
            if($_SESSION['es_admin'])
            {
                $cadena.="
                    <td>{$informe['usuario']}</td>";
            }
            $cadena.="  <td>{$informe['fecha']}</td>
                </tr>
                <tr>";
            if($_SESSION['es_admin'])
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
                </tr>";
        }
        return $cadena;
    }
    private function presento_informe($xml_informe)
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
    public function hago_informes($argv,$lo_guardo=false)
    {
        if(!isset($argv[1]))
        {
            echo "ERROR! No existe el usuario.\n";
            return false;
        }
        $usuario=CCStrip($argv[1]);
        if($usuario=="todos")
        {
            $query="SELECT    *
                  FROM      `users` 
                  WHERE     `activo`=1 AND 
                            `usertype`='ftp'";
        }else
        {
            $query="SELECT    *
                  FROM      `users` 
                  WHERE     `enable_user`=1 AND 
                            `usertype`='ftp' AND 
                            `username`='{$usuario}' LIMIT 1";
        }
        if(sql_select($query, $consulta))
        {
            if($consulta->rowCount()==0)
            {
                echo "ERROR! ".$usuario." no corresponde con un usuario cargado en el sistema.\n";
            }
            while($registro = $consulta->fetch(PDO::FETCH_ASSOC))
            {
                hago_informe($registro,$lo_guardo);
            }
        }
        unset($consulta);
    }    
    private function hago_informe($registro,$lo_guardo=false)
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
                    $query="INSERT INTO `informes` 
                                (`userid`,`informe`,`fecha`) 
                            VALUES 
                                ({$registro['id']},'{$informe}','{$fecha_actual}')";
                    if(sql_select($query, $consulta))
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
        unset($consulta);
    }
    private function analizo_sondas($sondas)
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
    
    public function comprobar_conexion()
    {
        $usuario=  CCGetFromPost('usuario');
        $password=  CCGetFromPost('password');
        $servidor=  CCGetFromPost('servidor');
        $directorio_remoto= CCGetFromPost('directorio');
        if(!$obj_ftp=new FTP($servidor,$usuario,$password,$directorio_remoto))
        {
            unset($consulta);
            return false;
        }
        unset($consulta);
        return true;
    }
    
    public function borrar_informe($id_informe=0)
    {
        if($id_informe==0) return false;
        $query="  DELETE FROM `informes`
                WHERE `id`={$id_informe}";
        if(sql_select($query, $consulta))
        {
            unset($consulta);
            return true;
        }
        unset($consulta);
        return false;
    }
    
    public function borrar_informes_todos($userid=0)
    {
        if($userid) return false;
        $query="  DELETE FROM `informes` 
                WHERE `userid`={$userid}";
        if(sql_select($query, $consulta))
        {
            unset($consulta);
            return true;
        }
        unset($consulta);
        return false;
    }    
    
    private function proceso_fecha($fecha)
    {
        $dia=substr($fecha,-2);
        $mes=substr($fecha,-4,2);
        $anio=intval(substr($fecha,0,2))+2000;
        return $dia."/".$mes."/".$anio;
    }
    
    private function fecha_vencida($dato)
    {
        $ahora=mktime(0,0,0,date("n"),date("j"),date("Y"));
        if(strlen($dato['fecha'])==6)
        {
            $anio=intval(substr($dato['fecha'],0,2))+2000;
            $mes=substr($dato['fecha'],2,2);
            $dia=substr($dato['fecha'],4,2);
            $fdato=mktime(0,0,0,$mes,$dia,$anio);
            $fecha_ahora=new DateTime(date("Y")."-".date("m")."-".date("d"));
            $fecha_dato=new DateTime($anio."-".$mes."-".$dia);
            $dife=  $this->dateTimeDiff($fecha_ahora, $fecha_dato);
            if($dife->d>DIFERENCIA_DIAS)
            {
                // si diferencia es mayor a DIFERENCIA_DIAS envio mail
                return true;
            }
            return false;
        }
    }
    
    private function dateTimeDiff($date1, $date2)
    {
        $alt_diff = new stdClass();
        $alt_diff->y =  floor(abs($date1->format('U') - $date2->format('U')) / (60*60*24*365));
        $alt_diff->m =  floor((floor(abs($date1->format('U') - $date2->format('U')) / (60*60*24)) - ($alt_diff->y * 365))/30);
        $alt_diff->d =  floor(floor(abs($date1->format('U') - $date2->format('U')) / (60*60*24)) - ($alt_diff->y * 365) - ($alt_diff->m * 30));
        $alt_diff->h =  floor( floor(abs($date1->format('U') - $date2->format('U')) / (60*60)) - ($alt_diff->y * 365*24) - ($alt_diff->m * 30 * 24 )  - ($alt_diff->d * 24) );
        $alt_diff->i = floor( floor(abs($date1->format('U') - $date2->format('U')) / (60)) - ($alt_diff->y * 365*24*60) - ($alt_diff->m * 30 * 24 *60)  - ($alt_diff->d * 24 * 60) -  ($alt_diff->h * 60) );
        $alt_diff->s =  floor( floor(abs($date1->format('U') - $date2->format('U'))) - ($alt_diff->y * 365*24*60*60) - ($alt_diff->m * 30 * 24 *60*60)  - ($alt_diff->d * 24 * 60*60) -  ($alt_diff->h * 60*60) -  ($alt_diff->i * 60) );
        $alt_diff->invert =  (($date1->format('U') - $date2->format('U')) > 0)? 0 : 1 ;
        return $alt_diff;
    }    

}