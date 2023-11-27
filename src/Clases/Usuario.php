<?php

class Usuario
{
    public $id;
    public $email;
    public $tipo;
    public $password;

    public function __construct( $email, $tipo, $password, $id = null)
    {
        $this->email = $email;
        $this->tipo = $tipo;
        $this->password = $password;
        if($id != null){
            $this->id = $id;
        }
    }
    public function insertar_usuario()
	{
		$objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
		$consulta =$objetoAccesoDato->retornarConsulta("insert into usuarios (email, tipo, password)values('$this->email','$this->tipo','$this->password')");
		$consulta->execute();
		return $objetoAccesoDato->retornarUltimoIdInsertado();
	}
    public static function traer_todo_los_usuarios()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $consulta =$objetoAccesoDato->retornarConsulta("select id as id, email as email, tipo as tipo, password as password, token as token from usuarios");
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
    public static function traer_un_usuario_Id($id) 
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
    public static function filtrar_para_mostrar($array){
        // if(count($array) > 0){
        //     foreach($array as $i){
        //         unset($i->password);
        //     }
        //     return $array;
        // }

        if (is_countable($array)) {
            foreach ($array as $i) {
                unset($i->password);
            }
            return $array;
        } else {
            // Manejar el caso en el que $array no es contable (puede ser un solo objeto)
            unset($array->password);
            return $array;
        }
    }
}

?>