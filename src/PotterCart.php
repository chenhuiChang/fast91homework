<?php

namespace App;

class PotterCart
{
    /**
     * @var ICartRepo
     */
    private $cartRepo;

    /**
     * PotterCart constructor.
     * @param ICartRepo $cartRepo
     */
    public function __construct(ICartRepo $cartRepo)
    {
        $this->cartRepo = $cartRepo;
    }

    public function checkOut()
    {
        $books = $this->cartRepo->getBooks();
        $bookStat = [];
        foreach ($books as $id => $amount) {
            $bookStat[$id] = array_fill(0, $amount, $id);
        }
        $maxBookCount = $this->maxBookCount($bookStat);
        $sets = [];
        for ($i = 0; $i < $maxBookCount; $i++) {
            $set = [];
            for ($j = 1; $j < 6; $j++) {
                if (!array_key_exists($j, $bookStat)) {
                    continue;
                }
                if ($book = array_pop($bookStat[$j])) {
                    $set[] = $book;
                }
            }
            $sets[] = $set;
        }

        $countOfSets = array_map(function ($set) {
            return count($set);
        }, $sets);

        $threeSets = array_filter($countOfSets, function ($count) {
            return $count === 3;
        });
        $fiveSets = array_filter($countOfSets, function ($count) {
            return $count === 5;
        });

        while (count($fiveSets) > 0 && count($threeSets) > 0) {
            $sets[max(array_keys($fiveSets))] = range(1, 4);
            $sets[max(array_keys($threeSets))] = range(1, 4);
            array_pop($fiveSets);
            array_pop($threeSets);
        }
        $that = $this;
        $result = array_reduce($sets, function ($carry, $set) use ($that) {
            $numberOfBookType = count($set);
            return $carry + intval(count($set) * 100 * $that->getDiscount($numberOfBookType));
        }, 0);
        return $result;
    }

    /**
     * @param int $numberOfBookType
     * @return float
     */
    public function getDiscount(int $numberOfBookType): float
    {
        $discount = 1.0;
        if ($numberOfBookType === 1) {
            $discount = 1.0;
        }
        if ($numberOfBookType == 2) {
            $discount = 0.95;
        }
        if ($numberOfBookType == 3) {
            $discount = 0.9;
        }
        if ($numberOfBookType == 4) {
            $discount = 0.8;
        }
        if ($numberOfBookType == 5) {
            $discount = 0.75;
        }
        return $discount;
    }

    /**
     * @param array $bookStat
     * @return int|mixed
     */
    public function maxBookCount(array $bookStat)
    {
        $bookCounts = array_map(function ($books) {
            return count($books);
        }, $bookStat);
        if (count($bookCounts) === 0) {
            return 0;
        }
        $maxBookCount = max($bookCounts);
        return $maxBookCount;
    }
}
