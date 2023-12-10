<?php
use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
// use AutentificadorJWT;
// use Exception;

require_once '../src/Clases/AutentificadorJWT.php';
// require_once '../src/Clases/Usuario.php';


class SupervisorMiddleware
{
    
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $response = new ResponseMW();    
      
        try
        {
            $data = AutentificadorJWT::ObtenerData($token);
            if (is_object($data) && property_exists($data, 'tipo') && $data->tipo == 'supervisor') 
            {
                $response= $handler->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(array('Error' => "Accion reservada solamente para los supervisor.")));
            }
        }
        catch(Exception $excepcion)
        {
            $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
