<?php

namespace Exadsrcise\Domain\Common;

use function array_map;
use function rand;

class Arr
{
    /**
     * Generates random array with given size
     *
     * @param int $size Size of the array to be generated
     *
     * @return array<int>
     */
    public static function generateRandomInteger(int $size): array
    {
        $array = [];
        for ($x = 1; $x <= $size; $x++) {
            $array[] = rand(1, $size);
        }

        return $array;
    }

    /**
     * Wrap given array with given start and end strings
     *
     * @param array       $array Populated array
     * @param string      $start Start string content
     * @param string|null $end   End string content. If not given, start value will be applied
     *
     * @return array
     */
    public static function wrapElementsWith(array $array, string $start, string $end = null): array
    {
        return array_map(function ($item) use ($start, $end) {
            return $start . $item . ($end ?? $start);
        }, $array);
    }
}
