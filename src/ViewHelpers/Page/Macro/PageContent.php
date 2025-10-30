<?php

namespace FWK\ViewHelpers\Page\Macro;

use SDK\Dtos\Catalog\Page\Page;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the PageContent class, a macro class for the PageViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's PageContent.
 *
 * @see PageContent::getViewParameters()
 *
 * @package FWK\ViewHelpers\Page\Macro
 */
class PageContent {

    public ?Page $page = null;

    public array $data = [];

    /**
     * Constructor method for PageContent.
     * 
     * @see PageContent
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for PageViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->page)) {
            throw new CommerceException("The value of [page] argument: '" . $this->page . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'page' => $this->page,
            'data' => $this->data
        ];
    }
}
