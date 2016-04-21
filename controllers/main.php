<?php
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
if(Login::getLoginSession())
{
    //echo "pase por login true main.php<br>";
    //exit;
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
    User::users_list(Login::getIsAdmin());
    echo "          <br><br><br>
                </div>
                <div id=\"detenidas\" class=\"tab-pane fade\">";
    // listado de archivos csv
    //listado_csvs();
    // todos los informes
    Reports::reports_list();
    echo "  </div>";
    Page::footer();
    echo "</div>";
    //
    if(isset($_POST['comprobar']))
    {
        // vuelvo a mostrar el div
        ?>
        <script LANGUAGE="JavaScript">
            mostrar_ocultar('nuevo_usuario');
        </script>
        <?php
    }
}else
{
    if(isset($_POST['username']) and isset($_POST['password']))
    {
        $q_usuario = req("username");
        $q_password = req("password");
        // verifico el usuario
        if(Login::verify_user($q_usuario, $q_password))
        {
            // bien
            echo "bien<br>";
            dump($_SESSION);
            //redireccionar('/');
            //header('Location: /');
            redireccionar('/');
        }else
        {
            Login::SignOff();
            mensaje("Error en dato de usuario y/o contraseña","","error");
        }
    }else
    {
        //pido usuario y contraseña para el ingreso
        Login::login_session();
    }
}
?>