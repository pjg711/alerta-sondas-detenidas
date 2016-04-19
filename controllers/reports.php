<?php
if(Login::getLoginSession())
{
    echo "pase por users<br>";
    if(!isset($_POST['action'])) exit;
    $reportid=req('reportid');
    switch($_POST['action'])
    {
        case 'delete_report':
            if(Report::borrar_informe($id_informe))
            break;
            
        case 'confirmed_delete':
            break;
    }
    /*
    if(isset($_POST['confirmed_delete_report']))
    {
        // borrar informe 
        $id_informe=$_POST['confirmed_delete_report'];
        if(User::borrar_informe($id_informe))
        {
            mensaje("Se borr\u00F3 el informe","Borrar informe");
        }else
        {
            mensaje("ERROR! No se pudo borrar el informe","","error");
        }
    }
    if(isset($_POST['confirmed_erase_all']))
    {
        // borra todos los informes para el usuario 
        if(isset($_SESSION['userid']))
        {
            $userid=$_SESSION['userid'];
            User::borrar_informes_todos($userid);
        }
    }
    */
}