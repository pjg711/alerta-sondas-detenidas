<?php
require 'lib/class_users.php';
if(User::getLoginSession())
{
    echo "pase por aca<br>";
    if(!isset($_POST['action'])) exit;
    $userid=req($_POST['userid']);
    switch($_POST['action'])
    {
        case 'new':
            if(User::save())
            {
                mensaje("El usuario se guard\u00F3 con \u00E9xito","Nuevo usuario");
            }else
            {
                mensaje("ERROR. No se pudo guardar el nuevo usuario","","error");
            }
            break;
            
        case 'edit':
            
            break;
        case 'update':
            
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