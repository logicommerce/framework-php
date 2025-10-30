<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\Resources\Language;
use FWK\Enums\LanguageLabels;
use SDK\Dtos\Catalog\Product\Product;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the Countdown class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's countdown.
 *
 * @see Countdown::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class Countdown {

    public const EVENT_TIME_END_OFFER_DATE = 'endOfferDate';

    public const EVENT_TIME_AVAILABLE_DATE = 'availableDate';

    public const CALLBACK_RELOAD = 'reload';

    public const CALLBACK_GO_BACK = 'goBack';

    private const CALLBACK_VALUES = [
        self::CALLBACK_RELOAD,
        self::CALLBACK_GO_BACK
    ];

    private const EVENT_TIME_VALUES = [
        self::EVENT_TIME_END_OFFER_DATE,
        self::EVENT_TIME_AVAILABLE_DATE
    ];

    public ?Product $product = null;

    public string $eventTime = self::EVENT_TIME_END_OFFER_DATE;

    public string $callback = self::CALLBACK_RELOAD;

    public ?string $template = null;

    public ?int $categoryId = null;

    public ?\DateTime $endDate = null;

    private ?Language $languageSheet = null;

    /**
     * Constructor method for Countdown class.
     * 
     * @see Countdown
     * 
     * @param array $arguments
     * @param Language $languageSheet
     */
    public function __construct(array $arguments, Language $languageSheet) {
        $this->languageSheet = $languageSheet;
        $this->setDefaults();
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method sets non static default properties
     *
     * @return void
     */
    private function setDefaults(): void {
        $this->template = $this->languageSheet->getLabelValue(LanguageLabels::PRODUCT_COUNTDOWN_TEMPLATE);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->product)) {
            throw new CommerceException("The value of [product] argument: '" . $this->product . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!in_array($this->eventTime, self::EVENT_TIME_VALUES, true)) {
            throw new CommerceException("The value of [eventTime] argument: '" . $this->eventTime . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }
        if (!in_array($this->callback, self::CALLBACK_VALUES, true)) {
            throw new CommerceException("The value of [callback] argument: '" . $this->callback . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }

        $this->setEndDate();

        return $this->getProperties();
    }

    /**
     * Set formatted endDate date from product property or manual
     *
     * @return void
     */
    private function setEndDate(): void {
        if (is_null($this->endDate)) {
            $productDefinition = $this->product->getDefinition();

            if (!is_null($productDefinition)) {
                if ($this->eventTime === self::EVENT_TIME_END_OFFER_DATE) {
                    $this->endDate = $productDefinition->getEndOfferDate()->getDateTime();
                } elseif ($this->eventTime === self::EVENT_TIME_AVAILABLE_DATE) {
                    $this->endDate = $productDefinition->getAvailableDate()->getDateTime();
                }
            }
        } else {
            // if date custom passed, format.
            $this->endDate = $this->endDate->getDateTime();
        }
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'product' => $this->product,
            'eventTime' => $this->eventTime,
            'callback' => $this->callback,
            'template' => $this->template,
            'categoryId' => $this->categoryId,
            'endDate' => $this->endDate
        ];
    }
}
