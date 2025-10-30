<?php

namespace FWK\Controllers;

use FWK\Enums\Parameters;
use SDK\Dtos\Common\Route;

/**
 * This is the webhook path Controller.
 * 
 * 
 * This class extends WebhookController (FWK\Core\Controllers\WebhookController), see this class.
 *
 * @see PluginService
 *
 * @package FWK\Controllers
 */
class WebhookPathController extends WebhookController {

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
    }

    /**
     * This method returns the plugin module.
     *
     * @return string
     */
    protected function getPluginModule(): ?string {
        $path = $this->getRequestParam(Parameters::PATH);
        $splitPath = explode('/', $path);
        $module = end($splitPath);
        return $module;
    }
}
