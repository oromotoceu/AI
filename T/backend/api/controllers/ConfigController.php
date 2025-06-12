<?php
// getConfig, updateConfig
function getConfig($pdo) {
    $stmt = $pdo->query("SELECT * FROM config WHERE id = 1");
    $config = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($config);
}
function updateConfig($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);

    $fields = [];
    $params = [];

    foreach(['empresa_nome', 'empresa_email', 'permite_visitante', 'permite_cadastro', 'conta_envio_email'] as $key) {
        if (isset($data[$key])) {
            $fields[] = "$key = ?";
            $params[] = $data[$key];
        }
    }

    if (empty($fields)) {
        echo json_encode(['message' => 'Nenhum campo fornecido para atualização']);
        return;
    }

    $sql = "UPDATE config SET " . implode(', ', $fields) . " WHERE id = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['message' => 'Configuração atualizada com sucesso']);
}
