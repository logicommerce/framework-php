<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\FormFactory;
use SDK\Core\Dtos\UserPluginPaymentTokenCollection;
use SDK\Core\Dtos\PluginProperties;

/**
 * This is the PaymentCards class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's payment cards.
 *
 * @see PaymentCards::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class PaymentCards {

    public const USER_PLUGIN_PAYMENT_TOKENS = 'UserPluginPaymentTokens';

    public const PLUGINS_PROPERTIES = 'pluginProperties';

    public array $paymentCards = [];

    /**
     * Constructor method for PaymentCards.
     *
     * @see PaymentCards
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->paymentCards)) {
            throw new CommerceException("The value of [paymentCards] argument: '" . $this->paymentCards . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        foreach ($this->paymentCards as $paymentCards) {
            if (!$paymentCards[self::USER_PLUGIN_PAYMENT_TOKENS] instanceof UserPluginPaymentTokenCollection) {
                throw new CommerceException('The value of each paymentCards[' . self::USER_PLUGIN_PAYMENT_TOKENS . '] argument must be a instance of ' . UserPluginPaymentTokenCollection::class . '. ' . ' Instance of ' . get_class($paymentCards) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
            if (!$paymentCards[self::PLUGINS_PROPERTIES] instanceof PluginProperties) {
                throw new CommerceException('The value of each paymentCards[' . self::PLUGINS_PROPERTIES . '] argument must be a instance of ' . PluginProperties::class . '. ' . ' Instance of ' . get_class($paymentCards) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
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
            'paymentCards' => $this->paymentCards,
            'deleteCardForm' => FormFactory::getDeletePaymentCard(0, '{{token}}')
        ];
    }
}
