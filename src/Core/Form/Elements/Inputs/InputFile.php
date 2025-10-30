<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMultipleTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;

/**
 * This is the InputFile class. This class represents a form input of type 'file'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputFile extends Input {

    public const TYPE = 'file';

    use AttributeMultipleTrait;
    use AttributeRequiredTrait;

}