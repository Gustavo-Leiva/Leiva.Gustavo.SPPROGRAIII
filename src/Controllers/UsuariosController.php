<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Usuario;
use AutentificadorJWT;
use AccesoDatos;
use exception;





require_once '../src/Clases/Usuario.php';
require_once '../src/AccesoDatos.php';
require_once '../src/Clases/Logger.php';
require_once '../src/Clases/AutentificadorJWT.php';


class UsuariosController
{
    
    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }


    //ok visto
    public static function POST_Login(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();

        $email = $parametros['email'];
        $contraseña = $parametros['password'];
        $tipo =$parametros['tipo'];

        $usuarioEncontrado = Usuario::traer_un_usuario_email($email);

        if($usuarioEncontrado != null){
            if($contraseña == $usuarioEncontrado->password){
                $id = $usuarioEncontrado->id;
                $tipo = $usuarioEncontrado->tipo;
                $data['id']=$id;
                $data['tipo']=$tipo;
                $token = AutentificadorJWT::CrearToken($data);

                $data = array(
                    "token" => $token
                );
                $usuarioEncontrado->modificar_token_DB($data);
                // $retorno = json_encode(array("mensaje" => "Proceso exitoso"));
                $retorno = json_encode(array("mensaje" => "Proceso exitoso", "token" => $token));
            }
            else{
                $retorno = json_encode(array("mensaje" => "Contraseña incorrecta"));
            }
        }
        else{
            $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
        }

        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $logger = $objetoAccesoDato->obtenerLogger();
        $logger->logAccess('GET', '/usuarios/Login', '127.0.0.1', 'Éxito');
        $response->getBody()->write($retorno);
        return $response;

    
    }


     
    //ok visto
    public static function GET_TraerTodos(Request $request, Response $response, array $args)
    {
        $token = $request->getHeaderLine('Authorization');
        $userId = self::obtenerUserIdDesdeToken($token);

        $usuarios = Usuario::traer_todos_los_usuarios();
        
        if($usuarios != null){
            $usuariosFiltrados = Usuario::filtrar_para_mostrar($usuarios);
            $retorno = json_encode(array("ListadoUsuarios" => $usuariosFiltrados));
            
            // El ID del usuario se obtuvo correctamente, ahora puedes utilizarlo
            // para registrar la transacción en el log, etc.
            $usuario =Usuario::obtenerInstanciaVacia(); // Instanciar un objeto Usuario sin proporcionar argumentos
            $operationNumber = $usuario->obtenerOperationNumber();

            // Registrar la transacción en el log
            $logger = AccesoDatos::obtenerConexionDatos()->obtenerLogger();
            $logger->logTransaction($userId, $operationNumber);

        } else {
            $retorno = json_encode(array("mensaje" => "Error al obtener usuarios"));
        }

    
       

        $response->getBody()->write($retorno);
        return $response;
    }
    


    //ok visto
    public static function GET_TraerUno(Request $request, Response $response, array $args)
{
    $token = $request->getHeaderLine('Authorization');
        $userId = self::obtenerUserIdDesdeToken($token);
    $param = $request->getQueryParams();

    $idUsuario = $param['id_usuario'];
    $usuarios = Usuario::traer_un_usuarioId($idUsuario);

    if ($usuarios !== null) {
        $usuariosFiltrados = Usuario::filtrar_para_mostrar($usuarios);
        $retorno = json_encode(array("ListadoUsuarios" => $usuariosFiltrados));
        $usuario =Usuario::obtenerInstanciaVacia(); // Instanciar un objeto Usuario sin proporcionar argumentos
        $operationNumber = $usuario->obtenerOperationNumber();

        // Registrar la transacción en el log
        $logger = AccesoDatos::obtenerConexionDatos()->obtenerLogger();
        $logger->logTransaction($userId, $operationNumber);

    } else {
        $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
    }

    $response->getBody()->write($retorno);
    return $response;
}


   public static function GET_GuardarEnCSV(Request $request, Response $response, array $args){
    $token = $request->getHeaderLine('Authorization');
    $userId = self::obtenerUserIdDesdeToken($token);
       $path = "Usuarios.csv";
        
        $usuarios = Usuario::traer_todos_los_usuarios_EnArray();
        $archivo = fopen($path, "w");
        $encabezado = array("id", "email", "tipo", "password");
        fputcsv($archivo, $encabezado);
        foreach($usuarios as $fila){
            fputcsv($archivo, $fila);
        }
        fclose($archivo);
        $retorno = json_encode(array("mensaje"=>"Usuarios guardados en CSV con exito"));
    
        $usuario =Usuario::obtenerInstanciaVacia(); // Instanciar un objeto Usuario sin proporcionar argumentos
        $operationNumber = $usuario->obtenerOperationNumber();

        // Registrar la transacción en el log
        $logger = AccesoDatos::obtenerConexionDatos()->obtenerLogger();
        $logger->logTransaction($userId, $operationNumber);
   
    $response->getBody()->write($retorno);
    return $response;
    }
    
    public static function GET_CargarUsuariosCSV(Request $request, Response $response, array $args){
      
        
        $path = "Usuarios.csv";
      
        $archivo = fopen($path, "r");
        $encabezado = fgets($archivo);
    
        while(!feof($archivo)){
            $linea = fgets($archivo);
            $datos = str_getcsv($linea);
            if(isset($datos[1])){
                $usuario = new Usuario($datos[1], $datos[2], $datos[3],$datos[6],$datos[7],$datos[4],$datos[5],$datos[8],$datos[0]);
                $usuario->insertarUsuario();
            }
        }
        fclose($archivo);
        
        $retorno = json_encode(array("mensaje"=>"Usuarios guardados en base de datos con exito"));
      
        $response->getBody()->write($retorno);
        return $response;
    }
    

    //ok visto
    public static function POST_InsertarUsuario(Request $request, Response $response, array $args) {
        $token = $request->getHeaderLine('Authorization');
        $userId = self::obtenerUserIdDesdeToken($token);
        
        $parametros = $request->getParsedBody();
        $tipo = $parametros['tipo'];
        $email = $parametros['email'];
        $contraseña = $parametros['password'];
        $user = new Usuario($email, $tipo, $contraseña);
        $ok = $user->insertar_usuario();
        if($ok != null){
            $retorno = json_encode(array("mensaje" => "Usuario creado con exito"));
            
            $usuario =Usuario::obtenerInstanciaVacia(); // Instanciar un objeto Usuario sin proporcionar argumentos
            $operationNumber = $usuario->obtenerOperationNumber();
            // Registrar la transacción en el log
            $logger = AccesoDatos::obtenerConexionDatos()->obtenerLogger();
            $logger->logTransaction($userId, $operationNumber);
        }
        else{
            $retorno = json_encode(array("mensaje" => "No se pudo crear"));
        }  
        $response->getBody()->write($retorno);
        return $response;
    }


    public static function obtenerUserIdDesdeToken($token)
    {
        try {
            // Verificar y decodificar el token
            $payload = AutentificadorJWT::ObtenerData($token);
    
            // Verificar si se obtuvo el ID del usuario desde el token
            if (isset($payload['id'])) {
                return $payload['id'];
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