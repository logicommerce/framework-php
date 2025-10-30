<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\FiltrableShoppingListRowsTrait;
use FWK\Core\Controllers\Traits\RichShoppingListRows;
use FWK\Enums\Parameters;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Dtos\User\ShoppingList;


/**
 * This is the user Shopping Lists controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\User
 */
class ShoppingListsController extends BaseHtmlController {

    use FiltrableShoppingListRowsTrait, RichShoppingListRows;

    public const SHOPPING_LIST_ROWS = 'shoppingListRows';

    protected int $shopppingListId = 0;

    protected ?ShoppingList $shopppingList = null;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->initFiltrableShoppingListRows(self::getTheme()->getConfiguration()->getShoppingList()->getRowsList());
        parent::__construct($route);
        $this->shopppingListId = $this->getRequestParam(Parameters::ID, false, $this->getSession()->getShoppingList()->getDefaultOneId());
        $shopppingList = array_filter($this->getSession()->getShoppingList()->getShoppingLists()->getItems(), fn ($shopppingList) => $shopppingList->getId() === $this->shopppingListId);
        $this->shopppingList = array_pop($shopppingList);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if ($this->shopppingListId > 0) {
            $this->addGetShoppingListRowsToBatchRequest($requests, self::SHOPPING_LIST_ROWS, $this->shopppingListId);
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        if (is_null($this->getControllerData(self::SHOPPING_LIST_ROWS)->getError())) {
            $this->setDataValue(self::CONTROLLER_ITEM, $this->getRichShoppingListRows($this->getControllerData(self::SHOPPING_LIST_ROWS)));
        } else {
            $this->setDataValue(self::CONTROLLER_ITEM, new ElementCollection());
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
