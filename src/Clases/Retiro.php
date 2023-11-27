<?php
class Retiro{
    public $idRetiro;
    public $numeroCuenta;
    public $tipoCuenta;
    public $moneda;
    public $nombre;
    public $importeRetirar;
    public $fechaRetiro;


    public function __construct($numeroCuenta, $tipoCuenta,$moneda,$nombre, $importeRetirar, $fechaRetiro = null, $idRetiro=null){
        
    $this->numeroCuenta = $numeroCuenta;
    $this->tipoCuenta = $tipoCuenta;
    $this->moneda = $moneda;
    $this->nombre = $nombre;
    $this->importeRetirar = $importeRetirar;
    $this->fechaRetiro = $fechaRetiro;
   
  
    if($fechaRetiro == null){
        $this->fechaRetiro=  date("Y-m-d");
    }
    else{
        $this->fechaRetiro = $fechaRetiro;
    }
    if($idRetiro != null){
        $this->idRetiro = $idRetiro;
    }
   }

   public static $tipoCuentas = array("CA", "CC");
  public static $tipoMoneda = array("$", "USD");

    // public function insertarRetiro()
	// {
	// 	$objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
	// 	$consulta =$objetoAccesoDato->retornarConsulta("INSERT INTO retiros (numeroCuenta, tipoCuenta,moneda, importeRetirar, fechaRetiro)values('$this->numeroCuenta','$this->tipoCuenta','$this->moneda', '$this->importeRetirar', '$this->fechaRetiro')");
	// 	$consulta->execute();
	// 	return $objetoAccesoDato->retornarUltimoIdInsertado();
	// }

    // public function insertarRetiro()
    // {
    //     $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();

    //     try {
    //         // Comenzar la transacción
    //         $objetoAccesoDato->beginTransaction();

    //         // Insertar el retiro
    //         $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO retiros (numeroCuenta, tipoCuenta, moneda, importeRetirar, fechaRetiro) VALUES ('$this->numeroCuenta', '$this->tipoCuenta', '$this->moneda', '$this->importeRetirar', '$this->fechaRetiro')");
    //         $consulta->execute();
        
    //         // Obtener el saldo actual de la cuenta
    //         $consultaSaldo = $objetoAccesoDato->retornarConsulta("SELECT saldo FROM cuentas WHERE id = :idCuenta");
    //         $consultaSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
    //         $consultaSaldo->execute();
    //         $saldoActual = $consultaSaldo->fetchColumn();
        
    //         // Calcular el nuevo saldo
    //         $nuevoSaldo = $saldoActual - $this->importeRetirar;
        
    //          // Actualizar el saldo en la cuenta
    //          $consultaActualizarSaldo = $objetoAccesoDato->retornarConsulta("UPDATE cuentas SET saldo = :nuevoSaldo WHERE id = :idCuenta");
    //          $consultaActualizarSaldo->bindValue(':nuevoSaldo', $nuevoSaldo, PDO::PARAM_STR);
    //          $consultaActualizarSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
    //          $consultaActualizarSaldo->execute();
        
    //         // Confirmar la transacción
    //         $objetoAccesoDato->commit();
        
    //         return $objetoAccesoDato->retornarUltimoIdInsertado();
    //     } catch (Exception $e) {
    //         // Revertir la transacción en caso de error
    //         $objetoAccesoDato->rollBack();
    //         var_dump($e->getMessage());
    //         return null;
    //     }
    // }
    
//     public function insertarRetiro()
// {
//     $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();

//     try {
//         // Comenzar la transacción
//         $objetoAccesoDato->beginTransaction();

//         // Verificar si el número de cuenta existe
//         $consultaVerificarCuenta = $objetoAccesoDato->retornarConsulta("SELECT COUNT(*) FROM cuentas WHERE id = :idCuenta");
//         $consultaVerificarCuenta->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
//         $consultaVerificarCuenta->execute();

//         $cuentaExistente = $consultaVerificarCuenta->fetchColumn();

//         if ($cuentaExistente > 0) {
//             // La cuenta existe, continuar con el retiro
//             $consultaInsertarRetiro = $objetoAccesoDato->retornarConsulta("INSERT INTO retiros (numeroCuenta, tipoCuenta, moneda, importeRetirar, fechaRetiro) VALUES (:numeroCuenta, :tipoCuenta, :moneda, :importeRetirar, :fechaRetiro)");
//             $consultaInsertarRetiro->bindValue(':numeroCuenta', $this->numeroCuenta, PDO::PARAM_INT);
//             $consultaInsertarRetiro->bindValue(':tipoCuenta', $this->tipoCuenta, PDO::PARAM_STR);
//             $consultaInsertarRetiro->bindValue(':moneda', $this->moneda, PDO::PARAM_STR);
//             $consultaInsertarRetiro->bindValue(':importeRetirar', $this->importeRetirar, PDO::PARAM_STR);
//             $consultaInsertarRetiro->bindValue(':fechaRetiro', $this->fechaRetiro, PDO::PARAM_STR);
//             $consultaInsertarRetiro->execute();

//             // Obtener el saldo actual de la cuenta
//             $consultaSaldo = $objetoAccesoDato->retornarConsulta("SELECT saldo FROM cuentas WHERE id = :idCuenta");
//             $consultaSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
//             $consultaSaldo->execute();
//             $saldoActual = $consultaSaldo->fetchColumn();

//             // Calcular el nuevo saldo
//             $nuevoSaldo = $saldoActual - $this->importeRetirar;

//             // Actualizar el saldo en la cuenta
//             $consultaActualizarSaldo = $objetoAccesoDato->retornarConsulta("UPDATE cuentas SET saldo = :nuevoSaldo WHERE id = :idCuenta");
//             $consultaActualizarSaldo->bindValue(':nuevoSaldo', $nuevoSaldo, PDO::PARAM_STR);
//             $consultaActualizarSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
//             $consultaActualizarSaldo->execute();

//             // Confirmar la transacción
//             $objetoAccesoDato->commit();

//             return $objetoAccesoDato->retornarUltimoIdInsertado();
//         } else {
//             // La cuenta no existe, abortar la transacción
//             $objetoAccesoDato->rollBack();
//             return null; // O puedes lanzar una excepción indicando que la cuenta no existe
//         }
//     } catch (Exception $e) {
//         // Revertir la transacción en caso de error
//         $objetoAccesoDato->rollBack();
//         var_dump($e->getMessage());
//         return null;
//     }
// }


// public function insertarRetiro()
// {
//     $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();

//     try {
//         // Comenzar la transacción
//         $objetoAccesoDato->beginTransaction();

//         // Verificar si el número de cuenta existe
//         $consultaVerificarCuenta = $objetoAccesoDato->retornarConsulta("SELECT COUNT(*) FROM cuentas WHERE id = :idCuenta");
//         $consultaVerificarCuenta->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
//         $consultaVerificarCuenta->execute();

//         $cuentaExistente = $consultaVerificarCuenta->fetchColumn();

//         if ($cuentaExistente > 0) {
//             // La cuenta existe, continuar con el retiro
//             $consultaInsertarRetiro = $objetoAccesoDato->retornarConsulta("INSERT INTO retiros (numeroCuenta, tipoCuenta, moneda, importeRetirar, fechaRetiro) VALUES (:numeroCuenta, :tipoCuenta, :moneda, :importeRetirar, :fechaRetiro)");
//             $consultaInsertarRetiro->bindValue(':numeroCuenta', $this->numeroCuenta, PDO::PARAM_INT);
//             $consultaInsertarRetiro->bindValue(':tipoCuenta', $this->tipoCuenta, PDO::PARAM_STR);
//             $consultaInsertarRetiro->bindValue(':moneda', $this->moneda, PDO::PARAM_STR);
//             $consultaInsertarRetiro->bindValue(':importeRetirar', $this->importeRetirar, PDO::PARAM_STR);
//             $consultaInsertarRetiro->bindValue(':fechaRetiro', $this->fechaRetiro, PDO::PARAM_STR);
//             $consultaInsertarRetiro->execute();

//             // Obtener el saldo actual de la cuenta
//             $consultaSaldo = $objetoAccesoDato->retornarConsulta("SELECT saldo FROM cuentas WHERE id = :idCuenta");
//             $consultaSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
//             $consultaSaldo->execute();
//             $saldoActual = $consultaSaldo->fetchColumn();

//             // Verificar si el saldo a retirar es mayor que el saldo actual
//             if ($this->importeRetirar > $saldoActual) {
//                 // Saldo a retirar es mayor que el saldo actual, abortar la transacción
//                 $objetoAccesoDato->rollBack();
//                 return json_encode(["mensaje" => "El importe a retirar es mayor a su saldo actual"]); // O puedes lanzar una excepción indicando el error
//             }

//             // Calcular el nuevo saldo
//             $nuevoSaldo = $saldoActual - $this->importeRetirar;

//             // Actualizar el saldo en la cuenta
//             $consultaActualizarSaldo = $objetoAccesoDato->retornarConsulta("UPDATE cuentas SET saldo = :nuevoSaldo WHERE id = :idCuenta");
//             $consultaActualizarSaldo->bindValue(':nuevoSaldo', $nuevoSaldo, PDO::PARAM_STR);
//             $consultaActualizarSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
//             $consultaActualizarSaldo->execute();

//             // Confirmar la transacción
//             $objetoAccesoDato->commit();
          

//             return $objetoAccesoDato->retornarUltimoIdInsertado();
//         } else {
//             // La cuenta no existe, abortar la transacción
//             $objetoAccesoDato->rollBack();
//             return "El numero de cuenta no existe"; // O puedes lanzar una excepción indicando que la cuenta no existe
//         }
//     } catch (Exception $e) {
//         // Revertir la transacción en caso de error
//         $objetoAccesoDato->rollBack();
//         var_dump($e->getMessage());
//         return null;
//     }
// }

public function insertarRetiro()
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
            $consultaInsertarRetiro = $objetoAccesoDato->retornarConsulta("INSERT INTO retiros (numeroCuenta, tipoCuenta, moneda, nombre, importeRetirar, fechaRetiro) VALUES (:numeroCuenta, :tipoCuenta, :moneda, :nombre,:importeRetirar, :fechaRetiro)");
            $consultaInsertarRetiro->bindValue(':numeroCuenta', $this->numeroCuenta, PDO::PARAM_INT);
            $consultaInsertarRetiro->bindValue(':tipoCuenta', $this->tipoCuenta, PDO::PARAM_STR);
            $consultaInsertarRetiro->bindValue(':moneda', $this->moneda, PDO::PARAM_STR);
            $consultaInsertarRetiro->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consultaInsertarRetiro->bindValue(':importeRetirar', $this->importeRetirar, PDO::PARAM_STR);
            $consultaInsertarRetiro->bindValue(':fechaRetiro', $this->fechaRetiro, PDO::PARAM_STR);
            $consultaInsertarRetiro->execute();

           
            // Obtener el saldo actual de la cuenta
            $consultaSaldo = $objetoAccesoDato->retornarConsulta("SELECT saldo FROM cuentas WHERE id = :idCuenta");
            $consultaSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
            $consultaSaldo->execute();
            $saldoActual = $consultaSaldo->fetchColumn();

            // Verificar si el saldo a retirar es mayor que el saldo actual
            if ($this->importeRetirar > $saldoActual) {
                // Saldo a retirar es mayor que el saldo actual, abortar la transacción
                $objetoAccesoDato->rollBack();
                return json_encode(["mensaje" => "El importe a retirar es mayor a su saldo actual"]);
            }

            // Calcular el nuevo saldo
            $nuevoSaldo = $saldoActual - $this->importeRetirar;

            // Actualizar el saldo en la cuenta
            $consultaActualizarSaldo = $objetoAccesoDato->retornarConsulta("UPDATE cuentas SET saldo = :nuevoSaldo WHERE id = :idCuenta");
            $consultaActualizarSaldo->bindValue(':nuevoSaldo', $nuevoSaldo, PDO::PARAM_STR);
            $consultaActualizarSaldo->bindValue(':idCuenta', $this->numeroCuenta, PDO::PARAM_INT);
            $consultaActualizarSaldo->execute();

            // Confirmar la transacción
            $objetoAccesoDato->commit();
            return json_encode(["mensaje" => "Retiro realizado con exito"]);
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
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT id as idRetiro, numeroCuenta as numeroCuenta, tipoCuenta as tipoCuenta, moneda as moneda, nombre as nombre, importeRetirar as importeRetirar, fechaRetiro as fechaRetiro from retiros");
        $consulta->execute();
        $arrayObtenido = array();
        $retiros = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $retiro = new Retiro( $i->numeroCuenta,$i->tipoCuenta, $i->moneda, $i->nombre, $i->importeRetirar, $i->fechaRetiro,$i->idRetiro);
            $retiros[] = $retiro;
        }
        return $retiros;
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


    public static function totalRetiradoPorTipoCuentaYMonedaEnFecha($fecha)
    {
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $consulta = $objetoAccesoDato->retornarConsulta("SELECT tipoCuenta, moneda, SUM(importeRetirar) as totalRetirado 
                                                       FROM retiros 
                                                       WHERE fechaRetiro = :fecha 
                                                       GROUP BY tipoCuenta, moneda");
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->execute();
    
        $resultados = $consulta->fetchAll(PDO::FETCH_OBJ);
        return $resultados;
    }


    public static function traer_retiros_por_usuario($numeroCuenta)
    {
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM retiros WHERE numeroCuenta = ?");
        $consulta->bindValue(1, $numeroCuenta, PDO::PARAM_INT);
        $consulta->execute();
    
        $retiros = array();
        $resultados = $consulta->fetchAll(PDO::FETCH_OBJ);
    
        foreach ($resultados as $resultado) {
            $retiro = new Retiro(
                $resultado->numeroCuenta,
                $resultado->tipoCuenta,
                $resultado->moneda,
                $resultado->nombre,
                $resultado->importeRetirar,
                $resultado->fechaRetiro,
                $resultado->id
            );
            $retiros[] = $retiro;
        }
    
        return $retiros;
    }
    

    public static function traer_Retiros_EntreFechas_OrdenadosPorNombre($fechaInicio, $fechaFin)
{
    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT id as idRetiro, numeroCuenta as numeroCuenta, tipoCuenta as tipoCuenta, moneda as moneda,
    nombre as nombre,  importeRetirar as importeRetirar, fechaRetiro as fechaRetiro FROM retiros WHERE fechaRetiro BETWEEN :fechaInicio AND :fechaFin ORDER BY nombre");
    $consulta->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
    $consulta->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
    $consulta->execute();
    
    $retiros = array();
    $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
   
    
    foreach($arrayObtenido as $i){
        $retiro = new Retiro($i->numeroCuenta, $i->tipoCuenta, $i->moneda,$i->nombre, $i->importeRetirar, $i->fechaRetiro, $i->idRetiro);
        $retiros[] = $retiro;
    }
    
    return $retiros;
}
    


public static function traer_retiros_por_tipo_cuenta($tipoCuenta)
{
    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM retiros WHERE tipoCuenta = ?");
    $consulta->bindValue(1, $tipoCuenta, PDO::PARAM_STR);
    $consulta->execute();
    
    $retiros = array();
    while ($retiroBuscado = $consulta->fetch(PDO::FETCH_OBJ)) {
        $retiro = new Deposito(
            $retiroBuscado->numeroCuenta,
            $retiroBuscado->tipoCuenta,
            $retiroBuscado->moneda,
            $retiroBuscado->nombre,
            $retiroBuscado->importeRetirar,
            $retiroBuscado->fechaRetiro,
            $retiroBuscado->id
        );
        $retiros[] = $retiro;
    }

    return $retiros;
}




public static function traer_retiros_por_tipo_moneda($tipoMoneda)
{
    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM retiros WHERE moneda = ?");
    $consulta->bindValue(1, $tipoMoneda, PDO::PARAM_STR);
    $consulta->execute();
    
    $retiros = array();
    while ($retiroBuscado = $consulta->fetch(PDO::FETCH_OBJ)) {
        $retiro = new Retiro(
            $retiroBuscado->numeroCuenta,
            $retiroBuscado->tipoCuenta,
            $retiroBuscado->moneda,
            $retiroBuscado->nombre,
            $retiroBuscado->importeRetirar,
            $retiroBuscado->fechaRetiro,
            $retiroBuscado->id
        );
        $retiros[] = $retiro;
    }

    return $retiros;
}
  
    }

    ?>