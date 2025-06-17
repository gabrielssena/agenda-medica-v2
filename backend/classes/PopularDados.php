<?php

require_once __DIR__ . '/../classes/AgendaMedicaReal.php';
require_once __DIR__ . '/../dados/DadosDemo.php';

class PopularDados {
    private $agenda;
    
    public function __construct() {
        $this->agenda = new AgendaMedicaReal();
    }
    
    public function popularTodosSistema() {
        $resultado = [
            'pacientes' => $this->popularPacientes(),
            'consultas' => $this->popularConsultas(),
            'urgencias' => $this->popularUrgencias(),
            'compressao' => $this->demonstrarCompressao()
        ];
        
        return [
            'success' => true,
            'message' => 'Sistema populado com dados realistas para demonstração',
            'detalhes' => $resultado,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    private function popularPacientes() {
        $pacientes = DadosDemo::getPacientesDemo();
        $resultados = [];
        
        foreach ($pacientes as $paciente) {
            $resultado = $this->agenda->cadastrarPaciente($paciente['cpf'], $paciente);
            $resultados[] = [
                'nome' => $paciente['nome'],
                'cpf' => $paciente['cpf'],
                'success' => $resultado['success'],
                'performance' => $resultado['performance'] ?? null
            ];
        }
        
        return [
            'total_inseridos' => count($resultados),
            'detalhes' => $resultados,
            'estatisticas_hash' => $this->agenda->getEstatisticasHash()
        ];
    }
    
    private function popularConsultas() {
        $consultas = DadosDemo::getConsultasDemo();
        $resultados = [];
        
        foreach ($consultas as $consulta) {
            $resultado = $this->agenda->agendarConsulta(
                $consulta['dados']['cpf_paciente'],
                $consulta['dataHora'],
                $consulta['dados']['medico'],
                $consulta['dados']['observacoes']
            );
            
            $resultados[] = [
                'paciente' => $consulta['dados']['nome_paciente'],
                'dataHora' => $consulta['dataHora'],
                'medico' => $consulta['dados']['medico'],
                'success' => $resultado['success'],
                'performance' => $resultado['performance'] ?? null
            ];
        }
        
        return [
            'total_inseridas' => count($resultados),
            'detalhes' => $resultados,
            'estatisticas_avl' => $this->agenda->getEstatisticasAVL()
        ];
    }
    
    private function popularUrgencias() {
        $urgencias = DadosDemo::getUrgenciasDemo();
        $resultados = [];
        
        foreach ($urgencias as $urgencia) {
            // Primeiro cadastrar o paciente se não existir
            $dadosPaciente = [
                'nome' => $urgencia['nome'],
                'idade' => rand(18, 80),
                'telefone' => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
                'email' => strtolower(str_replace(' ', '.', $urgencia['nome'])) . '@email.com',
                'endereco' => 'Endereço de emergência - São Paulo/SP',
                'convenio' => 'SUS'
            ];
            
            $this->agenda->cadastrarPaciente($urgencia['cpf'], $dadosPaciente);
            
            // Depois adicionar à fila de urgência
            $resultado = $this->agenda->adicionarUrgencia(
                $urgencia['cpf'],
                $urgencia['prioridade'],
                $urgencia['descricao']
            );
            
            $resultados[] = [
                'nome' => $urgencia['nome'],
                'prioridade' => $urgencia['prioridade'],
                'descricao' => substr($urgencia['descricao'], 0, 50) . '...',
                'success' => $resultado['success'],
                'performance' => $resultado['performance'] ?? null
            ];
        }
        
        return [
            'total_inseridas' => count($resultados),
            'detalhes' => $resultados,
            'estatisticas_fila' => $this->agenda->getEstatisticasFilaPrioridade()
        ];
    }
    
    private function demonstrarCompressao() {
        $dadosMedicos = DadosDemo::getDadosMedicosCompletos();
        
        $resultado = $this->agenda->comprimirDadosMedicos(
            $dadosMedicos['paciente_cpf'],
            $dadosMedicos
        );
        
        return [
            'compressao_realizada' => $resultado['success'],
            'estatisticas' => $resultado['estatisticas'] ?? null,
            'economia_percentual' => $resultado['estatisticas']['economia_percentual'] ?? 0,
            'tamanho_original' => $resultado['estatisticas']['tamanho_original_bits'] ?? 0,
            'tamanho_comprimido' => $resultado['estatisticas']['tamanho_codificado_bits'] ?? 0
        ];
    }
    
    public function getEstatisticasCompletas() {
        return $this->agenda->getRelatorioCompleto();
    }
}
?>