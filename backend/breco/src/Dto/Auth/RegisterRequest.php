<?php
declare(strict_types=1);

namespace App\Dto\Auth;

class RegisterRequest
{
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastName;
    private string $phone;
    private ?string $gender;
    private ?int $age;

    public function __construct(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        string $phone,
        ?string $gender = null,
        ?int $age = null
    ) {
        $this->validate($email, $password, $firstName, $lastName, $phone, $gender);

        $this->email = trim($email);
        $this->password = $password;
        $this->firstName = trim($firstName);
        $this->lastName = trim($lastName);
        $this->phone = trim($phone);
        $this->gender = $gender;
        $this->age = $age;
    }

    private function validate(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        string $phone,
        ?string $gender
    ): void {
        // Email
        if (empty($email)) {
            throw new \InvalidArgumentException("L'e-mail est obligatoire");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        // Password
        if (empty($password)) {
            throw new \InvalidArgumentException("Le mot de passe est obligatoire");
        }
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException("Le mot de passe doit contenir au moins 8 caractères");
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            throw new \InvalidArgumentException("Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre");
        }

        // First name
        if (empty($firstName)) {
            throw new \InvalidArgumentException("Le prénom est obligatoire");
        }
        if (strlen($firstName) > 50) {
            throw new \InvalidArgumentException("Le prénom ne peut pas dépasser 50 caractères");
        }
        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/', $firstName)) {
            throw new \InvalidArgumentException("Le prénom contient des caractères invalides");
        }

        // Last name
        if (empty($lastName)) {
            throw new \InvalidArgumentException("Le nom de famille est obligatoire");
        }
        if (strlen($lastName) > 50) {
            throw new \InvalidArgumentException("Le nom de famille ne peut pas dépasser 50 caractères");
        }
        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/', $lastName)) {
            throw new \InvalidArgumentException("Le nom de famille contient des caractères invalides");
        }

        // Phone
        if (empty($phone)) {
            throw new \InvalidArgumentException('Le numéro de téléphone est obligatoire');
        }
        $cleanedPhone = preg_replace('/\s/', '', $phone);
        if (!preg_match('/^0[1-9]\d{8}$/', $cleanedPhone)) {
            throw new \InvalidArgumentException("Format téléphone invalide (10 chiffres commençant par 0)");
        }

        // Gender
        if ($gender !== null && !in_array($gender, ['Homme', 'Femme', 'Ne pas dire'])) {
            throw new \InvalidArgumentException("Genre invalide");
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

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => password_hash($this->password, PASSWORD_BCRYPT),
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'age' => $this->age,
        ];
    }

    /**
     * Named constructor: allows instantiation without assigning to a variable.
     * Used in tests to trigger constructor validation without SonarLint warnings.
     */
    public static function create(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        string $phone,
        ?string $gender = null,
        ?int $age = null
    ): self {
        return new self($email, $password, $firstName, $lastName, $phone, $gender, $age);
    }
}
