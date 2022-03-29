<?php

namespace App\Controllers;

use App\Database;
use App\Models\Apartment;
use App\Models\Reservation;
use App\Redirect;
use App\Services\User\EmailNotRegistered\EmailNotRegisteredService;
use App\Services\User\EmailRegistered\EmailRegisteredService;
use App\Services\User\Enter\EnterUserService;
use App\Services\User\Index\IndexUserService;
use App\Services\User\Register\RegisterUserRequest;
use App\Services\User\Register\RegisterUserService;
use App\Services\User\Show\ShowUserService;
use App\Views\View;
use Carbon\Carbon;

class UsersController
{
    public function index(): View
    {
        $service = new IndexUserService();
        $users = $service->execute();

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
        $active = $_SESSION["fullName"];
        $activeId = (int)$_SESSION["id"];
        $apartmentId = (int)$vars['id'];
        $service = new ShowUserService();
        $user = $service->execute($apartmentId);

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
        $email = $_POST['email'];
        $service = new EmailNotRegisteredService();

        // 1=>email is already registered or 0=>email is not registered
        if ($service->execute($email) == 1) {
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
        $request = new RegisterUserRequest($_POST['name'], $_POST['surname'], $_POST['birthday'], $_POST['email'], $hashedPassword);
        $service = new RegisterUserService();
        $service->execute($request);

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
        $email = $_POST['email'];
        // Getting user data to validate login inputs
        $service = new EmailRegisteredService();
        $userData = $service->execute($email);

        //Validation: is email registered in database?
        if ($userData['email'] == null) {
            $_SESSION["unknownEmail"] = "This e-mail is not registered in BookingApp database";
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/login');
        }

        //Validation: is password correct?
        if (!password_verify($_POST['password'], $userData['password'])) {
            $_SESSION["wrongPassword"] = "Wrong password";
            $_SESSION["email"] =$_POST['email'];
            return new Redirect('/users/login');
        }

        // Getting user_profile data to save active user info
        $service = new EnterUserService();
        $userProfileData = $service->execute($userData['id']);

        // Saving active user data in session
        $_SESSION["fullName"] = $userProfileData->getName() . " " . $userProfileData->getSurname();
        $_SESSION["id"] = $userProfileData->getId();

        return new Redirect('/welcome');
    }

    public function logout(): View
    {
        session_unset();
        session_destroy();
        return new View('Users/logout');
    }

    public function reservations(array $vars): View
    {
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];

        $apartmentReservationsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartment_reservations')
            ->where('user_id = ?')
            ->setParameter(0, $activeId)
            ->orderBy('reserved_from', 'asc')
            ->executeQuery()
            ->fetchAllAssociative();

        $reservations = [];
        $apartmentIds = [];
        foreach ($apartmentReservationsQuery as $apartmentReservationData) {
            $reservations [] = new Reservation(
                $apartmentReservationData['apartment_id'],
                $apartmentReservationData['user_id'],
                $apartmentReservationData['reserved_from'],
                $apartmentReservationData['reserved_to'],
                $apartmentReservationData['id']
            );
            $apartmentIds [] = $apartmentReservationData['apartment_id'];
        }
        $uniqueApartmentIds = array_unique($apartmentIds);
        $apartments =[];
        foreach ($uniqueApartmentIds as $apartmentId){
            $apartmentQuery = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('apartments')
                ->where('id = ?')
                ->setParameter(0, $apartmentId)
                ->executeQuery()
                ->fetchAssociative();

            $apartments [] = new Apartment(
                $apartmentQuery['name'],
                $apartmentQuery['address'],
                $apartmentQuery['description'],
                $apartmentQuery['available_from'],
                $apartmentQuery['available_to'],
                $apartmentQuery['owner_id'],
                $apartmentQuery['price'],
                $apartmentQuery['rating'],
                $apartmentQuery['id']
            );
        }

        return new View("Users/reservations", [
            'reservations' => $reservations,
            'apartments' => $apartments,
            'active' => $active,
            'activeId' => $activeId
        ]);
    }

    public function apartments(array $vars): View
    {
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];

        $apartmentsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('owner_id = ?')
            ->setParameter(0, $activeId)
            ->executeQuery()
            ->fetchAllAssociative();

        $apartments = [];
        foreach ($apartmentsQuery as $apartmentData) {
            $apartments [] = new Apartment(
                $apartmentData['name'],
                $apartmentData['address'],
                $apartmentData['description'],
                $apartmentData['available_from'],
                $apartmentData['available_to'],
                $apartmentData['owner_id'],
                $apartmentData['price'],
                $apartmentData['rating'],
                $apartmentData['id']
            );

        }

        return new View("Users/apartments", [
            'apartments' => $apartments,
            'active' => $active,
            'activeId' => $activeId
        ]);
    }

}