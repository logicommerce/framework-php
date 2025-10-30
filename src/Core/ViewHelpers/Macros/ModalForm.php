<?php

namespace FWK\Core\ViewHelpers\Macros;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;

/**
 * This is the ModalForm class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buttonClearProductsFilter.
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class ModalForm extends Modal {

    public ?Form $form = null;

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
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
        return (parent::getProperties() + [
            'form' => $this->form
        ]);
    }
}
