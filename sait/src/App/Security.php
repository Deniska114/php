<?php

namespace App;

class Security
{
    public static function filterXSS(string $input): string
    {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        return $input;
    }

    public static function filterEmail(string $email): string
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $email = self::filterXSS($email);
        return $email;
    }

    public static function filterString(string $input): string
    {
        return self::filterXSS($input);
    }

    public static function escapeOutput(string $output): string
    {
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePassword(string $password): bool
    {
        return strlen($password) >= 6;
    }
}
