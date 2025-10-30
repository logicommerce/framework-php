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
use SDK\Services\Parameters\Groups\User\ShoppingListRowsParametersGroup;

/**
 * This is the filtrable news list trait.
 *
 * @internal This trait has been created to share common code between some controllers (like Blog HomeController, CategoryController,...)
 *          
 * @package FWK\Core\Controllers
 */
trait FiltrableShoppingListRowsTrait {

    protected array $shoppingListRowsFilter = [];

    protected ?ItemList $itemListConfiguration = null;

    protected array $additionalRequestParameters = [];

    protected ?ShoppingListRowsParametersGroup $shoppingListRowsParametersGroup = null;

    /**
     * This method initializes:
     * <ul>
     * <li>The ShoppingListRowsParametersGroup for the SDK communication (into $this->shoppingListRowsParametersGroup)</li>
     * <li>The given configuration theme configuration data (into $this->itemListConfiguration)</li>
     * <li>The given additional request parameters (into $this->additionalRequestParameters)</li>
     * </ul>
     *
     * @param ItemList $itemListConfiguration
     * @param array $additionalRequestParameters
     * @param int $shoppingListId
     *
     * @return void
     */
    protected function initFiltrableShoppingListRows(ItemList $itemListConfiguration, array $additionalRequestParameters = []): void {
        $this->shoppingListRowsParametersGroup = new ShoppingListRowsParametersGroup();
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
        return
            [
                Parameters::PER_PAGE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => $this->itemListConfiguration->getViewOptions()->getPerPage()->getAvailablePaginations()
                ]),
                Parameters::TEMPLATE => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => $this->itemListConfiguration->getViewOptions()->getTemplate()->getAvailableTemplates()
                ]),
                Parameters::Q => new FilterInput([
                    FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                    FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => '/^.{' . $this->itemListConfiguration->getApplicableFilters()->getQ()->getQMinCharacters() . ',}$/'
                ])
            ] +
            FilterInputFactory::getPaginableItemsParameter() +
            FilterInputFactory::getSortableItemsParameters('ShoppingListRowsSort') +
            FilterInputFactory::getIdParameter();
    }

    /**
     * This method adds the get ShoppingListRows request (with the corresponding requestParams) to the given batch request.
     *
     * @param BatchRequests $requests
     * @param string $dataKey
     * @param string $shopppingListId
     *
     * @return void
     */
    protected function addGetShoppingListRowsToBatchRequest(BatchRequests $requests, string $dataKey, int $shopppingListId): void {
        $userRequest = array_merge(
            $this->itemListConfiguration->getDefaultParametersValues(),
            $this->getRequestParams(),
            $this->additionalRequestParameters,
            $this->itemListConfiguration->getRequestParameters()
        );
        $userService = Loader::service(Services::USER);
        $this->shoppingListRowsFilter = $userService->generateParametersGroupFromArray($this->shoppingListRowsParametersGroup, $userRequest);
        $this->shoppingListRowsFilter[Parameters::ID] = $shopppingListId;
        $userService->addGetShoppingListRows($requests, $dataKey, $shopppingListId, $this->shoppingListRowsParametersGroup);
        $this->setDataValue('shoppingListRowsFilter', $this->shoppingListRowsFilter);
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
