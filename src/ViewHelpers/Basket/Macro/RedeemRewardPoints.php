<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Basket\Basket;
use SDK\Dtos\User\RewardPointsBalance;
use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use FWK\Dtos\User\RewardPointsBalance as FWKRewardPointsBalance;

/**
 * This is the RedeemRewardPoints class, a macro class for the basketViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket reward points.
 *
 * @see RedeemRewardPoints::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class RedeemRewardPoints {

    public ?Basket $basket = null;

    public ?ElementCollection $rewardPoints = null;

    public bool $showSelectableBox = false;

    public string $class = '';

    public bool $quantityPlugin = true;

    public array $maxAvailableRewardPoints = [];

    public bool $showRewardPointsHeader = true;

    /**
     * Constructor method for RewardPoints.
     * 
     * @see RewardPoints
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

        if (is_null($this->basket)) {
            throw new CommerceException("The value of [basket] argument: '" . $this->basket . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        if (is_null($this->rewardPoints)) {
            throw new CommerceException("The value argument 'rewardPoints' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!($this->rewardPoints instanceof ElementCollection)) {
            throw new CommerceException('The value of rewardPoints argument must be a instance of ' . ElementCollection::class . '. ' . ' Instance of ' . get_class($this->rewardPoints) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }

        $toRedeem = [];
        foreach ($this->basket->getRewardPoints() as $rewardPoint) {
            if (!is_null($rewardPoint->getRedeemed())) {
                $toRedeem[$rewardPoint->getPId()] = $rewardPoint->getRedeemed()->getToRedeem();
            }
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
            if (isset($this->maxAvailableRewardPoints[$rewardPoint->getPId()])) {
                $rewardPoint->setMaxAvailable($this->maxAvailableRewardPoints[$rewardPoint->getPId()]);
            } else {
                $rewardPoint->setMaxAvailable($availablePoints);
            }
            $rewardPoint->setAvailable($availablePoints);
            $rewardPoint->setToRedeem(0);
            if (isset($toRedeem[$rewardPoint->getPId()])) {
                $rewardPoint->setToRedeem($toRedeem[$rewardPoint->getPId()]);
            }
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
            'basket' => $this->basket,
            'rewardPoints' => $this->rewardPoints,
            'showSelectableBox' => $this->showSelectableBox,
            'class' => $this->class,
            'quantityPlugin' => $this->quantityPlugin,
            'showRewardPointsHeader' => $this->showRewardPointsHeader,
        ];
    }
}
