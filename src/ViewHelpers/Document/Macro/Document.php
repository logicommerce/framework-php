<?php

namespace FWK\ViewHelpers\Document\Macro;

use FWK\Core\Dtos\Traits\RichDocumentPrices;
use SDK\Dtos\Documents\Document as SDKDocument;
use SDK\Enums\BasketRowType;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\Macros\Basket\Macro\BaseOutput;
use FWK\Dtos\Documents\Document as FWKDocument;
use FWK\ViewHelpers\Document\Macro\Output\Disclosure;
use FWK\ViewHelpers\Document\Macro\Output\Footer;
use SDK\Enums\DocumentRowType;

/**
 * This is the Document class, a macro class for the document viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the document output.
 *
 * @see Document::getViewParameters()
 *
 * @package FWK\ViewHelpers\Document\Macro
 */
class Document extends BaseOutput {
    use RichDocumentPrices;

    public ?SDKDocument $document = null;

    public bool $showDiscountValue = false;

    public bool $mergeRows = false;

    public array $showCustomTagPositions = [];

    private array $mergedRows = [];

    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->document)) {
            throw new CommerceException('The value of [document] argument: "' . $this->document . '" is required in ' . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        } else {
            $this->validateMainParams();
        }

        if (!($this->document instanceof FWKDocument)) {
            $this->document = FWKDocument::fillFromParent($this->document);
        }

        $this->setCalculatedAttributes($this->document);
        $this->setDocuemntRowRichPrices($this->document);

        if ($this->mergeRows) {
            $this->setMergedRows();
        }

        return $this->getProperties();
    }

    protected function setMergedRows(): void {
        $mergedRows = [];        
        foreach ($this->document->getItems() as $item) {
            if ($item->getType() == DocumentRowType::PRODUCT) {
                if (!isset($mergedRows[$item->getItemId()])) {
                    $mergedRows[$item->getItemId()] = [];
                    $mergedRows[$item->getItemId()]['hashes'] = [];
                    $mergedRows[$item->getItemId()]['mergeableOptions'] = [];
                }
                $mergedRows[$item->getItemId()]['hashes'][] = $item->getHash();
                foreach ($item->getOptions() as $option) {
                    if (!$mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]) {
                        $mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()] = [];
                        $mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]['mergeable'] = null;
                        $mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]['values'] = [];
                    }
                    if (is_null($mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]['mergeable'])) {
                        foreach ($option->getValues() as $value) {
                            $mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]['values'][] = $value->getValue();
                        }
                        $mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]['mergeable'] = true;
                    } else if (
                        $mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]['mergeable'] === true
                        && count($mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]['values']) == count($option->getValues())
                    ) {
                        foreach ($option->getValues() as $value) {
                            if (!in_array($value->getValue(), $mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]['values'])) {
                                $mergedRows[$item->getItemId()]['mergeableOptions'][$option->getOptionId()]['mergeable'] = false;
                                break;
                            }
                        }
                    }
                }
            }
        }
        $this->mergedRows['hashes'] = [];
        foreach ($mergedRows as $productId => $mergedRow) {
            if (count($mergedRow['hashes']) > 1) {
                $this->mergedRows['hashes'] = array_merge($this->mergedRows['hashes'], $mergedRow['hashes']);
                $this->mergedRows[$productId] = [];
                $this->mergedRows[$productId]['hashes'] = $mergedRow['hashes'];
                $this->mergedRows[$productId]['mergeableOptions'] = [];
                $this->mergedRows[$productId]['noMergeableOptions'] = [];
                foreach ($mergedRows[$productId]['mergeableOptions'] as $optionId => $mergeableOption) {
                    if ($mergeableOption['mergeable'] === true) {
                        $this->mergedRows[$productId]['mergeableOptions'][] = $optionId;
                    } else {
                        $this->mergedRows[$productId]['noMergeableOptions'][] = $optionId;
                    }
                }
            }
        }

        $this->mergeRows = !empty($this->mergedRows);
    }

    protected function setFooter(): void {
        $footer = new Footer($this, $this->languageSheet);
        $this->footerRows = $footer->getRows();
    }

    protected function setDisclosure(): void {
        if ($this->document instanceof SDKDocument) {
            $disclosure = new Disclosure($this);
            $this->disclosure = $disclosure->getData();
        }
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return parent::getProperties() + [
            'document' => $this->document,
            'showCustomTagPositions' => $this->showCustomTagPositions,
            'showDiscountValue' => $this->showDiscountValue,
            'mergeRows' => $this->mergeRows,
            'mergedRows' => $this->mergedRows,
        ];
    }

    /**
     * Get documentRow classes for <tr> html
     *
     * @param $documentRow
     *
     * @return string
     */
    protected function getRowClassName(&$documentRow): string {
        $className = 'order orderProduct';
        if ($documentRow->getType() === BasketRowType::LINKED) {
            $className .= ' linkedBasketProduct';
        } elseif ($documentRow->getType() === BasketRowType::BUNDLE) {
            $className .= ' orderBundleProduct';
        } elseif ($documentRow->getType() === BasketRowType::GIFT) {
            $className .= ' orderGift';
        } elseif ($documentRow->getType() === BasketRowType::VOUCHER_PURCHASE) {
            $className .= ' orderVoucherPurchase';
        } 
        return $className;
    }

    /**
     * 
     *
     */
    protected function setTotalProductDiscounts(): void {
        $totalOrderRowTotalDiscounts = 0;
        foreach ($this->document->getItems() as $orderRow) {
            $totalDiscounts = 0;
            if ($orderRow->getType() === BasketRowType::BUNDLE) {
                foreach ($orderRow->getItems() as $item) {
                    foreach ($item->getDiscounts() as $discount) {
                        $totalOrderRowTotalDiscounts += $discount->getValue();
                        $totalDiscounts += $discount->getValue();
                    }
                }
            } else {
                foreach ($orderRow->getDiscounts() as $discount) {
                    $totalOrderRowTotalDiscounts += $discount->getValue();
                    $totalDiscounts += $discount->getValue();
                }
            }
            $orderRow->setTotalDiscounts($totalDiscounts);
        }
        $this->totalProductDiscounts = $totalOrderRowTotalDiscounts;
    }
}
