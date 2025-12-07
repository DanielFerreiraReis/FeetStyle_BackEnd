<?php
$input = json_decode(file_get_contents('php://input'), true);
$modelo = $input['modelo'] ?? 'desconhecido';

echo json_encode([
    'success' => true,
    'message' => "Calçado $modelo cadastrado com sucesso"
]);