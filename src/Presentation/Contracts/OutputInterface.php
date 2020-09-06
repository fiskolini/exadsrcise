<?php

namespace Exadsrcise\Presentation\Contracts;

interface OutputInterface
{
    /**
     * Render the presentation view
     */
    public function output(): string;
}
