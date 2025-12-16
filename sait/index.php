<?php

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
    '404' => [
        'title' => '404 — Сторінку не знайдено',
        'file'  => 'pages/404.php',
    ],
];

if (!array_key_exists($page, $pages)) {
    $page = '404';
}

$title = $pages[$page]['title'] . ' | ' . $siteTitle;
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="style.css">

    <?php if ($page === 'login'): ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/light.min.css">
    <?php endif; ?>
</head>
<body>

<nav>
    <?php foreach ($pages as $key => $p): ?>
        <?php if ($key === '404') continue; ?>
        <a href="/?page=<?= $key ?>"><?= $p['title'] ?></a>
    <?php endforeach; ?>
</nav>

<main>
    <?php require $pages[$page]['file']; ?>
</main>

<script src="script.js"></script>
</body>
</html>
