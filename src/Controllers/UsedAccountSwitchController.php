<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Core\Form\FormFactory;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Account\RegisteredUsersMeAccountsParametersGroup;

/**
 * This is the Used Account Switch controller class.
 * This class extends BaseHtmlController, see this class.
 *
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\UsedAccountSwitch\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::USED_ACCOUNT_SWITCH
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class UsedAccountSwitchController extends BaseHtmlController {
    public const ACCOUNTS = "accounts";

    public const USED_ACCOUNT_SWITCH_FORM = "usedAccountSwitchForm";

    protected ?RegisteredUsersMeAccountsParametersGroup $registeredUsersMeAccountsParametersGroup;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->registeredUsersMeAccountsParametersGroup = new RegisteredUsersMeAccountsParametersGroup();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        Loader::service(Services::ACCOUNT)->addGetRegisteredUsersMeAccounts($requests, self::ACCOUNTS, $this->registeredUsersMeAccountsParametersGroup);
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
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setControllerBaseData(): void {
        $this->setDataValue(self::CONTROLLER_ITEM, [
            self::ACCOUNTS => $this->getControllerData(self::ACCOUNTS),
            self::USED_ACCOUNT_SWITCH_FORM => FormFactory::getUsedAccountSwitch($this->getControllerData(self::ACCOUNTS), $this->getSession()?->getUser()?->getId())
        ]);
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
}
