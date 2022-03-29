<?php

namespace App\Services\Review\Save;

class SaveReviewRequest
{
    private int $apartmentId;
    private string $author;
    private int $authorId;
    private string $text;
    private int $rating;

    public function __construct(int $apartmentId, string $author, int $authorId, string $text, int $rating)
    {
        $this->apartmentId = $apartmentId;
        $this->author = $author;
        $this->authorId = $authorId;
        $this->text = $text;
        $this->rating = $rating;
    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

}