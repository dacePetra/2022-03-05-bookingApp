<?php

namespace App\Services\User\Index;

class IndexUserProfileDataResponse
{
    private string $name;
    private string $surname;
    private string $birthday;
    private int $id;

    public function __construct(string $name, string $surname, string $birthday, int $id)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->birthday = $birthday;
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

    public function getBirthday(): string
    {
        return $this->birthday;
    }
    public function getId(): int
    {
        return $this->id;
    }

}