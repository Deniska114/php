<?php

// 1. Абстрактний клас User
abstract class User
{
    // Приватні поля
    private string $name;
    private string $email;

    // Конструктор
    public function __construct(string $name, string $email)
    {
        $this->name  = $name;
        $this->email = $email;
    }

    // Абстрактний метод ролі
    abstract public function getRole(): string;

    // Гетери та сетери
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}

// 2. Клас Student, який наслідує User
class Student extends User
{
    private string $group;

    public function __construct(string $name, string $email, string $group)
    {
        parent::__construct($name, $email);
        $this->group = $group;
    }

    public function getRole(): string
    {
        return 'Студент';
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group): void
    {
        $this->group = $group;
    }
}

// 3. Клас Teacher, який наслідує User
class Teacher extends User
{
    private string $subject;

    public function __construct(string $name, string $email, string $subject)
    {
        parent::__construct($name, $email);
        $this->subject = $subject;
    }

    public function getRole(): string
    {
        return 'Викладач';
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
}

// 4. Приклад використання: створюємо одного студента та одного викладача

$student = new Student('Іван Петренко', 'ivan.petrenko@example.com', 'КН-21');
$teacher = new Teacher('Олена Іваненко', 'olena.ivanenko@example.com', 'Веб-програмування');

// Функція для виводу інформації про користувача
function printUserInfo(User $user): void
{
    echo '<p>';
    echo 'Ім’я: ' . htmlspecialchars($user->getName()) . '<br>';
    echo 'Email: ' . htmlspecialchars($user->getEmail()) . '<br>';
    echo 'Роль: ' . htmlspecialchars($user->getRole()) . '<br>';

    if ($user instanceof Student) {
        echo 'Група: ' . htmlspecialchars($user->getGroup()) . '<br>';
    } elseif ($user instanceof Teacher) {
        echo 'Предмет: ' . htmlspecialchars($user->getSubject()) . '<br>';
    }

    echo '</p>';
}

// 5. Виводимо дані
echo '<h1>Користувачі системи</h1>';
printUserInfo($student);
printUserInfo($teacher);


