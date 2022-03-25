<?php

namespace App\Repositories\Apartment;

interface ApartmentRepository
{
        public function delete(int $apartmentId, int $activeId):void;
}