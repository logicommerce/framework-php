<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Session;
use FWK\ViewHelpers\Util\Macro\LcCommerceSession\LcCommerceSession as LcCommerceSessionLcCommerceSession;

/**
 * This is the LcCommerceSession class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's search form.
 *
 * @see LcCommerceSession::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class LcCommerceSession {

    public ?Session $session = null;

    private ?LcCommerceSessionLcCommerceSession $lcCommerceSession = null;

    /**
     * Constructor method for LcCommerceSession
     *
     * @see LcCommerceSession
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
        if (is_null($this->session)) {
            throw new CommerceException("The value of [session] argument is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->lcCommerceSession = new LcCommerceSessionLcCommerceSession($this->session);

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'lcCommerceSession' => $this->lcCommerceSession,
            'session' => $this->session
        ];
    }
}
