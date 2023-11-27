<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Retiro;
use Usuario;
use Cuenta;
use Autenticador;

require_once '../src/Clases/Cuenta.php';
require_once '../src/Clases/Retiro.php';
require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/Autenticador.php';

class RetirosController
{
    public static $tipoCuentas = array("CA", "CC");
    public static $tipoMoneda = array("$", "USD");

    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }

    // public static function POST_insertarRetiro(Request $request, Response $response, array $args){
    //     $param = $request->getQueryParams();
    //     $retorno = null; // Definir $retorno inicialmente como null
    
    //     if (!isset($param['token'])) {
    //         $retorno = json_encode(array("mensaje" => "Token necesario"));
    //     } else {
    //         $token = $param['token'];
    //         $respuesta = Autenticador::validar_token($token, "Admin");
    
    //         if ($respuesta == "Validado") {
    //             $parametros = $request->getParsedBody();
    //             $numeroCuenta = $parametros['numeroCuenta'];
    //             $tipoCuenta = $parametros['tipoCuenta'];
    //             $moneda = $parametros['moneda'];
    //             $importeRetirar = $parametros['importeRetirar'];
    
    //             // Validar el tipo de cuenta y la moneda
    //             if (in_array($tipoCuenta, self::$tipoCuentas) && in_array($moneda, self::$tipoMoneda)) {
    //                 $cuenta = new Retiro($numeroCuenta, $tipoCuenta, $moneda, $importeRetirar);
    //                 $ok = $cuenta->insertarRetiro();
    
    //                 // var_dump($ok);
    //                 if ($ok != null) {
    //                     $retorno = json_encode(array("mensaje" => "Retiro realizado con exito"));
    //                 } else {
    //                     $retorno = json_encode(array("mensaje" => "No se pudo realizar el retiro"));
    //                 }
    //             } else {
    //                 $mensajeError = "Retiro no valido. ";
    
    //                 if (!in_array($tipoCuenta, self::$tipoCuentas)) {
    //                     $mensajeError .= "Tipo de cuenta no es valido. Debe ser 'CA' o 'CC'. ";
    //                 }
    
    //                 if (!in_array($moneda, self::$tipoMoneda)) {
    //                     $mensajeError .= "Moneda no es valida. Debe ser '$' o 'USD'.";
    //                 }
    
    //                 $retorno = json_encode(array("mensaje" => $mensajeError));
    //             }
    //         } else {
    //             $retorno = json_encode(array("mensaje" => $respuesta));
    //         }
    //     }
    
    //     $response->getBody()->write($retorno);
    //     return $response;
    // }


    public static function POST_insertarRetiro(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
        $retorno = null; // Definir $retorno inicialmente como null

        if (!isset($param['token'])) {
            $retorno = json_encode(["mensaje" => "Token necesario"]);
        } else {
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");

            if ($respuesta == "Validado") {
                $parametros = $request->getParsedBody();
                $numeroCuenta = $parametros['numeroCuenta'];
                $tipoCuenta = $parametros['tipoCuenta'];
                $moneda = $parametros['moneda'];
                $nombre = $parametros['nombre'];
                $importeRetirar = $parametros['importeRetirar'];

                // Validar el tipo de cuenta y la moneda
                if (in_array($tipoCuenta, self::$tipoCuentas) && in_array($moneda, self::$tipoMoneda)) {
                    $cuenta = new Retiro($numeroCuenta, $tipoCuenta, $moneda,$nombre, $importeRetirar);
                    $respuesta = $cuenta->insertarRetiro();

                    // Analizar la respuesta del método insertarRetiro
                    $respuestaArray = json_decode($respuesta, true);

                    if (isset($respuestaArray["mensaje"])) {
                        // Verificar el mensaje y tomar acciones según el resultado
                        if ($respuestaArray["mensaje"] == "Retiro realizado con exito") {
                            $retorno = json_encode(["mensaje" => "Operacion exitosa"]);
                        } elseif ($respuestaArray["mensaje"] == "El importe a retirar es mayor a su saldo actual") {
                            $retorno = json_encode(["mensaje" => "Fondos insuficientes"]);
                        } elseif ($respuestaArray["mensaje"] == "El numero de cuenta no existe") {
                            $retorno = json_encode(["mensaje" => "Numero de cuenta invalido"]);
                        } else {
                            $retorno = json_encode(["mensaje" => "Error desconocido"]);
                        }
                    } else {
                        $retorno = json_encode(["mensaje" => "Error en la respuesta"]);
                    }
                } else {
                    $mensajeError = "Retiro no valido. ";

                    if (!in_array($tipoCuenta, self::$tipoCuentas)) {
                        $mensajeError .= "Tipo de cuenta no es valido. Debe ser 'CA' o 'CC'. ";
                    }

                    if (!in_array($moneda, self::$tipoMoneda)) {
                        $mensajeError .= "Moneda no es valida. Debe ser '$' o 'USD'.";
                    }

                    $retorno = json_encode(["mensaje" => $mensajeError]);
                }
            } else {
                $retorno = json_encode(["mensaje" => $respuesta]);
            }
        }

        $response->getBody()->write($retorno);
        return $response;
    }
    

  



    public static function GET_traerTodos(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            if($respuesta == "Validado"){
                $retiros = Retiro::obtenerTodos();
                $retorno = json_encode(array("Retiros"=>$retiros));
            }
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        } 
        $response->getBody()->write($retorno);
        return $response;
    }



    public static function GET_totalRetiradoPorTipoCuentaYMonedaEnFecha(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
    
        if (!isset($param['token'])) {
            $retorno = json_encode(["mensaje" => "Token necesario"]);
        } else {
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
    
            if ($respuesta == "Validado") {
                // Obtener la fecha del parámetro o usar la fecha del día anterior si no se proporciona
                $fecha = isset($param['fecha']) ? $param['fecha'] : date('Y-m-d', strtotime("-1 days"));
    
                // Obtener el total depositado por tipo de cuenta y moneda en la fecha especificada
                $totalRetirado = Retiro::totalRetiradoPorTipoCuentaYMonedaEnFecha($fecha);
    
                $retorno = json_encode(["TotalRetirado" => $totalRetirado]);
            } else {
                $retorno = json_encode(["mensaje" => $respuesta]);
            }
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }

    public static function GET_traer_retiros_por_usuario(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
    
        if (!isset($param['token'])) {
            $retorno = json_encode(["mensaje" => "Token necesario"]);
        } else {
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
    
            if ($respuesta == "Validado") {
                if (!isset($param['numero_cuenta'])) {
                    $retorno = json_encode(["mensaje" => "Número de cuenta necesario"]);
                } else {
                    $numeroCuenta = $param['numero_cuenta'];
    
                    // Verificar si la cuenta existe antes de realizar la consulta
                    $cuentaExistente = Cuenta::verificarExistenciaCuenta($numeroCuenta);
    
                    if ($cuentaExistente) {
                        $retiros = Retiro::traer_retiros_por_usuario($numeroCuenta);
    
                        // Filtrar y devolver el resultado
                        $depositosFiltrados = Retiro::filtrar_para_mostrar($retiros);
                        $retorno = json_encode(["Retiros" => $depositosFiltrados]);
                    } else {
                        $retorno = json_encode(["mensaje" => "La cuenta no existe"]);
                    }
                }
            } else {
                $retorno = json_encode(["mensaje" => $respuesta]);
            }
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }

    public static function GET_traer_retiros_entre_fechas_ordenados(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
    
        if (!isset($param['token'])) {
            $retorno = json_encode(["mensaje" => "Token necesario"]);
        } else {
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
    
            if ($respuesta == "Validado") {
                if (!isset($param['fecha_inicio']) || !isset($param['fecha_fin'])) {
                    $retorno = json_encode(["mensaje" => "Fechas de inicio y fin necesarias"]);
                } else {
                    $fechaInicio = $param['fecha_inicio'];
                    $fechaFin = $param['fecha_fin'];
    
                    $retiros = Retiro::traer_Retiros_EntreFechas_OrdenadosPorNombre($fechaInicio, $fechaFin);
                  
                    // Filtrar y devolver el resultado
                    $retirosFiltrados = Retiro::filtrar_para_mostrar($retiros);
                    $retorno = json_encode(["Retiros" => $retirosFiltrados]);
                }
            } else {
                $retorno = json_encode(["mensaje" => $respuesta]);
            }
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }
    

    public static function GET_traer_retiros_por_tipo_cuenta(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
    
        if (!isset($param['token'])) {
            $retorno = json_encode(["mensaje" => "Token necesario"]);
        } else {
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
    
            if ($respuesta == "Validado") {
                if (!isset($param['tipo_cuenta'])) {
                    $retorno = json_encode(["mensaje" => "Tipo de cuenta necesario"]);
                } else {
                    $tipoCuenta = strtoupper($param['tipo_cuenta']); 
                    if ($tipoCuenta === "CA" || $tipoCuenta === "CC") {
                        $retiros = Retiro::traer_retiros_por_tipo_cuenta($tipoCuenta);
    
                        // Filtrar y devolver el resultado
                        $retirosFiltrados = Retiro::filtrar_para_mostrar($retiros);
                        $retorno = json_encode(["Depositos" => $retirosFiltrados]);
                    } else {
                        $retorno = json_encode(["mensaje" => "Tipo de cuenta no válido. Debe ser 'CA' o 'CC'."]);
                    }
                }
            } else {
                $retorno = json_encode(["mensaje" => $respuesta]);
            }
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }
    


    public static function GET_traer_retiros_por_tipo_moneda(Request $request, Response $response, array $args)
    {
        $param = $request->getQueryParams();
    
        if (!isset($param['token'])) {
            $retorno = json_encode(["mensaje" => "Token necesario"]);
        } else {
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
    
            if ($respuesta == "Validado") {
                if (!isset($param['tipo_moneda'])) {
                    $retorno = json_encode(["mensaje" => "Tipo de moneda necesario"]);
                } else {
                    $tipoMoneda = strtoupper($param['tipo_moneda']); 
                    if ($tipoMoneda === "$" || $tipoMoneda === "USD") {
                        $retiros = Retiro::traer_retiros_por_tipo_moneda($tipoMoneda);
    
                        // Filtrar y devolver el resultado
                        $retirosFiltrados = Retiro::filtrar_para_mostrar($retiros);
                        $retorno = json_encode(["Retiros" => $retirosFiltrados]);
                    } else {
                        $retorno = json_encode(["mensaje" => "Tipo de moneda no válido. Debe ser '$' o 'USD'."]);
                    }
                }
            } else {
                $retorno = json_encode(["mensaje" => $respuesta]);
            }
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }







}


?>