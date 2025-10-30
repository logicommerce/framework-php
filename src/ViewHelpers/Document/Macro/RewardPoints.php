<?php

namespace FWK\ViewHelpers\Document\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Documents\Document;

/**
 * This is the RewardPoints class, a macro class for the documentViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the document reward points.
 *
 * @see RewardPoints::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class RewardPoints {

    public ?Document $document = null;

    public string $class = '';

    public bool $showHeader = true;

    public bool $showTotalRedeemed = true;

    public bool $showTotalEarned = true;

    /**
     * Constructor method for RewardPoints.
     * 
     * @see RewardPoints
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

        if (is_null($this->document)) {
            throw new CommerceException("The value of [document] argument: '" . $this->document . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'document' => $this->document,
            'class' => $this->class,
            'showHeader' => $this->showHeader,
            'showTotalRedeemed' => $this->showTotalRedeemed,
            'showTotalEarned' => $this->showTotalEarned,
        ];
    }
}
