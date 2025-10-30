<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Utils;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\User\User;
use SDK\Enums\UserType;

/**
 * This is the FillDataFunction class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the trackers output.
 *
 * @see FillDataFunction::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class FillDataFunction {

    public ?User $user = null;

    public ?string $id = null;

    public ?string $type = null;

    /**
     * Constructor method for FillDataFunction
     *
     * @see FillDataFunction
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UtilViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->user)) {
            throw new CommerceException("The value of [user] argument: '" . $this->user . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        return $this->getProperties();
    }

    /**
     * Return registered user nick depends userType and has login.
     *
     * @param User $user
     *
     * @return string
     */
    private function getUserNick(): string {
        $nick = '';
        $defaultBillingAddress = $this->user->getDefaultBillingAddress();
        if (Utils::isUserLoggedIn($this->user) && !is_null($defaultBillingAddress)) {
            if ($defaultBillingAddress->getUserType() === UserType::BUSINESS) {
                if (strlen($defaultBillingAddress->getCompany())) {
                    $nick = $defaultBillingAddress->getCompany();
                } else {
                    $nick = $defaultBillingAddress->getFirstName() . ' ' . $defaultBillingAddress->getLastName();
                }
            } else {
                if (strlen(trim($defaultBillingAddress->getFirstName() . ' ' . $defaultBillingAddress->getLastName()))) {
                    $nick = $defaultBillingAddress->getFirstName() . ' ' . $defaultBillingAddress->getLastName();
                } else {
                    $nick = $defaultBillingAddress->getCompany();
                }
            }
        }
        return $nick;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'user' => $this->user,
            'userNick' => $this->getUserNick(),
            'id' => $this->id,
            'type' => $this->type
        ];
    }
}
