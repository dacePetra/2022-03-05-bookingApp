<?php declare(strict_types=1);

use App\Models\Reservation;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    public function testGetApartmentId(): void
    {
        $reservation = new Reservation(13, 8, "15-04-2022", "20-04-2022", 7);
        $this->assertSame(13, $reservation->getApartmentId());
    }

    public function testGetUserId(): void
    {
        $reservation = new Reservation(13, 8, "15-04-2022", "20-04-2022", 7);
        $this->assertSame(8, $reservation->getUserId());
    }

    public function testGetReservedFrom(): void
    {
        $reservation = new Reservation(13, 8, "15-04-2022", "20-04-2022", 7);
        $this->assertSame("15-04-2022", $reservation->getReservedFrom());
    }

    public function testGetReservedTo(): void
    {
        $reservation = new Reservation(13, 8, "15-04-2022", "20-04-2022", 7);
        $this->assertSame("20-04-2022", $reservation->getReservedTo());
    }

    public function testGetId(): void
    {
        $reservation = new Reservation(13, 8, "15-04-2022", "20-04-2022", 7);
        $this->assertSame(7, $reservation->getId());
    }
}