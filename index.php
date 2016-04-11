<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

session_start();

require 'config.php';
//

require PATH_ROOT.'/lib/class_page.php';
require PATH_ROOT.'/lib/class_imetos.php';
require PATH_ROOT.'/lib/class_station.php';
require PATH_ROOT.'/lib/class_sensor.php';
require PATH_ROOT.'/lib/class_config.php';
require PATH_ROOT.'/lib/class_ftp.php';
require PATH_ROOT.'/lib/class_users.php';

// Require composer autoloader
require PATH_ROOT.'/lib/vendor/autoload.php';
$router = new \Bramus\Router\Router();

Page::header();

// *****************************************************************************
// ruta principal / main
// *****************************************************************************
echo "<pre>";
print_r($_POST);
echo "</pre>";

$router->get('/', function(){
    include './controllers/main.php';
});
$router->post('/',function(){
    include './controllers/main.php';
});
$router->get('/sign_off',function(){
    User::SignOff();
    redireccionar('/');
});
$router->get('/users/(\w+)/(\d+)', function($action,$id){
    $_POST['action']=req($action);
    $_POST['userid']=req($id);
    include './controllers/users.php';
});
$router->get('/stations/(\w+)/(\d+)', function($action,$id){
    $_POST['action']=req($action);
    $_POST['stationid']=req($id);
    include './controllers/stations.php';
});
$router->get('/reports/(\w+)/(\d+)', function($action,$id){
    $_POST['action']=req($action);
    $_POST['reportid']=req($id);
    include './controllers/reports.php';
});
//
$router->run();
?>