<?php

namespace Exadsrcise\Model;

use Exadsrcise\Domain\Promotion\Contracts\PromotionContract;

class Promotion implements PromotionContract
{
    /**
     * @var string $name Name of current promotion
     */
    private string $name;

    /**
     * @var int $splitPercent Percent of promotion to be shown
     */
    private int $splitPercent;

    /**
     * Promotion constructor.
     *
     * @param string $name
     * @param int    $splitPercent
     */
    public function __construct(string $name, int $splitPercent)
    {
        $this->name = $name;
        $this->splitPercent = $splitPercent;
    }

    /**
     * Name property accessor
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Split percent accessor
     *
     * @return int
     */
    public function getPercent(): int
    {
        return $this->splitPercent;
    }

    /**
     * @inheritDoc
     */
    public function weight(): int
    {
        return $this->splitPercent;
    }
}
