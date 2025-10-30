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
use SDK\Services\Parameters\Groups\NewsParametersGroup;

/**
 * This is the filtrable news list trait.
 *
 * @internal This trait has been created to share common code between some controllers (like Blog HomeController, CategoryController,...)
 *          
 * @package FWK\Core\Controllers
 */
trait FiltrableNewsListTrait {

    public const NEWS_FILTER = 'newsFilter';

    protected array $newsFilter = [];

    protected ?ItemList $itemListConfiguration = null;

    protected array $additionalRequestParameters = [];

    protected ?NewsParametersGroup $newsParametersGroup = null;

    /**
     * This method initializes:
     * <ul>
     * <li>The NewsParametersGroup for the SDK communication (into $this->newsParametersGroup)</li>
     * <li>The given configuration theme configuration data (into $this->itemListConfiguration)</li>
     * <li>The given additional request parameters (into $this->additionalRequestParameters)</li>
     * </ul>
     *
     * @param ItemList $itemListConfiguration
     * @param array $additionalRequestParameters
     *
     * @return void
     */
    protected function initFiltrableNewsList(ItemList $itemListConfiguration, array $additionalRequestParameters = []): void {
        $this->newsParametersGroup = new NewsParametersGroup();
        $this->itemListConfiguration = $itemListConfiguration;
        $this->additionalRequestParameters = $additionalRequestParameters;
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
        $result = FilterInputFactory::getNewsListParameters();
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
    protected function initializeFiltrableNewsAppliedParameters(): void {
        $newsRequest = array_merge(
            $this->itemListConfiguration->getDefaultParametersValues(),
            $this->getRequestParams(),
            $this->additionalRequestParameters,
            $this->itemListConfiguration->getRequestParameters()
        );
        $this->newsFilter = Loader::service(Services::NEWS)->generateParametersGroupFromArray($this->newsParametersGroup, $newsRequest);
    }

    /**
     * This method adds the get products request (with the corresponding requestParams) to the given batch request.
     *
     * @param BatchRequests $requests
     * @param string $dataKey
     *
     * @return void
     */
    protected function addGetNewsToBatchRequest(BatchRequests $requests, string $dataKey): void {
        Loader::service(Services::NEWS)->addGetNews($requests, $dataKey, $this->newsParametersGroup);
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     * 
     * @param string filterVariableName. Sets the variable name to save de news filter applied
     * 
     */
    protected function setFiltrableNewsControllerBaseData(string $filterVariableName): void {
        $this->setDataValue($filterVariableName, $this->newsFilter);
    }


    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }
}
