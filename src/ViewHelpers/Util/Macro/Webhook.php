<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\WebhookResponse;

/**
 * This is the Webhook class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the Webhook output.
 *
 * @see Webhook::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class Webhook {

    public ?WebhookResponse $webhookResponse = null;

    public array $postParameters = [];

    public array $getParameters = [];

    /**
     * Constructor method for Webhook class.
     * 
     * @see Webhook
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
        if (is_null($this->webhookResponse)) {
            //throw new CommerceException("The value of [webhookResponse] argument: '" . $this->webhookResponse . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'webhookResponse' => $this->webhookResponse,
            'postParameters' => $this->postParameters,
            'getParameters' => $this->getParameters
        ];
    }
}
