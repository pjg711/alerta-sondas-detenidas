function borrar_informe(id){
    var respuesta = window.confirm('¿Está seguro que quiere borrar el informe?');
    if(respuesta){
        // lo elimino 
        confirmar_borrar_informe(id);
    }else{
        location.href='index.php';
    }
}
function confirmar_borrar_informe(id){
    location.href='index.php?confirmado_borrar_informe&id='+id;
}
function borrar_usuario(id){
    var respuesta = window.confirm('¿Está seguro que quiere borrar el usuario?');
    if(respuesta){
        // lo elimino 
        confirmar_borrar_usuario(id);
    } else {
        location.href='index.php';
    }
}
function confirmar_borrar_usuario(id){
    location.href='index.php?confirmado_borrar_usuario&id='+id;
}
function borrar_todos(){
    //borra todos los informes del mismo usuario
    var respuesta = window.confirm('¿Está seguro que quiere borrar todos los informes?');
    if(respuesta){
        // lo elimino 
        confirmar_borrar_todos();
    } else {
        location.href='index.php';
    }
}
function confirmar_borrar_todos(){
    location.href='index.php?confirmado_borrar_todos';
}
function mostrar_ocultar(id, visible){
    obj_ver = document.getElementById(id);
    if(visible == undefined){
        obj_ver.style.display = (obj_ver.style.display == 'block') ? 'none' : 'block';
    }else{
        obj_ver.style.display = visible;
    }
}