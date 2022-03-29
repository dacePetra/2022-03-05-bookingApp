<?php declare(strict_types=1);

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetName(): void
    {
        $user = new User("Anna", "Bite", "1980-05-05", "ab@gmail.com", "abc123", 33, "2022-03-24");
        $this->assertSame("Anna", $user->getName());
    }

    public function testGetSurname(): void
    {
        $user = new User("Anna", "Bite", "1980-05-05", "ab@gmail.com", "abc123", 33, "2022-03-24");
        $this->assertSame("Bite", $user->getSurname());
    }

    public function testGetBirthday(): void
    {
        $user = new User("Anna", "Bite", "1980-05-05", "ab@gmail.com", "abc123", 33, "2022-03-24");
        $this->assertSame("1980-05-05", $user->getBirthday());
    }

    public function testGetEmail(): void
    {
        $user = new User("Anna", "Bite", "1980-05-05", "ab@gmail.com", "abc123", 33, "2022-03-24");
        $this->assertSame("ab@gmail.com", $user->getEmail());
    }

    public function testGetPassword(): void
    {
        $user = new User("Anna", "Bite", "1980-05-05", "ab@gmail.com", "abc123", 33, "2022-03-24");
        $this->assertSame("abc123", $user->getPassword());
    }

    public function testGetId(): void
    {
        $user = new User("Anna", "Bite", "1980-05-05", "ab@gmail.com", "abc123", 33, "2022-03-24");
        $this->assertSame(33, $user->getId());
    }

    public function testGetCreatedAt(): void
    {
        $user = new User("Anna", "Bite", "1980-05-05", "ab@gmail.com", "abc123", 33, "2022-03-24");
        $this->assertSame("2022-03-24", $user->getCreatedAt());
    }

}