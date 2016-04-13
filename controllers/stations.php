<?php
if(Login::getLoginSession())
{
    echo "pase por stations<br>";
    if(!isset($_POST['action'])) exit;
    $station_code=$_POST['station_code'];
    $userid=$_POST['userid'];
    switch($_POST['action'])
    {
        case 'export_data':
            if(Station::export())
            {
                
            }
            
            echo "station-code--->{$station_code}<br>";
            echo "userid--------->{$userid}<br>";
            break;
    }
}