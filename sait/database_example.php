<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Database;

echo "<h2>Приклад 1: Підключення до SQLite</h2>";
$pdoSqlite = Database::getConnection('sqlite', [
    'path' => __DIR__ . '/database.db'
]);

if ($pdoSqlite) {
    echo "<p style='color: green;'>✓ Підключення до SQLite успішне!</p>";
} else {
    echo "<p style='color: red;'>✗ Помилка підключення до SQLite</p>";
}

echo "<h2>Приклад 3: Реєстрація користувача (INSERT)</h2>";
if ($pdoSqlite) {
    $result = Database::insertUser(
        $pdoSqlite,
        'Іван Петренко',
        'ivan@example.com',
        'password123'
    );
    
    if ($result) {
        echo "<p style='color: green;'>✓ Користувач успішно зареєстрований!</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Користувач вже існує або сталася помилка</p>";
    }
}

echo "<h2>Приклад 4: Авторизація користувача (SELECT)</h2>";
if ($pdoSqlite) {
    $user = Database::selectUser(
        $pdoSqlite,
        'ivan@example.com',
        'password123'
    );
    
    if ($user) {
        echo "<p style='color: green;'>✓ Авторизація успішна!</p>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>✗ Невірний email або пароль</p>";
    }
}

echo "<h2>Приклад 5: Спроба авторизації з невірним паролем</h2>";
if ($pdoSqlite) {
    $user = Database::selectUser(
        $pdoSqlite,
        'ivan@example.com',
        'wrong_password'
    );
    
    if ($user) {
        echo "<p style='color: green;'>✓ Авторизація успішна!</p>";
    } else {
        echo "<p style='color: red;'>✗ Невірний пароль (очікувано)</p>";
    }
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
    }
    h2 {
        color: #333;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 10px;
    }
    pre {
        background: #f4f4f4;
        padding: 10px;
        border-radius: 5px;
    }
</style>
