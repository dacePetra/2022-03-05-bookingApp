<?php

namespace App\Services\User\EmailRegistered;

use App\Repositories\User\MySqlUserRepository;
use App\Repositories\User\UserRepository;

class EmailRegisteredService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new MySqlUserRepository();
    }

    public function execute(string $email): array
    {
        return $this->userRepository->emailRegistered($email);
    }

}