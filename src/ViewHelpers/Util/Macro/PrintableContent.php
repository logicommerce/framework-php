<?php

namespace FWK\ViewHelpers\Util\Macro;

use SDK\Enums\TrackerAmbience;
use SDK\Enums\TrackerPosition;
use SDK\Enums\TrackerScopeType;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the PrintableContent class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the PrintableContent output.
 *
 * @see PrintableContent::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class PrintableContent {

    public string $windowAttributes = '';

    public string $content = '';

    public string $hrefType = '';

    public string $title = '';

    /**
     * Constructor method for PrintableContent
     *
     * @see PrintableContent
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
        $this->windowAttributes = preg_replace('/\'/', '&#39;', $this->windowAttributes);
        $this->hrefType = preg_replace('/\'/', '&#39;', $this->hrefType);
        $this->title = preg_replace('/\'/', '&#39;', $this->title);

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'content' => $this->content,
            'windowAttributes' => $this->windowAttributes,
            'hrefType' => $this->hrefType,
            'title' => $this->title
        ];
    }
}
