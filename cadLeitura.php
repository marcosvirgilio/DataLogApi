<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Garante que apenas requisições POST sejam aceitas
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido. Use POST.']);
    exit;
}

require_once 'conexao.php';
$con->set_charset("utf8");

$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes.']);
    exit;
}

// Extração e sanitização dos dados da tabela 'leitura'
$idSensor  = intval($jsonParam['idSensor'] ?? 0);
$vlLeitura = floatval($jsonParam['vlLeitura'] ?? 0);
// Se dtleitura não for enviada, assume o horário atual do servidor
$dtleitura = !empty($jsonParam['dtleitura']) 
             ? date('Y-m-d H:i:s', strtotime($jsonParam['dtleitura'])) 
             : date('Y-m-d H:i:s');

// Validação básica
if ($idSensor <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID do sensor inválido.']);
    exit;
}

// Preparação da Query para a tabela 'leitura'
$stmt = $con->prepare("
    INSERT INTO leitura (idSensor, dtleitura, vlLeitura)
    VALUES (?, ?, ?)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação: ' . $con->error]);
    exit;
}

/* No bind_param: 
   i = integer (idSensor)
   s = string (dtleitura)
   d = double/decimal (vlLeitura)
*/
$stmt->bind_param("isd", $idSensor, $dtleitura, $vlLeitura);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Leitura registrada com sucesso!',
        'idGerado' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao inserir: ' . $stmt->error]);
}

$stmt->close();
$con->close();

?>