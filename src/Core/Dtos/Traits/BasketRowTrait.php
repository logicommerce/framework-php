<?php

namespace FWK\Core\Dtos\Traits;

use SDK\Dtos\Basket\BasketLockedStockTimers;

/**
 * This is the Basket Row Trait
 * 
 * @see BasketRowTrait::getHtmlId()
 * @see BasketRowTrait::setHtmlId()
 * @see BasketRowTrait::getQuantityOutput()
 * @see BasketRowTrait::setQuantityOutput()
 * @see BasketRowTrait::getClassName()
 * @see BasketRowTrait::setClassName()
 * @see BasketRowTrait::getWarningSeverity()
 * @see BasketRowTrait::setWarningSeverity()
 * @see BasketRowTrait::getOffer()
 * @see BasketRowTrait::setOffer()
 * @see BasketRowTrait::getBasketLockedStockTimers()
 * @see BasketRowTrait::setBasketLockedStockTimers()
 * 
 * @package FWK\Core\Dtos\Traits
 */
trait BasketRowTrait {

    protected string $htmlId = '';

    protected string $quantityOutput = '';

    protected string $className = '';

    protected string $warningSeverity = '';

    protected bool $offer = false;

    protected ?BasketLockedStockTimers $lockedStockTimer = null;

    /**
     * Returns the htmlId.
     *
     * @return string
     */
    public function getHtmlId(): string {
        return $this->htmlId;
    }

    /**
     * Set the htmlId.
     * 
     * @param string $htmlId
     *
     */
    public function setHtmlId(string $htmlId): void {
        $this->htmlId = $htmlId;
    }

    /**
     * Returns the quantityOutput.
     *
     * @return string
     */
    public function getQuantityOutput(): string {
        return $this->quantityOutput;
    }

    /**
     * Set the quantityOutput.
     * 
     * @param string $quantityOutput
     *
     */
    public function setQuantityOutput(string $quantityOutput): void {
        $this->quantityOutput = $quantityOutput;
    }

    /**
     * Returns the className.
     *
     * @return string
     */
    public function getClassName(): string {
        return $this->className;
    }

    /**
     * Set the className.
     * 
     * @param string $className
     *
     */
    public function setClassName(string $className): void {
        $this->className = $className;
    }

    /**
     * Returns the warningSeverity.
     *
     * @return string
     */
    public function getWarningSeverity(): string {
        return $this->warningSeverity;
    }

    /**
     * Set the warningSeverity.
     * 
     * @param string $warningSeverity
     *
     */
    public function setWarningSeverity(string $warningSeverity): void {
        $this->warningSeverity = $warningSeverity;
    }

    /**
     * Returns the offer.
     *
     * @return bool
     */
    public function getOffer(): bool {
        return $this->offer;
    }

    /**
     * Set the offer.
     * 
     * @param bool $offer
     *
     */
    public function setOffer(bool $offer): void {
        $this->offer = $offer;
    }

    /**
     * Set the lockedStockTimer.
     * 
     * @param string $hash
     * @param array $lockedStockTimers
     *
     */
    public function setLockedStockTimer(string $hash, array $lockedStockTimers): void {
        foreach ($lockedStockTimers as $lockedStockTimer) {
            foreach ($lockedStockTimer->getBasketProductHashes() as $BasketProductHash) {
                if ($hash == $BasketProductHash) {
                    $this->lockedStockTimer = $lockedStockTimer;
                    break 2;
                }
            }
        }
    }

    /**
     * Get the lockedStockTimer.
     * 
     * @return null|BasketLockedStockTimers $lockedStockTimer
     *
     */
    public function getLockedStockTimer(): ?BasketLockedStockTimers {
        return $this->lockedStockTimer;
    }
}
