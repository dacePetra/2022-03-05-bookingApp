<?php

namespace App\Repositories\Apartment;

use App\Database;
use App\Models\Apartment;

class MySqlApartmentRepository implements ApartmentRepository
{


    public function save(Apartment $apartment)
    {
        Database::connection()
            ->insert('apartments', [
                'name' => $apartment->getName(),
                'address' => $apartment->getAddress(),
                'description' => $apartment->getDescription(),
                'available_from' => $apartment->getAvailableFrom(),
                'available_to' => $apartment->getAvailableTo(),
                'owner_id' => $apartment->getOwnerId(),
                'price' => $apartment->getPrice(),
                'rating' => $apartment->getRating()
            ]);
    }

    public function delete(int $apartmentId, int $activeId):void
    {
        $apartmentQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('apartments')
            ->where('id = ?')
            ->setParameter(0, $apartmentId)
            ->executeQuery()
            ->fetchAssociative();

        if ($activeId == $apartmentQuery['owner_id']) {
            Database::connection()
                ->delete('apartments', ['id' => $apartmentId]);
            Database::connection()
                ->delete('apartment_reviews', ['apartment_id' => $apartmentId]);
            Database::connection()
                ->delete('apartment_reservations', ['apartment_id' => $apartmentId]);
        }
        // TODO: Implement delete() method.
    }
}