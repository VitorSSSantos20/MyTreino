<?php
/**
 * Funções auxiliares usadas em todo o app.
 */
// Fuso horário correto do Brasil
date_default_timezone_set('America/Sao_Paulo');
// Garante que a sessão está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * Escapa texto para exibição segura no HTML .
 */
function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/**
 *  dias da semana em português.
 */
function dias_semana(): array
{
    return [
        0 => 'Domingo',
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sábado',
    ];
}

/**
 * Ordem de exibição da semana nas listagens (Segunda -> Domingo).
 */
function ordem_semana(): array
{
    return [1, 2, 3, 4, 5, 6, 0];
}
function dia_hoje(): int
{
    return (int) date('w');
}

/**
 * Guarda uma mensagem de feedback para a próxima página.
 */
function flash_set(string $tipo, string $mensagem): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

/**
 * Lê (e apaga) a mensagem de feedback, se existir.
 */
function flash_get(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
