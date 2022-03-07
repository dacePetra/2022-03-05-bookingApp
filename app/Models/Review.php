<?php

namespace App\Models;

class Review
{
    private int $apartmentId;
    private string $createdAt;
    private string $author;
    private int $authorId;
    private string $text;
    private ?int $id = null;

    public function __construct(int $apartmentId, string $createdAt, string $author, int $authorId, string $text, ?int $id)
    {
        $this->apartmentId = $apartmentId;
        $this->createdAt = $createdAt;
        $this->author = $author;
        $this->authorId = $authorId;
        $this->text = $text;
        $this->id = $id;
    }

    public function getArticleId(): int
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

    public function getId(): ?int
    {
        return $this->id;
    }
}
