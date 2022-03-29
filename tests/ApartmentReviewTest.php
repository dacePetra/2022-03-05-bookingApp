<?php declare(strict_types=1);

use App\Models\Review;
use PHPUnit\Framework\TestCase;

class ApartmentReviewTest extends TestCase
{
    public function testGetApartmentId(): void
    {
        $review = new Review(7, "Ron Swanson", 1, "nice", 3, 44, "2022-03-24");
        $this->assertSame(7, $review->getApartmentId());
    }

    public function testGetAuthor(): void
    {
        $review = new Review(7, "Ron Swanson", 1, "nice", 3, 44, "2022-03-24");
        $this->assertSame("Ron Swanson", $review->getAuthor());
    }

    public function testGetAuthorId(): void
    {
        $review = new Review(7, "Ron Swanson", 1, "nice", 3, 44, "2022-03-24");
        $this->assertSame(1, $review->getAuthorId());
    }

    public function testGetText(): void
    {
        $review = new Review(7, "Ron Swanson", 1, "nice", 3, 44, "2022-03-24");
        $this->assertSame("nice", $review->getText());
    }

    public function testGetRating(): void
    {
        $review = new Review(7, "Ron Swanson", 1, "nice", 3, 44, "2022-03-24");
        $this->assertSame(3, $review->getRating());
    }

    public function testGetId(): void
    {
        $review = new Review(7, "Ron Swanson", 1, "nice", 3, 44, "2022-03-24");
        $this->assertSame(44, $review->getId());
    }

    public function testGetCreatedAt(): void
    {
        $review = new Review(7, "Ron Swanson", 1, "nice", 3, 44, "2022-03-24");
        $this->assertSame("2022-03-24", $review->getCreatedAt());
    }
}