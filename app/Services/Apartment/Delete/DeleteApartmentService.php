<?php

namespace App\Services\Apartment\Delete;

use App\Repositories\Apartment\MySqlApartmentRepository;

class DeleteApartmentService
{
    public function execute(DeleteApartmentRequest $request)
    {
        $repository = new MySqlApartmentRepository();
        $repository->delete($request->getApartmentId(), $request->getActiveId());
    }
}