<?php

/**
 * Returns the id of the user with this username.
 * Creates the user if it does not exist yet.
 */
function getOrCreateUser(PDO $pdo, string $username): int
{
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        return (int) $user['id'];
    }

    $stmt = $pdo->prepare('INSERT INTO users (username) VALUES (:username)');
    $stmt->execute([':username' => $username]);

    return (int) $pdo->lastInsertId();
}