<?php
if(!User::getLoginSession())
{
    if(isset($_POST['username']) and isset($_POST['password']))
    {
        $q_usuario = req("username");
        $q_password = req("password");
        // verifico el usuario
        if(User::verify_user($q_usuario, $q_password))
        {
            // bien
            redireccionar('/');
        }else
        {
            User::SignOff();
            mensaje("Error en dato de usuario y/o contraseña","","error");
        }
    }else
    {
        //pido usuario y contraseña para el ingreso
        User::Login();
    }
}
?>