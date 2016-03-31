<?php
// para los informes de sondas detenidas
if(isset($argv))
{
    if(count($argv)==2)
    {
        // llamado desde un script en el cron...
        // el 2do argumento es el nombre de usuario... 
        // o "todos" para realizar el informe para todos los usuarios tipos FTP
        // ejemplo: php index.php monsanto.seedmech.com.ar
        // busco el nombre de usuario en la base de datos
        $usuario->hago_informes($argv,true);
        exit;
    }
}
?>