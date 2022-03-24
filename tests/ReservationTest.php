<?php declare(strict_types=1);

use App\Models\Reservation;
use PHPUnit\Framework\TestCase;

//    public function __construct(int $id, int $apartmentId, int $userId, string $reservedFrom, string $reservedTo)
//    {
//        $this->id = $id;
//        $this->apartmentId = $apartmentId;
//        $this->userId = $userId;
//        $this->reservedFrom = $reservedFrom;
//        $this->reservedTo = $reservedTo;
//    }

class ReservationTest extends TestCase
{
    public function testGetId(): void
    {
        $reservation = new Reservation(7, 13, 8, "15-04-2022", "20-04-2022");
        $this->assertSame(7, $reservation->getId());
    }

    public function testGetApartmentId(): void
    {
        $reservation = new Reservation(7, 13, 8, "15-04-2022", "20-04-2022");
        $this->assertSame(13, $reservation->getApartmentId());
    }

    public function testGetUserId(): void
    {
        $reservation = new Reservation(7, 13, 8, "15-04-2022", "20-04-2022");
        $this->assertSame(8, $reservation->getUserId());
    }

    public function testGetReservedFrom(): void
    {
        $reservation = new Reservation(7, 13, 8, "15-04-2022", "20-04-2022");
        $this->assertSame("15-04-2022", $reservation->getReservedFrom());
    }

    public function testGetReservedTo(): void
    {
        $reservation = new Reservation(7, 13, 8, "15-04-2022", "20-04-2022");
        $this->assertSame("20-04-2022", $reservation->getReservedTo());
    }

}