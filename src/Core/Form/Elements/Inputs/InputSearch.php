<?php

namespace FWK\Core\Form\Elements\Inputs;

use FWK\Core\Form\Elements\AttributeTraits\AttributeSizeTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributePatternTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributePlaceholderTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeRequiredTrait;
use FWK\Core\Form\Elements\Input;
use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;

/**
 * This is the InputSearch class. This class represents a form input of type 'search'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputSearch extends Input{

    public const TYPE = 'search';

    use AttributeSizeTrait;
    use AttributePatternTrait;
    use AttributePlaceholderTrait;
    use AttributeRequiredTrait;
    use AttributeAutocompleteTrait;
    
}