<?php
session_start(); // Inicia a sessão
include_once 'config/database.php'; // Inclui a configuração do banco de dados

$message = ''; // Variável para mensagens de feedback

// Se o usuário já estiver logado, redireciona para a página inicial
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: index.php');
    exit;
}

// Verifica se o formulário de registro foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);

    // Validação básica
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = '<div class="alert alert-danger" role="alert">Por favor, preencha todos os campos.</div>';
    } elseif ($password !== $confirm_password) {
        $message = '<div class="alert alert-danger" role="alert">As senhas não coincidem.</div>';
    } elseif (strlen($password) < 6) {
        $message = '<div class="alert alert-danger" role="alert">A senha deve ter pelo menos 6 caracteres.</div>';
    } else {
        try {
            // Verifica se o nome de usuário já existe
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $message = '<div class="alert alert-danger" role="alert">Nome de usuário já existe. Escolha outro.</div>';
            } else {
                // Hash da senha antes de armazenar no banco de dados
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insere o novo usuário no banco de dados
                $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hashed_password);

                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success" role="alert">Usuário registrado com sucesso! Você pode <a href="login.php">fazer login agora</a>.</div>';
                } else {
                    $message = '<div class="alert alert-danger" role="alert">Erro ao registrar usuário.</div>';
                }
            }
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger" role="alert">Erro no banco de dados: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Meus Investimentos</title>
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
        .register-card {
            max-width: 450px;
            width: 100%;
            padding: 20px;
            border-radius: 0.75rem;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
        }
    </style>
</head>
<body>
    <div class="card register-card">
        <div class="card-header bg-success text-white text-center">
            <h4 class="mb-0"><i class="fas fa-user-plus"></i> Registrar Nova Conta</h4>
        </div>
        <div class="card-body">
            <?php echo $message; ?>
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nome de Usuário:</label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Senha:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success"><i class="fas fa-user-plus"></i> Registrar</button>
                    <a href="login.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Voltar para Login</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
