<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormRegisteredUser' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormSetUser::getFields()
 * @see FormSetUser::getAccountTabPane()
 * @see FormSetUser::getDefaultAccountTabPane()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormRegisteredUser extends Element {
    use ElementTrait;

    public const FIELDS = 'fields';

    public const APPROVE_FIELDS = 'approveFields';

    public const ACCOUNT_TAB_PANE = 'accountTabPane';

    public const DEFAULT_ACCOUNT_TAB_PANE = 'defaultAccountTabPane';

    private ?FormRegisteredUserFields $fields = null;

    private ?FormRegisteredUserApproveFields $approveFields = null;

    private ?FormAccountTabPane $accountTabPane = null;

    private string $defaultAccountTabPane = FormAccountTabPane::INTERNAL;

    /**
     * This method returns the create fields configuration.
     * 
     * @return FormRegisteredUserFields|NULL
     */
    public function getFields(): ?FormRegisteredUserFields {
        return $this->fields;
    }

    private function setFields(array $fields): void {
        $this->fields = new FormRegisteredUserFields($fields);
    }

    /**
     * This method returns the approve fields configuration.
     * 
     * @return FormRegisteredUserApproveFields|NULL
     */
    public function getApproveFields(): ?FormRegisteredUserApproveFields {
        return $this->approveFields;
    }

    private function setApproveFields(array $approveFields): void {
        $this->approveFields = new FormRegisteredUserApproveFields($approveFields);
    }

    /**
     * This method returns the account tab pane configuration.
     * 
     * @return FormAccountTabPane|NULL
     */
    public function getAccountTabPane(): ?FormAccountTabPane {
        return $this->accountTabPane;
    }

    private function setAccountTabPane(array $accountTabPane): void {
        $this->accountTabPane = new FormAccountTabPane($accountTabPane);
    }

    /**
     * This method returns the default account tab pane to show in the form.
     * 
     * @return string
     */
    public function getDefaultAccountTabPane(): string {
        return $this->defaultAccountTabPane;
    }

    private function setDefaultAccountTabPane(string $defaultAccountTabPane): void {
        $this->defaultAccountTabPane = $defaultAccountTabPane;
    }
}
