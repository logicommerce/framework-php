<?php

namespace FWK\Services;

use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Registries\RegistryService;
use FWK\Core\Resources\Session;
use FWK\Enums\Services;
use FWK\Services\Traits\ServiceTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Accounts\Account;
use SDK\Dtos\Accounts\AccountInvoicingAddress;
use SDK\Dtos\Accounts\AccountShippingAddress;
use SDK\Dtos\Accounts\MasterVal;
use SDK\Dtos\Accounts\RegisteredUser;
use SDK\Dtos\Accounts\SalesAgentCustomerData;
use SDK\Dtos\Basket\Basket;
use SDK\Enums\AccountKey;
use SDK\Services\AccountService as AccountServiceSDK;
use SDK\Services\Parameters\Groups\Account\Addresses\AccountInvoicingAddressCompatibleParametersGroup;
use SDK\Services\Parameters\Groups\Account\Addresses\AccountShippingAddressParametersGroup;
use SDK\Services\Parameters\Groups\User\SalesAgentCustomersParametersGroup;
use SDK\Services\Parameters\Groups\Account\UpdateAccountParametersGroup;
use SDK\Services\Parameters\Groups\Account\UpdateAccountRegisteredUsersParametersGroup;
use SDK\Services\Parameters\Groups\Account\UpdateRegisteredUserParametersGroup;

/**
 * This is the AccountService class.
 * A service is an extension of a SDK model that adds additional actions to a model request or creates new methods to simplify common requests.
 * In this case, the AccountService extends the SDK\Services\AccountService.
 *
 * @see AccountService::getAllSalesAgentCustomers()
 * @see AccountService::salesAgentLogin()
 * @see AccountService::salesAgentLogout()
 * @see AccountService::updateAccountsInvoicingAddresses()
 * @see AccountService::createAccountShippingAddresses()
 * @see AccountService::usedAccount()
 * @see AccountService::updateAccountById()
 * @see AccountService::updateAccountRegisteredUser()
 * @see AccountService::updateRegisteredUserMe()
 * 
 * @see AccountService
 * 
 * @package FWK\Services
 */
class AccountService extends AccountServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::ACCOUNT_SERVICE;

    /**
     * Returns all current sales agent customers
     *
     * @param SalesAgentCustomersParametersGroup $params
     *            object with the needed filters to send to the API user sales agent customers resource
     *
     * @return ElementCollection|NULL
     */
    public function getAllSalesAgentCustomers(SalesAgentCustomersParametersGroup $params = null): ?ElementCollection {
        if (is_null($params)) {
            $params = new SalesAgentCustomersParametersGroup();
        }
        return $this->getAllElementCollectionItems(SalesAgentCustomerData::class, 'SalesAgentCustomers', $params);
    }

    /**
     * Sales Agent - Login simulated account
     *
     * @param int $customerId
     *
     * @return Basket|NULL
     */
    public function salesAgentLogin(int $customerId): ?Basket {
        Session::getInstance()->resetShoppingList();
        return parent::salesAgentLogin($customerId);
    }

    /**
     * Sales Agent - Logout simulated account
     *
     * @return Basket|NULL
     */
    public function salesAgentLogout(): ?Basket {
        Session::getInstance()->resetShoppingList();
        return parent::salesAgentLogout();
    }

    /**
     * @see \SDK\Services\AccountService::updateAccountsInvoicingAddresses()
     */
    public function updateAccountsInvoicingAddresses(int $id, AccountInvoicingAddressCompatibleParametersGroup $data, string $dataValidatior = ''): ?AccountInvoicingAddress {
        $response =  parent::updateAccountsInvoicingAddresses($id, $data, $dataValidatior);
        if (is_null($response->getError())) {
            // refresh sessionBasket
            Loader::service(Services::BASKET)->getBasket();
        }
        return $response;
    }


    /**
     * @see \SDK\Services\AccountService::createAccountShippingAddresses()
     */
    public function createAccountShippingAddresses(string $idUsed, AccountShippingAddressParametersGroup $data, string $dataValidatior = ''): ?AccountShippingAddress {
        $response =  parent::createAccountShippingAddresses($idUsed, $data, $dataValidatior);
        if (is_null($response->getError())) {
            // refresh sessionBasket
            Loader::service(Services::BASKET)->getBasket();
        }
        return $response;
    }

    /**
     * Used Account - Login account
     *
     * @param int $accountId
     *
     * @return Basket|NULL
     */
    public function usedAccount(int $accountId): ?Basket {
        $response = parent::usedAccount($accountId);
        if (is_null($response->getError())) {
            Session::getInstance()->loginReset(is_null($response->getError()) ? $response : null);
        }
        Loader::service(Services::BASKET)->getBasket();

        return $response;
    }

    /**
     * Update account
     *
     * @param string $idUsed
     * @param UpdateAccountParametersGroup $data
     * @param string $dataValidator
     *
     * @return Basket|NULL
     */
    public function updateAccountById(string $idUsed, UpdateAccountParametersGroup $data,  string $dataValidator = ''): ?Account {
        $response = parent::updateAccount($idUsed, $data, $dataValidator);

        if (
            is_null($response->getError()) &&
            (
                $idUsed == AccountKey::USED ||
                $idUsed == Session::getInstance()->getBasket()->getAccountRegisteredUser()->getAccountId()
            )
        ) {
            Loader::service(Services::BASKET)->getBasket();
        }
        return $response;
    }

    /**
     * Update registered user
     * @param string $idUsed
     * @param int $registeredUserId
     * @param UpdateAccountRegisteredUsersParametersGroup $data
     * @return MasterVal
     */
    public function updateAccountRegisteredUser(string $idUsed, int $registeredUserId, UpdateAccountRegisteredUsersParametersGroup $data): ?MasterVal {
        $response = parent::updateAccountRegisteredUser($idUsed, $registeredUserId, $data);
        if (
            is_null($response->getError()) &&
            (
                (
                    (
                        $idUsed == AccountKey::USED ||
                        $idUsed == Session::getInstance()->getBasket()->getAccountRegisteredUser()->getAccountId()
                    ) &&
                    $registeredUserId == Session::getInstance()->getBasket()->getAccountRegisteredUser()->getRegisteredUserId()
                )
                ||
                isset($data->toArray()["master"])
            )
        ) {
            Loader::service(Services::BASKET)->getBasket();
        }
        return $response;
    }

    /**
     * Update registered user me
     * @param UpdateRegisteredUserParametersGroup $data
     * @return RegisteredUser
     */
    public function updateRegisteredUserMe(UpdateRegisteredUserParametersGroup $data): ?RegisteredUser {
        $response = parent::updateRegisteredUserMe($data);

        if (is_null($response->getError())) {
            Loader::service(Services::BASKET)->getBasket();
        }
        return $response;
    }
}
