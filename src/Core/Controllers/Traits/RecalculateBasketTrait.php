<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;
use SDK\Services\Parameters\Groups\Basket\EditProductParametersGroup;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;
use SDK\Enums\AddProductsMode;
use SDK\Enums\BasketRowType;
use SDK\Services\Parameters\Groups\Basket\AddProductParametersGroup;
use SDK\Services\Parameters\Groups\Basket\AddProductsParametersGroup;
use SDK\Services\Parameters\Groups\Basket\EditBundleParametersGroup;
use SDK\Services\Parameters\Groups\Basket\EditLinkedParametersGroup;
use SDK\Services\Parameters\Groups\Basket\EditVoucherPurchaseParametersGroup;

/**
 * This is the set delivery trait.
 *
 * @see RecalculateBasketTrait::getRecalculateBasketResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait RecalculateBasketTrait {
    use AddItemToBasketTrait;

    private int $countEditedRows = 0;

    private string $editedRowsErrorMessage = '';

    private ?Element $editedRowsErrorResponse = null;

    /**
     * Returns the response data for the set quantity actions.
     * 
     * @return Element|NULL
     */
    protected function getRecalculateBasketResponseData(array $data = []): ?Element {
        if (count($data) === 0) {
            $data = $this->getRequestParam(Parameters::DATA);
        }
        $lastEdited = null;
        if ($data !== null) {
            $gridProducts = [];
            foreach ($data as $hash => $basketRow) {
                $editParameters = FilterInputHandler::getFilterFilterInputs($basketRow, FilterInputFactory::getEditQuantityParameters());
                $type = isset($editParameters[Parameters::TYPE]) ? $editParameters[Parameters::TYPE] : BasketRowType::PRODUCT;
                switch ($type) {
                    case BasketRowType::PRODUCT:
                        if (strpos($hash, "Grid") === 0) {
                            $gridProducts[] = $basketRow;
                        } else {
                            $editProduct = new EditProductParametersGroup();
                            $this->basketService->generateParametersGroupFromArray($editProduct, $editParameters);
                            $lastEdited = $this->basketService->editProduct($hash, $editProduct);
                        }
                        break;
                    case BasketRowType::VOUCHER_PURCHASE:
                        if (strpos($hash, "Grid") === 0) {
                            $gridProducts[] = $basketRow;
                        } else {
                            $editVoucherPurchase = new EditVoucherPurchaseParametersGroup();
                            $this->basketService->generateParametersGroupFromArray($editVoucherPurchase, $editParameters);                            
                            $lastEdited = $this->basketService->editProduct($hash, $editVoucherPurchase);
                        }
                        break;
                    case BasketRowType::LINKED:
                        $editLinked = new EditLinkedParametersGroup();
                        $this->basketService->generateParametersGroupFromArray($editLinked, $editParameters);
                        $lastEdited = $this->basketService->editLinked($hash, $editLinked);
                        break;
                    case BasketRowType::BUNDLE:
                        $editBundle = new EditBundleParametersGroup();
                        $this->basketService->generateParametersGroupFromArray($editBundle, $editParameters);
                        $lastEdited = $this->basketService->editBundle($hash, $editBundle);
                        break;
                }
                if ($lastEdited !== null) {
                    $this->countEditedRows++;
                    if (!is_null($lastEdited->getError())) {
                        $this->editedRowsErrorMessage .= Utils::getErrorLabelValue($lastEdited) . '. ';
                        $this->editedRowsErrorResponse = $lastEdited;
                    }
                }
            }

            if (!empty($gridProducts)) {
                $addProductsParameters = new AddProductsParametersGroup();
                $addProductsParameters->setMode(AddProductsMode::UPDATE);
                $products = [];
                foreach ($gridProducts as $product) {
                    $addProductParametersGroup = new AddProductParametersGroup();
                    $addProductParametersGroup->setMode(AddProductsMode::UPDATE);
                    $productOptionsParameters = [];
                    $productAppliedOptions = [];
                    $this->parseOptions($product[Parameters::OPTIONS], $productOptionsParameters, $productAppliedOptions);
                    $this->basketService->generateParametersGroupFromArray(
                        $addProductParametersGroup,
                        array_merge($product, [Parameters::OPTIONS => $productOptionsParameters])
                    );
                    $products[] = $addProductParametersGroup;
                }
                $this->basketService->generateParametersGroupFromArray($addProductsParameters, [Parameters::PRODUCTS => $products]);
                $lastEdited = $this->basketService->addProducts($addProductsParameters);
                if (!is_null($lastEdited->getError())) {
                    $this->editedRowsErrorMessage .= Utils::getErrorLabelValue($lastEdited) . '. ';
                    $this->editedRowsErrorResponse = $lastEdited;
                } else {
                    $lastEdited = $lastEdited->getBasket();
                }
            }
        }
        return $lastEdited;
    }
}
