<?php

namespace App\Controllers;

use App\Database;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Apartment;
use App\Models\Review;
use App\Redirect;
use App\Services\Apartment\Create\CreateApartmentRequest;
use App\Services\Apartment\Create\CreateApartmentService;
use App\Services\Apartment\Delete\DeleteApartmentRequest;
use App\Services\Apartment\Delete\DeleteApartmentService;
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

        try {
            $apartmentQuery = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('apartments')
                ->where('id = ?')
                ->setParameter(0, $apartmentId)
                ->executeQuery()
                ->fetchAssociative();

            if (!$apartmentQuery) {
                throw new ResourceNotFoundException("Apartment with ID {$apartmentId} not  found.");
            }

            $apartment = new Apartment(
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

            $reviewsQuery = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('apartment_reviews')
                ->where('apartment_id = ?')
                ->setParameter(0, $apartmentId)
                ->orderBy('created_at', 'desc')
                ->executeQuery()
                ->fetchAllAssociative();

            $reviews = [];
            foreach ($reviewsQuery as $reviewData) {
                $reviews [] = new Review(
                    $reviewData['apartment_id'],
                    $reviewData['created_at'],
                    $reviewData['author'],
                    $reviewData['author_id'],
                    $reviewData['text'],
                    $reviewData['rating'],
                    $reviewData['id']
                );
            }

            $numberOfReviews = count($reviews);
            $inputReview = $_SESSION["review"];
            unset($_SESSION["review"]);

            $emptyRating = "";
            if (isset($_SESSION['emptyRating'])) {
                $emptyRating = $_SESSION['emptyRating'];
                unset($_SESSION["emptyRating"]);
            }

            $errorInRating = "";
            if (isset($_SESSION['errorInRating'])) {
                $errorInRating = $_SESSION['errorInRating'];
                unset($_SESSION["errorInRating"]);
            }

            $active = $_SESSION["fullName"];
            $activeId = (int)$_SESSION["id"];
            return new View('Apartments/show', [
                'apartment' => $apartment,
                'reviews' => $reviews,
                'numberOfReviews' => $numberOfReviews,
                'review' => $inputReview,
                'inputReview' => $inputReview,
                'emptyRating' => $emptyRating,
                'errorInRating' => $errorInRating,
                'active' => $active,
                'activeId' => $activeId
            ]);

        } catch (ResourceNotFoundException $exception) {
            $message = $exception->getMessage();
            return new View('404', [
                'message' => $message
            ]);
        }

    }

    public function create(array $vars): View
    {
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];

        $name = $_SESSION["name"];
        $address = $_SESSION["address"];
        $description = $_SESSION["description"];
        unset($_SESSION["name"]);
        unset($_SESSION["address"]);
        unset($_SESSION["description"]);

        $invalidFromDate = "";
        if (isset($_SESSION["invalidFromDate"])) {
            $invalidFromDate = $_SESSION["invalidFromDate"];
            unset($_SESSION["invalidFromDate"]);
        }

        $invalidDates = "";
        if (isset($_SESSION["invalidDates"])) {
            $invalidDates = $_SESSION["invalidDates"];
            unset($_SESSION["invalidDates"]);
        }

        return new View('Apartments/create', [
            'active' => $active,
            'id' => $activeId,
            'invalidFromDate' => $invalidFromDate,
            'invalidDates' => $invalidDates,
            'name' => $name,
            'address' => $address,
            'description' => $description
        ]);
    }

    public function store(): Redirect
    {
        $activeId = $_SESSION["id"];
        $availableFrom = $_POST['available_from'];
        $availableTo = $_POST['available_to'];

        //Validation: is availableFrom date grater than or equal to today's date?
        $carbonAvailableFrom = Carbon::parse($availableFrom);
        $carbonAvailableTo = Carbon::parse($availableTo);
        $carbonToday = Carbon::parse(Carbon::now()->toDateString());

        if ($carbonAvailableFrom->lessThan($carbonToday)) {
            $_SESSION["invalidFromDate"] = "Invalid date, 'Available from' date must be after or equal to today's date";
            $_SESSION["name"] = $_POST['name'];
            $_SESSION["address"] = $_POST['address'];
            $_SESSION["description"] = $_POST['description'];
            return new Redirect("/apartments/create");
        }

        //Validation: is availableFrom date before availableTo date?
        if ($carbonAvailableFrom->greaterThan($carbonAvailableTo)) {
            $_SESSION["invalidDates"] = "Invalid dates, 'Available from' date must be before 'Available to' date";
            $_SESSION["name"] = $_POST['name'];
            $_SESSION["address"] = $_POST['address'];
            $_SESSION["description"] = $_POST['description'];
            return new Redirect("/apartments/create");
        }

        $price = (float)str_replace(",", ".", $_POST['price']);

        $request = new CreateApartmentRequest($_POST['name'], $_POST['address'], $_POST['description'], $availableFrom, $availableTo, $activeId, $price, 0);
        $service = new CreateApartmentService();
        $service->execute($request);
//        Database::connection()
//            ->insert('apartments', [
//                'name' => $_POST['name'],
//                'address' => $_POST['address'],
//                'description' => $_POST['description'],
//                'available_from' => $availableFrom,
//                'available_to' => $availableTo,
//                'owner_id' => $activeId,
//                'price' => $price,
//                'rating' => 0,
//            ]);

        unset($_SESSION["name"]);
        unset($_SESSION["address"]);
        unset($_SESSION["description"]);

        return new Redirect('/apartments');
    }

    public function delete(array $vars): Redirect
    {
        $apartmentId = (int)$vars['id'];
        $activeId = $_SESSION["id"];

        $request = new DeleteApartmentRequest($apartmentId, $activeId);
        $service = new DeleteApartmentService();
        $service->execute($request);

//        $apartmentQuery = Database::connection()
//            ->createQueryBuilder()
//            ->select('*')
//            ->from('apartments')
//            ->where('id = ?')
//            ->setParameter(0, $apartmentId)
//            ->executeQuery()
//            ->fetchAssociative();
////        if ($activeId == $apartmentQuery['owner_id']) {
//            Database::connection()
//                ->delete('apartments', ['id' => $apartmentId]);
//            Database::connection()
//                ->delete('apartment_reviews', ['apartment_id' => $apartmentId]);
//            Database::connection()
//                ->delete('apartment_reservations', ['apartment_id' => $apartmentId]);
//        }
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
        $price = (float)str_replace(",", ".", $_POST['price']);

        if ($activeId == $apartmentQuery['owner_id']) {
            Database::connection()
                ->update('apartments', [
                    'name' => $_POST['name'],
                    'address' => $_POST['address'],
                    'description' => $_POST['description'],
                    'price' => $price,
                ], ['id' => $apartmentId]
                );
        }
        return new Redirect('/apartments/' . $apartmentId);
    }

}