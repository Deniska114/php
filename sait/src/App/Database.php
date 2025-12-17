<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    public static function getConnection(string $type = 'sqlite', array $config = []): ?PDO
    {
        try {
            if ($type === 'sqlite') {
                $path = $config['path'] ?? __DIR__ . '/../../database.db';
                $dsn = "sqlite:" . $path;
                $pdo = new PDO($dsn);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                self::createUsersTable($pdo);
                
                return $pdo;
            } elseif ($type === 'mysql') {
                $host = $config['host'] ?? 'localhost';
                $dbname = $config['dbname'] ?? 'database';
                $username = $config['username'] ?? 'root';
                $password = $config['password'] ?? '';
                $charset = $config['charset'] ?? 'utf8mb4';
                
                $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
                $pdo = new PDO($dsn, $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                self::createUsersTable($pdo);
                
                return $pdo;
            } else {
                throw new \InvalidArgumentException("Непідтримуваний тип БД: {$type}. Використовуйте 'sqlite' або 'mysql'");
            }
        } catch (PDOException $e) {
            error_log("Помилка підключення до БД: " . $e->getMessage());
            return null;
        }
    }

    public static function selectUser(PDO $pdo, string $email, string $password)
    {
        try {
            $stmt = $pdo->prepare("SELECT id, name, email, password FROM Users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Помилка при перевірці користувача: " . $e->getMessage());
            return false;
        }
    }

    public static function insertUser(PDO $pdo, string $name, string $email, string $password): bool
    {
        try {
            $stmt = $pdo->prepare("SELECT id FROM Users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                return false;
            }
            
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $pdo->prepare("INSERT INTO Users (name, email, password) VALUES (:name, :email, :password)");
            $result = $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword
            ]);
            
            return $result;
        } catch (PDOException $e) {
            error_log("Помилка при створенні користувача: " . $e->getMessage());
            return false;
        }
    }

    private static function createUsersTable(PDO $pdo): void
    {
        try {
            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            if ($driver === 'sqlite') {
                $sql = "CREATE TABLE IF NOT EXISTS Users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )";
            } else {
                $sql = "CREATE TABLE IF NOT EXISTS Users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )";
            }
            
            $pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Помилка при створенні таблиці Users: " . $e->getMessage());
        }
    }
}

