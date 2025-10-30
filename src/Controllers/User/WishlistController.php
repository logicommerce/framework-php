<?php

namespace FWK\Controllers\User;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\FiltrableProductListTrait;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\Product\ProductsParametersGroup;
use FWK\Core\Form\FormFactory;
use SDK\Dtos\Common\Route;

/**
 * This is the user wishlist controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\User
 */
class WishlistController extends BaseHtmlController {
    use FiltrableProductListTrait;

    public const FORM_SEND_WISHLIST = 'formSendWishlist';

    public const FORM_DELETE_MULTIPLE_WISHLIST = 'formDeleteMultipleWishlist';

    protected ?ProductsParametersGroup $productsParameters = null;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->initFiltrableProductList(self::CONTROLLER_ITEM, self::getTheme()->getConfiguration()->getWishlist()->getProductList());
        parent::__construct($route);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $wishlist = $this->getSession()->getAggregateData()->getWishlist();
        if (!is_null($wishlist) && count($wishlist->getItemIdList()) > 0) {
            $idList = join(',', $wishlist->getItemIdList());
            $this->productsProductParametersGroup->setIdList($idList);
            $this->setProducts();
        } else {
            $this->setDataValue(self::CONTROLLER_ITEM, null);
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
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $this->setDataValue(self::FORM_SEND_WISHLIST, FormFactory::getSendWishlist());
        $this->setDataValue(self::FORM_DELETE_MULTIPLE_WISHLIST, FormFactory::getDeleteWishlist());
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
