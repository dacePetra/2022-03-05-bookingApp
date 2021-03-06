<?php

namespace App\Services\User\Enter;

class EnterUserProfileDataResponse
{
    private string $name;
    private string $surname;
    private int $id;

    public function __construct(string $name, string $surname, int $id)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getId(): int
    {
        return $this->id;
    }

}