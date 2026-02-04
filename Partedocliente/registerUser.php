<?php
require "../databaseConnection.php";

// Validar se os campos foram preenchidos
if (empty($_POST["user"]) || empty($_POST["email"]) || empty($_POST["senha"])) {
    echo "<script>
        alert('Por favor, preencha todos os campos obrigatórios!');
        window.location.href = 'registerUserForm.html';
        </script>";
    exit;
}

$username = trim($_POST["user"]);
$email = trim($_POST["email"]);
$senha = $_POST["senha"];
$tipo = $_POST["tipo"] ?? 'cliente';

// Validar formato do email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
        alert('Por favor, digite um email válido!');
        window.location.href = 'registerUserForm.html';
        </script>";
    exit;
}

// Validar força da senha
if (strlen($senha) < 6) {
    echo "<script>
        alert('A senha deve ter pelo menos 6 caracteres!');
        window.location.href = 'registerUserForm.html';
        </script>";
    exit;
}

// Verificar se o email já existe
$sql = $conn->prepare("SELECT ID FROM usuarios WHERE EMAIL = ?");
$sql->bind_param("s", $email);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    echo "<script>
        alert('⚠️ Este email já está em uso!\\n\\nPor favor:\\n• Use outro endereço de email\\n• Ou faça login com sua conta existente');
        window.location.href = 'registerUserForm.html';
        </script>";
    exit;
}

// Verificar se o username já existe
$sql = $conn->prepare("SELECT ID FROM usuarios WHERE USERNAME = ?");
$sql->bind_param("s", $username);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    echo "<script>
        alert('Este nome de usuário já está em uso! Por favor, escolha outro.');
        window.location.href = 'registerUserForm.html';
        </script>";
    exit;
}

// Hash da senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Inserir novo usuário
$stmt = $conn->prepare("INSERT INTO usuarios (USERNAME, EMAIL, SENHA, TIPO) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $senhaHash, $tipo);

if ($stmt->execute()) {
    echo "<script>
        alert('✅ Cadastro realizado com sucesso!\\n\\nFaça login para continuar.');
        window.location.href = 'loginForm.html';
        </script>";
} else {
    echo "<script>
        alert('❌ Erro ao cadastrar usuário. Tente novamente.\\n\\nSe o problema persistir, entre em contato com o suporte.');
        window.location.href = 'registerUserForm.html';
        </script>";
}
?>
