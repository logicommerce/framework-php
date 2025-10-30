<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Services\Parameters\Groups\Basket\ProductOptionParametersGroup;
use SDK\Services\Parameters\Groups\Basket\ProductOptionValueParametersGroup;

/**
 * This is the set delivery trait.
 *
 * @see BuidlProductOptionParametersGroupTrait::parseResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait BuidlProductOptionParametersGroupTrait {

    protected function parseOptions(array $options, array &$itemOptionsParameters, array &$itemAppliedOptions) {
        $basketService = Loader::service(Services::BASKET);
        foreach ($options as $option) {
            if (isset($option[Parameters::ID]) && $option[Parameters::ID] !== null) {
                $newOption = new ProductOptionParametersGroup();
                $optionsParameters = FilterInputHandler::getFilterFilterInputs($option, FilterInputFactory::getAddProductOptionParameters());
                $values = [];
                $auxAppliedOptionValues = [];
                foreach ($optionsParameters[Parameters::VALUES] as $value) {
                    $optionValueParameters = FilterInputHandler::getFilterFilterInputs($value, FilterInputFactory::getAddProductOptionValueParameters());
                    $newOptionValue = new ProductOptionValueParametersGroup();
                    $auxAppliedOptionValues2 = $basketService->generateParametersGroupFromArray($newOptionValue, $optionValueParameters);
                    if (!is_null($auxAppliedOptionValues2[Parameters::VALUE]) && strlen($auxAppliedOptionValues2[Parameters::VALUE]) > MAX_LENGTH_APPLIED_PARAMETER_VALUE) {
                        $auxAppliedOptionValues2[Parameters::VALUE] = substr($auxAppliedOptionValues2[Parameters::VALUE], 0, MAX_LENGTH_APPLIED_PARAMETER_VALUE) . '...';
                    }
                    $auxAppliedOptionValues[] = $auxAppliedOptionValues2;
                    $values[] = $newOptionValue;
                }
                $auxAppliedOptions = $basketService->generateParametersGroupFromArray($newOption, array_merge($optionsParameters, [Parameters::VALUES => $values]));
                $auxAppliedOptions[Parameters::VALUES] = $auxAppliedOptionValues;
                if ($basketService->validateParameterGroup($newOption)) {
                    $itemOptionsParameters[] = $newOption;
                    $itemAppliedOptions[] = $auxAppliedOptions;
                }
            }
        }
    }
}
