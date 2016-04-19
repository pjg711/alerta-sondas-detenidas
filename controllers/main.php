<?php
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
    //Page::footer();
}else
{
    // no esta logeado
    redireccionar('/login');
    echo "</body>
    </html>";
}
?>