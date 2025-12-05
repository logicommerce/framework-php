<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Services\LmsService;
use SDK\Dtos\Accounts\MasterVal;

/**
 * This is the ApproveRegisteredUser class, a macro class for the account view helper.
 * The purpose of this class is to encapsulate the logic that handles the approval of a registered user.
 *
 * @see ApproveRegisteredUser::getViewParameters()
 *
 * @package FWK\ViewHelpers\Account\Macro
 */
class ApproveRegisteredUser {

    public ?MasterVal $registeredUser = null;

    /**
     * Constructor method for RegisteredUserApproveForm.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * Returns all view parameters needed by the macro.
     *
     * @return array
     */
    public function getViewParameters(): array {
        return $this->getProperties();
    }

    private function getAdvcaLicense(): bool {
        return LmsService::getAdvcaLicense();
    }

    /**
     * Returns macro use properties.
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'registeredUser' => $this->registeredUser,
            'advcaLicense' => $this->getAdvcaLicense(),
        ];
    }
}
