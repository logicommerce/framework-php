<?php

namespace FWK\Core\Resources\Session;

use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Dtos\ElementCollection;
use FWK\Services\UserService;
use SDK\Services\Parameters\Groups\User\ShoppingListsParametersGroup;
use FWK\Core\Theme\Theme;

/**
 * This is the SessionShoppingList class.
 * The SessionShoppingList items will be stored in that class and will remain immutable (only get methods are available)
 *
 * @see SessionShoppingList::getDefaultOneId()
 *
 * @package SDK\Dtos
 */
class SessionShoppingList {

    private ?ElementCollection $shoppingLists = null;

    private bool $isInit = false;

    private int $defaultOneId = 0;

    private ?UserService $userService = null;

    /**
     * Returns the SessionShoppingList isInit value.
     *
     * @return void
     */
    public function init(): void {
        $this->userService = Loader::service(Services::USER);
        $params = new ShoppingListsParametersGroup();
        $this->userService->generateParametersGroupFromArray($params, Theme::getInstance()->getConfiguration()->getShoppingList()->getRowsList()->getDefaultParametersValues());
        $allShoppingLists = $this->userService->getAllShoppingLists($params);
        if (is_null($allShoppingLists->getError())) {
            $this->setShoppingLists($allShoppingLists);
            $this->isInit = true;
        } else {
            $this->isInit = false;
        }
    }

    /**
     * Returns if is isInit value.
     *
     * @return bool
     */
    public function isInit(): bool {
        return $this->isInit;
    }

    /**
     * Returns the defaultOneId value.
     *
     * @return int
     */
    public function getDefaultOneId(): int {
        if ($this->defaultOneId === 0) {
            $this->defaultOneId = Loader::service(Services::USER)->createDefaultShoppingList()->getId();
        }
        return $this->defaultOneId;
    }

    /**
     * Returns the shoppingLists value.
     *
     * @return NULL|ElementCollection
     */
    public function getShoppingLists(): ?ElementCollection {
        return $this->shoppingLists;
    }

    public function setShoppingLists(ElementCollection $shoppingLists) {
        $this->shoppingLists = $shoppingLists;
        foreach ($this->shoppingLists as $shoppingList) {
            if ($shoppingList->getDefaultOne()) {
                $this->defaultOneId = $shoppingList->getId();
                break;
            }
        }
    }
}
