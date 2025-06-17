<?php

class NoHuffman {
    public $caractere;
    public $frequencia;
    public $esquerda;
    public $direita;
    public $ehFolha;
    
    public function __construct($caractere = null, $frequencia = 0, $esquerda = null, $direita = null) {
        $this->caractere = $caractere;
        $this->frequencia = $frequencia;
        $this->esquerda = $esquerda;
        $this->direita = $direita;
        $this->ehFolha = ($esquerda === null && $direita === null);
    }
}

class HuffmanCompression {
    private $tabelaFrequencia;
    private $arvoreHuffman;
    private $codigosHuffman;
    private $estatisticas;
    
    public function __construct() {
        $this->tabelaFrequencia = [];
        $this->arvoreHuffman = null;
        $this->codigosHuffman = [];
        $this->estatisticas = [];
    }
    
    /**
     * Comprimir dados médicos usando algoritmo de Huffman
     */
    public function comprimirDadosMedicos($dadosMedicos) {
        $inicio = microtime(true);
        
        // Preparar texto para compressão
        $textoCompleto = $this->preparaTextoMedico($dadosMedicos);
        
        if (empty($textoCompleto)) {
            return [
                'success' => false,
                'error' => 'Dados médicos vazios ou inválidos'
            ];
        }
        
        // Etapa 1: Construir tabela de frequência
        $this->construirTabelaFrequencia($textoCompleto);
        
        // Etapa 2: Construir árvore de Huffman
        $this->construirArvoreHuffman();
        
        // Etapa 3: Gerar códigos de Huffman
        $this->gerarCodigosHuffman();
        
        // Etapa 4: Codificar texto
        $textoCodificado = $this->codificarTexto($textoCompleto);
        
        // Etapa 5: Calcular estatísticas
        $this->calcularEstatisticas($textoCompleto, $textoCodificado);
        
        $tempoTotal = (microtime(true) - $inicio) * 1000;
        
        return [
            'success' => true,
            'dados_originais' => $dadosMedicos,
            'texto_original' => $textoCompleto,
            'texto_codificado' => $textoCodificado,
            'tabela_frequencia' => $this->tabelaFrequencia,
            'codigos_huffman' => $this->codigosHuffman,
            'arvore_huffman' => $this->serializarArvore($this->arvoreHuffman),
            'estatisticas' => array_merge($this->estatisticas, [
                'tempo_compressao_ms' => $tempoTotal
            ]),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Descomprimir dados usando árvore de Huffman
     */
    public function descomprimirDados($textoCodificado, $arvoreSerializada) {
        $inicio = microtime(true);
        
        // Reconstruir árvore de Huffman
        $this->arvoreHuffman = $this->deserializarArvore($arvoreSerializada);
        
        if (!$this->arvoreHuffman) {
            return [
                'success' => false,
                'error' => 'Árvore de Huffman inválida'
            ];
        }
        
        // Decodificar texto
        $textoDecodificado = $this->decodificarTexto($textoCodificado);
        
        // Reconstruir dados médicos estruturados
        $dadosReconstruidos = $this->reconstruirDadosMedicos($textoDecodificado);
        
        $tempoTotal = (microtime(true) - $inicio) * 1000;
        
        return [
            'success' => true,
            'texto_decodificado' => $textoDecodificado,
            'dados_reconstruidos' => $dadosReconstruidos,
            'tempo_descompressao_ms' => $tempoTotal
        ];
    }
    
    /**
     * Preparar texto médico para compressão
     */
    private function preparaTextoMedico($dados) {
        $texto = '';
        
        $campos = [
            'historico_medico' => 'HISTÓRICO',
            'sintomas' => 'SINTOMAS',
            'diagnostico' => 'DIAGNÓSTICO',
            'prescricao' => 'PRESCRIÇÃO',
            'exames' => 'EXAMES',
            'observacoes' => 'OBSERVAÇÕES',
            'anotacoes' => 'ANOTAÇÕES'
        ];
        
        foreach ($campos as $campo => $prefixo) {
            if (isset($dados[$campo]) && !empty($dados[$campo])) {
                $texto .= $prefixo . ': ' . $dados[$campo] . '\n';
            }
        }
        
        // Adicionar metadados se disponíveis
        if (isset($dados['paciente_cpf'])) {
            $texto .= 'CPF: ' . $dados['paciente_cpf'] . '\n';
        }
        
        if (isset($dados['data_consulta'])) {
            $texto .= 'DATA: ' . $dados['data_consulta'] . '\n';
        }
        
        return trim($texto);
    }
    
    /**
     * Construir tabela de frequência dos caracteres
     */
    private function construirTabelaFrequencia($texto) {
        $this->tabelaFrequencia = [];
        
        for ($i = 0; $i < strlen($texto); $i++) {
            $char = $texto[$i];
            
            if (!isset($this->tabelaFrequencia[$char])) {
                $this->tabelaFrequencia[$char] = 0;
            }
            
            $this->tabelaFrequencia[$char]++;
        }
        
        // Ordenar por frequência (opcional, para análise)
        arsort($this->tabelaFrequencia);
    }
    
    /**
     * Construir árvore de Huffman usando heap de prioridade
     */
    private function construirArvoreHuffman() {
        // Criar heap de prioridade (min-heap)
        $heap = new SplPriorityQueue();
        $heap->setExtractFlags(SplPriorityQueue::EXTR_DATA);
        
        // Inserir todos os caracteres como nós folha
        foreach ($this->tabelaFrequencia as $char => $freq) {
            $no = new NoHuffman($char, $freq);
            $heap->insert($no, -$freq); // Negativo para min-heap
        }
        
        // Construir árvore bottom-up
        while ($heap->count() > 1) {
            // Extrair dois nós de menor frequência
            $no1 = $heap->extract();
            $no2 = $heap->extract();
            
            // Criar nó interno
            $noInterno = new NoHuffman(
                null, 
                $no1->frequencia + $no2->frequencia,
                $no1,
                $no2
            );
            
            // Inserir nó interno de volta no heap
            $heap->insert($noInterno, -$noInterno->frequencia);
        }
        
        // Raiz da árvore
        $this->arvoreHuffman = $heap->extract();
    }
    
    /**
     * Gerar códigos de Huffman para cada caractere
     */
    private function gerarCodigosHuffman() {
        $this->codigosHuffman = [];
        
        if ($this->arvoreHuffman) {
            // Caso especial: apenas um caractere único
            if ($this->arvoreHuffman->ehFolha) {
                $this->codigosHuffman[$this->arvoreHuffman->caractere] = '0';
            } else {
                $this->gerarCodigosRecursivo($this->arvoreHuffman, '');
            }
        }
    }
    
    private function gerarCodigosRecursivo($no, $codigo) {
        if ($no->ehFolha) {
            $this->codigosHuffman[$no->caractere] = $codigo;
        } else {
            if ($no->esquerda) {
                $this->gerarCodigosRecursivo($no->esquerda, $codigo . '0');
            }
            if ($no->direita) {
                $this->gerarCodigosRecursivo($no->direita, $codigo . '1');
            }
        }
    }
    
    /**
     * Codificar texto usando códigos de Huffman
     */
    private function codificarTexto($texto) {
        $textoCodificado = '';
        
        for ($i = 0; $i < strlen($texto); $i++) {
            $char = $texto[$i];
            
            if (isset($this->codigosHuffman[$char])) {
                $textoCodificado .= $this->codigosHuffman[$char];
            }
        }
        
        return $textoCodificado;
    }
    
    /**
     * Decodificar texto usando árvore de Huffman
     */
    private function decodificarTexto($textoCodificado) {
        $textoDecodificado = '';
        $noAtual = $this->arvoreHuffman;
        
        for ($i = 0; $i < strlen($textoCodificado); $i++) {
            $bit = $textoCodificado[$i];
            
            // Navegar na árvore
            if ($bit === '0') {
                $noAtual = $noAtual->esquerda;
            } else {
                $noAtual = $noAtual->direita;
            }
            
            // Se chegou em uma folha, adicionar caractere
            if ($noAtual && $noAtual->ehFolha) {
                $textoDecodificado .= $noAtual->caractere;
                $noAtual = $this->arvoreHuffman; // Voltar para raiz
            }
        }
        
        return $textoDecodificado;
    }
    
    /**
     * Calcular estatísticas de compressão
     */
    private function calcularEstatisticas($textoOriginal, $textoCodificado) {
        $tamanhoOriginal = strlen($textoOriginal) * 8; // bits
        $tamanhoCodificado = strlen($textoCodificado); // bits
        
        $this->estatisticas = [
            'caracteres_unicos' => count($this->tabelaFrequencia),
            'tamanho_original_chars' => strlen($textoOriginal),
            'tamanho_original_bits' => $tamanhoOriginal,
            'tamanho_codificado_bits' => $tamanhoCodificado,
            'taxa_compressao' => $tamanhoOriginal > 0 ? round($tamanhoCodificado / $tamanhoOriginal, 4) : 0,
            'economia_percentual' => $tamanhoOriginal > 0 ? round((1 - $tamanhoCodificado / $tamanhoOriginal) * 100, 2) : 0,
            'economia_bits' => $tamanhoOriginal - $tamanhoCodificado,
            'comprimento_medio_codigo' => $this->calcularComprimentoMedioCodigo(),
            'entropia' => $this->calcularEntropia(),
            'eficiencia_huffman' => $this->calcularEficienciaHuffman()
        ];
    }
    
    /**
     * Calcular comprimento médio dos códigos de Huffman
     */
    private function calcularComprimentoMedioCodigo() {
        $totalCaracteres = array_sum($this->tabelaFrequencia);
        $comprimentoMedio = 0;
        
        foreach ($this->tabelaFrequencia as $char => $freq) {
            if (isset($this->codigosHuffman[$char])) {
                $comprimento = strlen($this->codigosHuffman[$char]);
                $probabilidade = $freq / $totalCaracteres;
                $comprimentoMedio += $probabilidade * $comprimento;
            }
        }
        
        return round($comprimentoMedio, 4);
    }
    
    /**
     * Calcular entropia do texto
     */
    private function calcularEntropia() {
        $totalCaracteres = array_sum($this->tabelaFrequencia);
        $entropia = 0;
        
        foreach ($this->tabelaFrequencia as $freq) {
            $probabilidade = $freq / $totalCaracteres;
            if ($probabilidade > 0) {
                $entropia -= $probabilidade * log($probabilidade, 2);
            }
        }
        
        return round($entropia, 4);
    }
    
    /**
     * Calcular eficiência do algoritmo de Huffman
     */
    private function calcularEficienciaHuffman() {
        $entropia = $this->calcularEntropia();
        $comprimentoMedio = $this->calcularComprimentoMedioCodigo();
        
        if ($comprimentoMedio > 0) {
            return round(($entropia / $comprimentoMedio) * 100, 2);
        }
        
        return 0;
    }
    
    /**
     * Serializar árvore de Huffman para transmissão/armazenamento
     */
    private function serializarArvore($no) {
        if (!$no) {
            return null;
        }
        
        if ($no->ehFolha) {
            return [
                'tipo' => 'folha',
                'caractere' => $no->caractere,
                'frequencia' => $no->frequencia
            ];
        }
        
        return [
            'tipo' => 'interno',
            'frequencia' => $no->frequencia,
            'esquerda' => $this->serializarArvore($no->esquerda),
            'direita' => $this->serializarArvore($no->direita)
        ];
    }
    
    /**
     * Deserializar árvore de Huffman
     */
    private function deserializarArvore($dados) {
        if (!$dados) {
            return null;
        }
        
        if ($dados['tipo'] === 'folha') {
            return new NoHuffman($dados['caractere'], $dados['frequencia']);
        }
        
        $esquerda = $this->deserializarArvore($dados['esquerda']);
        $direita = $this->deserializarArvore($dados['direita']);
        
        return new NoHuffman(null, $dados['frequencia'], $esquerda, $direita);
    }
    
    /**
     * Reconstruir dados médicos estruturados a partir do texto
     */
    private function reconstruirDadosMedicos($texto) {
        $dados = [];
        $linhas = explode('\n', $texto);
        
        foreach ($linhas as $linha) {
            if (strpos($linha, 'HISTÓRICO: ') === 0) {
                $dados['historico_medico'] = substr($linha, 11);
            } elseif (strpos($linha, 'SINTOMAS: ') === 0) {
                $dados['sintomas'] = substr($linha, 10);
            } elseif (strpos($linha, 'DIAGNÓSTICO: ') === 0) {
                $dados['diagnostico'] = substr($linha, 13);
            } elseif (strpos($linha, 'PRESCRIÇÃO: ') === 0) {
                $dados['prescricao'] = substr($linha, 12);
            } elseif (strpos($linha, 'EXAMES: ') === 0) {
                $dados['exames'] = substr($linha, 8);
            } elseif (strpos($linha, 'OBSERVAÇÕES: ') === 0) {
                $dados['observacoes'] = substr($linha, 13);
            } elseif (strpos($linha, 'ANOTAÇÕES: ') === 0) {
                $dados['anotacoes'] = substr($linha, 11);
            } elseif (strpos($linha, 'CPF: ') === 0) {
                $dados['paciente_cpf'] = substr($linha, 5);
            } elseif (strpos($linha, 'DATA: ') === 0) {
                $dados['data_consulta'] = substr($linha, 6);
            }
        }
        
        return $dados;
    }
    
    /**
     * Visualizar árvore de Huffman
     */
    public function visualizarArvore() {
        if (!$this->arvoreHuffman) {
            return ['estrutura' => 'Árvore não construída'];
        }
        
        $visualizacao = [];
        $this->construirVisualizacaoArvore($this->arvoreHuffman, $visualizacao, '', true);
        
        return [
            'estrutura' => $visualizacao,
            'estatisticas_arvore' => [
                'altura' => $this->calcularAlturaArvore($this->arvoreHuffman),
                'nos_internos' => $this->contarNosInternos($this->arvoreHuffman),
                'nos_folha' => count($this->codigosHuffman)
            ]
        ];
    }
    
    private function construirVisualizacaoArvore($no, &$visualizacao, $prefixo, $ehUltimo) {
        if ($no) {
            $simbolo = $ehUltimo ? '└── ' : '├── ';
            
            if ($no->ehFolha) {
                $char = $no->caractere === '\n' ? '\n' : $no->caractere;
                $codigo = isset($this->codigosHuffman[$no->caractere]) ? $this->codigosHuffman[$no->caractere] : '';
                $visualizacao[] = $prefixo . $simbolo . "'" . $char . "' (freq: " . $no->frequencia . ", código: " . $codigo . ")";
            } else {
                $visualizacao[] = $prefixo . $simbolo . "Interno (freq: " . $no->frequencia . ")";
                
                $novoPrefixo = $prefixo . ($ehUltimo ? '    ' : '│   ');
                
                if ($no->esquerda || $no->direita) {
                    if ($no->direita) {
                        $this->construirVisualizacaoArvore($no->direita, $visualizacao, $novoPrefixo, !$no->esquerda);
                    }
                    if ($no->esquerda) {
                        $this->construirVisualizacaoArvore($no->esquerda, $visualizacao, $novoPrefixo, true);
                    }
                }
            }
        }
    }
    
    private function calcularAlturaArvore($no) {
        if (!$no) return 0;
        if ($no->ehFolha) return 1;
        
        $alturaEsq = $this->calcularAlturaArvore($no->esquerda);
        $alturaDir = $this->calcularAlturaArvore($no->direita);
        
        return 1 + max($alturaEsq, $alturaDir);
    }
    
    private function contarNosInternos($no) {
        if (!$no || $no->ehFolha) return 0;
        
        return 1 + $this->contarNosInternos($no->esquerda) + $this->contarNosInternos($no->direita);
    }
    
    /**
     * Obter estatísticas completas
     */
    public function getEstatisticas() {
        return $this->estatisticas;
    }
    
    /**
     * Obter tabela de códigos de Huffman
     */
    public function getCodigosHuffman() {
        return $this->codigosHuffman;
    }
    
    /**
     * Obter tabela de frequência
     */
    public function getTabelaFrequencia() {
        return $this->tabelaFrequencia;
    }
}
?>