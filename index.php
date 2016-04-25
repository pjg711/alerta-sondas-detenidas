<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

session_start();

require 'config.php';
require PATH_ROOT.'/lib/class_page.php';
require PATH_ROOT.'/lib/class_login.php';
require PATH_ROOT.'/lib/class_imetos.php';
require PATH_ROOT.'/lib/class_station.php';
require PATH_ROOT.'/lib/class_sensor.php';
require PATH_ROOT.'/lib/class_config.php';
require PATH_ROOT.'/lib/class_ftp.php';
require PATH_ROOT.'/lib/class_users.php';
require PATH_ROOT.'/lib/class_reports.php';
require PATH_ROOT.'/lib/class_log.php';
//
require PATH_ROOT.'/lib/functions_standard.php';

require_once PATH_ROOT . '/lib/vendor/autoload.php';
$router = new \Klein\Klein();

Page::header();
// API Klein https://github.com/klein/klein.php/wiki/Api
//controllers

/*
$router->with('/users', function () use ($router) {
    $router->respond('GET', '/?', function ($request, $response) {
        // listado de todos los usuarios
        $_POST['action']='listUsers';
    });

    $router->respond('GET', '/[:userid]', function ($request, $response) {
        // Muestra solo un usuario
        $_POST['action']='listUser';
        if(isset($request->userid)) $_POST['userid']=$request->userid;
    });
    
    $router->respond('POST', '/[:action]/[:userid]', function ($request, $response) {
        // new,edit,delete user
        if(isset($request->action)) $_POST['action']=$request->action;
        if(isset($request->userid)) $_POST['userid']=$request->userid;
    });
    //
    include './controllers/users.php';
});

$router->with('/stations', function () use ($router) {
    $router->respond('GET', '/?', function ($request, $response) {
        // listado de todos los usuarios
        $_POST['action']='listStations';
    });

    $router->respond('GET', '/[:stationcode]', function ($request, $response) {
        // Muestra solo un usuario
        $_POST['action']='listStation';
        if(isset($request->stationcode)) $_POST['station_code']=$request->stationcode;
    });
    
    $router->respond('POST', '/[:action]/[:stationcode]', function ($request, $response) {
        if(isset($request->action)) $_POST['action']=$request->action;
        if(isset($request->stationcode)) $_POST['station_code']=$request->stationcode;
    });
    //
    include './controllers/stations.php';
});
 * 
 */
$router->respond('GET','/users', function ()
{
    //listado de todos los usuarios
    $_POST['action']='listUsers';
    include './controllers/users.php';
});
$router->respond('POST','/users/[:action]?/[:userid]?', function ($request,$response)
{
    //editar usuario
    if(isset($request->action)) $_POST['action']=$request->action;
    if(isset($request->userid)) $_POST['userid']=$request->userid;
    include './controllers/users.php';
});
$router->respond('GET','/stations', function ()
{
    //listado de todos los usuarios
    $_POST['action']='listStations';
    include './controllers/stations.php';
});
$router->respond('POST','/stations/[:action]?/[:station_code]?', function ($request,$response)
{
    //editar estacion
    if(isset($request->action)) $_POST['action']=$request->action;
    if(isset($request->station_code)) $_POST['station_code']=$request->station_code;
    include './controllers/stations.php';
});

/*
$router->respond('POST','/[:controller]?/[:action]?/[:station_code]?', function ($request, $response)
{
    if(isset($request->action)) $_POST['action']=$request->action;
    if(isset($request->station_code)) $_POST['station_code']=$request->station_code;
    if(isset($request->userid)) $_POST['userid']=$request->userid;
    // users
    if($request->controller=="users")
    {
        include './controllers/users.php';
    }
    // stations
    if($request->controller=="stations")
    {
        include './controllers/stations.php';
    }
    // reports
    if($request->controller=="reports")
    {
        include './controllers/reports.php';        
    }
});
 * 
 */
//sign off
$router->respond('GET', '/sign_off', function () {
    Login::SignOff();
});
// *****************************************************************************
// ruta principal / main
// *****************************************************************************
$router->respond(function () {
    include './controllers/main.php';
});

$router->dispatch();
/*
    echo "<script type=\"text/javascript\">";
    echo "location.replace(\"/\");";
    echo "</script>";
*/
?>