<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\Form\Form;
use FWK\Core\ViewHelpers\Macros\Basket\Macro\BaseOutput;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the ReturnRequestForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic to make a product return.
 *
 * @see Orders::getViewParameters()
 * 
 * @package FWK\ViewHelpers\User\Macro
 */

class ReturnRequestForm extends BaseOutput {

    public ?ElementCollection $returnProducts = null;

    public ?Form $returnRequestForm = null;

    public ?string $returnPointsContent = null;

    /**
     * Constructor method for ReturnRequestForm.
     *
     * @see ReturnRequestForm
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
    protected function getProperties(): array {
        return parent::getProperties() + [
            'returnProducts' => $this->returnProducts,
            'returnRequestForm' => $this->returnRequestForm,
            'returnPointsContent' => $this->returnPointsContent
        ];
    }

    protected function setFooter(): void {
    }

    protected function setTotalProductDiscounts(): void {
    }

    protected function getRowClassName(&$documentRow): string {
        return '';
    }

    protected function setDisclosure(): void {
    }
}
