<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'ItemList' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ItemList::getViewOptions()
 * @see ItemList::getApplicableFilters() 
 * @see ItemList::getDefaultParametersValues() 
 * @see ItemList::getPagination() 
 * @see ItemList::getRequestParameters()  
 * 
 * @see Element
 * 
 * @uses ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class ItemList extends Element {
    use ElementTrait;

    public const VIEW_OPTIONS = 'viewOptions';

    public const APPLICABLE_FILTERS = 'applicableFilters';

    public const DEFAULT_PARAMETERS_VALUES = 'defaultParametersValues';

    public const PAGINATION = 'pagination';

    public const REQUEST_PARAMETERS = 'requestParameters';

    private ?ViewOptions $viewOptions = null;

    private ?ApplicableFilters $applicableFilters = null;

    private array $defaultParametersValues = [];

    private ?Pagination $pagination = null;

    private int $qMinCharacters = 3;

    private array $requestParameters = [];

    /**
     * This method returns the view options to apply in the views section.
     *
     * @return ViewOptions|NULL
     */
    public function getViewOptions(): ?ViewOptions {
        return $this->viewOptions;
    }

    private function setViewOptions(array $viewOptions): void {
        $this->viewOptions = new ViewOptions($viewOptions);
    }

    /**
     * This method returns the applicable filters
     *
     * @return ApplicableFilters|NULL
     */
    public function getApplicableFilters(): ?ApplicableFilters {
        return $this->applicableFilters;
    }

    private function setApplicableFilters(array $applicableFilters): void {
        $this->applicableFilters = new ApplicableFilters($applicableFilters);
    }

    /**
     * This method returns the default values to apply in the list request.
     *
     * @return array
     */
    public function getDefaultParametersValues(): array {
        return $this->defaultParametersValues;
    }

    private function setDefaultParametersValues(array $defaultParametersValues): void {
        $this->defaultParametersValues = $defaultParametersValues;
    }

    /**
     * This method returns the pagination configuration.
     *
     * @return Pagination|NULL
     */
    public function getPagination(): ?Pagination {
        return $this->pagination;
    }

    private function setPagination(array $pagination): void {
        $this->pagination = new Pagination($pagination);
    }


    /**
     * This method returns the parameters to be applied in the request.
     *
     * @return array
     */
    public function getRequestParameters(): array {
        return $this->requestParameters;
    }

    private function setRequestParameters(array $requestParameters): void {
        $this->requestParameters = $requestParameters;
    }
}
