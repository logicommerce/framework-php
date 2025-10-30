<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\User\UserStockAlert;

/**
 * This is the StockAlerts class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's lostPassword.
 *
 * @see StockAlerts::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class StockAlerts {

    public ?ElementCollection $stockAlerts = null;

    public bool $allowRemove = true;

    /**
     * Constructor method for StockAlerts.
     * 
     * @see StockAlerts
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
        if(is_null($this->stockAlerts)) {
            throw new CommerceException("The value argument 'stockAlerts' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!($this->stockAlerts instanceof ElementCollection)) {
            throw new CommerceException('The value of stockAlerts argument must be a instance of ' . ElementCollection::class. '. ' . ' Instance of ' . get_class($this->stockAlerts) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }
        foreach($this->stockAlerts as $stockAlert) {
            if (!($stockAlert instanceof UserStockAlert)) {
                throw new CommerceException('Each element of stockAlerts must be a instance of ' . UserStockAlert::class. '. ' . ' Instance of ' . get_class($stockAlert) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);            
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
            'stockAlerts' => $this->stockAlerts,
            'allowRemove' => $this->allowRemove
        ];
    }
}