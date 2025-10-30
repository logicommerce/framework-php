<?php

namespace FWK\ViewHelpers\Product\Macro;

use SDK\Dtos\Snippets\ProductRichSnippets;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the RichSnippets class, a macro class for the ProductViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the products rich snippets.
 *
 * @see RichSnippets::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class RichSnippets {

    public ?ProductRichSnippets $richSnippets = null;

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
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
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
