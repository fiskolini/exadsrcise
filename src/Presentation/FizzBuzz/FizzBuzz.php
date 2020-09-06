<?php

namespace Exadsrcise\Presentation\FizzBuzz;

use Exadsrcise\Presentation\Contracts\OutputInterface;

class FizzBuzz extends BaseAdapter implements OutputInterface
{
    /**
     * @inheritDoc
     */
    public function output(): string
    {
        return "FizzBuzz";
    }
}
