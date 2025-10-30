<?php

namespace FWK\ViewHelpers\Category\Macro;

use SDK\Dtos\Snippets\CategoryRichSnippets;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the RichSnippets class, a macro class for the CategoryViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the categories rich snippets.
 *
 * @see RichSnippets::getViewParameters()
 *
 * @package FWK\ViewHelpers\Category\Macro
 */
class RichSnippets {

    public ?CategoryRichSnippets $richSnippets = null;

    /**
     * Constructor method for RichSnippets class.
     * 
     * @see RichSnippets
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for CategoryViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->richSnippets)) {
            throw new CommerceException("The value of [richSnippets] argument: '" . $this->richSnippets . "' is required " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
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
            'richSnippets' => $this->richSnippets
        ];
    }
}
