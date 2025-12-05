<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Accounts\Account;
use SDK\Enums\AccountKey;

/**
 * This is the EditAccountForm class, a macro class for the account view helper.
 * Encapsulates the logic to render a form used to edit an account.
 *
 * @see EditAccountForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Account\Macro
 */
class EditAccountForm {

    public ?Form $form = null;

    public ?Account $account = null;

    public string $accountName = '';

    public ?ElementCollection $invoicingAddresses = null;

    public ?ElementCollection $shippingAddresses = null;

    /**@deprecated*/
    public string $accountId = AccountKey::USED;

    public array $permissions = [];

    public string $errorMessage = '';

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
            'account' => $this->account,
            'accountName' => $this->accountName,
            'invoicingAddresses' => $this->invoicingAddresses,
            'shippingAddresses' => $this->shippingAddresses,
            'permissions' => $this->permissions,
            'errorMessage' => $this->errorMessage,
        ];
    }
}
