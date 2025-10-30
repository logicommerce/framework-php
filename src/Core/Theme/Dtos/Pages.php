<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;
use SDK\Services\Parameters\Groups\AreaCategoriesTreeParametersGroup;

/**
 * This is the 'Pages' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Events::getSetup()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class Pages extends Element {
    use ElementTrait;

    public const PAGE_SUBPAGE_LEVELS = 'pageSubpagesLevel';

    public const SITE_MAP_PARAMETER_GROUP = 'siteMapParametersGroup';

    private int $pageSubpagesLevels = 1;

    private ?AreaCategoriesTreeParametersGroup $siteMapParametersGroup = null;

    /**
     * This method returns the siteMapParametersGroup setup.
     *
     * @return AreaCategoriesTreeParametersGroup|NULL
     */
    public function getSiteMapParametersGroup(): ?AreaCategoriesTreeParametersGroup {
        return $this->siteMapParametersGroup;
    }

    /**
     * This method returns the default levels to apply in controllerItem getPage.
     *
     * @return int
     */
    public function getPageSubpagesLevels(): int {
        return $this->pageSubpagesLevels;
    }
}
