<?php
class Deposito{
    public $idDeposito;
    public $numeroCuenta;
    public $tipoCuenta;
    public $moneda;
    public $nombre;
    public $importeDepositar;
    public $fechaDeposito;


    public function __construct($numeroCuenta, $tipoCuenta,$moneda,$nombre, $importeDepositar, $fechaDeposito = null, $idDeposito=null){
        
    $this->numeroCuenta = $numeroCuenta;
    $this->tipoCuenta = $tipoCuenta;
    $this->moneda = $moneda;
    $this->nombre = $nombre;
    $this->importeDepositar = $importeDepositar;
    $this->fechaDeposito = $fechaDeposito;
   
  
    if($fechaDeposito == null){
        $this->fechaDeposito=  date("Y-m-d");
    }
    else{
        $this->fechaDeposito = $fechaDeposito;
    }
    if($idDeposito != null){
        $this->idDeposito = $idDeposito;
    }
   }


    public function insertarDeposito()
{

    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();

    try {
        // Comenzar la transacción
        $objetoAccesoDato->beginTransaction();

        // Verificar si el número de cuenta existe
        $consultaVerificarCuenta = $objetoAccesoDato->retornarConsulta("SELECT COUNT(*) FROM cuentas WHERE id = :idCuenta");
        $consultaVerificarCuenta->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
        $consultaVerificarCuenta->execute();

        $cuentaExistente = $consultaVerificarCuenta->fetchColumn();

        if ($cuentaExistente > 0) {
            // La cuenta existe, continuar con el retiro
            $consultaInsertarDeposito = $objetoAccesoDato->retornarConsulta("INSERT INTO depositos (numeroCuenta, tipoCuenta, moneda, nombre,importeDepositar, fechaDeposito) VALUES (:numeroCuenta, :tipoCuenta, :moneda, :nombre, :importeDepositar, :fechaDeposito)");
            $consultaInsertarDeposito->bindValue(':numeroCuenta', $this->numeroCuenta, PDO::PARAM_INT);
            $consultaInsertarDeposito->bindValue(':tipoCuenta', $this->tipoCuenta, PDO::PARAM_STR);
            $consultaInsertarDeposito->bindValue(':moneda', $this->moneda, PDO::PARAM_STR);
            $consultaInsertarDeposito->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consultaInsertarDeposito->bindValue(':importeDepositar', $this->importeDepositar, PDO::PARAM_STR);
            $consultaInsertarDeposito->bindValue(':fechaDeposito', $this->fechaDeposito, PDO::PARAM_STR);
            $consultaInsertarDeposito->execute();

            // Obtener el saldo actual de la cuenta
            $consultaSaldo = $objetoAccesoDato->retornarConsulta("SELECT saldo FROM cuentas WHERE id = :idCuenta");
            $consultaSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
            $consultaSaldo->execute();
            $saldoActual = $consultaSaldo->fetchColumn();

            // Verificar si el saldo a retirar es mayor que el saldo actual
            if (!($this->importeDepositar > 0)) {
                // Saldo a retirar es mayor que el saldo actual, abortar la transacción
                $objetoAccesoDato->rollBack();
                return json_encode(["mensaje" => "El importe a depositar debe ser mayor a 0"]);
            }

            // Calcular el nuevo saldo
            $nuevoSaldo = $saldoActual + $this->importeDepositar;

            // Actualizar el saldo en la cuenta
            $consultaActualizarSaldo = $objetoAccesoDato->retornarConsulta("UPDATE cuentas SET saldo = :nuevoSaldo WHERE id = :idCuenta");
            $consultaActualizarSaldo->bindValue(':nuevoSaldo', $nuevoSaldo, PDO::PARAM_STR);
            $consultaActualizarSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
            $consultaActualizarSaldo->execute();

            // Confirmar la transacción
            $objetoAccesoDato->commit();
           

            return json_encode(["mensaje" => "Deposito realizado con exito"]);
        } else {
            // La cuenta no existe, abortar la transacción
            $objetoAccesoDato->rollBack();
            return json_encode(["mensaje" => "El numero de cuenta no existe"]);
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $objetoAccesoDato->rollBack();
        return json_encode(["mensaje" => $e->getMessage()]);
    }
}


    public static function obtenerTodos()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT id as idDeposito, numeroCuenta as numeroCuenta, tipoCuenta as tipoCuenta, moneda as moneda,nombre as nombre,  importeDepositar as importeDepositar, fechaDeposito as fechaDeposito from depositos");
        $consulta->execute();
        $arrayObtenido = array();
        $depositos = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $deposito = new Deposito( $i->numeroCuenta,$i->tipoCuenta, $i->moneda,$i->nombre,  $i->importeDepositar, $i->fechaDeposito,$i->idDeposito);
            $depositos[] = $deposito;
        }
        return $depositos;
	}

    public function definir_destino_imagen($ruta){
        $destino = $ruta . "\\" . $this->tipoCuenta . "-" . $this->numeroCuenta . "-" . $this->idDeposito . ".png";
        return $destino;
    }
    


    public static function traer_un_deposito_Id($id) 
	{
        $deposito = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("select * from depositos where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $depositoBuscado= $consulta->fetchObject();
        if($depositoBuscado != null){
            $deposito  = new Deposito($depositoBuscado->numeroCuenta, $depositoBuscado->tipoCuenta, $depositoBuscado->moneda,$depositoBuscado->nombre,  $depositoBuscado->importeDepositar,$depositoBuscado->fechaDeposito,$depositoBuscado->id);
            
        }
       
        return $deposito ;
	}



     
    public static function filtrar_para_mostrar($array)
    {
        if (is_array($array)) {
            foreach ($array as $i) {
                unset($i->password);
                unset($i->token);
            }
            return $array;
        } elseif (is_object($array)) {
            unset($array->password);
            unset($array->token);
            return $array;
        } else {
            return $array; // O manejar el caso de otro tipo de dato si es necesario
        }
    }

    public static function totalDepositadoPorTipoCuentaYMonedaEnFecha($fecha)
    {
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $consulta = $objetoAccesoDato->retornarConsulta("SELECT tipoCuenta, moneda, SUM(importeDepositar) as totalDepositado 
                                                       FROM depositos 
                                                       WHERE fechaDeposito = :fecha 
                                                       GROUP BY tipoCuenta, moneda");
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->execute();
    
        $resultados = $consulta->fetchAll(PDO::FETCH_OBJ);
        return $resultados;
    }
    

    
    public static function traer_Depositos_EntreFechas_OrdenadosPorNombre($fechaInicio, $fechaFin)
{
    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT id as idDeposito, numeroCuenta as numeroCuenta, tipoCuenta as tipoCuenta, moneda as moneda,
    nombre as nombre,  importeDepositar as importeDepositar, fechaDeposito as fechaDeposito FROM depositos WHERE fechaDeposito BETWEEN :fechaInicio AND :fechaFin ORDER BY nombre");
    $consulta->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
    $consulta->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
    $consulta->execute();
    
    $depositos = array();
    $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
   
    
    foreach($arrayObtenido as $i){
        $deposito = new Deposito($i->numeroCuenta, $i->tipoCuenta, $i->moneda,$i->nombre, $i->importeDepositar, $i->fechaDeposito, $i->idDeposito);
        $depositos[] = $deposito;
    }
    
    return $depositos;
}


    public static function traer_depositos_por_usuario($numeroCuenta)
{
    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM depositos WHERE numeroCuenta = ?");
    $consulta->bindValue(1, $numeroCuenta, PDO::PARAM_INT);
    $consulta->execute();

    $depositos = array();
    $resultados = $consulta->fetchAll(PDO::FETCH_OBJ);

    foreach ($resultados as $resultado) {
        $deposito = new Deposito(
            $resultado->numeroCuenta,
            $resultado->tipoCuenta,
            $resultado->moneda,
            $resultado->nombre,
            $resultado->importeDepositar,
            $resultado->fechaDeposito,
            $resultado->id
        );
        $depositos[] = $deposito;
    }

    return $depositos;
}




    public static function traer_depositos_por_tipo_cuenta($tipoCuenta)
{
    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM depositos WHERE tipoCuenta = ?");
    $consulta->bindValue(1, $tipoCuenta, PDO::PARAM_STR);
    $consulta->execute();
    
    $depositos = array();
    while ($depositoBuscado = $consulta->fetch(PDO::FETCH_OBJ)) {
        $deposito = new Deposito(
            $depositoBuscado->numeroCuenta,
            $depositoBuscado->tipoCuenta,
            $depositoBuscado->moneda,
            $depositoBuscado->nombre,
            $depositoBuscado->importeDepositar,
            $depositoBuscado->fechaDeposito,
            $depositoBuscado->id
        );
        $depositos[] = $deposito;
    }

    return $depositos;
}


public static function traer_depositos_por_tipo_moneda($tipoMoneda)
{
    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM depositos WHERE moneda = ?");
    $consulta->bindValue(1, $tipoMoneda, PDO::PARAM_STR);
    $consulta->execute();
    
    $depositos = array();
    while ($depositoBuscado = $consulta->fetch(PDO::FETCH_OBJ)) {
        $deposito = new Deposito(
            $depositoBuscado->numeroCuenta,
            $depositoBuscado->tipoCuenta,
            $depositoBuscado->moneda,
            $depositoBuscado->nombre,
            $depositoBuscado->importeDepositar,
            $depositoBuscado->fechaDeposito,
            $depositoBuscado->id
        );
        $depositos[] = $deposito;
    }

    return $depositos;
}
  
    }

    ?>