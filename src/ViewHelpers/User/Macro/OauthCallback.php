<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Resources\RoutePaths;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Enums\RouteType;

/**
 * This is the OauthCallback class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic to make a product return.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\User\Macro
 */

class OauthCallback {

    public string $checkoutLoginRedirect = '';

    public string $commonLoginRedirect = '';

    /**
     * Constructor method for OauthCallback.
     *
     * @see OauthCallback
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
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    private function getProperties(): array {
        return [
            'checkoutLoginRedirect' => $this->checkoutLoginRedirect,
            'commonLoginRedirect' => $this->commonLoginRedirect
        ];
    }
}