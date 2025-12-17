<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use App\Models\Product;

echo "<h1>Приклади використання моделей</h1>";

echo "<h2>Модель User</h2>";

$userModel = new User();

echo "<h3>1. Створення користувача</h3>";
$result = $userModel->create('Тестовий Користувач', 'test@example.com', 'password123');
if ($result) {
    echo "<p style='color: green;'>✓ Користувач успішно створений</p>";
} else {
    echo "<p style='color: orange;'>⚠ Користувач вже існує або помилка</p>";
}

echo "<h3>2. Авторизація користувача</h3>";
$user = $userModel->authenticate('test@example.com', 'password123');
if ($user) {
    echo "<p style='color: green;'>✓ Авторизація успішна</p>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ Помилка авторизації</p>";
}

echo "<h3>3. Пошук користувача за ID</h3>";
if ($user) {
    $foundUser = $userModel->findById($user['id']);
    if ($foundUser) {
        echo "<pre>";
        print_r($foundUser);
        echo "</pre>";
    }
}

echo "<h3>4. Отримання всіх користувачів</h3>";
$allUsers = $userModel->getAll();
echo "<p>Знайдено користувачів: " . count($allUsers) . "</p>";

echo "<h2>Модель Product (MyModel)</h2>";

$productModel = new Product();

echo "<h3>1. Створення продуктів</h3>";
$products = [
    [
        'name' => 'Blooming Spring',
        'description' => 'Ароматна кава з нотками ягід',
        'composition' => '100% арабіка Colombia El Llano',
        'aroma' => 'ігристе, полуниця, червона смородина',
        'price' => 379.00,
        'image_url' => 'https://e-c.storage.googleapis.com/res/2efadcdf-7c20-4b44-8997-39778e8a52f6/480'
    ],
    [
        'name' => 'Yellow Honey',
        'description' => 'Солодка кава з медовими нотами',
        'composition' => '100% арабіка Honduras',
        'aroma' => 'мармелад, мед, шоколад, мигдаль, тростинний цукор',
        'price' => 345.00,
        'image_url' => 'https://e-c.storage.googleapis.com/res/2fc67cd4-dc78-491c-8252-3b91bf07134d/480'
    ],
    [
        'name' => 'Creamy Nugat',
        'description' => 'Ніжна кава з нотками нуги',
        'composition' => '100% арабіка Ethiopia Shonora',
        'aroma' => 'сушені фрукти, нуга, зелене яблуко, фундук',
        'price' => 325.00,
        'image_url' => 'https://e-c.storage.googleapis.com/res/16e32b75-d857-46e8-a2f8-9a0f3bc164bb/480'
    ]
];

foreach ($products as $productData) {
    $result = $productModel->create(
        $productData['name'],
        $productData['description'],
        $productData['composition'],
        $productData['aroma'],
        $productData['price'],
        $productData['image_url']
    );
    if ($result) {
        echo "<p style='color: green;'>✓ Продукт '{$productData['name']}' створено</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Продукт '{$productData['name']}' вже існує або помилка</p>";
    }
}

echo "<h3>2. Отримання всіх продуктів</h3>";
$allProducts = $productModel->getAll();
echo "<p>Знайдено продуктів: " . count($allProducts) . "</p>";
if (!empty($allProducts)) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Назва</th><th>Ціна</th><th>Склад</th></tr>";
    foreach ($allProducts as $product) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($product['id']) . "</td>";
        echo "<td>" . htmlspecialchars($product['name']) . "</td>";
        echo "<td>₴" . number_format($product['price'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($product['composition'] ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>3. Пошук продукту за ID</h3>";
if (!empty($allProducts)) {
    $firstProduct = $productModel->findById($allProducts[0]['id']);
    if ($firstProduct) {
        echo "<pre>";
        print_r($firstProduct);
        echo "</pre>";
    }
}

echo "<h3>4. Пошук продуктів за ціною (300-350 грн)</h3>";
$productsByPrice = $productModel->getByPriceRange(300, 350);
echo "<p>Знайдено продуктів: " . count($productsByPrice) . "</p>";
if (!empty($productsByPrice)) {
    foreach ($productsByPrice as $product) {
        echo "<p><strong>" . htmlspecialchars($product['name']) . "</strong> - ₴" . number_format($product['price'], 2) . "</p>";
    }
}

echo "<h3>5. Пошук продуктів за текстом</h3>";
$searchResults = $productModel->search('мед');
echo "<p>Знайдено продуктів з 'мед': " . count($searchResults) . "</p>";
if (!empty($searchResults)) {
    foreach ($searchResults as $product) {
        echo "<p><strong>" . htmlspecialchars($product['name']) . "</strong></p>";
    }
}

echo "<h3>6. Оновлення продукту</h3>";
if (!empty($allProducts)) {
    $productId = $allProducts[0]['id'];
    $updated = $productModel->update($productId, null, 'Оновлений опис продукту');
    if ($updated) {
        echo "<p style='color: green;'>✓ Продукт оновлено</p>";
        $updatedProduct = $productModel->findById($productId);
        echo "<pre>";
        print_r($updatedProduct);
        echo "</pre>";
    }
}

echo "<h3>7. Підрахунок продуктів</h3>";
$productCount = $productModel->count();
echo "<p>Загальна кількість продуктів: <strong>$productCount</strong></p>";

?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
    }
    h1 {
        color: #333;
        border-bottom: 3px solid #4CAF50;
        padding-bottom: 10px;
    }
    h2 {
        color: #555;
        margin-top: 30px;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 5px;
    }
    h3 {
        color: #777;
        margin-top: 20px;
    }
    pre {
        background: #f4f4f4;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }
    table {
        margin: 20px 0;
        width: 100%;
    }
    th {
        background-color: #4CAF50;
        color: white;
        padding: 10px;
    }
    td {
        padding: 8px;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>
