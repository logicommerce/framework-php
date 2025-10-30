<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;

/**
 * This is the InputImage class. This class represents a form input of type 'image'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputImage extends Input {
    
    use AttributeRequiredTrait;

    public const TYPE = 'image';
}