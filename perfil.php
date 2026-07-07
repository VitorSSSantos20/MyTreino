<?php
/**
 * Perfil do usuário — nome, peso, altura e idade.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$usuarioId = (int) $_SESSION['usuario_id'];
$erros     = [];

// Carrega os dados atuais
$stmt = $pdo->prepare('SELECT nome, email, peso, altura, idade, created_at FROM usuarios WHERE id = ?');
$stmt->execute([$usuarioId]);
$usuario = $stmt->fetch();

// Processa a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario['nome']   = trim($_POST['nome'] ?? '');
    $usuario['peso']   = trim($_POST['peso'] ?? '');
    $usuario['altura'] = trim($_POST['altura'] ?? '');
    $usuario['idade']  = trim($_POST['idade'] ?? '');

    // ----- Validação no PHP -----
    if (mb_strlen($usuario['nome']) < 2) {
        $erros[] = 'Informe o seu nome.';
    }

    // Os campos opcionais, mas se preenchidos precisam ser válidos.
    $peso = $usuario['peso'] !== '' ? (float) str_replace(',', '.', $usuario['peso']) : null;
    if ($peso !== null && ($peso < 20 || $peso > 400)) {
        $erros[] = 'Informe um peso entre 20 e 400 kg.';
    }

    $altura = $usuario['altura'] !== '' ? (int) $usuario['altura'] : null;
    if ($altura !== null && ($altura < 100 || $altura > 250)) {
        $erros[] = 'Informe a altura em centímetros (entre 100 e 250).';
    }

    $idade = $usuario['idade'] !== '' ? (int) $usuario['idade'] : null;
    if ($idade !== null && ($idade < 10 || $idade > 120)) {
        $erros[] = 'Informe uma idade entre 10 e 120 anos.';
    }

    if (!$erros) {
        $stmt = $pdo->prepare('UPDATE usuarios SET nome = ?, peso = ?, altura = ?, idade = ? WHERE id = ?');
        $stmt->execute([$usuario['nome'], $peso, $altura, $idade, $usuarioId]);

        // Atualiza o nome guardado na sessão 
        $_SESSION['usuario_nome'] = $usuario['nome'];

        flash_set('success', 'Perfil editado com sucesso!');
        header('Location: perfil.php');
        exit;
    }
}

$tituloPagina = 'Perfil';
$paginaAtiva  = 'perfil';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-6">

        <div class="mt-card">
            <div class="card-body">
                <!-- Cabeçalho do perfil -->
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="stat-icone" style="width: 56px; height: 56px; font-size: 1.5rem;">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <div>
                        <h1 class="h5 mb-0"><?= e($usuario['nome']) ?></h1>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-envelope"></i> <?= e($usuario['email']) ?>
                        </p>
                    </div>
                </div>

                <?php if ($erros): ?>
                    <div class="alert alert-danger mt-alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= e($erros[0]) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="perfil.php" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome"
                               value="<?= e($usuario['nome']) ?>" minlength="2" maxlength="100" required>
                        <div class="invalid-feedback">Informe o seu nome.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-4">
                            <label for="peso" class="form-label">Peso (kg)</label>
                            <input type="number" step="0.1" min="20" max="400" class="form-control"
                                   id="peso" name="peso" placeholder="72.5"
                                   value="<?= e($usuario['peso']) ?>">
                            <div class="invalid-feedback">Entre 20 e 400 kg.</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label for="altura" class="form-label">Altura (cm)</label>
                            <input type="number" min="100" max="250" class="form-control"
                                   id="altura" name="altura" placeholder="175"
                                   value="<?= e($usuario['altura']) ?>">
                            <div class="invalid-feedback">Entre 100 e 250 cm.</div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label for="idade" class="form-label">Idade</label>
                            <input type="number" min="10" max="120" class="form-control"
                                   id="idade" name="idade" placeholder="25"
                                   value="<?= e($usuario['idade']) ?>">
                            <div class="invalid-feedback">Entre 10 e 120 anos.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-mt w-100">
                        <i class="bi bi-check-lg"></i> Salvar perfil
                    </button>
                </form>

                <p class="text-muted small text-center mt-3 mb-0">
                    <i class="bi bi-calendar-check"></i>
                    No MyTreino desde <?= e(date('d/m/Y', strtotime($usuario['created_at']))) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
