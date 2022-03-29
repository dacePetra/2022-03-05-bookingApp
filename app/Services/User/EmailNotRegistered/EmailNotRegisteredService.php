<?php

namespace App\Services\User\EmailNotRegistered;

use App\Repositories\User\MySqlUserRepository;
use App\Repositories\User\UserRepository;

class EmailNotRegisteredService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new MySqlUserRepository();
    }

    public function execute(string $email):int
    {
        return $this->userRepository->emailNotRegistered($email);
    }

}