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
    form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'index.php');
    myvar = document.createElement('input');
    myvar.setAttribute('name', 'confirmed_delete_report');
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
        confirmed_delete_user(id);
    } else {
        location.href='index.php';
    }
}
function confirmed_delete_user(id){
    form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'index.php');
    myvar = document.createElement('input');
    myvar.setAttribute('name', 'confirmed_delete_user');
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
    //location.href='index.php?confirmed_erase_all';
    form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'index.php');
    myvar = document.createElement('input');
    myvar.setAttribute('name', 'confirmed_erase_all');
    myvar.setAttribute('type', 'hidden');
    myvar.setAttribute('value', '');
    form.appendChild(myvar);
    document.body.appendChild(form);
    form.submit();       
}
function mostrar_ocultar(id,grupo,visible){
    if(grupo === undefined){
        //console.log(id+' no esta definido');
    }else{
        var elementos=document.getElementsByClassName(grupo);
        for(i = 0; i < elementos.length; i++){
            elementos[i].style.display = 'none';
        }
    }
    //console.log('id--->'+id);
    obj_ver = document.getElementById(id);
    if(visible === undefined){
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
function seleccionar_sensores_todos(estacion){
    var obj=document.getElementById('estacion_'+estacion);
    var objs=obj.getElementsByClassName('sensores-todos');
    var valor=document.getElementById('sensor-todos-'+estacion);
    console.log('cantidad-->'+objs.length);
    console.log('valor----->'+valor.checked);
    for(i=0;i<objs.length;i++){
        if(objs[i].id=='sensor-'+estacion){
            objs[i].checked=valor.checked;
        }
    }
}
function estacion_activa(activar,estacion){
    // console.log('estacion--->'+estacion);
    var obj=document.getElementById(estacion);
    var objs=obj.getElementsByClassName('todos')
    var objs2=obj.getElementsByClassName('sensores-todos');
    var objs3=obj.getElementsByClassName('sensores');
    // console.log('cantidad objetos todos-->'+objs.length);
    // console.log('activa?-->'+activar.checked);
    if(activar.checked){
        //activo
        var valor=false;
        var bcolor='#000000';
    }else{
        // desactivo
        var valor=true;
        var bcolor='#dadada';
    }
    for(var i=0;i<objs.length;i++){
        // console.log('objeto-->'+objs[i].id);
        if(objs[i].id!='activar' && objs[i].type!='hidden'){
            // console.log('pase por aca');
            objs[i].disabled=valor;
        }else{
            objs[i].disabled=false;
        }
    }
    for(var i=0;i<objs2.length;i++){
        objs2[i].disabled=valor;
    }
    objs3[0].disabled=valor;
    //
    var objs4=obj.getElementsByTagName('label');
    for(i=0;i<objs4.length;i++){
        objs4[i].style.color=bcolor;
    }
}