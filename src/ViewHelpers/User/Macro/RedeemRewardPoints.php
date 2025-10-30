<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Dtos\User\RewardPointsBalance as FWKRewardPointsBalance;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\User\RewardPointsBalance;

/**
 * This is the RedeemRewardPoints class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's reward points.
 *
 * @see RedeemRewardPoints::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class RedeemRewardPoints {

    public ?ElementCollection $rewardPoints = null;

    public bool $showPending = true;

    public bool $showDistribution = true;

    /**
     * Constructor method for RedeemRewardPoints.
     * 
     * @see RedeemRewardPoints
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->rewardPoints)) {
            throw new CommerceException("The value argument 'rewardPoints' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!($this->rewardPoints instanceof ElementCollection)) {
            throw new CommerceException('The value of rewardPoints argument must be a instance of ' . ElementCollection::class . '. ' . ' Instance of ' . get_class($this->rewardPoints) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }
        $this->rewardPoints = DtosElementCollection::fillFromParentCollection($this->rewardPoints, FWKRewardPointsBalance::class);

        foreach ($this->rewardPoints as $rewardPoint) {
            $availablePoints = 0;
            if (!($rewardPoint instanceof RewardPointsBalance)) {
                throw new CommerceException('Each element of rewardPoints must be a instance of ' . RewardPointsBalance::class . '. ' . ' Instance of ' . get_class($rewardPoint) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
            foreach ($rewardPoint->getAvailables() as $available) {
                $availablePoints += $available->getValue();
            }
            $rewardPoint->setAvailable($availablePoints);
        }
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'rewardPoints' => $this->rewardPoints,
            'showPending' => $this->showPending,
            'showDistribution' => $this->showDistribution
        ];
    }
}
