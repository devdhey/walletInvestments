<?php
session_start(); // Inicia a sessão

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destrói a sessão.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Destrói os dados da sessão

// Redireciona para a página de login
header('Location: login.php');
exit;
?>
