<?php

abstract class User
{
    private string $name;
    private string $email;

    public function __construct(string $name, string $email)
    {
        $this->name  = $name;
        $this->email = $email;
    }

    abstract public function getRole(): string;

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

$student = new Student('Іван Петренко', 'ivan.petrenko@example.com', 'КН-21');
$teacher = new Teacher('Олена Іваненко', 'olena.ivanenko@example.com', 'Веб-програмування');

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

echo '<h1>Користувачі системи</h1>';
printUserInfo($student);
printUserInfo($teacher);



