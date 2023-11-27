<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Usuario;
use Autenticador;

require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/Autenticador.php';

class UsuariosController
{
    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }


    public static function POST_Login(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();

        $email = $parametros['email'];
        $contraseña = $parametros['password'];

        $usuarioEncontrado = null;
        $usuarioEncontrado = Usuario::traer_un_usuario_email($email);
        if($usuarioEncontrado != null){
            if($contraseña == $usuarioEncontrado->password){
                $token = Autenticador::definir_token($usuarioEncontrado->id, $email, $usuarioEncontrado->tipo);
                $retorno = json_encode(array("mensaje" => "OK!", "perfil de usuario"=>$usuarioEncontrado->tipo,"token" => $token));
            }
            else{
                $retorno = json_encode(array("mensaje" => "Contrasenia incorrecta"));
            }
        }
        else{
            $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }

    public static function POST_InsertarUsuario(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();
        $tipo = $parametros['tipo'];
        $email = $parametros['email'];
        $contraseña = $parametros['password'];
        $user = new Usuario($email, $tipo, $contraseña);
        $ok = $user->insertar_usuario();
        if($ok != null){
            $retorno = json_encode(array("mensaje" => "Usuario creado con exito"));
        }
        else{
            $retorno = json_encode(array("mensaje" => "No se pudo crear"));
        }  
        $response->getBody()->write($retorno);
        return $response;
    }

    public static function GET_TraerTodos(Request $request, Response $response, array $args){
        $usuarios = Usuario::traer_todo_los_usuarios();
        $usuariosFiltrados = Usuario::filtrar_para_mostrar($usuarios);
        $retorno = json_encode(array("ListadoUsuarios"=>$usuariosFiltrados));
        
        $response->getBody()->write($retorno);
        return $response;
    }

    public static function GET_TraerUno(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
                
        if (!isset($param['token'])) {
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        } elseif (!isset($param['id_usuario'])) {
           $retorno = json_encode(array("mensaje" => "Se requiere el ID del usuario"));
        } else {
            $token = $param['token'];
            $respuesta = Autenticador::validar_token($token, "Admin");
            if ($respuesta == "Validado") {
                $idUsuario = $param['id_usuario'];
                $usuarios = Usuario::traer_un_usuario_Id($idUsuario);           
                if ($usuarios !== null) {
                    $usuariosFiltrados = Usuario::filtrar_para_mostrar($usuarios);
                    $retorno = json_encode(array("ListadoUsuarios" => $usuariosFiltrados));
                } else {
                    $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
                }
            } else {
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
   

   


    // public static function POST_Login(Request $request, Response $response, array $args){
    //     $parametros = $request->getParsedBody();

    //     $email = $parametros['email'];
    //     $contraseña = $parametros['contraseña'];

    //     $usuarioEncontrado = null;
    //     $usuarioEncontrado = Usuario::traer_un_usuario_email($email);

    //     if($usuarioEncontrado != null){
    //         if($contraseña == $usuarioEncontrado->password){
    //             $token = Autenticador::definir_token($usuarioEncontrado->id, $email);

    //             $data = array(
    //                 "token" => $token
    //             );
    //             $usuarioEncontrado->modificar_token_DB($data);
    //             // $retorno = json_encode(array("mensaje" => "Proceso exitoso"));
    //             $retorno = json_encode(array("mensaje" => "Proceso exitoso", "token" => $token));
    //         }
    //         else{
    //             $retorno = json_encode(array("mensaje" => "Contraseña incorrecta"));
    //         }
    //     }
    //     else{
    //         $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
    //     }
    //     $response->getBody()->write($retorno);
    //     return $response;
    // }
}
























?>