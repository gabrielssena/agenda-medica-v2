<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/classes/AgendaMedicaSimples.php';
require_once __DIR__ . '/dados/DadosDemo.php';

function sendJSON($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $agenda = new AgendaMedicaSimples();
    $sistemaReal = true;
} catch (Exception $e) {
    $sistemaReal = false;
    $erro = $e->getMessage();
    sendJSON(['error' => 'Erro ao inicializar sistema: ' . $erro], 500);
}

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
    case 'test':
        sendJSON([
            'test_status' => 'SUCESSO',
            'sistema_real' => $sistemaReal,
            'php_version' => phpversion(),
            'timestamp' => date('Y-m-d H:i:s'),
            'estruturas_implementadas' => [
                'Tabela Hash' => 'Busca O(1) - Pacientes',
                'Árvore AVL' => 'Busca O(log n) - Consultas',
                'Fila Prioridade' => 'Heap O(log n) - Urgências',
                'Algoritmo Huffman' => 'Compressão - Dados médicos'
            ]
        ]);
        break;

    // ========== NOVAS AÇÕES FUNCIONAIS ==========
    case 'cadastrar_paciente':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendJSON(['error' => 'Método não permitido'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            sendJSON(['error' => 'Dados não fornecidos'], 400);
        }
        
        $result = $agenda->cadastrarPacienteFuncional(
            $input['cpf'] ?? '',
            $input['nome'] ?? '',
            $input['idade'] ?? null,
            $input['telefone'] ?? '',
            $input['email'] ?? '',
            $input['convenio'] ?? '',
            $input['endereco'] ?? ''
        );
        
        sendJSON($result);
        break;

    case 'buscar_paciente':
        $cpf = $_GET['cpf'] ?? '';
        if (!$cpf) {
            sendJSON(['error' => 'CPF não fornecido'], 400);
        }
        
        $result = $agenda->buscarPacienteFuncional($cpf);
        sendJSON($result);
        break;

    case 'agendar_consulta':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendJSON(['error' => 'Método não permitido'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            sendJSON(['error' => 'Dados não fornecidos'], 400);
        }
        
        $result = $agenda->agendarConsultaFuncional(
            $input['cpf'] ?? '',
            $input['dataHora'] ?? '',
            $input['medico'] ?? '',
            $input['observacoes'] ?? ''
        );
        
        sendJSON($result);
        break;

    case 'adicionar_urgencia':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendJSON(['error' => 'Método não permitido'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            sendJSON(['error' => 'Dados não fornecidos'], 400);
        }
        
        $result = $agenda->adicionarUrgenciaFuncional(
            $input['cpf'] ?? '',
            $input['prioridade'] ?? 3,
            $input['descricao'] ?? ''
        );
        
        sendJSON($result);
        break;

    // ========== AÇÕES EXISTENTES MANTIDAS ==========        
    case 'popular_dados':
        try {
            // Popular pacientes
            $pacientes = DadosDemo::getPacientesDemo();
            $contadorPacientes = 0;
            foreach ($pacientes as $paciente) {
                $resultado = $agenda->cadastrarPaciente($paciente['cpf'], $paciente);
                if ($resultado['success']) $contadorPacientes++;
            }
            
            // Popular consultas
            $consultas = DadosDemo::getConsultasDemo();
            $contadorConsultas = 0;
            foreach ($consultas as $consulta) {
                $resultado = $agenda->agendarConsulta(
                    $consulta['dados']['cpf_paciente'],
                    $consulta['dataHora'],
                    $consulta['dados']['medico'],
                    $consulta['dados']['observacoes']
                );
                if ($resultado['success']) $contadorConsultas++;
            }
            
            // Popular urgências
            $urgencias = DadosDemo::getUrgenciasDemo();
            $contadorUrgencias = 0;
            foreach ($urgencias as $urgencia) {
                // Cadastrar paciente da urgência
                $dadosPaciente = [
                    'nome' => $urgencia['nome'],
                    'idade' => rand(18, 80),
                    'telefone' => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
                    'email' => strtolower(str_replace(' ', '.', $urgencia['nome'])) . '@email.com',
                    'endereco' => 'Endereço de emergência - São Paulo/SP',
                    'convenio' => 'SUS'
                ];
                $agenda->cadastrarPaciente($urgencia['cpf'], $dadosPaciente);
                
                // Adicionar à fila de urgência
                $resultado = $agenda->adicionarUrgencia(
                    $urgencia['cpf'],
                    $urgencia['prioridade'],
                    $urgencia['descricao']
                );
                if ($resultado['success']) $contadorUrgencias++;
            }
            
            sendJSON([
                'success' => true,
                'message' => 'Sistema populado com sucesso!',
                'detalhes' => [
                    'pacientes' => ['total_inseridos' => $contadorPacientes],
                    'consultas' => ['total_inseridas' => $contadorConsultas],
                    'urgencias' => ['total_inseridas' => $contadorUrgencias]
                ]
            ]);
        } catch (Exception $e) {
            sendJSON(['error' => 'Erro ao popular dados: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'dashboard':
        try {
            $dashboard = $agenda->getDashboard();
            sendJSON($dashboard);
        } catch (Exception $e) {
            sendJSON(['error' => 'Erro no dashboard: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'pacientes':
        try {
            $resultado = $agenda->listarPacientes();
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON(['error' => 'Erro ao listar pacientes: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'consultas':
        try {
            $resultado = $agenda->listarConsultas();
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON(['error' => 'Erro ao listar consultas: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'urgencias':
        try {
            $resultado = $agenda->listarFilaUrgencias();
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON(['error' => 'Erro ao listar urgências: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'compressao':
        try {
            $resultado = $agenda->demonstrarCompressao();
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON(['error' => 'Erro na compressão: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'reset_dados':
        try {
            $resultado = $agenda->resetarSistema();
            sendJSON($resultado);
        } catch (Exception $e) {
            sendJSON(['error' => 'Erro ao resetar: ' . $e->getMessage()], 500);
        }
        break;
        
    default:
        sendJSON(['error' => 'Ação não encontrada: ' . $action], 404);
}
?>