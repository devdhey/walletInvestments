<?php
session_start(); // Garante que a sessão seja iniciada
// Se o utilizador não estiver logado, redireciona para a página de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include_once 'config/database.php'; // Inclui a configuração do banco de dados
include_once 'includes/header.php'; // Inclui o cabeçalho da página

$investimento = null;
$user_id = $_SESSION['id']; // Obtém o ID do usuário logado

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Busca o investimento, filtrando também pelo user_id
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
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-info-circle"></i> Detalhes do Investimento</h4>
            </div>
            <div class="card-body">
                <?php if ($investimento): ?>
                <dl class="row">
                    <dt class="col-sm-4">ID:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($investimento['id']); ?></dd>

                    <dt class="col-sm-4">Título:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($investimento['titulo']); ?></dd>

                    <dt class="col-sm-4">Tipo:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($investimento['tipo']); ?></dd>

                    <dt class="col-sm-4">Valor Total Investido:</dt>
                    <dd class="col-sm-8">R$ <?php echo number_format($investimento['valor'], 2, ',', '.'); ?></dd>

                    <dt class="col-sm-4">Quantidade:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($investimento['quantidade']); ?></dd>

                    <dt class="col-sm-4">Data do Investimento:</dt>
                    <dd class="col-sm-8"><?php echo date('d/m/Y', strtotime($investimento['data_investimento'])); ?></dd>

                    <dt class="col-sm-4">Observações:</dt>
                    <dd class="col-sm-8"><?php echo !empty($investimento['observacoes']) ? nl2br(htmlspecialchars($investimento['observacoes'])) : 'Nenhuma'; ?></dd>

                    <dt class="col-sm-4">Data de Criação do Registro:</dt>
                    <dd class="col-sm-8"><?php echo date('d/m/Y H:i:s', strtotime($investimento['data_criacao'])); ?></dd>
                </dl>
                <?php else: ?>
                    <div class="alert alert-danger" role="alert">
                        Investimento não encontrado.
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <a href="editar.php?id=<?php echo $investimento['id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar para a lista</a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
