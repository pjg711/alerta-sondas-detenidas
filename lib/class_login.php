<?php

class Login 
{
    private $es_admin=0;
    
    public function get_es_admin()
    {
        if(isset($_SESSION['es_admin'])) return $_SESSION['es_admin'];
        return $this->es_admin;
    }
	public function ingreso_usuario($action="index.php") 
	{
		?>
        <br>
        <h1>Sistema de alerta de sondas detenidas</h1>
        <form id="frmIngreso" name="frmIngreso" method="post" action="<?=$action;?>">
            <input type="hidden" name="usar_imap" value="1">
            <br>
            <table class="tabla_ingreso">
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
                        <!-- <input type="submit" name="ingreso" value="Ingresar"/> -->
                        <button type="submit">
                            <i class="fa fa-sign-in"></i>&nbsp;Ingresar
                        </button>
                    </td>
                </tr>
            </table>
        </form>
		<?php		
	}
    public function verifico_usuario($usuario,$password)
    {
        $obj_BD=new BD();
        $sql="SELECT * FROM `usuarios` WHERE usuario='".$usuario."' AND password='".$password."' LIMIT 1";
        if($obj_BD->sql_select($sql, $consulta))
        {
            if($registro=$consulta->fetch(PDO::FETCH_ASSOC))
            {
                $_SESSION['user_login_session']=true;
                $_SESSION['id_usuario']=$registro['id'];
                $_SESSION['user_active']=$registro['usuario'];
                $_SESSION['password']=$registro['password'];
                $_SESSION['es_admin']=$registro['es_admin'];
                return true;
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
            echo "  <tr>
                        <td align=\"right\">
                            <a href=\"#\" onclick=\"mostrar_ocultar('configurar-usuario')\">Configurar usuario</a>
                        </td>
                    </tr>";
            echo "  <tr>
                        <td align=\"right\">
                            <a href=\"index.php?cerrar_sesion\">Cerrar sesion</a>
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
    public function cerrar_sesion()
    {
        unset($_SESSION['user_login_session']);
        unset($_SESSION['password']);
        unset($_SESSION['id_usuario']);
        unset($_SESSION['user_active']);
        unset($_SESSION['es_admin']);
        session_unset();
        session_destroy();
    }
}