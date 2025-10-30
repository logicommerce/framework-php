<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'ViewOptionSortItems' class, a DTO class for the theme configuration data.
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
class ViewOptionSortItems extends Element {
    use ElementTrait;

    public const ID = 'id';

    public const PID = 'pId';

    public const SKU = 'sku';

    public const NAME = 'name';

    public const PRIORITY = 'priority';

    public const PRICE = 'price';

    public const OFFER = 'offer';

    public const FEATURED = 'featured';

    public const DATEADDED = 'dateadded';

    public const PUBLICATIONDATE = 'publicationdate';

    public const DOCUMENTNUMBER = 'documentnumber';

    private ?ViewOptionSortItem $id = null;

    private ?ViewOptionSortItem $pId = null;

    private ?ViewOptionSortItem $sku = null;

    private ?ViewOptionSortItem $name = null;

    private ?ViewOptionSortItem $priority = null;

    private ?ViewOptionSortItem $price = null;

    private ?ViewOptionSortItem $offer = null;

    private ?ViewOptionSortItem $featured = null;

    private ?ViewOptionSortItem $dateadded = null;

    private ?ViewOptionSortItem $publicationdate = null;

    private ?ViewOptionSortItem $documentnumber = null;

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
     * This method returns pId sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getPid(): ?ViewOptionSortItem {
        return $this->pId;
    }

    private function setPid(array $pId): void {
        $this->pId = new ViewOptionSortItem($pId);
    }

    /**
     * This method returns sku sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getSku(): ?ViewOptionSortItem {
        return $this->sku;
    }

    private function setSku(array $sku): void {
        $this->sku = new ViewOptionSortItem($sku);
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

    /**
     * This method returns priority sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getPriority(): ?ViewOptionSortItem {
        return $this->priority;
    }

    private function setPriority(array $priority): void {
        $this->priority = new ViewOptionSortItem($priority);
    }

    /**
     * This method returns price sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getPrice(): ?ViewOptionSortItem {
        return $this->price;
    }

    private function setPrice(array $price): void {
        $this->price = new ViewOptionSortItem($price);
    }

    /**
     * This method returns offer sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getOffer(): ?ViewOptionSortItem {
        return $this->offer;
    }

    private function setOffer(array $offer): void {
        $this->offer = new ViewOptionSortItem($offer);
    }

    /**
     * This method returns featured sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getFeatured(): ?ViewOptionSortItem {
        return $this->featured;
    }

    private function setFeatured(array $featured): void {
        $this->featured = new ViewOptionSortItem($featured);
    }

    /**
     * This method returns dateadded sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getDateadded(): ?ViewOptionSortItem {
        return $this->dateadded;
    }

    private function setDateadded(array $dateadded): void {
        $this->dateadded = new ViewOptionSortItem($dateadded);
    }

    /**
     * This method returns publicationdate sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getPublicationdate(): ?ViewOptionSortItem {
        return $this->publicationdate;
    }

    private function setPublicationdate(array $publicationdate): void {
        $this->publicationdate = new ViewOptionSortItem($publicationdate);
    }

    /**
     * This method returns documentnumber sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getDocumentnumber(): ?ViewOptionSortItem {
        return $this->publicationdate;
    }

    private function setDocumentnumber(array $documentnumber): void {
        $this->documentnumber = new ViewOptionSortItem($documentnumber);
    }
}
