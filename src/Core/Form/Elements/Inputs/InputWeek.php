<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMinTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeMaxTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeStepTrait;
use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;

/**
 * This is the InputWeek class. This class represents a form input of type 'week'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputWeek extends Input {

    public const TYPE = 'week';

    use AttributeRequiredTrait;
    use AttributeAutocompleteTrait;
    use AttributeMinTrait;
    use AttributeMaxTrait;
    use AttributeStepTrait;
}