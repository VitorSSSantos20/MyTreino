<?php
/**
 * Proteção das páginas internas.
 * está no topo de qualquer página que exija login.
 */

require_once __DIR__ . '/helpers.php';

if (empty($_SESSION['usuario_id'])) {
    flash_set('warning', 'Faça login para acessar o MyTreino.');
    header('Location: login.php');
    exit;
}
