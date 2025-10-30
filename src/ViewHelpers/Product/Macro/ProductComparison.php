<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Catalog\Product\ProductComparison as ProductProductComparison;

/**
 * This is the ProductComparison class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's rate.
 *
 * @see ProductComparison::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class ProductComparison {

    public ?ProductProductComparison $productComparison = null;

    public ?int $tableItems = 4;

    public ?string $offerImage = null;

    public ?string $featuredImage = null;

    public ?string $discountsImage = null;

    public ?array $productsIdsWithDiscounts = null;

    /**
     * Constructor method for ProductComparison class.
     * 
     * @see ProductComparison
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (!is_null($this->productComparison) && !($this->productComparison instanceof ProductProductComparison)) {
            throw new CommerceException('The value of productComparison argument must be a instance of ' . ProductProductComparison::class . '. ' . ' Instance of ' . get_class($this->productComparison) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'productComparison' => $this->productComparison,
            'tableItems' => $this->tableItems,
            'offerImage' => $this->offerImage,
            'featuredImage' => $this->featuredImage,
            'discountsImage' => $this->discountsImage,
            'customTagsValues' => $this->getCustomTagsData(),
            'productsIdsWithDiscounts' => $this->productsIdsWithDiscounts
        ];
    }


    private function getCustomTagsData(): array {
        $customTagsData = [];
        foreach ($this->productComparison->getCustomTagGroups() as $customTagGroup) {
            $customTagsData = array_merge($customTagsData, $this->getCustomTagValues($customTagGroup->getCustomTags()));
        }
        $customTagsData = array_merge($customTagsData, $this->getCustomTagValues($this->productComparison->getCustomTagsWithoutGroup()));

        return $customTagsData;
    }

    private function getCustomTagValues(array $customTags): array {
        $customTagsValues = [];
        $products = $this->productComparison->getItems();

        foreach ($customTags as $customTag) {
            $data = [
                "id" => $customTag->getId(),
                "name" => '',
                "controlType" => '',
                "values" => [],
            ];

            $productsId = $customTag->getProductIds();
            foreach ($productsId as $prodId) {
                $product = array_filter($products, fn ($prod) => $prod->getId() === $prodId);
                $customTagValueData = [];
                if (!empty($product)) {
                    $product = array_shift($product);
                    $currentProdId = $product->getId();
                    $currentCustomTag = array_filter($product->getCustomTagValues(), fn ($ct) => $ct->getCustomTagId() === $data["id"]);
                    if (!empty($currentCustomTag)) {
                        $currentCustomTag = array_shift($currentCustomTag);
                        $value = $currentCustomTag->getValue();

                        if (!(next($productsId))) {
                            $data['name'] = $currentCustomTag->getName();
                            $data['controlType'] = $currentCustomTag->getControlType();
                        }
                    }

                    $customTagValueData = [
                        "prodId" => $currentProdId,
                        "value" => $value
                    ];
                }
                array_push($data["values"], $customTagValueData);
            }
            array_push($customTagsValues, $data);
        }

        return $customTagsValues;
    }
}
