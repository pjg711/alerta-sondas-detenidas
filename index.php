<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);
ini_set('memory_limit','128M');
//
include("config.php");
//
require 'lib/class_page.php';
require 'lib/class_imetos.php';
require 'lib/class_station.php';
require 'lib/class_sensor.php';
require 'lib/class_config.php';

require 'lib/class_ftp.php';
require 'lib/class_users.php';
//
$page=new PAGE();
$page->header();
//
// Require composer autoloader
require __DIR__ . '/lib/autoload.php';
$router = new \Bramus\Router\Router();
//
$router->set404(function(){
    header('HTTP/1.1 404 Not Found');
    // ... do something special here
});
// *****************************************************************************
// ruta principal / main
// *****************************************************************************
$router->get('/', function(){
    include './controllers/main.php';
});
$router->get('/user/(\w+)', function($action){
    $_GET['action']=req($action);
    include './controllers/user.php';
});
$router->get('/station/(\w+)', function($action){
    $_GET['action']=req($action);
    include './controllers/station.php';
});
$router->get('/report/(\w+)', function($action){
    $_GET['action']=req($action);
    include './controllers/report.php';
});
$router->get('/ftp/(\w+)', function($action){
    $_GET['action']=req($action);
    include './controllers/ftp.php';
});
$router->run();
?>