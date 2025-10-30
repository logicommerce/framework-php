<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Services\LmsService;
use SDK\Dtos\Accounts\MasterVal;

/**
 * This is the RegisteredUserApproveForm class, a macro class for the accountViewHelper.
 * It encapsulates the logic to render a form used to approve a registered user.
 *
 * @package FWK\ViewHelpers\Account\Macro
 */
class RegisteredUserApproveForm {

    public ?Form $form = null;
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
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

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
            'form' => $this->form,
            'registeredUser' => $this->registeredUser,
            'advcaLicense' => $this->getAdvcaLicense(),
        ];
    }
}
