<?php

namespace FWK\Dtos\Basket;

use FWK\Core\Dtos\Traits\FillFromParentTrait;
use SDK\Dtos\Basket\BasketWarnings\BasketWarning as SDKBasketWarnings;

/**
 * This is the BasketWarning class
 *
 * @see BasketWarning::getMessage()
 * @see BasketWarning::setMessage()
 * @see BasketWarning::getMessageHash()
 * @see BasketWarning::setMessageHash()
 *
 * @see FillFromParentTrait
 * @see SDK\Dtos\Basket\BasketWarnings\BasketWarning
 * 
 * @package FWK\Dtos\Basket
 */
class BasketWarning extends SDKBasketWarnings{
    use FillFromParentTrait;

    protected string $message = '';

    protected string $messageHash = '';
    
    /**
     * Returns the message
     *
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * Sets the message
     *
     * @param string 
     */
    public function setMessage(string $message): void {
        $this->message = $message;
    }

    /**
     * Returns the message hash
     *
     * @return string
     */
    public function getMessageHash(): string {
        return $this->messageHash;
    }
    /**
     * Returns the message hash
     *
     * @param string $messageHash
     */
    public function setMessageHash(string $messageHash): void {
        $this->messageHash = $messageHash;
    }


}
