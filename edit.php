<?php
// edit.php
require_once 'db_config.php';

$message = '';
$message_type = '';
$produto = null;

// Garante que um ID foi passado e é um número
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // --- C.R.U.D - READ (Ler Produto para Edição) ---
    try {
        $stmt = $pdo->prepare("SELECT id, nome, descricao, preco FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            $message = "Produto não encontrado.";
            $message_type = 'error';
        }
    } catch (PDOException $e) {
        $message = "Erro ao buscar produto: " . $e->getMessage();
        $message_type = 'error';
    }
} else {
    $message = "ID do produto não especificado.";
    $message_type = 'error';
}

// --- C.R.U.D - UPDATE (Atualizar Produto) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = filter_var($_POST['preco'], FILTER_VALIDATE_FLOAT);

    if ($id && !empty($nome) && $preco !== false) {
        try {
            $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ? WHERE id = ?");
            $stmt->execute([$nome, $descricao, $preco, $id]);
            $message = "Produto '{$nome}' atualizado com sucesso!";
            $message_type = 'success';
            // Recarregar os dados do produto para exibir as alterações no formulário
            $stmt = $pdo->prepare("SELECT id, nome, descricao, preco FROM produtos WHERE id = ?");
            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $message = "Erro ao atualizar produto: " . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $message = "Dados inválidos para atualização.";
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RDS-DEMO: Editar Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>RDS-DEMO</h1>
            <p>Edição de Produto</p>
        </header>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($produto): ?>
            <h2>Editar Produto: <?php echo htmlspecialchars($produto['nome']); ?></h2>
            <form method="POST" action="edit.php?id=<?php echo htmlspecialchars($produto['id']); ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($produto['id']); ?>">

                <label for="nome">Nome do Produto:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>

                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>

                <label for="preco">Preço:</label>
                <input type="number" id="preco" name="preco" step="0.01" value="<?php echo htmlspecialchars($produto['preco']); ?>" required>

                <button type="submit">Atualizar Produto</button>
            </form>
        <?php else: ?>
            <p>Produto não encontrado ou ID inválido.</p>
        <?php endif; ?>
        <p><a href="index.php" style="color: #232F3E; text-decoration: none; font-weight: bold;">&#8592; Voltar para a Lista</a></p>
    </div>
</body>
</html>