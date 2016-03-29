<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);
ini_set('memory_limit','128M');
//
include("config.php");
//
require 'lib/class_page.php';
require 'lib/class_imetos.php';
require 'lib/class_station.php';
require 'lib/class_sensor.php';
require 'lib/class_config.php';

require 'lib/class_ftp.php';
require 'lib/class_users.php';
//
$page=new PAGE();
$page->header();
//
$user=new User();
//       
if($user->getLoginSession())
{
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    //
    //
    if(isset($_POST['check_connection']))
    {
        //mensaje("pase por aca");
        $_SESSION['action']='check_connection';
        //comprobar la conexion al sitio ftp
        $username=  req('username_ftp');
        $password=  req('password_ftp');
        $server=  req('server_ftp');
        $remotedir= req('remotedir');
        //if($obj_ftp=new FTP($server,$username,$password,$remotedir))
        //$server=null,$user=null,$passw=null
        if(FTP::check_connection($server,$username,$password))
        {
            mensaje("Conexi\u00F3n al servidor con \u00E9xito.","Comprobar conexión");
        }else
        {
            mensaje("ERROR! Revise los datos de la conexi\u00F3n al servidor","","error");
        }
    }
    // *************************************************
    // User
    // *************************************************
    if(isset($_POST['new_user']))
    {
        // grabo nuevo usuario
        $_SESSION['action']='new_user';
        if(User::save())
        {
            mensaje("El usuario se guard\u00F3 con \u00E9xito","Nuevo usuario");
        }else
        {
            mensaje("ERROR. No se pudo guardar el nuevo usuario","","error");
        }
    }
    if(isset($_POST['edit_user']))
    {
        $_SESSION['action']='edit_user';
        if(User::update())
        {
            mensaje("Se actualiz\u00F3 el usuario","Editar usuario");
        }else
        {
            mensaje("ERROR! Problema al actualizar usuario","","error");
        }
    }
    //
    if(isset($_POST['data_export']))
    {
        $_SESSION['action']='data_export';
        // exporto los datos
        $userid=  req('userid');
        $f_station_code=  req('f_station_code');
    }
    //
    if(isset($_POST['confirmed_delete_report']))
    {
        // borrar informe 
        $id_informe=$_POST['confirmed_delete_report'];
        if($user->borrar_informe($id_informe))
        {
            mensaje("Se borr\u00F3 el informe","Borrar informe");
        }else
        {
            mensaje("ERROR! No se pudo borrar el informe","","error");
        }
    }
    if(isset($_POST['confirmado_borrar_todos']))
    {
        // borra todos los informes para el usuario 
        if(isset($_SESSION['userid']))
        {
            $userid=$_SESSION['userid'];
            $user->borrar_informes_todos($userid);
        }
    }
    if(isset($_POST['save_config']))
    {
        if(Config_Station::update())
        {
            mensaje("Se guardó la configuración para la estación","Configurar estación");
        }else
        {
            mensaje("Error ");
        }
    }
}
//
/*
echo "
    <script language='javascript'>
        toastr.success('Have fun storming the castle!', 'Miracle Max Says');
    </script>";
*/
//
if(isset($_GET['sign_off']))
{
    $user->SignOff();
}
if(!$user->getLoginSession())
{
    if(isset($_POST['usuario']) and isset($_POST['password']))
    {
        $q_usuario = req("usuario");
        $q_password = req("password");
        // verifico el usuario
        if($user->verify_user($q_usuario, $q_password))
        {
            // bien
        }else
        {
            $user->SignOff();
            mensaje("Error en dato de usuario y/o contraseña","","error");
            redireccionar("index.php");
        }
    }else
    {
        //pido usuario y contraseña para el ingreso
        $user->getInto();
    }
}
if($user->getLoginSession())
{
    echo "
    <ul style=\"margin:19px 0 18px 0;\" class=\"nav nav-tabs test2\">
        <li class=\"active\"><a data-toggle=\"tab\" href=\"#exportacion\">Exportación de datos de sondas</a></li>
        <li><a data-toggle=\"tab\" href=\"#detenidas\">Informe de detenidas</a></li>
    </ul>";
    $user->logged($user->getIsAdmin());
    if($user->getIsAdmin())
    {
        // para administradores
        if(isset($_POST['alta_usuario']))
        {
            //inserto usuario nuevo
            if($user->insertar())
            {
                mensaje("Se guard\u00F3 el nuevo usuario","Nuevo usuario");
            }else
            {
                mensaje("ERROR! No se pudo guardar el usuario","","error");
            }
        }
        if(isset($_POST['confirmed_delete_user']))
        {
            $userid= req("confirmed_delete_user");
            if($user->delete_user($userid))
            {
                mensaje("El usuario fue borrado","Borrar usuario");
            }else
            {
                mensaje("ERROR! No se pudo borrar el usuario","","error");
            }
        }
        if(isset($_POST['realizar_informe']))
        {
            $q_usuario=array();
            $q_usuario[1]=  req("realizar_informe");
            hago_informes($q_usuario,true);
        }
        if(isset($_POST['cambiar_configuracion']))
        {
            
        }
        echo "<div class=\"tab-content\">
                <div id=\"exportacion\" class=\"tab-pane fade in active\">";
        // solo admin puede crear un nuevo usuario
        $user->new_user();
        // es usuario admin y presento todos los informes ordenados por fecha
        $user->listar(true);
        echo "      <br><br><br>
                </div>
                <div id=\"detenidas\" class=\"tab-pane fade\">";
        // listado de archivos csv
        //listado_csvs();
        // todos los informes
        $user->listado_informes();
        echo "  </div>
              </div>";
    }else
    {
        $userid=$_SESSION['userid'];
        echo "<div class=\"tab-content\">
                <div id=\"exportacion\" class=\"tab-pane fade in active\">";
        //
        $user->listar(false);
        echo "      <br><br><br>
                </div>
                <div id=\"detenidas\" class=\"tab-pane fade\">";
        // solo los informes de usuario ftp $userid
        $user->listado_informes($userid);
        echo "  </div>
              </div>";
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
    $page->footer();
}
?>