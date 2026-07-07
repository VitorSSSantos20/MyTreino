<?php
/**
 * Formulário de treino — serve para CADASTRAR e para EDITAR.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$usuarioId = (int) $_SESSION['usuario_id'];
$erros     = [];

// Valores padrão do formulário
$treino = [
    'id'             => 0,
    'dia_semana'     => isset($_GET['dia']) ? (int) $_GET['dia'] : dia_hoje(),
    'tipo_dia'       => 'treino',
    'nome_treino'    => '',
    'grupo_muscular' => '',
    'observacao'     => '',
];

// Modo EDIÇÃO: carrega o treino
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM treinos WHERE id = ? AND usuario_id = ? LIMIT 1');
    $stmt->execute([(int) $_GET['id'], $usuarioId]);
    $registro = $stmt->fetch();

    if (!$registro) {
        flash_set('danger', 'Treino não encontrado.');
        header('Location: treinos.php');
        exit;
    }
    $treino = $registro;
}

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $treino['id']             = (int) ($_POST['id'] ?? 0);
    $treino['dia_semana']     = (int) ($_POST['dia_semana'] ?? -1);
    $treino['tipo_dia']       = $_POST['tipo_dia'] ?? 'treino';
    $treino['nome_treino']    = trim($_POST['nome_treino'] ?? '');
    $treino['grupo_muscular'] = trim($_POST['grupo_muscular'] ?? '');
    $treino['observacao']     = trim($_POST['observacao'] ?? '');

    // ----- Validação no PHP -----
    if (!array_key_exists($treino['dia_semana'], dias_semana())) {
        $erros[] = 'Escolha um dia da semana válido.';
    }
    if (!in_array($treino['tipo_dia'], ['treino', 'descanso'], true)) {
        $erros[] = 'Escolha se o dia é de treino ou de descanso.';
    }
    if ($treino['tipo_dia'] === 'treino') {
        if (mb_strlen($treino['nome_treino']) < 2) {
            $erros[] = 'Informe o nome do treino (ex.: Treino A).';
        }
        if (mb_strlen($treino['grupo_muscular']) < 2) {
            $erros[] = 'Informe o grupo muscular (ex.: Peito e tríceps).';
        }
    } else {
        // Dia de descanso não guarda nome nem grupo muscular
        $treino['nome_treino']    = null;
        $treino['grupo_muscular'] = null;
    }

    // O dia da semana não pode estar ocupado por OUTRO registro
    if (!$erros) {
        $stmt = $pdo->prepare('SELECT id FROM treinos WHERE usuario_id = ? AND dia_semana = ? AND id <> ? LIMIT 1');
        $stmt->execute([$usuarioId, $treino['dia_semana'], $treino['id']]);
        if ($stmt->fetch()) {
            $erros[] = 'Esse dia já tem um treino/descanso cadastrado. Edite ou exclua o registro existente.';
        }
    }

    // ----- Salva no banco -----
    if (!$erros) {
        $observacao = $treino['observacao'] !== '' ? $treino['observacao'] : null;

        if ($treino['id'] > 0) {
            // Atualiza 
            $stmt = $pdo->prepare(
                'UPDATE treinos
                    SET dia_semana = ?, tipo_dia = ?, nome_treino = ?, grupo_muscular = ?, observacao = ?
                  WHERE id = ? AND usuario_id = ?'
            );
            $stmt->execute([
                $treino['dia_semana'], $treino['tipo_dia'], $treino['nome_treino'],
                $treino['grupo_muscular'], $observacao, $treino['id'], $usuarioId,
            ]);
            flash_set('success', 'Treino editado com sucesso!');
        } else {
            // Insere novo
            $stmt = $pdo->prepare(
                'INSERT INTO treinos (usuario_id, dia_semana, tipo_dia, nome_treino, grupo_muscular, observacao)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $usuarioId, $treino['dia_semana'], $treino['tipo_dia'],
                $treino['nome_treino'], $treino['grupo_muscular'], $observacao,
            ]);
            flash_set('success', 'Treino cadastrado com sucesso!');
        }

        header('Location: treinos.php');
        exit;
    }
}

$editando     = $treino['id'] > 0;
$tituloPagina = $editando ? 'Editar treino' : 'Novo treino';
$paginaAtiva  = 'treinos';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        <a href="treinos.php" class="btn btn-mt-suave btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Voltar para Meus Treinos
        </a>

        <div class="mt-card">
            <div class="card-body">
                <h1 class="h5 mb-3">
                    <i class="bi <?= $editando ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-1"></i>
                    <?= $editando ? 'Editar treino' : 'Cadastrar treino' ?>
                </h1>

                <?php if ($erros): ?>
                    <div class="alert alert-danger mt-alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= e($erros[0]) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="treino_form.php" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?= (int) $treino['id'] ?>">

                    <!-- Dia da semana -->
                    <div class="mb-3">
                        <label for="dia_semana" class="form-label">Dia da semana</label>
                        <select class="form-select" id="dia_semana" name="dia_semana" required>
                            <?php foreach (ordem_semana() as $dia): ?>
                                <option value="<?= $dia ?>" <?= (int) $treino['dia_semana'] === $dia ? 'selected' : '' ?>>
                                    <?= e(dias_semana()[$dia]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Escolha o dia da semana.</div>
                    </div>

                    <!-- Tipo do dia: treino ou descanso -->
                    <div class="mb-3">
                        <span class="form-label d-block">Este dia será de...</span>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo_dia" id="tipo_treino"
                                       value="treino" <?= $treino['tipo_dia'] === 'treino' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tipo_treino">
                                    <i class="bi bi-fire text-success"></i> Treino
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo_dia" id="tipo_descanso"
                                       value="descanso" <?= $treino['tipo_dia'] === 'descanso' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="tipo_descanso">
                                    <i class="bi bi-moon-stars"></i> Descanso
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Campos exibidos apenas quando o dia é de treino -->
                    <div id="campos-treino">
                        <div class="mb-3">
                            <label for="nome_treino" class="form-label">Nome do treino</label>
                            <input type="text" class="form-control" id="nome_treino" name="nome_treino"
                                   value="<?= e($treino['nome_treino']) ?>"
                                   placeholder="Ex.: Treino A" maxlength="100" minlength="2" data-obrigatorio>
                            <div class="invalid-feedback">Informe o nome do treino.</div>
                        </div>

                        <div class="mb-3">
                            <label for="grupo_muscular" class="form-label">Grupo muscular</label>
                            <input type="text" class="form-control" id="grupo_muscular" name="grupo_muscular"
                                   value="<?= e($treino['grupo_muscular']) ?>"
                                   placeholder="Ex.: Peito e tríceps" maxlength="100" minlength="2" data-obrigatorio>
                            <div class="invalid-feedback">Informe o grupo muscular.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observacao" class="form-label">Observação <span class="text-muted fw-normal">(opcional)</span></label>
                        <textarea class="form-control" id="observacao" name="observacao" rows="2"
                                  maxlength="255" placeholder="Ex.: aumentar carga aos poucos"><?= e($treino['observacao']) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-mt">
                            <i class="bi bi-check-lg"></i> <?= $editando ? 'Salvar alterações' : 'Cadastrar' ?>
                        </button>
                        <a href="treinos.php" class="btn btn-mt-suave">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
