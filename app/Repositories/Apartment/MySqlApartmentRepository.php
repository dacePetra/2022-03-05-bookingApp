<?php

namespace App\Repositories\Apartment;

use App\Database;

class MySqlApartmentRepository implements ApartmentRepository
{
    public function delete(int $apartmentId, int $activeId)
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