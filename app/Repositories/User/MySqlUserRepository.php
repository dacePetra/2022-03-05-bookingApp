<?php

namespace App\Repositories\User;

use App\Database;
use App\Models\User;
use App\Services\User\Enter\EnterUserProfileDataResponse;
use App\Services\User\Index\IndexUserDataResponse;
use App\Services\User\Index\IndexUserProfileDataResponse;
use App\Services\User\Show\ShowUserDataResponse;
use App\Services\User\Show\ShowUserProfileDataResponse;

class MySqlUserRepository implements UserRepository
{
    public function getUsersData(): array
    {
        $usersQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->executeQuery()
            ->fetchAllAssociative();

        $response = [];
        foreach ($usersQuery as $userData) {
            $response [] = new IndexUserDataResponse(
                $userData['email'],
                $userData['password'],
                $userData['id'],
                $userData['created_at']
            );
        }
        return $response;
    }

    public function getUserProfilesData(): array
    {
        $userProfilesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('user_profiles')
            ->executeQuery()
            ->fetchAllAssociative();

        $response = [];
        foreach ($userProfilesQuery as $userProfileData) {
            $response [] = new IndexUserProfileDataResponse(
                    $userProfileData['name'],
                    $userProfileData['surname'],
                    $userProfileData['birthday'],
                    $userProfileData['user_id'],
                );
        }
        return $response;
    }

    public function getUserData(int $apartmentId): ShowUserDataResponse
    {
        $usersQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        return new ShowUserDataResponse(
            $usersQuery['email'],
            $usersQuery['password'],
            $usersQuery['id'],
            $usersQuery['created_at']
        );
    }
    public function getUserProfileData(int $apartmentId): ShowUserProfileDataResponse
    {
        $userProfilesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('user_profiles')
            ->where('user_id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        return new ShowUserProfileDataResponse(
            $userProfilesQuery['name'],
            $userProfilesQuery['surname'],
            $userProfilesQuery['birthday'],
        );
    }

    public function emailNotRegistered(string $email): int
    {
        $userQuery = Database::connection()
            ->createQueryBuilder()
            ->select('email')
            ->from('users')
            ->where("email = '{$email}'")
            ->executeQuery()
            ->fetchAssociative();

        return (int)$userQuery;
    }

    public function saveUser(User $user): int
    {
        Database::connection()
            ->insert('users', [
                'email' => $user->getEmail(),
                'password' => $user->getPassword()
            ]);

        $id = Database::connection()->lastInsertId();

        return $id;
    }

    public function saveUserProfile(User $user): void
    {
        Database::connection()
            ->insert('user_profiles', [
                'user_id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'birthday' => $user->getBirthday()
            ]);
    }

    public function emailRegistered(string $email): array
    {
        $userQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where("email = '{$email}'")
            ->executeQuery()
            ->fetchAssociative();

        return [
            'email' => $userQuery['email'],
            'password' => $userQuery['password'],
            'id' => $userQuery['id']
        ];
    }

    public function getActiveUserProfileData(int $id): EnterUserProfileDataResponse
    {
        $userProfileQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('user_profiles')
            ->where('user_id = ?')
            ->setParameter(0, $id)
            ->executeQuery()
            ->fetchAssociative();

        return new EnterUserProfileDataResponse(
            $userProfileQuery['name'],
            $userProfileQuery['surname'],
            $userProfileQuery['user_id']
        );
    }
}