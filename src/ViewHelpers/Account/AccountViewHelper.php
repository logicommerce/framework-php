<?php

namespace FWK\ViewHelpers\Account;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\ViewHelpers\Account\Macro\CustomerOrders;
use FWK\ViewHelpers\Account\Macro\RegisteredUsers;
use FWK\ViewHelpers\Account\Macro\RegisteredUserCreateForm;
use FWK\ViewHelpers\Account\Macro\RegisteredUserMoveForm;
use FWK\ViewHelpers\Account\Macro\RegisteredUsersForm;
use FWK\ViewHelpers\Account\Macro\RegisteredUserUpdateForm;
use FWK\ViewHelpers\Account\Macro\SalesAgentCustomers;
use FWK\ViewHelpers\Account\Macro\SalesAgentCustomersForm;
use FWK\ViewHelpers\Account\Macro\SalesAgentSales;
use FWK\ViewHelpers\Account\Macro\SalesAgentSalesForm;
use FWK\ViewHelpers\Account\Macro\UsedAccountSwitch;
use FWK\ViewHelpers\Account\Macro\AccountOrders;
use FWK\ViewHelpers\Account\Macro\ApproveRegisteredUser;
use FWK\ViewHelpers\Account\Macro\CompanyRoles;
use FWK\ViewHelpers\Account\Macro\OrdersForm;
use FWK\ViewHelpers\Account\Macro\CompanyStructure;
use FWK\ViewHelpers\Account\Macro\Panel;
use FWK\ViewHelpers\Account\Macro\RegisteredUserApproveForm;
use FWK\ViewHelpers\User\Macro\Orders;

/**
 * This is the AccountViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of the account's view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see ViewHelper
 *
 * @see AccountViewHelper::customerOrdersMacro()
 * @see AccountViewHelper::registeredUsersFormMacro()
 * @see AccountViewHelper::registeredUsersMacro()
 * @see AccountViewHelper::registeredUserCreateFormMacro()
 * @see AccountViewHelper::registeredUserMoveFormMacro()
 * @see AccountViewHelper::registeredUserUpdateFormMacro()
 * @see AccountViewHelper::salesAgentCustomersMacro()
 * @see AccountViewHelper::salesAgentCustomersFormMacro()
 * @see AccountViewHelper::salesAgentSalesMacro()
 * @see AccountViewHelper::salesAgentSalesFormMacro()

 *
 * @package FWK\ViewHelpers\Account
 */
class AccountViewHelper extends ViewHelper {

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
     * This method merges the given arguments, calculates and returns the view parameters for the registeredUsersForm.
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
    public function registeredUsersFormMacro(array $arguments = []): array {
        $registeredUsersForm = new RegisteredUsersForm($arguments);
        return $registeredUsersForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the registeredUsersMacro.
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
    public function registeredUsersMacro(array $arguments = []): array {
        $registeredUsers = new RegisteredUsers($arguments);
        return $registeredUsers->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the companyRolesMacro.
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
    public function companyRolesMacro(array $arguments = []): array {
        $companyRoles = new CompanyRoles($arguments);
        return $companyRoles->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the registeredUserMoveFormMacro.
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
    public function registeredUserMoveFormMacro(array $arguments = []): array {
        $registeredUserMoveForm = new RegisteredUserMoveForm($arguments);
        return $registeredUserMoveForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the registeredUserUpdateFormMacro.
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
    public function registeredUserUpdateFormMacro(array $arguments = []): array {
        $registeredUser = new RegisteredUserUpdateForm($arguments);
        return $registeredUser->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the registeredUserCreateFormMacro.
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
    public function registeredUserCreateFormMacro(array $arguments = []): array {
        $registeredUserCreateForm = new RegisteredUserCreateForm($arguments);
        return $registeredUserCreateForm->getViewParameters();
    }


    /**
     * This method merges the given arguments, calculates and returns the view parameters for the salesAgentCustomersMacro.
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
     */
    public function salesAgentSalesFormMacro(array $arguments = []): array {
        $salesAgentSalesForm = new SalesAgentSalesForm($arguments);
        return $salesAgentSalesForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the UsedAccountSwitch.
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
    public function usedAccountSwitchMacro(array $arguments = []): array {
        $usedAccountSwitch = new UsedAccountSwitch($arguments);
        return $usedAccountSwitch->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the accountOrders.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>accountOrders</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function accountOrdersMacro(array $arguments = []): array {
        $accountOrders = new AccountOrders($arguments);
        return $accountOrders->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the ordersForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>ordersForm</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function ordersFormMacro(array $arguments = []): array {
        $ordersForm = new OrdersForm($arguments);
        return $ordersForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the companyStructure.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>companyStructure</li>
     * <li>currentUser</li>
     * <li>permissions</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function companyStructureMacro(array $arguments = []): array {
        $companyStructure = new CompanyStructure();
        if (isset($arguments['companyStructure'])) {
            $companyStructure->setCompanyStructure($arguments['companyStructure']);
        }
        if (isset($arguments['currentUser'])) {
            $companyStructure->setCurrentUser($arguments['currentUser']);
        }
        if (isset($arguments['permissions'])) {
            $companyStructure->setPermissions($arguments['permissions']);
        }
        return $companyStructure->getViewParameters($arguments['parameters'] ?? []);
    }

    public function panelMacro(array $arguments = []): array {
        $panel = new Panel($arguments);
        return $panel->getViewParameters();
    }
    /**
     * This method merges the given arguments, calculates and returns the view parameters for the registeredUserApproveFormMacro.
     * The purpose of this macro is to encapsulate the logic to show a form used to approve a registered user.
     * <br>
     * The array keys of the returned parameters are:
     * <ul>
     * <li>registeredUserApproveForm</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function registeredUserApproveFormMacro(array $arguments = []): array {
        $registeredUserApproveForm = new RegisteredUserApproveForm($arguments);
        return $registeredUserApproveForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the approveRegisteredUserMacro.
     * The purpose of this macro is to encapsulate the logic to show a form used to approve a registered user.
     * <br>
     * The array keys of the returned parameters are:
     * <ul>
     * <li>approveRegisteredUser</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function approveRegisteredUserMacro(array $arguments = []): array {
        $approveRegisteredUser = new ApproveRegisteredUser($arguments);
        return $approveRegisteredUser->getViewParameters();
    }
}
