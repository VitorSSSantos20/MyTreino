<?php
/**
 * Exclui um exercício. Recebe o id por POST.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$usuarioId = (int) $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: treinos.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

// Descobre o treino do exercício (para voltar à página certa)
// e confere se ele pertence ao usuário logado
$stmt = $pdo->prepare(
    'SELECT e.id, e.treino_id
       FROM exercicios e
       INNER JOIN treinos t ON t.id = e.treino_id
      WHERE e.id = ? AND t.usuario_id = ?
      LIMIT 1'
);
$stmt->execute([$id, $usuarioId]);
$exercicio = $stmt->fetch();

if (!$exercicio) {
    flash_set('danger', 'Exercício não encontrado.');
    header('Location: treinos.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM exercicios WHERE id = ?');
$stmt->execute([$id]);

flash_set('success', 'Exercício excluído com sucesso!');
header('Location: exercicios.php?treino=' . (int) $exercicio['treino_id']);
exit;
