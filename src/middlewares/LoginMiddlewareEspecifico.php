<?php
use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;


require_once '../src/Clases/Autenticador.php';

class LoginMiddlewareEspecifico
{
    private $tipoUsuario;
    public function __construct($tipoUsuario){
        $this->tipoUsuario = $tipoUsuario;
    }

    public function __invoke(Request $request, RequestHandler $handler):ResponseMW
    {
        $response = new ResponseMW();
        $param = $request->getQueryParams();

        // var_dump($param);
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, $this->tipoUsuario);
            if($respuesta == "Validado"){
                $response = $handler->handle($request);
                return $response;
            }
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
                
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}