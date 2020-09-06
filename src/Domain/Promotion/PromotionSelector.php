<?php

namespace Exadsrcise\Domain\Promotion;

use Exadsrcise\Domain\Promotion\Contracts\PromotionContract;
use Exadsrcise\Domain\Promotion\Exceptions\InvalidMinWeightException;
use Exadsrcise\Domain\Promotion\Exceptions\InvalidPromotionInstanceException;
use Exadsrcise\Domain\Promotion\Exceptions\PromotionNotFoundException;
use Exadsrcise\Domain\Promotion\Exceptions\PromotionWeightLessThanZeroException;
use function array_push;
use function gettype;
use function sprintf;

class PromotionSelector
{
    /**
     * @var array<PromotionContract> $promotionList Promotion collection
     */
    private array $promotionList = [];

    /**
     * @var int $listWeightSum Sum of promotion weight
     */
    private int $listWeightSum = 0;

    /**
     * PromotionSelector constructor.
     *
     * @param array $list
     */
    public function __construct(array $list = [])
    {
        $this->pushPromotions($list);
    }

    /**
     * Push new promotion into internal list
     *
     * @param PromotionContract $promotion
     *
     * @return $this
     */
    public function pushPromotion(PromotionContract $promotion): self
    {
        // Push given promotion
        array_push($this->promotionList, $promotion);

        // Update the weight sum (required to calculate selected element)
        $this->listWeightSum += $promotion->weight();

        return $this;
    }

    /**
     * Push multiple promotions into current list
     *
     * @param array $promotions
     *
     * @return $this
     * @throws InvalidPromotionInstanceException
     */
    public function pushPromotions(array $promotions): self
    {
        foreach ($promotions as $promotion) {
            if( ! $promotion instanceof PromotionContract )
                throw new InvalidPromotionInstanceException(
                    sprintf("'%s' is an invalid Promotion instance.", gettype($promotion))
                );

            $this->pushPromotion($promotion);
        }

        return $this;
    }


    /**
     * Pick one promotion based on random weight
     *
     * @param int|null $minimumWeight Minimum weight to check. Must be greater than 0.
     *
     * @return PromotionContract
     *
     * @throws PromotionWeightLessThanZeroException
     * @throws InvalidMinWeightException
     * @throws PromotionNotFoundException
     */
    public function pickOne(int $minimumWeight = 0): PromotionContract
    {
        // Validate sum weight of the promotions list
        if( $this->listWeightSum <= 0 )
            throw new PromotionWeightLessThanZeroException(
                "The sum of promotions is less than 0."
            );

        // Validate minimum weight argument
        if( $minimumWeight < 0 )
            throw new InvalidMinWeightException(
                "The given minimum weight must be greater than 0."
            );

        $rand = mt_rand($minimumWeight, $this->listWeightSum);
        $totalWeight = $minimumWeight;

        /** @var PromotionContract $promotion */
        foreach ($this->promotionList as $promotion) {
            $totalWeight += $promotion->weight();
            if( $totalWeight >= $rand )
                return $promotion;
        }

        throw new PromotionNotFoundException("No promotion could be picked");
    }
}
