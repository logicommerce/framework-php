<?php

namespace FWK\Controllers\Resources\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Session;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\PluginTriggers;
use SDK\Enums\PluginConnectorType;
use SDK\Services\Parameters\Groups\PluginConnectorTypeParametersGroup;

/**
 * This is the Plugin Execute controller.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 * 
 * Post example
 *  data: 
 *    {
 *      "id":7,
 *      "event":"SELECT_PAYMENT_SYSTEM",
 *      "data":"eventDataBasket"
 *    }
 *
 * Post example
 *  data: 
 *    {
 *      "id":3,
 *      "event":"SELECT_PAYMENT_SYSTEM",
 *      "data":{
 *        "id":1,
 *        "name":"Some User Name",
 *        "email":"some@email.com"
 *      }
 *    }
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Resources\Internal
 */
class PluginExecuteController extends BaseJsonController {

    public const EVENT_DATA_BASKET = 'eventDataBasket';

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getPluginExecute();
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
        return FilterInputHandler::PARAMS_FROM_POST_DATA_OBJECT;
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {

        $id = (int) $this->getRequestParam(Parameters::ID, true);
        $event = $this->getRequestParam(Parameters::EVENT, true);
        $data = $this->getRequestParam(Parameters::DATA, false);
        $response = [];

        if ($data === self::EVENT_DATA_BASKET) {
            $data = Session::getInstance()->getBasket();
        }
        $params = new PluginConnectorTypeParametersGroup();
        $params->setType(PluginConnectorType::NONE);
        $params->setNavigationHash($this->getSession()->getNavigationHash());
        /** @var \SDK\Services\PluginService */
        $pluginService = Loader::service(Services::PLUGIN);
        $plugins = $pluginService->getPlugins($params);
        $plugin = null;
        foreach ($plugins->getItems() as $item) {
            if ($item->getId() === $id) {
                $plugin = $item;
                break;
            }
        }

        if (!is_null($plugin)) {
            $pluginProperties = $pluginService->getPluginProperties($id);
            $interfaces = class_implements(get_class($pluginProperties));
            if (isset($interfaces['SDK\Core\Interfaces\PluginPropertyTriggers'])) {
                $response = PluginTriggers::execute(
                    $event,
                    $plugin,
                    $data
                );
            }
        }

        return new class($response) extends Element {
            public array $response = [];
            public function __construct(array $response) {
                $this->response = $response;
            }
            public function jsonSerialize(): mixed {
                return $this->response;
            }
        };
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

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
