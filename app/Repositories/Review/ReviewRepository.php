<?php

namespace App\Repositories\Review;

use App\Models\Review;


interface ReviewRepository
{
    public function save(Review $review): void;

}