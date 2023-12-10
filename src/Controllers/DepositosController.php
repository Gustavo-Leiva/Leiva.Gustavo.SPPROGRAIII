<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Deposito;
use Cuenta;
use AutentificadorJWT;
use AccesoDatos;
use Usuario;

require_once '../src/Clases/Deposito.php';
require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/Cuenta.php';
require_once '../src/Clases/AutentificadorJWT.php';
require_once '../src/Clases/Logger.php';
require_once '../src/AccesoDatos.php';

class DepositosController
{  
    public static $tipoCuentas = array("CA", "CC");
    public static $tipoMoneda = array("$", "USD");

    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }



    //ok visto
    public static function POST_insertarDeposito(Request $request, Response $response, array $args)
{
    $rutaImagenDeposito = 'C:\xampp\htdocs\CuentasBancariasII\src\Controllers\Imagen\imagenesDeDepositos2023';
    $retorno = null; // Definir $retorno inicialmente como null

    // Verificar si la imagen está presente en la solicitud
    if (isset($_FILES['imagen'])) {
        $imagen = $_FILES['imagen'];
    } else {
        $imagen = null;
    }

    $parametros = $request->getParsedBody();
    $numeroCuenta = $parametros['numeroCuenta'];
    $tipoCuenta = $parametros['tipoCuenta'];
    $moneda = $parametros['moneda'];
    $nombre = $parametros['nombre'];
    $importeDepositar = $parametros['importeDepositar'];

    // Validar el tipo de cuenta y la moneda
    if (in_array($tipoCuenta, self::$tipoCuentas) && in_array($moneda, self::$tipoMoneda)) {
        $cuenta = new Deposito($numeroCuenta, $tipoCuenta, $moneda, $nombre, $importeDepositar);
        $respuesta = $cuenta->insertarDeposito();

        // Analizar la respuesta del método insertarRetiro
        $respuestaArray = json_decode($respuesta, true);

        if (isset($respuestaArray["mensaje"])) {
            // Verificar el mensaje y tomar acciones según el resultado
            if ($respuestaArray["mensaje"] == "Deposito realizado con exito") {
                $retorno = json_encode(["mensaje" => "Operacion exitosa"]);
                // Mover la imagen solo si la respuesta es exitosa y se proporcionó una imagen
                if ($imagen !== null) {
                    $destino = $cuenta->definir_destino_imagen($rutaImagenDeposito);
                    move_uploaded_file($imagen['tmp_name'], $destino);
                }
            } elseif ($respuestaArray["mensaje"] == "El importe a depositar debe ser mayor a 0") {
                $retorno = json_encode(["mensaje" => "Fondos insuficientes, el deposito debe ser superior a 0"]);
            } elseif ($respuestaArray["mensaje"] == "El numero de cuenta no existe") {
                $retorno = json_encode(["mensaje" => "Numero de cuenta invalido"]);
            } else {
                $retorno = json_encode(["mensaje" => "Error desconocido"]);
            }
        } else {
            $retorno = json_encode(["mensaje" => "Error en la respuesta"]);
        }
    } else {
        $mensajeError = "Deposito no valido. ";

        if (!in_array($tipoCuenta, self::$tipoCuentas)) {
            $mensajeError .= "Tipo de cuenta no es valido. Debe ser 'CA' o 'CC'. ";
        }

        if (!in_array($moneda, self::$tipoMoneda)) {
            $mensajeError .= "Moneda no es valida. Debe ser '$' o 'USD'.";
        }

        $retorno = json_encode(["mensaje" => $mensajeError]);
    }

    // $token = $request->getHeaderLine('Authorization');
    // $userId = self::obtenerUserIdDesdeToken($token);
    // $usuario =Usuario::obtenerInstanciaVacia(); // Instanciar un objeto Usuario sin proporcionar argumentos
    // $operationNumber = $usuario->obtenerOperationNumber();
    // // Registrar la transacción en el log
    // $logger = AccesoDatos::obtenerConexionDatos()->obtenerLogger();
    // $logger->logTransaction($userId, $operationNumber);
    $response->getBody()->write($retorno);
    return $response;
}


    public static function GET_traerTodos(Request $request, Response $response, array $args){
     
                $depositos = Deposito::obtenerTodos();
                $retorno = json_encode(array("Depositos"=>$depositos));
                 $response->getBody()->write($retorno);
                return $response;
    }


    public static function GET_traerUno(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['numero_deposito'])){
            $retorno = json_encode(array("mensaje" => "numero de cuenta necesario"));
        }
        else{
           
                $idNumeroDeposito = $param['numero_deposito'];
                $depositos = Deposito::traer_un_deposito_Id($idNumeroDeposito);
                $productosFiltrados = Deposito::filtrar_para_mostrar($depositos);
                $retorno = json_encode(array("ListadoDepositos"=>$productosFiltrados));
                // $retorno = json_encode(array("listado de depositos"=>$depositos));
            }
           
        $response->getBody()->write($retorno);
        return $response;
    }

    //ok visto
    public static function GET_totalDepositadoPorTipoCuentaYMonedaEnFecha(Request $request, Response $response, array $args)
{
    $param = $request->getQueryParams();

           // Obtener la fecha del parámetro o usar la fecha del día anterior si no se proporciona
            $fecha = isset($param['fecha']) ? $param['fecha'] : date('Y-m-d', strtotime("-1 days"));

            // Obtener el total depositado por tipo de cuenta y moneda en la fecha especificada
            $totalDepositado = Deposito::totalDepositadoPorTipoCuentaYMonedaEnFecha($fecha);

            $retorno = json_encode(["TotalDepositado" => $totalDepositado]);
      
    $response->getBody()->write($retorno);
    return $response;
}


//ok visto
    public static function GET_traer_depositos_entre_fechas_ordenados(Request $request, Response $response, array $args)
{
    $param = $request->getQueryParams();

            if (!isset($param['fecha_inicio']) || !isset($param['fecha_fin'])) {
                $retorno = json_encode(["mensaje" => "Fechas de inicio y fin necesarias"]);
            } else {
                $fechaInicio = $param['fecha_inicio'];
                $fechaFin = $param['fecha_fin'];

                $depositos = Deposito::traer_Depositos_EntreFechas_OrdenadosPorNombre($fechaInicio, $fechaFin);
              
                // Filtrar y devolver el resultado
                $depositosFiltrados = Deposito::filtrar_para_mostrar($depositos);
                $retorno = json_encode(["Depositos" => $depositosFiltrados]);
            }
   
    $response->getBody()->write($retorno);
    return $response;
}


//ok visto
    public static function GET_traer_depositos_por_usuario(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
    
                if (!isset($param['numero_cuenta'])) {
                    $retorno = json_encode(["mensaje" => "Número de cuenta necesario"]);
                } else {
                    $numeroCuenta = $param['numero_cuenta'];
    
                    // Verificar si la cuenta existe antes de realizar la consulta
                    $cuentaExistente = Cuenta::verificarExistenciaCuenta($numeroCuenta);
    
                    if ($cuentaExistente) {
                        $depositos = Deposito::traer_depositos_por_usuario($numeroCuenta);
    
                        // Filtrar y devolver el resultado
                        $depositosFiltrados = Deposito::filtrar_para_mostrar($depositos);
                        $retorno = json_encode(["Depositos" => $depositosFiltrados]);
                    } else {
                        $retorno = json_encode(["mensaje" => "La cuenta no existe"]);
                    }
                }
           
        $response->getBody()->write($retorno);
        return $response;
    }
    
    //ok visto
    public static function GET_traer_depositos_por_tipo_cuenta(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
    
      
                if (!isset($param['tipo_cuenta'])) {
                    $retorno = json_encode(["mensaje" => "Tipo de cuenta necesario"]);
                } else {
                    $tipoCuenta = strtoupper($param['tipo_cuenta']); 
                    if ($tipoCuenta === "CA" || $tipoCuenta === "CC") {
                        $depositos = Deposito::traer_depositos_por_tipo_cuenta($tipoCuenta);
    
                        // Filtrar y devolver el resultado
                        $depositosFiltrados = Deposito::filtrar_para_mostrar($depositos);
                        $retorno = json_encode(["Depositos" => $depositosFiltrados]);
                    } else {
                        $retorno = json_encode(["mensaje" => "Tipo de cuenta no válido. Debe ser 'CA' o 'CC'."]);
                    }
                }
      
    
        $response->getBody()->write($retorno);
        return $response;
    }
    

    //ok visto
    public static function GET_traer_depositos_por_tipo_moneda(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
    
      
                if (!isset($param['tipo_moneda'])) {
                    $retorno = json_encode(["mensaje" => "Tipo de moneda necesario"]);
                } else {
                    $tipoMoneda = strtoupper($param['tipo_moneda']); 
                    if ($tipoMoneda === "$" || $tipoMoneda === "USD") {
                        $depositos = Deposito::traer_depositos_por_tipo_moneda($tipoMoneda);
    
                        // Filtrar y devolver el resultado
                        $depositosFiltrados = Deposito::filtrar_para_mostrar($depositos);
                        $retorno = json_encode(["Depositos" => $depositosFiltrados]);
                    } else {
                        $retorno = json_encode(["mensaje" => "Tipo de moneda no válido. Debe ser '$' o 'USD'."]);
                    }
                }
           
        
    
        $response->getBody()->write($retorno);
        return $response;
    }
    


// public static function GET_traer_tipo_cuenta(Request $request, Response $response, array $args){
//     $param = $request->getQueryParams();
//     if(!isset($param['token'])){
//         $retorno = json_encode(array("mensaje" => "Token necesario"));
//     }
//     else{
//         $token = $param['token'];
//         $respuesta = Autenticador::validar_token($token, "Admin");
//         // $respuesta = Autenticador::validar_token($token, "Empleado");
//         if($respuesta == "Validado"){
//             $tipoCuenta = $param['tipoCuenta'];
//             $depositos = Deposito::traer_un_deposito_tipo_cuenta($tipoCuenta); 
//             $productosFiltrados = Deposito::filtrar_para_mostrar($depositos);
//             $retorno = json_encode(array("ListadoUsuarios"=>$productosFiltrados));
//             // $retorno = json_encode(array("listado de depositos"=>$depositos));
//         }
//         else{
//             $retorno = json_encode(array("mensaje" => $respuesta));
//         }
//     }
//     $response->getBody()->write($retorno);
//     return $response;
// }
public static function obtenerUserIdDesdeToken($token)
{
    try {
        // Verificar y decodificar el token
        $payload = AutentificadorJWT::ObtenerData($token);

        // Verificar si se obtuvo el ID del usuario desde el token
        if (isset($payload['email'])) {
            return $payload['email'];
        } else {
            return null; // No se encontró el ID del usuario en el payload del token
        }
    } catch (Exception $e) {
        // Manejar cualquier excepción que pueda ocurrir al decodificar el token
        return null;
    }
}


}


?>