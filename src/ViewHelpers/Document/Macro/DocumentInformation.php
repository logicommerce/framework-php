<?php

namespace FWK\ViewHelpers\Document\Macro;

use SDK\Dtos\Documents\Document;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the DocumentInformation class, a macro class for the document viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the document's DocumentInformation.
 *
 * @see DocumentInformation::getViewParameters()
 *
 * @package FWK\ViewHelpers\Document\Macro
 */
class DocumentInformation {

    public ?Document $document = null;

    public const TYPE_ORDER = 'order';
    
    public const TYPE_DELIVERY_NOTE = 'deliveryNote';

    public const TYPE_INVOICE = 'invoice';
    
    public const TYPE_RMA = 'rma';

    public const TYPE_RETURN = 'return';

    public const TYPE_CORRECTIVE_INVOICE = 'correctiveInvoice';

    private const DOCUMENT_TYPE_VALUES = [
        self::TYPE_ORDER,
        self::TYPE_DELIVERY_NOTE,
        self::TYPE_INVOICE,
        self::TYPE_RMA,
        self::TYPE_RETURN,
        self::TYPE_CORRECTIVE_INVOICE
    ];

    public bool $showTransactionId = false;

    public bool $showAuthNumber = false;

    public string $documentType = self::TYPE_ORDER; //order, rma, return, correctiveInvoice

    /**
     * Constructor method for DocumentInformation class.
     *
     * @see DocumentInformation
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for DocumentViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->document)) {
            throw new CommerceException("The value of [document] argument: '" . $this->document . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!in_array($this->documentType, self::DOCUMENT_TYPE_VALUES, true)) {
            throw new CommerceException("The value of [documentType] argument: '" . $this->documentType . "' is required " . self::class,
            CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'document' => $this->document,
            'showTransactionId' => $this->showTransactionId,
            'showAuthNumber' => $this->showAuthNumber,
            'documentType' => $this->documentType,
        ];
    }
}
