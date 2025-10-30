<?php

namespace FWK\ViewHelpers\Blog\Macro;

use SDK\Dtos\Blog\Blogger;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the BloggerInformation class, a macro class for the blogViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's BloggerInformation.
 *
 * @see BloggerInformation::getViewParameters()
 *
 * @package FWK\ViewHelpers\Blog\Macro
 */
class BloggerInformation {

    public ?Blogger $blogger = null;

    public bool $showLabel = true;

    public bool $showName = true;

    public bool $showDescription = true;

    /**
     * Constructor method for BloggerInformation.
     * 
     * @see BloggerInformation
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
        if (is_null($this->blogger)) {
            throw new CommerceException("The value of [blogger] argument: '" . $this->blogger . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'blogger' => $this->blogger,
            'showLabel' => $this->showLabel,
            'showName' => $this->showName,
            'showDescription' => $this->showDescription
        ];
    }
}
