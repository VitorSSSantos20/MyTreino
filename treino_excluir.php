<?php
/**
 * Exclui um treino .
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$usuarioId = (int) $_SESSION['usuario_id'];

// Só aceita requisições POST 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: treinos.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

// Exclui apenas se o treino pertencer ao usuário logado
$stmt = $pdo->prepare('DELETE FROM treinos WHERE id = ? AND usuario_id = ?');
$stmt->execute([$id, $usuarioId]);

if ($stmt->rowCount() > 0) {
    flash_set('success', 'Treino excluído com sucesso!');
} else {
    flash_set('danger', 'Treino não encontrado.');
}

header('Location: treinos.php');
exit;
