<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use FWK\Core\Resources\Session;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Basket\Basket as BasketDto;
use SDK\Enums\BasketRowType;

/**
 * This is the Basket class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
class Basket {
    use ElementTrait;

    private string $currency = '';

    private array $rows = [];

    private ?BasketTotals $totals = null;

    public ?array $warnings = null;

    /**
     * Constructor method for Basket
     *
     * @param BasketDto $basket
     */
    public function __construct(BasketDto $basket) {
        if (!is_null($basket->getBasketUser()?->getUser())) {
            $this->currency = $basket->getBasketUser()->getUser()->getCurrencyCode();
        }
        foreach ($basket->getItems() as $item) {
            switch ($item->getType()) {
                case BasketRowType::BUNDLE:
                    $this->rows[] = new BasketRowBundle($item);
                    break;
                case BasketRowType::LINKED:
                    $this->rows[] = new BasketRowLinked($item);
                    break;
                default:
                    $this->rows[] = new BasketRowProduct($item);
                    break;
            }
        }
        $this->totals = new BasketTotals($basket->getTotals());
        $this->warnings = $this->getWarnings();
    }

    private function getWarnings(): ?array {
        $warnings = $_SESSION[Session::WARNING] ?? null;
        if ($warnings !== null) {
            Session::getInstance()->clearWarning();
            return $warnings;
        }
        return null;
    }
}
