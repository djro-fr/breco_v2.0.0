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
            throw new \InvalidArgumentException("L'e-mail est obligatoire");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Le format d'e-mail n'est pas bon");
        }

        if (empty($password)) {
            throw new \InvalidArgumentException("Le mot de passe est obligatoire");
        }
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException("Le mot de passe doit contenir au moins 8 caractères");
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            throw new \InvalidArgumentException("Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre");
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
