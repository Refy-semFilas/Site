<?php
// Script de teste para configuração de email
require_once "config.php";
require_once "databaseConnection.php";

echo "<!DOCTYPE html>
<html lang='pt-br'>
<head>
    <meta charset='UTF-8'>
    <title>Teste de Configuração de Email</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .test-btn { background: #ff6200; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 10px 5px; }
        .test-btn:hover { background: #ff9500; }
        .test-btn:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Teste de Configuração de Email</h1>
        
        <div class='status info'>
            <h3>📊 Status da Configuração:</h3>";
        
        // Verificar configurações
        $configErrors = [];
        $configWarnings = [];

        if (!function_exists('mail')) {
            $configErrors[] = "Função mail() do PHP não está disponível";
        }

        if (strpos(SITE_URL, 'localhost') !== false) {
            $configWarnings[] = "Executando em modo desenvolvimento - emails serão salvos em arquivos";
        }

        // Verificar se tabela de usuários existe
        try {
            $result = $conn->query("SHOW TABLES LIKE 'usuarios'");
            if ($result->num_rows === 0) {
                $configErrors[] = "Tabela 'usuarios' não encontrada no banco de dados";
            }
        } catch (Exception $e) {
            $configErrors[] = "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }

        // Exibir status
        if (empty($configErrors)) {
            echo "<p class='success'>✅ Configuração básica está OK!</p>";
        } else {
            foreach ($configErrors as $error) {
                echo "<p class='error'>❌ " . htmlspecialchars($error) . "</p>";
            }
        }

        if (!empty($configWarnings)) {
            foreach ($configWarnings as $warning) {
                echo "<p class='warning'>⚠️ " . htmlspecialchars($warning) . "</p>";
            }
        }

        echo "
        </div>

        <div class='status info'>
            <h3>📋 Informações do Sistema:</h3>
            <pre>
Versão PHP: " . PHP_VERSION . "
Site URL: " . SITE_URL . "
Expiração Token: " . TOKEN_EXPIRY_HOURS . " hora(s)
Email From: " . EMAIL_FROM_ADDRESS . "
Função mail(): " . (function_exists('mail') ? 'Disponível' : 'Não disponível') . "
            </pre>
        </div>";

        // Testar envio de email se solicitado
        if (isset($_POST['test_email'])) {
            $testEmail = $_POST['test_email'] ?? '';
            $testUsername = $_POST['test_username'] ?? 'UsuarioTeste';
            
            if (!empty($testEmail)) {
                $testToken = bin2hex(random_bytes(16));
                $testLink = generateResetLink($testToken);
                $testBody = createRecoveryEmailBody($testUsername, $testLink);
                $testSubject = "🧪 Teste de Email - Cantina System";
                
                $emailSent = sendEmail($testEmail, $testSubject, $testBody, true);
                logEmail($testEmail, $testSubject, $emailSent);
                
                if ($emailSent) {
                    echo "<div class='status success'>
                        <h3>✅ Email de teste enviado com sucesso!</h3>
                        <p>Para: " . htmlspecialchars($testEmail) . "</p>
                        <p>Verifique sua caixa de entrada (e pasta de spam).</p>";
                    
                    if (strpos(SITE_URL, 'localhost') !== false) {
                        echo "<p><strong>Modo desenvolvimento:</strong> Email salvo em <code>email_log.html</code></p>";
                    }
                    
                    echo "</div>";
                } else {
                    echo "<div class='status error'>
                        <h3>❌ Falha ao enviar email de teste</h3>
                        <p>Verifique a configuração do seu servidor SMTP.</p>
                    </div>";
                }
            }
        }

        // Verificar logs
        if (file_exists(__DIR__ . '/email_log.txt')) {
            $logContent = file_get_contents(__DIR__ . '/email_log.txt');
            $logLines = array_filter(explode("\n", trim($logContent)));
            $totalEmails = count($logLines);
            $recentEmails = array_slice($logLines, -5);
            
            echo "<div class='status info'>
                <h3>📊 Estatísticas de Email:</h3>
                <p><strong>Total de emails processados:</strong> " . $totalEmails . "</p>
                <p><strong>Envios recentes:</strong></p>
                <pre>";
            foreach ($recentEmails as $line) {
                echo htmlspecialchars($line) . "\n";
            }
            echo "</pre>
                <p><small>Log completo em: <code>email_log.txt</code></small></p>
            </div>";
        }

        echo "
        <div class='status info'>
            <h3>🧪 Testar Envio de Email:</h3>
            <form method='post'>
                <div style='margin-bottom: 15px;'>
                    <label for='test_email'>Email para teste:</label><br>
                    <input type='email' id='test_email' name='test_email' 
                           placeholder='seu-email@teste.com' 
                           style='width: 300px; padding: 8px; margin-top: 5px;' required>
                </div>
                <div style='margin-bottom: 15px;'>
                    <label for='test_username'>Nome de usuário:</label><br>
                    <input type='text' id='test_username' name='test_username' 
                           placeholder='UsuarioTeste' 
                           style='width: 300px; padding: 8px; margin-top: 5px;' 
                           value='UsuarioTeste'>
                </div>
                <button type='submit' name='test_email' class='test-btn'>
                    📧 Enviar Email de Teste
                </button>
            </form>
        </div>

        <div class='status warning'>
            <h3>📖 Próximos Passos:</h3>
            <ol>
                <li>Configure seu servidor SMTP no php.ini ou use um serviço de email</li>
                <li>Teste o envio usando o formulário acima</li>
                <li>Verifique se está recebendo os emails</li>
                <li>Teste o fluxo completo de recuperação de senha</li>
                <li>Em produção, remova os arquivos de log de debug</li>
            </ol>
        </div>

        <div style='text-align: center; margin-top: 30px;'>
            <a href='Partedocliente/forgotPassword.html' class='test-btn'>
                🔄 Ir para página de recuperação
            </a>
        </div>
    </div>
</body>
</html>";
?>