<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);
ini_set('memory_limit','128M');
//
include("config.php");
//
require 'lib/class_imetos.php';
require 'lib/class_station.php';
require 'lib/class_sensor.php';
require 'lib/class_config.php';

require 'lib/class_ftp.php';
require 'lib/class_users.php';
require 'lib/class_pagina.php';
//
$usuario=new User();
//       
if($usuario->getLoginSession())
{
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    //
    if(isset($_POST['confirmado_borrar_informe']))
    {
        $id_informe=$_POST['confirmado_borrar_informe'];
        if($usuario->borrar_informe($id_informe))
        {
            mensaje("Se borr\u00F3 el informe","Borrar informe");
        }else
        {
            mensaje("ERROR! No se pudo borrar el informe","","error");
        }
    }
    if(isset($_POST['confirmado_borrar_todos']))
    {
        if(isset($_SESSION['userid']))
        {
            $userid=$_SESSION['userid'];
            $usuario->borrar_informes_todos($userid);
        }
    }
    if(isset($_POST['guardar_configuracion']))
    {
        if(actualizar_estacion())
        {
            mensaje("Se guardó la configuración para la estación","Configurar estación");
        }else
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
/*
echo "
    <script language='javascript'>
        toastr.success('Have fun storming the castle!', 'Miracle Max Says');
    </script>";
*/
//
if(isset($_GET['cerrar_sesion']))
{
    $usuario->cerrar_sesion();
}
if(!$usuario->getLoginSession())
{
    if(isset($_POST['usuario']) and isset($_POST['password']))
    {
        $q_usuario=CCGetFromPost("usuario");
        $q_password=CCGetFromPost("password");
        // verifico el usuario
        if($usuario->verificar($q_usuario, $q_password))
        {
            // bien
        }else
        {
            $usuario->cerrar_sesion();
            mensaje("Error en dato de usuario y/o contraseña","","error");
            redireccionar("index.php");
        }
    }else
    {
        //pido usuario y contraseña para el ingreso
        $usuario->ingreso();
    }
}
if($usuario->getLoginSession())
{
    $usuario->sesion_iniciada();
    if($usuario->getIsAdmin())
    {
        if(isset($_POST['comprobar']))
        {
            //comprobar la conexion al sitio ftp
            if($usuario->comprobar_conexion())
            {
                mensaje("Conexi\u00F3n al servidor con \u00E9xito.","Comprobar conexión");
            }else
            {
                mensaje("ERROR! Revise los datos de la conexi\u00F3n al servidor","","error");
            }
        }
        if(isset($_POST['alta_usuario']))
        {
            //inserto usuario nuevo
            if($usuario->insertar())
            {
                mensaje("Se guard\u00F3 el nuevo usuario","Nuevo usuario");
            }else
            {
                mensaje("ERROR! No se pudo guardar el usuario","","error");
            }
        }
        if(isset($_POST['guardar_edicion_usuario']))
        {
            if($usuario->actualizar())
            {
                mensaje("Se actualiz\u00F3 el usuario","Editar usuario");
            }else
            {
                mensaje("ERROR! Problema al actualizar usuario","","error");
            }
        }
        if(isset($_POST['confirmado_borrar_usuario']))
        {
            $userid= CCGetFromPost("confirmado_borrar_usuario");
            if($usuario->borrar_usuario($userid))
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
            $q_usuario[1]=  CCGetFromPost("realizar_informe");
            hago_informes($q_usuario,true);
        }
        // solo admin puede crear un nuevo usuario
        $usuario->formulario_crear();
        // es usuario admin y presento todos los informes ordenados por fecha
        $usuario->listar(true);
        // listado de archivos csv
        //listado_csvs();
        // todos los informes
        $usuario->listado_informes();
    }else
    {
        $userid=$_SESSION['userid'];
        //
        $usuario->listar(false);
        // solo los informes de usuario ftp $userid
        $usuario->listado_informes($userid);
        $pagina->pie();
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
    $pagina->pie();
}
exit;
/*******************************************************/
/* ------------------FUNCIONES ------------------------*/
/*******************************************************/













function actualizar_estacion()
{
    if(!isset($_POST['activar']) OR $_POST['activar']=='off')
    {
        $activa=0;
    }else
    {
        $activa=1;
    }
    $info= json_encode($_POST);
    if(isset($_POST['userid']))
    {
        $userid=  CCGetFromPost('userid');
    }
    if(isset($_POST['f_station_code']))
    {
        $f_station_code=  CCGetFromPost('f_station_code');
    }
    if(isset($userid) AND isset($f_station_code))
    {
        // primero verifico que exista
        $query="  SELECT  `id`
                FROM    `configuraciones`
                WHERE   `id_usuario`={$id_usuario} AND
                        `f_station_code`={$f_station_code}";
        if(!sql_select($query,$consulta))
        {
            mensaje("No se pudo consultar la base de datos","","error");
            return false;
        }
        if($consulta->rowCount() > 0)
        {
            // lo actualizo
            $query="  UPDATE  `configuraciones`
                    SET     `info`='{$info}',
                            `activa`={$activa}
                    WHERE   `id_usuario`={$id_usuario} AND
                            `f_station_code`={$f_station_code}";
            if(!sql_select($query,$consulta2))
            {
                mensaje("No se pudo actualizar los datos de la estacion","","error");
                return false;
            }
        }else
        {
            // inserto estacion
            $query="  INSERT INTO `configuraciones` 
                        (`id_usuario`,`f_station_code`,`activa`,`info`)
                    VALUES 
                        ({$id_usuario},{$f_station_code},{$activa},'{$info}')";
            if(!sql_select($query,$consulta2))
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
?>