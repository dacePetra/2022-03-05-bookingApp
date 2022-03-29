<?php

namespace App\Services\User\Register;

use App\Models\User;
use App\Repositories\User\MySqlUserRepository;
use App\Repositories\User\UserRepository;

class RegisterUserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new MySqlUserRepository();
    }

    public function execute(RegisterUserRequest $request): void
    {
        $user = new User($request->getName(), $request->getSurname(), $request->getBirthday(), $request->getEmail(), $request->getPassword());
        $id = $this->userRepository->saveUser($user);

        $registeredUser = new User($request->getName(), $request->getSurname(), $request->getBirthday(), $request->getEmail(), $request->getPassword(), $id);
        $this->userRepository->saveUserProfile($registeredUser);
    }

}