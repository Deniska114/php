<?php

namespace App\Models;

use App\Database;
use App\Security;
use PDO;
use PDOException;

class Product
{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection('sqlite', [
            'path' => __DIR__ . '/../../../database.db'
        ]);
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists(): void
    {
        if (!$this->pdo) {
            return;
        }

        try {
            $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

            if ($driver === 'sqlite') {
                $sql = "CREATE TABLE IF NOT EXISTS Products (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    description TEXT,
                    composition TEXT,
                    aroma TEXT,
                    price DECIMAL(10, 2) NOT NULL,
                    image_url TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )";
            } else {
                $sql = "CREATE TABLE IF NOT EXISTS Products (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    description TEXT,
                    composition TEXT,
                    aroma TEXT,
                    price DECIMAL(10, 2) NOT NULL,
                    image_url VARCHAR(500),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
            }

            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Помилка при створенні таблиці Products: " . $e->getMessage());
        }
    }

    public function findById(int $id): ?array
    {
        if (!$this->pdo) {
            return null;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM Products WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $product = $stmt->fetch();
            return $product ?: null;
        } catch (PDOException $e) {
            error_log("Помилка при пошуку продукту: " . $e->getMessage());
            return null;
        }
    }

    public function getAll(): array
    {
        if (!$this->pdo) {
            return [];
        }

        try {
            $stmt = $this->pdo->query("SELECT * FROM Products ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Помилка при отриманні продуктів: " . $e->getMessage());
            return [];
        }
    }

    public function getByPriceRange(float $minPrice, float $maxPrice): array
    {
        if (!$this->pdo) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM Products WHERE price BETWEEN :minPrice AND :maxPrice ORDER BY price ASC");
            $stmt->execute([
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice
            ]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Помилка при пошуку продуктів за ціною: " . $e->getMessage());
            return [];
        }
    }

    public function search(string $query): array
    {
        if (!$this->pdo) {
            return [];
        }

        try {
            $query = Security::filterString($query);
            $searchTerm = "%{$query}%";
            $stmt = $this->pdo->prepare("SELECT * FROM Products WHERE name LIKE :query OR description LIKE :query OR composition LIKE :query OR aroma LIKE :query ORDER BY name ASC");
            $stmt->execute(['query' => $searchTerm]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Помилка при пошуку продуктів: " . $e->getMessage());
            return [];
        }
    }

    public function create(string $name, string $description, string $composition, string $aroma, float $price, string $imageUrl = ''): bool
    {
        if (!$this->pdo) {
            return false;
        }

        try {
            $name = Security::filterString($name);
            $description = Security::filterString($description);
            $composition = Security::filterString($composition);
            $aroma = Security::filterString($aroma);
            $imageUrl = Security::filterString($imageUrl);

            if (empty($name) || $price <= 0) {
                return false;
            }

            $stmt = $this->pdo->prepare("INSERT INTO Products (name, description, composition, aroma, price, image_url) VALUES (:name, :description, :composition, :aroma, :price, :image_url)");
            return $stmt->execute([
                'name' => $name,
                'description' => $description,
                'composition' => $composition,
                'aroma' => $aroma,
                'price' => $price,
                'image_url' => $imageUrl
            ]);
        } catch (PDOException $e) {
            error_log("Помилка при створенні продукту: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, ?string $name = null, ?string $description = null, ?string $composition = null, ?string $aroma = null, ?float $price = null, ?string $imageUrl = null): bool
    {
        if (!$this->pdo) {
            return false;
        }

        try {
            $updates = [];
            $params = ['id' => $id];

            if ($name !== null) {
                $updates['name'] = Security::filterString($name);
                $params['name'] = $updates['name'];
            }

            if ($description !== null) {
                $updates['description'] = Security::filterString($description);
                $params['description'] = $updates['description'];
            }

            if ($composition !== null) {
                $updates['composition'] = Security::filterString($composition);
                $params['composition'] = $updates['composition'];
            }

            if ($aroma !== null) {
                $updates['aroma'] = Security::filterString($aroma);
                $params['aroma'] = $updates['aroma'];
            }

            if ($price !== null) {
                if ($price <= 0) {
                    return false;
                }
                $updates['price'] = $price;
                $params['price'] = $price;
            }

            if ($imageUrl !== null) {
                $updates['image_url'] = Security::filterString($imageUrl);
                $params['image_url'] = $updates['image_url'];
            }

            if (empty($updates)) {
                return false;
            }

            $setClause = [];
            foreach (array_keys($updates) as $key) {
                $setClause[] = "$key = :$key";
            }
            $setClause[] = "updated_at = CURRENT_TIMESTAMP";
            $setClause = implode(', ', $setClause);

            $stmt = $this->pdo->prepare("UPDATE Products SET $setClause WHERE id = :id");
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Помилка при оновленні продукту: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        if (!$this->pdo) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM Products WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Помилка при видаленні продукту: " . $e->getMessage());
            return false;
        }
    }

    public function count(): int
    {
        if (!$this->pdo) {
            return 0;
        }

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Products");
            $result = $stmt->fetch();
            return (int)($result['count'] ?? 0);
        } catch (PDOException $e) {
            error_log("Помилка при підрахунку продуктів: " . $e->getMessage());
            return 0;
        }
    }
}
