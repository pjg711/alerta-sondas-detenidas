<?php
require '../config.php';
require 'lib/class_imetos.php';
require 'lib/class_usuarios.php';


// busco los usuarios para expotar datos
$query="
    SELECT  `username`,
            `password`,
            `server`,
            `database`,
            `usertype`,
            `mails`
    FROM    `usuarios`
    WHERE   `enable_user`=1 AND
            `usertype`='imetos'";
if(sql_select($query,$consulta))
{
    
}

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