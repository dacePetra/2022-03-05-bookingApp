<?php

namespace App\Models;

class Reservation
{
    private int $apartmentId;
    private int $userId;
    private string $reservedFrom;
    private string $reservedTo;
    private ?int $id = null;

    public function __construct(int $apartmentId, int $userId, string $reservedFrom, string $reservedTo, ?int $id = null)
    {
        $this->apartmentId = $apartmentId;
        $this->userId = $userId;
        $this->reservedFrom = $reservedFrom;
        $this->reservedTo = $reservedTo;
        $this->id = $id;
    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getReservedFrom(): string
    {
        return $this->reservedFrom;
    }

    public function getReservedTo(): string
    {
        return $this->reservedTo;
    }

    public function getId(): int
    {
        return $this->id;
    }

}