<?php

namespace FWK\ViewHelpers\Basket\Macro;

use FWK\Core\Dtos\CustomTag as FWKCustomTag;
use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use SDK\Core\Dtos\ElementCollection;
use SDK\Dtos\Basket\Basket;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the CustomTags class, a macro class for the basket viewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the basket's customTags.
 *
 * @see CustomTags::getViewParameters()
 *
 * @package FWK\ViewHelpers\Basket\Macro
 */
class CustomTags {

    public ?Basket $basket = null;

    public ?ElementCollection $customTags = null;

    public array $showPositions = [];

    public bool $useCalendar = true;

    public bool $showFormFields = true;

    public string $saveButtonSubmitAction = '';

    /**
     * Constructor method for CustomTags class.
     * 
     * @see CustomTags
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for BasketViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->basket)) {
            throw new CommerceException("The value of [basket] argument: '" . $this->basket . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (is_null($this->customTags)) {
            throw new CommerceException("The value of [customTags] argument: '" . $this->customTags . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        } else {
            $this->customTags = DtosElementCollection::fillFromParentCollection($this->customTags, FWKCustomTag::class);
        }

        if (!empty($this->basket->getItems())) {
            $this->setCustomTagValues();
        } else {
            $this->customTags = null;
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
            'customTags' => $this->customTags,
            'showPositions' => $this->showPositions,
            'useCalendar' => $this->useCalendar,
            'showFormFields' => $this->showFormFields,
            'saveButtonSubmitAction' => $this->saveButtonSubmitAction,
        ];
    }

    /**
     * Set basket customTags values to customTags object
     *
     * @return void
     */
    private function setCustomTagValues(): void {
        $customTags = $this->customTags->getItems();
        $customTagValues = $this->basket->getCustomTagValues();

        foreach ($customTags as $customTag) {
            $customTag->setValue('');
            foreach ($customTagValues as $customTagValue) {
                if ($customTag->getId() === $customTagValue->getCustomTagId()) {
                    $customTag->setValue($customTagValue->getValue());
                    break;
                }
            }
        }
    }
}
