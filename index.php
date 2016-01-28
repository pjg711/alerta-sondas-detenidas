<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);
require_once("lib/class_base-datos.php");
require_once("lib/class_login.php");
require_once("lib/class_ftp.php");
require_once("lib/class_pagina.php");
//
include("config.php");
//
if(isset($argv))
{
    if(count($argv)==2)
    {
        // llamado desde un script en el cron...
        // el 2do argumento es el nombre de usuario
        // busco el nombre de usuario en la base de datos
        $sql="SELECT * FROM `usuarios` WHERE `usuario`='".$argv[1]."' LIMIT 1";
        hago_informe($sql,true);
        exit;
    }
}
$login=new Login();
// borrar informe
if($login->getLoginSession())
{
    if(isset($_GET['confirmado_borrar_informe']))
    {
        //print_r($_SERVER)."<br>";
        $id_informe=$_GET['id'];
        echo "borro el informe con id ".$id_informe."<br>";
    }
    if(isset($_GET['cambiar_conf']))
    {
        
    }
}
//
$pagina=new PAGINA();
//$pagina->analizo_argumentos($argv);
$pagina->encabezado();
//
if(isset($_GET['cerrar_sesion']))
{
    $login->cerrar_sesion();
}
if(!$login->getLoginSession())
{
    if(isset($_POST['usuario']) and isset($_POST['password']))
    {
        $usuario=CCGetFromPost("usuario");
        $password=CCGetFromPost("password");
        if($login->verifico_usuario($usuario, $password))
        {
            // bien
        }else
        {
            echo "Error en dato de usuario y/o contraseña<br><br>";
        }
    }else
    {
        $login->ingreso_usuario();
    }
}
if($login->getLoginSession())
{
    $login->sesion_iniciada();
    if($login->get_es_admin())
    {
        if(isset($_POST['alta_usuario']))
        {
            //inserto usuario nuevo
            insertar_usuario();
        }
        if(isset($_POST['comprobar']))
        {
            //comprobar la conexion al sitio ftp
            comprobar_conexion();
        }
        /* usuarios */
        if(isset($_POST['guardar_edicion_usuario']))
        {
            if(actualizar_usuario())
            {
                
            }
        }
        // es usuario admin y presento todos los informes ordenados por fecha
        opciones_admin();
        listado_usuarios_ftp(true);
        // todos los informes
        listado_informes();
    }else
    {
        $id_usuario=$_SESSION['id_usuario'];
        // solo los informes de usuario ftp $id_usuario
        listado_informes($id_usuario);
    }
    if(isset($_POST['comprobar']))
    {
        // vuelvo a mostrar el div
        ?>
        <script LANGUAGE="JavaScript">
            mostrar_ocultar('nuevo_usuario',true);
        </script>
        <?php
    }
    // 
    if(!isset($_SESSION['id_usuario']))
    {
        echo "El usuario de FTP no existe<br>";
        exit;
    }
}
$pagina->pie();
exit;
/*******************************************************/
/* ------------------FUNCIONES ------------------------*/
/*******************************************************/
function hago_informe($sql,$lo_guardo=false)
{
    $obj_BD=new BD();
    if($obj_BD->sql_select($sql, $consulta))
    {
        // hago el informe y lo guardo
        if($registro=$consulta->fetch(PDO::FETCH_ASSOC))
        {
            $servidor=trim(utf8_decode($registro['servidor']));
            $usuario=trim(utf8_decode($registro['usuario']));
            $password=trim(utf8_decode($registro['password']));
            $directorio=trim(utf8_decode($registro['directorio_remoto']));
            if($obj_ftp=new FTP($servidor,$usuario,$password,$directorio))
            {
                // hago el informe
                if($informe=analizo_sondas($obj_ftp->get_listado()))
                {
                    if($lo_guardo)
                    {
                        // y lo guardo en la base de datos
                        $fecha_actual=date("Y-m-d H:i:s");
                        $sql="INSERT INTO `informes` (`id_usuario`,`informe`,`fecha`) VALUES (".$registro['id'].",'".$informe."','".$fecha_actual."')";
                        if($obj_BD->sql_select($sql, $consulta))
                        {
                            echo "Se inserto informe\n";
                        }
                    }
                }
            }
        }
    }
    unset($obj_BD);
}
function presento_informes($informes)
{
    echo "
        <br><br><br>
        <h1>Listado de informes realizados</h1>
        <table class=\"table table-striped table-hover table-bordered table-condensed\">
            <tr>
                <th>&nbsp;</th>
                <th>Fecha</th>
                <th>Informe</th>
                <th>&nbsp;</th>
            </tr>";    
    foreach($informes as $informe)
    {
        echo "<tr>
                <td align=\"right\"><a href=\"#\" onclick=\"borrar_informe(\"".$informe['id']."\",\"¿Está seguro que quiere borrar el informe?\")\"><img src='./img/b_drop.png'>&nbsp;</a></td>
                <td>";
        echo $informe['fecha'].
                "</td>
                 <td>
                    <div id=\"ver_informe_".$informe['id']."\" style=\"display:block\">
                        <a href=\"#\" onclick=\"mostrar_informe(\"".$informe['id']."\")\">Ver informe</a>
                    </div>
                    <div id=\"mostrar_informe_".$informe['id']."\" style=\"display:none\">
                        <a href=\"#\" onclick=\"mostrar_informe(\"".$informe['id']."\")\">Cerrar informe</a>";
                        if($texto_informe=presento_informe(trim($informe['informe'])))
                        {
                            echo $texto_informe;
                        }
        echo "      </div>
                </td>
             </tr>";
    }

}
function presento_informe($xml_informe)
{
    //echo "xml_informe--->".htmlentities($xml_informe)."<br>";
    $dom = new DOMDocument;
    $dom->loadXML($xml_informe);
    if(!$dom)
    {
        echo 'Error en el xml';
        return false;
    }
    $s = simplexml_import_dom($dom);
    if($s->cantidad_sondas==0) return false;
    $cadena=
        "<table id='tabla-informe'>
            <tr>
                <th>nombre</th>
                <th>nro. archivos</th>
                <th>ultima fecha</th>
                <th>m&aacute;s informaci&oacute;n</th>
            </tr>";
    foreach($s as $sonda)
    {
        if($sonda->fuera_fecha=='Si')
        {
            $cadena.="<tr bgcolor=\"#D49590\">";
        }else
        {
            $cadena.="<tr bgcolor=\"#A6D490\">";
        }
        $cadena.="  <td>".$sonda->nombre."</td>
                    <td>".$sonda->nro_archivos."</td>
                    <td>".proceso_fecha($sonda->ultima_fecha)."</td>";
        if(!is_null($sonda->mas_info))
        {
            $cadena.=
                "<td>
                    <div style=\"display:block\">
                        <a href=\"#\" onclick=\"mostrar_archivo(\"".$sonda->nombre."\")\">Detalles</a>
                    </div>
                    <div style='display:none'>".
                        $sonda->mas_info.
                "       pongo archivo aqui
                    </div>
                </td>";
        }
        $cadena.="</tr>";
    }
    $cadena.="</table>";
    unset($dom);
    return $cadena;
}
function proceso_fecha($fecha)
{
    $dia=substr($fecha,-2);
    $mes=substr($fecha,-4,2);
    $anio=intval(substr($fecha,0,2))+2000;
    return $dia."/".$mes."/".$anio;
}
function analizo_sondas($sondas)
{
    if(!is_array($sondas)) 
    {
        echo "La variable $sondas no es un array\n";
        return false;
    }
    $cadena="";
    foreach($sondas as $key => $sonda)
    {
        if(substr($key,-4)==".esp")
        {
            $partes=explode("-",$key);
            if(count($partes)==4)
            {   
                // es sonda
                $agrego=array("archivo"=>$key,"sonda"=>$partes[0],"fecha"=>$partes[2]);
                $sonda=array_merge($sonda,$agrego);
                $q_sondas[$partes[0]][]=$sonda;
                if(!isset($q_sondas_cantidad[$partes[0]])) $q_sondas_cantidad[$partes[0]]=0;
                $q_sondas_cantidad[$partes[0]]++;
            }
        }
    }
    $cadena.="
        <?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <sondas>
            <cantidad_sondas>".count($q_sondas_cantidad)."</cantidad_sondas>";
    $sonda_fuera=0;
    foreach($q_sondas_cantidad as $key => $cantidad)
    {
        $cadena.="<sonda>";
        // fuera de fecha?
        if(fecha_vencida($q_sondas[$key][count($q_sondas[$key])-1]))
        {
            $sonda_fuera++;
            $cadena.="  <fuera_fecha>Si</fuera_fecha>";
        }else
        {
            $cadena.="  <fuera_fecha>No</fuera_fecha>";
        }
        $cadena.="      <nombre>".$key."</nombre>
                        <nro_archivos>".$cantidad."</nro_archivos>
                        <ultima_fecha>".$q_sondas[$key][count($q_sondas[$key])-1]['fecha']."</ultima_fecha>
                        <mas_info>";
        $cadena.=print_r($q_sondas[$key][count($q_sondas[$key])-1]);
        $cadena.="      </mas_info>";
        $cadena.="</sonda>";
    }
    $cadena.="
            <cantidad_sondas_fuera_fecha>".$sonda_fuera."</cantidad_sondas_fuera_fecha>
        </sondas>";
    return trim($cadena);
}
function fecha_vencida($dato)
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
        $dife=$fecha_ahora->diff($fecha_dato);
        if($dife->days>DIFERENCIA_DIAS)
        {
            // si diferencia es mayor a DIFERENCIA_DIAS envio mail
            return true;
        }
        return false;
    }
}
function opciones_admin()
{
    // agregar nuevo informe
    echo "
        <br><br><br><br><br>
        <div class=\"nuevo-usuario\">
            <a href=\"#\" onclick=\"mostrar_ocultar('nuevo_usuario')\"><img src=\"./img/nuevo_informe.png\">&nbsp;Nuevo usuario FTP</a>
        </div>
        <table id='tabla-opciones-general'>
            <tr>
                <td>
                    <div id=\"nuevo_usuario\" style=\"display:none\">
                        <form name=\"nuevo_informe\" method=\"post\" action=\"index.php\">
                            <table id=\"tabla-nuevo-usuario\">
                                <tr>
                                    <td>Usuario FTP:</td>
                                </tr>
                                <tr>
                                    <td><input type=\"text\" name=\"usuario\" size=\"70\" maxlength=\"255\"></td>
                                </tr>
                                <tr>
                                    <td>Password FTP:</td>
                                </tr>
                                <tr>
                                    <td><input type=\"text\" name=\"password\" size=\"70\" maxlength=\"255\"></td>
                                </tr>
                                <tr>
                                    <td>Servidor FTP:</td>
                                </tr>
                                <tr>
                                    <td><input type=\"text\" name=\"servidor\" size=\"70\" maxlength=\"1000\"></td>
                                </tr>
                                <tr>
                                    <td>Directorio remoto:</td>
                                </tr>
                                <tr>
                                    <td><input type=\"text\" name=\"directorio\" size=\"70\" maxlength=\"1000\"></td>
                                </tr>
                                <tr>
                                    <td>Mails: (separados por coma)</td>
                                </tr>
                                <tr>
                                    <td><input type=\"text\" name=\"mails\" size=\"70\" maxlength=\"1000\"></td>
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
function listado_usuarios_ftp($es_admin=false)
{
    $obj_BD=new BD();
    $enum_tipos_usuarios=$obj_BD->getEnumOptions('usuarios', 'tipo_usuario');
    //print_r($datos_enum);
    //exit;
    if($usuarios=cargar_usuarios())
    {
        echo "
            <h1>Listado de usuarios</h1>
            <table class=\"table table-striped table-hover table-bordered table-condensed\">
                <tr>    
                    <th colspan=\"2\">&nbsp;</th>
                    <th>Usuario FTP</th>
                    <th>Servidor FTP</th>
                    <th>Directorio remoto</th>
                    <th>Tipo usuario</th>
                    <th>Mails</th>
                </tr>";
        foreach($usuarios as $usuario)
        {
            echo "
                <tr>
                    <td colspan=\"2\" align=\"right\">
                        <a href=\"#\" onclick=\"borrar_usuario('".$usuario['id']."')\"><img src='./img/b_drop.png'></a>&nbsp;
                        <a href=\"#\" onclick=\"mostrar_ocultar('usuario_".trim($usuario['id'])."')\"><img src='./img/b_edit.png'></a>
                    </td>
                    <td>".$usuario['usuario']."</td>
                    <td>".$usuario['servidor']."</td>
                    <td>".$usuario['directorio_remoto']."</td>
                    <td>".$usuario['tipo_usuario']."</td>
                    <td>".$usuario['mails']."</td>
                </tr>
                <tr>
                    <td colspan=\"6\">
                        <div id=\"usuario_".trim($usuario['id'])."\" style=\"display:none\">
                            <form name=\"editar_usuario\" method=\"post\" action=\"index.php\">
                                <input type=\"hidden\" name=\"id_usuario\" value=\"".$usuario['id']."\">
                                <table id=\"tabla-edicion-usuario\">
                                    <tr>
                                        <td align=\"right\">Usuario FTP:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"usuario\" value=\"".$usuario['usuario']."\" size=\"80\" maxlength=\"255\">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Password FTP:&nbsp;</td>
                                        <td>
                                            <input type=\"password\" name=\"password\" value=\"".$usuario['password']."\" size=\"80\" maxlength=\"255\">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Servidor FTP:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"servidor\" value=\"".$usuario['servidor']."\" size=\"80\" maxlength=\"1000\">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Directorio remoto:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"directorio_remoto\" value=\"".$usuario['directorio_remoto']."\" size=\"80\" maxlength=\"1000\">
                                        </td>
                                    </tr>";
            if($es_admin)
            {
                echo "              <tr>
                                        <td align=\"right\">Tipo de usuario:&nbsp;</td>
                                        <td>
                                            <select name=\"tipo_usuario\">";
                foreach($enum_tipos_usuarios as $key_enum => $enum_tipo_usuario)
                {
                    if($usuario['tipo_usuario']==$enum_tipo_usuario)
                    {
                        echo "                  <option value=\"".$key_enum."\" selected>".$enum_tipo_usuario."</option>";
                    }else
                    {
                        echo "                  <option value=\"".$key_enum."\">".$enum_tipo_usuario."</option>";
                    }
                }
                echo "                      </select>
                                        </td>
                                    </tr>";
            }
            echo "                  <tr>
                                        <td valign=\"top\" align=\"right\">Mails:&nbsp;</td>
                                        <td>
                                            <h6>Ingrese varios mails separados por coma</h6>
                                            <textarea rows=\"3\" cols=\"80\">".$usuario['mails']."</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"2\" align=\"right\">
                                            <input type=\"reset\" name=\"cancelar_edicion\" value=\"Cancelar\" onclick=\"mostrar_ocultar('usuario_".trim($usuario['id'])."')\">&nbsp;
                                            <input type=\"submit\" name=\"guardar_edicion_usuario\" value=\"Guardar edición\">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </td>
                </tr>";
        }
        echo "</table>";
    }else
    {
        echo "No se pudo cargar los usuarios<br>";
    }
}
function cargar_usuarios()
{
    $sql="SELECT * FROM `usuarios` ORDER BY `fecha_alta` ASC";
    //echo $sql."<br>";
    $obj_BD=new BD();
    $usuarios=array();
    if(!$obj_BD->sql_select($sql, $consulta))
    {
        return false;
    }
    while($usuario = $consulta->fetch(PDO::FETCH_ASSOC))
    {
        $usuarios[]=$usuario;
    }
    unset($obj_BD);
    return $usuarios;
}
function actualizar_usuario()
{
    $id_usuario=  CCGetFromPost("id_usuario");
    $usuario=  CCGetFromPost("usuario");
    $password=  CCGetFromPost("password");
    $servidor= CCGetFromPost("servidor");
    $directorio_remoto=  CCGetFromPost("directorio_remoto");
    $tipo_usuario=  CCGetFromPost("tipo_usuario");
    $mails=  CCGetFromPost("mails");
    $sql="  UPDATE usuarios 
            SET `usuario`='".$usuario."',
                `password`='".$password."',
                `servidor`='".$servidor."',
                `directorio_remoto`='".$directorio_remoto."',
                `tipo_usuario`='".$tipo_usuario."',
                `mails`='".$mails."'
            WHERE `id`=".$id_usuario;
    $obj_BD=new BD();
    if(!$obj_BD->sql_select($sql, $consulta))
    {
        mensaje("ERROR! Problema al actualizar usuario");
        return false;
    }
    mensaje("Se actualizó usuario");
    return true;
}
function listado_informes($id_usuario=0)
{
    if($id_usuario==0)
    {
        $sql="SELECT * FROM `informes` ORDER BY `fecha` DESC";
    }else
    {
        $sql="SELECT * FROM `informes` WHERE `id_usuario`=".$id_usuario." ORDER BY `fecha` DESC";
    }
    // muestro tabla con informes para el usuario logeado
    $obj_BD=new BD();
    $informes=array();
    if($obj_BD->sql_select($sql, $consulta))
    {
        while($registro = $consulta->fetch(PDO::FETCH_ASSOC))
        {
            $informes[]=$registro;
        }
        if(!is_null($informes))
        {
            presento_informes($informes);
        }else
        {
            echo "No hay informes que mostrar<br>";
        }
    }
    unset($obj_BD);
}
function insertar_usuario()
{
    $usuario_ftp=  CCGetFromPost('usuario');
    $password_ftp=  CCGetFromPost('password');
    $servidor_ftp=  CCGetFromPost('servidor');
    $directorio_remoto= CCGetFromPost('directorio');
    $mails=  CCGetFromPost('mails');
    $fecha_alta=time();
    $obj_BD=new BD();
    $sql="INSERT INTO `usuarios` (`activo`,`fecha_alta`,`usuario`,`password`,`servidor`,`directorio_remoto`,
                `es_admin`,`tipo_usuario`,`mails`) 
            VALUES (1,".$fecha_alta.",'".$usuario_ftp."','".$password_ftp."','".$servidor_ftp."','".$directorio_remoto."',0,'ftp','".$mails."'";
    if(!$obj_BD->sql_select($sql, $consulta))
    {
        echo "ERROR! No se pudo guardar el usuario<br>";
    }
    unset($obj_BD);
}
function comprobar_conexion()
{
    $usuario=  CCGetFromPost('usuario');
    $password=  CCGetFromPost('password');
    $servidor=  CCGetFromPost('servidor');
    $directorio_remoto= CCGetFromPost('directorio');
    if($obj_ftp=new FTP($servidor,$usuario,$password,$directorio_remoto))
    {
        mensaje("Conexion al servidor con exito");
    }
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
function mensaje($texto)
{
    echo "<script type=\"text/javascript\">";
    echo "alert(\"".utf8_decode($texto)."\");";
    echo "</script>";
}
?>