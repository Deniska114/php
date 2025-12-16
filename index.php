<?php
// Базовий роутер для динамічного сайту

$page = $_GET['page'] ?? 'home';
$siteTitle = 'Coffee Lover';

$pages = [
    'home' => [
        'title' => 'Головна',
        'file'  => 'pages/home.php',
    ],
    'login' => [
        'title' => 'Логін / Реєстрація',
        'file'  => 'pages/login.php',
    ],
    // окрема конфігурація для 404 сторінки
    '404' => [
        'title' => '404 — Сторінку не знайдено',
        'file'  => 'pages/404.php',
    ],
];

// Якщо сторінка не існує в списку — показуємо 404
if (!array_key_exists($page, $pages)) {
    $page = '404';
}

// Динамічний заголовок <title> в залежності від сторінки
$title = $pages[$page]['title'] . ' | ' . $siteTitle;
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="style.css">

    <?php if ($page === 'login'): ?>
        <!-- Додаткові стилі лише для сторінки логіну -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/light.min.css">
    <?php endif; ?>
</head>
<body>

<nav>
    <?php foreach ($pages as $key => $p): ?>
        <?php if ($key === '404') continue; // не показуємо 404 в навігації ?>
        <a href="/?page=<?= $key ?>"><?= $p['title'] ?></a>
    <?php endforeach; ?>
</nav>

<main>
    <?php require $pages[$page]['file']; ?>
</main>

<script src="script.js"></script>
</body>
</html>
