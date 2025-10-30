<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'Forms' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Forms::getSetUser()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class Forms extends Element {
    use ElementTrait;

    public const SET_USER = 'setUser';

    public const COMMENTS = 'comments';

    public const PRODUCT_CONTACT = 'productContact';

    public const CONTACT = 'contact';

    public const ELEMENTS = 'elements';

    public const USE_CAPTCHA = 'useCaptcha';

    public const SHOPPING_LIST = 'shoppingList';

    public const SHOPPING_LIST_ROW_NOTE = 'shoppingListRowNote';

    private ?FormSetUser $setUser = null;

    private ?FormComments $comments = null;

    private ?FormProductContact $productContact = null;

    private ?FormContact $contact = null;

    private ?FormElements $elements = null;

    private ?FormUseCaptcha $useCaptcha = null;

    private ?FormShoppingList $shoppingList = null;

    private ?FormShoppingListRowNote $shoppingListRowNote = null;

    /**
     * This method returns the setUser form configurations.
     *
     * @return FormSetUser|NULL
     */
    public function getSetUser(): ?FormSetUser {
        return $this->setUser;
    }

    private function setSetUser(array $setUser): void {
        $this->setUser = new FormSetUser($setUser);
    }

    /**
     * This method returns the comments form configurations.
     *
     * @return FormComments|NULL
     */
    public function getComments(): ?FormComments {
        return $this->comments;
    }

    private function setComments(array $comments): void {
        $this->comments = new FormComments($comments);
    }

    /**
     * This method returns the product contact form configurations.
     *
     * @return FormProductContact|NULL
     */
    public function getProductContact(): ?FormProductContact {
        return $this->productContact;
    }

    private function setProductContact(array $productContact): void {
        $this->productContact = new FormProductContact($productContact);
    }

    /**
     * This method returns the contact form configurations.
     *
     * @return FormContact|NULL
     */
    public function getContact(): ?FormContact {
        return $this->contact;
    }

    private function setContact(array $contact): void {
        $this->contact = new FormContact($contact);
    }

    /**
     * This method returns the form elements configuration.
     *
     * @return FormElements|NULL
     */
    public function getElements(): ?FormElements {
        return $this->elements;
    }

    private function setElements(array $elements): void {
        $this->elements = new FormElements($elements);
    }

    /**
     * This method returns the form useCaptcha configuration.
     *
     * @return FormUseCaptcha|NULL
     */
    public function getUseCaptcha(): ?FormUseCaptcha {
        return $this->useCaptcha;
    }

    private function setUseCaptcha(array $useCaptcha): void {
        $this->useCaptcha = new FormUseCaptcha($useCaptcha);
    }

    /**
     * This method returns the form shoppingList configuration.
     *
     * @return FormShoppingList|NULL
     */
    public function getShoppingList(): ?FormShoppingList {
        return $this->shoppingList;
    }

    private function setShoppingList(array $shoppingList): void {
        $this->shoppingList = new FormShoppingList($shoppingList);
    }

    /**
     * This method returns the form shoppingListRowNote configuration.
     *
     * @return FormShoppingListRowNote|NULL
     */
    public function getShoppingListRowNote(): ?FormShoppingListRowNote {
        return $this->shoppingListRowNote;
    }

    private function setShoppingListRowNote(array $shoppingListRowNote): void {
        $this->shoppingListRowNote = new FormShoppingListRowNote($shoppingListRowNote);
    }
}
