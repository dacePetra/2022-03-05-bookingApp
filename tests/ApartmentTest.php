<?php declare(strict_types=1);

use App\Models\Apartment;
use PHPUnit\Framework\TestCase;

class ApartmentTest extends TestCase
{
    public function testGetId(): void
    {
        $apartment = new Apartment(88, "Green place", "Stone 22, Riga", "Nice place", "15-04-2022", "30-04-2022", 7, 150, 0);
        $this->assertSame(88, $apartment->getId());
    }

    public function testGetName(): void
    {
        $apartment = new Apartment(88, "Green place", "Stone 22, Riga", "Nice place", "15-04-2022", "30-04-2022", 7, 150, 0);
        $this->assertSame("Green place", $apartment->getName());
    }

    public function testGetAddress(): void
    {
        $apartment = new Apartment(88, "Green place", "Stone 22, Riga", "Nice place", "15-04-2022", "30-04-2022", 7, 150, 0);
        $this->assertSame("Stone 22, Riga", $apartment->getAddress());
    }

    public function testGetDescription(): void
    {
        $apartment = new Apartment(88, "Green place", "Stone 22, Riga", "Nice place", "15-04-2022", "30-04-2022", 7, 150, 0);
        $this->assertSame("Nice place", $apartment->getDescription());
    }

    public function testGetAvailableFrom(): void
    {
        $apartment = new Apartment(88, "Green place", "Stone 22, Riga", "Nice place", "15-04-2022", "30-04-2022", 7, 150, 0);
        $this->assertSame("15-04-2022", $apartment->getAvailableFrom());
    }

    public function testGetAvailableTo(): void
    {
        $apartment = new Apartment(88, "Green place", "Stone 22, Riga", "Nice place", "15-04-2022", "30-04-2022", 7, 150, 0);
        $this->assertSame("30-04-2022", $apartment->getAvailableTo());
    }

    public function testGetOwnerId(): void
    {
        $apartment = new Apartment(88, "Green place", "Stone 22, Riga", "Nice place", "15-04-2022", "30-04-2022", 7, 150, 0);
        $this->assertSame(7, $apartment->getOwnerId());
    }

    public function testGetPrice(): void
    {
        $apartment = new Apartment(88, "Green place", "Stone 22, Riga", "Nice place", "15-04-2022", "30-04-2022", 7, 150, 0);
        $this->assertSame(150.0, $apartment->getPrice());
    }

    public function testGetRating(): void
    {
        $apartment = new Apartment(88, "Green place", "Stone 22, Riga", "Nice place", "15-04-2022", "30-04-2022", 7, 150, 0);
        $this->assertSame(0, $apartment->getRating());
    }
}