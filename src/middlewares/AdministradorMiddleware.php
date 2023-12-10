<?php

use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
// use AutentificadorJWT;
// use Exception;

require_once '../src/Clases/AutentificadorJWT.php';

class AdministradorMiddleware
{
    
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);
        
       
      
        // if ($data->tipo == 'socio') {
            if (is_object($data) && property_exists($data, 'tipo') && $data->tipo == 'administrador') {   
             $response = $handler->handle($request);                
        }else{
            $response = new ResponseMW();
            $payload = json_encode(array('mensaje' => 'No tiene permisos como administrador' ));            
            $response->getBody()->write($payload);
        }        
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}

?>
