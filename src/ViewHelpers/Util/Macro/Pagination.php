<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\Pagination as CoreDtoPagination;
use FWK\Core\Theme\Dtos\Pagination as CoreThemePagination;
use FWK\Enums\Pagination as EnumPagination;

/**
 * This is the Pagination class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's pagination.
 *
 * @see Pagination::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class Pagination {

    public const MODE_BLOCKS = 'modeBlocks';

    public const MODE_TEXT = 'modeText';

    public ?CoreDtoPagination $pagination = null;

    public ?CoreThemePagination $pagerParameters = null;

    public string $mode = self::MODE_BLOCKS;

    private array $paginationItems = [];

    /**
     * Constructor method for Pagination
     *
     * @see Pagination
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UtilViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->pagination)) {
            throw new CommerceException("The value of [pagination] argument: '" . $this->pagination . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (is_null($this->pagerParameters)) {
            throw new CommerceException("The value of [pagerParameters] argument: '" . $this->pagerParameters . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->paginationItems = $this->getPaginationItems();

        return $this->getProperties();
    }

    /**
     * Return array of pagination items for html output loop
     * 
     * @internal Original algorithm https://gist.github.com/kottenator/9d936eb3e4e3c3e02598
     *
     * @return array
     */
    private function getPaginationItems(): array {
        $items = [];

        // Algorithm
        $current = $this->pagination->getPage();
        $last = $this->pagination->getTotalPages();
        $delta = $this->pagerParameters->getPagesToShow() - 3;
        $left = $current - $delta;
        $right = $current + $delta + 1;
        $range = [];
        $rangeWithDots = [];
        $l = null;

        for ($i = 1; $i <= $last; $i++) {
            if ($i == 1 || $i == $last || $i >= $left && $i < $right) {
                $range[] = $i;
            }
        }

        foreach ($range as $i) {
            if ($l !== null) {
                if ($i - $l === 2) {
                    $rangeWithDots[] = $l + 1;
                } else if ($i - $l !== 1) {
                    $rangeWithDots[] = '...';
                }
            }
            $rangeWithDots[] = $i;
            $l = $i;
        }
        // End algorithm

        if ($current > 1) {
            $items[] = $this->getPaginationItem(EnumPagination::TYPE_BEFORE, $current - 1);
        }
        foreach ($rangeWithDots as $value) {
            if ($value === '...') {
                $items[] = $this->getPaginationItem(EnumPagination::TYPE_SEPARATOR, 0);
            } else {
                $items[] = $this->getPaginationItem(EnumPagination::TYPE_PAGE, $value, $value === $current);
            }
        }
        if ($current < $last) {
            $items[] = $this->getPaginationItem(EnumPagination::TYPE_AFTER, $current + 1);
        }

        return $items;
    }

    /**
     * Create object paginationItem
     *
     * @param string $type
     * @param int $page
     * @param bool $selected
     *
     * @return array
     */
    private function getPaginationItem(string $type, int $page, bool $selected = false): array {
        if ($type === EnumPagination::TYPE_AFTER || $type === EnumPagination::TYPE_BEFORE) {
            if ($this->pagerParameters->getLinkIsFollow()) {
                $item['rel'] = 'follow';
            } else {
                $item['rel'] = 'nofollow';
            }
        }
        $item['type'] = $type;
        $item['page'] = $page;
        $item['selected'] = $selected;
        return $item;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'pagination' => $this->pagination,
            'pagerParameters' => $this->pagerParameters,
            'paginationItems' => $this->paginationItems,
            'mode' => $this->mode
        ];
    }
}
