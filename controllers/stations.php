<?php
if(Login::getLoginSession())
{
    dump($_POST);
    /*
    Array
    (
        [userid] => 4
        [f_station_code] => 164
        [enable] => on
        [sensor_17921_771] => seleccionado
        [sensor_17921_770] => seleccionado
        [periodo] => periodo
        [fecha_inicial] => 10/05/2015
        [fecha_final] => 11/05/2016
        [periodo_dias] => 30
        [tipo_archivo] => csv
        [separador] => tab
        [encabezado] => si
        [archivo] => prueba11.csv
        [save_config] => 
        [action] => config
    ) 
     * 
     */   
    if(!isset($_POST['action'])) exit;
    if(isset($_POST['userid']))
    {
        $userid=$_POST['userid'];
    }
    if(isset($_POST['station_code']))
    {
        $station_code=$_POST['station_code'];
    }
    switch($_POST['action'])
    {
        case 'export':
            if($id_log=Station::export_data($userid,$station_code))
            {
                //mensaje("Se exportó con exito el archivo id->{$id_log}","Exportar datos");
                $archivo=Log::search($id_log);
                $enlace = $archivo[0]->getInfo();
                $enlace2 = $archivo[0]->getFile();
                ob_clean();
                header ("Content-Disposition: attachment; filename=$enlace2 ");
                header ("Content-Type: application/force-download");
                header ("Content-Length: ".filesize($enlace));
                readfile($enlace);
                //
                //mensaje("Se exportó con exito los datos al archivo {$enlace2}","Exportar datos");
            }else
            {
                echo "ocurrio algun error<br>";
            }
            break;
            
        case 'config':

            if($errores=Config_Station::update())
            {
                mensaje("Se guardó la configuración de la estación","Configurar estación","","/");
            }else
            {
                echo "Errores--->{$errores}<br>";
                mensaje("Error en la configuración de la estación","","error","/");
            }
            break;
    }
}