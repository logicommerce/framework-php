<?php

namespace FWK\Core\Controllers;

use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInput;
use FWK\Enums\Parameters;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Core\Theme\Dtos\ItemList;
use FWK\Services\DiscountService;
use SDK\Services\Parameters\Groups\DiscountsParametersGroup;

/**
 * This is the filtrable discount list trait.
 *          
 * @package FWK\Core\Controllers
 */
trait FiltrableDiscountListTrait {

    protected array $discountsFilter = [];

    protected ?ItemList $itemListConfiguration = null;

    protected array $additionalRequestParameters = [];

    protected ?DiscountsParametersGroup $discountsParametersGroup = null;

    protected ?DiscountService $discountService = null;

    /**
     * This method initializes:
     * <ul>
     * <li>The DiscountsParametersGroup for the SDK communication (into $this->discountsParametersGroup)</li>
     * <li>The given configuration theme configuration data (into $this->itemListConfiguration)</li>
     * <li>The given additional request parameters (into $this->additionalRequestParameters)</li>
     * </ul>
     *
     * @param ItemList $itemListConfiguration
     * @param array $additionalRequestParameters
     *
     * @return void
     */
    protected function initFiltrableDiscountList(ItemList $itemListConfiguration, array $additionalRequestParameters = []): void {
        $this->discountsParametersGroup = new DiscountsParametersGroup();
        $this->itemListConfiguration = $itemListConfiguration;
        $this->additionalRequestParameters = $additionalRequestParameters;
        $this->discountService = Loader::service(Services::DISCOUNT);
    }

    /**
     * This method adds the given additionalRequestParameters.
     *
     * @param array $additionalRequestParameters
     *
     * @return void
     */
    protected function addAdditionalRequestParameters(array $additionalRequestParameters = []): void {
        $this->additionalRequestParameters = $additionalRequestParameters;
    }

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     *
     * @see FilterInputHandler
     */
    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_QUERY_STRING;
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        $result = FilterInputFactory::getDiscountsParameters();
        return [
            Parameters::PER_PAGE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => $this->itemListConfiguration->getViewOptions()->getPerPage()->getAvailablePaginations()
            ]),
            Parameters::TEMPLATE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => $this->itemListConfiguration->getViewOptions()->getTemplate()->getAvailableTemplates()
            ])
        ] + $result;
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeFiltrableDiscountAppliedParameters(): void {
        $discountRequest = array_merge(
            $this->itemListConfiguration->getDefaultParametersValues(),
            $this->getRequestParams(),
            $this->additionalRequestParameters,
            $this->itemListConfiguration->getRequestParameters()
        );
        $this->discountsFilter = $this->discountService->generateParametersGroupFromArray($this->discountsParametersGroup, $discountRequest);
    }

    /**
     * This method adds the get products request (with the corresponding requestParams) to the given batch request.
     *
     * @param BatchRequests $requests
     * @param string $dataKey
     *
     * @return void
     */
    protected function addGetDiscountsToBatchRequest(BatchRequests $requests, string $dataKey): void {
        $this->discountService->addGetDiscounts($requests, $dataKey, $this->discountsParametersGroup);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     * 
     * @param string filterVariableName. Sets the variable name to save de discount filter applied
     * 
     */
    protected function setFiltrableDiscountControllerBaseData(string $filterVariableName): void {
        $this->setDataValue($filterVariableName, $this->discountsFilter);
    }
}
