function borrar_informe(id){
    var respuesta = window.confirm('\u00BFEst\u00E1 seguro que quiere borrar el informe?');
    if(respuesta){
        // lo elimino 
        confirmar_borrar_informe(id);
    }else{
        location.href='index.php';
    }
}
function confirmar_borrar_informe(id){
    //location.href='index.php?confirmado_borrar_informe&id='+id;
    form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'index.php');
    myvar = document.createElement('input');
    myvar.setAttribute('name', 'confirmado_borrar_informe');
    myvar.setAttribute('type', 'hidden');
    myvar.setAttribute('value', id);
    form.appendChild(myvar);
    document.body.appendChild(form);
    form.submit();       
}
function borrar_usuario(id){
    var respuesta = window.confirm('\u00BFEst\u00E1 seguro que quiere borrar el usuario?');
    if(respuesta){
        // lo elimino 
        confirmar_borrar_usuario(id);
    } else {
        location.href='index.php';
    }
}
function confirmar_borrar_usuario(id){
    //location.href='index.php?confirmado_borrar_usuario&id='+id;
    form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'index.php');
    myvar = document.createElement('input');
    myvar.setAttribute('name', 'confirmado_borrar_usuario');
    myvar.setAttribute('type', 'hidden');
    myvar.setAttribute('value', id);
    form.appendChild(myvar);
    document.body.appendChild(form);
    form.submit();       
}
function borrar_todos(){
    //borra todos los informes del mismo usuario
    var respuesta = window.confirm('\u00BFEst\u00E1 seguro que quiere borrar todos los informes?');
    if(respuesta){
        // lo elimino 
        confirmar_borrar_todos();
    } else {
        location.href='index.php';
    }
}
function confirmar_borrar_todos(){
    //location.href='index.php?confirmado_borrar_todos';
    form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'index.php');
    myvar = document.createElement('input');
    myvar.setAttribute('name', 'confirmado_borrar_todos');
    myvar.setAttribute('type', 'hidden');
    myvar.setAttribute('value', '');
    form.appendChild(myvar);
    document.body.appendChild(form);
    form.submit();       
}
function mostrar_ocultar(id, visible){
    obj_ver = document.getElementById(id);
    if(visible == undefined){
        obj_ver.style.display = (obj_ver.style.display == 'block') ? 'none' : 'block';
    }else{
        obj_ver.style.display = visible;
    }
}
function realizar_informe(usuario){
    form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'index.php');
    myvar = document.createElement('input');
    myvar.setAttribute('name', 'realizar_informe');
    myvar.setAttribute('type', 'hidden');
    myvar.setAttribute('value', usuario);
    form.appendChild(myvar);
    document.body.appendChild(form);
    form.submit();       
}