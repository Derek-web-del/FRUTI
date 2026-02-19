<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const FRUTI_USERS_FILE = __DIR__ . '/users.json';

/**
 * @return array<int, array<string, mixed>>
 */
function fruti_load_users(): array
{
    if (!file_exists(FRUTI_USERS_FILE)) {
        return [];
    }

    $json = file_get_contents(FRUTI_USERS_FILE);
    if ($json === false || $json === '') {
        return [];
    }

    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

/**
 * @param array<int, array<string, mixed>> $users
 */
function fruti_save_users(array $users): void
{
    file_put_contents(
        FRUTI_USERS_FILE,
        json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function fruti_find_user_by_email(string $email): ?array
{
    $email = strtolower(trim($email));
    foreach (fruti_load_users() as $user) {
        if (isset($user['email']) && strtolower((string) $user['email']) === $email) {
            return $user;
        }
    }

    return null;
}

function fruti_cart_count(): int
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }

    $total = 0;
    foreach ($_SESSION['cart'] as $qty) {
        $total += (int) $qty;
    }

    return $total;
}


