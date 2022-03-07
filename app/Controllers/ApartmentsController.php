<?php

namespace App\Controllers;

use App\Database;
use App\Models\Apartment;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Review;
use App\Redirect;
use App\Views\View;
use Carbon\Carbon;

class ApartmentsController
{
    public function index(): View
    {
        $apartmentsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->executeQuery()
            ->fetchAllAssociative();

        $apartments = [];
        foreach ($apartmentsQuery as $apartmentData) {
            $apartments [] = new Apartment(
                $apartmentData['id'],
                $apartmentData['name'],
                $apartmentData['address'],
                $apartmentData['description'],
                $apartmentData['available_from'],
                $apartmentData['available_to'],
                $apartmentData['owner_id']
            );
        }
        $active = $_SESSION["fullName"];
        $activeId = (int)$_SESSION["id"];
        return new View('Apartments/index', [
            'apartments' => $apartments,
            'active' => $active,
            'activeId' => $activeId
        ]);
    }

    public function show(array $vars): View
    {
        $apartmentId = (int)$vars['id'];
        $apartmentQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        $apartment = new Apartment(
            $apartmentQuery['id'],
            $apartmentQuery['name'],
            $apartmentQuery['address'],
            $apartmentQuery['description'],
            $apartmentQuery['available_from'],
            $apartmentQuery['available_to'],
            $apartmentQuery['owner_id']
        );

        $reviewsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartment_reviews')
            ->where('apartment_id = ?')
            ->setParameter(0, $apartmentId)
            ->orderBy('created_at', 'desc')
            ->executeQuery()
            ->fetchAllAssociative();

        //check if not null, then create object
        $reviews = [];
        foreach ($reviewsQuery as $reviewData) {
            $reviews [] = new Review(
                $reviewData['apartment_id'],
                $reviewData['created_at'],
                $reviewData['author'],
                $reviewData['author_id'],
                $reviewData['text'],
                $reviewData['id']
            );
        }
        $numberOfReviews = count($reviews);

        $emptyReview = "";
        if (isset($_SESSION['emptyReview'])) {
            $emptyReview = $_SESSION['emptyReview'];
            unset($_SESSION['emptyReview']);
        }

        $active = $_SESSION["fullName"];
        $activeId = (int)$_SESSION["id"];
        return new View('Apartments/show', [
            'apartment' => $apartment,
            'reviews' => $reviews,
            'numberOfReviews' => $numberOfReviews,
            'active' => $active,
            'activeId' => $activeId,
            'emptyReview' => $emptyReview
        ]);
    }

    public function create(array $vars): View
    {
        $name = $_SESSION["name"];
        $address = $_SESSION["address"];
        $description = $_SESSION["description"];
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];
        return new View('Apartments/create', [
            'active' => $active,
            'id' => $activeId,
            'name' => $name,
            'address' => $address,
            'description' => $description
        ]);
    }

    public function store(): Redirect
    {
        if (empty($_POST['name']) || empty($_POST['address']) || empty($_POST['description'])) {
            $_SESSION["name"] = $_POST['name'];
            $_SESSION["address"] = $_POST['address'];
            $_SESSION["description"] = $_POST['description'];
            //empty name/address/description                   TODO error messages, time >=now and limit
            return new Redirect('/apartments/create');
        }

        $dt = Carbon::now();

        if (empty($_POST['available_from'])) {
            $availableFrom = $dt->toDateString();
        } else {
            $availableFrom = $_POST['available_from'];
        }
        $endOfYear = $dt->endOfYear()->toDateString();
        if (empty($_POST['available_to'])) {
            $availableTo = $endOfYear;
        } else {
            $availableTo = $_POST['available_to'];
        }


        $activeId = $_SESSION["id"];
        Database::connection()
            ->insert('apartments', [
                'name' => $_POST['name'],
                'address' => $_POST['address'],
                'description' => $_POST['description'],
                'available_from' => $availableFrom,
                'available_to' => $availableTo,
                'owner_id' => $activeId
            ]);
        unset($_SESSION["name"]);
        unset($_SESSION["address"]);
        unset($_SESSION["description"]);
        return new Redirect('/apartments');
    }

    public function delete(array $vars): Redirect         //TODO delete also reservations
    {
        $apartmentId = (int)$vars['id'];
        $apartmentQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        $activeId = $_SESSION["id"];
        if ($activeId == $apartmentQuery['owner_id']) {
            Database::connection()
                ->delete('apartments', ['id' => $apartmentId]);
        }
        return new Redirect('/apartments');
    }

    public function edit(array $vars): View
    {

        $apartmentId = (int)$vars['id'];
        $apartmentQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        $apartment = new Apartment(
            $apartmentQuery['id'],
            $apartmentQuery['name'],
            $apartmentQuery['address'],
            $apartmentQuery['description'],
            $apartmentQuery['available_from'],
            $apartmentQuery['available_to'],
            $apartmentQuery['owner_id']
        );

        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];

        return new View('apartments/edit', [
            'apartment' => $apartment,
            'active' => $active,
            'activeId' => $activeId
        ]);
    }

    public function update(array $vars): Redirect
    {
        $apartmentId = (int)$vars['id'];
        $apartmentQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        $activeId = $_SESSION["id"];
        if ($activeId == $apartmentQuery['owner_id']) {
            Database::connection()
                ->update('apartments', [
                    'name' => $_POST['name'],
                    'address' => $_POST['address'],
                    'description' => $_POST['description'],
                ], ['id' => $apartmentId]
                );
        }
        return new Redirect('/apartments/' . $apartmentId);
    }

}