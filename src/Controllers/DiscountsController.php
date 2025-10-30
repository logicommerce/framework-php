<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Controllers\FiltrableDiscountListTrait;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\DiscountsParametersGroup;

/**
 * This is the DiscountsController class.
 * This class extends BaseHtmlController, see this class.
 *
 * @controllerData: 
 *  <p>self::CONTROLLER_ITEM: \SDK\Core\Dtos\ElementCollection of \SDK\Dtos\Catalog\Discount</p>
 *  <p>self::DISCOUNTS_FILTER: array with the applied filters</p>
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Discounts\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::DISCOUNTS
 * 
 * @getAllDiscounts: protected bool $getAllDiscounts. Default value true, sets false for get paginated brands
 * 
 * @uses FiltrableDiscountListTrait
 *
 * @package FWK\Controllers
 */
class DiscountsController extends BaseHtmlController {
    use FiltrableDiscountListTrait;

    public const DISCOUNTS_FILTER = 'discountsFilter';

    protected ?DiscountsParametersGroup $discountsParametersGroup = null;

    protected array $discountsFilter = [];

    protected bool $getAllDiscounts = false;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->initFiltrableDiscountList(self::getTheme()->getConfiguration()->getDiscounts()->getDiscountsList());
        parent::__construct($route);
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->initializeFiltrableDiscountAppliedParameters();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        if (!$this->getAllDiscounts) {
            $this->addGetDiscountsToBatchRequest($requests, self::CONTROLLER_ITEM, $this->discountsParametersGroup);
        }
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        if ($this->getAllDiscounts) {
            $this->setDataValue(self::CONTROLLER_ITEM, Loader::service(Services::DISCOUNT)->getAllDiscounts($this->discountsParametersGroup));
        }
        $this->setFiltrableDiscountControllerBaseData(self::DISCOUNTS_FILTER);
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
