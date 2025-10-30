<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Common\Breadcrumb as CommonBreadcrumb;

/**
 * This is the Breadcrumb class, a DTO class for the utilViewHelpers LcCommerce.
 *
 * @see LcCommerceData::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData;
 */
class Breadcrumb {
    use ElementTrait;

    private string $name = '';

    private string $itemType = '';

    private int $itemId = 0;

    private string $url = '';

    /**
     * Constructor method for Breadcrumb
     * 
     * @param CommonBreadcrumb $breadcrumb
     */
    public function __construct(CommonBreadcrumb $breadcrumb) {
        $this->name = $breadcrumb->getName();
        $this->itemType = $breadcrumb->getItemType();
        $this->itemId = $breadcrumb->getItemId();
        $this->url = $breadcrumb->getUrlSeo();
    }
}
