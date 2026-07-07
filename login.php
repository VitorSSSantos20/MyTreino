<?php
/**
 * Tela de login do MyTreino.
 */

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

// Quem já está logado vai direto para a Home
if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';
$emailDigitado = '';

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailDigitado = trim($_POST['email'] ?? '');
    $senhaDigitada = $_POST['senha'] ?? '';

    // Validação no PHP 
    if ($emailDigitado === '' || $senhaDigitada === '') {
        $erro = 'Preencha o e-mail e a senha.';
    } elseif (!filter_var($emailDigitado, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } else {
        // Busca o usuário pelo e-mail 
        $stmt = $pdo->prepare('SELECT id, nome, senha FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$emailDigitado]);
        $usuario = $stmt->fetch();

        // Confere a senha com password_verify
        if ($usuario && password_verify($senhaDigitada, $usuario['senha'])) {
            session_regenerate_id(true); // segurança: novo id de sessão
            $_SESSION['usuario_id']   = (int) $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            flash_set('success', 'Bem-vindo(a) de volta, ' . $usuario['nome'] . '!');
            header('Location: index.php');
            exit;
        }

        $erro = 'E-mail ou senha incorretos.';
    }
}

$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar · MyTreino</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-body">

    <section class="auth-card">
        <!-- Logo do app -->
        <div class="auth-logo">
            <span class="mt-brand-icon"><i class="bi bi-lightning-charge-fill"></i></span>
            <span class="auth-nome">My<span>Treino</span></span>
        </div>
        <p class="auth-frase">Seu treino da semana, sempre à mão.</p>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['tipo']) ?> mt-alert"><?= e($flash['mensagem']) ?></div>
        <?php endif; ?>

        <?php if ($erro): ?>
            <div class="alert alert-danger mt-alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= e($erro) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= e($emailDigitado) ?>" placeholder="voce@email.com" required>
                <div class="invalid-feedback">Informe um e-mail válido.</div>
            </div>

            <div class="mb-4">
                <label for="senha_login" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha_login" name="senha"
                       placeholder="Sua senha" required>
                <div class="invalid-feedback">Informe a sua senha.</div>
            </div>

            <button type="submit" class="btn btn-mt w-100 mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
            </button>
        </form>

        <p class="text-center mb-0">
            Ainda não tem conta?
            <a href="registro.php" class="auth-link">Criar conta grátis</a>
        </p>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
