<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'ViewOptionTemplate' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOptionTemplate::getAvailableTemplates()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */

class ViewOptionTemplate extends ViewOption {
    use ElementTrait;
    
    public const AVAILABLE_TEMPLATES = 'availableTemplates';
    
    private array $availableTemplates = [];

    /**
     * This method returns the available templates to show. 
     *
     * @return array
     */
    public function getAvailableTemplates(): array {
        return $this->availableTemplates;
    }
    
}
