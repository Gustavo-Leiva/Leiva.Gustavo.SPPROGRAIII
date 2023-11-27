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

require __DIR__ . '/../vendor/autoload.php';
require_once '../src/AccesoDatos.php';
require_once '../src/Controllers/UsuariosController.php';
require_once '../src/Controllers/DepositosController.php';
require_once '../src/Controllers/RetirosController.php';
require_once '../src/Controllers/CuentasController.php';
require_once '../src/middlewares/LoginMiddlewareEspecifico.php';
require_once '../src/middlewares/LoginMiddlewareTodos.php';
// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->post('/login', UsuariosController::class . ':POST_Login');
  $group->get('/listar', UsuariosController::class . ':GET_TraerTodos')
  ->add(new LoginMiddlewareTodos());
  $group->get('/listarId', UsuariosController::class . ':GET_TraerUno')
    ->add(new LoginMiddlewareEspecifico("Admin"));    
  $group->post('/insertar', UsuariosController::class . ':POST_InsertarUsuario')
      ->add(new LoginMiddlewareEspecifico("Admin"));

  // $group->get('/guardarUsuarioCsv', UsuariosController::class . ':GET_GuardarEnCSV');
  // $group->get('/leerUsuarioDeCsv', UsuariosController::class . ':GET_CargarUsuariosCSV');
});







$app->group('/depositos', function (RouteCollectorProxy $group) {
  $group->post('/insertar', DepositosController::class . ':POST_insertarDeposito');
  $group->get('/listar', DepositosController::class . ':GET_traerTodos');
  $group->post('/cambiarEstado', MesasController::class . ':POST_cambiar_estado_de_mesa')
  ->add(new LoginMiddlewareEspecifico("Admin"));
  
});

$app->group('/retiros', function (RouteCollectorProxy $group) {
  $group->post('/insertar', RetirosController::class . ':POST_insertarRetiro');
  $group->get('/listar', RetirosController::class . ':GET_traerTodos')
  ->add(new LoginMiddlewareEspecifico("Admin"));
 
  
});





$app->group('/cuentas', function (RouteCollectorProxy $group) {
  $group->post('/insertar', CuentasController::class . ':POST_insertarCuenta');
  $group->get('/listar', CuentasController::class . ':GET_TraerTodos');
  $group->get('/listarId', CuentasController::class . ':GET_TraerUno')
    ->add(new LoginMiddlewareEspecifico("Admin"));
  $group->post('/modificarCuenta', CuentasController::class . ':POST_modificarCuenta')
  ->add(new LoginMiddlewareEspecifico("Admin"));
  $group->post('/borrarCuenta', CuentasController::class . ':POST_borrarCuenta')
  ->add(new LoginMiddlewareEspecifico("Admin"));

});


$app->group('/ajustes', function (RouteCollectorProxy $group) {
  $group->post('/realizar', CuentasController::class . ':POST_realizarAjuste');
  
});

$app->group('/movimientos', function (RouteCollectorProxy $group) {
  //Depositos
  $group->get('/listarDepositosTipoCuenta-Moneda-fecha', DepositosController::class . ':GET_totalDepositadoPorTipoCuentaYMonedaEnFecha')
  ->add(new LoginMiddlewareEspecifico("Admin"));
  $group->get('/listarDepositosUsuario', DepositosController::class . ':GET_traer_depositos_por_usuario')
    ->add(new LoginMiddlewareEspecifico("Admin"));
   $group->get('/listarDepositosFechasOrdenadoNombre', DepositosController::class . ':GET_traer_depositos_entre_fechas_ordenados');
   $group->get('/listarDepositosTipoCuenta', DepositosController::class . ':GET_traer_depositos_por_tipo_cuenta');
   $group->get('/listarDepositosTipoMoneda', DepositosController::class . ':GET_traer_depositos_por_tipo_moneda');
   $group->get('/listarTodos', CuentasController::class . ':GET_operacionesCuenta');
   
   //Retiros
   $group->get('/listarRetirosTipoCuenta-Moneda-fecha', RetirosController::class . ':GET_totalRetiradoPorTipoCuentaYMonedaEnFecha')
   ->add(new LoginMiddlewareEspecifico("Admin"));
   $group->get('/listarRetirosUsuario', RetirosController::class . ':GET_traer_retiros_por_usuario')
   ->add(new LoginMiddlewareEspecifico("Admin"));
   $group->get('/listarRetirosFechasOrdenadoNombre', RetirosController::class . ':GET_traer_retiros_entre_fechas_ordenados');
   $group->get('/listarRetirosTipoCuenta', RetirosController::class . ':GET_traer_retiros_por_tipo_cuenta');
   $group->get('/listarRetirosTipoMoneda', RetirosController::class . ':GET_traer_retiros_por_tipo_moneda');

    
  
  
});




//Run application
$app->run();
