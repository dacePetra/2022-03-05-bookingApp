<?php

namespace App\Repositories\Review;

use App\Database;
use App\Models\Review;


class MySqlReviewRepository implements ReviewRepository
{
    public function save(Review $review): void
    {
        Database::connection()
            ->insert('apartment_reviews', [
                'apartment_id' => $review->getApartmentId(),
                'author' => $review->getAuthor(),
                'author_id' => $review->getAuthorId(),
                'text' => $review->getText(),
                'rating' => $review->getRating()
            ]);
    }

}