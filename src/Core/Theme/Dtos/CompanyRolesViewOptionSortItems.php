<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'CompanyRolesViewOptionSortItems' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOption::getAvailableTemplates()
 *
 * @see Element
 * 
 * @uses ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class CompanyRolesViewOptionSortItems extends Element {
    use ElementTrait;
    public const ID = 'id';
    public const NAME = 'name';
    private ?ViewOptionSortItem $id = null;
    private ?ViewOptionSortItem $name = null;

    /**
     * This method returns id sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getId(): ?ViewOptionSortItem {
        return $this->id;
    }
    private function setId(array $id): void {
        $this->id = new ViewOptionSortItem($id);
    }

	/**
	 * This method returns name sort configuration.
	 *
	 * @return ViewOptionSortItem|NULL
	 */
	public function getName(): ?ViewOptionSortItem {
		return $this->name;
	}
    private function setName(array $name): void {
        $this->name = new ViewOptionSortItem($name);
    }
}
