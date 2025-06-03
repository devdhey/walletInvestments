<?php
session_start(); // Inicia a sessão para gerenciar o estado do login
include_once 'config/database.php'; // Inclui a configuração do banco de dados

$message = ''; // Variável para mensagens de feedback

// Se o usuário já estiver logado, redireciona para a página inicial
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: index.php');
    exit;
}

// Verifica se o formulário de login foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']); // Sanitiza o nome de usuário
    $password = htmlspecialchars($_POST['password']); // Sanitiza a senha

    try {
        // Prepara a query para buscar o usuário pelo nome de usuário
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Busca o usuário

        // Verifica se o usuário existe e se a senha está correta
        if ($user && password_verify($password, $user['password'])) {
            // Login bem-sucedido: define variáveis de sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redireciona para a página inicial
            header('Location: index.php');
            exit;
        } else {
            $message = '<div class="alert alert-danger" role="alert">Nome de usuário ou senha inválidos.</div>';
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger" role="alert">Erro no banco de dados: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Meus Investimentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+pjYQddoGWhpPNemLp+g4yW9Y6T+Wk5z5Gv3zFpKg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 0.75rem;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
        }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0"><i class="fas fa-lock"></i> Login</h4>
        </div>
        <div class="card-body">
            <?php echo $message; ?>
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nome de Usuário:</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Entrar</button>
                    <a href="register.php" class="btn btn-outline-secondary">Não tem conta? Registre-se</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
