<?php
namespace App\Controllers;

use App\Database;
use App\Models\User;
use App\Views\View;

class UsersController
{
    public function index(): View
    {
        $usersQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->executeQuery()
            ->fetchAllAssociative();

        $users = [];
        foreach ($usersQuery as $userData) {
            $users [] = new User(
                $userData['email'],
                $userData['password'],
                $userData['id']
            );
        }


        return new View('Users/index', [
            'users' => $users
        ]);
    }

}