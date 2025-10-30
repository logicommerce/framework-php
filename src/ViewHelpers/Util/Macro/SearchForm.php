<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;

/**
 * This is the SearchForm class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's search form.
 *
 * @see SearchForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class SearchForm {

    public const SEARCH_BY_CT_SEARCH_ALL = 'searchAll';

    public const SEARCH_BY_CT_SHOW_CHECKS = 'showChecks';

    public const SEARCH_BY_CT_SHOW_RADIOS = 'showRadios';

    public const SEARCH_BY_CT_SHOW_COMBO = 'showCombo';

    public ?Form $form = null;

    public bool $showLabel = true;

    public bool $showPlaceholder = true;

    public int $minCharacters = 3;

    public bool $searchProducts = true;

    public bool $searchCategories = false;

    public bool $searchBlog = false;

    public bool $searchPages = false;

    public bool $searchNews = false;

    /**
     * Constructor method for SearchForm
     *
     * @see SearchForm
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
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
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
            'form' => $this->form,
            'showLabel' => $this->showLabel,
            'showPlaceholder' => $this->showPlaceholder,
            'minCharacters' => $this->minCharacters,
            'searchProducts' => $this->searchProducts,
            'searchCategories' => $this->searchCategories,
            'searchBlog' => $this->searchBlog,
            'searchPages' => $this->searchPages,
            'searchNews' => $this->searchNews
        ];
    }
}
