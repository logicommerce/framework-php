<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'ViewOptionSortItem' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOptionSortItem::getAsc()
 * @see ViewOptionSortItem::getDesc()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */

class ViewOptionSortItem extends ViewOption {
    use ElementTrait;
    
    public const ASC = 'asc';
    
    public const DESC = 'desc';

    public const SORTS = 'sorts';
    
    private bool $asc = false;
    
    private bool $desc = false;

    private array $sorts = [];

    /**
     * This method returns if ascending is enabled. 
     *
     * @return bool
     */
    public function getAsc(): bool {
        return $this->asc;
    }
    
    /**
     * This method returns if descending is enabled.
     *
     * @return bool
     */
    public function getDesc(): bool {
        return $this->desc;
    }

    /**
     * This method returns the applicable sorts.
     *
     * @return array
     */
    public function getSorts(): array {
        return $this->sorts;
    }
}
