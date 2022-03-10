<?php

namespace App\Controllers;

use App\Database;
use App\Redirect;

class ApartmentReviewsController
{
    public function review(array $vars): Redirect
    {
        $apartmentId = (int)$vars['id'];
        $active = $_SESSION["fullName"];
        $activeId = $_SESSION["id"];

        // Review field is "required", no need to check if review is empty

        Database::connection()
            ->insert('apartment_reviews', [
                'apartment_id' => $apartmentId,
                'author' => $active,
                'author_id' => $activeId,
                'text' => $_POST['review']
            ]);
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
        return new Redirect('/apartments/' . $apartmentId);
    }

}