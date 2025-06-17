<?php

require_once 'TabelaHash.php';
require_once 'ArvoreAVL.php';
require_once 'FilaPrioridade.php';
require_once 'HuffmanCompression.php';

class AgendaMedicaReal {
    private $tabelaPacientes;
    private $arvoreConsultas;
    private $filaUrgencias;
    private $compressaoHuffman;
    private $estatisticasGerais;
    
    public function __construct() {
        $this->tabelaPacientes = new TabelaHash(1009);
        $this->arvoreConsultas = new ArvoreAVL();
        $this->filaUrgencias = new FilaPrioridade();
        $this->compressaoHuffman = new HuffmanCompression();
        $this->estatisticasGerais = [
            'inicializado_em' => date('Y-m-d H:i:s'),
            'operacoes_realizadas' => 0,
            'tempo_total_operacoes' => 0
        ];
    }
    
    // ========== GESTÃO DE PACIENTES (TABELA HASH) ==========
    
    public function cadastrarPaciente($cpf, $dadosPaciente) {
        $inicio = microtime(true);
        
        if (!$this->validarCPF($cpf)) {
            return [
                'success' => false,
                'error' => 'CPF inválido',
                'estrutura_dados' => 'Tabela Hash'
            ];
        }
        
        $dadosPaciente['data_cadastro'] = date('Y-m-d H:i:s');
        $dadosPaciente['id'] = uniqid('pac_');
        
        $resultado = $this->tabelaPacientes->inserir($cpf, $dadosPaciente);
        
        $tempo = (microtime(true) - $inicio) * 1000;
        $this->atualizarEstatisticasGerais($tempo);
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Tabela Hash',
            'complexidade' => 'O(1) médio',
            'tempo_operacao_ms' => $tempo
        ]);
    }
    
    public function buscarPaciente($cpf) {
        if (!$this->validarCPF($cpf)) {
            return [
                'success' => false,
                'error' => 'CPF inválido'
            ];
        }
        
        $resultado = $this->tabelaPacientes->buscar($cpf);
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Tabela Hash',
            'complexidade' => 'O(1) médio'
        ]);
    }
    
    public function listarPacientes() {
        $pacientes = $this->tabelaPacientes->listarTodos();
        $estatisticas = $this->tabelaPacientes->getEstatisticas();
        
        return [
            'success' => true,
            'pacientes' => $pacientes,
            'total' => count($pacientes),
            'estrutura_dados' => 'Tabela Hash',
            'estatisticas_hash' => $estatisticas
        ];
    }
    
    public function getEstatisticasHash() {
        return $this->tabelaPacientes->getEstatisticas();
    }
    
    public function visualizarTabelaHash() {
        return $this->tabelaPacientes->visualizarDistribuicao();
    }
    
    // ========== GESTÃO DE CONSULTAS (ÁRVORE AVL) ==========
    
    public function agendarConsulta($cpf, $dataHora, $medico, $observacoes = '') {
        $paciente = $this->tabelaPacientes->buscar($cpf);
        
        if (!$paciente['success']) {
            return [
                'success' => false,
                'error' => 'Paciente não encontrado'
            ];
        }
        
        $dadosConsulta = [
            'id' => uniqid('cons_'),
            'cpf_paciente' => $cpf,
            'nome_paciente' => $paciente['paciente']['nome'],
            'medico' => $medico,
            'observacoes' => $observacoes,
            'status' => 'agendada',
            'data_agendamento' => date('Y-m-d H:i:s')
        ];
        
        $resultado = $this->arvoreConsultas->inserir($dataHora, $dadosConsulta);
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Árvore AVL',
            'complexidade' => 'O(log n)',
            'consulta' => $dadosConsulta
        ]);
    }
    
    public function buscarConsulta($dataHora) {
        $resultado = $this->arvoreConsultas->buscar($dataHora);
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Árvore AVL',
            'complexidade' => 'O(log n)'
        ]);
    }
    
    public function listarConsultas() {
        $resultado = $this->arvoreConsultas->listarEmOrdem();
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Árvore AVL',
            'complexidade' => 'O(n)'
        ]);
    }
    
    public function cancelarConsulta($dataHora) {
        $resultado = $this->arvoreConsultas->remover($dataHora);
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Árvore AVL',
            'complexidade' => 'O(log n)'
        ]);
    }
    
    public function getEstatisticasAVL() {
        return $this->arvoreConsultas->getEstatisticas();
    }
    
    public function visualizarArvoreAVL() {
        return $this->arvoreConsultas->visualizarArvore();
    }
    
    // ========== GESTÃO DE URGÊNCIAS (FILA DE PRIORIDADE) ==========
    
    public function adicionarUrgencia($cpf, $prioridade, $descricao) {
        $paciente = $this->tabelaPacientes->buscar($cpf);
        
        if (!$paciente['success']) {
            return [
                'success' => false,
                'error' => 'Paciente não encontrado'
            ];
        }
        
        $resultado = $this->filaUrgencias->inserir(
            $cpf, 
            $paciente['paciente']['nome'], 
            $prioridade, 
            $descricao
        );
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Fila de Prioridade (Min-Heap)',
            'complexidade' => 'O(log n)'
        ]);
    }
    
    public function chamarProximoUrgencia() {
        $resultado = $this->filaUrgencias->removerProximo();
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Fila de Prioridade (Min-Heap)',
            'complexidade' => 'O(log n)'
        ]);
    }
    
    public function listarFilaUrgencias() {
        $resultado = $this->filaUrgencias->listarFila();
        $estatisticas = $this->filaUrgencias->contarPorPrioridade();
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Fila de Prioridade (Min-Heap)',
            'estatisticas_prioridade' => $estatisticas
        ]);
    }
    
    public function getEstatisticasFilaPrioridade() {
        return $this->filaUrgencias->getEstatisticas();
    }
    
    public function visualizarFilaPrioridade() {
        return $this->filaUrgencias->visualizarHeap();
    }
    
    // ========== COMPRESSÃO DE DADOS (HUFFMAN) ==========
    
    public function comprimirDadosMedicos($cpf, $dadosMedicos) {
        $paciente = $this->tabelaPacientes->buscar($cpf);
        
        if (!$paciente['success']) {
            return [
                'success' => false,
                'error' => 'Paciente não encontrado'
            ];
        }
        
        $dadosMedicos['paciente_cpf'] = $cpf;
        $dadosMedicos['paciente_nome'] = $paciente['paciente']['nome'];
        
        $resultado = $this->compressaoHuffman->comprimirDadosMedicos($dadosMedicos);
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Árvore de Huffman',
            'algoritmo' => 'Compressão de Huffman'
        ]);
    }
    
    public function descomprimirDados($textoCodificado, $arvoreSerializada) {
        $resultado = $this->compressaoHuffman->descomprimirDados($textoCodificado, $arvoreSerializada);
        
        return array_merge($resultado, [
            'estrutura_dados' => 'Árvore de Huffman',
            'algoritmo' => 'Descompressão de Huffman'
        ]);
    }
    
    public function visualizarArvoreHuffman() {
        return $this->compressaoHuffman->visualizarArvore();
    }
    
    // ========== DASHBOARD E ESTATÍSTICAS ==========
    
    public function getDashboard() {
        $estatisticasPacientes = $this->tabelaPacientes->getEstatisticas();
        $estatisticasConsultas = $this->arvoreConsultas->getEstatisticas();
        $estatisticasUrgencias = $this->filaUrgencias->getEstatisticas();
        
        return [
            'success' => true,
            'dashboard' => [
                'pacientes' => [
                    'total' => $estatisticasPacientes['elementos_total'],
                    'eficiencia_hash' => $estatisticasPacientes['eficiencia'],
                    'fator_carga' => $estatisticasPacientes['fator_carga'],
                    'colisoes' => $estatisticasPacientes['total_colisoes']
                ],
                'consultas' => [
                    'total' => $estatisticasConsultas['nos_total'],
                    'altura_arvore' => $estatisticasConsultas['altura_arvore'],
                    'balanceada' => $estatisticasConsultas['balanceada'],
                    'eficiencia_altura' => $estatisticasConsultas['eficiencia_altura']
                ],
                'urgencias' => [
                    'total' => $estatisticasUrgencias['elementos_total'],
                    'altura_heap' => $estatisticasUrgencias['altura_heap'],
                    'tempo_medio_espera' => $estatisticasUrgencias['tempo_medio_espera'],
                    'distribuicao' => $estatisticasUrgencias['distribuicao_prioridade']
                ],
                'sistema' => $this->estatisticasGerais,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];
    }
    
    public function getRelatorioCompleto() {
        return [
            'success' => true,
            'relatorio' => [
                'hash_table' => $this->tabelaPacientes->getEstatisticas(),
                'avl_tree' => $this->arvoreConsultas->getEstatisticas(),
                'priority_queue' => $this->filaUrgencias->getEstatisticas(),
                'huffman_stats' => $this->compressaoHuffman->getEstatisticas(),
                'sistema_geral' => $this->estatisticasGerais
            ],
            'visualizacoes' => [
                'hash_distribution' => $this->tabelaPacientes->visualizarDistribuicao(),
                'avl_structure' => $this->arvoreConsultas->visualizarArvore(),
                'heap_structure' => $this->filaUrgencias->visualizarHeap(),
                'huffman_tree' => $this->compressaoHuffman->visualizarArvore()
            ]
        ];
    }
    
    // ========== UTILITÁRIOS ==========
    
    private function validarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11) return false;
        if (preg_match('/^(\d)\1+$/', $cpf)) return false;
        
        // Validação dos dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        
        return true;
    }
    
    private function atualizarEstatisticasGerais($tempo) {
        $this->estatisticasGerais['operacoes_realizadas']++;
        $this->estatisticasGerais['tempo_total_operacoes'] += $tempo;
        $this->estatisticasGerais['tempo_medio_operacao'] = 
            $this->estatisticasGerais['tempo_total_operacoes'] / 
            $this->estatisticasGerais['operacoes_realizadas'];
    }
}
?>