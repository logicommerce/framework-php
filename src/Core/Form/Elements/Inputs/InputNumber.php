<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMinTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMaxTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeStepTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;

/**
 * This is the InputNumber class. This class represents a form input of type 'number'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputNumber extends Input{

    public const TYPE = 'number';
    
    use AttributeMinTrait;
    use AttributeMaxTrait;
    use AttributeStepTrait;
    use AttributeRequiredTrait;
    
}