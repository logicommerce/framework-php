<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;
use FWK\Enums\Pagination as EnumPagination;

/**
 * This is the Pagination DTO class.
 * The pagination items will be stored in that class and will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Pagination::getPagesToShow()
 * @see Pagination::getBeforeClass()
 * @see Pagination::getBeforeLabel()
 * @see Pagination::getPageClass()
 * @see Pagination::getPageLabel()
 * @see Pagination::getClassSelected()
 * @see Pagination::getAfterClass()
 * @see Pagination::getAfterLabel()
 * @see Pagination::getSeparatorClass()
 * @see Pagination::getSeparatorLabel()
 * @see Pagination::getLinkTarget()
 * @see Pagination::getLinkIsFollow()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class Pagination extends Element {
    use ElementTrait;

    public const PAGES_TO_SHOW = EnumPagination::PAGES_TO_SHOW;

    public const BEFORE_CLASS = EnumPagination::BEFORE_CLASS;

    public const BEFORE_LABEL = EnumPagination::BEFORE_LABEL;

    public const PAGE_CLASS = EnumPagination::PAGE_CLASS;

    public const PAGE_LABEL = EnumPagination::PAGE_LABEL;

    public const SELECTED_CLASS = EnumPagination::SELECTED_CLASS;

    public const AFTER_CLASS = EnumPagination::AFTER_CLASS;

    public const AFTER_LABEL = EnumPagination::AFTER_LABEL;

    public const SEPARATOR_CLASS = EnumPagination::SEPARATOR_CLASS;

    public const SEPARATOR_LABEL = EnumPagination::SEPARATOR_LABEL;

    public const LINK_TARGET = EnumPagination::LINK_TARGET;

    public const LINK_IS_FOLLOW = EnumPagination::LINK_IS_FOLLOW;

    private int $pagesToShow = 5;
    private string $beforeClass = '';
    private string $beforeLabel = '';
    private string $pageClass = '';
    private string $pageLabel = '';
    private string $selectedClass = '';
    private string $afterClass = '';
    private string $afterLabel = '';
    private string $separatorClass = '';
    private string $separatorLabel = '';
    private string $linkTarget = '_self';
    private bool $linkIsFollow = true;



    /**
     * Returns the number of linkable pages.
     *
     * @return int
     */
    public function getPagesToShow(): int {
        return $this->pagesToShow;
    }

    private function setPagesToShow(int $pagesToShow): void {
        $this->pagesToShow = $pagesToShow;
    }

    /**
     * Returns the class to insert into the before link.
     *
     * @return string
     */
    public function getBeforeClass(): string {
        return $this->beforeClass;
    }

    private function setBeforeClass(string $beforeClass): void {
        $this->beforeClass = $beforeClass;
    }

    /**
     * Returns the label to insert into the before link.
     *
     * @return string
     */
    public function getBeforeLabel(): string {
        return $this->beforeLabel;
    }

    private function setBeforeLabel(string $beforeLabel): void {
        $this->beforeLabel = $beforeLabel;
    }

    /**
     * Returns the class to insert into each page link.
     *
     * @return string
     */
    public function getPageClass(): string {
        return $this->pageClass;
    }

    private function setPageClass(string $pageClass): void {
        $this->pageClass = $pageClass;
    }

    /**
     * Returns the label to insert into each page link.
     *
     * @return string
     */
    public function getPageLabel(): string {
        return $this->pageLabel;
    }

    private function setPageLabel(string $pageLabel): void {
        $this->pageLabel = $pageLabel;
    }

    /**
     * Returns the class to insert into selected link.
     *
     * @return string
     */
    public function getSelectedClass(): string {
        return $this->selectedClass;
    }

    private function setSelectedClass(string $selectedClass): void {
        $this->selectedClass = $selectedClass;
    }

    /**
     * Returns the class to insert into after link.
     *
     * @return string
     */
    public function getAfterClass(): string {
        return $this->afterClass;
    }

    private function setAfterClass(string $afterClass): void {
        $this->afterClass = $afterClass;
    }

    /**
     * Returns the label to insert into after link.
     *
     * @return string
     */
    public function getAfterLabel(): string {
        return $this->afterLabel;
    }

    private function setAfterLabel(string $afterLabel): void {
        $this->afterLabel = $afterLabel;
    }

    /**
     * Returns the class to insert into separator item.
     *
     * @return string
     */
    public function getSeparatorClass(): string {
        return $this->separatorClass;
    }

    private function setSeparatorClass(string $separatorClass): void {
        $this->separatorClass = $separatorClass;
    }

    /**
     * Returns the label to insert into separator item.
     *
     * @return string
     */
    public function getSeparatorLabel(): string {
        return $this->separatorLabel;
    }

    private function setSeparatorLabel(string $separatorLabel): void {
        $this->separatorLabel = $separatorLabel;
    }

    /**
     * Returns the link target value.
     *
     * @return string
     */
    public function getLinkTarget(): string {
        return $this->linkTarget;
    }

    private function setLinkTarget(string $linkTarget): void {
        $this->linkTarget = $linkTarget;
    }

    /**
     * Returns the link is follow.
     *
     * @return bool
     */
    public function getLinkIsFollow(): bool {
        return $this->linkIsFollow;
    }

    private function setLinkIsFollow(bool $linkIsFollow): void {
        $this->linkIsFollow = $linkIsFollow;
    }
}
