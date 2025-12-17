<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\Security;

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не дозволено']);
    exit;
}

$action = $_POST['action'] ?? '';

$pdo = Database::getConnection('sqlite', [
    'path' => __DIR__ . '/database.db'
]);

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Помилка підключення до БД']);
    exit;
}

if ($action === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $email = Security::filterEmail($email);
    $password = Security::filterString($password);
    
    if (!Security::validateEmail($email)) {
        echo json_encode(['success' => false, 'message' => 'Невірний формат email']);
        exit;
    }
    
    if (!Security::validatePassword($password)) {
        echo json_encode(['success' => false, 'message' => 'Пароль повинен містити мінімум 6 символів']);
        exit;
    }
    
    $user = Database::selectUser($pdo, $email, $password);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        echo json_encode([
            'success' => true,
            'message' => 'Авторизація успішна',
            'user' => [
                'id' => $user['id'],
                'name' => Security::escapeOutput($user['name']),
                'email' => Security::escapeOutput($user['email'])
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Невірний email або пароль']);
    }
    
} elseif ($action === 'register') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $name = Security::filterString($name);
    $email = Security::filterEmail($email);
    $password = Security::filterString($password);
    
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Ім\'я обов\'язкове']);
        exit;
    }
    
    if (!Security::validateEmail($email)) {
        echo json_encode(['success' => false, 'message' => 'Невірний формат email']);
        exit;
    }
    
    if (!Security::validatePassword($password)) {
        echo json_encode(['success' => false, 'message' => 'Пароль повинен містити мінімум 6 символів']);
        exit;
    }
    
    $result = Database::insertUser($pdo, $name, $email, $password);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Реєстрація успішна']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Користувач з таким email вже існує']);
    }
    
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Невідома дія']);
}

