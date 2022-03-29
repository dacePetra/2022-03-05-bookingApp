<?php

namespace App\Services\User\Index;

use App\Models\User;
use App\Repositories\User\MySqlUserRepository;
use App\Repositories\User\UserRepository;

class IndexUserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new MySqlUserRepository();
    }

    public function execute(): array
    {
        $usersData = $this->userRepository->getUsersData();
        $userProfilesData = $this->userRepository->getUserProfilesData();
        $users = [];
        foreach ($usersData as $userData) {
            foreach ($userProfilesData as $userProfileData) {
                if ($userData->getId() == $userProfileData->getId()) {
                    $users [] = new User(
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
        }
        return $users;
    }

}