<?php

namespace FWK\ViewHelpers\Blog\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the Comments class, a macro class for the BlogViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the Blog's comments.
 *
 * @see Comments::getViewParameters()
 *
 * @package FWK\ViewHelpers\Blog\Macro
 */
class Comments {

    public array $comments = [];

    /**
     * Constructor method for Comments class.
     * 
     * @see Comments
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for BlogViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->comments)) {
            throw new CommerceException("The value of [comments] argument: '" . $this->comments . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'comments' => $this->comments
        ];
    }
}
