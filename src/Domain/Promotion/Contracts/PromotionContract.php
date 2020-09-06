<?php

namespace Exadsrcise\Domain\Promotion\Contracts;

interface PromotionContract
{
    /**
     * Get weight of promotion.
     * Must be an integer value between 0 and 100
     *
     * @return int
     */
    public function weight(): int;
}
