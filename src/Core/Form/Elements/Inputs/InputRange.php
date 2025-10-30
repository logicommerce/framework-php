<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeStepTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMinTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMaxTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;

/**
 * This is the InputRange class. This class represents a form input of type 'range'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputRange extends Input{

    public const TYPE = 'range';
    
    use AttributeAutocompleteTrait;
    use AttributeStepTrait;
    use AttributeMinTrait;
    use AttributeMaxTrait;
    
    
}