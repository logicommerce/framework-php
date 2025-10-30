<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Theme\Dtos\ItemList;
use FWK\Core\Theme\Dtos\ViewOptions;

/**
 * This is the ViewSection class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's pagination.
 *
 * @see ViewSection::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class ViewSection {

    public ?ItemList $itemList = null;

    public string $showElementsList = '';

    /**
     * Constructor method for ViewSection
     *
     * @see ViewSection
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
        if (is_null($this->itemList)) {
            throw new CommerceException("The value of [itemList] argument: '" . $this->itemList . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!($this->itemList instanceof ItemList)) {
            throw new CommerceException('The value of itemList argument must be a instance of ' . ItemList::class . '. ' . ' Instance of ' . get_class($this->itemList) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
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
            'itemList' => $this->itemList,
            'showElementsList' => $this->showElementsList,
        ];
    }
}
