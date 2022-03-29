<?php

namespace App\Services\User\Show;

class ShowUserDataResponse
{
    private string $email;
    private string $password;
    private int $id;
    private string $createdAt;

    public function __construct(string $email, string $password, int $id, string $createdAt)
    {
        $this->email = $email;
        $this->password = $password;
        $this->id = $id;
        $this->createdAt = $createdAt;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

}