
<?php

class Usuario
{
    //ok visto
    public $id;
    public $email;
    public $tipo;
    public $password;
    
    

    //ok visto
    public function __construct( $email, $tipo, $password, $id = null)
    {
        $this->email = $email;
        $this->tipo = $tipo;
        $this->password = $password;
        if($id != null){
            $this->id = $id;
        }
    }

    public static function obtenerInstanciaVacia()
    {
        return new self('', '', ''); // Llamando al constructor con valores por defecto
    }



    public function obtenerOperationNumber()
    {
        return mt_rand(100000, 999999); // Número aleatorio de 6 dígitos
    }





    public function obtenerUserId()
    {
        // Ejemplo: si usas un token JWT para autenticación
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $payload = AutentificadorJWT::ObtenerData($token);
        
        return $payload['id'] ?? null;
    }

    //ok visto
    public function insertar_usuario()
	{
		$objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
		$consulta =$objetoAccesoDato->retornarConsulta("insert into usuarios (email, tipo, password)values('$this->email','$this->tipo','$this->password')");
		$consulta->execute();
		return $objetoAccesoDato->retornarUltimoIdInsertado();
	}
    
  
    

    //no revisado
    public static function traer_todos_los_usuarios_EnArray()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT id as id, email as email, tipo as tipo, password as password from usuarios");
        $consulta->execute();
        $usuarios = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $usuario = array($i->id, $i->email, $i->tipo, $i->password);
            $usuarios[] = $usuario;
        }
        return $usuarios;
	}

    //ok visto
    public static function traer_todos_los_usuarios()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $consulta =$objetoAccesoDato->retornarConsulta("select id as id, email as email, tipo as tipo, password as password,token as token from usuarios");
        $consulta->execute();
        $arrayObtenido = array();
        $usuarios = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $usuario = new Usuario($i->email, $i->tipo, $i->password, $i->id);
            $usuarios[] = $usuario;
        }
        return $usuarios;
	}

    //ok visto
    public static function traer_un_usuarioId($id) 
	{
        $usuario = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $consulta =$objetoAccesoDato->retornarConsulta("select * from usuarios where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $usuarioBuscado= $consulta->fetchObject();
        if($usuarioBuscado != null){
            $usuario = new Usuario($usuarioBuscado->email, $usuarioBuscado->tipo, $usuarioBuscado->password, $usuarioBuscado->id);
        }
        return $usuario;
	}


    //ok visto
    public static function traer_un_usuario_email($email) 
	{
        $usuario = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $consulta =$objetoAccesoDato->retornarConsulta("select * from usuarios where email = ?");
        $consulta->bindValue(1, $email, PDO::PARAM_STR);
        $consulta->execute();
        $usuarioBuscado= $consulta->fetchObject();
        
        if($usuarioBuscado != null){
            $usuario = new Usuario($usuarioBuscado->email, $usuarioBuscado->tipo, $usuarioBuscado->password, $usuarioBuscado->id);
        }
        return $usuario;
	}


    //no revisado
    public function modificar_token_DB($data){
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("UPDATE usuarios set token = ? where id = ?");
        $consulta->bindValue(1, $data["token"], PDO::PARAM_STR);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }

  
    public static function filtrar_para_mostrar($arrayOrObject){
        if($arrayOrObject !== null){
            // Si es un array, simplemente lo devolvemos
            if(is_array($arrayOrObject)){
                return $arrayOrObject;
            }
    
            // Si es un objeto Usuario, lo convertimos a un array antes de filtrar
            if(is_object($arrayOrObject) && $arrayOrObject instanceof Usuario){
                $arrayOrObject = (array) $arrayOrObject;
            }
    
            // Filtramos y eliminamos las claves sensibles
            unset($arrayOrObject['password']);
            unset($arrayOrObject['token']);
    
            return $arrayOrObject;
        }
    
        return [];
    }
    

    public static function filtrar_para_guardar($array){
        if(count($array) > 0){
            foreach($array as $i){
                unset($i['password']);
                unset($i['token']);
            }
            return $array;
        }
    }


 


}


?>


