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
    if(User::getLoginSession())
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
    /*
    $router->get('/',function(){
        include './controllers/login.php';
    });
    $router->post('/',function(){
        include './controllers/login.php';
    });
     * 
     */
});
$router->get('/sign_off',function(){
    User::SignOff();
    redireccionar('/login');
});
// Users
$router->mount('/users', function() use ($router){
    $router->get('/', function(){
        if(User::getIsAdmin())
        {
            // si es admin muestro listado de usuarios
        }else
        {
            redireccionar('/');
        }
    });
    $router->post('/new', function(){
        // nuevo usuario
        if(User::save())
        {
            mensaje("El usuario se guard\u00F3 con \u00E9xito","Nuevo usuario");
        }else
        {
            mensaje("ERROR. No se pudo guardar el nuevo usuario","","error");
        }
    });
    $router->post('/update', function(){
        // actualizar usuario
        if(User::update())
        {
            mensaje("Se actualiz\u00F3 el usuario","Editar usuario");
        }else
        {
            mensaje("ERROR! Problema al actualizar usuario","","error");
        }
    });
    $router->post('/save_config', function(){
        // con
        $config = new Config_Station();
        if($config->update())
        {
            mensaje("Se guardó la configuración para la estación","Configurar estación");
        }else
        {
            mensaje($config->getError(),"","error");
        }
        
    });
    $router->post('/delete', function(){
        // borrar usuario
        $id=req('id');
        
    });
});
//Stations
$router->mount('/stations', function() use ($router){
    $router->get('/', function(){
        // listado de estaciones
        echo "listado de estaciones<br>";
    });
});
// Informe de sondas detenidas
$router->mount('/reports', function() use ($router){
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