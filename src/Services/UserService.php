<?php

namespace FWK\Services;

use FWK\Core\Resources\Language;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Registries\RegistryService;
use SDK\Services\UserService as UserServiceSDK;
use FWK\Core\Resources\Session;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\Traits\ServiceTrait;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Dtos\IncidenceSaveForLaterListRowsCollection;
use SDK\Core\Dtos\Status;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Basket\Basket;
use SDK\Dtos\User\Address;
use SDK\Dtos\User\SalesAgentCustomer;
use SDK\Dtos\User\ShippingAddress;
use SDK\Dtos\User\ShoppingList;
use SDK\Dtos\User\ShoppingListRow;
use SDK\Enums\ListRowReferenceType;
use SDK\Services\Parameters\Groups\User\Addresses\AddressParametersGroup;
use SDK\Services\Parameters\Groups\User\Addresses\ShippingAddressParametersGroup;
use SDK\Services\Parameters\Groups\User\AddSaveForLaterListRowsParametersGroup;
use SDK\Services\Parameters\Groups\User\AddShoppingListParametersGroup;
use SDK\Services\Parameters\Groups\User\AddShoppingListRowParametersGroup;
use SDK\Services\Parameters\Groups\User\AddShoppingListRowReferenceParametersGroup;
use SDK\Services\Parameters\Groups\User\AddShoppingListRowsParametersGroup;
use SDK\Services\Parameters\Groups\User\DeleteShoppingListRowsParametersGroup;
use SDK\Services\Parameters\Groups\User\LoginParametersGroup;
use SDK\Services\Parameters\Groups\User\SalesAgentCustomersParametersGroup;
use SDK\Services\Parameters\Groups\User\ShoppingListRowsParametersGroup;
use SDK\Services\Parameters\Groups\User\ShoppingListsParametersGroup;
use SDK\Core\Dtos\IncidencesDeleteItem;

/**
 * This is the UserService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the UserService extends the SDK\Services\Service.
 *
 * @see UserService::__call()
 *
 * @see Service
 *
 * @package FWK\Services
 */
class UserService extends UserServiceSDK {
    use ServiceTrait;

    private const REGISTRY_KEY = RegistryService::USER_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    /**
     *
     * @see SDK\Services\UserService::login()
     */
    public function login(LoginParametersGroup $data = null): ?Basket {
        $response = parent::login($data);
        if (is_null($response->getError()) || $response->getError()->getCode() === 'A01000-USER_IS_LOGGED_IN') {
            Session::getInstance()->loginReset(is_null($response->getError()) ? $response : null);
        }
        Loader::service(Services::BASKET)->getBasket();
        return $response;
    }

    /**
     * 
     * 
     *
     * @see SDK\Services\UserService::logout()
     */
    public function logout(): ?Basket {
        $response = parent::logout();
        if (is_null($response->getError()) || (!is_null($response->getError()) && $response->getError()->getCode() === 'LOGIN_REQUIRED')) {
            Session::getInstance()->loginReset(is_null($response->getError()) ? $response : null);
        }
        return $response;
    }

    /**
     * Returns all current sales agent customers
     *
     * @param SalesAgentCustomersParametersGroup $params
     *            object with the needed filters to send to the API user sales agent customers resource
     *
     * @return ElementCollection|NULL
     */
    public function getAllSalesAgentCustomers(SalesAgentCustomersParametersGroup $params = null): ?ElementCollection {
        // trigger_error("The function 'getAllSalesAgentCustomers' will be deprecated soon. you must use AccountService 'getAllSalesAgentCustomers'");
        if (is_null($params)) {
            $params = new SalesAgentCustomersParametersGroup();
        }
        return $this->getAllElementCollectionItems(SalesAgentCustomer::class, 'SalesAgentCustomers', $params);
    }

    /**
     *
     * @see \SDK\Services\UserService::updateBillingAddress()
     * @deprecated use AccountService::updateAccountsInvoicingAddresses
     */
    public function updateBillingAddress(int $id, AddressParametersGroup $data = null, string $dataValidatior = ''): ?Address {
        $response =  parent::updateBillingAddress($id, $data, $dataValidatior);
        if (is_null($response->getError())) {
            // refresh sessionBasket
            Loader::service(Services::BASKET)->getBasket();
        }
        return $response;
    }

    /**
     *
     * @see \SDK\Services\UserService::createShippingAddress()
     * @deprecated use AccountService::createAccountShippingAddresses
     */
    public function createShippingAddress(ShippingAddressParametersGroup $data = null, string $dataValidatior = ''): ?ShippingAddress {
        $response =  parent::createShippingAddress($data, $dataValidatior);
        if (is_null($response->getError())) {
            // refresh sessionBasket
            Loader::service(Services::BASKET)->getBasket();
        }
        return $response;
    }

    /**
     *
     * @see \SDK\Services\UserService::addSaveForLaterListRows()
     */
    public function addSaveForLaterListRows(AddSaveForLaterListRowsParametersGroup $data): ?IncidenceSaveForLaterListRowsCollection {
        $response =  parent::addSaveForLaterListRows($data);
        if (is_null($response->getError())) {
            // refresh sessionBasket
            Loader::service(Services::BASKET)->getBasket();
        }
        return $response;
    }

    /**
     *
     * @see \SDK\Services\UserService::transferToBasketSaveForLaterListRow()
     */
    public function transferToBasketSaveForLaterListRow(int $id): ?Status {
        $response =  parent::transferToBasketSaveForLaterListRow($id);
        if (is_null($response->getError())) {
            // refresh sessionBasket
            Loader::service(Services::BASKET)->getBasket();
        }
        return $response;
    }

    /**
     *
     * @see \SDK\Services\UserService::createShoppingList()
     */
    public function createShoppingList(AddShoppingListParametersGroup $data): ?ShoppingList {
        $result = parent::createShoppingList($data);
        if (is_null($result->getError())) {
            Session::getInstance()->updateShoppingList();
        }
        return $result;
    }

    /**
     *
     * @see \SDK\Services\UserService::updateShoppingList()
     */
    public function updateShoppingList(int $id, AddShoppingListParametersGroup $data): ?ShoppingList {
        $result = parent::updateShoppingList($id, $data);
        if (is_null($result->getError())) {
            Session::getInstance()->updateShoppingList();
        }
        return $result;
    }

    /**
     *
     * @see \SDK\Services\UserService::deleteShoppingList()
     */
    public function deleteShoppingList(int $id): ?Status {
        $result = parent::deleteShoppingList($id);
        if (is_null($result->getError())) {
            Session::getInstance()->updateShoppingList();
        }
        return $result;
    }

    /**
     * Returns all available Shopping Lists filtered with the given parameters
     *
     * @param ShoppingListsParametersGroup $params
     *            object with the needed filters to send to the API ShoppingLists resource
     *
     * @return ElementCollection|NULL
     */
    public function getAllShoppingLists(ShoppingListsParametersGroup $shoppingListsParametersGroup = null): ?ElementCollection {
        if (is_null($shoppingListsParametersGroup)) {
            $shoppingListsParametersGroup = new ShoppingListsParametersGroup();
        }
        return $this->getAllElementCollectionItems(ShoppingList::class, 'ShoppingLists', $shoppingListsParametersGroup);
    }

    /**
     * Returns all available Shopping ListRows filtered with the given parameters
     *
     * @param ShoppingListRowsParametersGroup $params
     *            object with the needed filters to send to the API ShoppingListRows resource
     *
     * @return ElementCollection|NULL
     */
    public function getAllShoppingListRows(ShoppingListRowsParametersGroup $shoppingListRowsParametersGroup = null): ?ElementCollection {
        if (is_null($shoppingListRowsParametersGroup)) {
            $shoppingListRowsParametersGroup = new ShoppingListRowsParametersGroup();
        }
        return $this->getAllElementCollectionItems(ShoppingListRow::class, 'ShoppingListRows', $shoppingListRowsParametersGroup);
    }

    /**
     * Create the default shopping List
     *
     * @return ShoppingList|NULL
     */
    public function createDefaultShoppingList(): ?ShoppingList {
        $addShoppingListParametersGroup = new AddShoppingListParametersGroup();
        $addShoppingListParametersGroup->setDefaultOne(true);
        $addShoppingListParametersGroup->setName(Language::getInstance()->getLabelValue(LanguageLabels::DEFAULT_SHOPPING_LIST_NAME));
        $response = $this->createShoppingList($addShoppingListParametersGroup);
        if (is_null($response->getError())) {
            Session::getInstance()->updateShoppingList(
                new ElementCollection(['items' => [$response]])
            );
        }
        return $response;
    }

    /**
     * Returns the products from the default shopping list
     *
     * @return ElementCollection|NULL
     */
    public function getDefaultShoppingListRows(ShoppingListRowsParametersGroup $shoppingListRowsParametersGroup = null): ?ElementCollection {
        if (is_null($shoppingListRowsParametersGroup)) {
            $shoppingListRowsParametersGroup = new ShoppingListRowsParametersGroup();
        }
        return $this->getShoppingListRows(Session::getInstance()->getShoppingList()->getDefaultOneId(), $shoppingListRowsParametersGroup);
    }

    /**
     * Adds the given product Id to the default shopping list
     * 
     * @param int $id productId to add to the default shopping list
     *
     * @return ElementCollection|NULL
     */
    public function addProductToDefaultShoppingListRows(int $id): ?ElementCollection {
        $addShoppingListRowReferenceParametersGroup = new AddShoppingListRowReferenceParametersGroup();
        $addShoppingListRowParametersGroup = new AddShoppingListRowParametersGroup();
        $addShoppingListRowReferenceParametersGroup->setId($id);
        $addShoppingListRowReferenceParametersGroup->setType(ListRowReferenceType::PRODUCT);
        $addShoppingListRowParametersGroup->setReference($addShoppingListRowReferenceParametersGroup);
        $addShoppingListRowsParametersGroup = new AddShoppingListRowsParametersGroup();
        $addShoppingListRowsParametersGroup->addItem($addShoppingListRowParametersGroup);
        $defaultOneId = Session::getInstance()->getShoppingList()->getDefaultOneId();
        $status = $this->createShoppingListRow($defaultOneId, $addShoppingListRowsParametersGroup);
        return $status;
    }

    /**
     *
     * @see SDK\Dtos\User\User::createShoppingListRow()
     */
    public function createShoppingListRow(int $id, AddShoppingListRowsParametersGroup $data): ?ElementCollection {
        $elementCollection = parent::createShoppingListRow($id, $data);
        $session = Session::getInstance();
        if (is_null($elementCollection->getError()) &&  $id === $session->getShoppingList()->getDefaultOneId()) {
            foreach ($data->toArray()[Parameters::ITEMS] as $item) {
                if (isset($item[Parameters::REFERENCE]) && !empty($item[Parameters::REFERENCE])) {
                    $session->setAggregateDataShoppingLists($item[Parameters::REFERENCE][Parameters::ID], $item[Parameters::REFERENCE][Parameters::TYPE],  self::POST);
                }
            }
        }
        return $elementCollection;
    }

    /**
     * Delete from the default shopping list, all rows that refer to the given product Id.
     *
     * @return IncidencesDeleteItem|NULL
     */
    public function deleteProductFromDefaultShoppingListRow(int $id): IncidencesDeleteItem|NULL {
        $deleteShoppingListRowsParametersGroup =  new DeleteShoppingListRowsParametersGroup();
        $deleteShoppingListRowsParametersGroup->setProductIdList([$id]);
        return $this->deleteShoppingListRows(Session::getInstance()->getShoppingList()->getDefaultOneId(), $deleteShoppingListRowsParametersGroup);
    }

    /**
     *
     * @see SDK\Dtos\User\User::addGetWishlist()
     */
    public function addGetDefaultShoppingListRows(BatchRequests $batchRequests, string $batchName, ShoppingListRowsParametersGroup $params = null): void {
        $this->addGetShoppingListRows($batchRequests, $batchName, Session::getInstance()->getShoppingList()->getDefaultOneId(), $params);
    }

    /**
     * 
     *
     * @see SDK\Dtos\User\User::getWishlist()
     */
    public function getWishlist(): ?ElementCollection {
        // trigger_error("The function 'getWishlist' will be deprecated soon. you must use 'getDefaultShoppingListRows'");
        $result = new ElementCollection();
        if (Session::getInstance()->getShoppingList()->getDefaultOneId() > 0) {
            $shoppingListRows = $this->getShoppingListRows(Session::getInstance()->getShoppingList()->getDefaultOneId());
            if (is_null($shoppingListRows->getError())) {
                $result = new ElementCollection(['items' => $shoppingListRows->getProducts()]);
            }
        }
        return $result;
    }

    /**
     *
     * @see SDK\Dtos\User\User::addWishlistProduct()
     */
    public function addWishlistProduct(int $id): ?Status {
        // trigger_error("The function 'addWishlistProduct' will be deprecated soon. you must use 'addToDefaultShoppingListRows'");
        $response = $this->addProductToDefaultShoppingListRows($id);
        if (is_null($response->getError())) {
            if (empty($response->getIncidences())) {
                $status = new Status();
            } else {
                $detail = $response->getIncidences()[0]->getDetail();
                if ($detail->getCode() == 'SHOPPING_LIST_ROW_EXISTS') {
                    $status = new Status();
                } else {
                    $status = new Status([
                        'error' => [
                            'reference' => $detail->getReference(),
                            'status' => $detail->getStatus(),
                            'code' => $detail->getCode(),
                            'message' => $detail->getMessage()
                        ]
                    ]);
                }
            }
        } else {
            $status = new Status(['error' => $response->getError()->toArray()]);
        }
        return $status;
    }

    /**
     *
     * @see SDK\Dtos\User\User::deleteWishlistProduct()
     */
    public function deleteWishlistProduct(int $id): ?Status {
        // trigger_error("The function 'deleteWishlistProduct' will be deprecated soon. you must use 'deleteFromDefaultShoppingListRow'");
        $response = $this->deleteProductFromDefaultShoppingListRow($id);
        if (is_null($response->getError()) || $response->getError()->getCode() == 'A01000-WISHLIST_DEL_PRODUCT_NOT_EXISTS') {
            Session::getInstance()->setAggregateDataShoppingLists($id, ListRowReferenceType::PRODUCT, self::DELETE);
            return new Status();
        } else {
            return new Status([
                'error' => $response->getError()->toArray()
            ]);
        }
    }

    /**
     * Sales Agent - Login simulated user
     *
     * @param int $customerId
     *
     * @return Basket|NULL
     */
    public function salesAgentLogin(int $customerId): ?Basket {
        // trigger_error("The function 'salesAgentLogin' will be deprecated soon. you must use AccountService 'salesAgentLogin'");
        Session::getInstance()->resetShoppingList();
        return parent::salesAgentLogin($customerId);
    }

    /**
     * Sales Agent - Logout simulated user
     *
     * @return Basket|NULL
     */
    public function salesAgentLogout(): ?Basket {
        // trigger_error("The function 'salesAgentLogout' will be deprecated soon. you must use AccountService 'salesAgentLogout'");
        Session::getInstance()->resetShoppingList();
        return parent::salesAgentLogout();
    }
}
