<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\CacheControl;
use FWK\ViewHelpers\Util\Macro\LcCommerceData\LcCommerceData as LcCommerceDataLcCommerceData;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\Cookie;
use SDK\Dtos\Catalog\Category;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Dtos\Common\Route;
use SDK\Dtos\Documents\Document;

/**
 * This is the LcCommerceData class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's search form.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class LcCommerceData {

    public ?Route $route = null;

    public ?Product $pageProduct = null;

    public ?Category $pageCategory = null;

    public ?ElementCollection $pageProducts = null;

    public ?ElementCollection $pageCategoryProducts = null;

    public ?Document $order = null;

    public ?bool $cacheable = null;

    private ?LcCommerceDataLcCommerceData $lcCommerceData = null;

    /**
     * Constructor method for LcCommerceData
     *
     * @see LcCommerceData
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
        if (is_null($this->route)) {
            throw new CommerceException("The value of [route] argument is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->lcCommerceData = new LcCommerceDataLcCommerceData($this->route);
        if (!is_null($this->pageProduct)) {
            $this->lcCommerceData->setPageProduct($this->pageProduct);
        }
        if (!is_null($this->pageCategory)) {
            $this->lcCommerceData->setPageCategory($this->pageCategory, $this->pageCategoryProducts);
        }
        if (!is_null($this->order)) {
            $this->lcCommerceData->setOrder($this->order);
        }
        if (!is_null($this->pageProducts)) {
            $this->lcCommerceData->setPageProducts($this->pageProducts);
        }
        if (is_null($this->cacheable)) {
            $this->cacheable = CacheControl::getInstance()->isCacheable(Cookie::get('cache-hash'));
        }
        $this->lcCommerceData->setFolcsVersion($this->cacheable);
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'lcCommerceData' => $this->lcCommerceData,
            'cacheable' => $this->cacheable ? 1 : 0
        ];
    }
}
