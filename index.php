<?php
require_once __DIR__ . '/includes/auth.php';   // exige login (já inicia sessão e fuso)
require_once __DIR__ . '/config/db.php';
//Iniciação
$usuarioId = (int) $_SESSION['usuario_id'];

$hoje        = dia_hoje();                 
$nomeDiaHoje = dias_semana()[$hoje];       
$dataHoje    = date('d/m/Y');              

// Dados do usuário (peso, altura, idade)
$stmt = $pdo->prepare('SELECT nome, peso, altura, idade FROM usuarios WHERE id = ?');
$stmt->execute([$usuarioId]);
$usuario = $stmt->fetch();

// Treino de HOJE — usa a MESMA variável $hoje da iniciação
$stmt = $pdo->prepare('SELECT * FROM treinos WHERE usuario_id = ? AND dia_semana = ? LIMIT 1');
$stmt->execute([$usuarioId, $hoje]);
$treinoHoje = $stmt->fetch();

// Exercícios do treino de hoje (se existir e não for descanso)
$exerciciosHoje = [];
if ($treinoHoje && $treinoHoje['tipo_dia'] === 'treino') {
    $stmt = $pdo->prepare('SELECT * FROM exercicios WHERE treino_id = ? ORDER BY id');
    $stmt->execute([$treinoHoje['id']]);
    $exerciciosHoje = $stmt->fetchAll();
}

// Resumo da semana para a fita de dias (quais dias têm treino/descanso)
$stmt = $pdo->prepare('SELECT dia_semana, tipo_dia FROM treinos WHERE usuario_id = ?');
$stmt->execute([$usuarioId]);
$semana = [];
foreach ($stmt->fetchAll() as $linha) {
    $semana[(int) $linha['dia_semana']] = $linha['tipo_dia'];
}

$tituloPagina = 'Home';
$paginaAtiva  = 'home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Iniciação -->
<section class="mt-hero mb-4">
    <span class="hero-dia">
        <i class="bi bi-calendar-event"></i>
        <?= e($nomeDiaHoje) ?> · <?= e($dataHoje) ?>
    </span>
    <h1>Olá, <?= e($usuario['nome']) ?>! 👋</h1>
    <p>
        <?php if ($treinoHoje && $treinoHoje['tipo_dia'] === 'descanso'): ?>
            Hoje o corpo agradece: dia de recuperar as energias.
        <?php elseif ($treinoHoje): ?>
            Seu treino de hoje está pronto. Bora treinar!
        <?php else: ?>
            Que tal planejar o treino de hoje?
        <?php endif; ?>
    </p>
</section>

<!-- Fita da semana -->
<section class="mb-4" aria-label="Resumo da semana">
    <div class="mt-semana">
        <?php foreach (ordem_semana() as $dia): ?>
            <?php
                // Abreviação do dia 
                $abrev = mb_substr(dias_semana()[$dia], 0, 3);
                $classes = ['dia-chip'];
                if ($dia === $hoje) { $classes[] = 'hoje'; }

                if (isset($semana[$dia])) {
                    $icone = $semana[$dia] === 'descanso' ? 'bi-moon-stars' : 'bi-fire';
                    if ($semana[$dia] === 'treino') { $classes[] = 'treino'; }
                } else {
                    $icone = 'bi-dash-circle-dotted';
                }
            ?>
            <a href="treinos.php" class="<?= implode(' ', $classes) ?>" title="<?= e(dias_semana()[$dia]) ?>">
                <i class="bi <?= $icone ?> chip-icone"></i>
                <?= e($abrev) ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<div class="row g-4">
    <!-- Coluna: dados do perfil -->
    <div class="col-12 col-lg-4">
        <div class="mt-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 mb-0">Seu perfil</h2>
                    <a href="perfil.php" class="btn btn-mt-suave btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="mt-stat">
                        <span class="stat-icone"><i class="bi bi-speedometer2"></i></span>
                        <div>
                            <div class="stat-valor"><?= $usuario['peso'] !== null ? e(rtrim(rtrim(number_format((float)$usuario['peso'], 2, ',', ''), '0'), ',')) . ' kg' : '—' ?></div>
                            <div class="stat-rotulo">Peso</div>
                        </div>
                    </div>
                    <div class="mt-stat">
                        <span class="stat-icone"><i class="bi bi-rulers"></i></span>
                        <div>
                            <div class="stat-valor"><?= $usuario['altura'] !== null ? e($usuario['altura']) . ' cm' : '—' ?></div>
                            <div class="stat-rotulo">Altura</div>
                        </div>
                    </div>
                    <div class="mt-stat">
                        <span class="stat-icone"><i class="bi bi-cake2"></i></span>
                        <div>
                            <div class="stat-valor"><?= $usuario['idade'] !== null ? e($usuario['idade']) . ' anos' : '—' ?></div>
                            <div class="stat-rotulo">Idade</div>
                        </div>
                    </div>
                </div>

                <?php if ($usuario['peso'] === null || $usuario['altura'] === null || $usuario['idade'] === null): ?>
                    <p class="text-muted small mt-3 mb-0">
                        <i class="bi bi-info-circle"></i>
                        Complete seu perfil para acompanhar seus dados por aqui.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Coluna: treino de hoje -->
    <div class="col-12 col-lg-8">
        <div class="mt-card h-100">
            <div class="card-body">
                <h2 class="h6 mb-3">
                    <i class="bi bi-fire text-success"></i>
                    Treino de hoje — <?= e($nomeDiaHoje) ?>
                </h2>

                <?php if ($treinoHoje && $treinoHoje['tipo_dia'] === 'descanso'): ?>
                    <!-- Dia de descanso -->
                    <div class="text-center py-4">
                        <i class="bi bi-moon-stars" style="font-size: 2.4rem; color: var(--mt-primaria);"></i>
                        <h3 class="h5 mt-3">Hoje é dia de descanso</h3>
                        <p class="text-muted mb-0">Descansar também faz parte do treino. Aproveite!</p>
                    </div>

                <?php elseif ($treinoHoje): ?>
                    <!-- Treino cadastrado para hoje -->
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <h3 class="h5 mb-1"><?= e($treinoHoje['nome_treino']) ?></h3>
                            <span class="badge-dia">
                                <i class="bi bi-bullseye"></i> <?= e($treinoHoje['grupo_muscular']) ?>
                            </span>
                        </div>
                        <a href="exercicios.php?treino=<?= (int) $treinoHoje['id'] ?>" class="btn btn-mt btn-sm">
                            <i class="bi bi-list-check"></i> Gerenciar exercícios
                        </a>
                    </div>

                    <?php if ($treinoHoje['observacao']): ?>
                        <p class="text-muted small"><i class="bi bi-chat-left-text"></i> <?= e($treinoHoje['observacao']) ?></p>
                    <?php endif; ?>

                    <?php if ($exerciciosHoje): ?>
                        <div class="mt-2">
                            <?php foreach ($exerciciosHoje as $ex): ?>
                                <div class="exercicio-item">
                                    <div>
                                        <strong><?= e($ex['nome']) ?></strong>
                                        <?php if ($ex['observacao']): ?>
                                            <div class="text-muted small"><?= e($ex['observacao']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="exercicio-series">
                                        <?= (int) $ex['series'] ?> × <?= e($ex['repeticoes']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0 mt-2">
                            Este treino ainda não tem exercícios.
                            <a href="exercicios.php?treino=<?= (int) $treinoHoje['id'] ?>" class="auth-link">Adicionar agora</a>.
                        </p>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Nenhum treino cadastrado para hoje -->
                    <div class="text-center py-4">
                        <i class="bi bi-clipboard-plus" style="font-size: 2.4rem; color: var(--mt-primaria);"></i>
                        <h3 class="h5 mt-3">Nenhum treino para hoje</h3>
                        <p class="text-muted">Cadastre um treino (ou marque como descanso) para <?= e($nomeDiaHoje) ?>.</p>
                        <a href="treino_form.php?dia=<?= $hoje ?>" class="btn btn-mt-acento">
                            <i class="bi bi-plus-lg"></i> Cadastrar treino de hoje
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
