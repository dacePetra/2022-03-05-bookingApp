<?php

namespace App\Services\Review\Save;

use App\Models\Review;
use App\Repositories\Review\MySqlReviewRepository;
use App\Repositories\Review\ReviewRepository;

class SaveReviewService
{
    private ReviewRepository $reviewRepository;

    public function __construct()
    {
        $this->reviewRepository = new MySqlReviewRepository();
    }

    public function execute(SaveReviewRequest $request): void
    {
        $review = new Review($request->getApartmentId(), $request->getAuthor(), $request->getAuthorId(), $request->getText(), $request->getRating());
        $this->reviewRepository->save($review);
    }

}