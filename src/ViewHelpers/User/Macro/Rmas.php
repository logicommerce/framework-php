<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection;
use SDK\Enums\RMAStatus;

/**
 * This is the Rmas class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's rmas.
 *
 * @see Rmas::ACTION_RMA
 * @see Rmas::ACTION_RMA_PDF
 * @see Rmas::ACTION_RMA_RETURNS
 * @see Rmas::ACTION_RMA_RETURNS_PDF
 * 
 * @see Rmas::getViewParameters()
 * 
 * @package FWK\ViewHelpers\User\Macro
 */

class Rmas {

    public const ACTION_RMA = 'rma';

    public const ACTION_RMA_PDF = 'rmaPdf';

    public const ACTION_RMA_RETURNS = 'rmaReturns';

    public const ACTION_RMA_RETURNS_PDF = 'rmaReturnsPdf';

    public const ACTION_RMA_CORRECTIVE_INVOICE = 'rmaCorrectiveInvoice';

    public const ACTION_RMA_CORRECTIVE_INVOICE_PDF = 'rmaCorrectiveInvoicePdf';

    private const ACTIONS = [
        self::ACTION_RMA,
        self::ACTION_RMA_PDF,
        self::ACTION_RMA_RETURNS,
        self::ACTION_RMA_RETURNS_PDF,
        self::ACTION_RMA_CORRECTIVE_INVOICE,
        self::ACTION_RMA_CORRECTIVE_INVOICE_PDF,
    ];

    public const POPUP = 'popup';

    public const WINDOW = 'window';

    public ?ElementCollection $rmas = null;

    public int $userId = 0;

    public array $showRmasStates = [];

    public array $showRmasActions = [];

    public array $showRmasIcons = [];

    public string $documentView = self::POPUP;

    /**
     * Constructor method for Rmas.
     *
     * @see Rmas
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
        if(is_null($this->rmas)) {
            throw new CommerceException("The value of [rmas] argument: '" . $this->rmas . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        
        if($this->userId === 0) {
            throw new CommerceException("The value of [userId] argument: '" . $this->userId . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        foreach ($this->showRmasStates as $status) {
            if (!RMAStatus::isValid($status)) {
                throw new CommerceException("The value of [showRmasStates] argument: '" . $status . "' not exists in " . RMAStatus::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }

        foreach ($this->showRmasActions as $action) {
            if (!in_array($action, self::ACTIONS, true)) {
                throw new CommerceException("The value of [showRmasActions] argument: '" . $action . "' not exists in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
        }
        
        foreach ($this->showRmasIcons as $action) {
            if (!in_array($action, self::ACTIONS, true)) {
                throw new CommerceException("The value of [showRmasIcons] argument: '" . $action . "' not exists in " . self::class, CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
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
            'rmas' => $this->rmas,
            'userId' => $this->userId,
            'showRmasStates' => $this->showRmasStates,
            'showRmasActions' => $this->showRmasActions,
            'showRmasIcons' => $this->showRmasIcons,
            'documentView' => $this->documentView,
        ];
    }
}