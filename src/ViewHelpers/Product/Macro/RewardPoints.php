<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Catalog\Product\ProductRewardPoints;

/**
 * This is the RewardPoints class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's wishlist button.
 *
 * @see RewardPoints::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class RewardPoints {

    public ?ElementCollection $rewardPoints = null;

    public bool $showHeader = true;

    public bool $showRulesHeader = true;

    public bool $showRulesCondition = true;

    /**
     * Constructor method for RewardPoints class.
     * 
     * @see RewardPoints
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (!is_null($this->rewardPoints) && !($this->rewardPoints instanceof ElementCollection)) {
            throw new CommerceException('The value of rewardPoints argument must be a instance of ' . ElementCollection::class . '. ' . ' Instance of ' . get_class($this->rewardPoints) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }
        foreach ($this->rewardPoints->getItems() as $rewardPoint) {
            if (!($rewardPoint instanceof ProductRewardPoints)) {
                throw new CommerceException('Each element of countries must be a instance of ' . ProductRewardPoints::class . '. ' . ' Instance of ' . get_class($rewardPoint) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }
        return $this->getProperties();
    }

    protected function getProperties(): array {
        return [
            'rewardPoints' => $this->rewardPoints,
            'showHeader' => $this->showHeader,
            'showRulesHeader' => $this->showRulesHeader,
            'showRulesCondition' => $this->showRulesCondition,
        ];
    }
}
