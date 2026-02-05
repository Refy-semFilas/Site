# 📧 Configuração de Email - Cantina System

## 🚀 Como Configurar o Envio de Email

O sistema de recuperação de senha está funcionando, mas para receber emails reais, você precisa configurar seu ambiente.

### 🔧 Opção 1: Configuração Local (Desenvolvimento)

Para testar localmente com XAMPP:

1. **Configure o php.ini:**
   ```ini
   [mail function]
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = seu-email@gmail.com
   
   ; Para Gmail, você precisará configurar auth
   auth_username = seu-email@gmail.com
   auth_password = sua-senha-de-app
   ```

2. **Configure o sendmail.ini:**
   ```ini
   sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
   
   [sendmail]
   smtp_server=smtp.gmail.com
   smtp_port=587
   smtp_ssl=auto
   auth_username=seu-email@gmail.com
   auth_password=sua-senha-de-app
   ```

### 🔧 Opção 2: Usar Serviço de Email (Recomendado)

#### **Mailtrap (para testes):**
1. Crie conta gratuita em [mailtrap.io](https://mailtrap.io)
2. Configure as credenciais no sistema
3. Use as credenciais SMTP no php.ini

#### **Mailgun/SendGrid (para produção):**
1. Crie conta no serviço escolhido
2. Instale biblioteca PHPMailer via Composer:
   ```bash
   composer require phpmailer/phpmailer
   ```
3. Configure as credenciais no `config.php`

### 📝 Configuração Gmail (Mais Comum)

1. **Ative 2FA na sua conta Google**
2. **Crie uma Senha de App:**
   - Vá para: [contas.google.com/apppasswords](https://myaccount.google.com/apppasswords)
   - Selecione: "App" → "Outro"
   - Digite: "Cantina System"
   - Copie a senha gerada (16 caracteres)

3. **Configure no config.php:**
   ```php
   // No arquivo config.php, atualize as constantes:
   define('EMAIL_FROM_ADDRESS', 'seu-email-real@gmail.com');
   ```

### 🗂️ Estrutura de Arquivos

```
├── config.php              # Configurações de email
├── Partedocliente/
│   ├── forgotPassword.html     # Formulário de recuperação
│   ├── resetPassword.php       # Processa solicitação
│   ├── resetPasswordForm.html  # Formulário de nova senha
│   └── updatePassword.php      # Atualiza senha
├── email_log.txt           # Log de envios (debug)
└── email_log.html          # Cópias dos emails (debug)
```

### 🐛 Debug e Testes

O sistema salva automaticamente:
- **`email_log.txt`** - Registro de todos os envios
- **`email_log.html`** - Cópias dos emails enviados

Em modo desenvolvimento (localhost), os emails são salvos em arquivo em vez de serem enviados.

### ⚡ Testes Rápidos

1. **Acesse:** `http://localhost/Site/Partedocliente/forgotPassword.html`
2. **Digite:** Email e usuário cadastrados
3. **Verifique:** 
   - Console do navegador para tokens de debug
   - Arquivo `email_log.html` para ver o email
   - Redirecionamento automático para página de reset

### 🔒 Segurança

- ✅ Tokens com expiração (1 hora)
- ✅ Tokens únicos e aleatórios
- ✅ Hash seguro de senhas
- ✅ Validação de inputs
- ✅ Proteção contra SQL Injection

### 📱 Features Implementadas

- ✅ Email HTML responsivo e profissional
- ✅ Redirecionamento automático após envio
- ✅ Indicador visual de força de senha
- ✅ Validação em tempo real
- ✅ Interface moderna e intuitiva
- ✅ Suporte a dispositivos móveis

### 🚨 Importante

- Em produção, remova os arquivos de log de debug
- Configure HTTPS no servidor
- Use credenciais reais de email
- Teste todos os cenários antes de ir ao ar

---

**Dica:** Para testar rápido, use Mailtrap. É gratuito e perfeito para desenvolvimento!