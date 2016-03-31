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
                    
                    echo "<br>-------------------------------------------------------<br>";
                    echo "UserId--------->{$user->getId()}<br>";
                    echo "station-code--->{$station->getStationCode()}<br>";
                    echo "enable--------->{$q_config->getEnable()}<br>";
                    
                    switch($q_config->getPeriodo())
                    {
                        case ''
                    }
                    if($q_config->getEnable())
                    {
                        echo "fecha inicial------>{$q_config->getPeriodoFechaInicial()}<br>";
                        echo "mk fecha inicial--->{$q_config->getPeriodoMkFechaInicial()}<br>";
                        
                        //$query=$q_config->runQuery();
                        /*
                        foreach($q_config->getSensores() as $sensor)
                        {
                            
                            $query="SELECT * "
                            echo "<pre>";
                            print_r($sensor);
                            echo "</pre>";
                        }
                        */
                    }
                }
            }
        }
    }
}


?>