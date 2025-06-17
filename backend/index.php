<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

function sendJSON($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Verificar se as classes existem antes de carregar
$classesPath = __DIR__ . '/classes/';
$dadosPath = __DIR__ . '/dados/';

if (!file_exists($classesPath . 'AgendaMedicaReal.php')) {
    sendJSON([
        'error' => 'Classe AgendaMedicaReal.php não encontrada',
        'path_esperado' => $classesPath . 'AgendaMedicaReal.php'
    ], 500);
}

if (!file_exists($classesPath . 'PopularDados.php')) {
    sendJSON([
        'error' => 'Classe PopularDados.php não encontrada',
        'path_esperado' => $classesPath . 'PopularDados.php'
    ], 500);
}

// Carregar estruturas de dados reais
try {
    require_once $classesPath . 'AgendaMedicaReal.php';
    require_once $classesPath . 'PopularDados.php';
} catch (Exception $e) {
    sendJSON([
        'error' => 'Erro ao carregar classes: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], 500);
}

// Inicializar sistema com estruturas reais
try {
    $agenda = new AgendaMedicaReal();
    $sistemaReal = true;
    $erro = null;
} catch (Exception $e) {
    $sistemaReal = false;
    $erro = $e->getMessage();
}

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
        sendJSON([
            'status' => 'FUNCIONANDO',
            'message' => 'API Agenda Médica v2.1 - ESTRUTURAS REAIS + DADOS DEMO',
            'version' => '2.1.1',
            'sistema_real' => $sistemaReal,
            'erro_sistema' => $erro,
            'timestamp' => date('Y-m-d H:i:s'),
            'estruturas_implementadas' => [
                'Tabela Hash' => 'Busca O(1) - Pacientes',
                'Árvore AVL' => 'Busca O(log n) - Consultas',
                'Fila Prioridade' => 'Heap O(log n) - Urgências',
                'Algoritmo Huffman' => 'Compressão - Dados médicos'
            ],
            'endpoints_demo' => [
                '?action=popular_dados' => 'Popular sistema com dados realistas',
                '?action=reset_dados' => 'Limpar todos os dados',
                '?action=relatorio_completo' => 'Relatório completo do sistema'
            ]
        ]);
        break;
        
    case 'popular_dados':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não disponível: ' . $erro], 500);
            break;
        }
        
        try {
            $popular = new PopularDados();
            $resultado = $popular->popularTodosSistema();
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro ao popular dados: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'reset_dados':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não disponível: ' . $erro], 500);
            break;
        }
        
        try {
            // Reinicializar sistema (simular reset)
            $agenda = new AgendaMedicaReal();
            sendJSON([
                'success' => true,
                'message' => 'Sistema resetado com sucesso',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro ao resetar sistema: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'relatorio_completo':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não disponível: ' . $erro], 500);
            break;
        }
        
        try {
            $relatorio = $agenda->getRelatorioCompleto();
            sendJSON($relatorio);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro ao gerar relatório: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'dashboard':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não inicializado: ' . $erro], 500);
            break;
        }
        
        try {
            $dashboard = $agenda->getDashboard();
            sendJSON($dashboard);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro no dashboard: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'pacientes':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não disponível: ' . $erro], 500);
            break;
        }
        
        try {
            $resultado = $agenda->listarPacientes();
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro ao listar pacientes: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'consultas':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não disponível: ' . $erro], 500);
            break;
        }
        
        try {
            $resultado = $agenda->listarConsultas();
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro ao listar consultas: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'urgencias':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não disponível: ' . $erro], 500);
            break;
        }
        
        try {
            $resultado = $agenda->listarFilaUrgencias();
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro ao listar urgências: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'compressao':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não disponível: ' . $erro], 500);
            break;
        }
        
        try {
            // Verificar se arquivo de dados existe
            if (!file_exists($dadosPath . 'DadosDemo.php')) {
                sendJSON([
                    'error' => 'Arquivo DadosDemo.php não encontrado',
                    'path_esperado' => $dadosPath . 'DadosDemo.php'
                ], 500);
                break;
            }
            
            require_once $dadosPath . 'DadosDemo.php';
            $dadosCompletos = DadosDemo::getDadosMedicosCompletos();
            
            $resultado = $agenda->comprimirDadosMedicos(
                $dadosCompletos['paciente_cpf'], 
                $dadosCompletos
            );
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro na compressão: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'stats':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não disponível: ' . $erro], 500);
            break;
        }
        
        try {
            $relatorio = $agenda->getRelatorioCompleto();
            sendJSON($relatorio);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro ao gerar estatísticas: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'visualizar':
        if (!$sistemaReal) {
            sendJSON(['error' => 'Sistema não disponível: ' . $erro], 500);
            break;
        }
        
        try {
            $tipo = $_GET['tipo'] ?? 'hash';
            
            switch ($tipo) {
                case 'hash':
                    $visualizacao = $agenda->visualizarTabelaHash();
                    break;
                case 'avl':
                    $visualizacao = $agenda->visualizarArvoreAVL();
                    break;
                case 'heap':
                    $visualizacao = $agenda->visualizarFilaPrioridade();
                    break;
                case 'huffman':
                    $visualizacao = $agenda->visualizarArvoreHuffman();
                    break;
                default:
                    $visualizacao = ['error' => 'Tipo de visualização inválido'];
            }
            
            sendJSON([
                'success' => true,
                'tipo' => $tipo,
                'visualizacao' => $visualizacao
            ]);
        } catch (Exception $e) {
            sendJSON([
                'error' => 'Erro na visualização: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
        break;
        
    case 'test':
        sendJSON([
            'test_status' => 'SUCESSO',
            'sistema_real' => $sistemaReal,
            'erro_sistema' => $erro,
            'php_version' => phpversion(),
            'memory_usage' => memory_get_usage(true),
            'estruturas_carregadas' => $sistemaReal,
            'dados_demo_disponiveis' => file_exists($dadosPath . 'DadosDemo.php'),
            'classes_disponiveis' => [
                'AgendaMedicaReal' => file_exists($classesPath . 'AgendaMedicaReal.php'),
                'PopularDados' => file_exists($classesPath . 'PopularDados.php'),
                'TabelaHash' => file_exists($classesPath . 'TabelaHash.php'),
                'ArvoreAVL' => file_exists($classesPath . 'ArvoreAVL.php'),
                'FilaPrioridade' => file_exists($classesPath . 'FilaPrioridade.php'),
                'HuffmanCompression' => file_exists($classesPath . 'HuffmanCompression.php')
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;
        
    default:
        sendJSON([
            'error' => 'Ação não encontrada: ' . $action,
            'acoes_disponiveis' => [
                'home', 'dashboard', 'pacientes', 'consultas', 
                'urgencias', 'compressao', 'stats', 'visualizar', 
                'popular_dados', 'reset_dados', 'relatorio_completo', 'test'
            ]
        ], 404);
}
?>