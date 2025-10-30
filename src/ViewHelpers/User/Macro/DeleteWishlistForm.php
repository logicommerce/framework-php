<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;

/**
 * This is the DeleteWishlistForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's lostPassword.
 *
 * @see DeleteWishlistForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class DeleteWishlistForm {

    public ?Form $form = null;

    public ?array $products = null;

    /**
     * Constructor method for DeleteWishlistForm.
     * 
     * @see DeleteWishlistForm
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
            'products' => $this->products,
        ];
    }
}