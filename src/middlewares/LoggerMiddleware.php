<?php
// middlewares.php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

// require_once '../src/Clases/AutentificadorJWT.php';
// require_once '../src/AccesoDatos.php';


class LoggerMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        
        $method = $request->getMethod();
        $url = $route->getPattern();
        $ip = $request->getServerParams()['REMOTE_ADDR'];
    
        $logger = AccesoDatos::obtenerConexionDatos()->obtenerLogger();
        $logger->logAccess($method, $url, $ip, 'Éxito');


        $response = $handler->handle($request);

        return $response;
    }

}

?>
