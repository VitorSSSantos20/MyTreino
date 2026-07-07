<?php
/**
 * Cadastro de novo usuário.
 */

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/db.php';

if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erros = [];
$nome  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $senha     = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    // Validação no PHP
    if (mb_strlen($nome) < 2) {
        $erros[] = 'Informe o seu nome (mínimo de 2 letras).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um e-mail válido.';
    }
    if (mb_strlen($senha) < 6) {
        $erros[] = 'A senha precisa ter pelo menos 6 caracteres.';
    }
    if ($senha !== $confirmar) {
        $erros[] = 'As senhas não são iguais.';
    }

    // Verifica se o e-mail já está em uso
    if (!$erros) {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erros[] = 'Este e-mail já está cadastrado. Tente fazer login.';
        }
    }

    // Tudo certo: cria o usuário
    if (!$erros) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
        $stmt->execute([$nome, $email, $hash]);

        // Já entra logado
        session_regenerate_id(true);
        $_SESSION['usuario_id']   = (int) $pdo->lastInsertId();
        $_SESSION['usuario_nome'] = $nome;

        flash_set('success', 'Conta criada com sucesso! Bem-vindo(a) ao MyTreino.');
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar conta · MyTreino</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-body">

    <section class="auth-card">
        <div class="auth-logo">
            <span class="mt-brand-icon"><i class="bi bi-lightning-charge-fill"></i></span>
            <span class="auth-nome">My<span>Treino</span></span>
        </div>
        <p class="auth-frase">Crie sua conta e organize seus treinos.</p>

        <?php if ($erros): ?>
            <div class="alert alert-danger mt-alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?= e($erros[0]) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="registro.php" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome"
                       value="<?= e($nome) ?>" placeholder="Como podemos te chamar?"
                       minlength="2" maxlength="100" required>
                <div class="invalid-feedback">Informe o seu nome.</div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= e($email) ?>" placeholder="voce@email.com" required>
                <div class="invalid-feedback">Informe um e-mail válido.</div>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha"
                       placeholder="Mínimo de 6 caracteres" minlength="6" required>
                <div class="invalid-feedback">A senha precisa ter pelo menos 6 caracteres.</div>
            </div>

            <div class="mb-4">
                <label for="confirmar_senha" class="form-label">Confirmar senha</label>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha"
                       placeholder="Repita a senha" minlength="6" required>
                <div class="invalid-feedback">As senhas não são iguais.</div>
            </div>

            <button type="submit" class="btn btn-mt-acento w-100 mb-3">
                <i class="bi bi-lightning-charge-fill me-1"></i> Criar conta
            </button>
        </form>

        <p class="text-center mb-0">
            Já tem conta?
            <a href="login.php" class="auth-link">Fazer login</a>
        </p>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
