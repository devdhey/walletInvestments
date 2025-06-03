<?php
session_start(); // Garante que a sessão seja iniciada
// Se o utilizador não estiver logado, redireciona para a página de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php'; // Inclui a configuração do banco de dados

$user_id = $_SESSION['id']; // Obtém o ID do usuário logado

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // Exclui o investimento, filtrando por id e user_id
        $stmt = $conn->prepare("DELETE FROM investimentos WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id); // Binda o user_id

        if ($stmt->execute()) {
            header("Location: index.php?status=excluido");
            exit();
        } else {
            error_log("Erro ao excluir investimento (PHP): Falha na execução da query para ID " . $id . " e UserID " . $user_id);
            header("Location: index.php?status=erro_exclusao");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro ao excluir investimento (PDO Exception): " . $e->getMessage());
        header("Location: index.php?status=erro_exclusao_db");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
