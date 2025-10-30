<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormUseCaptcha' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormUseCaptcha::getAddress()
 * @see FormUseCaptcha::getBlogSubscribe()
 * @see FormUseCaptcha::getComment()
 * @see FormUseCaptcha::getContact()
 * @see FormUseCaptcha::getDeleteAccount()
 * @see FormUseCaptcha::getDeletePaymentCard()
 * @see FormUseCaptcha::getDeleteShoppingListRows()
 * @see FormUseCaptcha::getLogin()
 * @see FormUseCaptcha::getLostPassword()
 * @see FormUseCaptcha::getNewPassword()
 * @see FormUseCaptcha::getNewsletter()
 * @see FormUseCaptcha::getPostComment()
 * @see FormUseCaptcha::getProductContact()
 * @see FormUseCaptcha::getReturnRequest()
 * @see FormUseCaptcha::getSendMail()
 * @see FormUseCaptcha::getSendShoppingListRows()
 * @see FormUseCaptcha::getSetUser()
 * @see FormUseCaptcha::getShoppingListRowNotes()
 * @see FormUseCaptcha::getStockAlert()
 * @see FormUseCaptcha::getUpdatePassword()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormUseCaptcha extends Element {
    use ElementTrait;

    public const ADDRESS = 'address';
    public const BLOG_SUBSCRIBE = 'blogSubscribe';
    public const COMMENT = 'comment';
    public const CONTACT = 'contact';
    public const DELETE_ACCOUNT = 'deleteAccount';
    public const DELETE_PAYMENT_CARD = 'deletePaymentCard';
    public const DELETE_SHOPPING_LIST_ROWS = 'deleteShoppingListRows';
    public const LOGIN = 'login';
    public const LOST_PASSWORD = 'lostPassword';
    public const NEW_PASSWORD = 'newPassword';
    public const NEWSLETTER = 'newsletter';
    public const POST_COMMENT = 'postComment';
    public const PRODUCT_CONTACT = 'productContact';
    // @deprecate
    public const RECOMMEND = 'Recommend';
    public const RETURN_REQUEST = 'returnRequest';
    public const SEND_MAIL = 'sendMail';
    public const SEND_SHOPPING_LIST_ROWS = 'sendShoppingListRows';
    public const SET_USER = 'setUser';
    public const SHOPPING_LIST = 'shoppingList';
    public const SHOPPING_LIST_ROW_NOTES = 'shoppingListRowNotes';
    public const STOCK_ALERT = 'stockAlert';
    public const UPDATE_PASSWORD = 'updatePassword';

    private bool $address = false;
    private bool $blogSubscribe = false;
    private bool $comment = false;
    private bool $contact = false;
    private bool $deleteAccount = false;
    private bool $deletePaymentCard = false;
    private bool $deleteShoppingListRows = false;
    private bool $login = false;
    private bool $lostPassword = false;
    private bool $newPassword = false;
    private bool $newsletter = false;
    private bool $postComment = false;
    private bool $productContact = false;
    private bool $recommend = false;
    private bool $returnRequest = false;
    private bool $sendMail = false;
    private bool $sendShoppingListRows = false;
    private bool $setUser = false;
    private bool $shoppingList = false;
    private bool $shoppingListRowNotes = false;
    private bool $stockAlert = false;
    private bool $updatePassword = false;

    /**
     * This method returns the address element configuration
     * 
     * @return bool
     */
    public function getAddress(): bool {
        return $this->address;
    }

    /**
     * This method returns the blogSubscribe element configuration
     * 
     * @return bool
     */
    public function getBlogSubscribe(): bool {
        return $this->blogSubscribe;
    }

    /**
     * This method returns the comment element configuration
     * 
     * @return bool
     */
    public function getComment(): bool {
        return $this->comment;
    }

    /**
     * This method returns the contact element configuration
     * 
     * @return bool
     */
    public function getContact(): bool {
        return $this->contact;
    }

    /**
     * This method returns the deleteAccount element configuration
     * 
     * @return bool
     */
    public function getDeleteAccount(): bool {
        return $this->deleteAccount;
    }

    /**
     * This method returns the deletePaymentCard element configuration
     * 
     * @return bool
     */
    public function getDeletePaymentCard(): bool {
        return $this->deletePaymentCard;
    }

    /**
     * This method returns the deleteShoppingListRows element configuration
     * 
     * @return bool
     */
    public function getDeleteShoppingListRows(): bool {
        return $this->deleteShoppingListRows;
    }

    /**
     * This method returns the login element configuration
     * 
     * @return bool
     */
    public function getLogin(): bool {
        return $this->login;
    }

    /**
     * This method returns the lostPassword element configuration
     * 
     * @return bool
     */
    public function getLostPassword(): bool {
        return $this->lostPassword;
    }

    /**
     * This method returns the newPassword element configuration
     * 
     * @return bool
     */
    public function getNewPassword(): bool {
        return $this->newPassword;
    }

    /**
     * This method returns the newsletter element configuration
     * 
     * @return bool
     */
    public function getNewsletter(): bool {
        return $this->newsletter;
    }

    /**
     * This method returns the postComment element configuration
     * 
     * @return bool
     */
    public function getPostComment(): bool {
        return $this->postComment;
    }

    /**
     * This method returns the productContact element configuration
     * 
     * @return bool
     */
    public function getProductContact(): bool {
        return $this->productContact;
    }

    /**
     * This method returns the recommend element configuration
     * 
     * @return bool
     */
    public function getRecommend(): bool {
        return $this->recommend;
    }

    /**
     * This method returns the returnRequest element configuration
     * 
     * @return bool
     */
    public function getReturnRequest(): bool {
        return $this->returnRequest;
    }

    /**
     * This method returns the sendMail element configuration
     * 
     * @return bool
     */
    public function getSendMail(): bool {
        return $this->sendMail;
    }

    /**
     * This method returns the sendShoppingListRows element configuration
     * 
     * @return bool
     */
    public function getSendShoppingListRows(): bool {
        return $this->sendShoppingListRows;
    }

    /**
     * This method returns the setUser element configuration
     * 
     * @return bool
     */
    public function getSetUser(): bool {
        return $this->setUser;
    }

    /**
     * This method returns the shoppingList element configuration
     * 
     * @return bool
     */
    public function getShoppingList(): bool {
        return $this->shoppingList;
    }

    /**
     * This method returns the shoppingListRowNotes element configuration
     * 
     * @return bool
     */
    public function getShoppingListRowNotes(): bool {
        return $this->shoppingListRowNotes;
    }

    /**
     * This method returns the stockAlert element configuration
     * 
     * @return bool
     */
    public function getStockAlert(): bool {
        return $this->stockAlert;
    }

    /**
     * This method returns the updatePassword element configuration
     * 
     * @return bool
     */
    public function getUpdatePassword(): bool {
        return $this->updatePassword;
    }
}
