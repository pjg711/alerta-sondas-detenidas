<?php
if(Login::getLoginSession())
{
    if(!isset($_POST['action'])) exit;
    $userid=$_POST['userid'];
    $station_code=$_POST['station_code'];
    switch($_POST['action'])
    {
        case 'export_data':
            if($id_log=Station::export_data($userid,$station_code))
            {
                mensaje("Se exportó con exito el archivo id->{$id_log}","Exportar datos");
                redireccionar('/export/'.$id_log);
            }else
            {
                //mensaje($error,"","error");
                echo "ocurrio algun error<br>";
            }
    }
}