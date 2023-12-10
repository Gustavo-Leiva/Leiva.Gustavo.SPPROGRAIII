<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once '../src/Clases/Usuario.php';

class AutentificadorJWT
{
    private static $claveSecreta = "gustavoLeiva";
    private static $tipoEncriptacion = "HS256";

    public static function CrearToken($data){
        $time = time();
        $payload = array(
         
            "iat" => $time, //Tiempo en que inicia el token
            "exp" => $time + (60*60*24*30), // Validez del token por 30 días
            'aud' => self::Aud(),
            'data' => $data,


                  
        );
        $token = JWT::encode($payload, self::$claveSecreta, self::$tipoEncriptacion);
        return $token;

        
    }


    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode($token, new Key(self::$claveSecreta, self::$tipoEncriptacion)
            );
        } catch (Exception $e) {
            throw $e;
        }
        //hace esto para que el token puede ser usado por quien lo genero. Es una verificacion extra. 
        if ($decodificado->aud !== self::Aud()) {//se fija si el que esta usando el token es quien lo genero. Esta para se pude sacar igual
            throw new Exception("No es el usuario valido");
        }

        // if (property_exists($decodificado, 'aud') && $decodificado->aud !== self::Aud()) {
        //     throw new Exception("No es el usuario válido");
        // }

      
    }
    


    public static function ObtenerPayLoad($token)//obtiene el payload del token
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            new Key(self::$claveSecreta, self::$tipoEncriptacion)
        );
    }


    public static function ObtenerData($token)//obtiene toda la data del token
    {
        return JWT::decode(
            $token,
            new Key(self::$claveSecreta, self::$tipoEncriptacion)
        )->data;
    }


    private static function Aud()//agarra datos del cliente y hace un hash unico
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

}

?>