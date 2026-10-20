<?php

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3307;dbname=cpoe_simrs;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    echo '<h1>DATABASE CONNECTED ✅</h1>';

    $version = $pdo->query('SELECT VERSION()')->fetchColumn();

    echo '<p>MariaDB version: ' . htmlspecialchars($version) . '</p>';
} catch (PDOException $e) {
    echo '<h1>DATABASE ERROR ❌</h1>';
    echo '<pre>';
    echo htmlspecialchars($e->getMessage());
    echo '</pre>';
}