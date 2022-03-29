<?php

namespace App\Services\Apartment\Create;

use App\Models\Apartment;
use App\Repositories\Apartment\ApartmentRepository;
use App\Repositories\Apartment\MySqlApartmentRepository;

class CreateApartmentService
{
    private ApartmentRepository $apartmentRepository;

    public function __construct()
    {
        $this->apartmentRepository = new MySqlApartmentRepository();
    }

    public function execute(CreateApartmentRequest $request): void
    {
        $article = new Apartment(
            $request->getName(),
            $request->getAddress(),
            $request->getDescription(),
            $request->getAvailableFrom(),
            $request->getAvailableTo(),
            $request->getOwnerId(),
            $request->getPrice(),
            $request->getRating()
        );

        $this->apartmentRepository->save($article);

        // in seve() after inserting in db, get mysql last inserted id, return article with id or just id and then add it here
    }
}