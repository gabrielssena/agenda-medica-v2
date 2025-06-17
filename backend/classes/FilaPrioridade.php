<?php

class FilaPrioridade {
    private $heap;
    private $tamanho;
    private $comparacoes;
    private $trocas;
    
    public function __construct() {
        $this->heap = [];
        $this->tamanho = 0;
        $this->comparacoes = 0;
        $this->trocas = 0;
    }
    
    /**
     * Inserir elemento na fila de prioridade (Min-Heap)
     * Prioridade: 1 = Emergência (maior prioridade), 2 = Urgente, 3 = Normal
     */
    public function inserir($cpf, $nome, $prioridade, $descricao = '') {
        $inicio = microtime(true);
        $this->comparacoes = 0;
        $this->trocas = 0;
        
        $elemento = [
            'cpf' => $cpf,
            'nome' => $nome,
            'prioridade' => $prioridade,
            'descricao' => $descricao,
            'timestamp' => time(),
            'data_entrada' => date('Y-m-d H:i:s'),
            'posicao_inicial' => $this->tamanho
        ];
        
        // Inserir no final do heap
        $this->heap[$this->tamanho] = $elemento;
        $this->tamanho++;
        
        // Subir o elemento para manter propriedade do heap
        $this->subirHeap($this->tamanho - 1);
        
        $tempo = (microtime(true) - $inicio) * 1000;
        
        return [
            'success' => true,
            'operacao' => 'inserido',
            'posicao_final' => $this->encontrarPosicao($cpf),
            'performance' => [
                'tempo_insercao_ms' => $tempo,
                'comparacoes' => $this->comparacoes,
                'trocas' => $this->trocas,
                'tamanho_fila' => $this->tamanho
            ]
        ];
    }
    
    /**
     * Remover elemento de maior prioridade (raiz do heap)
     */
    public function removerProximo() {
        if ($this->tamanho === 0) {
            return [
                'success' => false,
                'message' => 'Fila vazia'
            ];
        }
        
        $inicio = microtime(true);
        $this->comparacoes = 0;
        $this->trocas = 0;
        
        // Salvar elemento de maior prioridade
        $proximoPaciente = $this->heap[0];
        
        // Mover último elemento para raiz
        $this->heap[0] = $this->heap[$this->tamanho - 1];
        $this->tamanho--;
        
        // Descer o elemento para manter propriedade do heap
        if ($this->tamanho > 0) {
            $this->descerHeap(0);
        }
        
        $tempo = (microtime(true) - $inicio) * 1000;
        
        return [
            'success' => true,
            'paciente' => $proximoPaciente,
            'performance' => [
                'tempo_remocao_ms' => $tempo,
                'comparacoes' => $this->comparacoes,
                'trocas' => $this->trocas,
                'tamanho_fila' => $this->tamanho
            ]
        ];
    }
    
    /**
     * Subir elemento no heap (usado na inserção)
     */
    private function subirHeap($indice) {
        while ($indice > 0) {
            $indicePai = intval(($indice - 1) / 2);
            
            $this->comparacoes++;
            
            // Comparar prioridades (menor valor = maior prioridade)
            if ($this->compararPrioridade($this->heap[$indice], $this->heap[$indicePai]) >= 0) {
                break;
            }
            
            // Trocar com pai
            $this->trocar($indice, $indicePai);
            $indice = $indicePai;
        }
    }
    
    /**
     * Descer elemento no heap (usado na remoção)
     */
    private function descerHeap($indice) {
        while (true) {
            $menorIndice = $indice;
            $filhoEsquerdo = 2 * $indice + 1;
            $filhoDireito = 2 * $indice + 2;
            
            // Comparar com filho esquerdo
            if ($filhoEsquerdo < $this->tamanho) {
                $this->comparacoes++;
                if ($this->compararPrioridade($this->heap[$filhoEsquerdo], $this->heap[$menorIndice]) < 0) {
                    $menorIndice = $filhoEsquerdo;
                }
            }
            
            // Comparar com filho direito
            if ($filhoDireito < $this->tamanho) {
                $this->comparacoes++;
                if ($this->compararPrioridade($this->heap[$filhoDireito], $this->heap[$menorIndice]) < 0) {
                    $menorIndice = $filhoDireito;
                }
            }
            
            // Se não precisa trocar, heap está correto
            if ($menorIndice === $indice) {
                break;
            }
            
            // Trocar e continuar descendo
            $this->trocar($indice, $menorIndice);
            $indice = $menorIndice;
        }
    }
    
    /**
     * Comparar prioridade entre dois elementos
     * Retorna: < 0 se a tem maior prioridade que b
     *          > 0 se b tem maior prioridade que a
     *          = 0 se têm mesma prioridade
     */
    private function compararPrioridade($a, $b) {
        // Primeiro critério: prioridade (1 = emergência, 2 = urgente, 3 = normal)
        if ($a['prioridade'] !== $b['prioridade']) {
            return $a['prioridade'] - $b['prioridade'];
        }
        
        // Segundo critério: timestamp (FIFO para mesma prioridade)
        return $a['timestamp'] - $b['timestamp'];
    }
    
    /**
     * Trocar dois elementos no heap
     */
    private function trocar($i, $j) {
        $temp = $this->heap[$i];
        $this->heap[$i] = $this->heap[$j];
        $this->heap[$j] = $temp;
        $this->trocas++;
    }
    
    /**
     * Visualizar próximo elemento sem remover
     */
    public function verProximo() {
        if ($this->tamanho === 0) {
            return [
                'success' => false,
                'message' => 'Fila vazia'
            ];
        }
        
        return [
            'success' => true,
            'proximo_paciente' => $this->heap[0],
            'posicao' => 1,
            'tamanho_fila' => $this->tamanho
        ];
    }
    
    /**
     * Listar toda a fila em ordem de prioridade
     */
    public function listarFila() {
        $fila = [];
        
        // Criar cópia do heap para não modificar original
        $heapCopia = array_slice($this->heap, 0, $this->tamanho);
        
        // Extrair elementos em ordem de prioridade
        for ($i = 0; $i < $this->tamanho; $i++) {
            $fila[] = array_merge($heapCopia[$i], [
                'posicao_fila' => $i + 1,
                'tempo_espera' => $this->calcularTempoEspera($heapCopia[$i]['timestamp'])
            ]);
        }
        
        // Ordenar por prioridade real (simulando heap sort)
        usort($fila, function($a, $b) {
            return $this->compararPrioridade($a, $b);
        });
        
        return [
            'success' => true,
            'fila' => $fila,
            'total' => $this->tamanho,
            'estrutura' => [
                'tipo' => 'Min-Heap',
                'altura' => $this->calcularAltura(),
                'completo' => $this->verificarCompleto()
            ]
        ];
    }
    
    /**
     * Remover paciente específico da fila
     */
    public function removerPaciente($cpf) {
        $inicio = microtime(true);
        $indice = $this->encontrarIndice($cpf);
        
        if ($indice === -1) {
            return [
                'success' => false,
                'message' => 'Paciente não encontrado na fila'
            ];
        }
        
        // Mover último elemento para posição do removido
        $this->heap[$indice] = $this->heap[$this->tamanho - 1];
        $this->tamanho--;
        
        // Rebalancear heap
        if ($this->tamanho > 0 && $indice < $this->tamanho) {
            // Tentar subir primeiro
            $this->subirHeap($indice);
            // Depois tentar descer
            $this->descerHeap($indice);
        }
        
        $tempo = (microtime(true) - $inicio) * 1000;
        
        return [
            'success' => true,
            'message' => 'Paciente removido da fila',
            'performance' => [
                'tempo_remocao_ms' => $tempo,
                'tamanho_fila' => $this->tamanho
            ]
        ];
    }
    
    /**
     * Encontrar índice de um paciente no heap
     */
    private function encontrarIndice($cpf) {
        for ($i = 0; $i < $this->tamanho; $i++) {
            if ($this->heap[$i]['cpf'] === $cpf) {
                return $i;
            }
        }
        return -1;
    }
    
    /**
     * Encontrar posição de um paciente na fila ordenada
     */
    private function encontrarPosicao($cpf) {
        $filaOrdenada = $this->listarFila()['fila'];
        
        foreach ($filaOrdenada as $posicao => $paciente) {
            if ($paciente['cpf'] === $cpf) {
                return $posicao + 1;
            }
        }
        
        return -1;
    }
    
    /**
     * Calcular tempo de espera
     */
    private function calcularTempoEspera($timestamp) {
        $agora = time();
        $diferenca = $agora - $timestamp;
        
        if ($diferenca < 60) {
            return $diferenca . ' segundos';
        } elseif ($diferenca < 3600) {
            return floor($diferenca / 60) . ' minutos';
        } else {
            $horas = floor($diferenca / 3600);
            $minutos = floor(($diferenca % 3600) / 60);
            return $horas . 'h ' . $minutos . 'min';
        }
    }
    
    /**
     * Calcular altura do heap
     */
    private function calcularAltura() {
        if ($this->tamanho === 0) return 0;
        return floor(log($this->tamanho, 2)) + 1;
    }
    
    /**
     * Verificar se heap está completo
     */
    private function verificarCompleto() {
        if ($this->tamanho === 0) return true;
        
        $altura = $this->calcularAltura();
        $nosMinimos = pow(2, $altura - 1);
        
        return $this->tamanho >= $nosMinimos;
    }
    
    /**
     * Contar pacientes por prioridade
     */
    public function contarPorPrioridade() {
        $contadores = [1 => 0, 2 => 0, 3 => 0];
        
        for ($i = 0; $i < $this->tamanho; $i++) {
            $prioridade = $this->heap[$i]['prioridade'];
            if (isset($contadores[$prioridade])) {
                $contadores[$prioridade]++;
            }
        }
        
        return [
            'emergencia' => $contadores[1],
            'urgente' => $contadores[2],
            'normal' => $contadores[3],
            'total' => $this->tamanho
        ];
    }
    
    /**
     * Estatísticas detalhadas da fila de prioridade
     */
    public function getEstatisticas() {
        $contadores = $this->contarPorPrioridade();
        
        return [
            'elementos_total' => $this->tamanho,
            'altura_heap' => $this->calcularAltura(),
            'heap_completo' => $this->verificarCompleto(),
            'distribuicao_prioridade' => $contadores,
            'tempo_medio_espera' => $this->calcularTempoMedioEspera(),
            'eficiencia_heap' => $this->calcularEficienciaHeap(),
            'operacoes_realizadas' => [
                'comparacoes_total' => $this->comparacoes,
                'trocas_total' => $this->trocas
            ]
        ];
    }
    
    /**
     * Calcular tempo médio de espera
     */
    private function calcularTempoMedioEspera() {
        if ($this->tamanho === 0) return 0;
        
        $tempoTotal = 0;
        $agora = time();
        
        for ($i = 0; $i < $this->tamanho; $i++) {
            $tempoTotal += ($agora - $this->heap[$i]['timestamp']);
        }
        
        return round($tempoTotal / $this->tamanho / 60, 2); // em minutos
    }
    
    /**
     * Calcular eficiência do heap
     */
    private function calcularEficienciaHeap() {
        if ($this->tamanho <= 1) return 100;
        
        $alturaAtual = $this->calcularAltura();
        $alturaOtima = floor(log($this->tamanho, 2)) + 1;
        
        return round(($alturaOtima / $alturaAtual) * 100, 2);
    }
    
    /**
     * Visualizar estrutura do heap
     */
    public function visualizarHeap() {
        if ($this->tamanho === 0) {
            return [
                'estrutura' => ['Heap vazio'],
                'propriedades' => [
                    'tipo' => 'Min-Heap (menor prioridade = maior urgência)',
                    'altura' => 0,
                    'completo' => 'N/A',
                    'elementos' => 0
                ]
            ];
        }
        
        $visualizacao = [];
        $this->construirVisualizacaoHeap(0, $visualizacao, '', true);
        
        return [
            'estrutura' => $visualizacao,
            'propriedades' => [
                'tipo' => 'Min-Heap (menor prioridade = maior urgência)',
                'altura' => $this->calcularAltura(),
                'completo' => $this->verificarCompleto() ? 'Sim' : 'Não',
                'elementos' => $this->tamanho
            ]
        ];
    }
    
    /**
     * Construir visualização do heap recursivamente
     */
    private function construirVisualizacaoHeap($indice, &$visualizacao, $prefixo, $ehUltimo) {
        if ($indice < $this->tamanho) {
            $elemento = $this->heap[$indice];
            $simbolo = $ehUltimo ? '└── ' : '├── ';
            
            $prioridadeTexto = $this->getPrioridadeTexto($elemento['prioridade']);
            $visualizacao[] = $prefixo . $simbolo . $elemento['nome'] . ' (' . $prioridadeTexto . ')';
            
            $novoPrefixo = $prefixo . ($ehUltimo ? '    ' : '│   ');
            
            $filhoEsquerdo = 2 * $indice + 1;
            $filhoDireito = 2 * $indice + 2;
            
            if ($filhoEsquerdo < $this->tamanho || $filhoDireito < $this->tamanho) {
                if ($filhoDireito < $this->tamanho) {
                    $this->construirVisualizacaoHeap($filhoDireito, $visualizacao, $novoPrefixo, $filhoEsquerdo >= $this->tamanho);
                }
                if ($filhoEsquerdo < $this->tamanho) {
                    $this->construirVisualizacaoHeap($filhoEsquerdo, $visualizacao, $novoPrefixo, true);
                }
            }
        }
    }
    
    /**
     * Converter prioridade numérica para texto
     */
    private function getPrioridadeTexto($prioridade) {
        switch ($prioridade) {
            case 1: return 'Emergência';
            case 2: return 'Urgente';
            case 3: return 'Normal';
            default: return 'Indefinida';
        }
    }
    
    /**
     * Verificar integridade do heap
     */
    public function verificarIntegridade() {
        for ($i = 0; $i < $this->tamanho; $i++) {
            $filhoEsquerdo = 2 * $i + 1;
            $filhoDireito = 2 * $i + 2;
            
            // Verificar propriedade do min-heap
            if ($filhoEsquerdo < $this->tamanho) {
                if ($this->compararPrioridade($this->heap[$i], $this->heap[$filhoEsquerdo]) > 0) {
                    return false;
                }
            }
            
            if ($filhoDireito < $this->tamanho) {
                if ($this->compararPrioridade($this->heap[$i], $this->heap[$filhoDireito]) > 0) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Verificar se fila está vazia
     */
    public function estaVazia() {
        return $this->tamanho === 0;
    }
    
    /**
     * Obter tamanho da fila
     */
    public function getTamanho() {
        return $this->tamanho;
    }
}
?>