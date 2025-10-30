<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use FWK\Core\Dtos\Traits\RichDocumentPrices;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Documents\Document;
use SDK\Enums\DocumentCurrencyMode;
use SDK\Enums\DocumentRowType;

/**
 * This is the Order class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class Order {
    use ElementTrait, RichDocumentPrices;

    private int $id = 0;

    private array $rows = [];

    private ?OrderTotals $totals = null;

    private string $currency = '';

    private string $documentNumber = '';

    /**
     * Constructor method for Order
     *
     * @param Document $order
     */
    public function __construct(Document $order) {
        if ($order->getId() > 0) {
            $this->id = $order->getId();
            $navigationCurrency = array_filter($order->getCurrencies(), fn ($currency) => $currency->getMode() === DocumentCurrencyMode::PURCHASE);
            $navigationCurrency = array_shift($navigationCurrency);
            $this->currency = $navigationCurrency->getCode();
            $this->documentNumber = $order->getDocumentNumber();
            $this->totals = new OrderTotals($order->getTotals());
            $this->setDocuemntRowRichPrices($order);
            foreach ($order->getItems() as $item) {
                switch ($item->getType()) {
                    case DocumentRowType::BUNDLE:
                        $this->rows[] = new OrderRowBundle($item);
                        break;
                    case DocumentRowType::LINKED:
                        $this->rows[] = new OrderRowLinked($item);
                        break;
                    case DocumentRowType::GIFT:
                        $this->rows[] = new OrderRowGift($item);
                        break;
                    case DocumentRowType::VOUCHER_PURCHASE:
                        $this->rows[] = new OrderRowVoucherPurchase($item);
                        break;
                    default:
                        $this->rows[] = new OrderRowProduct($item);
                        break;
                }
            }
        }
    }
}
