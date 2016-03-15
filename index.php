<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);
ini_set('memory_limit','128M');
//
require 'lib/class_imetos.php';
require 'lib/class_login2.php';
require 'lib/class_ftp.php';
require 'lib/class_pagina.php';
//
include("config.php");
//
if(isset($argv))
{
    if(count($argv)==2)
    {
        // llamado desde un script en el cron...
        // el 2do argumento es el nombre de usuario... 
        // o "todos" para realizar el informe para todos los usuarios tipos FTP
        // ejemplo: php index.php monsanto.seedmech.com.ar
        // busco el nombre de usuario en la base de datos
        hago_informes($argv,true);
        exit;
    }
}

$login=new Login();

if($login->getLoginSession())
{
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    //
    if(isset($_POST['confirmado_borrar_informe']))
    {
        $id_informe=$_POST['confirmado_borrar_informe'];
        if(borrar_informe($id_informe))
        {
            mensaje("Se borr\u00F3 el informe");
        }else
        {
            mensaje("ERROR! No se pudo borrar el informe");
        }
    }
    if(isset($_POST['confirmado_borrar_todos']))
    {
        if(isset($_SESSION['id_usuario']))
        {
            $id_usuario=$_SESSION['id_usuario'];
            borrar_informes($id_usuario);
        }
    }
    if(isset($_POST['guardar_configuracion']))
    {
        if(actualizar_estacion())
        {
            
        }
    }
    if(isset($_GET['cambiar_conf']))
    {
        
    }    
}
//
$pagina=new PAGINA();
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
            mensaje("Error en dato de usuario y/o contraseña");
            redireccionar("index.php");
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
        if(isset($_POST['comprobar']))
        {
            //comprobar la conexion al sitio ftp
            if(comprobar_conexion())
            {
                mensaje("Conexi\u00F3n al servidor con \u00E9xito.");
            }else
            {
                mensaje("ERROR! Revise los datos de la conexi\u00F3n al servidor.");
            }
        }
        if(isset($_POST['alta_usuario']))
        {
            //inserto usuario nuevo
            if(insertar_usuario())
            {
                mensaje("Se guard\u00F3 el nuevo usuario.");
            }else
            {
                mensaje("ERROR! No se pudo guardar el usuario.");
            }
        }
        if(isset($_POST['guardar_edicion_usuario']))
        {
            if(actualizar_usuario())
            {
                mensaje("Se actualiz\u00F3 el usuario");
            }else
            {
                mensaje("ERROR! Problema al actualizar usuario");
            }
        }
        if(isset($_POST['confirmado_borrar_usuario']))
        {
            $id_usuario= CCGetFromPost("confirmado_borrar_usuario");
            if(borrar_usuario($id_usuario))
            {
                mensaje("El usuario fue borrado");
            }else
            {
                mensaje("ERROR! No se pudo borrar el usuario");
            }
        }
        if(isset($_POST['realizar_informe']))
        {
            $usuario=array();
            $usuario[1]=  CCGetFromPost("realizar_informe");
            hago_informes($usuario,true);
        }
        // es usuario admin y presento todos los informes ordenados por fecha
        nuevo_usuario();
        //
        listado_usuarios(true);
        // listado de archivos csv
        //listado_csvs();
        // todos los informes
        listado_informes();
    }else
    {
        $id_usuario=$_SESSION['id_usuario'];
        //
        listado_usuarios(false);
        // solo los informes de usuario ftp $id_usuario
        listado_informes($id_usuario);
    }
    if(isset($_POST['comprobar']))
    {
        // vuelvo a mostrar el div
        ?>
        <script LANGUAGE="JavaScript">
            mostrar_ocultar('nuevo_usuario');
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
function hago_informes($argv,$lo_guardo=false)
{
    if(!isset($argv[1]))
    {
        echo "ERROR! No existe el usuario.\n";
        return false;
    }
    $usuario=CCStrip($argv[1]);
    if($usuario=="todos")
    {
        $sql="SELECT    *
              FROM      `usuarios` 
              WHERE     `activo`=1 AND `tipo_usuario`='ftp'";
    }else
    {
        $sql="SELECT    *
              FROM      `usuarios` 
              WHERE     `activo`=1 AND `tipo_usuario`='ftp' AND `usuario`='".$usuario."' LIMIT 1";
    }
    if(sql_select($sql, $consulta))
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
function hago_informe($registro,$lo_guardo=false)
{
    $servidor=trim(utf8_decode($registro['servidor']));
    $usuario=trim(utf8_decode($registro['usuario']));
    $password=trim(utf8_decode($registro['password']));
    $directorio=trim(utf8_decode($registro['directorio_remoto']));
    $emails=explode(",",$registro['mails']);
    if($obj_ftp=new FTP($servidor,$usuario,$password,$directorio))
    {
        // hago el informe
        if($informe=analizo_sondas($obj_ftp->get_listado()))
        {
            if($lo_guardo)
            {
                // y lo guardo en la base de datos
                $fecha_actual=date("Y-m-d H:i:s");
                $sql="INSERT INTO `informes_sondas_detenidas` (`id_usuario`,`informe`,`fecha`) VALUES (".$registro['id'].",'".$informe."','".$fecha_actual."')";
                if(sql_select($sql, $consulta))
                {
                    //echo "Se inserto informe\n";
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
function informes_sondas_detenidas($informes)
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
                    <a class=\"link-tabla\" href=\"javascript:mostrar_ocultar('informe_".$informe['id_informe']."');\" title=\"Ver informe\">
                        <i class=\"fa fa-eye\"></i>
                    </a>&nbsp;&nbsp;
                    <a class=\"link-tabla\" href=\"javascript:borrar_informe('".$informe['id_informe']."');\" title=\"Borrar informe\">
                        <i class=\"fa fa-trash-o\"></i>
                    </a>&nbsp;&nbsp;
                </td>";
        if($_SESSION['es_admin'])
        {
            $cadena.="
                <td>".$informe['usuario']."</td>";
        }
        $cadena.="  <td>".$informe['fecha']."</td>
            </tr>
            <tr>";
        if($_SESSION['es_admin'])
        {
            $cadena.="<td colspan=\"3\">";
        }else
        {
            $cadena.="<td colspan=\"2\">";
        }
        $cadena.="      <div id=\"informe_".$informe['id_informe']."\" style=\"display:none\">";
        if($texto_informe=presento_informe(trim($informe['informe'])))
        {
            $cadena.=$texto_informe;
        }
        $cadena.="      </div>
                </td>
            </tr>";
    }
    return $cadena;
}
function presento_informe($xml_informe)
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
            $cadena.="  <td>".$sonda->nombre."</td>
                        <td align=\"center\">".$sonda->nro_archivos."</td>
                        <td align=\"center\">".proceso_fecha($sonda->ultima_fecha)."</td>";
            if($sonda->mas_info<>"")
            {
                $cadena.=
                    "<td align=\"center\">
                        <div style=\"display:block\">
                            <a href=\"javascript:mostrar_ocultar('sonda_".$sonda->nombre."');\" title=\"M&aacute;s informaci&oacute;n\">
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
                //$contenido_archivo=file_get_contents_utf8("temp/".$sonda->mas_info);
                $contenido_archivo=str_replace("\n","<br>",file_get_contents("temp/".$sonda->mas_info));
                $cadena.="
                    <td colspan=\"4\">
                        <div id=\"sonda_".$sonda->nombre."\" style='display:none'>
                            Archivo  :".$sonda->mas_info."<br>";
                if($sonda->fecha_mas_info<>"")
                {
                    //$cadena.="Fecha    :".date("d-m-Y H:i:s",$sonda->fecha_mas_info)."<br>";
                    $fecha=intval($sonda->fecha_mas_info);
                    $cadena.="Fecha    :".date("d-m-Y H:i:s",$fecha)."<br>";
                }
                $cadena.="  Contenido:<br><hr><div id=\"contenido-txt\">".$contenido_archivo."<hr></div><br>
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
        if(fecha_vencida($q_sondas[$key][count($q_sondas[$key])-1]))
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
        $cadena.="<nombre>".$key."</nombre>";
        $cadena.="<nro_archivos>".$cantidad."</nro_archivos>";
        $cadena.="<ultima_fecha>".$q_sondas[$key][count($q_sondas[$key])-1]['fecha']."</ultima_fecha>";
        $cadena.="<mas_info>".$contenido_archivo_comunicacion2."</mas_info>";
        $cadena.="<fecha_mas_info>".$fecha_comunicacion2."</fecha_mas_info>";
        $cadena.="</sonda>";
    }
    $cadena.="<cantidad_sondas_fuera_fecha>".$sonda_fuera."</cantidad_sondas_fuera_fecha></sondas>";
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
        //$dife=$fecha_ahora->diff($fecha_dato);
        $dife=  dateTimeDiff($fecha_ahora, $fecha_dato);
        //if($dife->days>DIFERENCIA_DIAS)
        if($dife->d>DIFERENCIA_DIAS)
        {
            // si diferencia es mayor a DIFERENCIA_DIAS envio mail
            return true;
        }
        return false;
    }
}
function dateTimeDiff($date1, $date2)
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
function borrar_informe($id_informe=0)
{
    if($id_informe==0) return false;
    $sql="DELETE FROM `informes_sondas_detenidas` WHERE `id`=".$id_informe;
    if(sql_select($sql, $consulta))
    {
        unset($consulta);
        return true;
    }
    unset($consulta);
    return false;
}
function borrar_informes($id_usuario=0)
{
    if($id_usuario==0) return false;
    $sql="DELETE FROM `informes_sondas_detenidas` WHERE `id_usuario`=".$id_usuario;
    if(sql_select($sql, $consulta))
    {
        unset($consulta);
        return true;
    }
    unset($consulta);
    return false;
}
function nuevo_usuario()
{
    if(isset($_POST['comprobar']))
    {
        if(isset($_POST['usuario']))
        {
            $usuario=  CCGetFromPost("usuario");
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
        $usuario="";
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
                                    <td><input type=\"text\" name=\"usuario\" value=\"".$usuario."\" size=\"70\" maxlength=\"255\"></td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                </tr>
                                <tr>
                                    <td><input type=\"password\" name=\"password\" value=\"".$password."\" size=\"70\" maxlength=\"255\"></td>
                                </tr>
                                <tr>
                                    <td>Servidor FTP:</td>
                                </tr>
                                <tr>
                                    <td><input type=\"text\" name=\"servidor\" value=\"".$servidor."\" size=\"70\" maxlength=\"1000\"></td>
                                </tr>
                                <tr>
                                    <td>Directorio remoto:</td>
                                </tr>
                                <tr>
                                    <td><input type=\"text\" name=\"directorio\" value=\"".$directorio."\" size=\"70\" maxlength=\"1000\"></td>
                                </tr>
                                <tr>
                                    <td>Mails: (separados por coma)</td>
                                </tr>
                                <tr>
                                    <td><input type=\"text\" name=\"mails\" value=\"".$mails."\" size=\"70\" maxlength=\"1000\"></td>
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

function listado_usuarios($es_admin=false, $id_usuario=0)
{
    $enum_tipos_usuarios=getEnumOptions('usuarios', 'tipo_usuario');
    if($usuarios=cargar_usuarios())
    {
        echo "
            <h1>Listado de usuarios iMetos</h1>
            <table class=\"table table-striped table-hover table-bordered table-condensed\">
                <tr>    
                    <th>&nbsp;</th>
                    <th>Usuario</th>
                    <!--
                    <th>Servidor</th>
                    <th>Directorio remoto</th> 
                    <th>Tipo usuario</th>
                    -->
                    <th>Mails</th>
                </tr>";
        foreach($usuarios as $usuario)
        {
            echo "
                <tr>
                    <td align=\"center\">
                        <a class=\"link-tabla\" href=\"javascript:borrar_usuario('".$usuario['id']."');\">
                            <i class=\"fa fa-trash\"></i>
                        </a>&nbsp;";
            if($usuario['tipo_usuario']=="ftp")
            {
                echo "  <a class=\"link-tabla\" href=\"javascript:realizar_informe('".$usuario['usuario']."');\" title=\"Realizar informe\">
                            <i class=\"fa fa-terminal\"></i>
                        </a>&nbsp;&nbsp;";
            }
            echo "      <a class=\"link-tabla\" href=\"javascript:mostrar_ocultar('conf_usuario_".trim($usuario['id'])."');\" title=\"Editar usuario\">
                            <i class=\"fa fa-user\"></i>
                        </a>&nbsp;&nbsp;";
            echo "      <a class=\"link-tabla\" href=\"javascript:mostrar_ocultar('conf_csv_".trim($usuario['id'])."');\" title=\"Configuraci&oacute;n de estaciones\">
                            <i class=\"fa fa-pencil\"></i>
                        </a>";
            echo "  </td>
                    <td>".$usuario['usuario']."</td>
                    <!--
                    <td>".$usuario['servidor']."</td>
                    <td>".$usuario['directorio_remoto']."</td>
                    <td>".$usuario['tipo_usuario']."</td>
                    -->
                    <td>".$usuario['ftp'][0]['mails']."</td>
                </tr>
                <tr>
                    <td colspan=\"6\">
                        <div id=\"conf_usuario_".trim($usuario['id'])."\" style=\"display:none\">
                            <form name=\"editar_usuario\" method=\"post\" action=\"index.php\">
                                <input type=\"hidden\" name=\"id_usuario\" value=\"".$usuario['id']."\">";
            if(isset($usuario['ftp'][0]['id']))
            {
                echo "          <input type=\"hidden\" name=\"id_ftp\" value=\"".$usuario['ftp'][0]['id']."\">";
            }
            if(isset($usuario['mysql'][0]['id']))
            {
                echo "          <input type=\"hidden\" name=\"id_mysql\" value=\"".$usuario['mysql'][0]['id']."\">";
            }
            echo "              <table id=\"tabla-edicion-usuario\">
                                    <tr>
                                        <td align=\"2\"><dt>Datos de cuenta Fieldclimate</dt></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Usuario iMetos:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"usuario_imetos\" value=\"".$usuario['usuario']."\" size=\"80\" maxlength=\"255\">&nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Password iMetos:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"Password_imetos\" value=\"\" size=\"80\" maxlength=\"255\">&nbsp;
                                            <button name=\"verificar_usuario_imetos\">Verificar</button>    
                                        </td>
                                    </tr>";
            if(isset($usuario['ftp'][0]))
            {
                echo "              <tr>
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
                                            <input type=\"text\" name=\"usuario_ftp\" value=\"".$usuario['ftp'][0]['usuario']."\" size=\"80\" maxlength=\"255\">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Password FTP:&nbsp;</td>
                                        <td>
                                            <input type=\"password\" name=\"password_ftp\" value=\"".$usuario['ftp'][0]['password']."\" size=\"80\" maxlength=\"255\">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Servidor FTP:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"servidor_ftp\" value=\"".$usuario['ftp'][0]['servidor']."\" size=\"80\" maxlength=\"1000\">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Directorio remoto:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"directorio_remoto\" value=\"".$usuario['directorio_remoto']."\" size=\"80\" maxlength=\"1000\">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">
                                            Mails para el env&iacute;o de alertas:&nbsp;<br>
                                            <h6>Para varios mails sep&aacute;relos por coma</h6>
                                        </td>
                                        <td>
                                            <textarea name=\"mails\" rows=\"3\" cols=\"80\">".$usuario['mails']."</textarea>
                                        </td>
                                    </tr>";
            }
            // agrego usuario mysql
            if(isset($usuario['mysql'][0]))
            {
                echo "              <tr>
                                        <td colspan=\"2\"><hr></td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"2\"><dt>Datos de conexión a la base de datos iMetos</dt></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Usuario Mysql:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"usuario_mysql\" value=\"".$usuario['mysql'][0]['usuario']."\" size=\"80\" maxlength=\"255\">&nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Password Mysql:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"password_mysql\" value=\"".$usuario['mysql'][0]['password']."\" size=\"80\" maxlength=\"255\">&nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Base de datos Mysql:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"base_datos_mysql\" value=\"".$usuario['mysql'][0]['base_datos']."\" size=\"80\" maxlength=\"255\">&nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Servidor Mysql:&nbsp;</td>
                                        <td>
                                            <input type=\"text\" name=\"servidor_mysql\" value=\"".$usuario['mysql'][0]['servidor']."\" size=\"80\" maxlength=\"255\">&nbsp;
                                        </td>
                                    </tr>";
            }
            echo "                  <tr>
                                        <td colspan=\"2\" align=\"right\">
                                            <input type=\"reset\" name=\"cancelar_edicion\" value=\"Cancelar\" onclick=\"mostrar_ocultar('usuario_".trim($usuario['id'])."')\">&nbsp;
                                            <input type=\"submit\" name=\"guardar_edicion_usuario\" value=\"Guardar edici&oacute;n\">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                        <div class=\"conf_csv\" id=\"conf_csv_".trim($usuario['id'])."\" style=\"display:none\">";
            // obtengo datos de conexion de la base de datos
            $conexion=buscar_datos_conexion($usuario['id']);
            //
            $BD = new IMETOS($conexion['servidor'],$conexion['base_datos'],$conexion['usuario'],$conexion['password']);
            $sql="  SELECT  *
                    FROM    `seedclima_station_info`";
            $estaciones=array();
            if($BD->sql_select($sql, $consulta))
            {
                while($estacion=$consulta->fetch(PDO::FETCH_ASSOC))
                {
                    $estaciones[]=$estacion;
                }
            }
            unset($BD);
            echo "          <div class=\"estaciones\" id=\"estaciones\">
                                <label class=\"col-xs-3 control-label\">Seleccione estaci&oacute;n:</label>
                                <select class=\"form-control\" onChange=\"mostrar_ocultar('estacion_'+this.value,'info-sensores');\">";
            foreach($estaciones as $estacion)
            {
                echo "              <option value=\"".$estacion['f_station_code']."\">".$estacion['f_name']." - ".$estacion['f_user_station_name']."</option>";
            }
            echo "              </select>
                            </div>";
            foreach($estaciones as $key_est => $estacion)
            {
                $q_estacion = Station::load($estacion['f_station_code']);
                $q_estacion->loadSensors(1);
                $stationSensorsList = $q_estacion->getAvailableSensors();
                //$info=cargar_informacion_estacion($q_estacion->getStationCode(),$usuario['id']);
                $q_configuracion = Config_Station::load($usuario['id'],$q_estacion->getStationCode());
                echo "<pre>";
                print_r($q_configuracion);
                echo "</pre>";
                if($key_est == 0)
                {
                    echo "  <div class=\"info-sensores\" id=\"estacion_".$q_estacion->getStationCode()."\" style=\"display:block\">";
                }else
                {
                    echo "  <div class=\"info-sensores\" id=\"estacion_".$q_estacion->getStationCode()."\" style=\"display:none\">";
                }
                echo "          <div class=\"container\">
                                    <hr class=\"\">
                                    <div class=\"row\">
                                        <div class=\"col-md-9\">
                                            <h2>".$q_estacion->getFName()." - ".$q_estacion->getName()."</h2>
                                        </div>";
                echo "                  <form class=\"form-horizontal\" role=\"form\" method=\"post\" action=\"index.php\">
                                            <input type=\"hidden\" name=\"id_usuario\" value=\"".$usuario['id']."\">
                                            <input type=\"hidden\" name=\"f_station_code\" value=\"".$q_estacion->getStationCode()."\">
                                            <div class=\"col-md-4\">
                                                <div class=\"panel panel-default\">
                                                    <div class=\"panel-heading\">
                                                        <h2 class=\"\">Sensores</h2>
                                                        <h4 class=\"\">Seleccione que sensores que quiere descargar</h4>
                                                    </div>
                                                    <div class=\"panel-body\">
                                                        <input type=\"checkbox\" id=\"sensor_todos\" name=\"".$estacion['f_station_code']."\" value=\"-9999\" onClick=\"seleccionar_sensores_todos('".$q_estacion->getStationCode()."');\">Todos<br>";
                foreach($stationSensorsList['enabled'] as $key_sensor => $sensor)
                {
                    //echo "                              <input type=\"checkbox\" id=\"sensor_todos\" name=\"sensor_".$sensor->getSensorCh()."_".$sensor->getSensorCode()."\" value=\"".$sensor->getSensorCh()."|".$sensor->getSensorCode()."\">&nbsp;".$sensor->getName()."<br>";
                    if(in_array($key_sensor,$q_configuracion->getSensores()))
                    {
                        echo "                          <input type=\"checkbox\" id=\"sensor_todos\" name=\"sensor_".$sensor->getSensorCode()."_".$sensor->getSensorCh()."\" value=\"seleccionado\" checked=\"\">&nbsp;".$sensor->getName()."<br>";
                    }else
                    {
                        echo "                          <input type=\"checkbox\" id=\"sensor_todos\" name=\"sensor_".$sensor->getSensorCode()."_".$sensor->getSensorCh()."\" value=\"seleccionado\">&nbsp;".$sensor->getName()."<br>";
                    }   
                }
                echo "                              </div>
                                                </div>
                                            </div>";
                echo "                      <div class=\"col-md-4\">
                                                <div class=\"panel panel-default\">
                                                    <div class=\"panel-heading\">
                                                        <h2 class=\"\">Configuraci&oacute;n</h2>
                                                    </div>
                                                    <div class=\"panel-body\">";
                // Periodo a descargar  
                echo "                                  <div class=\"form-group\">
                                                            <label for=\"tipo_archivo\">Per&iacute;odo a descargar:</label><br>
                                                            <label class=\"radio-inline\">";
                if($q_configuracion->getPeriodo()=='periodo')
                {
                    echo "                                      <input type=\"radio\" name=\"periodo\" id=\"descarga_periodo\" value=\"periodo\" checked=\"\">&nbsp;Descarga de datos desde";
                }else
                {
                    echo "                                      <input type=\"radio\" name=\"periodo\" id=\"descarga_periodo\" value=\"periodo\">&nbsp;Descarga de datos desde";
                }
                echo "                                      </label><br>
                                                            Fecha inicial:&nbsp;<input type=\"text\" class=\"form-control\" name=\"fecha_inicial\" id=\"fecha_inicial\" value=\"".$q_configuracion->getPeriodoFechaInicial()."\" size=\"8\" maxlength=\"8\">
                                                            Fecha final:&nbsp;<input type=\"text\" class=\"form-control\" name=\"fecha_final\" id=\"fecha_final\" value=\"".$q_configuracion->getPeriodoFechaFinal()."\" size=\"8\" maxlength=\"8\">
                                                            <label class=\"radio-inline\">";
                if($q_configuracion->getPeriodo()=='mes_actual')
                {
                    echo "                                      <input type=\"radio\" name=\"periodo\" id=\"mes_actual\" value=\"mes_actual\" checked=\"\">&nbsp;Mes actual";
                }else
                {
                    echo "                                      <input type=\"radio\" name=\"periodo\" id=\"mes_actual\" value=\"mes_actual\">&nbsp;Mes actual";
                }
                echo "                                      </label>
                                                            <br>
                                                            <label class=\"radio-inline\">";
                if($q_configuracion->getPeriodo()=='todos')
                {
                    echo "                                      <input type=\"radio\" name=\"periodo\" id=\"todos\" value=\"todos\" checked=\"\">&nbsp;Desde el principio";
                }else
                {
                    echo "                                      <input type=\"radio\" name=\"periodo\" id=\"todos\" value=\"todos\">&nbsp;Desde el principio";
                }
                echo "                                      </label>
                                                            <br>
                                                            <label class=\"radio-inline\">";
                if($q_configuracion->getPeriodo()=='')
                {
                    echo "                                      <input type=\"radio\" name=\"periodo\" id=\"fijo\" value=\"fijo\" checked=\"\">&nbsp;Per&iacute;odo fijo";
                }else
                {
                    echo "                                      <input type=\"radio\" name=\"periodo\" id=\"fijo\" value=\"fijo\">&nbsp;Per&iacute;odo fijo";
                }
                echo "                                       </label>
                                                        </div>";
                // Tipo de archivo a exportar
                echo "                                  <div class=\"form-group\">
                                                            <label for=\"tipo_archivo\">Exportar a tipo de archivo:</label><br>
                                                            <label class=\"radio-inline\">";
                if($q_configuracion->getTipoArchivo()=='txt')
                {
                    echo "                                      <input type=\"radio\" name=\"tipo_archivo\" id=\"archivo_txt\" value=\"txt\" checked=\"\">TXT";
                }else
                {
                    echo "                                      <input type=\"radio\" name=\"tipo_archivo\" id=\"archivo_txt\" value=\"txt\">TXT";
                }
                echo "                                      </label>
                                                            <label class=\"radio-inline\">";
                if($q_configuracion->getTipoArchivo()=='csv')
                {
                    echo "                                      <input type=\"radio\" name=\"tipo_archivo\" id=\"archivo_csv\" value=\"csv\" checked=\"\">CSV";
                }else
                {
                    echo "                                      <input type=\"radio\" name=\"tipo_archivo\" id=\"archivo_csv\" value=\"csv\">CSV";
                }
                echo "                                      </label>
                                                        </div>";
                // Separador de columnas
                echo "                                  <div class=\"form-group\">
                                                            <label for=\"separador\">Separar columnas por:</label><br>
                                                            <label class=\"radio-inline\">";
                if($q_configuracion->getSeparador()=='coma')
                {
                    echo "                                     <input type=\"radio\" id=\"coma\" name=\"separador\" value=\"coma\" checked=\"\">&nbsp;COMA";
                }else
                {
                    echo "                                     <input type=\"radio\" id=\"coma\" name=\"separador\" value=\"coma\">&nbsp;COMA";
                }
                echo "                                      </label>
                                                            <br>
                                                            <label class=\"radio-inline\">
                                                                <input type=\"radio\" id=\"punto_coma\" name=\"separador\" value=\"punto_coma\" checked=\"\">&nbsp;PUNTO y COMA
                                                            </label>
                                                            <br>
                                                            <label class=\"radio-inline\">
                                                                <input type=\"radio\" id=\"tab\" name=\"separador\" value=\"tab\">&nbsp;TAB
                                                            </label>
                                                            <br>
                                                            <label class=\"radio-inline\">
                                                                <input type=\"radio\" id=\"espacio\" name=\"separador\" value=\"espacio\">&nbsp;ESPACIO
                                                            </label>
                                                        </div>";
                // Agregar encabezado                   
                echo "                                  <div class=\"form-group\">
                                                            <label for=\"encabezado\">Agregar encabezado:</label><br>
                                                            <label class=\"radio-inline\">
                                                                <input type=\"radio\" id=\"encabezado_si\" name=\"encabezado\" value=\"si\" checked=\"\">&nbsp;S&iacute;&nbsp;&nbsp;
                                                            </label>
                                                            <label class=\"radio-inline\">
                                                                <input type=\"radio\" id=\"encabezado_no\" name=\"encabezado\" value=\"no\">&nbsp;No
                                                            </label>    
                                                        </div>";
                // Nombre de archivo dde salida
                echo "                                  <div class=\"form-group\">
                                                            <label for=\"archivo\">Nombre de archivo (sin extension):</label><br>
                                                            <input class=\"form-control\" type=\"text\" id=\"archivo\" name=\"archivo\" size=\"40\" maxlength=\"50\">
                                                        </div>";
                echo "                              </div>
                                                </div>
                                            </div>";
                echo "                      <div class=\"col-md-4\">
                                                <div class=\"panel panel-default\">
                                                    <div class=\"panel-heading\">
                                                        <h2 class=\"\">Alertas</h2>
                                                    </div>
                                                    <div class=\"panel-body\">
                                                        Que alertas?";
                echo "                              </div>
                                                </div>
                                            </div>";
                echo "                  </div> <!-- cierre de div row -->";
                echo "                  <div class=\"row\">
                                            <div class=\"col-md-9\">
                                                <div class=\"pull-right\">
                                                    <button type=\"submit\" name=\"guardar_configuracion\" class=\"btn btn-default\">Guardar configuraci&oacute;n</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div> <!-- cierre de div container -->
                            </div> <!-- cierre de div info-sensores -->";
                unset($q_estacion);
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
function cargar_usuarios()
{
    // primero busco el usuario imetos
    $usuarios=array();
    $sql="SELECT * FROM `usuarios` WHERE `tipo_usuario`='imetos'";
    if(!sql_select($sql, $consulta))
    {
        unset($consulta);
        return false;
    }
    $con=0;
    while($usuario = $consulta->fetch(PDO::FETCH_ASSOC))
    {
        $usuarios[$con]=$usuario;
        // ahora busco usuarios ftp
        $sql="SELECT * FROM `usuarios` WHERE `tipo_usuario`='ftp' AND `id_usuario`=".$usuario['id'];
        if(sql_select($sql,$consulta2))
        {
            while($registro = $consulta2->fetch(PDO::FETCH_ASSOC))
            {
                $usuarios[$con]['ftp'][]=$registro;
            }
        }
        $sql="SELECT * FROM `usuarios` WHERE `tipo_usuario`='mysql' AND `id_usuario`=".$usuario['id'];
        if(sql_select($sql,$consulta3))
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
function actualizar_usuario()
{
    $error=false;
    // imetos
    $id_usuario=  CCGetFromPost("id_usuario");
    $usuario=  CCGetFromPost("usuario"); // usuario iMetos
    // ftp
    $id_usuario_ftp=CCGetFromPost("id_ftp");
    $usuario_ftp= CCGetFromPost("usuario_ftp"); // usuario ftp
    $password_ftp=  CCGetFromPost("password_ftp"); // password ftp
    $servidor_ftp= CCGetFromPost("servidor_ftp"); // servidor ftp
    $directorio_remoto=  CCGetFromPost("directorio_remoto"); 
    //$tipo_usuario=  CCGetFromPost("tipo_usuario");
    $mails=  CCGetFromPost("mails");
    // mysql
    $id_usuario_mysql=  CCGetFromPost("id_mysql");
    $usuario_mysql=  CCGetFromPost("usuario_mysql");
    $password_mysql=  CCGetFromPost("password_mysql");
    $servidor_mysql=  CCGetFromPost("servidor_mysql");
    //
    $sql="  UPDATE `usuarios`
            SET `usuario`='".$usuario."',
                `password`='',
                `servidor`='',
                `directorio_remoto`='',
                `es_admin`=0,
                `tipo_usuario`='imetos',
                `mails`=''
            WHERE `id`=".$id_usuario;
    if(!sql_select($sql, $consulta))
    {
        // hubo un error
        $error=true;
    }
    // ftp
    $sql="  UPDATE `usuarios`
            SET `usuario`='".$usuario_ftp."',
                `password`='".$password_ftp."',
                `servidor`='".$servidor_ftp."',
                `directorio_remoto`='".$directorio_remoto."',
                `es_admin`=0,
                `tipo_usuario`='ftp',
                `mails`='".$mails."'
            WHERE `id`=".$id_usuario_ftp;
    if(!sql_select($sql, $consulta2))
    {
        // hubo un error
        $error=true;
    }
    // mysql
    $sql="  UPDATE `usuarios`
            SET `usuario`='".$usuario_mysql."',
                `password`='".$password_mysql."',
                `servidor`='".$servidor_mysql."',
                `directorio_remoto`='',
                `es_admin`=0,
                `tipo_usuario`='mysql',
                `mails`=''
            WHERE `id`=".$id_usuario_mysql;
    if(!sql_select($sql, $consulta3))
    {
        // hubo un error
        $error=true;
    }
    if($error) return false;
    return true;
}
function borrar_usuario($id_usuario=0)
{
    if($id_usuario==0) return false;
    $sql="DELETE FROM `usuarios` WHERE `id`=".$id_usuario;
    if(!sql_select($sql, $consulta))
    {
        unset($consulta);
        return false;
    }
    unset($consulta);
    return true;
}
function listado_informes($id_usuario=0)
{
    $sql_select="  
        SELECT  informes.`id` AS id_informe,
                informes.`informe` AS informe,
                informes.`fecha` AS fecha,
                usuarios.`id` AS id_usuario,
                usuarios.`activo` AS activo,
                usuarios.`fecha_alta` AS fecha_alta,
                usuarios.`usuario` AS usuario,
                usuarios.`password` AS password,
                usuarios.`servidor` AS servidor,
                usuarios.`directorio_remoto` AS directorio_remoto,
                usuarios.`es_admin` AS es_admin,
                usuarios.`tipo_usuario` AS tipo_usuario,
                usuarios.`mails` AS mails";
    if($id_usuario==0)
    {
        // es admin y muestro todos los informes
        $sql_demas="
        FROM    `informes_sondas_detenidas` AS informes, 
                `usuarios` AS usuarios
        WHERE   informes.`id_usuario`=usuarios.`id`
        ORDER BY `fecha` DESC";
    }else
    {
        $sql_demas="
        FROM    `informes_sondas_detenidas` AS informes,
                `usuarios` AS usuarios
        WHERE   informes.`id_usuario`=".$id_usuario."
        ORDER BY `fecha` DESC";
    }
    $sql="$sql_select $sql_demas";
    // muestro tabla con informes para el usuario logeado
    $informes=array();
    if(sql_select($sql, $consulta))
    {
        while($registro = $consulta->fetch(PDO::FETCH_ASSOC))
        {
            $informes[]=$registro;
        }
        if(!is_null($informes))
        {
            //echo "--->".count($informes)."<br>";
            echo informes_sondas_detenidas($informes);
        }else
        {
            echo "No hay informes que mostrar<br>";
        }
    }
    unset($consulta);
}
function insertar_usuario()
{
    $usuario_ftp=   CCGetFromPost('usuario');
    $password_ftp=  CCGetFromPost('password');
    $servidor_ftp=  CCGetFromPost('servidor');
    $directorio_remoto= CCGetFromPost('directorio');
    $mails=  CCGetFromPost('mails');
    $fecha_alta=time();
    $sql="  INSERT INTO `usuarios` (`activo`,`fecha_alta`,`usuario`,`password`,`servidor`,
                `directorio_remoto`,`es_admin`,`tipo_usuario`,`mails`) 
            VALUES (1,".$fecha_alta.",'".$usuario_ftp."','".$password_ftp."','".$servidor_ftp."','".$directorio_remoto."',0,'ftp','".$mails."')";
    if(!sql_select($sql, $consulta))
    {
        unset($consulta);
        return false;
    }
    unset($consulta);
    return true;
}
function comprobar_conexion()
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
function actualizar_estacion()
{
    $info= json_encode($_POST);
    if(isset($_POST['id_usuario']))
    {
        $id_usuario=  CCGetFromPost('id_usuario');
    }
    if(isset($_POST['f_station_code']))
    {
        $f_station_code=  CCGetFromPost('f_station_code');
    }
    if(isset($id_usuario) AND isset($f_station_code))
    {
        // primero verifico que exista
        $sql="  SELECT  *
                FROM    `estaciones`
                WHERE   `id_usuario`=".$id_usuario." AND
                        `f_station_code`=".$f_station_code;
        echo "sql--->{$sql}<br>";
        if(!sql_select($sql,$consulta))
        {
            echo "ERROR! No se pudo consultar la base de datos";
            return false;
        }
        if($consulta->rowCount() > 0)
        {
            // lo actualizo
            $sql="  UPDATE  `estaciones`
                    SET     `info`='".$info."'
                    WHERE   `id_usuario`=".$id_usuario." AND
                            `f_station_code`=".$f_station_code;
            if(!sql_select($sql,$consulta2))
            {
                echo "ERROR! No se pudo actualizar los datos de la estacion";
                return false;
            }
        }else
        {
            // inserto estacion
            $sql="  INSERT INTO `estaciones` (`id_usuario`,`f_station_code`,`activa`,`info`)
                    VALUES (".$id_usuario.",".$f_station_code.",1,'".$info."')";
            if(!sql_select($sql,$consulta2))
            {
                echo "ERROR! No se pudo insertar los datos de la estacion";
                return false;
            }
        }
        return true;
    }
    echo "ERROR! No esta definido el usuario y/o la estaci&oacute;n";
    return false;
}
function file_get_contents_utf8($fn)
{
    $content = file_get_contents($fn);
    return mb_convert_encoding($content, 'UTF-8', 
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)); 
}
function buscar_datos_conexion($id_usuario)
{
    $sql="  SELECT  *
            FROM    `usuarios`
            WHERE   `tipo_usuario`='mysql' AND
                    `id_usuario`=".$id_usuario;
    if(!sql_select($sql,$consulta))
    {
        echo "ERROR! No se pudo determinar datos de conexion a la base de datos mysql";
        return false;
    }
    return $consulta->fetch(PDO::FETCH_ASSOC);
}
function cargar_informacion_estacion($f_station_code,$id_usuario)
{
    $sql="  SELECT  *
            FROM    `estaciones`
            WHERE   `f_station_code`={$f_station_code} AND 
                    `id_usuario`={$id_usuario}";
    if(!sql_select($sql, $consulta))
    {
        return false;
    }
    if($info=$consulta->fetch(PDO::FETCH_ASSOC))
    {
        return $info['info'];
    }
}
function configuracion_estacion($estacion,$info)
{
}
?>