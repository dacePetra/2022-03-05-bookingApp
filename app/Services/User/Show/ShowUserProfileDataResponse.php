<?php

namespace App\Services\User\Show;

class ShowUserProfileDataResponse
{
    private string $name;
    private string $surname;
    private string $birthday;

    public function __construct(string $name, string $surname, string $birthday)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->birthday = $birthday;
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

}