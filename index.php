<?php

    // Файл в якому створюємо таблицю User
    require_once 'dbCreateTable.php';


    // ------------------------------------------------------------
    // index.php — простий роутер з авторизацією та редиректами
    // ------------------------------------------------------------

    session_start(); // запуск сесії

    // ------------------------------------------------------------
    // Обробка виходу з облікового запису (?logout=1)
    // ------------------------------------------------------------
    if (isset($_GET['logout'])) {
        // Видаляємо дані з сесії
        session_unset();
        session_destroy();

        // Скидаємо cookie
        setcookie('login', '', time() - 3600, '/');

        // Після виходу — редирект на /login
        header("Location: /login");
        exit;
    }

    // ------------------------------------------------------------
    // 2) Перевірка: чи користувач уже авторизований
    //    Якщо в сесії або cookie є 'login' — вважаємо його авторизованим
    // ------------------------------------------------------------
    $isAuthenticated = false;
    if (isset($_SESSION['login']) || isset($_COOKIE['login'])) {
        $isAuthenticated = true;
    }


    // ------------------------------------------------------------
    // 3) Обробка POST-запиту — спроба авторизації
    // ------------------------------------------------------------
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (isset($_POST["login"], $_POST["password"])) {
            $login = trim($_POST["login"]);
            $password = trim($_POST["password"]);

            try {
                // Підключення до бази даних SQLite
                $myPDO = new PDO('sqlite:mydatabase.db');
                $myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
                // Підготовлений запит
                $sql = "SELECT id FROM User WHERE login = :login AND password = :password";
                $stmt = $myPDO->prepare($sql);

                // Виконання з параметрами
                $stmt->execute([
                    ':login' => $login,
                    ':password' => $password
                ]);

                // Отримання результату
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if($user){
                    // Записуємо логін у сесію
                    $_SESSION['login'] = $login;

                    // Додаємо cookie на 10000 секунд
                    setcookie('login', $login, time() + 10000, '/');

                    // Після успішного логіну — редирект на головну
                    header("Location: /");
                    exit;
                }
                else
                    echo "користувач не існує";
       
            } catch (PDOException $e) {
                echo "Помилка: " . $e->getMessage();
            }
        }
    }

    // ------------------------------------------------------------
    // 4) Таблиця маршрутів
    // ------------------------------------------------------------
    $routes = [
        "/"          => ["title" => "ГОЛОВНА",     "file" => "home.php"],
        "/login"     => ["title" => "АВТОРИЗАЦІЯ", "file" => "login.php"],
        "/game"     => ["title" => "GAME", "file" => "game.php"],
        "/fabric"     => ["title" => "GAME", "file" => "fabric.php"],
    ];

    // ------------------------------------------------------------
    // 5) Отримуємо шлях (URI), який користувач ввів у браузері
    // ------------------------------------------------------------
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if ($path === null || $path === false) {
        $path = '/';
    }

    // Нормалізація (забираємо кінцевий "/")
    if ($path !== '/') {
        $path = rtrim($path, '/');
    }
    if ($path === '') {
        $path = '/';
    }


    // ------------------------------------------------------------
    // 6) Якщо користувач уже авторизований — не пускаємо на /login та /register
    // ------------------------------------------------------------
    if ($isAuthenticated && in_array($path, ['/login', '/register'])) {
        // header() — HTTP-редирект
        header("Location: /");
        exit;
    }


    // ------------------------------------------------------------
    // 7) Пошук маршруту і підключення відповідного файлу
    // ------------------------------------------------------------
    if (array_key_exists($path, $routes)) {

        $title = $routes[$path]['title'] ?? 'Сторінка';
        $file  = __DIR__ . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . $routes[$path]['file'];

        if (is_file($file)) {
            $currentPath = $path;     // поточний шлях
            $GLOBALS['currentPath'] = $path; // робимо доступним глобально
            include $file;
            exit;
        } else {
            // Якщо файл не знайдено — помилка 500
            http_response_code(500);
            $file500 = __DIR__ . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . '500.php';
            include $file500;
            exit;
        }
    }

    // ------------------------------------------------------------
    // 8) Якщо маршруту не існує — показуємо сторінку 404
    // ------------------------------------------------------------
    http_response_code(404);
    $title = "Сторінка не знайдена";

    $file404 = __DIR__ . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . '404.php';

    if (is_file($file404)) {
        include $file404;
        exit;
    } else {
        echo "<h1>404</h1><p>Сторінку не знайдено.</p>";
        exit;
    }