<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'ViewOptionPerPage' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOptionPerPage::getAvailablePaginations()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */

class ViewOptionPerPage extends ViewOption {
    use ElementTrait;

    public const AVAILABLE_PAGINATIONS = 'availablePaginations';
    
    private array $availablePaginations = [];

    /**
     * This method returns the available paginations to show. 
     *
     * @return array
     */
    public function getAvailablePaginations(): array {
        return $this->availablePaginations;
    }
    
}
