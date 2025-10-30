<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceData;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Dtos\Common\Route;
use SDK\Dtos\Common\RouteWarning;

/**
 * This is the Navigation class, a DTO class for the utilViewHelper LcCommerceData.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceData
 */
class Navigation {
    use ElementTrait;

    private string $type = '';

    private string $language = '';

    private int $id = 0;

    private string $country = '';

    private string $currency = '';

    private ?RouteWarning $warning = null;

    private string $warningGeoIp = '';

    private array $breadcrumbs = [];

    /**
     * Constructor method for Navigation
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->type = $route->getType();
        $this->language = $route->getLanguage();
        $this->id = $route->getId();
        $this->country = $route->getCountry();
        $this->currency = $route->getCurrency();
        foreach ($route->getBreadcrumb() as $breadcrumb) {
            $this->breadcrumbs[] = new Breadcrumb($breadcrumb);
        }
        $this->warning = $route->getWarning();
        $this->warningGeoIp = $route->getWarningGeoIp();
    }
}
