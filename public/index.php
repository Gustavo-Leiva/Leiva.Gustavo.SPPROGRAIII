<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use src\Controllers\UsuariosController;
use src\Controllers\DepositosController;
use src\Controllers\RetirosController;
use src\Controllers\CuentasController;
use src\Controllers\LogsController;


require __DIR__ . '/../vendor/autoload.php';
require_once '../src/AccesoDatos.php';
require_once '../src/Controllers/UsuariosController.php';
require_once '../src/Controllers/DepositosController.php';
require_once '../src/Controllers/RetirosController.php';
require_once '../src/Controllers/CuentasController.php';
require_once '../src/Controllers/LogsController.php';
require_once '../src/Middlewares/SupervisorMiddleware.php';
require_once '../src/Middlewares/AuthMiddleware.php';
require_once '../src/Middlewares/AdministradorMiddleware.php';
require_once '../src/Middlewares/CajeroMiddleware.php';
require_once '../src/Middlewares/OperadorMiddleware.php';
require_once '../src/Middlewares/LoggerMiddleware.php';

// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

// Asignar el middleware a una variable


//ok visto
  $app->group('/usuarios', function (RouteCollectorProxy $group) {

    $group->post('/login', UsuariosController::class . ':POST_Login'); 
   
   
    
    $group->get('/listar', UsuariosController::class . ':GET_TraerTodos')    
    ->add(new AdministradorMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware()); 
   
   
    
    $group->get('/listarId', UsuariosController::class . ':GET_TraerUno')
    ->add(new AdministradorMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware());    
    
       
    $group->post('/insertar', UsuariosController::class . ':POST_InsertarUsuario')
    ->add(new AdministradorMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware());   
   

  
    $group->get('/guardarUsuarioCsv', UsuariosController::class . ':GET_GuardarEnCSV');
    $group->get('/leerUsuarioDeCsv', UsuariosController::class . ':GET_CargarUsuariosCSV');
  });



  $app->group('/depositos', function (RouteCollectorProxy $group) {
    $group->post('/insertar', DepositosController::class . ':POST_insertarDeposito')
    ->add(new CajeroMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware()); 
   
    
    $group->get('/listar', DepositosController::class . ':GET_traerTodos')
    ->add(new OperadorMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware()); 

    $group->get('/listarUno', DepositosController::class . ':GET_traerUno')
    ->add(new OperadorMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware()); 
    
      
    
  });



  $app->group('/retiros', function (RouteCollectorProxy $group) {
    $group->post('/insertar', RetirosController::class . ':POST_insertarRetiro')
    ->add(new CajeroMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware()); 

    $group->get('/listar', RetirosController::class . ':GET_traerTodos')
    ->add(new OperadorMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware()); 
  });


  $app->group('/cuentas', function (RouteCollectorProxy $group) {
    $group->post('/insertar', CuentasController::class . ':POST_InsertarCuenta');
    $group->get('/listar', CuentasController::class . ':GET_TraerTodos')
    ->add(new OperadorMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware());

    $group->get('/listarId', CuentasController::class . ':GET_TraerUno')
    ->add(new OperadorMiddleware())
    ->add(new AuthMiddleware())
    ->add(new LoggerMiddleware()); 

    $group->post('/modificarCuenta', CuentasController::class . ':POST_modificarCuenta')
    ->add(new LoggerMiddleware()); 

    $group->post('/borrarCuenta', CuentasController::class . ':POST_borrarCuenta')
    ->add(new LoggerMiddleware()); 
    
  });
    
$app->group('/ajustes', function (RouteCollectorProxy $group) {
  $group->post('/realizar', CuentasController::class . ':POST_realizarAjuste')
  ->add(new SupervisorMiddleware())
  ->add(new AuthMiddleware())
  ->add(new LoggerMiddleware()); 
  
});

$app->group('/movimientos', function (RouteCollectorProxy $group) {
  

  //DEPOSITOS
  //ok visto
  $group->get('/listarDepositosTipoCuenta-Moneda-fecha', DepositosController::class . ':GET_totalDepositadoPorTipoCuentaYMonedaEnFecha')
  ->add(new OperadorMiddleware())
  ->add(new AuthMiddleware())
  ->add(new LoggerMiddleware()); 

  //ok visto
  $group->get('/listarDepositosUsuario', DepositosController::class . ':GET_traer_depositos_por_usuario')
  ->add(new OperadorMiddleware())
  ->add(new AuthMiddleware())
  ->add(new LoggerMiddleware()); 

  //ok visto
   $group->get('/listarDepositosFechasOrdenadoNombre', DepositosController::class . ':GET_traer_depositos_entre_fechas_ordenados')
   ->add(new OperadorMiddleware())
   ->add(new AuthMiddleware())
   ->add(new LoggerMiddleware()); 


   //ok visto
   $group->get('/listarDepositosTipoCuenta', DepositosController::class . ':GET_traer_depositos_por_tipo_cuenta')
   ->add(new OperadorMiddleware())
   ->add(new AuthMiddleware())
   ->add(new LoggerMiddleware()); 

   //ok visto
   $group->get('/listarDepositosTipoMoneda', DepositosController::class . ':GET_traer_depositos_por_tipo_moneda')
   ->add(new OperadorMiddleware())
   ->add(new AuthMiddleware())
   ->add(new LoggerMiddleware()); 

   $group->get('/listarTodos', CuentasController::class . ':GET_operacionesCuenta')
   ->add(new OperadorMiddleware())
   ->add(new AuthMiddleware())
   ->add(new LoggerMiddleware()); 
   
   //RETIROS

   //ok visto
   $group->get('/listarRetirosTipoCuenta-Moneda-fecha', RetirosController::class . ':GET_totalRetiradoPorTipoCuentaYMonedaEnFecha')
   ->add(new OperadorMiddleware())
   ->add(new AuthMiddleware())
   ->add(new LoggerMiddleware()); 


   //ok visto
   $group->get('/listarRetirosUsuario', RetirosController::class . ':GET_traer_retiros_por_usuario')
   ->add(new OperadorMiddleware())
   ->add(new AuthMiddleware())
   ->add(new LoggerMiddleware()); 

   //ok visto
   $group->get('/listarRetirosFechasOrdenadoNombre', RetirosController::class . ':GET_traer_retiros_entre_fechas_ordenados')
   ->add(new OperadorMiddleware())
   ->add(new AuthMiddleware())
   ->add(new LoggerMiddleware()); 


   //ok visto
   $group->get('/listarRetirosTipoCuenta', RetirosController::class . ':GET_traer_retiros_por_tipo_cuenta')
   ->add(new OperadorMiddleware())
   ->add(new AuthMiddleware())
   ->add(new LoggerMiddleware()); 


   //ok visto
   $group->get('/listarRetirosTipoMoneda', RetirosController::class . ':GET_traer_retiros_por_tipo_moneda')
   ->add(new OperadorMiddleware())
   ->add(new AuthMiddleware())
   ->add(new LoggerMiddleware());

});

// Ruta para descargar logs como CSV
// $app->group('/logs', function (RouteCollectorProxy $group) {
//   $group->get('/descargar-csv', \src\Controllers\LogsController::class . ':GET_DescargarLogsCSV');
// });

$app->group('/Logs', function (RouteCollectorProxy $group) {
  $group->get('/descargar-csv', LogsController::class . ':GET_DescargarLogsCSV');
});



    
  


//Run application
$app->run();

?>
