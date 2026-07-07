<?php
/**
 * Exercícios de um treino Lista os exercícios e permite adicionar um novo na mesma página.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$usuarioId = (int) $_SESSION['usuario_id'];
$treinoId  = (int) ($_GET['treino'] ?? $_POST['treino_id'] ?? 0);
$erros     = [];

// Campos do formulário de novo exercício 
$novo = ['nome' => '', 'series' => '', 'repeticoes' => '', 'observacao' => ''];

// Carrega o treino, sempre conferindo se pertence ao usuário logado
$stmt = $pdo->prepare('SELECT * FROM treinos WHERE id = ? AND usuario_id = ? LIMIT 1');
$stmt->execute([$treinoId, $usuarioId]);
$treino = $stmt->fetch();

if (!$treino) {
    flash_set('danger', 'Treino não encontrado.');
    header('Location: treinos.php');
    exit;
}

// Dia de descanso não tem exercícios
if ($treino['tipo_dia'] === 'descanso') {
    flash_set('info', 'Dias de descanso não têm exercícios.');
    header('Location: treinos.php');
    exit;
}

// Processa o cadastro de um novo exercício
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo['nome']       = trim($_POST['nome'] ?? '');
    $novo['series']     = trim($_POST['series'] ?? '');
    $novo['repeticoes'] = trim($_POST['repeticoes'] ?? '');
    $novo['observacao'] = trim($_POST['observacao'] ?? '');

    // ----- Validação no PHP -----
    if (mb_strlen($novo['nome']) < 2) {
        $erros[] = 'Informe o nome do exercício.';
    }
    if (!ctype_digit($novo['series']) || (int) $novo['series'] < 1 || (int) $novo['series'] > 20) {
        $erros[] = 'Informe as séries (número de 1 a 20).';
    }
    if ($novo['repeticoes'] === '' || mb_strlen($novo['repeticoes']) > 20) {
        $erros[] = 'Informe as repetições (ex.: 12 ou 10-12).';
    }

    if (!$erros) {
        $stmt = $pdo->prepare(
            'INSERT INTO exercicios (treino_id, nome, series, repeticoes, observacao)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $treinoId,
            $novo['nome'],
            (int) $novo['series'],
            $novo['repeticoes'],
            $novo['observacao'] !== '' ? $novo['observacao'] : null,
        ]);

        flash_set('success', 'Exercício cadastrado com sucesso!');
        header('Location: exercicios.php?treino=' . $treinoId);
        exit;
    }
}

// Lista os exercícios do treino
$stmt = $pdo->prepare('SELECT * FROM exercicios WHERE treino_id = ? ORDER BY id');
$stmt->execute([$treinoId]);
$exercicios = $stmt->fetchAll();

$nomeDia      = dias_semana()[(int) $treino['dia_semana']];
$tituloPagina = 'Exercícios';
$paginaAtiva  = 'treinos';
require_once __DIR__ . '/includes/header.php';
?>

<a href="treinos.php" class="btn btn-mt-suave btn-sm mb-3">
    <i class="bi bi-arrow-left"></i> Voltar para Meus Treinos
</a>

<div class="row g-4">
    <!-- Coluna: lista de exercícios -->
    <div class="col-12 col-lg-7">
        <div class="mt-card">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge-dia mb-2 d-inline-block"><?= e($nomeDia) ?></span>
                    <h1 class="h5 mb-1"><?= e($treino['nome_treino']) ?></h1>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-bullseye"></i> <?= e($treino['grupo_muscular']) ?>
                    </p>
                </div>

                <?php if ($exercicios): ?>
                    <?php foreach ($exercicios as $ex): ?>
                        <div class="exercicio-item">
                            <div>
                                <strong><?= e($ex['nome']) ?></strong>
                                <?php if ($ex['observacao']): ?>
                                    <div class="text-muted small"><?= e($ex['observacao']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="exercicio-series"><?= (int) $ex['series'] ?> × <?= e($ex['repeticoes']) ?></span>

                                <a href="exercicio_form.php?id=<?= (int) $ex['id'] ?>" class="btn btn-mt-suave btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="post" action="exercicio_excluir.php" data-confirm="Excluir o exercício '<?= e($ex['nome']) ?>'?">
                                    <input type="hidden" name="id" value="<?= (int) $ex['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-clipboard-plus" style="font-size: 2rem; color: var(--mt-primaria);"></i>
                        <p class="text-muted mt-2 mb-0">Nenhum exercício ainda. Adicione o primeiro ao lado!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Coluna: formulário de novo exercício -->
    <div class="col-12 col-lg-5">
        <div class="mt-card">
            <div class="card-body">
                <h2 class="h6 mb-3"><i class="bi bi-plus-circle me-1"></i> Adicionar exercício</h2>

                <?php if ($erros): ?>
                    <div class="alert alert-danger mt-alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= e($erros[0]) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="exercicios.php?treino=<?= $treinoId ?>" class="needs-validation" novalidate>
                    <input type="hidden" name="treino_id" value="<?= $treinoId ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do exercício</label>
                        <input type="text" class="form-control" id="nome" name="nome"
                               value="<?= e($novo['nome']) ?>" placeholder="Ex.: Supino reto"
                               minlength="2" maxlength="100" required>
                        <div class="invalid-feedback">Informe o nome do exercício.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="series" class="form-label">Séries</label>
                            <input type="number" class="form-control" id="series" name="series"
                                   value="<?= e($novo['series']) ?>" placeholder="3"
                                   min="1" max="20" required>
                            <div class="invalid-feedback">De 1 a 20.</div>
                        </div>
                        <div class="col-6">
                            <label for="repeticoes" class="form-label">Repetições</label>
                            <input type="text" class="form-control" id="repeticoes" name="repeticoes"
                                   value="<?= e($novo['repeticoes']) ?>" placeholder="12 ou 10-12"
                                   maxlength="20" required>
                            <div class="invalid-feedback">Ex.: 12 ou 10-12.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observacao" class="form-label">Observação <span class="text-muted fw-normal">(opcional)</span></label>
                        <input type="text" class="form-control" id="observacao" name="observacao"
                               value="<?= e($novo['observacao']) ?>" placeholder="Ex.: pegada fechada" maxlength="255">
                    </div>

                    <button type="submit" class="btn btn-mt-acento w-100">
                        <i class="bi bi-plus-lg"></i> Adicionar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
