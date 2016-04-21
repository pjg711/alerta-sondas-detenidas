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
$router->respond('/[:controller]?/[:action]?', function ($request, $response) {
    echo "pase por aca<br>";
    if(isset($request->action)) $_POST['action']=$request->action;
    if($request->param('station_code')) $_POST['station_code']=$request->param('station_code');
    if($request->param('userid')) $_POST['userid']=$request->param('userid');
    // users
    if($request->controller=="users")
    {
        include './controllers/users.php';
    }
    // stations
    if($request->controller=="stations")
    {
        echo "pase por aca<br>";
        include './controllers/stations.php';
    }
    // reports
    if($request->controller=="reports")
    {
        include './controllers/reports.php';        
    }
});

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
?>