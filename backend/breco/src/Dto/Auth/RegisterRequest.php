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
        $this->validate($email, $password, $firstName, $lastName, $phone);

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
        string $phone
    ): void {
        // Email
        if (empty($email)) {
            throw new \InvalidArgumentException('Email is required');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        // Password
        if (empty($password)) {
            throw new \InvalidArgumentException('Password is required');
        }
        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters');
        }

        // First name
        if (empty($firstName)) {
            throw new \InvalidArgumentException('First name is required');
        }
        if (strlen($firstName) < 2) {
            throw new \InvalidArgumentException('First name must be at least 2 characters');
        }

        // Last name
        if (empty($lastName)) {
            throw new \InvalidArgumentException('Last name is required');
        }
        if (strlen($lastName) < 2) {
            throw new \InvalidArgumentException('Last name must be at least 2 characters');
        }

        // Phone
        if (empty($phone)) {
            throw new \InvalidArgumentException('Phone is required');
        }
        if (!preg_match('/^[0-9]{10,15}$/', preg_replace('/[\s\-\.]/', '', $phone))) {
            throw new \InvalidArgumentException('Invalid phone format');
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
}
