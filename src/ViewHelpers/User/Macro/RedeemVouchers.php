<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the RedeemVouchers class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's vouchers.
 *
 * @see RedeemVouchers::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class RedeemVouchers {

    public ?ElementCollection $vouchers = null;

    public bool $showCode = true;

    public bool $showAvailableBalance = true;

    public bool $showExpirationDate = true;

    /**
     * Constructor method for RedeemVouchers.
     * 
     * @see RedeemVouchers
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
        if (is_null($this->vouchers)) {
            throw new CommerceException("The value argument 'vouchers' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!($this->vouchers instanceof ElementCollection)) {
            throw new CommerceException('The value of vouchers argument must be a instance of ' . ElementCollection::class . '. ' . ' Instance of ' . get_class($this->vouchers) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
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
            'vouchers' => $this->vouchers,
            'showCode' => $this->showCode,
            'showAvailableBalance' => $this->showAvailableBalance,
            'showExpirationDate' => $this->showExpirationDate,
        ];
    }
}
