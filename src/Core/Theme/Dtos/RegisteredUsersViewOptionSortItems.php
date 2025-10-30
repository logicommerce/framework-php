<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'RegisteredUsersViewOptionSortItems' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOption::getAvailableTemplates()
 *
 * @see Element
 * 
 * @uses ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class RegisteredUsersViewOptionSortItems extends Element {
    use ElementTrait;
    public const ID = 'id';
    public const FIRSTNAME = 'firstName';
    public const LASTNAME = 'lastName';
    public const EMAIL = 'email';
    public const USERNAME = 'username';
    public const PID = 'pId';
    public const DATEADDED = 'dateAdded';
    private ?ViewOptionSortItem $id = null;
    private ?ViewOptionSortItem $firstName = null;
    private ?ViewOptionSortItem $lastName = null;
    private ?ViewOptionSortItem $email = null;
    private ?ViewOptionSortItem $username = null;
    private ?ViewOptionSortItem $pId = null;
    private ?ViewOptionSortItem $dateAdded = null;

    /**
     * This method returns id sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getId(): ?ViewOptionSortItem {
        return $this->id;
    }
    private function setId(array $id): void {
        $this->id = new ViewOptionSortItem($id);
    }

    /**
     * This method returns firstName sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getFirstName(): ?ViewOptionSortItem {
        return $this->firstName;
    }
    private function setFirstName(array $firstName): void {
        $this->firstName = new ViewOptionSortItem($firstName);
    }

    /**
     * This method returns lastName sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getLastName(): ?ViewOptionSortItem {
        return $this->lastName;
    }
    private function setLastName(array $lastName): void {
        $this->lastName = new ViewOptionSortItem($lastName);
    }

    /**
     * This method returns email sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getEmail(): ?ViewOptionSortItem {
        return $this->email;
    }
    private function setEmail(array $email): void {
        $this->email = new ViewOptionSortItem($email);
    }

    /**
     * This method returns username sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getUsername(): ?ViewOptionSortItem {
        return $this->username;
    }
    private function setUsername(array $username): void {
        $this->username = new ViewOptionSortItem($username);
    }

    /**
     * This method returns pId sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getPId(): ?ViewOptionSortItem {
        return $this->pId;
    }
    private function setPId(array $pId): void {
        $this->pId = new ViewOptionSortItem($pId);
    }

    /**
     * This method returns dateAdded sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getDateAdded(): ?ViewOptionSortItem {
        return $this->dateAdded;
    }
    private function setDateAdded(array $dateAdded): void {
        $this->dateAdded = new ViewOptionSortItem($dateAdded);
    }
}
