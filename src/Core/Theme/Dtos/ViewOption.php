<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;

/**
 * This is the 'ViewOption' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOption::getEnabled()
 * @see ViewOption::getShowLabel()
 * @see ViewOption::getPriority()
 *
 * @see Element
 *
 * @package FWK\Core\Theme\Dtos
 */

abstract class ViewOption extends Element {

    public const ENABLED = 'enabled';
    
    public const SHOW_LABEL = 'showLabel';

    public const VIEW_PRIORITY = 'viewPriority';
    
    protected bool $enabled = false;

    protected bool $showLabel = false;
    
    protected int $viewPriority = 0;

    /**
     * This method returns if the view option is enabled. 
     *
     * @return bool
     */
    public function getEnabled(): bool {
        return $this->enabled;
    }
    
    /**
     * This method returns if the head label is enabled. 
     *
     * @return bool
     */
    public function getShowLabel(): bool {
        return $this->showLabel;
    }

    /**
     * This method returns the item view priority.
     *
     * @return int
     */
    public function getViewPriority(): int {
        return $this->viewPriority;
    }
    
}
