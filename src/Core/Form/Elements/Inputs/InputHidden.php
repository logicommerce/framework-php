<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;
use FWK\Core\Form\Elements\Input;

/**
 * This is the InputHidden class. This class represents a form input of type 'hidden'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputHidden extends Input {

    use AttributeAutocompleteTrait;

    public const TYPE = 'hidden';
    
}