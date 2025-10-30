<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use FWK\Services\Traits\ServiceTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Catalog\Discount;
use SDK\Services\DiscountService as DiscountServiceSDK;
use SDK\Services\Parameters\Groups\DiscountsParametersGroup;
use SDK\Dtos\Basket\Basket;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Enums\DiscountType;
use SDK\Services\Parameters\Groups\DiscountSelectableGiftsParametersGroup;

/**
 * This is the DiscountService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the DiscountService extends the SDK\Services\DiscountService.
 *
 * @see DiscountService::getAllDiscountss()
 *
 * @see DiscountService
 *
 * @package FWK\Services
 */
class DiscountService extends DiscountServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::DISCOUNT_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     * Returns all discounts
     *
     * @param DiscountsParametersGroup $params
     *            object with the needed filters to send to the API brands resource
     *
     * @return ElementCollection|NULL
     */
    public function getAllDiscounts(DiscountsParametersGroup $params = null): ?ElementCollection {
        if (is_null($params)) {
            $params = new DiscountsParametersGroup();
        }
        return $this->getAllElementCollectionItems(Discount::class, 'Discounts', $params);
    }

    /**
     * This method returns selectable gift products from basket
     * 
     * @param Basket $basket 
     * @param DiscountSelectableGiftsParametersGroup $params
     * 
     * @return array
     */
    public function getAllSelectableGiftProductsFromBasket(Basket $basket, ?DiscountSelectableGiftsParametersGroup $params = null): array {
        if (is_null($params)) {
            $params = new DiscountSelectableGiftsParametersGroup();
        }
        $selectableGiftProducts = [];
        foreach ($basket->getAppliedDiscounts() as $appliedDiscount) {
            if ($appliedDiscount->getType() === DiscountType::SELECTABLE_GIFT && $appliedDiscount->getMaxGiftUnitsRemaining() > 0) {
                $selectableGift = $this->getAllElementCollectionItems(Product::class, 'DiscountSelectableGiftsIdProducts', $params, $appliedDiscount->getDiscountId());
                if (!empty($selectableGift->getItems())) {
                    $selectableGiftProducts[$appliedDiscount->getDiscountId()] = [];
                    $selectableGiftProducts[$appliedDiscount->getDiscountId()]['items'] = $selectableGift->getItems();
                    $selectableGiftProducts[$appliedDiscount->getDiscountId()]['discount'] = $appliedDiscount;
                }
            }
        }
        return $selectableGiftProducts;
    }
}
