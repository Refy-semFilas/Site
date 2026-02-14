<?php
require "../databaseConnection.php";

// pega os dados do forms
$user = $_POST['user'];
$senha = $_POST['senha'];


$sql = $conn->prepare("SELECT * FROM usuarios WHERE USERNAME = ?");
$sql->bind_param("s", $user);
$sql->execute();
$result = $sql->get_result();

function alerta($mensagem, $redirect = null, $voltar = false)
{
    echo "
    <html>
    <head>
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background: transparent;
            display: flex;
            justify-content: center;
            margin-top: 30px
        }

        .alert-box {
            background: linear-gradient(135deg, #ff9100, #ff5e00);
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            font-size: 16px;
            animation: slide 0.4s ease;
            height: 40px;
            width:200px;
            display: flex;
            justify-content: center;
            align-items: center;

        }

        @keyframes slide {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
    </head>

    <body>
        <div class='alert-box'>$mensagem</div>

        <script>
            setTimeout(() => {";

    if ($redirect) {
        echo "window.location.href = '$redirect';";
    }

    if ($voltar) {
        echo "window.history.back();";
    }

    echo "
            }, 2500);
        </script>
    </body>
    </html>
    ";
    exit;
}


if ($result->num_rows === 1) {
    $dados = $result->fetch_assoc();

    // para criptografia
    if (password_verify($senha, $dados['SENHA'])) {
        session_start();
        $_SESSION['user_id'] = $dados['ID'];
        $_SESSION['username'] = $dados['USERNAME'];
        $_SESSION['tipo'] = $dados['TIPO'] ?? 'cliente';

        if ($_SESSION['tipo'] === 'admin') {
            header("Location: ../Partedocantineiro/dashboard.php");
        } else {
            header("Location: home.html");
        }
        exit;

    }

    //verifica senha
    else {
        alerta('Senha incorreta', null, true);
        exit;
    }
}
// se n existir o user
else {
    alerta('Usuario não encontrado. Cadastre-se!', 'registerUserForm.html');
    ;
    exit;
}
?>