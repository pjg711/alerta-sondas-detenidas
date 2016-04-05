<?php
require '../config.php';
require '../lib/class_imetos.php';
require '../lib/class_users.php';
require '../lib/class_station.php';
require '../lib/class_sensor.php';
require '../lib/class_config.php';

// busco los usuarios para expotar datos
if($users=User::getAll(true))
{
    foreach($users as $user)
    {
        if($user->getEnableMySQL())
        {
            $BD = new IMETOS($user->getIdMySQL(), $user->getServerMySQL(), $user->getDatabaseMySQL(), $user->getUserMySQL(), $user->getPasswMySQL());
            if($stations=Station::getAll($BD))
            {
                foreach($stations as $station)
                {
                    $station->loadSensors($BD);
                    $stationSensorsList = $station->getAvailableSensors();
                    $q_config = Config_Station::load($user->getId(),$station->getStationCode());
                    //
                    echo "<br>-------------------------------------------------------<br>";
                    echo "UserId--------->{$user->getId()}<br>";
                    echo "station-code--->{$station->getStationCode()}<br>";
                    echo "enable--------->{$q_config->getEnable()}<br>";
                    echo "periodo-------->{$q_config->getPeriodo()}<br>";
                    //
                    $data=array();
                    $enca1="";
                    $enca2="";
                    $querys=$q_config->runQuery($BD, $station);
                    if(!empty($query))
                    {
                        foreach($querys as $query)
                        {
                            if($BD->sql_select($query, $results))
                            {
                                while($row=$results->fetch(PDO::FETCH_ASSOC))
                                {
                                    $data[$row['f_read_time']][$sensor]=$row;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}


?>