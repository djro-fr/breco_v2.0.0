<?php
declare(strict_types=1);

namespace App\Dto\Auth;

class LoginRequest
{
    private string $email;
    private string $password;

    public function __construct(string $email, string $password)
    {
        $this->validate($email, $password);

        $this->email = trim($email);
        $this->password = $password;
    }

    private function validate(string $email, string $password): void
    {
        if (empty($email)) {
            throw new \InvalidArgumentException('Email is required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        if (empty($password)) {
            throw new \InvalidArgumentException('Password is required');
        }

        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters');
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
