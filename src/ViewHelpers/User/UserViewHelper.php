<?php

namespace FWK\ViewHelpers\User;

use FWK\Core\ViewHelpers\Macros\ModalForm;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\ViewHelpers\User\Macro\AddressBook;
use FWK\ViewHelpers\User\Macro\AddressForm;
use FWK\ViewHelpers\User\Macro\ButtonShoppingListRows;
use FWK\ViewHelpers\User\Macro\ButtonWishlist;
use FWK\ViewHelpers\User\Macro\ChangePasswordForm;
use FWK\ViewHelpers\User\Macro\CustomerOrders;
use FWK\ViewHelpers\User\Macro\DeleteAccountForm;
use FWK\ViewHelpers\User\Macro\DeleteShoppingListRowsForm;
use FWK\ViewHelpers\User\Macro\DeleteWishlistForm;
use FWK\ViewHelpers\User\Macro\FilterShoppingListRowsForm;
use FWK\ViewHelpers\User\Macro\RedeemVouchers;
use FWK\ViewHelpers\User\Macro\LocationsPath;
use FWK\ViewHelpers\User\Macro\LoginForm;
use FWK\ViewHelpers\User\Macro\LostPasswordForm;
use FWK\ViewHelpers\User\Macro\OauthCallback;
use FWK\ViewHelpers\User\Macro\OrderRmas;
use FWK\ViewHelpers\User\Macro\Orders;
use FWK\ViewHelpers\User\Macro\OrderShipments;
use FWK\ViewHelpers\User\Macro\OrderTrackings;
use FWK\ViewHelpers\User\Macro\Panel;
use FWK\ViewHelpers\User\Macro\PaymentCards;
use FWK\ViewHelpers\User\Macro\RedeemRewardPoints;
use FWK\ViewHelpers\User\Macro\ReturnRequestForm;
use FWK\ViewHelpers\User\Macro\Rmas;
use FWK\ViewHelpers\User\Macro\SalesAgentCustomers;
use FWK\ViewHelpers\User\Macro\SalesAgentCustomersForm;
use FWK\ViewHelpers\User\Macro\SalesAgentSales;
use FWK\ViewHelpers\User\Macro\SalesAgentSalesForm;
use FWK\ViewHelpers\User\Macro\SendShoppingListRowsForm;
use FWK\ViewHelpers\User\Macro\SendWishlistForm;
use FWK\ViewHelpers\User\Macro\ShoppingListDeleteButton;
use FWK\ViewHelpers\User\Macro\ShoppingListEditButton;
use FWK\ViewHelpers\User\Macro\ShoppingListAddNoteButton;
use FWK\ViewHelpers\User\Macro\ShoppingListRowNotesForm;
use FWK\ViewHelpers\User\Macro\ShoppingListForm;
use FWK\ViewHelpers\User\Macro\ShoppingListRowMoveButton;
use FWK\ViewHelpers\User\Macro\ShoppingListRowEditButton;
use FWK\ViewHelpers\User\Macro\ShoppingListRowDeleteButton;
use FWK\ViewHelpers\User\Macro\SmallNewsletterForm;
use FWK\ViewHelpers\User\Macro\StockAlerts;
use FWK\ViewHelpers\User\Macro\Subscriptions;
use FWK\ViewHelpers\User\Macro\UserForm;

/**
 * This is the UserViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of the user's view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see ViewHelper
 *
 * @see UserViewHelper::addressBookMacro()
 * @see UserViewHelper::addressFormMacro()
 * @see UserViewHelper::addShoppingListRowNotesModalMacro()
 * @see UserViewHelper::buttonShoppingListRowsMacro()
 * @see UserViewHelper::changePasswordFormMacro()
 * @see UserViewHelper::deleteAccountFormMacro()
 * @see UserViewHelper::deleteShoppingListRowsFormMacro()
 * @see UserViewHelper::deleteShoppingListRowsModalMacro()
 * @see UserViewHelper::editShoppingListRowNotesModalMacro()
 * @see UserViewHelper::filterShoppingListRowsFormMacro()
 * @see UserViewHelper::formMacro()
 * @see UserViewHelper::loginFormMacro()
 * @see UserViewHelper::lostPasswordFormMacro()
 * @see UserViewHelper::oauthCallback()
 * @see UserViewHelper::orderRmasMacro()
 * @see UserViewHelper::ordersMacro()
 * @see UserViewHelper::panelMacro()
 * @see UserViewHelper::redeemRewardPointsMacro()
 * @see UserViewHelper::redeemVouchers()
 * @see UserViewHelper::sendShoppingListRowsFormMacro()
 * @see UserViewHelper::sendShoppingListRowsModalMacro()
 * @see UserViewHelper::setShoppingListModalMacro()
 * @see UserViewHelper::shoppingListAddNoteButtonMacro()
 * @see UserViewHelper::shoppingListDeleteButtonMacro()
 * @see UserViewHelper::shoppingListEditButtonMacro()
 * @see UserViewHelper::shoppingListFormMacro()
 * @see UserViewHelper::shoppingListRowDeleteButtonMacro()
 * @see UserViewHelper::shoppingListRowEditButtonMacro()
 * @see UserViewHelper::shoppingListRowMoveButtonMacro()
 * @see UserViewHelper::shoppingListRowNotesFormMacro()
 * @see UserViewHelper::smallNewsletterFormMacro()
 * 
 * deprecate
 * @see UserViewHelper::deleteWishlistFormMacro()
 * @see UserViewHelper::buttonWishlistMacro()
 * @see UserViewHelper::sendWishlistFormMacro()
 *
 * @package FWK\ViewHelpers\User
 */
class UserViewHelper extends ViewHelper {

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttonShoppingListRows.
     * The array keys of the returned parameters are:
     * <ul>
     *  <li>type</li>
     *  <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated
     */
    public function buttonShoppingListRowsMacro(array $arguments = []): array {
        $buttonShoppingListRows = new ButtonShoppingListRows($arguments);
        return $buttonShoppingListRows->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttonWishlist.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>type</li>
     *      <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated
     */
    public function buttonWishlistMacro(array $arguments = []): array {
        //trigger_error("The function 'buttonWishlistMacro' will be deprecated soon. you must use 'buttonShoppingListRowsMacro'", E_USER_NOTICE);
        $buttonWishlist = new ButtonWishlist($arguments);
        return $buttonWishlist->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the changePasswordForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function changePasswordFormMacro(array $arguments = []): array {
        $changePasswordForm = new ChangePasswordForm($arguments);
        return $changePasswordForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the deleteAccountForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function deleteAccountFormMacro(array $arguments = []): array {
        $deleteAccountForm = new DeleteAccountForm($arguments);
        return $deleteAccountForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the locationsPath.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>fieldName</li>
     * <li>selectedIds</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function locationsPathMacro(array $arguments = []): array {
        $locationsPath = new LocationsPath($arguments);
        return $locationsPath->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the lostPasswordForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function lostPasswordFormMacro(array $arguments = []): array {
        $lostPasswordForm = new LostPasswordForm($arguments);
        return $lostPasswordForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the returnRequestForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>items</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function returnRequestFormMacro(array $arguments = []): array {
        $returnRequestForm = new ReturnRequestForm($arguments);
        return $returnRequestForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the salesAgentCustomers.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>items</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated
     */
    public function salesAgentCustomersMacro(array $arguments = []): array {
        $salesAgentCustomers = new SalesAgentCustomers($arguments);
        return $salesAgentCustomers->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the salesAgentCustomersForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>items</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated
     */
    public function salesAgentCustomersFormMacro(array $arguments = []): array {
        $salesAgentCustomersForm = new SalesAgentCustomersForm($arguments);
        return $salesAgentCustomersForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the salesAgentSales.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>items</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated
     */
    public function salesAgentSalesMacro(array $arguments = []): array {
        $salesAgentSales = new SalesAgentSales($arguments);
        return $salesAgentSales->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the salesAgentSalesForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>items</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated
     */
    public function salesAgentSalesFormMacro(array $arguments = []): array {
        $salesAgentSalesForm = new SalesAgentSalesForm($arguments);
        return $salesAgentSalesForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the customerOrders.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>orders</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function customerOrdersMacro(array $arguments = []): array {
        $customerOrders = new CustomerOrders($arguments);
        return $customerOrders->getViewParameters();
    }



    /**
     * This method merges the given arguments, calculates and returns the view parameters for the oauth callback.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>checkoutLoginRedirect</li>
     * <li>commonLoginRedirect</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function oauthCallbackMacro(array $arguments = []): array {
        $oauthCallback = new OauthCallback($arguments);
        return $oauthCallback->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the orderRmas.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>rmas</li>
     * <li>userId</li>
     * <li>showRmasActions</li>
     * <li>showRmasIcons</li>
     * <li>orderId</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function orderRmasMacro(array $arguments = []): array {
        $orderRmas = new OrderRmas($arguments);
        return $orderRmas->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the panel.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>itemsList</li>
     * <li>keys</li>
     * <li>icons</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function panelMacro(array $arguments = []): array {
        $panel = new Panel($arguments);
        return $panel->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the payment cards.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>paymentSystems</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function paymentCards(array $arguments = []): array {
        $paymentCards = new PaymentCards($arguments);
        return $paymentCards->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the smallNewsletterForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>showLabel</li>
     * <li>showPlaceholder</li>
     * <li>disableValidationMessages</li>
     * <li>hiddeWithLogin</li>
     * <li>addLegalCheck</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function smallNewsletterFormMacro(array $arguments = []): array {
        $smallNewsletterForm = new SmallNewsletterForm($arguments);
        return $smallNewsletterForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the loginForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>showLabel</li>
     * <li>showPlaceholder</li>
     * <li>redirect</li>
     * <li>lostPasswordRedirect</li>
     * <li>registerRedirect</li>
     * <li>showLostPasswordLink</li>
     * <li>showCreateAccountLink</li>
     * <li>userWarnings</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function loginFormMacro(array $arguments = []): array {
        $loginForm = new LoginForm($arguments);
        return $loginForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the orders.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>orders</li>
     * <li>userId</li>
     * <li>showOrderStates</li>
     * <li>showOrderActions</li>
     * <li>showOrderIcons</li>
     * <li>documentView</li>
     * <li>returnProductsView</li>
     * <li>returnTracingView</li>
     * <li>showStatus</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function ordersMacro(array $arguments = []): array {
        $orders = new Orders($arguments);
        return $orders->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the order shipments.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>order</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function orderShipmentsMacro(array $arguments = []): array {
        $orders = new OrderShipments($arguments);
        return $orders->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the order trackings.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>shipments</li>
     * <li>itemClass</li>
     * <li>showContainer</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function orderTrackingsMacro(array $arguments = []): array {
        $orders = new OrderTrackings($arguments);
        return $orders->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the rmas.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>rmas</li>
     * <li>userId</li>
     * <li>showRmasStates</li>
     * <li>showRmasActions</li>
     * <li>showRmasIcons</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function rmasMacro(array $arguments = []): array {
        $rmas = new Rmas($arguments);
        return $rmas->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the user form.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>selectedCountry</li>
     * <li>selectedCountryLocations</li>
     * <li>showCreateAccountCheck</li>
     * <li>showShippingAddress</li>
     * <li>forceUseShippingAddress</li>
     * <li>defaultUserType</li>
     * <li>class</li>
     * <li>isGuest</li>
     * <li>billingAddresses</li>
     * <li>shippingAddresses</li>
     * <li>showAddNewBilling</li>
     * <li>showAddNewShipping</li>
     * <li>showEditBilling</li>
     * <li>showEditShipping</li>
     * <li>showDeleteBilling</li>
     * <li>showDeleteShipping</li>
     * <li>locationMode</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function userFormMacro(array $arguments = []): array {
        $userForm = new UserForm($arguments);
        return $userForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the address book.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>billingAddresses</li>
     * <li>shippingAddresses</li>
     * <li>defaultUserType</li>
     * <li>selectMode</li>
     * <li>showAddNewBilling</li>
     * <li>showAddNewShipping</li>
     * <li>showEditBilling</li>
     * <li>showEditShipping</li>
     * <li>showDeleteBilling</li>
     * <li>showDeleteShipping</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function addressBookMacro(array $arguments = []): array {
        $addressBook = new AddressBook($arguments);
        return $addressBook->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the address book form.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>selectedCountry</li>
     * <li>selectedCountryLocations</li>
     * <li>prefix</li>
     * <li>defaultUserType</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function addressFormMacro(array $arguments = []): array {
        $addressForm = new AddressForm($arguments);
        return $addressForm->getViewParameters();
    }


    /**
     * This method merges the given arguments, calculates and returns the view parameters for the deleteShoppingListRowsForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>rows</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated 
     */
    public function deleteShoppingListRowsFormMacro(array $arguments = []): array {
        $deleteShoppingListRowsForm = new DeleteShoppingListRowsForm($arguments);
        return $deleteShoppingListRowsForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the deleteWishlistForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>products</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated 
     */
    public function deleteWishlistFormMacro(array $arguments = []): array {
        //trigger_error("The function 'deleteWishlistFormMacro' will be deprecated soon. you must use 'deleteShoppingListRowsFormMacro'", E_USER_NOTICE);
        $deleteWishlistForm = new DeleteWishlistForm($arguments);
        return $deleteWishlistForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the SendShoppingListRowsForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>products</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated
     */
    public function SendShoppingListRowsFormMacro(array $arguments = []): array {
        $SendShoppingListRowsForm = new SendShoppingListRowsForm($arguments);
        return $SendShoppingListRowsForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the sendWishlistForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>products</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated
     */
    public function sendWishlistFormMacro(array $arguments = []): array {
        //trigger_error("The function 'sendWishlistFormMacro' will be deprecated soon. you must use 'SendShoppingListRowsFormMacro'", E_USER_NOTICE);
        $sendWishlistForm = new SendWishlistForm($arguments);
        return $sendWishlistForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the stockAlerts.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>stockAlerts</li>
     * <li>allowRemove</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function stockAlertsMacro(array $arguments = []): array {
        $stockAlerts = new StockAlerts($arguments);
        return $stockAlerts->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the subscriptions.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>subscriptions</li>
     * <li>allowUnsubscribe</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function subscriptionsMacro(array $arguments = []): array {
        $subscriptions = new Subscriptions($arguments);
        return $subscriptions->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the redeemRewardPoints.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>rewardPoints</li>
     * <li>showPending</li>
     * <li>showDistribution</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function redeemRewardPointsMacro(array $arguments = []): array {
        $rewardPoints = new RedeemRewardPoints($arguments);
        return $rewardPoints->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the redeemVouchers.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>vouchers</li>
     * <li>showCode</li>
     * <li>showAvailableBalance</li>
     * <li>showExpirationDate</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function redeemVouchersMacro(array $arguments = []): array {
        $arguments = new RedeemVouchers($arguments);
        return $arguments->getViewParameters();
    }


    /**
     * This method merges the given arguments, calculates and returns the view parameters for the shoppingListForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>shoppingList</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function shoppingListFormMacro(array $arguments = []): array {
        $arguments = new ShoppingListForm($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the shoppingListRowNotesForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>shoppingListRow</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function shoppingListRowNotesFormMacro(array $arguments = []): array {
        $arguments = new ShoppingListRowNotesForm($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the shoppingListRowMoveButtonMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>row</li>
     * <li>class</li>
     * <li>shoppingLists</li>
     * <li>containerId</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function shoppingListRowMoveButtonMacro(array $arguments = []): array {
        $arguments = new ShoppingListRowMoveButton($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the shoppingListRowDeleteButtonMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>row</li>
     * <li>class</li>
     * <li>containerId</li>
     * <li>shoppingListId</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function shoppingListRowDeleteButtonMacro(array $arguments = []): array {
        $arguments = new ShoppingListRowDeleteButton($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the shoppingListRowEditButtonMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>row</li>
     * <li>totalItems</li>
     * <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function shoppingListRowEditButtonMacro(array $arguments = []): array {
        $arguments = new ShoppingListRowEditButton($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the shoppingListDeleteButtonMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>shoppingList</li>
     * <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function shoppingListDeleteButtonMacro(array $arguments = []): array {
        $arguments = new ShoppingListDeleteButton($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the shoppingListEditButtonMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>shoppingList</li>
     * <li>totalItems</li>
     * <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function shoppingListEditButtonMacro(array $arguments = []): array {
        $arguments = new ShoppingListEditButton($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the shoppingListAddNoteButtonMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>shoppingList</li>
     * <li>totalItems</li>
     * <li>rowTemplate</li>
     * <li>containerId</li>
     * <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function shoppingListAddNoteButtonMacro(array $arguments = []): array {
        $arguments = new ShoppingListAddNoteButton($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the filterShoppingListRowsForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>applicableFilters</li>
     * <li>appliedFilters</li>
     * <li>defaultParametersValues</li>
     * <li>autosubmit</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function filterShoppingListRowsFormMacro(array $arguments = []): array {
        $vouchers = new FilterShoppingListRowsForm($arguments);
        return $vouchers->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the editShoppingListRowNotesModalMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>showHeader</li>
     * <li>headerTitle</li>
     * <li>form</li>
     * <li>dialogClasses</li>
     * <li>element</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function editShoppingListRowNotesModalMacro(array $arguments = []): array {
        $arguments = new ModalForm($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the addShoppingListRowNotesModalMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>showHeader</li>
     * <li>headerTitle</li>
     * <li>form</li>
     * <li>dialogClasses</li>
     * <li>element</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function addShoppingListRowNotesModalMacro(array $arguments = []): array {
        $arguments = new ModalForm($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the sendShoppingListRowsModalMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>showHeader</li>
     * <li>headerTitle</li>
     * <li>form</li>
     * <li>dialogClasses</li>
     * <li>element</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function sendShoppingListRowsModalMacro(array $arguments = []): array {
        $arguments = new ModalForm($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the deleteShoppingListRowsModalMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>showHeader</li>
     * <li>headerTitle</li>
     * <li>form</li>
     * <li>dialogClasses</li>
     * <li>element</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function deleteShoppingListRowsModalMacro(array $arguments = []): array {
        $arguments = new ModalForm($arguments);
        return $arguments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the setShoppingListModalMacro.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>showHeader</li>
     * <li>headerTitle</li>
     * <li>form</li>
     * <li>dialogClasses</li>
     * <li>element</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function setShoppingListModalMacro(array $arguments = []): array {
        $arguments = new ModalForm($arguments);
        return $arguments->getViewParameters();
    }
}
