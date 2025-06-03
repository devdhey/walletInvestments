<?php
session_start(); // Garante que a sessão seja iniciada
// Se o utilizador não estiver logado, redireciona para a página de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php'; // Inclui a configuração do banco de dados
include_once 'includes/header.php'; // Manter este include para a estrutura HTML

$message = '';
$investimento = null;
$user_id = $_SESSION['id']; // Obtém o ID do usuário logado

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Busca o investimento, filtrando também pelo user_id para garantir que o usuário só edite seus próprios investimentos
    $stmt = $conn->prepare("SELECT * FROM investimentos WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $user_id); // Binda o user_id
    $stmt->execute();
    $investimento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$investimento) {
        // Se o investimento não for encontrado ou não pertencer ao usuário, redireciona
        header("Location: index.php?status=nao_encontrado");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = htmlspecialchars($_POST['id']);
    $titulo = htmlspecialchars($_POST['titulo']);
    $tipo = htmlspecialchars($_POST['tipo']);
    $valor = floatval(str_replace(',', '.', $_POST['valor'])); 
    $quantidade = intval($_POST['quantidade']);
    $data_investimento = htmlspecialchars($_POST['data_investimento']);
    $observacoes = htmlspecialchars($_POST['observacoes']);
    // O user_id já está definido acima, não precisa ser lido do POST novamente

    try {
        // Atualiza o investimento, filtrando por id e user_id
        $stmt = $conn->prepare("UPDATE investimentos SET titulo = :titulo, tipo = :tipo, valor = :valor, quantidade = :quantidade, data_investimento = :data_investimento, observacoes = :observacoes WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':data_investimento', $data_investimento);
        $stmt->bindParam(':observacoes', $observacoes);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id); // Binda o user_id

        if ($stmt->execute()) {
            header("Location: index.php?status=editado");
            exit();
        } else {
            $errorInfo = $stmt->errorInfo();
            $message = '<div class="alert alert-danger" role="alert">Erro ao atualizar investimento. Detalhes do SQL: ' . htmlspecialchars($errorInfo[2]) . ' (Código SQLSTATE: ' . htmlspecialchars($errorInfo[0]) . ', Código Driver: ' . htmlspecialchars($errorInfo[1]) . ')</div>';
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger" role="alert">Erro no banco de dados (PDO Exception): ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// Re-busca os dados do investimento *após* a tentativa de POST para mostrar o estado atual do DB
// Esta parte do código é para garantir que o formulário exiba os dados mais recentes,
// mesmo se o POST não redirecionar (ex: em caso de erro de validação).
if (isset($id) && $investimento) { 
    $stmt_refetch = $conn->prepare("SELECT * FROM investimentos WHERE id = :id AND user_id = :user_id");
    $stmt_refetch->bindParam(':id', $id);
    $stmt_refetch->bindParam(':user_id', $user_id);
    $stmt_refetch->execute();
    $investimento = $stmt_refetch->fetch(PDO::FETCH_ASSOC);
}

?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <h2 class="mb-4">Editar Investimento</h2>
        <?php echo $message; ?>
        <?php if ($investimento): ?>
        <form action="editar.php?id=<?php echo htmlspecialchars($investimento['id']); ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($investimento['id']); ?>">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título do Investimento:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($investimento['titulo']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de Investimento:</label>
                <select class="form-select" id="tipo" name="tipo" required>
                    <option value="">Selecione o Tipo</option>
                    <option value="Ações" <?php echo ($investimento['tipo'] == 'Ações') ? 'selected' : ''; ?>>Ações</option>
                    <option value="FIIs" <?php echo ($investimento['tipo'] == 'FIIs') ? 'selected' : ''; ?>>FIIs (Fundos Imobiliários)</option>
                    <option value="CDB" <?php echo ($investimento['tipo'] == 'CDB') ? 'selected' : ''; ?>>CDB</option>
                    <option value="LCI/LCA" <?php echo ($investimento['tipo'] == 'LCI/LCA') ? 'selected' : ''; ?>>LCI/LCA</option>
                    <option value="Tesouro Direto" <?php echo ($investimento['tipo'] == 'Tesouro Direto') ? 'selected' : ''; ?>>Tesouro Direto</option>
                    <option value="Previdência Privada" <?php echo ($investimento['tipo'] == 'Previdência Privada') ? 'selected' : ''; ?>>Previdência Privada</option>
                    <option value="Criptomoedas" <?php echo ($investimento['tipo'] == 'Criptomoedas') ? 'selected' : ''; ?>>Criptomoedas</option>
                    <option value="Outro" <?php echo ($investimento['tipo'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="valor" class="form-label">Valor Investido (R$):</label>
                <input type="number" step="0.01" class="form-control" id="valor" name="valor" value="<?php echo htmlspecialchars($investimento['valor']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade:</label>
                <input type="number" class="form-control" id="quantidade" name="quantidade" value="<?php echo htmlspecialchars($investimento['quantidade']); ?>" required min="1">
            </div>
            <div class="mb-3">
                <label for="data_investimento" class="form-label">Data do Investimento:</label>
                <input type="date" class="form-control" id="data_investimento" name="data_investimento" value="<?php echo htmlspecialchars($investimento['data_investimento']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="observacoes" class="form-label">Observações:</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($investimento['observacoes']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-sync-alt"></i> Atualizar Investimento</button>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
        </form>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                Investimento não encontrado.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
