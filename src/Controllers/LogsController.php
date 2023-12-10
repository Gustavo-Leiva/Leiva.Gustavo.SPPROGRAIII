<?php
// LogsController.php

namespace src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use funciones; // Asegúrate de incluir el archivo funciones.php

require_once '../src/Clases/funciones.php';

class LogsController
{
    public static function GET_DescargarLogsCSV(Request $request, Response $response, array $args)
    {
        // Obtener logs desde la base de datos
        $logs = funciones\obtenerLogsDesdeBaseDeDatos();

        // Generar datos CSV
        $csvData = funciones\generarDatosCSV($logs);

        // Configurar la respuesta para descargar el archivo CSV
        $response = $response->withHeader('Content-Type', 'text/csv')
                            ->withHeader('Content-Disposition', 'attachment; filename="logs.csv"');
                            // Escribir el contenido del archivo CSV en el cuerpo de la respuesta
        $response->getBody()->write($csvData);

        return $response;
    }

    


}
?>