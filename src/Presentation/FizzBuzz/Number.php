<?php

namespace Exadsrcise\Presentation\FizzBuzz;

use Exadsrcise\Presentation\Contracts\OutputInterface;

class Number extends BaseAdapter implements OutputInterface
{
    /**
     * @inheritDoc
     */
    public function output(): string
    {
        return $this->number;
    }
}
