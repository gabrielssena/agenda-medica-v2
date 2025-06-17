<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste Backend - Agenda Médica</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-item { margin: 15px 0; padding: 15px; border-radius: 8px; border-left: 4px solid #007bff; background: #f8f9fa; }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        .info { border-left-color: #17a2b8; background: #d1ecf1; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Teste do Backend - Agenda Médica v2.0</h1>
        
        <div class="test-item success">
            <h3>✅ PHP Funcionando</h3>
            <p><strong>Versão:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Data/Hora:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
        
        <div class="test-item info">
            <h3>📊 Informações do Servidor</h3>
            <p><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Não disponível'; ?></p>
            <p><strong>Método HTTP:</strong> <?php echo $_SERVER['REQUEST_METHOD']; ?></p>
            <p><strong>URI:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></p>
        </div>
        
        <div class="test-item">
            <h3>🔗 Testes de API</h3>
            <button onclick="testAPI('home')">Teste Home</button>
            <button onclick="testAPI('dashboard')">Teste Dashboard</button>
            <button onclick="testAPI('pacientes')">Teste Pacientes</button>
            <button onclick="testAPI('consultas')">Teste Consultas</button>
            <button onclick="testAPI('urgencias')">Teste Urgências</button>
            <button onclick="testAPI('stats')">Teste Stats</button>
        </div>
        
        <div id="results"></div>
    </div>

    <script>
        async function testAPI(action) {
            const resultsDiv = document.getElementById('results');
            
            try {
                const response = await fetch(`index.php?action=${action}`);
                const data = await response.json();
                
                resultsDiv.innerHTML = `
                    <div class="test-item success">
                        <h3>✅ Teste ${action} - SUCESSO</h3>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    </div>
                `;
            } catch (error) {
                resultsDiv.innerHTML = `
                    <div class="test-item error">
                        <h3>❌ Teste ${action} - ERRO</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }
    </script>
</body>
</html>