<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once '../src/Clases/Usuario.php';

class Autenticador
{
    private static $claveSecreta = "gustavoLeiva";
    private static $tipoEncriptacion = "HS256";

   
    public static function definir_token($id, $email, $tipo){
        $time = time();
        $payload = array(
         
            "iat" => $time, //Tiempo en que inicia el token
            "exp" => $time + (60*60*24*30), //Tiempo de expiracion del token (10 dia)
            "data" => [
                "id" => $id,
                "email" => $email,
                "tipo" => $tipo
            ]
        );
        $token = JWT::encode($payload, self::$claveSecreta, self::$tipoEncriptacion);
        return $token;
    }
    
    public static function validar_token($token, $tipo){
        $resp = "No autorizado";
        try {
            $decodificado = JWT::decode(
                $token,
            new Key(self::$claveSecreta, self::$tipoEncriptacion)
            );
        if($decodificado->data->tipo == $tipo){
            $resp =  "Validado";
        }
     } catch (Exception $e) {
        switch($e->getMessage()){
            case "Expired token":
            $resp = "Sesion expirada"; 
            break;
            case "Signature verification failed":
                $resp = "Token invalido";
                break;
        }
        die(json_encode(array("mensaje" => $resp)));
    }
    return $resp;
}

public static function traer_tipo_desde_token($token){
    $decodificado = JWT::decode(
        $token,
    new Key(self::$claveSecreta, self::$tipoEncriptacion)
    );
    $resp = $decodificado->data->tipo;
    return $resp;
}
public static function traer_email_desde_token($token){
    $decodificado = JWT::decode(
        $token,
    new Key(self::$claveSecreta, self::$tipoEncriptacion)
    );
    $resp = $decodificado->data->email;
    return $resp;
}

public static function traer_Id_desde_token($token){
    $decodificado = JWT::decode(
        $token,
    new Key(self::$claveSecreta, self::$tipoEncriptacion)
    );
    $resp = $decodificado->data->id;
    return $resp;
}

}

?>