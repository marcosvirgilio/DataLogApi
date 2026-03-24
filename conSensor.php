<?php
// Exibir erros para facilitar o debug (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'conexao.php';

// Definir o charset para evitar problemas com acentuação
$con->set_charset("utf8");

// Query simplificada para buscar apenas na tabela 'sensor'
$sql = "SELECT 
            idSensor, 
            deSensor, 
            nmMarca, 
            nmModelo 
        FROM sensor";

$result = $con->query($sql);
$response = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Adiciona cada linha encontrada ao array de resposta
            $response[] = $row; 
        }
    }
} else {
    // Caso haja erro na query, registra para o desenvolvedor
    $response['error'] = "Erro na consulta: " . $con->error;
}

// Configura o cabeçalho para JSON
header('Content-Type: application/json; charset=utf-8');

// Retorna o JSON (JSON_UNESCAPED_UNICODE mantém acentos legíveis)
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Fecha a conexão
$con->close();
?>