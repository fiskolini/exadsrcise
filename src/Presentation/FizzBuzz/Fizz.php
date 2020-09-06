<?php

namespace Exadsrcise\Presentation\FizzBuzz;

use Exadsrcise\Presentation\Contracts\OutputInterface;

class Fizz extends BaseAdapter implements OutputInterface
{
    public function output(): string
    {
        return "Fizz";
    }
}
