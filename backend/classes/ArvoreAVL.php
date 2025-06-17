<?php

class NoAVL {
    public $chave;
    public $dados;
    public $altura;
    public $esquerda;
    public $direita;
    public $fatorBalanceamento;
    
    public function __construct($chave, $dados) {
        $this->chave = $chave;
        $this->dados = $dados;
        $this->altura = 1;
        $this->esquerda = null;
        $this->direita = null;
        $this->fatorBalanceamento = 0;
    }
}

class ArvoreAVL {
    private $raiz;
    private $numeroNos;
    private $rotacoes;
    private $comparacoes;
    
    public function __construct() {
        $this->raiz = null;
        $this->numeroNos = 0;
        $this->rotacoes = 0;
        $this->comparacoes = 0;
    }
    
    /**
     * Obter altura do nó
     */
    private function altura($no) {
        return $no ? $no->altura : 0;
    }
    
    /**
     * Calcular fator de balanceamento
     */
    private function fatorBalanceamento($no) {
        return $no ? $this->altura($no->esquerda) - $this->altura($no->direita) : 0;
    }
    
    /**
     * Atualizar altura e fator de balanceamento
     */
    private function atualizarNo($no) {
        if ($no) {
            $no->altura = 1 + max($this->altura($no->esquerda), $this->altura($no->direita));
            $no->fatorBalanceamento = $this->fatorBalanceamento($no);
        }
    }
    
    /**
     * Rotação à direita
     */
    private function rotacaoDireita($y) {
        $this->rotacoes++;
        $x = $y->esquerda;
        $T2 = $x->direita;
        
        // Realizar rotação
        $x->direita = $y;
        $y->esquerda = $T2;
        
        // Atualizar alturas
        $this->atualizarNo($y);
        $this->atualizarNo($x);
        
        return $x;
    }
    
    /**
     * Rotação à esquerda
     */
    private function rotacaoEsquerda($x) {
        $this->rotacoes++;
        $y = $x->direita;
        $T2 = $y->esquerda;
        
        // Realizar rotação
        $y->esquerda = $x;
        $x->direita = $T2;
        
        // Atualizar alturas
        $this->atualizarNo($x);
        $this->atualizarNo($y);
        
        return $y;
    }
    
    /**
     * Inserir consulta na árvore AVL
     */
    public function inserir($dataHora, $dadosConsulta) {
        $inicio = microtime(true);
        $this->comparacoes = 0;
        
        $this->raiz = $this->inserirRecursivo($this->raiz, $dataHora, $dadosConsulta);
        $this->numeroNos++;
        
        $tempo = (microtime(true) - $inicio) * 1000;
        
        return [
            'success' => true,
            'operacao' => 'inserido',
            'performance' => [
                'tempo_insercao_ms' => $tempo,
                'comparacoes' => $this->comparacoes,
                'rotacoes' => $this->rotacoes,
                'altura_arvore' => $this->altura($this->raiz),
                'nos_total' => $this->numeroNos
            ]
        ];
    }
    
    private function inserirRecursivo($no, $chave, $dados) {
        // Inserção BST padrão
        if ($no === null) {
            return new NoAVL($chave, $dados);
        }
        
        $this->comparacoes++;
        $timestamp1 = strtotime($chave);
        $timestamp2 = strtotime($no->chave);
        
        if ($timestamp1 < $timestamp2) {
            $no->esquerda = $this->inserirRecursivo($no->esquerda, $chave, $dados);
        } elseif ($timestamp1 > $timestamp2) {
            $no->direita = $this->inserirRecursivo($no->direita, $chave, $dados);
        } else {
            // Chave duplicada - atualizar dados
            $no->dados = $dados;
            return $no;
        }
        
        // Atualizar altura e fator de balanceamento
        $this->atualizarNo($no);
        
        // Obter fator de balanceamento
        $balance = $no->fatorBalanceamento;
        
        // Casos de rotação
        // Caso Esquerda-Esquerda
        if ($balance > 1 && strtotime($chave) < strtotime($no->esquerda->chave)) {
            return $this->rotacaoDireita($no);
        }
        
        // Caso Direita-Direita
        if ($balance < -1 && strtotime($chave) > strtotime($no->direita->chave)) {
            return $this->rotacaoEsquerda($no);
        }
        
        // Caso Esquerda-Direita
        if ($balance > 1 && strtotime($chave) > strtotime($no->esquerda->chave)) {
            $no->esquerda = $this->rotacaoEsquerda($no->esquerda);
            return $this->rotacaoDireita($no);
        }
        
        // Caso Direita-Esquerda
        if ($balance < -1 && strtotime($chave) < strtotime($no->direita->chave)) {
            $no->direita = $this->rotacaoDireita($no->direita);
            return $this->rotacaoEsquerda($no);
        }
        
        return $no;
    }
    
    /**
     * Buscar consulta por data/hora - O(log n)
     */
    public function buscar($dataHora) {
        $inicio = microtime(true);
        $this->comparacoes = 0;
        
        $resultado = $this->buscarRecursivo($this->raiz, $dataHora);
        
        $tempo = (microtime(true) - $inicio) * 1000;
        
        return [
            'success' => $resultado !== null,
            'consulta' => $resultado,
            'performance' => [
                'tempo_busca_ms' => $tempo,
                'comparacoes' => $this->comparacoes,
                'altura_arvore' => $this->altura($this->raiz)
            ]
        ];
    }
    
    private function buscarRecursivo($no, $chave) {
        if ($no === null) {
            return null;
        }
        
        $this->comparacoes++;
        $timestamp1 = strtotime($chave);
        $timestamp2 = strtotime($no->chave);
        
        if ($timestamp1 === $timestamp2) {
            return [
                'dataHora' => $no->chave,
                'dados' => $no->dados,
                'altura_no' => $no->altura,
                'fator_balanceamento' => $no->fatorBalanceamento
            ];
        }
        
        if ($timestamp1 < $timestamp2) {
            return $this->buscarRecursivo($no->esquerda, $chave);
        }
        
        return $this->buscarRecursivo($no->direita, $chave);
    }
    
    /**
     * Listar consultas em ordem cronológica (in-order traversal)
     */
    public function listarEmOrdem() {
        $consultas = [];
        $this->inOrder($this->raiz, $consultas);
        
        return [
            'success' => true,
            'consultas' => $consultas,
            'total' => count($consultas),
            'estrutura' => [
                'altura' => $this->altura($this->raiz),
                'nos_total' => $this->numeroNos,
                'balanceada' => $this->verificarBalanceamento($this->raiz)
            ]
        ];
    }
    
    private function inOrder($no, &$consultas) {
        if ($no !== null) {
            $this->inOrder($no->esquerda, $consultas);
            $consultas[] = [
                'dataHora' => $no->chave,
                'dados' => $no->dados,
                'nivel' => $this->calcularNivel($no->chave),
                'fator_balanceamento' => $no->fatorBalanceamento
            ];
            $this->inOrder($no->direita, $consultas);
        }
    }
    
    /**
     * Remover consulta
     */
    public function remover($dataHora) {
        $inicio = microtime(true);
        $this->comparacoes = 0;
        
        $this->raiz = $this->removerRecursivo($this->raiz, $dataHora);
        $this->numeroNos--;
        
        $tempo = (microtime(true) - $inicio) * 1000;
        
        return [
            'success' => true,
            'operacao' => 'removido',
            'performance' => [
                'tempo_remocao_ms' => $tempo,
                'comparacoes' => $this->comparacoes,
                'rotacoes' => $this->rotacoes,
                'altura_arvore' => $this->altura($this->raiz)
            ]
        ];
    }
    
    private function removerRecursivo($no, $chave) {
        if ($no === null) {
            return $no;
        }
        
        $this->comparacoes++;
        $timestamp1 = strtotime($chave);
        $timestamp2 = strtotime($no->chave);
        
        if ($timestamp1 < $timestamp2) {
            $no->esquerda = $this->removerRecursivo($no->esquerda, $chave);
        } elseif ($timestamp1 > $timestamp2) {
            $no->direita = $this->removerRecursivo($no->direita, $chave);
        } else {
            // Nó a ser removido
            if ($no->esquerda === null || $no->direita === null) {
                $temp = $no->esquerda ? $no->esquerda : $no->direita;
                
                if ($temp === null) {
                    $temp = $no;
                    $no = null;
                } else {
                    $no = $temp;
                }
            } else {
                $temp = $this->menorValor($no->direita);
                
                $no->chave = $temp->chave;
                $no->dados = $temp->dados;
                
                $no->direita = $this->removerRecursivo($no->direita, $temp->chave);
            }
        }
        
        if ($no === null) {
            return $no;
        }
        
        // Atualizar altura e rebalancear
        $this->atualizarNo($no);
        
        $balance = $no->fatorBalanceamento;
        
        // Rebalanceamento após remoção
        if ($balance > 1 && $this->fatorBalanceamento($no->esquerda) >= 0) {
            return $this->rotacaoDireita($no);
        }
        
        if ($balance > 1 && $this->fatorBalanceamento($no->esquerda) < 0) {
            $no->esquerda = $this->rotacaoEsquerda($no->esquerda);
            return $this->rotacaoDireita($no);
        }
        
        if ($balance < -1 && $this->fatorBalanceamento($no->direita) <= 0) {
            return $this->rotacaoEsquerda($no);
        }
        
        if ($balance < -1 && $this->fatorBalanceamento($no->direita) > 0) {
            $no->direita = $this->rotacaoDireita($no->direita);
            return $this->rotacaoEsquerda($no);
        }
        
        return $no;
    }
    
    private function menorValor($no) {
        $atual = $no;
        while ($atual->esquerda !== null) {
            $atual = $atual->esquerda;
        }
        return $atual;
    }
    
    /**
     * Verificar se árvore está balanceada
     */
    private function verificarBalanceamento($no) {
        if ($no === null) {
            return true;
        }
        
        $balance = abs($this->fatorBalanceamento($no));
        
        if ($balance > 1) {
            return false;
        }
        
        return $this->verificarBalanceamento($no->esquerda) && 
               $this->verificarBalanceamento($no->direita);
    }
    
    /**
     * Calcular nível de um nó
     */
    private function calcularNivel($chave) {
        return $this->calcularNivelRecursivo($this->raiz, $chave, 0);
    }
    
    private function calcularNivelRecursivo($no, $chave, $nivel) {
        if ($no === null) {
            return -1;
        }
        
        if ($no->chave === $chave) {
            return $nivel;
        }
        
        $timestamp1 = strtotime($chave);
        $timestamp2 = strtotime($no->chave);
        
        if ($timestamp1 < $timestamp2) {
            return $this->calcularNivelRecursivo($no->esquerda, $chave, $nivel + 1);
        }
        
        return $this->calcularNivelRecursivo($no->direita, $chave, $nivel + 1);
    }
    
    /**
     * Estatísticas detalhadas da árvore AVL
     */
    public function getEstatisticas() {
        return [
            'nos_total' => $this->numeroNos,
            'altura_arvore' => $this->altura($this->raiz),
            'altura_teorica_min' => $this->numeroNos > 0 ? floor(log($this->numeroNos, 2)) : 0,
            'altura_teorica_max' => $this->numeroNos > 0 ? $this->numeroNos - 1 : 0,
            'balanceada' => $this->verificarBalanceamento($this->raiz),
            'rotacoes_realizadas' => $this->rotacoes,
            'fator_balanceamento_raiz' => $this->raiz ? $this->raiz->fatorBalanceamento : 0,
            'eficiencia_altura' => $this->calcularEficienciaAltura(),
            'distribuicao_niveis' => $this->calcularDistribuicaoNiveis()
        ];
    }
    
    private function calcularEficienciaAltura() {
        if ($this->numeroNos <= 1) return 100;
        
        $alturaAtual = $this->altura($this->raiz);
        $alturaOtima = ceil(log($this->numeroNos, 2));
        
        return round(($alturaOtima / $alturaAtual) * 100, 2);
    }
    
    private function calcularDistribuicaoNiveis() {
        $distribuicao = [];
        $this->contarPorNivel($this->raiz, 0, $distribuicao);
        return $distribuicao;
    }
    
    private function contarPorNivel($no, $nivel, &$distribuicao) {
        if ($no !== null) {
            if (!isset($distribuicao[$nivel])) {
                $distribuicao[$nivel] = 0;
            }
            $distribuicao[$nivel]++;
            
            $this->contarPorNivel($no->esquerda, $nivel + 1, $distribuicao);
            $this->contarPorNivel($no->direita, $nivel + 1, $distribuicao);
        }
    }
    
    /**
     * Visualizar estrutura da árvore
     */
    public function visualizarArvore() {
        if ($this->raiz === null) {
            return ['estrutura' => 'Árvore vazia'];
        }
        
        $visualizacao = [];
        $this->construirVisualizacao($this->raiz, $visualizacao, '', true);
        
        return [
            'estrutura' => $visualizacao,
            'legenda' => [
                '└── ' => 'Último filho',
                '├── ' => 'Filho intermediário',
                '│   ' => 'Continuação vertical',
                '    ' => 'Espaço vazio'
            ]
        ];
    }
    
    private function construirVisualizacao($no, &$visualizacao, $prefixo, $ehUltimo) {
        if ($no !== null) {
            $simbolo = $ehUltimo ? '└── ' : '├── ';
            $visualizacao[] = $prefixo . $simbolo . $no->chave . ' (h:' . $no->altura . ', b:' . $no->fatorBalanceamento . ')';
            
            $novoPrefixo = $prefixo . ($ehUltimo ? '    ' : '│   ');
            
            if ($no->esquerda !== null || $no->direita !== null) {
                if ($no->direita !== null) {
                    $this->construirVisualizacao($no->direita, $visualizacao, $novoPrefixo, $no->esquerda === null);
                }
                if ($no->esquerda !== null) {
                    $this->construirVisualizacao($no->esquerda, $visualizacao, $novoPrefixo, true);
                }
            }
        }
    }
}
?>