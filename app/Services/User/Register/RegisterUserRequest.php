<?php

namespace App\Services\User\Register;

class RegisterUserRequest
{
    private string $name;
    private string $surname;
    private string $birthday;
    private string $email;
    private string $password;

    public function __construct(string $name, string $surname, string $birthday, string $email, string $password)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->birthday = $birthday;
        $this->email = $email;
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getBirthday(): string
    {
        return $this->birthday;
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