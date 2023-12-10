<?php
class AccesoDatos
{
    private static $ObjetoAccesoDatos;
    private $objetoPDO;

    private function __construct()
    {
        try { 
            $this->objetoPDO = new PDO('mysql:host=localhost;dbname=cuentasbancarias;charset=utf8', 'root', '', array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->objetoPDO->exec("SET CHARACTER SET utf8");
            } 
        catch (PDOException $e) { 
            print "Error!: " . $e->getMessage(); 
            die();
        }
    }

    public static function obtenerConexionDatos()
    {
        if (!isset(self::$ObjetoAccesoDatos)) {
            self::$ObjetoAccesoDatos = new AccesoDatos();
        }
        return self::$ObjetoAccesoDatos;
    }

    public function retornarConsulta($sql)
    {
        return $this->objetoPDO->prepare($sql);
    }

    public function retornarUltimoIdInsertado()
    {
        return $this->objetoPDO->lastInsertId();
    }

    // Nuevos métodos agregados para transacciones
    public function beginTransaction()
    {
        return $this->objetoPDO->beginTransaction();
    }

    public function commit()
    {
        return $this->objetoPDO->commit();
    }

    public function rollBack()
    {
        return $this->objetoPDO->rollBack();
    }

    public function __clone()
    {
        trigger_error('ERROR: La clonación de este objeto no está permitida', E_USER_ERROR);
    }

    
}

?>