<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Enums\Parameters;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Account\UpdateAccountParametersGroup;
use FWK\Core\FilterInput\FilterInput;

/**
 * This is the move account controller.
 * This controller handles moving an account to a different parent account.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Account\Internal
 */
class MoveAccountController extends BaseJsonController {

    protected bool $loggedInRequired = true;
    protected bool $accountRequired = true;
    protected ?UpdateAccountParametersGroup $updateAccountParametersGroup = null;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->updateAccountParametersGroup = new UpdateAccountParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return [
            Parameters::ACCOUNT_ID => [FilterInput::FILTER_VALIDATE_INT, FilterInput::FLAG_NOT_NEGATIVE],
            'parentAccountId' => [FilterInput::FILTER_VALIDATE_INT, FilterInput::FLAG_NOT_NEGATIVE]
        ];
    }

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getOriginParams() {
        return \FWK\Core\FilterInput\FilterInputHandler::PARAMS_FROM_POST;
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        // No base batch data needed for this operation
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $accountId = $this->getRequestParam(Parameters::ACCOUNT_ID, true);
        $parentAccountId = $this->getRequestParam('parentAccountId', true);

        try {
            // Set the new parent account ID
            $this->updateAccountParametersGroup->setParentAccountId($parentAccountId);

            // Call the account service to update the account
            $result = Loader::service(Services::ACCOUNT)->updateAccount($accountId, $this->updateAccountParametersGroup);

            $this->setDataValue('success', 1);
            $this->setDataValue('message', 'Account moved successfully');
            $this->setDataValue('data', $result);
        } catch (\Exception $e) {
            $this->setDataValue('success', 0);
            $this->setDataValue('message', $e->getMessage());
            $this->setDataValue('errorCode', $e->getCode());
        }
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
        // No additional batch data needed
    }
}
