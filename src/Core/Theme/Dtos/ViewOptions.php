<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;
use FWK\Enums\Parameters;

/**
 * This is the 'ViewOptions' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOptions::getItems()
 * @see ViewOptions::getTemplate()
 * @see ViewOptions::getPerPage()
 * @see ViewOptions::getSort()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */

class ViewOptions extends Element {
    use ElementTrait;

    public const TEMPLATE = Parameters::TEMPLATE;

    public const PER_PAGE = Parameters::PER_PAGE;

    public const SORT = Parameters::SORT;

    private ?ViewOptionTemplate $template = null;

    private ?ViewOptionPerPage $perPage = null;

    private ?ViewOption $sort = null;

    /**
     * This method returns an array with the available templates. 
     *
     * @return ViewOptionTemplate|NULL
     */
    public function getTemplate(): ?ViewOptionTemplate {
        return $this->template;
    }

    private function setTemplate(array $template): void {
        $this->template = new ViewOptionTemplate($template);
    }

    /**
     * This method returns an array with the available perPage values.
     *
     * @return ViewOptionPerPage|NULL
     */
    public function getPerPage(): ?ViewOptionPerPage {
        return $this->perPage;
    }

    private function setPerPage(array $perPage): void {
        $this->perPage = new ViewOptionPerPage($perPage);
    }

    /**
     * This method returns an array with the available sort values.
     *
     * @return ViewOption|NULL
     */
    public function getSort(): ?ViewOption {
        return $this->sort;
    }

    private function setSort(array $sort): void {
        if (isset($sort[ViewOptionSort::SORT_ITEMS])) {
            $sortItmesClass = 'FWK\\Core\\Theme\\Dtos\\' . $sort[ViewOptionSort::SORT_ITEMS] . 'ViewOptionSort';
            $this->sort = new $sortItmesClass($sort);
        } else {
            $this->sort = new ViewOptionSort($sort);
        }
    }
}
