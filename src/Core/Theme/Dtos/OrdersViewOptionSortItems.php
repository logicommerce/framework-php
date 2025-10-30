<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'OrdersViewOptionSortItems' class, a DTO class for the theme configuration data.
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
class OrdersViewOptionSortItems extends Element {
    use ElementTrait;

    public const ID = 'id';

    public const DATE = 'date';

    public const DOCUMENTNUMBER = 'documentnumber';

    private ?ViewOptionSortItem $id = null;

    private ?ViewOptionSortItem $date = null;

    private ?ViewOptionSortItem $documentnumber = null;

    /**
     * This method returns id sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getId(): ?ViewOptionSortItem {
        return $this->date;
    }

    private function setId(array $id): void {
        $this->id = new ViewOptionSortItem($id);
    }

    /**
     * This method returns date sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getDate(): ?ViewOptionSortItem {
        return $this->date;
    }

    private function setDate(array $date): void {
        $this->date = new ViewOptionSortItem($date);
    }

    /**
     * This method returns documentnumber sort configuration.
     * 
     * @return ViewOptionSortItem|NULL
     */
    public function getDocumentnumber(): ?ViewOptionSortItem {
        return $this->documentnumber;
    }

    private function setDocumentnumber(array $documentnumber): void {
        $this->documentnumber = new ViewOptionSortItem($documentnumber);
    }
}
