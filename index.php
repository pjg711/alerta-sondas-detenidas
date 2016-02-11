<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);
ini_set('memory_limit','128M');

require 'lib/class_base-datos.php';
require 'lib/class_login.php';
require 'lib/class_ftp.php';
require 'lib/class_pagina.php';
// cargo PHPMailer
require 'lib/PHPMailer-master/PHPMailerAutoload.php';
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
// borrar informe
if($login->getLoginSession())
{
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
            /*
            Array
            (
                [id_usuario] => 3
                [usuario] => monsanto.seedmech.com.ar
                [password] => monsanto
                [servidor] => 190.228.29.71
                [directorio_remoto] => /
                [tipo_usuario] => 0
                [mails] => pablo.ju.garcia@gmail.com, garcia@cifasis-conicet.gov.ar
                [guardar_edicion_usuario] => Guardar edición
            )
            echo "<pre>";
            print_r($_POST);
            echo "</pre>";
            exit;
             * 
             */
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
            //mensaje("Realizo informe para usuario ".$usuario);
            hago_informes($usuario,true);
        }
        // es usuario admin y presento todos los informes ordenados por fecha
        nuevo_usuario();
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
            mostrar_ocultar('nuevo_usuario');
        </script>
        <?php
    }
    /*
    if(isset($_POST['hago_informe']))
    {
        if(isset($_SESSION['user_active']))
        {
            $argv[1]=$_SESSION['user_active'];
            hago_informes($argv,true);
        }
    }
     */
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
    $obj_BD=new BD();
    if($obj_BD->sql_select($sql, $consulta))
    {
        if($obj_BD->num_registros==0)
        {
            echo "ERROR! ".$usuario." no corresponde con un usuario cargado en el sistema.\n";
        }
        while($registro = $consulta->fetch(PDO::FETCH_ASSOC))
        {
            hago_informe($registro,$lo_guardo);
        }
    }
    unset($obj_BD);
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
                $sql="INSERT INTO `informes` (`id_usuario`,`informe`,`fecha`) VALUES (".$registro['id'].",'".$informe."','".$fecha_actual."')";
                $obj_BD= new BD();
                if($obj_BD->sql_select($sql, $consulta))
                {
                    //echo "Se inserto informe\n";
                    // envio mails
                    envio_emails($informe,$usuario,$fecha_actual,$emails);
                }
                unset($obj_BD);
            }
        }else
        {
            echo "ERROR! Hubo algún problema en la creación del informe.\n";
        }
    }
}
/*
function hago_informe($usuario)
{
    $sql="SELECT * FROM `usuarios` WHERE `usuario`='".$usuario."' LIMIT 1";
    $obj_BD=new BD();
    if($obj_BD->sql_select($sql, $consulta))
    {
        if($obj_BD->num_registros>0)
        {
            // hago el informe y lo guardo
            if($registro=$consulta->fetch(PDO::FETCH_ASSOC))
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
                            $fecha_actual=date("d-m-Y H:i:s");
                            $sql="INSERT INTO `informes` (`id_usuario`,`informe`,`fecha`) VALUES (".$registro['id'].",'".$informe."','".$fecha_actual."')";
                            if($obj_BD->sql_select($sql, $consulta))
                            {
                                echo "Se inserto informe\n";
                                // envio mails
                                envio_emails($informe,$usuario,$fecha_actual,$emails);
                            }
                        }
                    }else
                    {
                        echo "ERROR! Hubo algún problema en la creación del informe.\n";
                    }
                }
            }
        }else
        {
            echo "ERROR! ".$argv[1]." no corresponde con un usuario cargado en el sistema.\n";
        }
    }
    unset($obj_BD);
}
 * 
 */
function presento_informes($informes)
{
    // para todos los informes
    $cadena= "
        <br><br>
        <!-- <button type=\"submit\" name=\"hago_informe\"><i class=\"fa fa-terminal\"></i>&nbsp;&nbsp;Realizar informe</button> -->
        <br>
        <h1>Listado de informes realizados</h1>
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
    $obj_BD=new BD();
    $sql="DELETE FROM `informes` WHERE `id`=".$id_informe;
    //echo "sql--->".$sql."<br>";
    if($obj_BD->sql_select($sql, $consulta))
    {
        return true;
    }
    return false;
}
function borrar_informes($id_usuario=0)
{
    if($id_usuario==0) return false;
    $obj_BD=new BD();
    $sql="DELETE FROM `informes` WHERE `id_usuario`=".$id_usuario;
    if($obj_BD->sql_select($sql, $consulta))
    {
        return true;
    }
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
        <a class=\"nuevo-usuario\" href=\"javascript:mostrar_ocultar('nuevo_usuario');\"><img src=\"./img/nuevo_informe.png\">&nbsp;Nuevo usuario FTP</a>
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
                                    <td><input type=\"text\" name=\"usuario\" value=\"".$usuario."\" size=\"70\" maxlength=\"255\"></td>
                                </tr>
                                <tr>
                                    <td>Password FTP:</td>
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
function listado_usuarios_ftp($es_admin=false)
{
    $obj_BD=new BD();
    $enum_tipos_usuarios=$obj_BD->getEnumOptions('usuarios', 'tipo_usuario');
    /*
    echo "enum_tipos_usuarios---><br>";
    print_r($enum_tipos_usuarios);
    echo "<br><br>";
     * 
     */
    if($usuarios=cargar_usuarios())
    {
        echo "
            <h1>Listado de usuarios</h1>
            <table class=\"table table-striped table-hover table-bordered table-condensed\">
                <tr>    
                    <th>&nbsp;</th>
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
                    <td align=\"center\">
                        <a class=\"link-tabla\" href=\"javascript:borrar_usuario('".$usuario['id']."');\">
                            <i class=\"fa fa-trash\"></i>
                        </a>&nbsp;";
            if($usuario['tipo_usuario']=="ftp")
            {
                echo "  <a class=\"link-tabla\" href=\"javascript:realizar_informe('".$usuario['usuario']."');\" title=\"Realizar informe\">
                            <i class=\"fa fa-terminal\"></i>
                        </a>&nbsp;";
            }
            echo "      <a class=\"link-tabla\" href=\"javascript:mostrar_ocultar('usuario_".trim($usuario['id'])."');\" title=\"Editar usuario\">
                            <i class=\"fa fa-pencil\"></i>
                        </a>
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
                                            <textarea name=\"mails\" rows=\"3\" cols=\"80\">".$usuario['mails']."</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"2\" align=\"right\">
                                            <input type=\"reset\" name=\"cancelar_edicion\" value=\"Cancelar\" onclick=\"mostrar_ocultar('usuario_".trim($usuario['id'])."')\">&nbsp;
                                            <input type=\"submit\" name=\"guardar_edicion_usuario\" value=\"Guardar edici&oacute;n\">
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
    $obj_BD=new BD();
    $enum_tipos_usuarios=$obj_BD->getEnumOptions('usuarios', 'tipo_usuario');
    //
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
                `tipo_usuario`='".$enum_tipos_usuarios[$tipo_usuario]."',
                `mails`='".$mails."'
            WHERE `id`=".$id_usuario;
    if(!$obj_BD->sql_select($sql, $consulta))
    {
        return false;
    }
    return true;
}
function borrar_usuario($id_usuario=0)
{
    if($id_usuario==0) return false;
    $obj_BD=new BD();
    $sql="DELETE FROM `usuarios` WHERE `id`=".$id_usuario;
    if(!$obj_BD->sql_select($sql, $consulta))
    {
        return false;
    }
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
        FROM    `informes` AS informes, 
                `usuarios` AS usuarios
        WHERE   informes.`id_usuario`=usuarios.`id`
        ORDER BY `fecha` DESC";
    }else
    {
        $sql_demas="
        FROM    `informes` AS informes,
                `usuarios` AS usuarios
        WHERE   informes.`id_usuario`=".$id_usuario."
        ORDER BY `fecha` DESC";
    }
    $sql="$sql_select $sql_demas";
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
            //echo "--->".count($informes)."<br>";
            echo presento_informes($informes);
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
    $sql="  INSERT INTO `usuarios` (`activo`,`fecha_alta`,`usuario`,`password`,`servidor`,
                `directorio_remoto`,`es_admin`,`tipo_usuario`,`mails`) 
            VALUES (1,".$fecha_alta.",'".$usuario_ftp."','".$password_ftp."','".$servidor_ftp."','".$directorio_remoto."',0,'ftp','".$mails."')";
    //echo "sql insertar_usuario--->".$sql."<br>";
    if(!$obj_BD->sql_select($sql, $consulta))
    {
        unset($obj_BD);
        return false;
    }
    unset($obj_BD);
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
        return false;
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
function mensaje($texto)
{
    echo "<script type=\"text/javascript\">";
    //echo "alert(\"".html_entity_decode($texto)."\");";
    echo "alert(\"".$texto."\");";
    echo "</script>";
}
function file_get_contents_utf8($fn)
{
    $content = file_get_contents($fn);
    return mb_convert_encoding($content, 'UTF-8', 
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)); 
}
function envio_emails($informe,$usuario,$fecha,$emails)
{
    $informe_html="
    <html>
        <head>
            <title>Informe de sondas para usuario ".$usuario.", fecha ".$fecha."</title>
            <meta charset=\"UTF-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        </head>
        <body>
            <div align=\"center\">
                <a href=\"http://www.seedmech.com\"><img src=\"img/seedmech_agrotecnologia.png\" height=\"90\" width=\"340\" alt=\"Seedmech\"></a>
            </div>";
    $informe_html.=  presento_informe($informe);
    $informe_html.="</body></html>";
    //
    $mail = new PHPMailer;
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = MAIL_HOST;
    // use
    // $mail->Host = gethostbyname('smtp.gmail.com');
    // if your network does not support SMTP over IPv6

    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = MAIL_PORT;
    $mail->SMTPSecure = MAIL_SMTPSecure;
    $mail->SMTPAuth = true;
    $mail->Username = MAIL_USERNAME;
    $mail->Password = MAIL_PASSWORD;
    $mail->setFrom('sondas.seedmech@gmail.com', 'Sondas Seedmech');
    foreach($emails as $email)
    {
        if($email<>"")
        {
            $mail->addAddress($email, '');
        }
    }
    $mail->Subject = "Informe de sondas para usuario ".$usuario.", ".$fecha;
    $mail->msgHTML($informe_html, dirname(__FILE__));
    $mail->AltBody = 'This is a plain-text message body';

    //Attach an image file
    $mail->addAttachment('img/seedmech_agrotecnologia.png');

    //send the message, check for errors
    if(!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo."\n";
    }else
    {
        //echo "Message sent!\n";
    }
}
?>