<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormMaster' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormSetUser::getFields()
 * @see FormSetUser::getAccountTabPane()
 * @see FormSetUser::getDefaultAccountTabPane()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormMaster extends Element {
    use ElementTrait;

    public const REGISTERED_USER = 'registeredUser';

    private ?FormRegisteredUser $registeredUser = null;

    /**
     * This method returns the registered user form configurations.
     *
     * @return FormRegisteredUser|NULL
     */
    public function getRegisteredUser(): ?FormRegisteredUser {
        return $this->registeredUser;
    }

    private function setRegisteredUser(array $registeredUser): void {
        $this->registeredUser = new FormRegisteredUser($registeredUser);
    }
}
