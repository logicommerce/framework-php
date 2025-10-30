<?php

namespace FWK\ViewHelpers\Category\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection;

/**
 * This is the CategoriesTree class, a macro class for the CategoryViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's CategoriesTree.
 *
 * @see CategoriesTree::getViewParameters()
 *
 * @package FWK\ViewHelpers\Category\Macro
 */
class CategoriesTree {

    public ?ElementCollection $categories = null;

    public const SHOW_IMAGE_BACKGROUND_IMAGE = 'backgroundImage';

    public const SHOW_IMAGE_BEFORE_NAME = 'beforeName';

    public const SHOW_IMAGE_AFTER_NAME = 'afterName';

    public const SHOW_IMAGE_NONE = 'none';

    private const SHOW_IMAGE_VALUES = [
        self::SHOW_IMAGE_BACKGROUND_IMAGE,
        self::SHOW_IMAGE_BEFORE_NAME,
        self::SHOW_IMAGE_AFTER_NAME,
        self::SHOW_IMAGE_NONE
    ];

    public string $showImage = self::SHOW_IMAGE_NONE;

    /**
     * Constructor method for CategoriesTree.
     * 
     * @see CategoriesTree
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
        if (is_null($this->categories)) {
            throw new CommerceException("The value of [categories] argument: '" . $this->categories . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!in_array($this->showImage, self::SHOW_IMAGE_VALUES, true)) {
            throw new CommerceException("The value of [showImage] argument: '" . $this->showImage . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
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
            'categories' => $this->categories,
            'showImage' => $this->showImage
        ];
    }
}