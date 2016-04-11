<?php
if(User::getLoginSession())
{
    print_r($_POST);
    $action=req($_GET['action']);
    switch($action)
    {
        case 'check':
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
            break;
    }
}