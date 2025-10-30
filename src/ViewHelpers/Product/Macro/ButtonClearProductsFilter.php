<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Theme\Dtos\ApplicableFilters;
use FWK\Enums\Parameters;
use FWK\ViewHelpers\Product\ProductViewHelper;

/**
 * This is the ButtonClearProductsFilter class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's buttonClearProductsFilter.
 *
 * @see ButtonClearProductsFilter::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class ButtonClearProductsFilter {

    public const SHOW_ALWAYS = 'always';

    public const SHOW_FITLERING = 'filtering';

    public const SHOW_WITH_NO_RESULTS = 'noresults';

    private const SHOW_VALUES = [
        self::SHOW_ALWAYS,
        self::SHOW_FITLERING,
        self::SHOW_WITH_NO_RESULTS
    ];

    public string $class = '';

    public string $show = self::SHOW_FITLERING;

    public ?ApplicableFilters $applicableFilters = null;

    public array $appliedFilters = [];

    public ?int $productItems = null;

    private bool $output = false;

    private array $hrefAllParams = [];

    /**
     * Constructor method for ButtonClearProductsFilter class.
     * 
     * @see ButtonClearProductsFilter
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
        if (is_null($this->applicableFilters)) {
            throw new CommerceException("The value of [applicableFilters] argument is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        if (!in_array($this->show, self::SHOW_VALUES, true)) {
            throw new CommerceException("The value of [show] argument: '" . $this->show . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }

        $this->hrefAllParams = array_merge(array_keys($this->applicableFilters->toArray()), [
            constant('URL_ROUTE'),
            Parameters::FILTER_CUSTOMTAG_INTERVAL,
            Parameters::FILTER_CUSTOMTAG_RANGE,
        ]);

        $this->setOutput();

        return $this->getProperties();
    }

    /**
     * Set property output, evals if print macro output
     *
     * @return void
     */
    private function setOutput(): void {
        $filtering = ProductViewHelper::getFiltering($this->applicableFilters, $this->appliedFilters);

        if ($this->show === self::SHOW_ALWAYS) {
            $this->output = true;
        } elseif ($this->show === self::SHOW_FITLERING && $filtering === true) {
            $this->output = true;
        } elseif ($this->show === self::SHOW_WITH_NO_RESULTS && $filtering === true) {
            if (is_null($this->productItems)) {
                throw new CommerceException("The [productItems] argument is required if argument [show] is: '" . self::SHOW_WITH_NO_RESULTS . ", " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
            } elseif ($this->productItems == 0) {
                $this->output = true;
            }
        }
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'class' => $this->class,
            'show' => $this->show,
            'applicableFilters' => $this->applicableFilters,
            'hrefAllParams' => $this->hrefAllParams,
            'output' => $this->output
        ];
    }
}
