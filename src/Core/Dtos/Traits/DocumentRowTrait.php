<?php

namespace FWK\Core\Dtos\Traits;

use SDK\Dtos\Documents\Rows\ReturnProcessDocumentRowRMAReason;

/**
 * This is the Basket Row Trait
 * 
 * @see DocumentRowTrait::getHtmlId()
 * @see DocumentRowTrait::setHtmlId()
 * @see DocumentRowTrait::getQuantityOutput()
 * @see DocumentRowTrait::setQuantityOutput()
 * @see DocumentRowTrait::getClassName()
 * @see DocumentRowTrait::setClassName()
 * @see DocumentRowTrait::getWarningSeverity()
 * @see DocumentRowTrait::setWarningSeverity()
 * @see DocumentRowTrait::getOffer()
 * @see DocumentRowTrait::setOffer()
 * @see DocumentRowTrait::getTotalDiscounts()
 * @see DocumentRowTrait::setTotalDiscounts()
 * 
 * @package FWK\Core\Dtos\Traits
 */
trait DocumentRowTrait {

    protected string $htmlId = '';

    protected string $quantityOutput = '';

    protected string $className = '';

    protected string $warningSeverity = '';

    protected bool $offer = false;

    protected float $totalDiscounts = 0.0;

    protected ?ReturnProcessDocumentRowRMAReason $rmaReason = null;

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
     * Returns the totalDiscounts.
     *
     * @return float
     */
    public function getTotalDiscounts(): float {
        return $this->totalDiscounts;
    }

    /**
     * Set the totalDiscounts.
     * 
     * @param float $totalDiscounts
     *
     */
    public function setTotalDiscounts(float $totalDiscounts): void {
        $this->totalDiscounts = $totalDiscounts;
    }

    /**
     * Returns the document row rmaReason.
     *
     * @return ReturnProcessDocumentRowRMAReason|NULL
     */
    public function getRmaReason(): ?ReturnProcessDocumentRowRMAReason {
        return $this->rmaReason;
    }

    protected function setRmaReason(array $rmaReason): void {
        $this->rmaReason = new ReturnProcessDocumentRowRMAReason($rmaReason);
    }
}
