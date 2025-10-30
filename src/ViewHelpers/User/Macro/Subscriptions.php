<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\User\Subscription;

/**
 * This is the Subscriptions class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's lostPassword.
 *
 * @see Subscriptions::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class Subscriptions {

    public ?ElementCollection $subscriptions = null;

    public bool $allowUnsubscribe = true;

    /**
     * Constructor method for Subscriptions.
     * 
     * @see Subscriptions
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
        foreach ($this->subscriptions as $subscription) {
            if (!($subscription instanceof Subscription)) {
                throw new CommerceException('Each element of Subscriptions must be a instance of ' . Subscription::class . '. ' . ' Instance of ' . get_class($subscription) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
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
            'subscriptions' => $this->subscriptions,
            'allowUnsubscribe' => $this->allowUnsubscribe
        ];
    }
}
