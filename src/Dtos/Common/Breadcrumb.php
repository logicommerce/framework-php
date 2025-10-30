<?php

namespace FWK\Dtos\Common;

use SDK\Dtos\Common\Breadcrumb as SDKBreadcrumb;

/**
 * This is the Breadcrumb class
 *
 * @see Breadcrumb::getShow()
 * @see Breadcrumb::setShow()
 *
 * @package FWK\Dtos\Common
 */
class Breadcrumb extends SDKBreadcrumb {

    protected string $show = '';

    protected string $name = '';

    /**
     * Returns the show
     *
     * @return string
     */
    public function getShow(): string {
        return $this->show;
    }

    /**
     * Sets the show
     *
     * @param string 
     */
    public function setShow(string $show): void {
        $this->show = $show;
    }

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string 
     */
    public function setName(string $name): void {
        $this->name = $name;
    }
}
