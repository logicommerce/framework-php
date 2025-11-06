<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Theme\Dtos\Traits\LocationFormFieldsTrait;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormCompanyDivision' class, a DTO class for the form configuration data.
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
class FormCompanyDivision extends Element {
    use ElementTrait, LocationFormFieldsTrait;

    public const MASTER_FIELDS = 'masterFields';

    public const INVOICING_FIELDS = 'invoicingFields';

    public const GENERAL_FIELDS = 'generalFields';

    public const ACCOUNT_TAB_PANE = 'accountTabPane';

    public const DEFAULT_ACCOUNT_TAB_PANE = 'defaultAccountTabPane';

    private ?FormCompanyDivisionInvoicingFields $invoicingFields = null;

    private ?FormCompanyDivisionGeneral $generalFields = null;

    private ?FormAccountTabPane $accountTabPane = null;

    private string $defaultAccountTabPane = FormAccountTabPane::INTERNAL;

    /**
     * This method returns the master fields configuration.
     * 
     * @return FormCompanyDivisionInvoicingFields|NULL
     */
    public function getInvoicingFields(): ?FormCompanyDivisionInvoicingFields {
        return $this->invoicingFields;
    }

    private function setInvoicingFields(array $invoicingFields): void {
        $this->invoicingFields = new FormCompanyDivisionInvoicingFields($invoicingFields);
    }

    /**
     * This method returns the general fields configuration.
     * 
     * @return FormCompanyDivisionGeneral|NULL
     */
    public function getGeneralFields(): ?FormCompanyDivisionGeneral {
        return $this->generalFields;
    }

    private function setGeneralFields(array $generalFields): void {
        $this->generalFields = new FormCompanyDivisionGeneral($generalFields);
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
