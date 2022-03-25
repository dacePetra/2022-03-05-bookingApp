<?php

namespace App\Services\Apartment\Delete;

class DeleteApartmentRequest
{
    private int $apartmentId;
    private int $activeId;

    public function __construct(int $apartmentId, int $activeId)
    {
        $this->apartmentId = $apartmentId;
        $this->activeId = $activeId;
    }

    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }

    public function getActiveId(): int
    {
        return $this->activeId;
    }

}