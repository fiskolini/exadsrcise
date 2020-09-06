<?php
/**
 * Randomly remove and discard an arbitary element from a generated array.
 * Assuming we have an unordered array with random repeated numbers, we can solve this
 * using one of two ways:
 *  1. Append removed item to a local array - fastest way, do not need to iterate the array;
 *  2. Generate a new ordered array and check natively with array_diff function;
 */

namespace Exadsrcise\Domain\ElementArray;

use Exadsrcise\Domain\Common\Arr;
use function array_push;
use function array_rand;
use function array_diff;
use function range;

class IntegerGenerator
{
    /**
     * @var int $size Size of array
     */
    private int $size;

    /**
     * @var array<int> $removedItems List of removed items
     */
    private array $removedItems = [];

    /**
     * IntegerGenerator constructor.
     *
     * @param int $size Size of array to be generated
     */
    public function __construct(int $size)
    {
        $this->size = $size;
    }

    /**
     * Removed items accessor
     *
     * @return array<int>
     */
    public function getRemovedItems(): array
    {
        return $this->removedItems;
    }

    /**
     * Run logic - generate a random array, randomly remove and discard an element and
     * check the removed items
     *
     * @return array
     */
    public function run(): array
    {
        $arrayList = Arr::generateRandomInteger($this->size);

        // Remove item randomly from array
        $this->removeItemRandomlyOf($arrayList);

        return $arrayList;
    }

    /**
     * @param array $of Array reference to remove item randomly
     */
    private function removeItemRandomlyOf(array &$of)
    {
        // Randomly pick a key to remove from array
        $itemKey = array_rand($of);

        // Append item to remove into local array
        // Just for the sake of the example. Could be removed
        array_push($this->removedItems, $of[$itemKey]);

        // Discard element of the array
        unset($of[$itemKey]);
    }

    /**
     * Find missing numbers using array_diff.
     * array_diff native function return the missing items between both arrays.
     * This code belongs to the option two.
     *
     * @param array $of Array to search
     *
     * @return array
     */
    public function findMissingNumbersNatively(array $of): array
    {
        // construct a new ordered array
        $new_arr = range(1, $this->size);
        // Find the missing elements
        return array_diff($new_arr, $of);
    }
}
