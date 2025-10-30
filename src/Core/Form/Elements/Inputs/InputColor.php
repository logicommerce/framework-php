<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;
use FWK\Core\Form\Elements\Input;

/**
 * This is the InputColor class. This class represents a form input of type 'color'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputColor extends Input {

    public const TYPE = 'color';

    use AttributeAutocompleteTrait;
}