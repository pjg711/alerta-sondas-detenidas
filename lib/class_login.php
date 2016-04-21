<?php
/**
 * Description of class_login
 *
 * @author pablo
 */
class Login 
{
    /**
     * 
     * @return type boolean
     */
    public static function getIsAdmin()
    {
        if(isset($_SESSION['is_admin'])) return $_SESSION['is_admin'];
        return false;
    }
    /**
     * 
     * @param type $action is string
     */
    public static function login_session()
	{
        echo "
        <br>\n
        <h1>".TITULO."</h1>
        <form id=\"frmLogin\" name=\"frmLogin\" method=\"post\" action=\"/login\">
            <input type=\"hidden\" name=\"usar_imap\" value=\"1\">
            <br>
            <table id=\"tabla-ingreso\">
                <tr>
                	<td colspan=\"2\" align=\"center\">
                        <img src=\"./img/enviroscan.png\">
                    </td>
                </tr>
                <tr><td colspan=\"2\">&nbsp;</td></tr>
                <tr>
                    <td align=\"right\">Usuario:&nbsp;</td>
                    <td align=\"left\"><input name=\"username\" type=\"text\" id=\"username\" size=\"25\" maxlength=\"70\" /></td>
                </tr>
                <tr><td colspan=\"2\">&nbsp;</td></tr>
                <tr>
                    <td align=\"right\">Contrase&ntilde;a:&nbsp;</td>
                    <td align=\"left\"><input name=\"password\" type=\"password\" id=\"password\" size=\"25\" maxlength=\"50\"/></td>
                </tr>
                <tr><td colspan=\"2\">&nbsp;</td></tr>
                <tr>
                    <td colspan=\"2\" align=\"center\">
                        <button type=\"submit\"><i class=\"fa fa-sign-in\"></i>&nbsp;Ingresar</button>
                    </td>
                </tr>
            </table>
        </form>";
	}    
    /**
     * Muestra informacion de la sesion iniciada
     * @param type $isAdmin is boolean
     */
    public static function logged($isAdmin=false)
    {
		if(Login::getLoginSession())
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
            if($isAdmin)
            {
                echo "  
                    <tr>
                        <td align=\"right\">
                            <a class=\"sesion-iniciada\" data-toggle=\"modal\" data-target=\"#configurarUsuario\"><i class=\"fa fa-user-md\"></i>&nbsp;Configuraci&oacute;n usuario</a>
                        </td>
                    </tr>";
            }
            echo "  <tr>
                        <td align=\"right\">
                            <a class=\"sesion-iniciada\" href=\"/sign_off\"><i class=\"fa fa-sign-out\"></i>&nbsp;Cerrar sesion</a>
                        </td>
                    </tr>
                </table>
            </div>";
            if($isAdmin)
            {
                echo "  <!-- Modal -->
                    <div id=\"configurarUsuario\" class=\"modal modal-wide fade\" role=\"dialog\">
                        <div class=\"modal-dialog\">
                            <!-- Modal content-->
                            <div class=\"modal-content\">
                                <div class=\"modal-header\">
                                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                                    <h4 class=\"modal-title\">Cambiar contraseña</h4>
                                </div>
                                <div class=\"modal-body\">
                                    <form class=\"form-horizontal\" role=\"form\" method=\"post\" action=\"/users/config\">
                                        <table width=\"99%\">
                                            <tr>
                                                <td>Ingrese la contraseña actual:</td>
                                                <td>
                                                    <input type=\"text\" name=\"password_anterior\" value=\"\" size=\"40\" maxlength=\"255\">
                                                </td>
                                            </tr>
                                            <tr><td colspan=\"2\">&nbsp;</td></tr>
                                            <tr>
                                                <td>Ingrese la contraseña nueva:</td>
                                                <td>
                                                    <input type=\"text\" name=\"password_nuevo\" value=\"\" size=\"40\" maxlength=\"255\">
                                                </td>
                                            </tr>
                                            <tr><td colspan=\"2\">&nbsp;</td></tr>
                                            <tr>
                                                <td>Repita la contraseña nueva:</td>
                                                <td>
                                                    <input type=\"text\" name=\"password_nuevo2\" value=\"\" size=\"40\" maxlength=\"255\">
                                                </td>
                                            </tr>
                                            <!--
                                            <tr><td colspan=\"2\">&nbsp;</td></tr>
                                            <tr>
                                                <td colspan=\"2\" align=\"right\">
                                                    <button type=\"submit\" name=\"config_admin\" class=\"btn btn-default\">Guardar nueva contraseña</button>&nbsp;
                                                </td>
                                            </tr>
                                            -->
                                        </table>
                                    </form>
                                </div>
                                <div class=\"modal-footer\">
                                    <button type=\"submit\" name=\"config_admin\" class=\"btn btn-default\">Guardar nueva contraseña</button>&nbsp;
                                    <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>";
            }
		}
    }
    /**
     * Verifica usuario y crear variables de sesion si es true con la informacion de:
     * user_login_session, userid, user_active, password, is_admin
     * 
     * @param type $usuario is string
     * @param type $password is string
     * @return boolean
     */
    public static function verify_user($usuario="",$password="")
    {
        // primero verifico que el usuario este en el sistema
        $query="SELECT  `id`,`username`,`password`,`is_admin`
                FROM    `" . SESSION_NAME . "users` 
                WHERE   `username`='{$usuario}' AND 
                        (`usertype`='imetos' OR `usertype`='sistema') LIMIT 1";
        echo "query--->{$query}<br>";
        if(sql_select($query, $results))
        {
            echo "bien 2<br>";
            if($registro=$results->fetch(PDO::FETCH_ASSOC))
            {
                echo "bien 3<br>";
                if(!AUTENTICAR)
                {
                    echo "bien 4<br>";
                    // sin autenticar
                    $_SESSION['user_login_session']=true;
                    $_SESSION['userid']=$registro['id'];
                    $_SESSION['user_active']=$registro['username'];
                    $_SESSION['password']=$registro['password'];
                    $_SESSION['is_admin']=$registro['is_admin'];
                    return true;
                }else
                {
                    echo "bien 5<br>";
                    if($registro['is_admin']==0)
                    {
                        // es usuario imetos y verifico el login en iMetos
                        echo "bien 6<br>";
                        $iMetos=new JSON_IMETOS($usuario,$password);
                        if(!$iMetos->get_error() OR !AUTENTICAR)
                        {
                            // bien
                            echo "bien 7<br>";
                            $_SESSION['user_login_session']=true;
                            $_SESSION['userid']=$registro['id'];
                            $_SESSION['user_active']=$registro['username'];
                            $_SESSION['password']=$registro['password'];
                            $_SESSION['is_admin']=$registro['is_admin'];
                            return true;
                        }
                    }else
                    {
                        // es tipo de usuario sistema
                        echo "bien 8<br>";
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
    /**
     * retorna si esta abierta una sesion o no
     * @return boolean
     */
    public static function getLoginSession() 
	{
        if(isset($_SESSION['user_login_session']))
        {
			if($_SESSION['user_login_session']) return true;
        }
        return false;
    }
    /**
     * 
     */
    public static function SignOff()
    {
        unset($_SESSION['user_login_session']);
        unset($_SESSION['password']);
        unset($_SESSION['userid']);
        unset($_SESSION['user_active']);
        unset($_SESSION['is_admin']);
        session_unset();
        session_destroy();
    }
}