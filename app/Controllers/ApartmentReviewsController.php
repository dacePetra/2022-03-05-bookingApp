<?php

namespace App\Controllers;

use App\Database;
use App\Models\Apartment;
use App\Redirect;

class ApartmentReviewsController
{
    public function review(array $vars): Redirect
    {
        $apartmentId = (int)$vars['id'];
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];

        $rating = 0;
        if (!isset($_POST['star1']) && !isset($_POST['star2']) && !isset($_POST['star3']) && !isset($_POST['star4']) && !isset($_POST['star5'])) {
            $_SESSION["emptyRating"] = "Don't forget to rate apartment!";
            $_SESSION["review"] = $_POST['review'];
            return new Redirect('/apartments/' . $apartmentId);
        }
        if ($_POST['star1'] == "1" && !isset($_POST['star2']) && !isset($_POST['star3']) && !isset($_POST['star4']) && !isset($_POST['star5'])) {
            $rating = (int)$_POST['star1'];

        }
        if (!isset($_POST['star1']) && $_POST['star2'] == "2" && !isset($_POST['star3']) && !isset($_POST['star4']) && !isset($_POST['star5'])) {
            $rating = (int)$_POST['star2'];
        }
        if (!isset($_POST['star1']) && !isset($_POST['star2']) && $_POST['star3'] == "3" && !isset($_POST['star4']) && !isset($_POST['star5'])) {
            $rating = (int)$_POST['star3'];
        }
        if (!isset($_POST['star1']) && !isset($_POST['star2']) && !isset($_POST['star3']) && $_POST['star4'] == "4" && !isset($_POST['star5'])) {
            $rating = (int)$_POST['star4'];
        }
        if (!isset($_POST['star1']) && !isset($_POST['star2']) && !isset($_POST['star3']) && !isset($_POST['star4']) && $_POST['star5'] == "5") {
            $rating = (int)$_POST['star5'];
        }
        if ($rating == 0) {
            $_SESSION["errorInRating"] = "Please rate the apartment!";
            $_SESSION["review"] = $_POST['review'];
            return new Redirect('/apartments/' . $apartmentId);
        }

        // Review field is "required", no need to check if review is empty

        Database::connection()
            ->insert('apartment_reviews', [
                'apartment_id' => $apartmentId,
                'author' => $active,
                'author_id' => $activeId,
                'text' => $_POST['review'],
                'rating' => $rating
            ]);

        $reviewsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('rating')
            ->from('apartment_reviews')
            ->where('apartment_id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAllAssociative();
        $ratings = [];
        foreach($reviewsQuery as $reviewData){
            $ratings [] = (int) $reviewData['rating'];
        }
        $avgRating = ceil(array_sum($ratings)/count($ratings));

        Database::connection()
            ->update('apartments', [
                'rating' => $avgRating
            ], ['id' => $apartmentId]
            );

        return new Redirect('/apartments/' . $apartmentId);
    }

    public function erase(array $vars): Redirect
    {
        $apartmentId = (int)$vars['nr'];
        $reviewId = (int)$vars['id'];
        $activeId = $_SESSION["id"];

        $reviewQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartment_reviews')
            ->where('id = ?')
            ->setParameter(0, $reviewId)
            ->executeQuery()
            ->fetchAssociative();

        if ($activeId == $reviewQuery['author_id']) {
            Database::connection()
                ->delete('apartment_reviews', ['id' => $reviewId]);
        }

        // Update average rating
        $reviewsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('rating')
            ->from('apartment_reviews')
            ->where('apartment_id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAllAssociative();
        $ratings = [];
        foreach($reviewsQuery as $reviewData){
            $ratings [] = (int) $reviewData['rating'];
        }
        $avgRating = ceil(array_sum($ratings)/count($ratings));

        Database::connection()
            ->update('apartments', [
                'rating' => $avgRating
            ], ['id' => $apartmentId]
            );

        return new Redirect('/apartments/' . $apartmentId);
    }

}