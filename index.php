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
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'adicionado') {
        $message = '<div class="alert alert-success" role="alert">Investimento adicionado com sucesso!</div>';
    } elseif ($_GET['status'] == 'editado') {
        $message = '<div class="alert alert-success" role="alert">Investimento atualizado com sucesso!</div>';
    } elseif ($_GET['status'] == 'excluido') {
        $message = '<div class="alert alert-warning" role="alert">Investimento excluído com sucesso!</div>';
    } elseif ($_GET['status'] == 'nao_encontrado') {
        $message = '<div class="alert alert-danger" role="alert">Investimento não encontrado.</div>';
    } elseif ($_GET['status'] == 'erro_exclusao' || $_GET['status'] == 'erro_exclusao_db') {
        $message = '<div class="alert alert-danger" role="alert">Erro ao excluir investimento.</div>';
    }
}

$user_id = $_SESSION['id']; // Obtém o ID do usuário logado para filtrar os dados

// Lógica para buscar investimentos individuais (para a tabela principal do CRUD)
try {
    $stmt_individual = $conn->prepare("SELECT * FROM investimentos WHERE user_id = :user_id ORDER BY data_investimento DESC");
    $stmt_individual->bindParam(':user_id', $user_id); // Binda o user_id
    $stmt_individual->execute();
    $investimentos_individuais = $stmt_individual->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message .= '<div class="alert alert-danger" role="alert">Erro ao carregar investimentos individuais: ' . $e->getMessage() . '</div>';
    $investimentos_individuais = [];
}

// Lógica para buscar Resumo por Título (NOVO)
$resumo_por_titulo = [];
try {
    $stmt_titulo = $conn->prepare("SELECT titulo, SUM(quantidade) AS total_quantidade, SUM(valor) AS total_valor_investido FROM investimentos WHERE user_id = :user_id GROUP BY titulo ORDER BY titulo ASC");
    $stmt_titulo->bindParam(':user_id', $user_id); // Binda o user_id
    $stmt_titulo->execute();
    $resumo_por_titulo = $stmt_titulo->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message .= '<div class="alert alert-danger" role="alert">Erro ao carregar resumo por título: ' . $e->getMessage() . '</div>';
}

// Lógica para Composição da Carteira por Tipo (NOVO)
$composicao_por_tipo = [];
$total_carteira = 0;
try {
    // Busca o valor total da carteira primeiro, filtrando por user_id
    $stmt_total_carteira = $conn->prepare("SELECT SUM(valor) AS total_carteira FROM investimentos WHERE user_id = :user_id");
    $stmt_total_carteira->bindParam(':user_id', $user_id); // Binda o user_id
    $stmt_total_carteira->execute();
    $total_carteira_result = $stmt_total_carteira->fetch(PDO::FETCH_ASSOC);
    $total_carteira = $total_carteira_result['total_carteira'] ?? 0;

    // Busca o valor total por tipo, filtrando por user_id
    $stmt_tipo = $conn->prepare("SELECT tipo, SUM(valor) AS valor_por_tipo FROM investimentos WHERE user_id = :user_id GROUP BY tipo ORDER BY tipo ASC");
    $stmt_tipo->bindParam(':user_id', $user_id); // Binda o user_id
    $stmt_tipo->execute();
    $composicao_por_tipo = $stmt_tipo->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message .= '<div class="alert alert-danger" role="alert">Erro ao carregar composição por tipo: ' . $e->getMessage() . '</div>';
}
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Dashboard de Investimentos</h2>
        <?php echo $message; ?>
        <a href="adicionar.php" class="btn btn-primary mb-4"><i class="fas fa-plus"></i> Adicionar Novo Investimento</a>

        <div class="row mb-5">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Resumo por Título (Posição Agregada)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($resumo_por_titulo) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Título</th>
                                        <th>Quantidade Total</th>
                                        <th>Valor Investido Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resumo_por_titulo as $resumo): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($resumo['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($resumo['total_quantidade']); ?></td>
                                        <td>R$ <?php echo number_format($resumo['total_valor_investido'], 2, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">Nenhum dado de resumo por título disponível.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-percentage"></i> Composição da Carteira por Tipo</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($total_carteira > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Valor Total</th>
                                        <th>% da Carteira</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($composicao_por_tipo as $comp_tipo): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($comp_tipo['tipo']); ?></td>
                                        <td>R$ <?php echo number_format($comp_tipo['valor_por_tipo'], 2, ',', '.'); ?></td>
                                        <td><?php echo number_format(($comp_tipo['valor_por_tipo'] / $total_carteira) * 100, 2, ',', '.'); ?>%</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th>Total da Carteira</th>
                                        <th>R$ <?php echo number_format($total_carteira, 2, ',', '.'); ?></th>
                                        <th>100.00%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">Nenhum dado de composição por tipo disponível.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="mb-4">Registros Individuais de Investimentos</h3>
        <?php if (count($investimentos_individuais) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Quantidade</th>
                        <th>Data Investimento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($investimentos_individuais as $investimento): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($investimento['id']); ?></td>
                        <td><?php echo htmlspecialchars($investimento['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($investimento['tipo']); ?></td>
                        <td>R$ <?php echo number_format($investimento['valor'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($investimento['quantidade']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($investimento['data_investimento'])); ?></td>
                        <td>
                            <a href="detalhes.php?id=<?php echo $investimento['id']; ?>" class="btn btn-info btn-sm" title="Ver Detalhes"><i class="fas fa-eye"></i></a>
                            <a href="editar.php?id=<?php echo $investimento['id']; ?>" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="excluir.php?id=<?php echo $investimento['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este investimento?');" title="Excluir"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info" role="alert">
            Nenhum investimento cadastrado ainda. <a href="adicionar.php">Adicione um novo investimento</a>!
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
