<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'conexao.php';
$con->set_charset("utf8");

$sql = "SELECT 
            l.idLeitura,
            l.dtleitura, 
            s.idSensor,
            s.deSensor,
            s.nmMarca,
            s.nmModelo,
            l.vlLeitura
        FROM leitura l
        JOIN sensor s ON l.idSensor = s.idSensor";

$result = $con->query($sql);
$response = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Atribuição direta: simples, rápida e automática
        $response[] = $row; 
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);

$con->close();
?>