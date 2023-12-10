<?php
namespace src\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Cuenta;
use Autenticador;
use AccesoDatos;

require_once '../src/Clases/Cuenta.php';
require_once '../src/Clases/AutentificadorJWT.php';
require_once '../src/AccesoDatos.php';

class CuentasController
{
    public static $tipoCuentas = array("CA", "CC");
    public static $tipoMoneda = array("$", "USD");

    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }


    //ok visto
     public static function POST_Login(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();

        $email = $parametros['email'];
        $contraseña = $parametros['contraseña'];

        $usuarioEncontrado = null;
        $usuarioEncontrado = Cuenta::traer_un_cuenta_email($email);

        if($usuarioEncontrado != null){
            if($contraseña == $usuarioEncontrado->password){
                $token = Autenticador::definir_token($usuarioEncontrado->id, $email);

                $data = array(
                    "token" => $token
                );
                $usuarioEncontrado->modificar_token_DB($data);
                $retorno = json_encode(array("mensaje" => "Proceso exitoso", "token" => $token));
            
            }
            else{
                $retorno = json_encode(array("mensaje" => "Contraseña incorrecta"));
            }
        }
        else{
            $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }

    //ok visto
    public static function POST_InsertarCuenta(Request $request, Response $response, array $args){
       
        $rutaImagenCuenta = 'C:\xampp\htdocs\CuentasBancariasII\src\Controllers\Imagen\foto_del_usuario\cliente\imagenesDeCuenta\2023';

                     
                $parametros = $request->getParsedBody();
                $nombre = $parametros['nombre'];
                $apellido = $parametros['apellido'];
                $tipoDni = $parametros['tipoDni'];
                $documento = $parametros['documento'];
                $email = $parametros['email'];
                $tipoCuenta = $parametros['tipoCuenta'];
                $moneda = $parametros['moneda'];
                $saldo = $parametros['saldo'];
                $role = $parametros['role'];
        
                $user = new Cuenta($nombre, $apellido, $tipoDni,$documento,$email,$tipoCuenta,$moneda,$role,$saldo);
                $ok = $user->insertarCuenta();

                // Después de insertar la cuenta, obtener el último ID insertado
                $lastInsertId = AccesoDatos::obtenerConexionDatos()->retornarUltimoIdInsertado();
                
                // Asignar el ID a la instancia de Cuenta
                $user->id = $lastInsertId;

                if(isset($_FILES['imagen'])){
                    $imagen = $_FILES['imagen'];
                    $destino = $user->definir_destino_imagen($rutaImagenCuenta);
                    move_uploaded_file($imagen['tmp_name'], $destino);
                }
                if($ok != null){
                    $retorno = json_encode(array("mensaje" => "Cuenta creada con exito"));
                    
                }
                else{
                    $retorno = json_encode(array("mensaje" => "No se pudo crear la cuenta"));
                }           
                
        $response->getBody()->write($retorno);
        return $response;
    }

  


    public static function GET_TraerTodos(Request $request, Response $response, array $args){
                $cuentas = Cuenta::traer_todas_las_cuentas();
                $cuentasFiltradas = Cuenta::filtrar_para_mostrar($cuentas);
                $retorno = json_encode(array("ListadoCuentas"=>$cuentasFiltradas));
                $response->getBody()->write($retorno);
        return $response;
    }


    //ok visto
    public static function GET_TraerUno(Request $request, Response $response, array $args)
    {
        $parametros = $request->getQueryParams();
    
        if (!isset($parametros['tipoCuenta']) || !isset($parametros['nro_cuenta']) || !isset($parametros['tipoMoneda'])) {
            $retorno = json_encode(array("mensaje" => "Se requieren el tipo de cuenta, el número de cuenta y el tipo de moneda"));
        } else {
            $tipoCuenta = $parametros['tipoCuenta'];
            $nroCuenta = $parametros['nro_cuenta'];
            $tipoMoneda = $parametros['tipoMoneda'];
    
            // Validación del tipo de cuenta
            if (!in_array($tipoCuenta, self::$tipoCuentas)) {
                $retorno = json_encode(array("mensaje" => "Tipo de cuenta no es valido. Debe ser 'CA' o 'CC'. "));
            } else {
                // Validación del tipo de moneda
                if (!in_array($tipoMoneda, self::$tipoMoneda)) {
                    $retorno = json_encode(array("mensaje" => "Moneda no es valida. Debe ser '$' o 'USD'."));
                } else {
                    $resultado = Cuenta::consultarCuenta($tipoCuenta, $nroCuenta, $tipoMoneda);
                    $retorno = $resultado;
                }
            }
        }
    
        $response->getBody()->write($retorno);
        return $response;
    }
    



    

    //ok visto
    public function POST_modificarCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        // $parametros = json_decode($request->getBody()->getContents(), true);
    
        $id = $parametros['id'];
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $tipoDni = $parametros['tipoDni'];
        $documento = $parametros['documento'];
        $email = $parametros['email'];
        $tipoCuenta = $parametros['tipoCuenta'];
        $moneda = $parametros['moneda'];
        $rol = $parametros['rol'];
        $saldo = $parametros['saldo'];
    
        // Pasa los valores al constructor de Cuenta
        $cuenta = new Cuenta(
            $nombre,
            $apellido,
            $tipoDni,
            $documento,
            $email,
            $tipoCuenta,
            $moneda,
            $rol,
            $saldo,
            null, // No necesitas establecer password aquí
            null, // No necesitas establecer fechaRegistro aquí
            null, // No necesitas establecer fechaBaja aquí
            $id
        );
    
        $resultado = $cuenta->modificarCuenta();
    
        $payload = json_encode($resultado);
    
        $response->getBody()->write($payload);
        return $response;
    }
    
   





    public function POST_borrarCuenta($request, $response, $args)
    {
        // Obtén los datos del cuerpo de la solicitud
        $parametros = $request->getParsedBody();
    
        // Verifica si 'id' está presente en los parámetros
        $id = isset($parametros['id']) ? $parametros['id'] : null;
    
        if ($id !== null) {
            // Crea la instancia de Cuenta y establece el ID
            $cuenta = new Cuenta();
            $cuenta->id = $id;
    
            // Realiza la operación para dar de baja la cuenta
            $resultado = $cuenta->darDeBajaCuenta();
    
            // Devuelve el resultado en formato JSON
            $payload = json_encode($resultado);
    
            // Escribe el resultado en el cuerpo de la respuesta
            $response->getBody()->write($payload);
        } else {
            // Manejar el caso en el que 'id' no está presente en los parámetros
            $resultado = ["mensaje" => "La clave 'id' no está presente en los parámetros"];
            $payload = json_encode($resultado);
            $response->getBody()->write($payload);
        }
    
        return $response ->withHeader('Content-Type', 'application/json');
    }
    
   
    //no reconoce el postman
    public function DELETE_borrarCuenta($request, $response, $args)
    {
        // Obtén los datos del cuerpo de la solicitud
        $parametros = $request->getParsedBody();
    
        // Verifica si 'id' está presente en los parámetros
        $id = isset($parametros['id']) ? $parametros['id'] : null;
    
        if ($id !== null) {
            // Crea la instancia de Cuenta y establece el ID
            $cuenta = new Cuenta();
            $cuenta->id = $id;
    
            // Realiza la operación para dar de baja la cuenta
            $resultado = $cuenta->darDeBajaCuenta();
    
            // Devuelve el resultado en formato JSON
            $payload = json_encode($resultado);
    
            // Escribe el resultado en el cuerpo de la respuesta
            $response->getBody()->write($payload);
        } else {
            // Manejar el caso en el que 'id' no está presente en los parámetros
            $resultado = ["mensaje" => "La clave 'id' no está presente en los parámetros"];
            $payload = json_encode($resultado);
            $response->getBody()->write($payload);
        }
    
        return $response ->withHeader('Content-Type', 'application/json');
    }
    
    
    
    






//probar
public function POST_realizarAjuste($request, $response, $args)
{
    // Obtén los datos del cuerpo de la solicitud
    $parametros = $request->getParsedBody();

    // Verifica si los parámetros necesarios están presentes
    $tipoOperacion = $parametros['tipo_operacion'] ?? null;
    $idOperacionAfectada = $parametros['id_operacion_afectada'] ?? null;
    $motivo = $parametros['motivo'] ?? null;
    $montoAjuste = $parametros['monto_ajuste'] ?? null;
    $tipoAjuste = $parametros['tipo_ajuste'] ?? null;
    $idCuenta = $parametros['id'] ?? null;
    var_dump($idCuenta);

    if (
        $tipoOperacion !== null &&
        $idOperacionAfectada !== null &&
        $motivo !== null &&
        $montoAjuste !== null &&
        $tipoAjuste !== null&&
        $idCuenta !==null
    ) {
        // Crea la instancia de Cuenta y realiza el ajuste
        $cuenta = new Cuenta();
        $cuenta->id = $idCuenta;
        $resultado = $cuenta->realizarAjuste($tipoOperacion, $idOperacionAfectada, $motivo, $montoAjuste, $tipoAjuste);
     

        // Devuelve el resultado en formato JSON
        $payload = json_encode($resultado);

        // Escribe el resultado en el cuerpo de la respuesta
        $response->getBody()->write($payload);
    } else {
        // Maneja el caso en el que algún parámetro está ausente
        $resultado = ["mensaje" => "Faltan parámetros requeridos"];
        $payload = json_encode($resultado);
        $response->getBody()->write($payload);
    }

    return $response;


}






public function GET_operacionesCuenta($request, $response, $args)
{
    $idCuenta = $request->getQueryParams()['id']; // Obtén el ID de la cuenta desde los parámetros de la URL

    $cuenta = new Cuenta();
    $cuenta->id = $idCuenta;  // Asignar el ID de la cuenta

    // var_dump($idCuenta);
    $operaciones = $cuenta->obtenerOperacionesCuenta();
    // var_dump($operaciones);

    $payload = json_encode($operaciones);
    $response->getBody()->write($payload);

    return $response;
}






public static function GET_GuardarEnCSV(Request $request, Response $response, array $args){
    $path = "Usuarios.csv";
    $param = $request->getQueryParams();
    if(!isset($param['token'])){
        $retorno = json_encode(array("mensaje" => "Token necesario"));
    }
    else{
        $token = $param['token'];
        $respuesta = Autenticador::validar_token($token, "Admin");
        if($respuesta == "Validado"){
            $usuarios = Cuenta::traer_todas_las_cuentas_EnArray();
            $archivo = fopen($path, "w");
            $encabezado = array("id", "nombre", "apellido", "tipo", "sub_tipo", "sector", "email", "password", "fecha_registro");
            fputcsv($archivo, $encabezado);
            foreach($usuarios as $fila){
                fputcsv($archivo, $fila);
            }
            fclose($archivo);
            $retorno = json_encode(array("mensaje"=>"Usuarios guardados en CSV con exito"));
        }
        else{
            $retorno = json_encode(array("mensaje" => $respuesta));
        }
    }
    $response->getBody()->write($retorno);
    return $response;
}
public static function GET_CargarUsuariosCSV(Request $request, Response $response, array $args){
    $path = "Usuarios.csv";
    $param = $request->getQueryParams();
    if(!isset($param['token'])){
        $retorno = json_encode(array("mensaje" => "Token necesario"));
    }
    else{
        $token = $param['token'];
        $respuesta = Autenticador::validar_token($token, "Admin");
        if($respuesta == "Validado"){
            $archivo = fopen($path, "r");
            $encabezado = fgets($archivo);

            while(!feof($archivo)){
                $linea = fgets($archivo);
                $datos = str_getcsv($linea);
                if(isset($datos[1])){
                    $usuario = new Cuenta($datos[1], $datos[2], $datos[3],$datos[6],$datos[7],$datos[4],$datos[5],$datos[8],$datos[0]);
                    $usuario->insertarCuenta();
                }
            }
            fclose($archivo);
            
            $retorno = json_encode(array("mensaje"=>"Usuarios guardados en base de datos con exito"));
        }
        else{
            $retorno = json_encode(array("mensaje" => $respuesta));
        }
    }
    $response->getBody()->write($retorno);
    return $response;
}


}

























?>