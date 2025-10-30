<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;
use FWK\Enums\Parameters;
use FWK\Services\LmsService;

/**
 * This is the 'ApplicableFilters' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ApplicableFilters::getCategoryIdList()
 * @see ApplicableFilters::getBrandsList()
 * @see ApplicableFilters::getFilterCustomTag()
 * @see ApplicableFilters::getFilterCustomTagGroup()
 * @see ApplicableFilters::getFilterOption()
 * @see ApplicableFilters::getPriceRange()
 * @see ApplicableFilters::getQ()
 * @see ApplicableFilters::getOnlyOffers()
 * @see ApplicableFilters::getOnlyFeatured()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class ApplicableFilters extends Element {
    use ElementTrait;

    public const CATEGORY_ID_LIST = Parameters::CATEGORY_ID_LIST;

    public const BRANDS_LIST = Parameters::BRANDS_LIST;

    public const FILTER_CUSTOMTAG = Parameters::FILTER_CUSTOMTAG;

    public const FILTER_CUSTOMTAG_GROUP = Parameters::FILTER_CUSTOMTAG_GROUP;

    public const FILTER_OPTION = Parameters::FILTER_OPTION;

    public const PRICE_RANGE = Parameters::PRICE_RANGE;

    public const Q = Parameters::Q;

    public const ONLY_OFFERS = Parameters::ONLY_OFFERS;

    public const ONLY_FEATURED = Parameters::ONLY_FEATURED;

    public const SHOPPING_LIST_ID = Parameters::SHOPPING_LIST_ID;

    private ?ApplicableFilter $categoryIdList = null;

    private ?ApplicableFilter $brandsList = null;

    private ?ApplicableFilter $filterCustomTag = null;

    private ?ApplicableFilter $filterCustomTagGroup = null;

    private ?ApplicableFilter $filterOption = null;

    private ?ApplicableFilter $priceRange = null;

    private ?ApplicableFilter $q = null;

    private ?ApplicableFilter $onlyOffers = null;

    private ?ApplicableFilter $onlyFeatured = null;

    private ?ApplicableFilter $shoppingListId = null;

    /**
     * This method returns the categories filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getCategoryIdList(): ?ApplicableFilter {
        return $this->categoryIdList;
    }

    private function setCategoryIdList(array $categoryIdList): void {
        $this->categoryIdList = new ApplicableFilter($categoryIdList);
    }

    /**
     * This method returns the brands list filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getBrandsList(): ?ApplicableFilter {
        return $this->brandsList;
    }

    private function setBrandsList(array $brandsList): void {
        $this->brandsList = new ApplicableFilter($brandsList);
    }

    /**
     * This method returns the filterCustomTag filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getFilterCustomTag(): ?ApplicableFilter {
        return $this->filterCustomTag;
    }

    private function setFilterCustomTag(array $filterCustomTag): void {
        $this->filterCustomTag = new ApplicableFilter($filterCustomTag);
    }

    /**
     * This method returns the filterCustomTagGroup filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getFilterCustomTagGroup(): ?ApplicableFilter {
        return $this->filterCustomTagGroup;
    }

    private function setFilterCustomTagGroup(array $filterCustomTagGroup): void {
        $this->filterCustomTagGroup = new ApplicableFilter($filterCustomTagGroup);
    }

    /**
     * This method returns the filterOption filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getFilterOption(): ?ApplicableFilter {
        return $this->filterOption;
    }

    private function setFilterOption(array $filterOption): void {
        $this->filterOption = new ApplicableFilter($filterOption);
    }

    /**
     * This method returns the price range filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getPriceRange(): ?ApplicableFilter {
        return $this->priceRange;
    }

    private function setPriceRange(array $priceRange): void {
        $this->priceRange = new ApplicableFilter($priceRange);
    }

    /**
     * This method returns the query filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getQ(): ?ApplicableFilter {
        return $this->q;
    }

    private function setQ(array $q): void {
        $this->q = new ApplicableFilter($q);
    }

    /**
     * This method returns the onlyOffers filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getOnlyOffers(): ?ApplicableFilter {
        return $this->onlyOffers;
    }

    private function setOnlyOffers(array $onlyOffers): void {
        $this->onlyOffers = new ApplicableFilter($onlyOffers);
    }


    /**
     * This method returns the onlyFeatured filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getOnlyFeatured(): ?ApplicableFilter {
        return $this->onlyFeatured;
    }

    private function setOnlyFeatured(array $onlyFeatured): void {
        $this->onlyFeatured = new ApplicableFilter($onlyFeatured);
    }

    /**
     * This method returns the shoppingListId filter configuration.
     *
     * @return ApplicableFilter|NULL
     */
    public function getShoppingListId(): ?ApplicableFilter {
        return $this->shoppingListId;
    }

    private function setShoppingListId(array $shoppingListId): void {
        if (!LmsService::getShoppingListLicense()) {
            $shoppingListId[ApplicableFilter::ENABLED] = false;
        }
        $this->shoppingListId = new ApplicableFilter($shoppingListId);
    }
}
