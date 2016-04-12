<?php
if(Login::getLoginSession())
{
    echo "pase por users<br>";
    if(!isset($_POST['action'])) exit;
    $userid=req('userid');
    switch($_POST['action'])
    {
        case 'new':
            // nuevo usuario
            if(User::save())
            {
                mensaje("El usuario se guard\u00F3 con \u00E9xito","Nuevo usuario");
            }else
            {
                mensaje("ERROR. No se pudo guardar el nuevo usuario","","error");
            }
            break;
        case 'update':
            if(User::update())
            {
                mensaje("El usuario se actualiz\u00F3 con \u00E9xito","Editar usuario");
            }else
            {
                mensaje("ERROR. No se pudo editar el usuario","","error");
            }
            break;
        case 'save_config':
            if(User::save_config())
            {
                
            }
            break;
        case 'delete':
            
            break;
        case 'confirmed_delete':
            if(User::delete_user($userid))
            {
                mensaje("El usuario fue borrado","Borrar usuario");
            }else
            {
                mensaje("ERROR! No se pudo borrar el usuario","","error");
            }
            break;
    }
    
}
?>