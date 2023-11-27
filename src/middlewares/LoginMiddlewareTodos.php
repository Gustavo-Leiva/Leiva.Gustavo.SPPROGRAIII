<?php

use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once '../src/Clases/Autenticador.php';
class LoginMiddlewareTodos
{
    public function __invoke(Request $request, RequestHandler $handler):ResponseMW
    {
        $response = new ResponseMW();
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            $respuesta1 = Autenticador::validar_token($token, "Empleado");
            $respuesta2 = Autenticador::validar_token($token, "SubAdmin");
            if($respuesta == "Validado" || $respuesta1 == "Validado" || $respuesta2 == "Validado"){
                $response = $handler->handle($request);
                return $response;
            }
            else{
                $retorno = json_encode(array("mensaje" => $respuesta2));
                
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}