<?php

namespace App\Services\User\Show;

use App\Models\User;
use App\Repositories\User\MySqlUserRepository;
use App\Repositories\User\UserRepository;

class ShowUserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new MySqlUserRepository();
    }

    public function execute(int $apartmentId): User
    {
        $userData = $this->userRepository->getUserData($apartmentId);
        $userProfileData = $this->userRepository->getUserProfileData($apartmentId);
        return new User(
            $userProfileData->getName(),
            $userProfileData->getSurname(),
            $userProfileData->getBirthday(),
            $userData->getEmail(),
            $userData->getPassword(),
            $userData->getId(),
            $userData->getCreatedAt()
        );
    }

}