<?php
session_start(); // Garante que a sessão seja iniciada
// Se o utilizador não estiver logado, redireciona para a página de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php'; // Inclui a configuração do banco de dados
include_once 'includes/header.php'; // Inclui o cabeçalho da página

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = htmlspecialchars($_POST['titulo']);
    $tipo = htmlspecialchars($_POST['tipo']);
    $valor = floatval(str_replace(',', '.', $_POST['valor']));
    $quantidade = intval($_POST['quantidade']);
    $data_investimento = htmlspecialchars($_POST['data_investimento']);
    $observacoes = htmlspecialchars($_POST['observacoes']);
    $user_id = $_SESSION['id']; // Obtém o ID do usuário logado

    try {
        // Inclui 'user_id' na query INSERT
        $stmt = $conn->prepare("INSERT INTO investimentos (user_id, titulo, tipo, valor, quantidade, data_investimento, observacoes) VALUES (:user_id, :titulo, :tipo, :valor, :quantidade, :data_investimento, :observacoes)");
        $stmt->bindParam(':user_id', $user_id); // Binda o user_id
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':data_investimento', $data_investimento);
        $stmt->bindParam(':observacoes', $observacoes);

        if ($stmt->execute()) {
            header("Location: index.php?status=adicionado");
            exit();
        } else {
            $message = '<div class="alert alert-danger" role="alert">Erro ao adicionar investimento.</div>';
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger" role="alert">Erro: ' . $e->getMessage() . '</div>';
    }
}
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h2 class="mb-4">Adicionar Novo Investimento</h2>
        <?php echo $message; ?>
        <form action="adicionar.php" method="POST">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título do Investimento:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de Investimento:</label>
                <select class="form-select" id="tipo" name="tipo" required>
                    <option value="">Selecione o Tipo</option>
                    <option value="Ações">Ações</option>
                    <option value="FIIs">FIIs (Fundos Imobiliários)</option>
                    <option value="CDB">CDB</option>
                    <option value="LCI/LCA">LCI/LCA</option>
                    <option value="Tesouro Direto">Tesouro Direto</option>
                    <option value="Previdência Privada">Previdência Privada</option>
                    <option value="Criptomoedas">Criptomoedas</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="valor" class="form-label">Valor Total Investido (R$):</label>
                <input type="number" step="0.01" class="form-control" id="valor" name="valor" required>
            </div>
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade:</label>
                <input type="number" class="form-control" id="quantidade" name="quantidade" required min="1">
            </div>
            <div class="mb-3">
                <label for="data_investimento" class="form-label">Data do Investimento:</label>
                <input type="date" class="form-control" id="data_investimento" name="data_investimento" required>
            </div>
            <div class="mb-3">
                <label for="observacoes" class="form-label">Observações:</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar Investimento</button>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
        </form>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
