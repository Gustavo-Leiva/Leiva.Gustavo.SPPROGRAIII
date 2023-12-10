<?php

class Logger {
    private $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }
    
    
    public function logTransaction($user_id, $operation_number) {

        $logMessage = [
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $user_id,
            'operation_number' => $operation_number,
            // Otros campos según tus necesidades
        ];
        
        // Inserta el registro en la tabla transaction_logs
        try {
            $query = $this->db->prepare("INSERT INTO transaction_logs (timestamp, user_id, operation_number) VALUES (?, ?, ?)");
            $query->execute([$logMessage['timestamp'], $logMessage['user_id'], $logMessage['operation_number']]);
            
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        
    
    }


    public function logAccess($method, $url, $ip, $result)
    {
        $logMessage = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $method,
            'url' => $url,
            'ip' => $ip,
            'result' => $result,
        ];

        // Inserta el registro en la base de datos
        $query = $this->db->prepare("INSERT INTO access_logs (timestamp, method, url, ip, result) VALUES (?, ?, ?, ?, ?)");
        $query->execute([$logMessage['timestamp'], $logMessage['method'], $logMessage['url'], $logMessage['ip'], $logMessage['result']]);
    }
}
?>
