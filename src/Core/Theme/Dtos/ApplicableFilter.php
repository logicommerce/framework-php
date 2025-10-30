<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;
use SDK\Core\Enums\SortableEnum;
use FWK\Enums\ApplicableFilterOrderBy;
use FWK\Enums\ApplicableFilterPriceRangeMode;

/**
 * This is the 'ApplicableFilter' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ApplicableFilter::getQMinCharacters()
 * @see ApplicableFilter::getOrderBy()
 * @see ApplicableFilter::getSort()
 * @see ApplicableFilter::isEnabled()
 * @see ApplicableFilter::getItemsToShow()  
 * @see ApplicableFilter::getItemsToRangeSlider()  
 * @see ApplicableFilter::getPriceRangeMode()  
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class ApplicableFilter extends Element {
    use ElementTrait;

    public const ENABLED = 'enabled';

    public const PRIORITY = 'priority';

    public const ORDER_BY  = 'orderBy';

    public const SORT  = 'sort';

    public const ITEMS_TO_SHOW = 'itemsToShow';

    public const ITEMS_TO_RANGE_SLIDER = 'itemsToRangeSlider';

    public const PRICE_RANGE_MODE = 'priceRangeMode';

    public const Q_MIN_CHARACTERS = 'qMinCharacters';

    private int $qMinCharacters = 0;

    private bool $enabled = false;

    private int $priority = 0;

    private array $itemsToShow = [];

    private array $itemsToRangeSlider = [];

    private string $priceRangeMode = ApplicableFilterPriceRangeMode::MODE_RANGE_SLIDER;

    private string $orderBy = SortableEnum::SORT_DIRECTION_ASC;

    private string $sort = '';

    /**
     * This method returns the minimum number of characters to make the list request.
     *
     * @return int
     */
    public function getQMinCharacters(): int {
        return $this->qMinCharacters;
    }

    private function setQMinCharacters(int $qMinCharacters): void {
        $this->qMinCharacters = $qMinCharacters;
    }

    /**
     * This method returns the 'order by' to be applied to the filtrable items. It retuns values compatibles with SortableEnum.
     *
     * @return string
     */
    public function getOrderBy(): string {
        return SortableEnum::isValid($this->orderBy) ? $this->orderBy : SortableEnum::SORT_DIRECTION_ASC;
    }

    private function setOrderBy(string $orderBy): void {
        $this->orderBy = $orderBy;
    }

    /**
     * This method returns the sort to apply to the filtrable item. Retuns values compatibles with ApplicableFilterSortable
     *
     * @return string
     */
    public function getSort(): string {
        return ApplicableFilterOrderBy::isValid($this->sort) ? $this->sort : '';
    }

    private function setSort(string $sort): void {
        $this->sort = $sort;
    }

    /**
     * This method returns true if the filter is enabled, and false otherwise.
     *
     * @return bool
     */
    public function isEnabled(): bool {
        return $this->enabled;
    }

    private function setEnabled(bool $enabled): void {
        $this->enabled = $enabled;
    }


    /**
     * This method the priority to show the filter
     *
     * @return int
     */
    public function isPriority(): int {
        return $this->priority;
    }

    private function setPriority(int $priority): void {
        $this->priority = $priority;
    }

    /**
     * This method returns the applicable items for the filter.
     *
     * @return array
     */
    public function getItemsToShow(): array {
        return $this->itemsToShow;
    }

    private function setItemsToShow(array $itemsToShow): void {
        $this->itemsToShow = $itemsToShow;
    }

    /**
     * This method returns the applicable range slider items for the filter.
     *
     * @return array
     */
    public function getItemsToRangeSlider(): array {
        return $this->itemsToRangeSlider;
    }

    private function setItemsToRangeSlider(array $itemsToRangeSlider): void {
        $this->itemsToRangeSlider = $itemsToRangeSlider;
    }

    /**
     * This method returns the applicable price range mode for the filter.
     *
     * @return string
     */
    public function getPriceRangeMode(): string {
        return $this->priceRangeMode;
    }

    private function setPriceRangeMode(string $priceRangeMode): void {
        $this->priceRangeMode = $priceRangeMode;
    }
}
