<?php

namespace FWK\ViewHelpers\Category;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\ViewHelpers\Category\Macro\CategoriesTree;
use FWK\ViewHelpers\Category\Macro\RichSnippets;

/**
 * This is the CategoryViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of the category's view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see ViewHelper
 * @see CategoryViewHelper::categoriesTreeMacro()
 *
 * @package FWK\ViewHelpers\Category
 */
class CategoryViewHelper extends ViewHelper{
       
    
    /**
     * This method merges the given arguments, calculates and returns the view parameters for the CategoriesTree.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>categories</li>
     * <li>showName</li>
     * <li>showImage</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function categoriesTreeMacro(array $arguments = []): array {
        return (new CategoriesTree($arguments))->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the richSnippets.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>richSnippets</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function richSnippetsMacro(array $arguments = []): array {
        $richSnippets = new RichSnippets($arguments);
        return $richSnippets->getViewParameters();
    }
}