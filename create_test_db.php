<?php
try {
    $pdo = new PDO("pgsql:host=127.0.0.1;port=5432", "postgres", "");
    $pdo->exec("CREATE DATABASE testing;");
    echo "Database created successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
