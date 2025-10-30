<?php

namespace FWK\Dtos\User;

use SDK\Dtos\User\RewardPointsBalance as SDKRewardPointsBalance;

/**
 * This is the RewardPointsBalance class
 *
 * @see RewardPointsBalance::getAvailable()
 * @see RewardPointsBalance::setAvailable()
 *
 * @package FWK\Dtos\User
 */
class RewardPointsBalance extends SDKRewardPointsBalance {

    protected int $available = 0;

    protected int $maxAvailable = 0;

    protected int $toRedeem = 0;

    /**
     * Returns the available.
     *
     * @return int
     */
    public function getAvailable(): int {
        return $this->available;
    }

    /**
     * Set the available.
     * 
     * @param int $available
     *
     */
    public function setAvailable(int $available): void {
        $this->available = $available;
    }

    /**
     * Returns the maxAvailable.
     *
     * @return int
     */
    public function getMaxAvailable(): int {
        return $this->maxAvailable;
    }

    /**
     * Set the maxAvailable.
     * 
     * @param int $maxAvailable
     *
     */
    public function setMaxAvailable(int $maxAvailable): void {
        $this->maxAvailable = $maxAvailable;
    }

    /**
     * Returns the toRedeem.
     *
     * @return int
     */
    public function getToRedeem(): int {
        return $this->toRedeem;
    }

    /**
     * Set the toRedeem.
     * 
     * @param int $toRedeem
     *
     */
    public function setToRedeem(int $toRedeem): void {
        $this->toRedeem = $toRedeem;
    }
}
