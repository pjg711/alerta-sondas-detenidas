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
//
// Require composer autoloader
require PATH_ROOT.'/lib/vendor/autoload.php';
$router = new \Bramus\Router\Router();

Page::header();

// *****************************************************************************
// ruta principal / main
// *****************************************************************************

$router->mount('/', function() use ($router){
    include './controllers/main.php';
    /*
    $router->get('/', function(){
        echo "pase por aca<br>";
        if(Login::getLoginSession())
        {
            include './controllers/main.php';
        }else
        {
            // no esta logeado
            redireccionar('/login');
        }
    });
     * 
     */
});
/*
$router->get('/', function(){
    //include './controllers/login.php';
    echo "pase por aca 2<br>";
    if(Login::getLoginSession())
    {
        include './controllers/main.php';
    }else
    {
        // no esta logeado
        redireccionar('/login');
    }
});
 * 
 */
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
    });
    include './controllers/users.php';
});
// Stations
$router->mount('/stations', function() use ($router){
    $router->post('/export/(\d+)/(\d+)', function($station_code, $userid){
        // exporto datos de estacion
        $_POST['action']='export_data';
        $_POST['station_code']=$station_code;
        $_POST['userid']=$userid;
    });
    $router->post('/config/(\d+)', function($station_code){
        // guardo la configuracion
        $_POST['action']='save_config';
        $_POST['station_code']=$station_code;
    });
    include './controllers/stations.php';
});
// Informe de sondas detenidas
$router->mount('/reports', function() use ($router){
    
    $router->get('/', function(){
        
    });
    $router->post('/', function(){
        
    });
    include './controllers/reports.php';
});

$router->run();
?>