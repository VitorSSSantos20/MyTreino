<?php
/**
 * Cadrasto de cada Treino
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$usuarioId = (int) $_SESSION['usuario_id'];
$hoje      = dia_hoje(); 

// Busca todos os treinos do usuário e organiza por dia da semana
$stmt = $pdo->prepare('SELECT * FROM treinos WHERE usuario_id = ?');
$stmt->execute([$usuarioId]);

$treinosPorDia = [];
foreach ($stmt->fetchAll() as $treino) {
    $treinosPorDia[(int) $treino['dia_semana']] = $treino;
}

// Conta os exercícios de cada treino (para mostrar no card)
$stmt = $pdo->prepare(
    'SELECT t.id, COUNT(e.id) AS total
       FROM treinos t
       LEFT JOIN exercicios e ON e.treino_id = t.id
      WHERE t.usuario_id = ?
      GROUP BY t.id'
);
$stmt->execute([$usuarioId]);

$totalExercicios = [];
foreach ($stmt->fetchAll() as $linha) {
    $totalExercicios[(int) $linha['id']] = (int) $linha['total'];
}

$tituloPagina = 'Meus Treinos';
$paginaAtiva  = 'treinos';
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1">Meus Treinos</h1>
        <p class="text-muted mb-0">Organize sua semana: cada dia pode ter um treino ou ser de descanso.</p>
    </div>
</div>

<div class="row g-3">
    <?php foreach (ordem_semana() as $dia): ?>
        <?php
            $treino  = $treinosPorDia[$dia] ?? null;
            $nomeDia = dias_semana()[$dia];
            $ehHoje  = ($dia === $hoje);
        ?>
        <div class="col-12 col-md-6 col-xl-4">
            <article class="mt-card h-100 <?= $ehHoje ? 'border-success' : '' ?>">
                <div class="card-body d-flex flex-column">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge-dia">
                            <?= e($nomeDia) ?>
                        </span>
                        <?php if ($ehHoje): ?>
                            <span class="badge text-bg-success rounded-pill">Hoje</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($treino && $treino['tipo_dia'] === 'descanso'): ?>
                        <!-- Dia de descanso -->
                        <h2 class="h6 mb-1"><i class="bi bi-moon-stars me-1"></i> Dia de descanso</h2>
                        <p class="text-muted small flex-grow-1">
                            <?= $treino['observacao'] ? e($treino['observacao']) : 'Recuperação faz parte do progresso.' ?>
                        </p>

                        <div class="d-flex gap-2">
                            <a href="treino_form.php?id=<?= (int) $treino['id'] ?>" class="btn btn-mt-suave btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form method="post" action="treino_excluir.php" data-confirm="Excluir a configuração de <?= e($nomeDia) ?>?">
                                <input type="hidden" name="id" value="<?= (int) $treino['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </form>
                        </div>

                    <?php elseif ($treino): ?>
                        <!-- Dia com treino -->
                        <h2 class="h6 mb-1"><i class="bi bi-fire me-1 text-success"></i> <?= e($treino['nome_treino']) ?></h2>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-bullseye"></i> <?= e($treino['grupo_muscular']) ?>
                        </p>
                        <p class="text-muted small flex-grow-1 mb-2">
                            <i class="bi bi-list-check"></i>
                            <?= $totalExercicios[(int) $treino['id']] ?? 0 ?> exercício(s)
                            <?php if ($treino['observacao']): ?>
                                <br><i class="bi bi-chat-left-text"></i> <?= e($treino['observacao']) ?>
                            <?php endif; ?>
                        </p>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="exercicios.php?treino=<?= (int) $treino['id'] ?>" class="btn btn-mt btn-sm">
                                <i class="bi bi-list-check"></i> Exercícios
                            </a>
                            <a href="treino_form.php?id=<?= (int) $treino['id'] ?>" class="btn btn-mt-suave btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form method="post" action="treino_excluir.php" data-confirm="Excluir o treino de <?= e($nomeDia) ?>? Os exercícios dele também serão excluídos.">
                                <input type="hidden" name="id" value="<?= (int) $treino['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </form>
                        </div>

                    <?php else: ?>
                        <!-- Dia sem nada cadastrado -->
                        <h2 class="h6 mb-1 text-muted"><i class="bi bi-dash-circle-dotted me-1"></i> Nada cadastrado</h2>
                        <p class="text-muted small flex-grow-1">Escolha se este dia será de treino ou de descanso.</p>

                        <a href="treino_form.php?dia=<?= $dia ?>" class="btn btn-mt-acento btn-sm align-self-start">
                            <i class="bi bi-plus-lg"></i> Cadastrar
                        </a>
                    <?php endif; ?>

                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
