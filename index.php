<?php
require_once 'db_config.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = filter_var($_POST['preco'], FILTER_VALIDATE_FLOAT);

    if (!empty($nome) && $preco !== false) {
        try {
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $descricao, $preco]);
            $message = "Produto '{$nome}' adicionado com sucesso!";
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = "Erro ao adicionar produto: " . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $message = "Por favor, preencha o nome e o preço corretamente.";
        $message_type = 'error';
    }
}

$produtos = [];
try {
    $stmt = $pdo->query("SELECT id, nome, descricao, preco, data_cadastro FROM produtos ORDER BY id DESC");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Erro ao carregar produtos: " . $e->getMessage();
    $message_type = 'error';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RDS-DEMO: CRUD de Produtos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>RDS-DEMO</h1>
            <p>Demonstração de CRUD com AWS RDS e PHP</p>
        </header>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <h2>Adicionar Novo Produto</h2>
        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="add">
            <label for="nome">Nome do Produto:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao"></textarea>

            <label for="preco">Preço:</label>
            <input type="number" id="preco" name="preco" step="0.01" required>

            <button type="submit">Adicionar Produto</button>
        </form>

        <h2>Lista de Produtos</h2>
        <?php if (empty($produtos)): ?>
            <p>Nenhum produto cadastrado ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($produto['id']); ?></td>
                            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                            <td><?php echo htmlspecialchars($produto['descricao']); ?></td>
                            <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($produto['data_cadastro'])); ?></td>
                            <td class="actions">
                                <a href="edit.php?id=<?php echo $produto['id']; ?>" class="edit-btn">Editar</a>
                                <a href="delete.php?id=<?php echo $produto['id']; ?>" class="delete-btn" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>