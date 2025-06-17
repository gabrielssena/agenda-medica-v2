# 🏥 Sistema de Agenda Médica Inteligente

Sistema hospitalar desenvolvido para demonstrar a aplicação prática de estruturas de dados fundamentais em um cenário médico real.

## 🎯 Visão Geral

Este projeto implementa um sistema completo de gestão médica utilizando quatro estruturas de dados clássicas:
- **Tabela Hash** para gestão de pacientes
- **Árvore AVL** para agendamento de consultas  
- **Min-Heap** para fila de urgências
- **Algoritmo de Huffman** para compressão de prontuários médicos

## 🚀 Funcionalidades

### 👥 Gestão de Pacientes
- ✅ Cadastro com validação de CPF
- ✅ Busca em tempo constante O(1)
- ✅ Armazenamento eficiente com tabela hash
- ✅ Tratamento de colisões por encadeamento separado

### 📅 Agendamento de Consultas
- ✅ Inserção ordenada por data/hora
- ✅ Árvore AVL auto-balanceada
- ✅ Operações com complexidade O(log n)
- ✅ Visualização de fatores de balanceamento

### 🚨 Fila de Urgências
- ✅ Priorização por nível de gravidade
- ✅ Min-Heap com propriedade de ordenação
- ✅ Inserção/remoção em O(log n)
- ✅ Acesso ao próximo atendimento em O(1)

### 📋 Compressão de Prontuários
- ✅ Algoritmo de Huffman implementado
- ✅ Compressão de 40% a 60% em prontuários
- ✅ Geração de códigos binários de comprimento variável
- ✅ Exibição de estatísticas de performance

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 8.0+** com orientação a objetos
- **Classes personalizadas** para estruturas de dados
- **API REST** com endpoints organizados
- **Persistência via JSON** simulando banco de dados
- **Validação de entrada** e tratamento de erros

### Frontend
- **HTML5 semântico**
- **CSS3** com Grid Layout e Flexbox
- **JavaScript ES6+** com uso de async/await
- **Fetch API** para chamadas assíncronas
- **Interface responsiva** compatível com múltiplos dispositivos

### Estruturas de Dados
- Tabela Hash com função hash própria
- Árvore AVL com rotações automáticas
- Min-Heap com heapify up/down
- Árvore de Huffman para compressão ótima

## 📁 Estrutura do Projeto

```

agenda-medica-v2/
├── backend/
│   ├── index\_simples.php
│   ├── classes/
│   │   ├── AgendaMedicaSimples.php
│   │   └── PersistenciaSimples.php
│   ├── dados/
│   │   ├── DadosDemo.php
│   │   └── sistema\_dados.json
│   └── .htaccess
├── frontend/
│   ├── index\_funcional.html
│   ├── css/
│   │   └── style\_funcional.css
│   └── js/
│       └── app\_funcional.js
└── README.md

````

## ⚙️ Instalação e Configuração

### Pré-requisitos
- PHP 8.0+ com extensões habilitadas
- Servidor web (Apache/Nginx) ou XAMPP/WAMP
- Navegador moderno com suporte a ES6+

### Passos

1. **Clone o repositório**
```bash
git clone https://github.com/seu-usuario/agenda-medica-v2.git
cd agenda-medica-v2
````

2. **Configure o servidor web**

```bash
# Apache (com mod_rewrite habilitado)
# Ou use o servidor embutido do PHP:
php -S localhost:8090 -t .
```

3. **Permissões de escrita**

```bash
# Linux/Mac
chmod 755 backend/dados/
chmod 666 backend/dados/sistema_dados.json

# Windows: conceder permissões de escrita à pasta `dados/`
```

4. **Abra no navegador**

```
http://localhost:8090/frontend/index_funcional.html
```

## 📡 Endpoints da API

| Método | Endpoint                                                    | Descrição                      |
| ------ | ----------------------------------------------------------- | ------------------------------ |
| GET    | `/backend/index_simples.php?action=test`                    | Teste de conectividade         |
| GET    | `/backend/index_simples.php?action=dashboard`               | Métricas do sistema            |
| POST   | `/backend/index_simples.php?action=cadastrar_paciente`      | Cadastrar novo paciente        |
| GET    | `/backend/index_simples.php?action=buscar_paciente&cpf=XXX` | Buscar paciente por CPF        |
| POST   | `/backend/index_simples.php?action=agendar_consulta`        | Agendar nova consulta          |
| POST   | `/backend/index_simples.php?action=adicionar_urgencia`      | Adicionar urgência             |
| GET    | `/backend/index_simples.php?action=pacientes`               | Listar pacientes               |
| GET    | `/backend/index_simples.php?action=consultas`               | Listar consultas               |
| GET    | `/backend/index_simples.php?action=urgencias`               | Listar urgências               |
| GET    | `/backend/index_simples.php?action=compressao`              | Executar compressão Huffman    |
| GET    | `/backend/index_simples.php?action=popular_dados`           | Carregar dados de demonstração |
| GET    | `/backend/index_simples.php?action=reset_dados`             | Resetar dados do sistema       |

### Exemplo de Requisição (JavaScript)

```javascript
const response = await fetch('http://localhost:8090/backend/index_simples.php?action=cadastrar_paciente', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        cpf: '12345678901',
        nome: 'João Silva',
        idade: 35,
        telefone: '11999999999',
        email: 'joao@email.com',
        convenio: 'Unimed',
        endereco: 'Rua das Flores, 123'
    })
});
const result = await response.json();
console.log(result);
```

## 🧠 Complexidade Computacional

| Estrutura   | Inserção   | Busca    | Remoção  | Espaço |
| ----------- | ---------- | -------- | -------- | ------ |
| Tabela Hash | O(1)       | O(1)     | O(1)     | O(n)   |
| Árvore AVL  | O(log n)   | O(log n) | O(log n) | O(n)   |
| Min-Heap    | O(log n)   | O(1)\*   | O(log n) | O(n)   |
| Huffman     | O(n log n) | O(1)     | —        | O(n)   |

\* busca do menor elemento

### Benchmarks

| Ação                         | Tempo Médio        |
| ---------------------------- | ------------------ |
| Cadastro de paciente         | < 1ms              |
| Busca por CPF                | < 1ms              |
| Agendamento de consulta      | < 10ms             |
| Inserção na fila de urgência | < 5ms              |
| Compressão de dados          | 40–60% de economia |

## 🧪 Testes

### Conteúdo de Teste

* 17 pacientes simulados
* 9 consultas agendadas
* 8 urgências com prioridades diferentes
* Prontuários médicos com compressão Huffman

### Como testar

1. Carregue os dados de demonstração pelo dashboard
2. Teste cada funcionalidade da interface
3. Observe as métricas atualizarem em tempo real

## 🎓 Aplicações Didáticas

### Tabela Hash

* Função: `hash(CPF) = CPF % 1009`
* Colisões tratadas por encadeamento separado
* Aplicação: busca instantânea de pacientes

### Árvore AVL

* Rotação simples e dupla
* Fator de balanceamento entre -1 e +1
* Mantém estrutura ordenada por data/hora

### Min-Heap

* Propriedade: pai ≤ filhos
* Heapify Up/Down
* Priorização por gravidade: 1 = emergência, 2 = urgente, 3 = normal

### Huffman

* Codificação baseada em frequência
* Construção bottom-up com heap
* Compressão de textos clínicos

## 📈 Dashboard e Métricas

* Pacientes cadastrados
* Consultas agendadas (hoje vs total)
* Fila de urgência por prioridade
* Taxa média de compressão dos prontuários

## 🎬 Demonstração em Vídeo

**Duração total: 4 minutos**

| Tempo     | Tópico                         |
| --------- | ------------------------------ |
| 0:00–0:30 | Introdução e dados demo        |
| 0:30–1:10 | Cadastro e busca de pacientes  |
| 1:10–1:50 | Agendamento de consultas (AVL) |
| 1:50–2:30 | Urgências com Min-Heap         |
| 2:30–3:30 | Compressão Huffman             |
| 3:30–4:00 | Integração e conclusão         |

## 🤝 Equipe

* **Gabriel** – Tabela Hash, integração geral
* **Nathan** – Árvore AVL, Min-Heap
* **Nicolas** – Compressão Huffman, otimizações

### Como contribuir

1. Fork o repositório
2. Crie uma branch: `git checkout -b minha-feature`
3. Commit: `git commit -m "Minha contribuição"`
4. Push: `git push origin minha-feature`
5. Abra um Pull Request

## 📚 Referências

* Cormen, T. H. *Introduction to Algorithms*, 3ª edição
* Sedgewick, R. *Algorithms*, 4ª edição
* Huffman, D. A. *"A Method for the Construction of Minimum-Redundancy Codes" (1952)*
* Adelson-Velsky, G. M. e Landis, E. M. *"An algorithm for the organization of information" (1962)*

### Recursos Online

* [VisualGo - Visualizador de Estruturas](https://visualgo.net/)
* [PHP Manual](https://www.php.net/manual/pt_BR/)
* [MDN JavaScript Docs](https://developer.mozilla.org/)

## 📄 Licença

Projeto acadêmico para fins educacionais – disciplina de Estruturas de Dados.

## 📞 Contato

* **Email:** [seu-email@universidade.edu.br](mailto:seu-email@universidade.edu.br)
* **GitHub:** [https://github.com/seu-usuario/agenda-medica-v2](https://github.com/seu-usuario/agenda-medica-v2)

---

## 🏆 Conclusão

Este projeto demonstra como algoritmos clássicos e estruturas de dados podem ser aplicados em soluções reais com impacto prático. A abordagem modular e orientada a desempenho permite explorar conceitos fundamentais de ciência da computação em um contexto significativo e profissional.

**Desenvolvido com 💻 e ☕ por Gabriel, Nathan e Nicolas**

---

*Última atualização: Junho de 2025*

```

---

Se quiser, posso gerar a versão `.md` final ou um `.pdf` desse README para documentação acadêmica. Deseja?
```
