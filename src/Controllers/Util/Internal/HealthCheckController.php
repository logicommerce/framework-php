<?php

namespace FWK\Controllers\Util\Internal;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Core\Theme\Theme;
use FWK\Twig\TwigLoader;
use SDK\Core\Resources\BatchRequests;
use FWK\Enums\TwigAutoescape;

/**
 * This is the HealthCheckController class.
 * The purpose of this class is to check the basic operation of the controller mechanism.<br>
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see HealthCheckController::testRender()
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Util\Internal
 */
class HealthCheckController extends BaseHtmlController {

    protected function alterTheme(): void {
        $theme = Theme::getInstance();
        $theme->setName(INTERNAL_THEME);
        $theme->setVersion('');
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $requests): void {
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
        $this->setDataValue(self::CONTROLLER_ITEM, '');
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::setTwig()
     */
    protected function setTwig(array $data = [], bool $loadCore = true, int $autoescape = 0): TwigLoader {
        $twig = parent::setTwig([], true, TwigAutoescape::AUTOESCAPE_HTML, INTERNAL_THEME);
        return $twig;
    }

    /**
     * This method executes a renderization test returning the renderization in a string.
     * 
     * @return string
     */
    public function testRender(): string {
        return $this->render();
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::render()
     */
    protected function render(String $content = null, String $layout = null, String $format = 'html'): string {
        $layout = 'layouts/default.html.twig';
        $content = 'Content/Util/HealthCheck/default.html.twig';
        return parent::render($content, $layout, '');
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
