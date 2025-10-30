<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Dtos\WebhookResponse;
use SDK\Services\PluginService;
use SDK\Enums\WebhookResponseType;

/**
 * This is the webhook Controller.
 *
 * @see CustomerController
 *
 * @package FWK\Controllers
 */
class WebhookController extends BaseHtmlController {

    private ?PluginService $pluginService = null;

    public const POST_PARAMETERS = 'postParameters';
    public const GET_PARAMETERS = 'getParameters';

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
    }

    /**
     * This private method sends the batch requests of the controller to the SDK to obtain the data and returns the batch result
     *
     * @return array with the result of the batch request
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    final protected function setControllerBaseData(): void {
        $this->setDataValue(self::POST_PARAMETERS, $_POST);
        $get = $_GET;
        unset($get[URL_ROUTE]);
        $this->setDataValue(self::GET_PARAMETERS, $get);
        $this->setDataValue(self::CONTROLLER_ITEM, $this->getWebhookResponse());
    }

    private function getWebhookResponse(): ?WebhookResponse {
        $this->pluginService = Loader::service(Services::PLUGIN);
        $pluginModule = $this->getPluginModule();
        $webhookResponse = $this->pluginService->webhookProcess($pluginModule);
        $webhookType = $webhookResponse->getType();
        if ($webhookType == WebhookResponseType::JSON) {
            header('Content-Type: application/json');
        }
        return $webhookResponse;
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

    /**
     * This method returns the plugin module.
     */
    protected function getPluginModule(): ?string {
        return $this->getRequestParam(Parameters::PLUGIN_MODULE);
    }
}
