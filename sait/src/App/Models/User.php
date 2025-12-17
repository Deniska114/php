<?php

namespace App\Models;

use App\Database;
use App\Security;
use PDO;
use PDOException;

class User
{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection('sqlite', [
            'path' => __DIR__ . '/../../../database.db'
        ]);
    }

    public function findById(int $id): ?array
    {
        if (!$this->pdo) {
            return null;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT id, name, email, created_at FROM Users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch();
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Помилка при пошуку користувача: " . $e->getMessage());
            return null;
        }
    }

    public function findByEmail(string $email): ?array
    {
        if (!$this->pdo) {
            return null;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT id, name, email, created_at FROM Users WHERE email = :email");
            $stmt->execute(['email' => Security::filterEmail($email)]);
            $user = $stmt->fetch();
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Помилка при пошуку користувача: " . $e->getMessage());
            return null;
        }
    }

    public function getAll(): array
    {
        if (!$this->pdo) {
            return [];
        }

        try {
            $stmt = $this->pdo->query("SELECT id, name, email, created_at FROM Users ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Помилка при отриманні користувачів: " . $e->getMessage());
            return [];
        }
    }

    public function create(string $name, string $email, string $password): bool
    {
        if (!$this->pdo) {
            return false;
        }

        try {
            $name = Security::filterString($name);
            $email = Security::filterEmail($email);
            $password = Security::filterString($password);

            if (!Security::validateEmail($email)) {
                return false;
            }

            if (!Security::validatePassword($password)) {
                return false;
            }

            $stmt = $this->pdo->prepare("SELECT id FROM Users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                return false;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare("INSERT INTO Users (name, email, password) VALUES (:name, :email, :password)");
            return $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword
            ]);
        } catch (PDOException $e) {
            error_log("Помилка при створенні користувача: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, string $name, ?string $email = null, ?string $password = null): bool
    {
        if (!$this->pdo) {
            return false;
        }

        try {
            $name = Security::filterString($name);
            $updates = ['name' => $name];
            $params = ['id' => $id, 'name' => $name];

            if ($email !== null) {
                $email = Security::filterEmail($email);
                if (!Security::validateEmail($email)) {
                    return false;
                }
                $updates['email'] = $email;
                $params['email'] = $email;
            }

            if ($password !== null) {
                $password = Security::filterString($password);
                if (!Security::validatePassword($password)) {
                    return false;
                }
                $updates['password'] = password_hash($password, PASSWORD_BCRYPT);
                $params['password'] = $updates['password'];
            }

            $setClause = [];
            foreach (array_keys($updates) as $key) {
                $setClause[] = "$key = :$key";
            }
            $setClause = implode(', ', $setClause);

            $stmt = $this->pdo->prepare("UPDATE Users SET $setClause WHERE id = :id");
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Помилка при оновленні користувача: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        if (!$this->pdo) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM Users WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Помилка при видаленні користувача: " . $e->getMessage());
            return false;
        }
    }

    public function authenticate(string $email, string $password): ?array
    {
        if (!$this->pdo) {
            return null;
        }

        try {
            $email = Security::filterEmail($email);
            $password = Security::filterString($password);

            $stmt = $this->pdo->prepare("SELECT id, name, email, password FROM Users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }

            return null;
        } catch (PDOException $e) {
            error_log("Помилка при авторизації: " . $e->getMessage());
            return null;
        }
    }
}

