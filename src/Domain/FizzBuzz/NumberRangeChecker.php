<?php

namespace Exadsrcise\Domain\FizzBuzz;

use Exadsrcise\Domain\FizzBuzz\Exceptions\InvalidMaxValueException;
use Exadsrcise\Domain\FizzBuzz\Exceptions\InvalidMinValueException;
use Exadsrcise\Presentation\Contracts\OutputInterface;
use Exadsrcise\Presentation\FizzBuzz\Buzz;
use Exadsrcise\Presentation\FizzBuzz\Fizz;
use Exadsrcise\Presentation\FizzBuzz\FizzBuzz;
use Exadsrcise\Presentation\FizzBuzz\Number;
use Generator;

/**
 * Class FizzBuzz
 */
final class NumberRangeChecker
{
    /**
     * @var int $min Min value permited
     */
    private int $min;

    /**
     * @var int $max Max value permited
     */
    private int $max;

    /**
     * FizzBuzz constructor.
     *
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max)
    {
        if( $min < 0 )
            throw new InvalidMinValueException("Min value cannot be less than 0.");

        if( $max <= $min )
            throw new InvalidMaxValueException("Max value should be greater than min value.");

        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return Generator<OutputInterface>
     */
    public function fetch(): Generator
    {
        for ($x = $this->min; $x <= $this->max; $x++) {

            switch ($x) {

                case $this->isMultipleOf($x, 3):
                    yield new Fizz($x);
                    break;

                case $this->isMultipleOf($x, 5);
                    yield new Buzz($x);
                    break;

                case $this->isMultipleOf($x, 3, 5);
                    yield new FizzBuzz($x);
                    break;

                default:
                    yield new Number($x);
            }
        }
    }

    /**
     * Check if given number is multiple of given needles
     *
     * @param int $number     number to check
     * @param int ...$needles multiple of given
     *
     * @return bool
     */
    private function isMultipleOf(int $number, int ...$needles)
    {
        foreach ($needles as $of)
            if($number % $of === 0)
                return  false;

        return true;
    }
}
