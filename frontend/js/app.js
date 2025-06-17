class AgendaMedicaApp {
    constructor() {
        this.API_BASE_URL = 'http://localhost:8090/agenda-medica-v2/backend/index_simples.php';
        this.currentSection = 'dashboard';
        this.isOnline = false;
        
        this.init();
    }

    async init() {
        console.log('🚀 Inicializando Agenda Médica v2.0...');
        
        // Verificar conexão com backend
        await this.checkConnection();
        
        // Carregar dados iniciais
        if (this.isOnline) {
            await this.loadDashboard();
        }
        
        // Configurar auto-refresh
        this.setupAutoRefresh();
        
        console.log('✅ Sistema inicializado com sucesso!');
    }

    async checkConnection() {
        try {
            const response = await fetch(`${this.API_BASE_URL}?action=test`);
            const data = await response.json();
            
            if (data.test_status === 'SUCESSO') {
                this.isOnline = true;
                this.updateConnectionStatus('online', 'Sistema Online - Estruturas Reais');
                this.showNotification('Conectado ao backend com estruturas de dados reais!', 'success');
            } else {
                throw new Error('Resposta inválida do servidor');
            }
        } catch (error) {
            this.isOnline = false;
            this.updateConnectionStatus('offline', 'Sistema Offline');
            this.showNotification('Erro de conexão com o backend: ' + error.message, 'error');
            console.error('Erro de conexão:', error);
        }
    }

    updateConnectionStatus(status, text) {
        const statusDot = document.querySelector('.status-dot');
        const statusText = document.querySelector('.status-text');
        
        if (statusDot && statusText) {
            statusDot.className = `status-dot ${status}`;
            statusText.textContent = text;
        }
    }

    async loadDashboard() {
        if (!this.isOnline) return;

        try {
            this.showLoading();
            
            // Carregar dados do dashboard
            const dashboardResponse = await fetch(`${this.API_BASE_URL}?action=dashboard`);
            const dashboardData = await dashboardResponse.json();
            
            if (dashboardData.success) {
                this.updateDashboardStats(dashboardData.dashboard);
            }
            
            // Carregar consultas de hoje
            await this.loadTodayConsultations();
            
            // Carregar fila de urgências
            await this.loadUrgencyQueue();
            
        } catch (error) {
            console.error('Erro ao carregar dashboard:', error);
            this.showNotification('Erro ao carregar dados do dashboard', 'error');
        } finally {
            this.hideLoading();
        }
    }

    updateDashboardStats(dashboard) {
        // Atualizar com estrutura real do backend
        const pacientesTotal = dashboard.pacientes?.total || 0;
        const consultasTotal = dashboard.consultas?.total || 0;
        const urgenciasTotal = dashboard.urgencias?.total || 0;
        
        const totalPacientesEl = document.getElementById('total-pacientes');
        const consultasHojeEl = document.getElementById('consultas-hoje');
        const urgenciasFilaEl = document.getElementById('urgencias-fila');
        const taxaOcupacaoEl = document.getElementById('taxa-ocupacao');
        
        if (totalPacientesEl) totalPacientesEl.textContent = pacientesTotal;
        if (consultasHojeEl) consultasHojeEl.textContent = consultasTotal;
        if (urgenciasFilaEl) urgenciasFilaEl.textContent = urgenciasTotal;
        if (taxaOcupacaoEl) {
            const eficiencia = dashboard.pacientes?.eficiencia_hash || 0;
            taxaOcupacaoEl.textContent = eficiencia + '%';
        }
    }

    async loadTodayConsultations() {
        try {
            const response = await fetch(`${this.API_BASE_URL}?action=consultas`);
            const data = await response.json();
            
            const container = document.getElementById('consultas-hoje-list');
            if (!container) return;
            
            if (data.success && data.consultas && data.consultas.length > 0) {
                const consultasHTML = data.consultas.map(consulta => `
                    <div class="data-item">
                        <h4>👤 ${consulta.dados?.nome_paciente || 'Nome não disponível'}</h4>
                        <p><strong>Médico:</strong> ${consulta.dados?.medico || 'Não informado'}</p>
                        <p><strong>Horário:</strong> ${this.formatDateTime(consulta.dataHora)}</p>
                        <p><strong>Status:</strong> <span class="status-${consulta.dados?.status || 'agendada'}">${consulta.dados?.status || 'agendada'}</span></p>
                        <p><strong>Nível na Árvore:</strong> ${consulta.nivel || 0}</p>
                        <p><strong>Fator Balanceamento:</strong> ${consulta.fator_balanceamento || 0}</p>
                    </div>
                `).join('');
                
                container.innerHTML = consultasHTML;
            } else {
                container.innerHTML = '<div class="text-center p-30">📅 Nenhuma consulta agendada para hoje</div>';
            }
        } catch (error) {
            console.error('Erro ao carregar consultas:', error);
            const container = document.getElementById('consultas-hoje-list');
            if (container) {
                container.innerHTML = '<div class="text-center p-30">❌ Erro ao carregar consultas</div>';
            }
        }
    }

    async loadUrgencyQueue() {
        try {
            const response = await fetch(`${this.API_BASE_URL}?action=urgencias`);
            const data = await response.json();
            
            console.log('🚨 DEBUG - Dados urgências:', data);
            
            const container = document.getElementById('urgencias-list');
            if (!container) {
                console.error('❌ Container urgencias-list não encontrado');
                return;
            }
            
            if (data.success) {
                if (data.fila && data.fila.length > 0) {
                    const urgenciasHTML = data.fila.map((urgencia, index) => {
                        const priorityColors = {1: '🔴', 2: '🟡', 3: '🟢'};
                        const priorityNames = {1: 'Emergência', 2: 'Urgente', 3: 'Normal'};
                        
                        return `
                            <div class="data-item" style="border-left: 4px solid ${urgencia.prioridade === 1 ? '#dc3545' : urgencia.prioridade === 2 ? '#ffc107' : '#28a745'};">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h4>🚨 ${urgencia.nome}</h4>
                                    <span style="background: #007bff; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.8em;">#${index + 1}</span>
                                </div>
                                <p><strong>CPF:</strong> ${urgencia.cpf}</p>
                                <p><strong>Prioridade:</strong> ${priorityColors[urgencia.prioridade]} ${priorityNames[urgencia.prioridade]}</p>
                                <p><strong>Descrição:</strong> ${urgencia.descricao}</p>
                                <p><strong>Tempo de espera:</strong> ${urgencia.tempo_espera}</p>
                                <p><strong>Data entrada:</strong> ${urgencia.data_entrada}</p>
                            </div>
                        `;
                    }).join('');
                    
                    container.innerHTML = urgenciasHTML;
                    console.log('✅ Urgências carregadas:', data.fila.length);
                } else {
                    container.innerHTML = '<div style="text-align: center; padding: 30px;">✅ Nenhuma urgência na fila</div>';
                    console.log('ℹ️ Fila vazia');
                }
            } else {
                container.innerHTML = '<div style="text-align: center; padding: 30px;">❌ Erro: ' + (data.error || 'Dados inválidos') + '</div>';
                console.error('❌ Erro nos dados:', data);
            }
        } catch (error) {
            console.error('❌ Erro ao carregar urgências:', error);
            const container = document.getElementById('urgencias-list');
            if (container) {
                container.innerHTML = '<div style="text-align: center; padding: 30px;">❌ Erro de conexão: ' + error.message + '</div>';
            }
        }
    }

    getPriorityClass(priority) {
        switch (priority) {
            case 1: return 'priority-high';
            case 2: return 'priority-medium';
            case 3: return 'priority-low';
            default: return '';
        }
    }

    getPriorityText(priority) {
        switch (priority) {
            case 1: return '🔴 Emergência';
            case 2: return '🟡 Urgente';
            case 3: return '🟢 Normal';
            default: return 'Indefinido';
        }
    }

    // MÉTODOS DE DEMONSTRAÇÃO
    async popularDadosDemo() {
        if (!this.isOnline) {
            this.showNotification('Sistema offline', 'error');
            return;
        }

        if (confirm('🎬 Isso irá popular o sistema com dados realistas para demonstração. Continuar?')) {
            try {
                this.showLoading();
                
                const response = await fetch(`${this.API_BASE_URL}?action=popular_dados`);
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification(
                        `✅ Sistema populado com sucesso! 
                        📊 ${data.detalhes.pacientes.total_inseridos} pacientes, 
                        📅 ${data.detalhes.consultas.total_inseridas} consultas, 
                        🚨 ${data.detalhes.urgencias.total_inseridas} urgências`, 
                        'success'
                    );
                    
                    // Recarregar dashboard
                    await this.loadDashboard();
                    
                    // Se estiver na seção atual, recarregar
                    switch (this.currentSection) {
                        case 'pacientes':
                            await this.loadPacientes();
                            break;
                        case 'consultas':
                            await this.loadConsultas();
                            break;
                        case 'urgencias':
                            await this.loadUrgencias();
                            break;
                        case 'compressao':
                            await this.demonstrarCompressao();
                            break;
                    }
                    
                    console.log('📊 Dados populados:', data);
                } else {
                    this.showNotification('Erro ao popular dados: ' + (data.error || 'Erro desconhecido'), 'error');
                }
            } catch (error) {
                this.showNotification('Erro de conexão ao popular dados: ' + error.message, 'error');
            } finally {
                this.hideLoading();
            }
        }
    }

    async resetarSistema() {
        if (!this.isOnline) {
            this.showNotification('Sistema offline', 'error');
            return;
        }

        if (confirm('⚠️ Isso irá limpar todos os dados do sistema. Continuar?')) {
            try {
                this.showLoading();
                
                const response = await fetch(`${this.API_BASE_URL}?action=reset_dados`);
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('🔄 Sistema resetado com sucesso!', 'success');
                    
                    // Recarregar tudo
                    await this.loadDashboard();
                    
                    console.log('🔄 Sistema resetado:', data);
                } else {
                    this.showNotification('Erro ao resetar sistema: ' + (data.error || 'Erro desconhecido'), 'error');
                }
            } catch (error) {
                this.showNotification('Erro de conexão ao resetar: ' + error.message, 'error');
            } finally {
                this.hideLoading();
            }
        }
    }

    async gerarRelatorioCompleto() {
        if (!this.isOnline) {
            this.showNotification('Sistema offline', 'error');
            return;
        }

        try {
            this.showLoading();
            
            const response = await fetch(`${this.API_BASE_URL}?action=relatorio_completo`);
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('📋 Relatório completo gerado - Verifique o console!', 'success');
                
                console.log('📋 RELATÓRIO COMPLETO DO SISTEMA:', data);
                console.log('📊 Estatísticas Hash:', data.relatorio.hash_table);
                console.log('🌳 Estatísticas AVL:', data.relatorio.avl_tree);
                console.log('🚨 Estatísticas Heap:', data.relatorio.priority_queue);
                console.log('🗜️ Estatísticas Huffman:', data.relatorio.huffman_stats);
                console.log('🔍 Visualizações:', data.visualizacoes);
            } else {
                this.showNotification('Erro ao gerar relatório: ' + (data.error || 'Erro desconhecido'), 'error');
            }
        } catch (error) {
            this.showNotification('Erro de conexão ao gerar relatório: ' + error.message, 'error');
        } finally {
            this.hideLoading();
        }
    }

    // Métodos para cada seção
    async loadPacientes() {
        if (!this.isOnline) return;

        try {
            this.showLoading();
            const response = await fetch(`${this.API_BASE_URL}?action=pacientes`);
            const data = await response.json();
            
            const container = document.getElementById('pacientes-list');
            if (!container) return;
            
            if (data.success && data.pacientes && data.pacientes.length > 0) {
                const estatisticas = data.estatisticas_hash || {};
                
                const pacientesHTML = `
                    <div class="mb-20" style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h4>📊 Estatísticas da Tabela Hash</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div><strong>Total de Pacientes:</strong> ${estatisticas.elementos_total || 0}</div>
                            <div><strong>Fator de Carga:</strong> ${estatisticas.fator_carga || 0}</div>
                            <div><strong>Eficiência:</strong> ${estatisticas.eficiencia || 0}%</div>
                            <div><strong>Colisões:</strong> ${estatisticas.total_colisoes || 0}</div>
                            <div><strong>Buckets Ocupados:</strong> ${estatisticas.buckets_ocupados || 0}</div>
                            <div><strong>Taxa de Colisão:</strong> ${estatisticas.taxa_colisao || 0}%</div>
                        </div>
                    </div>
                    ${data.pacientes.map(paciente => `
                        <div class="data-item">
                            <h4>👤 ${paciente.nome || 'Nome não disponível'}</h4>
                            <p><strong>CPF:</strong> ${this.formatCPF(paciente.cpf)}</p>
                            <p><strong>Idade:</strong> ${paciente.idade || 'N/A'} anos</p>
                            <p><strong>Telefone:</strong> ${paciente.telefone || 'N/A'}</p>
                            <p><strong>Profissão:</strong> ${paciente.profissao || 'N/A'}</p>
                            <p><strong>Convênio:</strong> ${paciente.convenio || 'N/A'}</p>
                            <p><strong>Data Cadastro:</strong> ${this.formatDateTime(paciente.data_cadastro)}</p>
                        </div>
                    `).join('')}
                `;
                
                container.innerHTML = pacientesHTML;
            } else {
                container.innerHTML = '<div class="text-center p-30">👥 Nenhum paciente cadastrado</div>';
            }
        } catch (error) {
            console.error('Erro ao carregar pacientes:', error);
            this.showNotification('Erro ao carregar pacientes', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async loadConsultas() {
        if (!this.isOnline) return;

        try {
            this.showLoading();
            const response = await fetch(`${this.API_BASE_URL}?action=consultas`);
            const data = await response.json();
            
            const container = document.getElementById('consultas-list');
            if (!container) return;
            
            if (data.success && data.consultas && data.consultas.length > 0) {
                const estrutura = data.estrutura || {};
                
                const consultasHTML = `
                    <div class="mb-20" style="background: #e8f5e8; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h4>🌳 Estatísticas da Árvore AVL</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div><strong>Total de Nós:</strong> ${estrutura.nos_total || 0}</div>
                            <div><strong>Altura da Árvore:</strong> ${estrutura.altura || 0}</div>
                            <div><strong>Balanceada:</strong> ${estrutura.balanceada ? 'Sim' : 'Não'}</div>
                            <div><strong>Complexidade:</strong> O(log n)</div>
                        </div>
                    </div>
                    ${data.consultas.map(consulta => `
                        <div class="data-item">
                            <h4>📅 ${consulta.dados?.nome_paciente || 'Nome não disponível'}</h4>
                            <p><strong>Médico:</strong> ${consulta.dados?.medico || 'N/A'}</p>
                            <p><strong>Especialidade:</strong> ${consulta.dados?.especialidade || 'N/A'}</p>
                            <p><strong>Data/Hora:</strong> ${this.formatDateTime(consulta.dataHora)}</p>
                            <p><strong>Status:</strong> <span class="status-${consulta.dados?.status || 'agendada'}">${consulta.dados?.status || 'agendada'}</span></p>
                            <p><strong>Tipo:</strong> ${consulta.dados?.tipo_consulta || 'N/A'}</p>
                            <p><strong>Nível na Árvore:</strong> ${consulta.nivel || 0}</p>
                            <p><strong>Fator Balanceamento:</strong> ${consulta.fator_balanceamento || 0}</p>
                        </div>
                    `).join('')}
                `;
                
                container.innerHTML = consultasHTML;
            } else {
                container.innerHTML = '<div class="text-center p-30">📅 Nenhuma consulta agendada</div>';
            }
        } catch (error) {
            console.error('Erro ao carregar consultas:', error);
            this.showNotification('Erro ao carregar consultas', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async loadUrgencias() {
        if (!this.isOnline) return;
    
        try {
            this.showLoading();
            const response = await fetch(`${this.API_BASE_URL}?action=urgencias`);
            const data = await response.json();
            
            console.log('🚨 Dados completos de urgências:', data); // Debug
            
            const container = document.getElementById('urgencias-list');
            if (!container) return;
            
            if (data.success && data.fila && Array.isArray(data.fila) && data.fila.length > 0) {
                const estrutura = data.estrutura || {};
                const estatisticas = data.estatisticas_prioridade || {};
                
                const urgenciasHTML = `
                    <div class="mb-20" style="background: #fff3e0; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h4>🚨 Estatísticas da Fila de Prioridade (Min-Heap)</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div><strong>Total na Fila:</strong> ${data.total || data.fila.length}</div>
                            <div><strong>Altura do Heap:</strong> ${estrutura.altura || 0}</div>
                            <div><strong>Tipo:</strong> ${estrutura.tipo || 'Min-Heap'}</div>
                            <div><strong>Emergências:</strong> ${estatisticas.emergencia || 0}</div>
                            <div><strong>Urgentes:</strong> ${estatisticas.urgente || 0}</div>
                            <div><strong>Normais:</strong> ${estatisticas.normal || 0}</div>
                        </div>
                    </div>
                    ${data.fila.map((urgencia, index) => {
                        const priorityClass = this.getPriorityClass(urgencia.prioridade);
                        const priorityText = this.getPriorityText(urgencia.prioridade);
                        
                        return `
                            <div class="data-item ${priorityClass}">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h4>🚨 ${urgencia.nome || 'Nome não disponível'}</h4>
                                    <span class="priority-badge">#${urgencia.posicao_fila || index + 1}</span>
                                </div>
                                <p><strong>CPF:</strong> ${urgencia.cpf || 'N/A'}</p>
                                <p><strong>Prioridade:</strong> ${priorityText}</p>
                                <p><strong>Descrição:</strong> ${urgencia.descricao || 'Sem descrição'}</p>
                                <p><strong>Tempo de espera:</strong> ${urgencia.tempo_espera || 'N/A'}</p>
                                <p><strong>Data entrada:</strong> ${this.formatDateTime(urgencia.data_entrada)}</p>
                            </div>
                        `;
                    }).join('')}
                `;
                
                container.innerHTML = urgenciasHTML;
            } else if (data.success && (!data.fila || data.fila.length === 0)) {
                container.innerHTML = '<div class="text-center p-30">✅ Fila de urgências vazia</div>';
            } else {
                console.error('❌ Erro nos dados de urgências:', data);
                container.innerHTML = '<div class="text-center p-30">❌ Erro ao carregar urgências: ' + (data.error || 'Dados inválidos') + '</div>';
            }
        } catch (error) {
            console.error('❌ Erro ao carregar urgências:', error);
            this.showNotification('Erro ao carregar urgências: ' + error.message, 'error');
            const container = document.getElementById('urgencias-list');
            if (container) {
                container.innerHTML = '<div class="text-center p-30">❌ Erro de conexão</div>';
            }
        } finally {
            this.hideLoading();
        }
    }

    async demonstrarCompressao() {
        if (!this.isOnline) return;

        try {
            this.showLoading();
            const response = await fetch(`${this.API_BASE_URL}?action=compressao`);
            const data = await response.json();
            
            const container = document.getElementById('compressao-demo');
            if (!container) return;
            
            if (data.success && data.estatisticas) {
                const stats = data.estatisticas;
                
                const compressaoHTML = `
                    <div class="mb-20" style="background: #f3e5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h4>🗜️ Estatísticas do Algoritmo de Huffman</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                            <div><strong>Caracteres Únicos:</strong> ${stats.caracteres_unicos || 0}</div>
                            <div><strong>Tamanho Original:</strong> ${stats.tamanho_original_bits || 0} bits</div>
                            <div><strong>Tamanho Comprimido:</strong> ${stats.tamanho_codificado_bits || 0} bits</div>
                            <div><strong>Taxa de Compressão:</strong> ${stats.taxa_compressao || 0}</div>
                            <div><strong>Economia:</strong> ${stats.economia_percentual || 0}%</div>
                            <div><strong>Eficiência Huffman:</strong> ${stats.eficiencia_huffman || 0}%</div>
                        </div>
                    </div>
                    
                    <div class="data-item">
                        <h4>📄 Dados Médicos Originais</h4>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 0.9em; max-height: 200px; overflow-y: auto;">
                            ${data.texto_original || 'Dados não disponíveis'}
                        </div>
                    </div>
                    
                    <div class="data-item">
                        <h4>🗜️ Dados Comprimidos (Primeiros 200 caracteres)</h4>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 0.8em; word-break: break-all; max-height: 150px; overflow-y: auto;">
                            ${(data.texto_codificado || '').substring(0, 200)}...
                        </div>
                    </div>
                    
                    <div class="data-item" style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border-left-color: #28a745;">
                        <h4>📊 Resultados da Compressão Real</h4>
                        <p><strong>Algoritmo:</strong> Huffman com árvore binária</p>
                        <p><strong>Economia de Espaço:</strong> ${stats.economia_bits || 0} bits (${stats.economia_percentual || 0}%)</p>
                        <p><strong>Comprimento Médio do Código:</strong> ${stats.comprimento_medio_codigo || 0} bits</p>
                        <p><strong>Entropia:</strong> ${stats.entropia || 0} bits</p>
                        <p style="margin-top: 15px; font-style: italic;">
                            💡 O algoritmo de Huffman real otimiza o armazenamento baseado na frequência dos caracteres, 
                            criando códigos mais curtos para caracteres mais frequentes.
                        </p>
                    </div>
                `;
                
                container.innerHTML = compressaoHTML;
            } else {
                container.innerHTML = '<div class="text-center p-30">❌ Erro na demonstração de compressão</div>';
            }
        } catch (error) {
            console.error('Erro na demonstração:', error);
            this.showNotification('Erro na demonstração de compressão', 'error');
        } finally {
            this.hideLoading();
        }
    }

    // Métodos de ação
    async chamarProximo() {
        if (!this.isOnline) {
            this.showNotification('Sistema offline', 'error');
            return;
        }

        if (confirm('🚨 Confirma a chamada do próximo paciente da fila de urgência?')) {
            try {
                // Simular chamada do próximo (implementar endpoint real depois)
                this.showNotification('📢 Funcionalidade em desenvolvimento - Sistema com estruturas reais!', 'warning');
                
                // Recarregar dados
                await this.loadUrgencias();
                await this.loadDashboard();
            } catch (error) {
                this.showNotification('Erro ao chamar próximo paciente', 'error');
            }
        }
    }

    async showHashStats() {
        if (!this.isOnline) return;

        try {
            const response = await fetch(`${this.API_BASE_URL}?action=visualizar&tipo=hash`);
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('📊 Visualização da Tabela Hash carregada - Verifique o console!', 'success');
                console.log('🔍 Distribuição da Tabela Hash:', data.visualizacao);
            }
        } catch (error) {
            this.showNotification('Erro ao carregar visualização da Hash', 'error');
        }
    }

    async showAVLStats() {
        if (!this.isOnline) return;

        try {
            const response = await fetch(`${this.API_BASE_URL}?action=visualizar&tipo=avl`);
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('🌳 Visualização da Árvore AVL carregada - Verifique o console!', 'success');
                console.log('🔍 Estrutura da Árvore AVL:', data.visualizacao);
            }
        } catch (error) {
            this.showNotification('Erro ao carregar visualização da AVL', 'error');
        }
    }

    async showCompressionStats() {
        if (!this.isOnline) return;

        try {
            const response = await fetch(`${this.API_BASE_URL}?action=visualizar&tipo=huffman`);
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('🗜️ Visualização da Árvore de Huffman carregada - Verifique o console!', 'success');
                console.log('🔍 Árvore de Huffman:', data.visualizacao);
            }
        } catch (error) {
            this.showNotification('Erro ao carregar visualização do Huffman', 'error');
        }
    }

    // Navegação entre seções
    showSection(sectionName) {
        // Remover classe active de todas as seções
        document.querySelectorAll('.section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Remover classe active de todos os botões
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Ativar seção e botão correspondentes
        const section = document.getElementById(`${sectionName}-section`);
        if (section) {
            section.classList.add('active');
        }
        
        // Encontrar e ativar botão correto
        const buttons = document.querySelectorAll('.nav-btn');
        buttons.forEach(btn => {
            if (btn.textContent.toLowerCase().includes(sectionName.toLowerCase())) {
                btn.classList.add('active');
            }
        });
        
        this.currentSection = sectionName;
        
        // Carregar dados da seção se necessário
        switch (sectionName) {
            case 'dashboard':
                this.loadDashboard();
                break;
            case 'pacientes':
                this.loadPacientes();
                break;
            case 'consultas':
                this.loadConsultas();
                break;
            case 'urgencias':
                this.loadUrgencias();
                break;
            case 'compressao':
                this.demonstrarCompressao();
                break;
        }
    }

    // Utilitários
    formatCPF(cpf) {
        if (!cpf) return 'N/A';
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    formatDateTime(dateTimeString) {
        if (!dateTimeString) return 'N/A';
        
        try {
            const date = new Date(dateTimeString);
            return date.toLocaleString('pt-BR');
        } catch (error) {
            return dateTimeString;
        }
    }

    showLoading() {
        const loading = document.getElementById('loading-overlay');
        if (loading) {
            loading.style.display = 'flex';
        }
    }

    hideLoading() {
        const loading = document.getElementById('loading-overlay');
        if (loading) {
            loading.style.display = 'none';
        }
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 1.2em; cursor: pointer; color: inherit;">×</button>
            </div>
        `;
        
        const container = document.getElementById('notifications');
        if (container) {
            container.appendChild(notification);
            
            // Auto-remover após 5 segundos
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
    }

    setupAutoRefresh() {
        // Atualizar dashboard a cada 30 segundos
        setInterval(() => {
            if (this.isOnline && this.currentSection === 'dashboard') {
                this.loadDashboard();
            }
        }, 30000);
        
        // Verificar conexão a cada 60 segundos
        setInterval(() => {
            this.checkConnection();
        }, 60000);
    }
}

// Funções globais para os botões HTML
function showSection(sectionName) {
    if (window.app) {
        window.app.showSection(sectionName);
    }
}

function loadPacientes() {
    if (window.app) {
        window.app.loadPacientes();
    }
}

function loadConsultas() {
    if (window.app) {
        window.app.loadConsultas();
    }
}

function loadUrgencias() {
    if (window.app) {
        window.app.loadUrgencias();
    }
}

function demonstrarCompressao() {
    if (window.app) {
        window.app.demonstrarCompressao();
    }
}

function chamarProximo() {
    if (window.app) {
        window.app.chamarProximo();
    }
}

function showHashStats() {
    if (window.app) {
        window.app.showHashStats();
    }
}

function showAVLStats() {
    if (window.app) {
        window.app.showAVLStats();
    }
}

function showCompressionStats() {
    if (window.app) {
        window.app.showCompressionStats();
    }
}

// Funções globais para demonstração
function popularDadosDemo() {
    if (window.app) {
        window.app.popularDadosDemo();
    }
}

function resetarSistema() {
    if (window.app) {
        window.app.resetarSistema();
    }
}

function gerarRelatorioCompleto() {
    if (window.app) {
        window.app.gerarRelatorioCompleto();
    }
}

// Inicializar aplicação quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    console.log('🏥 Iniciando Agenda Médica Inteligente v2.0...');
    window.app = new AgendaMedicaApp();
});

// Log de inicialização
console.log(`
🏥 AGENDA MÉDICA INTELIGENTE v2.0
================================
📊 Estruturas de Dados Implementadas:
  • Tabela Hash - Busca de pacientes O(1)
  • Árvore AVL - Consultas ordenadas O(log n)  
  • Fila de Prioridade - Urgências O(log n)
  • Algoritmo de Huffman - Compressão de dados

🚀 Sistema carregado com sucesso!
`);