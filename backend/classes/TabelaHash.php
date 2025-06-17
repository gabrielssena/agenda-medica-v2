<?php

class TabelaHash {
    private $buckets;
    private $tamanho;
    private $numeroElementos;
    private $fatorCarga;
    private $colisoes;
    
    public function __construct($tamanho = 1009) { // Número primo para melhor distribuição
        $this->tamanho = $tamanho;
        $this->buckets = array_fill(0, $tamanho, []);
        $this->numeroElementos = 0;
        $this->colisoes = 0;
        $this->fatorCarga = 0.0;
    }
    
    /**
     * Função hash usando método da divisão com tratamento de colisões
     */
    private function hash($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $soma = 0;
        
        // Aplicar peso aos dígitos para melhor distribuição
        for ($i = 0; $i < strlen($cpf); $i++) {
            $soma += (int)$cpf[$i] * ($i + 1);
        }
        
        return $soma % $this->tamanho;
    }
    
    /**
     * Função hash secundária para double hashing
     */
    private function hashSecundario($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $soma = 0;
        
        for ($i = 0; $i < strlen($cpf); $i++) {
            $soma += (int)$cpf[$i];
        }
        
        return 7 - ($soma % 7); // Número primo menor que tamanho
    }
    
    /**
     * Inserir paciente na tabela hash
     */
    public function inserir($cpf, $dadosPaciente) {
        $indice = $this->hash($cpf);
        $dadosPaciente['cpf'] = $cpf;
        $dadosPaciente['timestamp_insercao'] = microtime(true);
        
        // Verificar se já existe (atualizar)
        foreach ($this->buckets[$indice] as $key => $paciente) {
            if ($paciente['cpf'] === $cpf) {
                $this->buckets[$indice][$key] = $dadosPaciente;
                return [
                    'success' => true,
                    'operacao' => 'atualizado',
                    'indice' => $indice,
                    'colisoes' => count($this->buckets[$indice]) - 1
                ];
            }
        }
        
        // Inserir novo
        if (count($this->buckets[$indice]) > 0) {
            $this->colisoes++;
        }
        
        $this->buckets[$indice][] = $dadosPaciente;
        $this->numeroElementos++;
        $this->fatorCarga = $this->numeroElementos / $this->tamanho;
        
        // Redimensionar se fator de carga > 0.75
        if ($this->fatorCarga > 0.75) {
            $this->redimensionar();
        }
        
        return [
            'success' => true,
            'operacao' => 'inserido',
            'indice' => $indice,
            'colisoes' => count($this->buckets[$indice]) - 1,
            'fator_carga' => $this->fatorCarga
        ];
    }
    
    /**
     * Buscar paciente por CPF - O(1) médio
     */
    public function buscar($cpf) {
        $inicio = microtime(true);
        $indice = $this->hash($cpf);
        $comparacoes = 0;
        
        foreach ($this->buckets[$indice] as $paciente) {
            $comparacoes++;
            if ($paciente['cpf'] === $cpf) {
                $tempo = (microtime(true) - $inicio) * 1000; // ms
                
                return [
                    'success' => true,
                    'paciente' => $paciente,
                    'performance' => [
                        'tempo_busca_ms' => $tempo,
                        'comparacoes' => $comparacoes,
                        'indice_hash' => $indice,
                        'colisoes_bucket' => count($this->buckets[$indice]) - 1
                    ]
                ];
            }
        }
        
        $tempo = (microtime(true) - $inicio) * 1000;
        return [
            'success' => false,
            'message' => 'Paciente não encontrado',
            'performance' => [
                'tempo_busca_ms' => $tempo,
                'comparacoes' => $comparacoes,
                'indice_hash' => $indice
            ]
        ];
    }
    
    /**
     * Remover paciente
     */
    public function remover($cpf) {
        $indice = $this->hash($cpf);
        
        foreach ($this->buckets[$indice] as $key => $paciente) {
            if ($paciente['cpf'] === $cpf) {
                unset($this->buckets[$indice][$key]);
                $this->buckets[$indice] = array_values($this->buckets[$indice]);
                $this->numeroElementos--;
                $this->fatorCarga = $this->numeroElementos / $this->tamanho;
                
                return [
                    'success' => true,
                    'message' => 'Paciente removido com sucesso'
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Paciente não encontrado'
        ];
    }
    
    /**
     * Listar todos os pacientes
     */
    public function listarTodos() {
        $pacientes = [];
        
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as $paciente) {
                $pacientes[] = $paciente;
            }
        }
        
        return $pacientes;
    }
    
    /**
     * Redimensionar tabela hash quando fator de carga é alto
     */
    private function redimensionar() {
        $tabelaAntiga = $this->buckets;
        $this->tamanho = $this->proximoPrimo($this->tamanho * 2);
        $this->buckets = array_fill(0, $this->tamanho, []);
        $this->numeroElementos = 0;
        $this->colisoes = 0;
        
        // Reinserir todos os elementos
        foreach ($tabelaAntiga as $bucket) {
            foreach ($bucket as $paciente) {
                $this->inserir($paciente['cpf'], $paciente);
            }
        }
    }
    
    /**
     * Encontrar próximo número primo
     */
    private function proximoPrimo($n) {
        while (!$this->ehPrimo($n)) {
            $n++;
        }
        return $n;
    }
    
    /**
     * Verificar se número é primo
     */
    private function ehPrimo($n) {
        if ($n < 2) return false;
        if ($n == 2) return true;
        if ($n % 2 == 0) return false;
        
        for ($i = 3; $i <= sqrt($n); $i += 2) {
            if ($n % $i == 0) return false;
        }
        
        return true;
    }
    
    /**
     * Estatísticas detalhadas da tabela hash
     */
    public function getEstatisticas() {
        $bucketsVazios = 0;
        $bucketsComColisao = 0;
        $maxColisoes = 0;
        $totalColisoes = 0;
        $distribuicao = [];
        
        foreach ($this->buckets as $bucket) {
            $tamanho = count($bucket);
            
            if ($tamanho == 0) {
                $bucketsVazios++;
            } elseif ($tamanho > 1) {
                $bucketsComColisao++;
                $totalColisoes += ($tamanho - 1);
            }
            
            $maxColisoes = max($maxColisoes, $tamanho);
            
            if (!isset($distribuicao[$tamanho])) {
                $distribuicao[$tamanho] = 0;
            }
            $distribuicao[$tamanho]++;
        }
        
        return [
            'elementos_total' => $this->numeroElementos,
            'tamanho_tabela' => $this->tamanho,
            'fator_carga' => round($this->fatorCarga, 4),
            'buckets_vazios' => $bucketsVazios,
            'buckets_ocupados' => $this->tamanho - $bucketsVazios,
            'buckets_com_colisao' => $bucketsComColisao,
            'total_colisoes' => $totalColisoes,
            'max_colisoes_bucket' => $maxColisoes,
            'taxa_colisao' => $this->numeroElementos > 0 ? round(($totalColisoes / $this->numeroElementos) * 100, 2) : 0,
            'distribuicao' => $distribuicao,
            'eficiencia' => $this->calcularEficiencia()
        ];
    }
    
    /**
     * Calcular eficiência da tabela hash
     */
    private function calcularEficiencia() {
        if ($this->numeroElementos == 0) return 100;
        
        $buscasOtimas = $this->numeroElementos; // O(1) para cada elemento
        $buscasReais = $this->numeroElementos + $this->colisoes;
        
        return round(($buscasOtimas / $buscasReais) * 100, 2);
    }
    
    /**
     * Visualizar distribuição da tabela hash
     */
    public function visualizarDistribuicao() {
        $visualizacao = [];
        
        for ($i = 0; $i < min($this->tamanho, 50); $i++) { // Mostrar apenas primeiros 50
            $tamanho = count($this->buckets[$i]);
            $visualizacao[] = [
                'indice' => $i,
                'elementos' => $tamanho,
                'representacao' => str_repeat('█', $tamanho) . str_repeat('░', max(0, 5 - $tamanho))
            ];
        }
        
        return $visualizacao;
    }
}
?>