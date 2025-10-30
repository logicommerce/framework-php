<?php

namespace FWK\ViewHelpers\Form;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\ViewHelpers\Form\Macro\LegalCheck;

/**
 * This is the FormViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of a form's view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see ViewHelper
 * 
 * @see UserViewHelper::legalCheckMacro()
 *
 * @package FWK\ViewHelpers\Form
 */
class FormViewHelper extends ViewHelper {
    
    /**
     * This method merges the given arguments, calculates and returns the view parameters for the legalCheck.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>checkboxPosition</li>
     * <li>showCheck</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function legalCheckMacro(array $arguments = []): array {
        $legalCheck = new LegalCheck($arguments);
        return $legalCheck->getViewParameters();
    }
    
}