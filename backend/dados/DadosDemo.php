<?php

class DadosDemo {
    
    public static function getPacientesDemo() {
        return [
            [
                'cpf' => '12345678901',
                'nome' => 'João Silva Santos',
                'idade' => 45,
                'telefone' => '(11) 99876-5432',
                'email' => 'joao.silva@email.com',
                'endereco' => 'Rua das Flores, 123 - São Paulo/SP',
                'data_nascimento' => '1979-03-15',
                'convenio' => 'Unimed',
                'profissao' => 'Engenheiro Civil',
                'estado_civil' => 'Casado',
                'contato_emergencia' => 'Maria Silva - (11) 98765-4321'
            ],
            [
                'cpf' => '98765432100',
                'nome' => 'Maria Oliveira Costa',
                'idade' => 32,
                'telefone' => '(11) 98765-4321',
                'email' => 'maria.oliveira@email.com',
                'endereco' => 'Av. Paulista, 456 - São Paulo/SP',
                'data_nascimento' => '1992-07-22',
                'convenio' => 'Bradesco Saúde',
                'profissao' => 'Médica Pediatra',
                'estado_civil' => 'Solteira',
                'contato_emergencia' => 'Ana Oliveira - (11) 97654-3210'
            ],
            [
                'cpf' => '11122233344',
                'nome' => 'Pedro Henrique Lima',
                'idade' => 28,
                'telefone' => '(11) 97654-3210',
                'email' => 'pedro.lima@email.com',
                'endereco' => 'Rua Augusta, 789 - São Paulo/SP',
                'data_nascimento' => '1996-11-08',
                'convenio' => 'SulAmérica',
                'profissao' => 'Desenvolvedor de Software',
                'estado_civil' => 'Solteiro',
                'contato_emergencia' => 'Carlos Lima - (11) 96543-2109'
            ],
            [
                'cpf' => '55566677788',
                'nome' => 'Ana Carolina Ferreira',
                'idade' => 67,
                'telefone' => '(11) 96543-2109',
                'email' => 'ana.ferreira@email.com',
                'endereco' => 'Rua da Consolação, 321 - São Paulo/SP',
                'data_nascimento' => '1957-04-12',
                'convenio' => 'Amil',
                'profissao' => 'Professora Aposentada',
                'estado_civil' => 'Viúva',
                'contato_emergencia' => 'Roberto Ferreira - (11) 95432-1098'
            ],
            [
                'cpf' => '99988877766',
                'nome' => 'Carlos Eduardo Souza',
                'idade' => 52,
                'telefone' => '(11) 95432-1098',
                'email' => 'carlos.souza@email.com',
                'endereco' => 'Alameda Santos, 654 - São Paulo/SP',
                'data_nascimento' => '1972-09-30',
                'convenio' => 'Porto Seguro',
                'profissao' => 'Advogado',
                'estado_civil' => 'Divorciado',
                'contato_emergencia' => 'Lucia Souza - (11) 94321-0987'
            ],
            [
                'cpf' => '33344455566',
                'nome' => 'Fernanda Rodrigues Silva',
                'idade' => 29,
                'telefone' => '(11) 94321-0987',
                'email' => 'fernanda.rodrigues@email.com',
                'endereco' => 'Rua Oscar Freire, 987 - São Paulo/SP',
                'data_nascimento' => '1995-01-18',
                'convenio' => 'Prevent Senior',
                'profissao' => 'Arquiteta',
                'estado_civil' => 'Casada',
                'contato_emergencia' => 'Ricardo Silva - (11) 93210-9876'
            ],
            [
                'cpf' => '77788899900',
                'nome' => 'Roberto Carlos Almeida',
                'idade' => 38,
                'telefone' => '(11) 93210-9876',
                'email' => 'roberto.almeida@email.com',
                'endereco' => 'Rua Bela Cintra, 159 - São Paulo/SP',
                'data_nascimento' => '1986-06-25',
                'convenio' => 'Golden Cross',
                'profissao' => 'Contador',
                'estado_civil' => 'Casado',
                'contato_emergencia' => 'Patricia Almeida - (11) 92109-8765'
            ],
            [
                'cpf' => '44455566677',
                'nome' => 'Juliana Santos Pereira',
                'idade' => 41,
                'telefone' => '(11) 92109-8765',
                'email' => 'juliana.pereira@email.com',
                'endereco' => 'Av. Faria Lima, 753 - São Paulo/SP',
                'data_nascimento' => '1983-12-03',
                'convenio' => 'Hapvida',
                'profissao' => 'Gerente de Marketing',
                'estado_civil' => 'Casada',
                'contato_emergencia' => 'Marcos Pereira - (11) 91098-7654'
            ]
        ];
    }
    
    public static function getConsultasDemo() {
        $hoje = date('Y-m-d');
        $amanha = date('Y-m-d', strtotime('+1 day'));
        
        return [
            [
                'dataHora' => $hoje . ' 08:00:00',
                'dados' => [
                    'cpf_paciente' => '12345678901',
                    'nome_paciente' => 'João Silva Santos',
                    'medico' => 'Dr. Carlos Roberto Cardoso',
                    'especialidade' => 'Cardiologia',
                    'observacoes' => 'Consulta de retorno - acompanhamento hipertensão',
                    'status' => 'confirmada',
                    'tipo_consulta' => 'Retorno',
                    'duracao_estimada' => '30 minutos'
                ]
            ],
            [
                'dataHora' => $hoje . ' 09:30:00',
                'dados' => [
                    'cpf_paciente' => '98765432100',
                    'nome_paciente' => 'Maria Oliveira Costa',
                    'medico' => 'Dra. Ana Paula Mendes',
                    'especialidade' => 'Ginecologia',
                    'observacoes' => 'Consulta preventiva anual',
                    'status' => 'agendada',
                    'tipo_consulta' => 'Preventiva',
                    'duracao_estimada' => '45 minutos'
                ]
            ],
            [
                'dataHora' => $hoje . ' 10:15:00',
                'dados' => [
                    'cpf_paciente' => '55566677788',
                    'nome_paciente' => 'Ana Carolina Ferreira',
                    'medico' => 'Dr. José Fernando Silva',
                    'especialidade' => 'Geriatria',
                    'observacoes' => 'Avaliação geriátrica completa',
                    'status' => 'em_andamento',
                    'tipo_consulta' => 'Primeira consulta',
                    'duracao_estimada' => '60 minutos'
                ]
            ],
            [
                'dataHora' => $hoje . ' 14:00:00',
                'dados' => [
                    'cpf_paciente' => '11122233344',
                    'nome_paciente' => 'Pedro Henrique Lima',
                    'medico' => 'Dr. Ricardo Oliveira',
                    'especialidade' => 'Ortopedia',
                    'observacoes' => 'Dor no joelho direito após exercício',
                    'status' => 'agendada',
                    'tipo_consulta' => 'Primeira consulta',
                    'duracao_estimada' => '30 minutos'
                ]
            ],
            [
                'dataHora' => $hoje . ' 15:30:00',
                'dados' => [
                    'cpf_paciente' => '99988877766',
                    'nome_paciente' => 'Carlos Eduardo Souza',
                    'medico' => 'Dra. Mariana Costa',
                    'especialidade' => 'Endocrinologia',
                    'observacoes' => 'Acompanhamento diabetes tipo 2',
                    'status' => 'agendada',
                    'tipo_consulta' => 'Retorno',
                    'duracao_estimada' => '30 minutos'
                ]
            ],
            [
                'dataHora' => $hoje . ' 16:45:00',
                'dados' => [
                    'cpf_paciente' => '33344455566',
                    'nome_paciente' => 'Fernanda Rodrigues Silva',
                    'medico' => 'Dr. Paulo Henrique Santos',
                    'especialidade' => 'Dermatologia',
                    'observacoes' => 'Avaliação de pintas e manchas na pele',
                    'status' => 'agendada',
                    'tipo_consulta' => 'Preventiva',
                    'duracao_estimada' => '30 minutos'
                ]
            ],
            [
                'dataHora' => $amanha . ' 08:30:00',
                'dados' => [
                    'cpf_paciente' => '77788899900',
                    'nome_paciente' => 'Roberto Carlos Almeida',
                    'medico' => 'Dr. Fernando Augusto Lima',
                    'especialidade' => 'Gastroenterologia',
                    'observacoes' => 'Investigação dor abdominal recorrente',
                    'status' => 'agendada',
                    'tipo_consulta' => 'Primeira consulta',
                    'duracao_estimada' => '45 minutos'
                ]
            ],
            [
                'dataHora' => $amanha . ' 10:00:00',
                'dados' => [
                    'cpf_paciente' => '44455566677',
                    'nome_paciente' => 'Juliana Santos Pereira',
                    'medico' => 'Dra. Lucia Helena Rodrigues',
                    'especialidade' => 'Neurologia',
                    'observacoes' => 'Acompanhamento enxaqueca crônica',
                    'status' => 'agendada',
                    'tipo_consulta' => 'Retorno',
                    'duracao_estimada' => '30 minutos'
                ]
            ]
        ];
    }
    
    public static function getUrgenciasDemo() {
        return [
            [
                'cpf' => '12312312312',
                'nome' => 'José da Silva Urgente',
                'prioridade' => 1,
                'descricao' => 'Dor torácica intensa com irradiação para braço esquerdo, sudorese fria, náuseas. Suspeita de infarto agudo do miocárdio.'
            ],
            [
                'cpf' => '45645645645',
                'nome' => 'Marina Santos Emergência',
                'prioridade' => 1,
                'descricao' => 'Convulsões tônico-clônicas generalizadas, estado pós-ictal. Histórico de epilepsia, sem medicação há 3 dias.'
            ],
            [
                'cpf' => '78978978978',
                'nome' => 'Roberto Lima Urgente',
                'prioridade' => 2,
                'descricao' => 'Febre alta (39.5°C) há 2 dias, calafrios, dor de cabeça intensa, rigidez de nuca. Suspeita de meningite.'
            ],
            [
                'cpf' => '32132132132',
                'nome' => 'Ana Paula Ferreira',
                'prioridade' => 2,
                'descricao' => 'Dificuldade respiratória progressiva, tosse seca, febre baixa. Histórico de asma, sem melhora com broncodilatador.'
            ],
            [
                'cpf' => '65465465465',
                'nome' => 'Carlos Mendes Silva',
                'prioridade' => 2,
                'descricao' => 'Trauma craniano após queda de bicicleta, perda de consciência momentânea, vômitos, confusão mental.'
            ],
            [
                'cpf' => '14714714714',
                'nome' => 'Lucia Helena Costa',
                'prioridade' => 3,
                'descricao' => 'Dor abdominal em fossa ilíaca direita, náuseas, febre baixa. Suspeita de apendicite aguda.'
            ],
            [
                'cpf' => '85285285285',
                'nome' => 'Fernando Augusto Reis',
                'prioridade' => 3,
                'descricao' => 'Corte profundo em mão direita com serra elétrica, sangramento controlado, necessita sutura.'
            ],
            [
                'cpf' => '96396396396',
                'nome' => 'Patricia Oliveira Santos',
                'prioridade' => 3,
                'descricao' => 'Crise de ansiedade com palpitações, sudorese, sensação de morte iminente. Histórico de transtorno do pânico.'
            ]
        ];
    }
    
    public static function getDadosMedicosCompletos() {
        return [
            'paciente_cpf' => '12345678901',
            'paciente_nome' => 'João Silva Santos',
            'data_consulta' => date('Y-m-d H:i:s'),
            'medico_responsavel' => 'Dr. Carlos Roberto Cardoso - CRM 12345',
            'historico_medico' => 'Paciente masculino, 45 anos, com histórico de hipertensão arterial sistêmica diagnosticada há 8 anos, atualmente em uso de Losartana 50mg/dia. Diabetes mellitus tipo 2 diagnosticado há 5 anos, controlado com Metformina 850mg 2x/dia. Realizou angioplastia coronariana em 2020 devido a lesão em artéria descendente anterior.',
            'sintomas' => 'Paciente relata episódios de dor precordial de intensidade moderada (6/10), com duração de 5-10 minutos, desencadeada por esforços físicos moderados como subir escadas. Dor com característica opressiva, com irradiação para região cervical e membro superior esquerdo.',
            'diagnostico' => 'Angina pectoris estável - CID I20.9. Hipertensão arterial sistêmica - CID I10. Diabetes mellitus tipo 2 não insulino-dependente - CID E11.9.',
            'prescricao' => 'Losartana Potássica 50mg - 1 comprimido via oral pela manhã em jejum. Metformina 850mg - 1 comprimido via oral após café da manhã e jantar. Sinvastatina 20mg - 1 comprimido via oral ao deitar.',
            'exames' => 'Eletrocardiograma de repouso: ritmo sinusal, frequência cardíaca 78 bpm, eixo elétrico normal, sem alterações isquêmicas agudas. Glicemia de jejum: 180 mg/dL. Hemoglobina glicada: 8.2%.',
            'observacoes' => 'Paciente orientado sobre importância da adesão medicamentosa e controle rigoroso dos fatores de risco cardiovascular. Recomendada dieta hipossódica, hipoglicídica e hipolipídica.',
            'anotacoes' => 'Paciente colaborativo, demonstra boa compreensão das orientações médicas. Retorno em 15 dias para reavaliação clínica.'
        ];
    }
}
?>