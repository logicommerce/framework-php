<?php

namespace FWK\ViewHelpers\Product;

use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\Catalog\Product\PricesByQuantity;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Enums\PrevisionType;
use SDK\Application;

/**
 * This is the ProductJsonData class.
 * The purpose of this class is to facilitate to Twig the generation of the products's json output.
 *
 * @see ProductJsonData::output()
 *
 * @package FWK\ViewHelpers\Product
 */
class ProductJsonData {

    private const PRICES = 'prices';

    private const ALTERNATIVES = 'alternatives';

    private ?Product $product;

    private array $stocks;

    /**
     * Constructor method.
     * 
     * @param Product $product
     */
    public function __construct(Product $product = null) {
        $this->product = $product;
        if ($product != null) {
            $this->stocks = $this->getStocks();
        }
    }

    /**
     * This method returns an array with the entire data of the product.
     * Keys of the returned array:
     * <ul>
     *      <li>id</li>
     *      <li>sku</li>
     *      <li>name</li>
     *      <li>brandName</li>
     *      <li>availabilityId</li>
     *      <li>options</li>
     *      <li>definition -> Array containing these keys:
     *          <ul>
     *          <li>price</li>
     *          <li>basePrice</li>
     *          <li>retailPrice</li>
     *          <li>productBasePrice</li>
     *          <li>productRetailPrice</li>
     *          <li>alternativePrice</li>
     *          <li>alternativeBasePrice</li>
     *          <li>alternativeRetailPrice</li>
     *          <li>productAlternativeBasePrice</li>
     *          <li>productAlternativeRetailPrice</li>
     *          <li>featured</li>
     *          <li>offer</li>
     *          <li>stockManagement</li>
     *          <li>backorder</li>
     *          <li>minOrderQuantity</li>
     *          <li>maxOrderQuantity</li>
     *          <li>multipleOrderQuantity</li>
     *          <li>multipleActsOver</li>
     *          <li>groupQuantityByOptions</li>
     *          <li>onRequest</li>
     *          <li>onRequestDays</li>
     *          </ul>
     *      </li>
     *      <li>combinations</li>
     *      <li>stocks</li>
     *      <li>priceByQuantity</li>
     *      <li>restrictionsMain</li>
     *      <li>stockPrevisions</li>
     *      <li>backorderPrevisions</li>
     *      <li>stockLocks</li>
     *      <li>mainCategory</li>
     *      <li>mainCategoryName</li>
     * </ul>
     *
     * @return array
     */
    public function output(): array {
        $response = [];
        if ($this->product instanceof Product && $this->product->getId() > 0) {
            $parsedPrice = $this->parsePrice($this->product);
            $prices = $parsedPrice[self::PRICES];
            $pricesAlternative = $parsedPrice[self::ALTERNATIVES];
            $response = [
                'id' => $this->product->getId(),
                'sku' => $this->product->getCodes()->getSku(),
                'name' => $this->product->getLanguage()->getName(),
                'brandName' => $this->product->getBrand() === null ? '' : $this->product->getBrand()->getLanguage()->getName(),
                'availabilityId' => $this->product->getDefinition()->getAvailabilityId(),
                'options' => $this->getOptions(),
                'definition' => [
                    'price' => isset($prices['price']) ? $prices['price'] : null,
                    'basePrice' => isset($prices['basePrice']) ? $prices['basePrice'] : null,
                    'retailPrice' => isset($prices['retailPrice']) ? $prices['retailPrice'] : null,
                    'productBasePrice' => isset($prices['productBasePrice']) ? $prices['productBasePrice'] : null,
                    'productRetailPrice' => isset($prices['productRetailPrice']) ? $prices['productRetailPrice'] : null,
                    'alternativePrice' => isset($pricesAlternative['price']) ? $pricesAlternative['price'] : null,
                    'alternativeBasePrice' => isset($pricesAlternative['basePrice']) ? $pricesAlternative['basePrice'] : null,
                    'alternativeRetailPrice' => isset($pricesAlternative['retailPrice']) ? $pricesAlternative['retailPrice'] : null,
                    'productAlternativeBasePrice' => isset($pricesAlternative['productBasePrice']) ? $pricesAlternative['productBasePrice'] : null,
                    'productAlternativeRetailPrice' => isset($pricesAlternative['productRetailPrice']) ? $pricesAlternative['productRetailPrice'] : null,
                    'featured' => $this->product->getDefinition()->getFeatured() ? 1 : 0,
                    'offer' => $this->product->getDefinition()->getOffer() ? 1 : 0,
                    'stockManagement' => $this->product->getDefinition()->getStockManagement() ? 1 : 0,
                    'backorder' => $this->product->getDefinition()->getBackorder(),
                    'minOrderQuantity' => $this->product->getDefinition()->getMinOrderQuantity(),
                    'maxOrderQuantity' => $this->product->getDefinition()->getMaxOrderQuantity(),
                    'multipleOrderQuantity' => $this->product->getDefinition()->getMultipleOrderQuantity(),
                    'multipleActsOver' => $this->product->getDefinition()->getMultipleActsOver(),
                    'groupQuantityByOptions' => $this->product->getDefinition()->getGroupQuantityByOptions() ? 1 : 0,
                    'onRequest' => $this->product->getDefinition()->getOnRequest(),
                    'onRequestDays' => $this->product->getDefinition()->getOnRequestDays(),
                    'availability' => $this->product->getDefinition()->getAvailability()
                ],
                'combinationData' => $this->product->getCombinationData(),
                'combinations' => $this->getCombinations(),
                'stocks' => $this->stocks['stocks'],
                'priceByQuantity' => $this->getPricesByQuantity(),
                'restrictionsMain' => [],
                'stockPrevisions' => $this->stocks['stockPrevisions'],
                'backorderPrevisions' => $this->stocks['backorderPrevisions'],
                'stockLocks' => [],
                'mainCategory' => $this->product->getMainCategory(),
                'mainCategoryName' => $this->getMainCategoryName(),
                'language' => $this->product->getLanguage(),
            ];
        }
        return $response;
    }

    private function getStocks(): array {
        $localStocks = [
            'stocks' => [],
            'stockPrevisions' => [],
            'backorderPrevisions' => []
        ];
        foreach ($this->product->getCombinations() as $combination) {
            $combinationValues = $combination->getValues();
            asort($combinationValues);
            $combinationValuesKey = "";
            $combinationValueFWKms = count($combinationValues);
            $i = 0;
            foreach ($combinationValues as $values) {
                $combinationValuesKey .= $values->getProductOptionValueId();
                if (++$i !== $combinationValueFWKms) {
                    $combinationValuesKey .= "-";
                }
            }
            foreach ($combination->getStocks() as $stock) {
                $localStocks['stocks']['WH' . $stock->getWarehouseId() . '_' . $combinationValuesKey] = $stock->getUnits();
                foreach ($stock->getPrevisions() as $prevision) {
                    $typeStock = '';
                    if ($prevision->getPrevisionType() === PrevisionType::PREVISION) {
                        $typeStock = 'stockPrevisions';
                    } elseif ($prevision->getPrevisionType() === PrevisionType::RESERVE) {
                        $typeStock = 'backorderPrevisions';
                    }
                    if ($typeStock != '') {
                        $localStocks[$typeStock][] = [
                            'warehousesStructureId' => 'WH' . $stock->getWarehouseId() . '_' . $combinationValuesKey,
                            'stock' => $prevision->getUnits(),
                            'incomingDate' => date_format($prevision->getIncomingDate()->getDateTime(), 'Y-m-d H:i:s')
                        ];
                    }
                }
            }
        }
        return $localStocks;
    }

    private function getPricesByQuantity(): array {
        $response = [];
        $pricesKey = 'getPrices';

        if (ViewHelper::getApplicationTaxesIncluded()) {
            $pricesKey = 'getPricesWithTaxes';
        }

        foreach ($this->product->{$pricesKey}()->getPricesByQuantity() as $priceByQuantity) {
            $response[] = $this->getPriceByQuantity($priceByQuantity);
        }
        foreach ($this->product->getOptions() as $options) {
            foreach ($options->getValues() as $value) {
                foreach ($value->{$pricesKey}()->getPricesByQuantity() as $priceByQuantity) {
                    $response[] = $this->getPriceByQuantity($priceByQuantity, $value->getId());
                }
            }
        }
        return $response;
    }

    private function getPriceByQuantity(PricesByQuantity $priceByQuantity, int $optionValueId = 0): array {
        return [
            'optionValueId' => $optionValueId,
            'basePrice' => $priceByQuantity->getPrices()->getBasePrice(),
            'retailPrice' => $priceByQuantity->getPrices()->getRetailPrice(),
            'from' => $priceByQuantity->getQuantity()
        ];
    }

    private function getMainCategoryName(): string {
        foreach ($this->product->getCategories() as $category) {
            if ($category->getId() == $this->product->getMainCategory()) {
                return $category->getName();
            }
        }
        return '';
    }

    private function getOptions(): object {
        $response = [];
        foreach ($this->product->getOptions() as $option) {
            $response['id' . $option->getId()] = [
                'id' => $option->getId(),
                'priority' => $option->getPriority(),
                'valueType' => $option->getType(),
                'combinable' => $option->getCombinable() ? 1 : 0,
                'required' => $option->getRequired() ? 1 : 0,
                'name' => $option->getLanguage()->getName(),
                'values' => $this->getOptionsValues($option->getValues()),
                'typology' => $option->getTypology(),
                'showAsGrid' => $option->getShowAsGrid() ? 1 : 0,
                'uniquePrice' => $option->getUniquePrice() ? 1 : 0
            ];
        }
        return (object) $response;
    }

    private function getOptionsValues(array $values): object {
        $response = [];
        foreach ($values as $value) {

            $parsedPrice = $this->parsePrice($value);
            $prices = $parsedPrice[self::PRICES];
            $pricesAlternative = $parsedPrice[self::ALTERNATIVES];

            $response['id' . $value->getId()] = [
                'id' => $value->getId(),
                'sku' => '',
                'priority' => $value->getPriority(),
                'basePrice' => isset($prices['basePrice']) ? $prices['basePrice'] : null,
                'retailPrice' => isset($prices['retailPrice']) ? $prices['retailPrice'] : null,
                'alternativeBasePrice' => isset($pricesAlternative['basePrice']) ? $pricesAlternative['basePrice'] : null,
                'alternativeRetailPrice' => isset($pricesAlternative['retailPrice']) ? $pricesAlternative['retailPrice'] : null,
                'smallImage' => is_null($value->getImages()) ? '' : $value->getImages()->getSmallImage(),
                'largeImage' => is_null($value->getImages()) ? '' : $value->getImages()->getLargeImage(),
                'language' => [
                    'shortDescription' => $value->getLanguage()->getShortDescription(),
                    'longDescription' => $value->getLanguage()->getLongDescription()
                ],
                'stockPrevisions' => [],
                'backorderPrevisions' => []
            ];
        }

        return (object) $response;
    }

    private function getCombinations(): object {
        $response = [];
        foreach ($this->product->getCombinations() as $combination) {
            $combinationKey = "PC_";
            $values = $combination->getValues();

            $i = 0;
            $valueFWKms = count($values);
            foreach ($values as $pcv) {
                $combinationKey .= $pcv->getProductOptionValueId();
                if (++$i !== $valueFWKms) {
                    $combinationKey .= "-";
                }
            }
            $response[$combinationKey] = [
                'id' => $combination->getId(),
                'sku' => $combination->getCodes()->getSku(),
                'ean' => $combination->getCodes()->getEan(),
                'productCombinationId' => $combination->getPid(),
                'isbn' => $combination->getCodes()->getIsbn(),
                'jan' => $combination->getCodes()->getJan(),
                'upc' => $combination->getCodes()->getUpc()
            ];
        }

        return (object) $response;
    }

    private function parsePrice($item): array {
        $types = [
            'WithTaxes' => 'WithTaxes',
            'WithOutTaxes' => ''
        ];
        $pricesWithTaxes = [];
        $pricesWithOutTaxes = [];
        foreach ($types as $key => $type) {
            $fncGetPrices = 'getPrices' . $type;
            $itemPrices = $item->$fncGetPrices()->getPrices();
            if ($item instanceof Product) {
                $optPrices = $item->getDefaultOptionsPrices()->$fncGetPrices();
                ${"prices{$key}"} = [
                    'basePrice' => $itemPrices->getBasePrice() + $optPrices->getBasePrice(),
                    'retailPrice' => $itemPrices->getRetailPrice() + $optPrices->getRetailPrice(),
                    'productBasePrice' => $itemPrices->getBasePrice(),
                    'productRetailPrice' => $itemPrices->getRetailPrice()
                ];
            } else {
                ${"prices{$key}"} = [
                    'basePrice' => $itemPrices->getBasePrice(),
                    'retailPrice' => $itemPrices->getRetailPrice()
                ];
            }
            if ($this->product->getDefinition()->getOffer()) {
                ${"prices{$key}"}['price'] = min(${"prices{$key}"}['basePrice'], ${"prices{$key}"}['retailPrice']);
            } else {
                ${"prices{$key}"}['price'] = ${"prices{$key}"}['basePrice'];
            }
        }
        if (ViewHelper::getApplicationTaxesIncluded()) {
            return [
                self::PRICES => $pricesWithTaxes,
                self::ALTERNATIVES => $pricesWithOutTaxes
            ];
        } else {
            return [
                self::PRICES => $pricesWithOutTaxes,
                self::ALTERNATIVES => $pricesWithTaxes
            ];
        }
    }
}
