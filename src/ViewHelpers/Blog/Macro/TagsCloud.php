<?php

namespace FWK\ViewHelpers\Blog\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the TagsCloud class, a macro class for the blogViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's TagsCloud.
 *
 * @see TagsCloud::getViewParameters()
 *
 * @package FWK\ViewHelpers\Blog\Macro
 */
class TagsCloud {

    public array $tags = [];

    public bool $showLabel = true;

    /**
     * Constructor method for TagsCloud.
     * 
     * @see TagsCloud
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (count($this->tags) === 0) {
            throw new CommerceException("The value of [tags] argument: '" . $this->tags . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'tags' => $this->tags,
            'showLabel' => $this->showLabel
        ];
    }
}
