<?php

namespace FWK\ViewHelpers\Account\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection;
use SDK\Enums\OrderStatus;

/**
 * This is the AccountOrders class, a macro class for the account view helper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's account orders.
 *
 * @see AccountOrders::getViewParameters()
 *
 * @package FWK\ViewHelpers\Account\Macro
 */
class AccountOrders {

    public const ACTION_VIEW = 'view';

    public const ACTION_VIEW_PDF = 'viewPdf';

    public const ACTION_RECOVER = 'recover';

    public const ACTION_DELIVERY_NOTE = 'deliveryNote';

    public const ACTION_DELIVERY_NOTE_PDF = 'deliveryNotePDF';

    public const ACTION_INVOICE = 'invoice';

    public const ACTION_INVOICE_PDF = 'invoicePDF';

    public const ACTION_RETURN = 'return';

    public const ACTION_RETURN_TRACING = 'returnTracing';

    public const ACTION_APPROVAL = 'approval';

    private const ACTIONS = [
        self::ACTION_VIEW,
        self::ACTION_VIEW_PDF,
        self::ACTION_RECOVER,
        self::ACTION_DELIVERY_NOTE,
        self::ACTION_DELIVERY_NOTE_PDF,
        self::ACTION_INVOICE,
        self::ACTION_INVOICE_PDF,
        self::ACTION_RETURN,
        self::ACTION_RETURN_TRACING,
        self::ACTION_RETURN_TRACING,
        self::ACTION_APPROVAL
    ];

    public const POPUP = 'popup';

    public const WINDOW = 'window';

    public const SHOW_STATUS_ALL = 'showStatusAll';

    public const SHOW_STATUS_ONLY = 'showStatusOnly';

    public const SHOW_STATUS_PRIORITY_SUBSTATUS = 'showStatusPrioritySubstatus';

    private const SHOW_STATUS = [
        self::SHOW_STATUS_ALL,
        self::SHOW_STATUS_ONLY,
        self::SHOW_STATUS_PRIORITY_SUBSTATUS,
    ];

    public ?ElementCollection $orders = null;

    public int $userId = 0;

    public array $showOrderStates = [];

    public array $showOrderActions = [];

    public array $showOrderIcons = [];

    public string $documentView = self::POPUP;

    public string $returnProductsView = self::POPUP;

    public string $returnTracingView = self::POPUP;

    public string $showStatus = self::SHOW_STATUS_ALL;

    /**
     * Constructor method for AccountOrders.
     *
     * @see AccountOrders
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->orders)) {
            throw new CommerceException("The value of [orders] argument: '" . $this->orders . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        if ($this->userId === 0) {
            throw new CommerceException("The value of [userId] argument: '" . $this->userId . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        foreach ($this->showOrderStates as $status) {
            if (!OrderStatus::isValid($status)) {
                throw new CommerceException("The value of [showOrderStates] argument: '" . $status . "' not exists in " . OrderStatus::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }

        foreach ($this->showOrderActions as $action) {
            if (!in_array($action, self::ACTIONS, true)) {
                throw new CommerceException("The value of [showOrderActions] argument: '" . $action . "' not exists in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }

        foreach ($this->showOrderIcons as $action) {
            if (!in_array($action, self::ACTIONS, true)) {
                throw new CommerceException("The value of [showOrderIcons] argument: '" . $action . "' not exists in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }

        if ($this->documentView !== self::POPUP && $this->documentView !== self::WINDOW) {
            throw new CommerceException("The value of [documentView] argument: '" . $this->documentView . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        if ($this->returnTracingView !== self::POPUP && $this->returnTracingView !== self::WINDOW) {
            throw new CommerceException("The value of [returnTracingView] argument: '" . $this->returnTracingView . "' not exists in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }

        if ($this->returnProductsView !== self::POPUP && $this->returnProductsView !== self::WINDOW) {
            throw new CommerceException("The value of [returnProductsView] argument: '" . $this->returnProductsView . "' not exists in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }

        if (!in_array($this->showStatus, self::SHOW_STATUS, true)) {
            throw new CommerceException("The value of [showStatus] argument: '" . $this->showStatus . "' not exists in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
        }

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'orders' => $this->orders,
            'userId' => $this->userId,
            'showOrderStates' => $this->showOrderStates,
            'showOrderActions' => $this->showOrderActions,
            'showOrderIcons' => $this->showOrderIcons,
            'documentView' => $this->documentView,
            'returnProductsView' => $this->returnProductsView,
            'returnTracingView' => $this->returnTracingView,
            'showStatus' => $this->showStatus,
        ];
    }
}
