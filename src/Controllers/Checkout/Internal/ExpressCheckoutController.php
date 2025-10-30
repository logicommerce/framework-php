<?php

namespace FWK\Controllers\Checkout\Internal;

use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Core\Theme\Theme;
use FWK\Enums\Parameters;
use SDK\Enums\RouteType;
use SDK\Services\Parameters\Groups\PluginAccountIdParametersGroup;

/**
 * This is the ExpressCheckoutController class.
 * This class extends BaseJsonController, see this class.
 *
 * @see BaseJsonController
 *
 * @package FWK\Controllers\Checkout\Internal
 */
class ExpressCheckoutController extends BaseJsonController {

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getSetAddressBookParameters();
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
        $pluginAccountId = intval($this->getRequestParam(Parameters::ID, false, 0));
        if ($pluginAccountId > 0) {
            $redirectUrl = '';
            if (in_array($this->getRequestParam(Parameters::ACTION, false, 0), ['login', 'update'])) {
                $pluginAccountIdParametersGroup = new PluginAccountIdParametersGroup();
                $pluginAccountIdParametersGroup->setPluginAccountId($pluginAccountId);
                $response = Loader::service(Services::BASKET)->getExpressCheckoutUrl($pluginAccountIdParametersGroup);
                if (is_null($response->getError())) {
                    $redirectUrl = $response->getUrl();
                }
            } elseif ($this->getRequestParam(Parameters::ACTION, false, 0) == 'logout') {
                $response = Loader::service(Services::BASKET)->logoutExpressCheckout();
                if (is_null($response->getError())) {
                    if (Theme::getInstance()->getConfiguration()->getCommerce()->getUseOneStepCheckout()) {
                        $redirectUrl = RoutePaths::getPath(RouteType::CHECKOUT);
                    } else {
                        $redirectUrl = RoutePaths::getPath(RouteType::CHECKOUT_BASKET);
                    }
                }
            }
            if (!is_null($response->getError())) {
                $this->responseMessageError = Utils::getErrorLabelValue($response);
                return $response;
            }
            return new class($redirectUrl) extends Element {
                private ?string $redirectUrl = null;
                public function __construct(?string $redirectUrl) {
                    $this->redirectUrl = $redirectUrl;
                }
                public function jsonSerialize(): mixed {
                    return [
                        'redirectUrl' => $this->redirectUrl
                    ];
                }
            };
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
