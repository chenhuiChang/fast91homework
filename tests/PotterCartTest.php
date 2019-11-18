<?php

use App\ICartRepo;
use App\PotterCart;
use PHPUnit\Framework\TestCase;

class PotterCartTest extends TestCase
{
    /**
     * @var PotterCart
     */
    private $potterCart;

    /**
     * @var ICartRepo|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    private $cartRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRepo = Mockery::mock(ICartRepo::class);
        $this->potterCart = new PotterCart($this->cartRepo);
    }

    public function test_no_books()
    {
        $this->givenBooks([]);
        $this->amountShouldBe(0);
    }

    public function givenBooks($books): void
    {
        $this->cartRepo->shouldReceive('getBooks')->andReturn($books);
    }

    public function amountShouldBe($amount): void
    {
        $this->assertEquals($amount, $this->potterCart->checkOut());
    }

    public function test_1_books()
    {
        $this->givenBooks(['1' => 1]);
        $this->amountShouldBe(100);
    }

    public function test_2_different_books_in_1_set()
    {
        $this->givenBooks(['1' => 1, '2' => 1]);
        $this->amountShouldBe(190);
    }

    public function test_3_different_books_in_1_set()
    {
        $this->givenBooks(['1' => 1, '2' => 1, '3' => 1]);
        $this->amountShouldBe(270);
    }

    public function test_4_different_books_in_1_set()
    {
        $this->givenBooks(['1' => 1, '2' => 1, '3' => 1, '4' => 1]);
        $this->amountShouldBe(320);
    }

    public function test_5_different_books_in_1_set()
    {
        $this->givenBooks(['1' => 1, '2' => 1, '3' => 1, '4' => 1, '5' => 1]);
        $this->amountShouldBe(375);
    }

    public function test_2_same_books()
    {
        $this->givenBooks(['1' => 2]);
        $this->amountShouldBe(200);
    }

    public function test_3_same_books()
    {
        $this->givenBooks(['1' => 3]);
        $this->amountShouldBe(300);
    }

    public function test_2_sets_books()
    {
        $this->givenBooks(['1' => 2, '2' => 1, '3' => 1, '4' => 1, '5' => 1]);
        $this->amountShouldBe(475);
    }

    public function test_2_sets_books_with_4_books()
    {
        $this->givenBooks(['1' => 2, '2' => 2, '3' => 2, '4' => 1, '5' => 1]);
        // 1,2,3,4,5|1,2,3 : 645
        // 1,2,3,4|1,2,3,5 : 640
        $this->amountShouldBe(640);
    }

    public function test_performance()
    {
        $this->givenBooks(['1' => 100004, '2' => 4, '3' => 4, '4' => 2, '5' => 2]);
        $this->amountShouldBe(10001280);
    }

}
