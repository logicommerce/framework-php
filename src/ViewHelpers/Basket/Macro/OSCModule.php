<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the one step checkout module class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's one step checkout module.
 *
 * @see OSCModule::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class OSCModule {

    public ?string $content = null;

    public string $class = '';

    public string $type = '';

    public bool $additionalContent = false;

    // macro arguments constants
    public const TYPE_BASKET = 'basket';

    public const TYPE_USER_FORM = 'userForm';

    public const TYPE_PAYMENTS = 'payments';

    public const TYPE_SHIPPINGS = 'shippings';

    public const TYPE_COMMENTS = 'comments';

    public const TYPE_DISCOUNTS = 'discounts';

    public const TYPE_LEGAL_CHECK = 'legalCheck';

    public const TYPE_REWARD_POINTS = 'rewardPoints';

    public const TYPE_BUTTONS = 'buttons';

    public const TYPE_LINKEDS = 'linkeds';

    public const TYPE_SAVE_FOR_LATER = 'saveForLater';

    public const TYPE_SELECTABLE_GIFTS = 'selectableGifts';

    public const TYPE_LOCKED_STOCKS = 'lockedStocks';

    private const TYPE_VALUES = [
        self::TYPE_BASKET,
        self::TYPE_USER_FORM,
        self::TYPE_PAYMENTS,
        self::TYPE_SHIPPINGS,
        self::TYPE_COMMENTS,
        self::TYPE_PAYMENTS,
        self::TYPE_DISCOUNTS,
        self::TYPE_LEGAL_CHECK,
        self::TYPE_BUTTONS,
        self::TYPE_LINKEDS,
        self::TYPE_REWARD_POINTS,
        self::TYPE_SAVE_FOR_LATER,
        self::TYPE_SELECTABLE_GIFTS,
        self::TYPE_LOCKED_STOCKS
    ];

    /**
     * Constructor method for OSCModule class.
     * 
     * @see OSCModule
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (!in_array($this->type, self::TYPE_VALUES, true)) {
            throw new CommerceException('The value of [type] argument: "' . $this->type . '" not exists in ' . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    private function getProperties(): array {
        return [
            'additionalContent' => $this->additionalContent,
            'content' => $this->content,
            'class' => $this->class,
            'type' => $this->type
        ];
    }
}
