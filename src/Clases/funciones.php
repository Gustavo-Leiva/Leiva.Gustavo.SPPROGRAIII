<?php
namespace funciones;
function obtenerLogsDesdeBaseDeDatos() {
    try {
        // $pdo = new \PDO('mysql:host=localhost;dbname=cuentasbancarias;charset=utf8', 'tu_usuario', 'tu_contraseña');
        $pdo = new \PDO('mysql:host=localhost;dbname=cuentasbancarias;charset=utf8', 'root', '');
        
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Preparar y ejecutar la consulta SQL
        $query = $pdo->prepare("SELECT timestamp, user_id, operation_number FROM transaction_logs");
        $query->execute();

        // Obtener los resultados como un array asociativo
        $logs = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $logs;
    } catch (\PDOException $e) {
        // Manejar el error (puedes loggearlo, lanzar una excepción, etc.)
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// function generarDatosCSV($logs) {
//     $csvData = "Fecha y Hora,Usuario,Número de Operación\n";

//     foreach ($logs as $log) {
//         $csvData .= "{$log['timestamp']},{$log['user_id']},{$log['operation_number']}\n";
//     }

//     return $csvData;
// }


 function generarDatosCSV($logs){
 
       $path = "Logs.csv";
        
     
        $archivo = fopen($path, "w");
        $encabezado = array("Fecha y Hora","Usuario","Número de Operación");
        fputcsv($archivo, $encabezado);
        foreach($logs as $fila){
            fputcsv($archivo, $fila);
        }
        fclose($archivo);
        $retorno = json_encode(array("mensaje"=>"Logs guardados en CSV con exito"));
    
        return $retorno;
    // $response->getBody()->write($retorno);
    // return $response;
    }



?>
