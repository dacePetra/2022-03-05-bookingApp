<?php

namespace App\Models;

class Reservation
{
    private int $id;
    private int $apartmentId;
    private int $userId;
    private string $reservedFrom;
    private string $reservedTo;

    public function __construct(int $id, int $apartmentId, int $userId, string $reservedFrom, string $reservedTo)
    {
        $this->id = $id;
        $this->apartmentId = $apartmentId;
        $this->userId = $userId;
        $this->reservedFrom = $reservedFrom;
        $this->reservedTo = $reservedTo;
    }

    public function getId(): int
    {
        return $this->id;
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

}