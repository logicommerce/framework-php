<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Services\LmsService;
use SDK\Dtos\Settings\CountrySettings;
use SDK\Enums\AccountKey;

/**
 * This is the RegisteredUserCreateForm class, a macro class for the accountViewHelper.
 * The purpose of this class is to encapsulate the logic to show customers form from a sales agent.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\Account\Macro
 */
class RegisteredUserCreateForm {

    public const REGISTERED_USER_CREATE = 'registeredUserCreate';

    public const SAVE_COMPANY_DIVISION = 'saveCompanyDivision';

    public ?Form $form = null;

    public string $formPrefix = '';

    public ?CountrySettings $selectedCountry = null;

    public array $selectedCountryLocations = [];

    public string $locationMode = '';

    public string $accountId = AccountKey::USED;


    /**
     * Constructor method for RegisteredUserCreateForm.
     *
     * @see RegisteredUserCreateForm
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    private function getAdvcaLicense(): bool {
        return LmsService::getAdvcaLicense();
    }

    /**
     * This method returns all calculated arguments and new parameters for AccountViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'form' => $this->form,
            'selectedCountry' => $this->selectedCountry,
            'selectedCountryLocations' => $this->selectedCountryLocations,
            'formPrefix' => $this->formPrefix,
            'advcaLicense' => $this->getAdvcaLicense(),
            'locationMode' => $this->locationMode,
            'accountId' => $this->accountId,
        ];
    }
}
