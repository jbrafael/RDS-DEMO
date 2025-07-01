<?php
// delete.php
require_once 'db_config.php';

$message = '';
$message_type = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // --- C.R.U.D - DELETE (Excluir Produto) ---
    try {
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $message = "Produto excluído com sucesso!";
            $message_type = 'success';
        } else {
            $message = "Produto não encontrado para exclusão.";
            $message_type = 'error';
        }
    } catch (PDOException $e) {
        $message = "Erro ao excluir produto: " . $e->getMessage();
        $message_type = 'error';
    }
} else {
    $message = "ID do produto não especificado para exclusão.";
    $message_type = 'error';
}

// Redireciona de volta para a página principal com a mensagem
header("Location: index.php?message=" . urlencode($message) . "&type=" . urlencode($message_type));
exit();
?>