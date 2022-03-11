<?php

namespace App\Models;

class Apartment
{
    private int $id;
    private string $name;
    private string $address;
    private string $description;
    private string $availableFrom;
    private string $availableTo;
    private int $ownerId;
    private float $price;
    private int $rating;

    public function __construct(int $id, string $name, string $address, string $description, string $availableFrom, string $availableTo, int $ownerId, float $price, int $rating)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->description = $description;
        $this->availableFrom = $availableFrom;
        $this->availableTo = $availableTo;
        $this->ownerId = $ownerId;
        $this->price = $price;
        $this->rating = $rating;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getAvailableFrom(): string
    {
        return $this->availableFrom;
    }

    public function getAvailableTo(): string
    {
        return $this->availableTo;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getRating(): int
    {
        return $this->rating;
    }
}