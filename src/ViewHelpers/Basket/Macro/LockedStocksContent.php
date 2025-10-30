<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Resources\Loader;
use FWK\Core\Theme\Theme;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Enums\Services;
use SDK\Dtos\Basket\BasketLockedStockTimers;

/**
 * This is the LockedStocksContent class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's form.
 *
 * @see LockedStocksContent::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class LockedStocksContent {

    public ?bool $showRenewButton = null;

    public bool $showDescription = true;

    public string $class = '';

    public ?int $expiresAtExtendMinutes = null;

    public bool $expiresAtExtendMinutesUponUserRequest = true;

    public bool $popup = false;

    public bool $expired = false;

    public ?BasketLockedStockTimers $lockedStockTimer = null;

    private bool $actived = false;

    /**
     * Constructor method for LockedStocksContent class.
     * 
     * @see LockedStocksContent
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        $basketStockLockingSettings = Loader::service(Services::SETTINGS)->getBasketStockLockingSettings();
        $this->actived = $basketStockLockingSettings->getActive();

        if (
            (is_null($this->showRenewButton) || $this->showRenewButton)
            && $this->actived && $basketStockLockingSettings->getLockedStockTimerExtendibleByUser()
            && Theme::getInstance()->getConfiguration()->getCommerce()->getLockedStock()->getShowExtendButton()
        ) {
            $this->showRenewButton = true;
        } else {
            $this->showRenewButton = false;
        }

        if (is_null($this->expiresAtExtendMinutes)) {
            $this->expiresAtExtendMinutes = $basketStockLockingSettings->getLockedStockTimerDefaultExtensionMinutes();
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
            'showRenewButton' => $this->showRenewButton,
            'showDescription' => $this->showDescription,
            'class' => $this->class,
            'actived' => $this->actived,
            'expiresAtExtendMinutes' => $this->expiresAtExtendMinutes,
            'expiresAtExtendMinutesUponUserRequest' => $this->expiresAtExtendMinutesUponUserRequest,
            'popup' => $this->popup,
            'expired' => $this->expired,
            'lockedStockTimer' => $this->lockedStockTimer
        ];
    }
}
