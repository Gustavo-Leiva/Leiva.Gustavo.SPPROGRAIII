<?php

class Cuenta
{
    public $id;
    public $nombre;
    public $apellido;
    public $tipoDni;
    public $documento;
    public $email;
    public $tipoCuenta;
    public $moneda;
    public $rol;
    public $saldo;
    public $password;
    public $token;
    public $fechaRegistro;
    public $fechaBaja;

    public function __construct($nombre="", $apellido="", $tipoDni="", $documento="", $email="", $tipoCuenta="",$moneda="", $rol="", $saldo=0,$password = null, $fechaRegistro = null, $fechaBaja = null, $id = null, $token = null)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipoDni = $tipoDni;
        $this->documento = $documento;
        $this->email = $email;
        $this->tipoCuenta = $tipoCuenta;
        $this->moneda = $moneda;
        $this->rol = $rol;
        
        if($saldo != 0){
            $this->saldo = $saldo;
        }
        if($password != null){
            $this->password = $password;
        }
        else{
            $this->password = '12345';
        }
        if($fechaRegistro == null){
            $this->fechaRegistro =  date("Y-m-d");
        }
        else{
            $this->fechaRegistro = $fechaRegistro;
        }
        if($fechaBaja == null){
            $this->fechaBaja =  null;
        }
        else{
            $this->fechaBaja = $fechaBaja;
        }
        if($id != null){
            $this->id = $id;
        }
        if($token != null){
            $this->token = $token;
        }

        
    }
   

    public function insertarCuenta()
{
    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
    $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO cuentas (nombre, apellido, tipoDni, documento, email, tipoCuenta, moneda,   rol,saldo,password, fechaRegistro, fechaBaja,token) VALUES ('$this->nombre','$this->apellido','$this->tipoDni', '$this->documento', '$this->email', '$this->tipoCuenta', '$this->moneda',   '$this->rol','$this->saldo','$this->password', '$this->fechaRegistro', '$this->fechaBaja', '$this->token')");
    


    $consulta->execute();

    return $objetoAccesoDato->retornarUltimoIdInsertado();
}


    public static function traer_todas_las_cuentas_EnArray()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT id as id, nombre as nombre, apellido as apellido, tipoDni as tipoDni, documento as documento,  email as email, tipoCuenta as tipoCuenta, moneda as moneda,  rol as rol,saldo as saldo, password as password, token as token, fechaRegistro as fechaRegistro, fechaBaja as fechaBaja from cuentas");
        $consulta->execute();
        $cuentas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $cuenta = array($i->id, $i->nombre, $i->apellido, $i->tipoDni, $i->documento, $i->email, $i->tipoCuenta, $i->moneda, $i->rol,$i->saldo,$i->password,$i->fechaRegistro,$i->fechaBaja);
            $cuentas[] = $cuenta;
        }
        return $cuentas;
	}
    public static function traer_todas_las_cuentas()
	{
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT id as id, nombre as nombre, apellido as apellido, tipoDni as tipoDni, documento as documento,  email as email, tipoCuenta as tipoCuenta, moneda as moneda, rol as rol,  saldo as saldo, password as password, token as token, fechaRegistro as fechaRegistro, fechaBaja as fechaBaja from cuentas");
        $consulta->execute();
        $arrayObtenido = array();
        $cuentas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $cuenta = new Cuenta($i->nombre, $i->apellido, $i->tipoDni, $i->documento, $i->email,$i->tipoCuenta,$i->moneda, $i->rol,$i->saldo,$i->password, $i->fechaRegistro, $i->fechaBaja,$i->id, $i->token);
            $cuentas[] = $cuenta;
        }
        return $cuentas;
	}
    public static function traer_un_cuentaId($id) 
	{
        $cuenta = null;
        $cuenta = new Cuenta();//esto agregue a lo ultimo
        $cuenta->id = $id; //esto agregue a lo ultimo
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT * from cuentas where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $cuentaBuscada= $consulta->fetchObject();
        if($cuentaBuscada != null){
            $cuenta= new Cuenta($cuentaBuscada->nombre, $cuentaBuscada->apellido, $cuentaBuscada->tipoDni,$cuentaBuscada->documento, $cuentaBuscada->email,
            $cuentaBuscada->tipoCuenta,$cuentaBuscada->moneda,$cuentaBuscada->rol,$cuentaBuscada->saldo,$cuentaBuscada->password, $cuentaBuscada->fechaRegistro, $cuentaBuscada->fechaBaja, $cuentaBuscada->id,$cuentaBuscada->token);
        }
        return $cuenta;

       
	}
    public static function traer_un_cuenta_email($email) 
	{
        $cuenta = null;
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("SELECT * from cuentas where email = ?");
        $consulta->bindValue(1, $email, PDO::PARAM_STR);
        $consulta->execute();
        $cuentaBuscada= $consulta->fetchObject();
        
        if($cuentaBuscada != null){
            $cuenta = new Usuario($cuentaBuscada->nombre, $cuentaBuscada->apellido, $cuentaBuscada->tipoDni,$cuentaBuscada->documento, $cuentaBuscada->email,
            $cuentaBuscada->tipoCuenta,$cuentaBuscada->moneda,$cuentaBuscada->rol,$cuentaBuscada->saldo,$cuentaBuscada->password, $cuentaBuscada->fechaRegistro, $cuentaBuscada->fechaBaja, $cuentaBuscada->id,  $cuentaBuscada->token);
        }
        return $cuenta;
	}
    public function modificar_token_DB($data){
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos(); 
        $consulta =$objetoAccesoDato->retornarConsulta("UPDATE cuentas set token = ? where id = ?");
        $consulta->bindValue(1, $data["token"], PDO::PARAM_STR);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }


    public function definir_destino_imagen($ruta){
        echo "ID: " . $this->id; // Agregar esta línea para verificar
    $destino = $ruta."\\".$this->id."-".$this->tipoCuenta.".png";
    return $destino;
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




public static function consultarCuenta($tipoCuenta, $nroCuenta, $tipoMoneda)
{
    $cuentas = Cuenta::traer_todas_las_cuentas();

    $cuentaEncontrada = null;
    foreach ($cuentas as $cuenta) {
        if ($cuenta->tipoCuenta == $tipoCuenta && $cuenta->id == $nroCuenta) {
            // Verificar también el tipo de moneda
            if ($cuenta->moneda == $tipoMoneda) {
                $cuentaEncontrada = $cuenta;
                break;
            } else {
                return json_encode(array("mensaje" => "Tipo de moneda incorrecto para el numero de cuenta proporcionado"));
            }
        }
    }

    if ($cuentaEncontrada !== null) {
        return json_encode(array("moneda" => $cuentaEncontrada->moneda, "saldo" => $cuentaEncontrada->saldo));
    } else {
        foreach ($cuentas as $cuenta) {
            if ($cuenta->id == $nroCuenta) {
                return json_encode(array("mensaje" => "Tipo de cuenta incorrecto para el numero de cuenta proporcionado"));
            }
        }

        return json_encode(array("mensaje" => "No se encontro la combinación de tipo y numero de cuenta"));
    }
}

public static function verificarExistenciaCuenta($numeroCuenta)
{
    $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT COUNT(*) FROM cuentas WHERE id = ?");
    $consulta->bindValue(1, $numeroCuenta, PDO::PARAM_INT);
    $consulta->execute();
    $cuentaExistente = $consulta->fetchColumn();

    return $cuentaExistente > 0;
}








public function modificarCuenta()
{
    try {
        if ($this->verificarExistenciaCuenta($this->id)) {
            $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();

            $consultaModificar = $objetoAccesoDato->retornarConsulta("UPDATE cuentas SET 
                nombre = :nombre,
                apellido = :apellido,
                tipoDni = :tipoDni,
                documento = :documento,
                email = :email,
                tipoCuenta = :tipoCuenta,
                moneda = :moneda,
                rol = :rol
                WHERE id = :id");

            // Especificar explícitamente los tipos de datos
            $consultaModificar->bindParam(':id', $this->id, PDO::PARAM_INT);
            $consultaModificar->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $consultaModificar->bindParam(':apellido', $this->apellido, PDO::PARAM_STR);
            $consultaModificar->bindParam(':tipoDni', $this->tipoDni, PDO::PARAM_STR);
            $consultaModificar->bindParam(':documento', $this->documento, PDO::PARAM_INT);
            $consultaModificar->bindParam(':email', $this->email, PDO::PARAM_STR);
            $consultaModificar->bindParam(':tipoCuenta', $this->tipoCuenta, PDO::PARAM_STR);
            $consultaModificar->bindParam(':moneda', $this->moneda, PDO::PARAM_STR);
            $consultaModificar->bindParam(':rol', $this->rol, PDO::PARAM_STR);

            $consultaModificar->execute();

            return ["mensaje" => "Cuenta modificada con éxito"];
        } else {
            return ["mensaje" => "La cuenta no existe, no se puede modificar"];
        }
    } catch (PDOException $e) {
        return ["error" => "Error en la base de datos: " . $e->getMessage()];
    }
}


// En tu clase Cuenta

// ...

public function darDeBajaCuenta()
{
    try {
        if ($this->verificarExistenciaCuenta($this->id)) {
            $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();

            // Realizar el soft-delete actualizando la fechaBaja
            $consultaBaja = $objetoAccesoDato->retornarConsulta("UPDATE cuentas SET fechaBaja = :fechaBaja WHERE id = :id");
            $fechaBaja = date("Y-m-d");
            $consultaBaja->bindParam(':fechaBaja', $fechaBaja, PDO::PARAM_STR);
            $consultaBaja->bindParam(':id', $this->id, PDO::PARAM_INT);
            $consultaBaja->execute();

            // Mover la foto relacionada a la carpeta de respaldo
            $rutaFoto = $this->obtenerRutaFoto();
            $rutaBackup = $this->definir_destino_backup($rutaFoto);

            // Verifica si las rutas existen antes de intentar mover el archivo
            if (file_exists($rutaFoto) && file_exists(dirname($rutaBackup))) {
                // Agrega una comprobación adicional antes de realizar el cambio de nombre
                if (rename($rutaFoto, $rutaBackup)) {
                    return ["mensaje" => "Cuenta dada de baja con éxito"];
                } else {
                    return ["error" => "Error al intentar mover el archivo"];
                }
            } else {
                return ["error" => "Rutas de archivo no válidas o no existen"];
            }
        } else {
            return ["mensaje" => "La cuenta no existe, no se puede dar de baja"];
        }
    } catch (PDOException $e) {
        return ["error" => "Error en la base de datos: " . $e->getMessage()];
    }
}

// ...


public function definir_destino_backup($ruta)
{
    $rutaBackup = 'C:\xampp\htdocs\CuentasBancariasII\src\Controllers\Imagen\foto_del_usuario\cliente\imagenesBackupCuentas\2023';
    $destino = $rutaBackup . "\\" . $this->id . "-" . $this->tipoCuenta . ".png";
    return $destino;
}

public function obtenerRutaFoto()
{
    $rutaImagenCuenta = 'C:\xampp\htdocs\CuentasBancariasII\src\Controllers\Imagen\foto_del_usuario\cliente\imagenesDeCuenta\2023';
    
    // Limpiar $this->tipoCuenta y eliminar espacios en blanco
    $tipoCuentaLimpiado = trim($this->tipoCuenta);
    var_dump($tipoCuentaLimpiado);

    // Imprimir la longitud del tipo de cuenta para depurar
    echo "Longitud del tipo de cuenta: " . strlen($tipoCuentaLimpiado) . "<br>";

    $nombreArchivo = $this->id . "-" . $tipoCuentaLimpiado . ".png";

    // Imprimir la ruta y el nombre del archivo para depurar
    echo "Ruta: $rutaImagenCuenta<br>";
    echo "Nombre del archivo: $nombreArchivo<br>";

    $origen = $rutaImagenCuenta . "\\" . $nombreArchivo;
    return $origen;
}







public function realizarAjuste($tipoOperacion, $idOperacionAfectada, $motivo, $montoAjuste, $tipoAjuste)
{
    try {
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();

        // Obtener el saldo actual de la cuenta antes del ajuste
        $saldoActualizado = $this->obtenerSaldoCuenta();
        // var_dump($saldoActualizado);

        // Validar que la operación afectada exista
        if ($this->verificarExistenciaOperacion($idOperacionAfectada, $tipoOperacion)) {
            // Insertar un nuevo ajuste en la tabla 'ajustes'
            $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO ajustes (id_cuenta, tipo_operacion, id_retiro_afectado, id_deposito_afectado, motivo, monto_ajuste, tipo_ajuste) 
                                                            VALUES (:idCuenta, :tipoOperacion, :idRetiroAfectado, :idDepositoAfectado, :motivo, :montoAjuste, :tipoAjuste)");

            // Asigna el id de la cuenta al parámetro :idCuenta
            $consulta->bindParam(':idCuenta', $this->id, PDO::PARAM_INT);

            $idRetiroAfectado = null;
            $idDepositoAfectado = null;

            // Determinar qué columna de id_operacion_afectada se debe llenar
            switch ($tipoOperacion) {
                case 'retiros':
                    $idRetiroAfectado = $idOperacionAfectada;
                    break;
                case 'depositos':
                    $idDepositoAfectado = $idOperacionAfectada;
                    break;
                // Otros casos según las operaciones que manejes
            }

            $consulta->bindParam(':tipoOperacion', $tipoOperacion, PDO::PARAM_STR);
            $consulta->bindParam(':idRetiroAfectado', $idRetiroAfectado, PDO::PARAM_INT);
            $consulta->bindParam(':idDepositoAfectado', $idDepositoAfectado, PDO::PARAM_INT);
            $consulta->bindParam(':motivo', $motivo, PDO::PARAM_STR);
            $consulta->bindParam(':montoAjuste', $montoAjuste, PDO::PARAM_INT);
            $consulta->bindParam(':tipoAjuste', $tipoAjuste, PDO::PARAM_STR);

            $consulta->execute();

            // Actualizar el saldo de la cuenta después del ajuste
            $nuevoSaldo = $saldoActualizado + $montoAjuste;
            // var_dump($nuevoSaldo);
            $resultadoSaldo = $this->actualizarSaldoCuenta($nuevoSaldo);

            if (isset($resultadoSaldo['error'])) {
                return $resultadoSaldo; // Devolver el error si hubo algún problema al actualizar el saldo
            }

            return ["mensaje" => "Ajuste realizado con éxito"];
        } else {
            return ["mensaje" => "La operación afectada no existe"];
        }
    } catch (PDOException $e) {
        return ["error" => "Error en la base de datos: " . $e->getMessage()];
    }
}



public function actualizarSaldoCuenta($nuevoSaldo)
{
    try {
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
        $consulta = $objetoAccesoDato->retornarConsulta("UPDATE cuentas SET saldo = :nuevoSaldo WHERE id = :id");
        // var_dump($this->id);
        $consulta->bindParam(':nuevoSaldo', $nuevoSaldo, PDO::PARAM_INT);
        $consulta->bindParam(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        // var_dump($consulta->errorInfo());
        return ["mensaje" => "Saldo actualizado con éxito"];
    } catch (PDOException $e) {
        return ["error" => "Error al actualizar el saldo de la cuenta: " . $e->getMessage()];
    }
}



public function verificarExistenciaOperacion($idOperacion, $tipoOperacion)
{
    try {
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();

        // Utilizar el nombre de la tabla de operaciones según el tipo
        $nombreTabla = $tipoOperacion === 'retiros' ? 'retiros' : 'depositos';

        $consulta = $objetoAccesoDato->retornarConsulta("SELECT COUNT(*) FROM $nombreTabla WHERE id = :id");
        $consulta->bindParam(':id', $idOperacion, PDO::PARAM_INT);
        $consulta->execute();
        $cantidad = $consulta->fetchColumn();

        return $cantidad > 0;
    } catch (PDOException $e) {
        // Manejar el error según tus necesidades
        die("Error al verificar la existencia de la operación: " . $e->getMessage());
    }
}


    public function obtenerSaldoCuenta()
    {
        try {
            $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();
            $consulta = $objetoAccesoDato->retornarConsulta("SELECT saldo FROM cuentas WHERE id = :id");
            $consulta->bindParam(':id', $this->id, PDO::PARAM_INT);
             $consulta->execute();
            
            $saldo = $consulta->fetchColumn();
            

            // var_dump($saldo);
            return $saldo !== false ? $saldo : 0;
        } catch (PDOException $e) {
            // Maneja el error según tus necesidades
            die("Error al obtener el saldo de la cuenta: " . $e->getMessage());
        }
    }







    public function obtenerOperacionesCuenta()
{
    try {
        $objetoAccesoDato = AccesoDatos::obtenerConexionDatos();

        $consulta = $objetoAccesoDato->retornarConsulta("
            SELECT
                id AS id_operacion,
                'retiro' AS tipo_operacion,
                importeRetirar AS monto
            FROM retiros
            WHERE numeroCuenta = :id

            UNION

            SELECT
                id AS id_operacion,
                'deposito' AS tipo_operacion,
                importeDepositar AS monto
            FROM depositos
            WHERE numeroCuenta = :id
        ");

        $consulta->bindParam(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        $operaciones = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $operaciones;
    } catch (PDOException $e) {
        die("Error al obtener operaciones de la cuenta: " . $e->getMessage());
    }
}

    







}


?>