<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Services\User\Enter\EnterUserProfileDataResponse;
use App\Services\User\Show\ShowUserDataResponse;
use App\Services\User\Show\ShowUserProfileDataResponse;

interface UserRepository
{
    public function getUsersData(): array;
    public function getUserProfilesData(): array;

    public function getUserData(int $apartmentId): ShowUserDataResponse;
    public function getUserProfileData(int $apartmentId): ShowUserProfileDataResponse;

    public function emailNotRegistered(string $email): int;
    public function saveUser(User $user): int;
    public function saveUserProfile(User $user): void;

    public function emailRegistered(string $email): array;
    public function getActiveUserProfileData(int $id): EnterUserProfileDataResponse;

}