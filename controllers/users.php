<?php
if(Login::getLoginSession())
{
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
        case 'edit':
            if(User::update())
            {
                mensaje("El usuario se actualiz\u00F3 con \u00E9xito","Editar usuario");
            }else
            {
                mensaje("ERROR. No se pudo editar el usuario","","error");
            }
            break;
            
        case 'save_config':
            // guarda la nueva contraseña
            if(User::save_config())
            {
                mensaje("Se cambió contraseña de usuario");
            }else
            {
                mensaje("ERROR. No se pudo cambiar la contraseña.","","error");
            }
            break;
            
        case 'delete_user':
            // borrar usuario
            break;
        
        case 'confirmed_delete':
            // confirmar borrado de usuario
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