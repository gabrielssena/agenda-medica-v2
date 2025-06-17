<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    echo json_encode([
        'status' => 'OK',
        'php_version' => phpversion(),
        'timestamp' => date('Y-m-d H:i:s'),
        'files_exist' => [
            'TabelaHash' => file_exists(__DIR__ . '/classes/TabelaHash.php'),
            'ArvoreAVL' => file_exists(__DIR__ . '/classes/ArvoreAVL.php'),
            'FilaPrioridade' => file_exists(__DIR__ . '/classes/FilaPrioridade.php'),
            'HuffmanCompression' => file_exists(__DIR__ . '/classes/HuffmanCompression.php'),
            'AgendaMedicaReal' => file_exists(__DIR__ . '/classes/AgendaMedicaReal.php'),
            'PopularDados' => file_exists(__DIR__ . '/classes/PopularDados.php'),
            'DadosDemo' => file_exists(__DIR__ . '/dados/DadosDemo.php')
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>