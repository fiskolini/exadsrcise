<?php

namespace Exadsrcise\Presentation\FizzBuzz;

use Exadsrcise\Presentation\Contracts\OutputInterface;

class Buzz extends BaseAdapter implements OutputInterface
{
    public function output(): string
    {
        return "Buzz";
    }
}
