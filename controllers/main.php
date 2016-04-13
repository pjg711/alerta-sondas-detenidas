<?php
/*
if(User::getLoginSession())
{
    // *************************************************
    // User
    // *************************************************
    if(isset($_POST['data_export']))
    {
        $_SESSION['action']='data_export';
        // exporto los datos
        $userid=  req('userid');
        $f_station_code=  req('f_station_code');
        
    }
    //
    if(isset($_POST['confirmed_delete_report']))
    {
        // borrar informe 
        $id_informe=$_POST['confirmed_delete_report'];
        if(User::borrar_informe($id_informe))
        {
            mensaje("Se borr\u00F3 el informe","Borrar informe");
        }else
        {
            mensaje("ERROR! No se pudo borrar el informe","","error");
        }
    }
    if(isset($_POST['confirmed_erase_all']))
    {
        // borra todos los informes para el usuario 
        if(isset($_SESSION['userid']))
        {
            $userid=$_SESSION['userid'];
            User::borrar_informes_todos($userid);
        }
    }
}
 * 
 */
//
if(Login::getLoginSession())
{
    echo "
    <ul style=\"margin:19px 0 18px 0;\" class=\"nav nav-tabs test2\">
        <li class=\"active\"><a data-toggle=\"tab\" href=\"#exportacion\">Exportación de datos de sondas</a></li>
        <li><a data-toggle=\"tab\" href=\"#detenidas\">Informe de detenidas</a></li>
    </ul>";
    Login::logged(Login::getIsAdmin());
    
    if(Login::getIsAdmin())
    {
        echo "<div class=\"tab-content\">
                <div id=\"exportacion\" class=\"tab-pane fade in active\">";
        // solo admin puede crear un nuevo usuario
        User::new_user();
    }
    // es usuario admin y presento todos los informes ordenados por fecha
    User::listar(Login::getIsAdmin());
    echo "          <br><br><br>
                </div>
                <div id=\"detenidas\" class=\"tab-pane fade\">";
    // listado de archivos csv
    //listado_csvs();
    // todos los informes
    Reports::listado_informes();
    echo "  </div>";
    Page::footer();
    echo "</div>";
   
    /*
    if(Login::getIsAdmin())
    {
        // para administradores
        if(isset($_POST['alta_usuario']))
        {
            //inserto usuario nuevo
            if(User::save())
            {
                mensaje("Se guard\u00F3 el nuevo usuario","Nuevo usuario");
            }else
            {
                mensaje("ERROR! No se pudo guardar el usuario","","error");
            }
        }
        if(isset($_POST['confirmed_delete_user']))
        {
            $userid= req("confirmed_delete_user");
            if(User::delete_user($userid))
            {
                mensaje("El usuario fue borrado","Borrar usuario");
            }else
            {
                mensaje("ERROR! No se pudo borrar el usuario","","error");
            }
        }
        if(isset($_POST['realizar_informe']))
        {
            $q_usuario=array();
            $q_usuario[1]=  req("realizar_informe");
            User::hago_informes($q_usuario,true);
        }
        if(isset($_POST['cambiar_configuracion']))
        {
            
        }
        echo "<div class=\"tab-content\">
                <div id=\"exportacion\" class=\"tab-pane fade in active\">";
        // solo admin puede crear un nuevo usuario
        //User::new_user();
        // es usuario admin y presento todos los informes ordenados por fecha
        //User::listar(true);
        echo "      <br><br><br>
                </div>
                <div id=\"detenidas\" class=\"tab-pane fade\">";
        // listado de archivos csv
        //listado_csvs();
        // todos los informes
        $user->listado_informes();
        echo "  </div>";
        echo "</div>";
    }else
     * 
     */
     /*
    {
        $userid=$_SESSION['userid'];
        echo "<div class=\"tab-content\">
                <div id=\"exportacion\" class=\"tab-pane fade in active\">";
        //
        User::listar(false);
        echo "      <br><br><br>
                </div>
                <div id=\"detenidas\" class=\"tab-pane fade\">";
        // solo los informes de usuario ftp $userid
        //$user->listado_informes($userid);
        echo "  </div>";
        Page::footer();
        echo "</div>";
    }
      * 
      */
    if(isset($_POST['comprobar']))
    {
        // vuelvo a mostrar el div
        ?>
        <script LANGUAGE="JavaScript">
            mostrar_ocultar('nuevo_usuario');
        </script>
        <?php
    }
    Page::footer();
}else
{
    // no esta logeado
    redireccionar('/login');
    echo "</body>
    </html>";
}
?>