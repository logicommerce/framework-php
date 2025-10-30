<?php

namespace FWK\Dtos\Common;

use SDK\Core\Dtos\PluginProperties;
use SDK\Dtos\Common\Plugin as SDKPlugin;

/**
 * This is the Plugin class
 *
 * @see Plugin::getProperties()
 * @see Plugin::setProperties()
 *
 * @package FWK\Dtos\Common
 */
class Plugin extends SDKPlugin {

    protected ?PluginProperties $properties = null;

    /**
     * Returns the Properties
     *
     * @return PluginProperties
     */
    public function getProperties(): PluginProperties {
        return $this->properties;
    }

    /**
     * Sets the Properties
     *
     * @param PluginProperties 
     */
    public function setProperties(PluginProperties $properties): void {
        $this->properties = $properties;
    }
}
