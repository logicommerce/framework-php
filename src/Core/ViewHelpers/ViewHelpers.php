<?php

namespace FWK\Core\ViewHelpers;

use FWK\Core\Resources\Loader;
use FWK\Core\Theme\Theme;
use FWK\Core\Resources\Language;
use FWK\Core\Resources\Session;

/**
 * This is the ViewHelpers class.
 * This class centralizes the creation of all the ViewHelpers for a certain 'Theme' and 'Language', and provides them through get methods.
 *
 * @see ViewHelpers::getAccount()
 * @see ViewHelpers::getBasket()
 * @see ViewHelpers::getBlog()
 * @see ViewHelpers::getCategory()
 * @see ViewHelpers::getForm()
 * @see ViewHelpers::getDocument()
 * @see ViewHelpers::getPage()
 * @see ViewHelpers::getProduct()
 * @see ViewHelpers::getThirdParty()
 * @see ViewHelpers::getUser()
 * @see ViewHelpers::getUtil()
 *
 * @package FWK\Core\ViewHelpers
 */
class ViewHelpers {

    private $account = null;

    private $basket = null;

    private $blog = null;

    private $category = null;

    private $form = null;

    private $document = null;

    private $page = null;

    private $product = null;

    private $thirdParty = null;

    private $user = null;

    private $util = null;

    /**
     * Constructor. It initializes all the ViewHelpers for the given theme and languageSheet.
     * 
     * @param Language $languageSheet
     * @param Theme $theme
     */
    public function __construct(Language $languageSheet, Theme $theme, ?Session $session) {
        $this->account = Loader::ViewHelper('Account', 'AccountViewHelper', $languageSheet, $theme, $session);
        $this->basket = Loader::ViewHelper('Basket', 'BasketViewHelper', $languageSheet, $theme, $session);
        $this->blog = Loader::ViewHelper('Blog', 'BlogViewHelper', $languageSheet, $theme, $session);
        $this->category = Loader::ViewHelper('Category', 'CategoryViewHelper', $languageSheet, $theme, $session);
        $this->form = Loader::ViewHelper('Form', 'FormViewHelper', $languageSheet, $theme, $session);
        $this->document = Loader::ViewHelper('Document', 'DocumentViewHelper', $languageSheet, $theme, $session);
        $this->page = Loader::ViewHelper('Page', 'PageViewHelper', $languageSheet, $theme, $session);
        $this->product = Loader::ViewHelper('Product', 'ProductViewHelper', $languageSheet, $theme, $session);
        $this->thirdParty = Loader::ViewHelper('ThirdParty', 'ThirdPartyViewHelper', $languageSheet, $theme, $session);
        $this->user = Loader::ViewHelper('User', 'UserViewHelper', $languageSheet, $theme, $session);
        $this->util = Loader::ViewHelper('Util', 'UtilViewHelper', $languageSheet, $theme, $session);
    }

    /**
     * This method returns the AccountViewHelper.
     * 
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getAccount() {
        return $this->account;
    }

    /**
     * This method returns the BasketViewHelper.
     * 
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getBasket() {
        return $this->basket;
    }

    /**
     * This method returns the BlogViewHelper.
     * 
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getBlog() {
        return $this->blog;
    }

    /**
     * This method returns the CategoryViewHelper.
     *
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * This method returns the FormViewHelper.
     * 
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * This method returns the DocumentViewHelper.
     * 
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getDocument() {
        return $this->document;
    }

    /**
     * This method returns the PageViewHelper.
     *
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * This method returns the ProductViewHelper.
     * 
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * This method returns the ThirdPartyViewHelper.
     * 
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getThirdParty() {
        return $this->thirdParty;
    }

    /**
     * This method returns the UserViewHelper.
     * 
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * This method returns the UtilViewHelper.
     * 
     * @return \FWK\Core\ViewHelpers\ViewHelper
     */
    public function getUtil() {
        return $this->util;
    }
}
