<?php

namespace App\Models;

class Review
{
    private int $apartmentId;
    private string $createdAt;
    private string $author;
    private int $authorId;
    private string $text;
    private int $rating;
    private ?int $id = null;

    public function __construct(int $apartmentId, string $createdAt, string $author, int $authorId, string $text, int $rating, ?int $id)
    {
        $this->apartmentId = $apartmentId;
        $this->createdAt = $createdAt;
        $this->author = $author;
        $this->authorId = $authorId;
        $this->text = $text;
        $this->rating = $rating;
        $this->id = $id;
    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
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

    public function getId(): ?int
    {
        return $this->id;
    }
}
