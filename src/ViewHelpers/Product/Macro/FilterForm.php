<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\Theme\Dtos\ApplicableFilter;
use FWK\Core\Theme\Dtos\ApplicableFilters;
use FWK\Enums\Parameters;
use SDK\Core\Dtos\Filter\Filter as SDKFilter;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Enums\SortableEnum;

/**
 * This is the FilterForm class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's filterForm.
 *
 * @see FilterForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class FilterForm {

    public ?SDKFilter $filters = null;

    public ?ApplicableFilters $applicableFilters = null;

    public array $appliedFilters = [];

    public array $defaultParametersValues = [];

    public bool $autosubmit = false;

    public ?string $filterItemTemplate = null;

    private array $helperFilters = [
        Parameters::CATEGORY_ID_LIST => [],
        Parameters::BRANDS_LIST => [],
        Parameters::FILTER_CUSTOMTAG => [],
        Parameters::FILTER_CUSTOMTAG_GROUP => [],
        Parameters::FILTER_OPTION => [],
        Parameters::PRICE_RANGE => []
    ];

    private array $notEnabledFilters = [];

    /**
     * Constructor method for Filter class.
     *
     * @see Filter
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->filters)) {
            throw new CommerceException("The value of [filters] argument: '" . $this->filters . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (is_null($this->applicableFilters)) {
            throw new CommerceException("The value of [applicableFilters] argument: '" . $this->applicableFilters . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->setFilters();

        $this->helperFilters[Parameters::CATEGORY_ID_LIST] = $this->applyApplicableFilters($this->filters->getCategories(), $this->applicableFilters->getCategoryIdList());
        $this->helperFilters[Parameters::BRANDS_LIST] = $this->applyApplicableFilters($this->filters->getBrands(), $this->applicableFilters->getBrandsList());
        $this->helperFilters[Parameters::FILTER_CUSTOMTAG] = $this->applyApplicableFilters($this->filters->getCustomTags(), $this->applicableFilters->getFilterCustomTag());
        $this->helperFilters[Parameters::FILTER_CUSTOMTAG_GROUP] = $this->applyApplicableFilters($this->filters->getCustomTagGroups(), $this->applicableFilters->getFilterCustomTagGroup());
        $this->helperFilters[Parameters::FILTER_OPTION] = $this->applyApplicableFilters($this->filters->getOptions(), $this->applicableFilters->getFilterOption());
        $this->helperFilters[Parameters::PRICE_RANGE] = $this->filters->getPrices();

        $this->setNotEnabledFilters();

        return $this->getProperties();
    }

    /**
     * Set filters property
     *
     * @return void
     */
    private function setFilters(): void {
        $customTags = [];
        foreach ($this->filters->getCustomTags() as $customTag) {
            $customTags[$customTag->getId()] = $customTag;
        }

        $filters = $this->filters->toArray();
        $customTagGroups = $filters['customTagGroups'];
        foreach ($customTagGroups as &$customTagGroup) {
            $sortedCustomTags = [];
            foreach ($customTagGroup['customTags'] as $customTagId) {
                $sortedCustomTags[] = [
                    'id' => $customTagId,
                    'priority' => $customTags[$customTagId]->getPriority(),
                    'name' => $customTags[$customTagId]->getName(),
                ];
            }
            $priority  = array_column($sortedCustomTags, 'priority');
            $name = array_column($sortedCustomTags, 'name');
            array_multisort($priority, SORT_ASC, $name, SORT_ASC, $sortedCustomTags);
            $customTagGroup['customTags'] = array_column($sortedCustomTags, 'id');
        }
        $filters['customTagGroups'] = $customTagGroups;
        $this->filters = new SDKFilter($filters);
    }

    /**
     * Set notEnabledFilters property
     * If filter customTag is not enabled but filter customTagGroup is enabled fix
     *
     * @return void
     */
    private function setNotEnabledFilters(): void {
        $enabledFilterCustomTagGroup = $this->applicableFilters->getFilterCustomTagGroup()->isEnabled();
        foreach ($this->applicableFilters->toArray() as $key => $configuration) {
            if (!is_null($configuration) && (!$configuration[ApplicableFilter::ENABLED]) || ($key === ApplicableFilters::FILTER_CUSTOMTAG && $enabledFilterCustomTagGroup)) {
                $this->notEnabledFilters[$key] = new ApplicableFilter($configuration);
            }
        }
    }

    /**
     * Return sorted elements by ApplicableFilter
     *
     * @param array $elements
     * @param ApplicableFilter $filter
     *
     * @return array
     */
    private function applyApplicableFilters(array $elements, ApplicableFilter $filter): array {
        $sort = ucFirst(strtolower($filter->getSort()));
        $getSortBy = 'get' . $sort;
        if (!empty($elements) && method_exists($elements[0], $getSortBy)) {
            if ($filter->getOrderBy() == SortableEnum::SORT_DIRECTION_ASC) {
                usort($elements, fn($elementA, $elementB) => ($elementA->$getSortBy() <=> $elementB->$getSortBy()));
            } else {
                usort($elements, fn($elementA, $elementB) => - ($elementA->$getSortBy() <=> $elementB->$getSortBy()));
            }
        }
        return $elements;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'filters' => $this->filters,
            'applicableFilters' => $this->applicableFilters,
            'appliedFilters' => $this->appliedFilters,
            'defaultParametersValues' => $this->defaultParametersValues,
            'autosubmit' => $this->autosubmit,
            'filterItemTemplate' => $this->filterItemTemplate,
            'helperFilters' => $this->helperFilters,
            'notEnabledFilters' => $this->notEnabledFilters,
        ];
    }
}
