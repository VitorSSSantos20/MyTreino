<?php
/**
 * Encerra a sessão do usuário e volta para o login.
 */

require_once __DIR__ . '/includes/helpers.php';

// Limpa todos os dados da sessão
$_SESSION = [];
session_destroy();

// Inicia uma nova sessão apenas para mostrar a mensagem de despedida
session_start();
flash_set('info', 'Você saiu do MyTreino. Até o próximo treino!');

header('Location: login.php');
exit;
