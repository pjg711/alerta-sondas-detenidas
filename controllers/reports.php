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
}