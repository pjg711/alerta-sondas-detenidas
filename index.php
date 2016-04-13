<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

session_start();

require 'config.php';
//
require PATH_ROOT.'/lib/class_page.php';
require PATH_ROOT.'/lib/class_login.php';
require PATH_ROOT.'/lib/class_imetos.php';
require PATH_ROOT.'/lib/class_station.php';
require PATH_ROOT.'/lib/class_sensor.php';
require PATH_ROOT.'/lib/class_config.php';
require PATH_ROOT.'/lib/class_ftp.php';
require PATH_ROOT.'/lib/class_users.php';
require PATH_ROOT.'/lib/class_reports.php';

// Require composer autoloader
require PATH_ROOT.'/lib/vendor/autoload.php';
$router = new \Bramus\Router\Router();

Page::header();
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
 * 
 */
// *****************************************************************************
// ruta principal / main
// *****************************************************************************
$router->get('/', function(){
    //include './controllers/login.php';
    if(Login::getLoginSession())
    {
        include './controllers/main.php';
    }else
    {
        // no esta logeado
        redireccionar('/login');
    }
});
$router->mount('/login', function() use ($router){
    include './controllers/login.php';
});
$router->get('/sign_off',function(){
    Login::SignOff();
    redireccionar('/login');
});
// Users
$router->mount('/users', function() use ($router){
    $router->get('/', function(){
        include './controllers/users.php';
    });
    $router->get('/(\w+)/(\d+)', function($action,$id) {
        $_POST['action']=req($action);
        $_POST['userid']=req($id);
        include './controllers/users.php';
    });    
});
//Stations
$router->mount('/stations', function() use ($router){
    $router->post('/export/(\d+)/(\d+)', function($station_code, $userid){
        // exporto datos de estacion
        $_POST['action']='export_data';
        $_POST['station_code']=$station_code;
        $_POST['userid']=$userid;
        include './controllers/stations.php';
    });
    $router->post('/', function(){
        // formulario de configuracion de estacion
        echo "listado de estaciones post<br>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
    });
});
// Informe de sondas detenidas
$router->mount('/reports', function() use ($router){
    $router->get('/', function(){
        
    });
    $router->post('/', function(){
        
    });
    
});
/*
$router->get('/stations/(\w+)/(\d+)', function($action,$id){
    $_POST['action']=req($action);
    $_POST['stationid']=req($id);
    include './controllers/stations.php';
});
 * 
 */
$router->get('/reports/(\w+)/(\d+)', function($action,$id){
    $_POST['action']=req($action);
    $_POST['reportid']=req($id);
    include './controllers/reports.php';
});
$router->mount('/export', function() use ($router){
    
});
$router->run();
?>