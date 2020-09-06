<?php

namespace Exadsrcise\Presentation\FizzBuzz;

abstract class BaseAdapter
{
    /**
     * @var int $number Given number to adapter
     */
    protected int $number;

    /**
     * BaseAdapter constructor.
     *
     * @param int $number
     */
    public function __construct(int $number)
    {
        $this->number = $number;
    }
}
