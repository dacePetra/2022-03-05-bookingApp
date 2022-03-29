<?php

namespace App\Services\User\Enter;

use App\Repositories\User\MySqlUserRepository;
use App\Repositories\User\UserRepository;

class EnterUserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new MySqlUserRepository();
    }

    public function execute(int $id): EnterUserProfileDataResponse
    {
        return $this->userRepository->getActiveUserProfileData($id);
    }

}