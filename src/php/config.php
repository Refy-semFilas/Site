<?php
define('EMAIL_FROM_NAME', 'Cantina System');
define('EMAIL_FROM_ADDRESS', 'noreply@cantina.com');

define('SITE_URL', 'http://localhost/Site'); // URL do seu site
define('TOKEN_EXPIRY_HOURS', 1); // Tempo de expiração do token

function sendEmail($to, $subject, $body, $isHTML = true) {
    $headers = "MIME-Version: 1.0\r\n";
    
    if ($isHTML) {
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    } else {
        $headers .= "Content-type:text/plain;charset=UTF-8\r\n";
    }
    
    $headers .= "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM_ADDRESS . ">\r\n";
    $headers .= "Reply-To: " . EMAIL_FROM_ADDRESS . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    return mail($to, $subject, $body, $headers);
}

// link de reset de senha
function generateResetLink($token) {
    return SITE_URL . '/resetPasswordForm.html?token=' . $token;
}

//criar o corpo do email de recuperação
function createRecoveryEmailBody($username, $resetLink) {
    return "
    <html>
    <head>
        <title>Recuperação de Senha</title>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4;'>
        <div style='max-width: 600px; margin: 20px auto; background-color: white; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1);'>
            
            <div style='background: linear-gradient(135deg, #ff6200, #ff9500); color: white; text-align: center; padding: 30px 20px;'>
                <h1 style='margin: 0; font-size: 28px;'>Refy</h1>
                <p style='margin: 5px 0 0 0; opacity: 0.9;'>Recuperação de Senha</p>
            </div>
            
            <div style='padding: 40px 30px;'>
                <h2 style='color: #ff6200; margin-top: 0;'>Olá, " . htmlspecialchars($username) . "!</h2>
                
                <p style='font-size: 16px; margin-bottom: 30px;'>Para redefinir sua senha, clique no botão abaixo:</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='" . htmlspecialchars($resetLink) . "' 
                       style='background: linear-gradient(135deg, #ff6200, #ff9500); color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 16px; display: inline-block; box-shadow: 0 4px 15px rgba(255,98,0,0.3); transition: transform 0.3s;'>
                        Redefinir Minha Senha
                    </a>
                </div>
                
                <div style='background-color: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 4px solid #ff6200;'>
                    <p style='margin: 0; font-size: 14px; color: #666;'>
                        <strong>Ou copie e cole este link no seu navegador:</strong><br>
                        <span style='word-break: break-all; color: #ff6200; font-family: monospace;'>" . htmlspecialchars($resetLink) . "</span>
                    </p>
                </div>
                
                <div style='margin-top: 30px; padding: 20px; background-color: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;'>
                    <div style='display: flex; align-items: center; margin-bottom: 10px;'>
                        <strong style='color: #856404;'>Importante:</strong>
                    </div>
                </div>
            </div>
            
            <div style='background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;'>
                <p style='margin: 0; font-size: 12px; color: #6c757d;'>
                    © " . date('Y') . " Refy. Todos os direitos reservados.<br>
                </p>
            </div>
        </div>
        
        <style>
            @media only screen and (max-width: 600px) {
                div[style*='max-width: 600px'] {
                    margin: 0 !important;
                    border-radius: 0 !important;
                }
                div[style*='padding: 40px 30px'] {
                    padding: 20px !important;
                }
                a[style*='padding: 15px 40px'] {
                    padding: 12px 25px !important;
                    font-size: 14px !important;
                }
            }
        </style>
    </body>
    </html>";
}

function logEmail($to, $subject, $sent) {
    $logEntry = sprintf(
        "[%s] Para: %s | Assunto: %s | Status: %s\n",
        date('Y-m-d H:i:s'),
        $to,
        $subject,
        $sent ? 'ENVIADO' : 'FALHOU'
    );
    
    file_put_contents(__DIR__ . '/email_log.txt', $logEntry, FILE_APPEND | LOCK_EX);
}
?>