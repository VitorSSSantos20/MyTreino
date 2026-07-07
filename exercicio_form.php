<?php
/**
 * Edição de um exercício.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$usuarioId = (int) $_SESSION['usuario_id'];
$erros     = [];
$id        = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

// Carrega o exercício, conferindo se o treino é do usuário logado
$stmt = $pdo->prepare(
    'SELECT e.*, t.nome_treino, t.dia_semana
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

// Processa a edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exercicio['nome']       = trim($_POST['nome'] ?? '');
    $exercicio['series']     = trim($_POST['series'] ?? '');
    $exercicio['repeticoes'] = trim($_POST['repeticoes'] ?? '');
    $exercicio['observacao'] = trim($_POST['observacao'] ?? '');

    // ----- Validação no PHP -----
    if (mb_strlen($exercicio['nome']) < 2) {
        $erros[] = 'Informe o nome do exercício.';
    }
    if (!ctype_digit((string) $exercicio['series']) || (int) $exercicio['series'] < 1 || (int) $exercicio['series'] > 20) {
        $erros[] = 'Informe as séries (número de 1 a 20).';
    }
    if ($exercicio['repeticoes'] === '' || mb_strlen($exercicio['repeticoes']) > 20) {
        $erros[] = 'Informe as repetições (ex.: 12 ou 10-12).';
    }

    if (!$erros) {
        $stmt = $pdo->prepare(
            'UPDATE exercicios e
              INNER JOIN treinos t ON t.id = e.treino_id
                SET e.nome = ?, e.series = ?, e.repeticoes = ?, e.observacao = ?
              WHERE e.id = ? AND t.usuario_id = ?'
        );
        $stmt->execute([
            $exercicio['nome'],
            (int) $exercicio['series'],
            $exercicio['repeticoes'],
            $exercicio['observacao'] !== '' ? $exercicio['observacao'] : null,
            $id,
            $usuarioId,
        ]);

        flash_set('success', 'Exercício editado com sucesso!');
        header('Location: exercicios.php?treino=' . (int) $exercicio['treino_id']);
        exit;
    }
}

$tituloPagina = 'Editar exercício';
$paginaAtiva  = 'treinos';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-6">

        <a href="exercicios.php?treino=<?= (int) $exercicio['treino_id'] ?>" class="btn btn-mt-suave btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Voltar para os exercícios
        </a>

        <div class="mt-card">
            <div class="card-body">
                <span class="badge-dia mb-2 d-inline-block">
                    <?= e(dias_semana()[(int) $exercicio['dia_semana']]) ?> · <?= e($exercicio['nome_treino']) ?>
                </span>
                <h1 class="h5 mb-3"><i class="bi bi-pencil-square me-1"></i> Editar exercício</h1>

                <?php if ($erros): ?>
                    <div class="alert alert-danger mt-alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= e($erros[0]) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="exercicio_form.php?id=<?= $id ?>" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do exercício</label>
                        <input type="text" class="form-control" id="nome" name="nome"
                               value="<?= e($exercicio['nome']) ?>" minlength="2" maxlength="100" required>
                        <div class="invalid-feedback">Informe o nome do exercício.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="series" class="form-label">Séries</label>
                            <input type="number" class="form-control" id="series" name="series"
                                   value="<?= e($exercicio['series']) ?>" min="1" max="20" required>
                            <div class="invalid-feedback">De 1 a 20.</div>
                        </div>
                        <div class="col-6">
                            <label for="repeticoes" class="form-label">Repetições</label>
                            <input type="text" class="form-control" id="repeticoes" name="repeticoes"
                                   value="<?= e($exercicio['repeticoes']) ?>" maxlength="20" required>
                            <div class="invalid-feedback">Ex.: 12 ou 10-12.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observacao" class="form-label">Observação <span class="text-muted fw-normal">(opcional)</span></label>
                        <input type="text" class="form-control" id="observacao" name="observacao"
                               value="<?= e($exercicio['observacao']) ?>" maxlength="255">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-mt">
                            <i class="bi bi-check-lg"></i> Salvar alterações
                        </button>
                        <a href="exercicios.php?treino=<?= (int) $exercicio['treino_id'] ?>" class="btn btn-mt-suave">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
