<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Resources\Language;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Enums\LanguageLabels;
use FWK\Core\Exceptions\CommerceException;
use SDK\Enums\RouteType;
use FWK\Core\Resources\RoutePaths;

/**
 * This is the Steps class, a macro class for the basketViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's steps.
 *
 * @see Steps::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class Steps {

    public const STEP_BASKET = 'basket';

    public const STEP_CUSTOMER = 'customer';

    public const STEP_PAYMENT_AND_ASHIPPING = 'paymentAndShipping';

    public const STEP_CONFIRM_ORDER = 'confirmOrder';

    private const STEP_VALUES = [
        self::STEP_BASKET,
        self::STEP_CUSTOMER,
        self::STEP_PAYMENT_AND_ASHIPPING,
        self::STEP_CONFIRM_ORDER
    ];

    public array $steps = [
        self::STEP_BASKET,
        self::STEP_CUSTOMER,
        self::STEP_PAYMENT_AND_ASHIPPING,
        self::STEP_CONFIRM_ORDER
    ];

    public string $routeType = '';

    public bool $showNumbers = false;

    private ?Language $languageSheet = null;

    private array $stepsData = [];

    private int $selectedStep = 0;

    /**
     * Constructor method for Property class.
     *
     * @see Steps
     *
     * @param array $arguments
     * @param Language $languageSheet
     */
    public function __construct(array $arguments, Language $languageSheet) {
        ViewHelper::mergeArguments($this, $arguments);
        $this->languageSheet = $languageSheet;
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        $this->setSelectedStep();

        foreach ($this->steps as $step) {
            if (!in_array($step, self::STEP_VALUES, true)) {
                throw new CommerceException("The value '" . $step . "' of [steps] argument not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
            }

            $this->stepsData[] = $this->getStepObject($step);
        }

        return $this->getProperties();
    }

    private function setSelectedStep(): void {
        if ($this->routeType === RouteType::CHECKOUT_BASKET) {
            $this->selectedStep = 1;
        }
        elseif ($this->routeType === RouteType::CHECKOUT_CUSTOMER || $this->routeType === RouteType::CHECKOUT_CUSTOMER_NEW_REGISTER || $this->routeType === RouteType::CHECKOUT_GUEST || $this->routeType === RouteType::CHECKOUT_CREATE_ACCOUNT) {
            $this->selectedStep = 2;
        }
        elseif ($this->routeType === RouteType::CHECKOUT_PAYMENT_AND_SHIPPING|| $this->routeType === RouteType::CHECKOUT_DENIED_ORDER) {
            $this->selectedStep = 3;
        }
        elseif ($this->routeType === RouteType::CHECKOUT_CONFIRM_ORDER) {
            $this->selectedStep = 4;
        }
    }

    private function getStepObject(string $step): array {
        $name = '';
        $selected = false;
        $link = '';
        $linkable = false;
        $done = false;

        switch ($step) {
            case self::STEP_BASKET :
                $name = $this->languageSheet->getLabelValue(LanguageLabels::BASKET_STEP_BASKET);
                $link = RoutePaths::getPath(\FWK\Enums\RouteType::CHECKOUT_BASKET);
                if ($this->routeType === RouteType::CHECKOUT_BASKET) {
                    $selected = true;
                }
                $linkable = true;
                $done = true;
                break;
            case self::STEP_CUSTOMER :
                $name = $this->languageSheet->getLabelValue(LanguageLabels::BASKET_STEP_CUSTOMER);
                $link = RoutePaths::getPath(\FWK\Enums\RouteType::CHECKOUT_CUSTOMER);
                if ($this->routeType === RouteType::CHECKOUT_CUSTOMER || $this->routeType === RouteType::CHECKOUT_CUSTOMER_NEW_REGISTER || $this->routeType === RouteType::CHECKOUT_GUEST || $this->routeType === RouteType::CHECKOUT_CREATE_ACCOUNT) {
                    $selected = true;
                }
                $done = $this->selectedStep > 2;
                $linkable = $this->selectedStep >= 2;
                break;
            case self::STEP_PAYMENT_AND_ASHIPPING :
                $name = $this->languageSheet->getLabelValue(LanguageLabels::BASKET_STEP_PAYMENT_AND_SHIPPING);
                $link = RoutePaths::getPath(\FWK\Enums\RouteType::CHECKOUT_PAYMENT_AND_SHIPPING);
                if ($this->routeType === RouteType::CHECKOUT_PAYMENT_AND_SHIPPING) {
                    $selected = true;
                    $done = true;
                }
                $done = $this->selectedStep > 3;
                $linkable = $this->selectedStep >= 3;
                break;
            case self::STEP_CONFIRM_ORDER :
                $name = $this->languageSheet->getLabelValue(LanguageLabels::BASKET_STEP_CONFIRM_ORDER);
                $link = '';
                $linkable = false;
                if ($this->routeType === RouteType::CHECKOUT_CONFIRM_ORDER) {
                    $selected = true;
                    $done = true;
                }
                break;
        }

        return [
            'name' => $name,
            'link' => $link,
            'selected' => $selected,
            'linkable' => $linkable,
            'done' => $done
        ];
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'stepsData' => $this->stepsData,
            'showNumbers' => $this->showNumbers
        ];
    }
}