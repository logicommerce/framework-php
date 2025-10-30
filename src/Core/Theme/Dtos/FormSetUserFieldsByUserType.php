<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormSetUserFieldsByUserType' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormSetUserFieldsByUserType::getUser()
 * @see FormSetUserFieldsByUserType::getBilling()
 * @see FormSetUserFieldsByUserType::getShipping() 
 * @see FormSetUserFieldsByUserType::getKeyName()
 * @see FormSetUserFieldsByUserType::setKeyName()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormSetUserFieldsByUserType extends Element {
    use ElementTrait;
    
    public const INCLUDED = 'included';
    
    public const PRIORITY = 'priority';

    public const USER = 'user';

    public const BILLING = 'billing';
    
    private bool $included = false;

    private int $priority = 0;

    private ?FormSetUserFieldsByUserTypeElement $user = null;
        
    private ?FormSetUserFieldsByUserTypeElement $billing = null;

    protected string $keyName = '';

    /**
     * This method returns the included value.
     * 
     * @return bool
     */
    public function getIncluded(): bool {
        return $this->included;
    }

    /**
     * This method returns the priority value.
     * 
     * @return int
     */
    public function getPriority(): int {
        return $this->priority;
    }

    /**
     * This method returns the element user to apply in the set user form.
     * 
     * @return FormSetUserFieldsByUserTypeElement|NULL
     */
    public function getUser(): ?FormSetUserFieldsByUserTypeElement {
        return $this->user;
    }

    private function setUser(array $user): void {
        $this->user = new FormSetUserFieldsByUserTypeElement($user);
    }

    /**
     * This method returns the element billing to apply in the set user form.
     *
     * @return FormSetUserFieldsByUserTypeElement|NULL
     */
    public function getBilling(): ?FormSetUserFieldsByUserTypeElement {
        return $this->billing;
    }
    
    private function setBilling(array $billing): void {
        $this->billing = new FormSetUserFieldsByUserTypeElement($billing);
    }

    /**
     * This method returns the field keyName.
     *
     * @return string
     */
    public function getKeyName(): string {
        return $this->keyName;
    }

    /**
     * This method returns the field keyName.
     */
    public function setKeyName(string $keyName): void {
        $this->keyName = $keyName;
    }

}


