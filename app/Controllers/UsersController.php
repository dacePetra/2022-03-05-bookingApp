<?php

namespace App\Controllers;

use App\Database;
use App\Models\User;
use App\Redirect;
use App\Views\View;
use Carbon\Carbon;

class UsersController
{
    public function index(): View
    {
        $userProfilesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('user_profiles')
            ->executeQuery()
            ->fetchAllAssociative();

        $usersQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->executeQuery()
            ->fetchAllAssociative();

        $users = [];
        foreach ($usersQuery as $userData) {
            foreach ($userProfilesQuery as $userProfileData) {
                if ($userData['id'] == $userProfileData['user_id']) {
                    $users [] = new User(
                        $userProfileData['name'],
                        $userProfileData['surname'],
                        $userProfileData['birthday'],
                        $userData['email'],
                        $userData['password'],
                        $userData['created_at'],
                        $userData['id']
                    );
                }
            }
        }
        $active = $_SESSION["fullName"];
        $activeId = (int)$_SESSION["id"];

        return new View('Users/index', [
            'users' => $users,
            'active' => $active,
            'activeId' => $activeId
        ]);
    }

    public function show(array $vars): View
    {
        $userProfilesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('user_profiles')
            ->where('user_id = ?')
            ->setParameter(0, (int)$vars['id'])
            ->executeQuery()
            ->fetchAssociative();

        $usersQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('id = ?')
            ->setParameter(0, (int)$vars['id'])
            ->executeQuery()
            ->fetchAssociative();


        $user = new User(
            $userProfilesQuery['name'],
            $userProfilesQuery['surname'],
            $userProfilesQuery['birthday'],
            $usersQuery['email'],
            $usersQuery['password'],
            $usersQuery['created_at'],
            $usersQuery['id']
        );

        $active = $_SESSION["fullName"];
        $activeId = (int)$_SESSION["id"];

        return new View('Users/show', [
            'user' => $user,
            'active' => $active,
            'activeId' => $activeId
        ]);
    }

    public function signup(): View
    {
        $name =$_SESSION['name'];
        $surname =$_SESSION['surname'];
        $birthday =$_SESSION['birthday'];
        $email =$_SESSION['email'];
        unset($_SESSION["name"]);
        unset($_SESSION["surname"]);
        unset($_SESSION["birthday"]);
        unset($_SESSION["email"]);

        $invalidName = "";
        if (isset($_SESSION['invalidName'])) {
            $invalidName = $_SESSION['invalidName'];
            unset($_SESSION['invalidName']);
        }

        $invalidSurname = "";
        if (isset($_SESSION['invalidSurname'])) {
            $invalidSurname = $_SESSION['invalidSurname'];
            unset($_SESSION['invalidSurname']);
        }

        $invalidBirthday = "";
        if (isset($_SESSION['invalidBirthday'])) {
            $invalidBirthday = $_SESSION['invalidBirthday'];
            unset($_SESSION['invalidBirthday']);
        }

        $invalidEmail = "";
        if (isset($_SESSION['invalidEmail'])) {
            $invalidEmail = $_SESSION['invalidEmail'];
            unset($_SESSION['invalidEmail']);
        }

        $usedEmail = "";
        if (isset($_SESSION['usedEmail'])) {
            $usedEmail = $_SESSION['usedEmail'];
            unset($_SESSION['usedEmail']);
        }

        $invalidPasswords = "";
        if (isset($_SESSION['invalidPasswords'])) {
            $invalidPasswords = $_SESSION['invalidPasswords'];
            unset($_SESSION['invalidPasswords']);
        }
        $invalidPassword = "";
        if (isset($_SESSION['invalidPassword'])) {
            $invalidPassword = $_SESSION['invalidPassword'];
            unset($_SESSION['invalidPassword']);
        }

        return new View('Users/signup', [
            'invalidName' => $invalidName,
            'invalidSurname' => $invalidSurname,
            'invalidBirthday' => $invalidBirthday,
            'invalidEmail' => $invalidEmail,
            'usedEmail' => $usedEmail,
            'invalidPasswords' => $invalidPasswords,
            'invalidPassword' => $invalidPassword,
            'name' => $name,
            'surname' => $surname,
            'birthday' => $birthday,
            'email' => $email
        ]);
    }

    public function register(array $vars): Redirect
    {
        //Validation: is name valid?
        if (!preg_match("/^[a-zA-Z]*$/", $_POST['name'])) {
            $_SESSION["invalidName"] = "Invalid name, please use only letters";
            $_SESSION["name"] =$_POST['name'];
            $_SESSION["surname"] =$_POST['surname'];
            $_SESSION["birthday"] =$_POST['birthday'];
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/signup');
        }

        //Validation: is surname valid?
        if (!preg_match("/^[a-zA-Z]*$/", $_POST['surname'])) {
            $_SESSION["invalidSurname"] = "Invalid surname, please use only letters";
            $_SESSION["name"] =$_POST['name'];
            $_SESSION["surname"] =$_POST['surname'];
            $_SESSION["birthday"] =$_POST['birthday'];
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/signup');
        }

        //Validation: is date valid? (must be before today's date)
        $birthday = Carbon::parse($_POST['birthday']);
        $today = Carbon::parse(Carbon::now()->toDateString());
        if($birthday->greaterThanOrEqualTo($today)){
            $_SESSION["invalidBirthday"] = "Invalid date";
            $_SESSION["name"] =$_POST['name'];
            $_SESSION["surname"] =$_POST['surname'];
            $_SESSION["birthday"] =$_POST['birthday'];
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/signup');
        }

        //Validation: is email format valid?
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["invalidEmail"] = "Invalid email";
            $_SESSION["name"] =$_POST['name'];
            $_SESSION["surname"] =$_POST['surname'];
            $_SESSION["birthday"] =$_POST['birthday'];
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/signup');
        }
        //Validation: is email already registered in database?
        $usersQuery = Database::connection()
            ->createQueryBuilder()
            ->select('email')
            ->from('users')
            ->where("email = '{$_POST['email']}'")
            ->executeQuery()
            ->fetchAssociative();
        if ($usersQuery != false) {
            $_SESSION["usedEmail"] = "E-mail is already registered in BookingApp database";
            $_SESSION["name"] =$_POST['name'];
            $_SESSION["surname"] =$_POST['surname'];
            $_SESSION["birthday"] =$_POST['birthday'];
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/signup');
        }

        //Validation: do passwords match?
        if ($_POST['password'] != $_POST['password_repeat']) {
            $_SESSION["invalidPasswords"] = "Passwords don't match";
            $_SESSION["name"] =$_POST['name'];
            $_SESSION["surname"] =$_POST['surname'];
            $_SESSION["birthday"] =$_POST['birthday'];
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/signup');
        }

        //Validation: is password's length at least 8 symbols?
        if (strlen($_POST['password'])<8) {
            $_SESSION["invalidPassword"] = "Password must be at least 8 symbols";
            $_SESSION["name"] =$_POST['name'];
            $_SESSION["surname"] =$_POST['surname'];
            $_SESSION["birthday"] =$_POST['birthday'];
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/signup');
        }

        // Before saving the password, it must be hashed
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Saving user data in database (to 2 tables: users & user_profiles)
        Database::connection()
            ->insert('users', [
                'email' => $_POST['email'],
                'password' => $hashedPassword
            ]);
        $usersQuery = Database::connection()
            ->createQueryBuilder()
            ->select('email, id')
            ->from('users')
            ->where('email = ?')
            ->setParameter(0, $_POST['email'])
            ->executeQuery()
            ->fetchAssociative();

        $id = $usersQuery["id"];
        Database::connection()
            ->insert('user_profiles', [
                'user_id' => $id,
                'name' => $_POST['name'],
                'surname' => $_POST['surname'],
                'birthday' => $_POST['birthday']
            ]);

        return new Redirect('/');
    }

    public function login(array $vars): View
    {

        $email =$_SESSION['email'];
        unset($_SESSION["email"]);

        $unknownEmail = "";
        if (isset($_SESSION['unknownEmail'])) {
            $unknownEmail = $_SESSION['unknownEmail'];
            unset($_SESSION['unknownEmail']);
        }

        $wrongPassword = "";
        if (isset($_SESSION['wrongPassword'])) {
            $wrongPassword = $_SESSION['wrongPassword'];
            unset($_SESSION['wrongPassword']);
        }

        return new View('Users/login', [
            'unknownEmail' => $unknownEmail,
            'wrongPassword' => $wrongPassword,
            'email' => $email
        ]);
    }

    public function enter(array $vars): Redirect
    {
        // Getting user data to validate login inputs
        $usersQuery = Database::connection()
            ->createQueryBuilder()
            ->select('email, password, created_at, id')
            ->from('users')
            ->where('email = ?')
            ->setParameter(0, $_POST['email'])
            ->executeQuery()
            ->fetchAssociative();

        //Validation: is email registered in database?
        if ($usersQuery == false) {
            $_SESSION["unknownEmail"] = "This e-mail is not registered in BookingApp database";
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/login');
        }

        //Validation: is password correct?
        if (!password_verify($_POST['password'], $usersQuery['password'])) {
            $_SESSION["wrongPassword"] = "Wrong password";
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/login');
        }

        // Getting user_profile data to save active user info
        $userProfilesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('user_profiles')
            ->where('user_id = ?')
            ->setParameter(0, (int)$usersQuery["id"])
            ->executeQuery()
            ->fetchAssociative();

        // Saving active user data in session
        $_SESSION["fullName"] = $userProfilesQuery['name'] . " " . $userProfilesQuery['surname'];
        $_SESSION["id"] = $userProfilesQuery['user_id'];

        return new Redirect('/welcome');
    }

    public function logout(): View
    {
        session_unset();
        session_destroy();
        return new View('Users/logout');
    }

}