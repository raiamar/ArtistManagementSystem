<?php

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrf(string $token) : bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currenctUser(): ?array{
    if(!isLoggedIn())
        return null;

    return[
        'id'=>$_SESSION['user_id'],
        'name'=>$_SESSION['user_name'],
        'email'=>$_SESSION['user_email'],
        'role'=>$_SESSION['user_role'],
    ];
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}