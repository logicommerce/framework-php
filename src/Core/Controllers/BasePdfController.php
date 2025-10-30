<?php

namespace FWK\Core\Controllers;

use FWK\Core\Resources\Response;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\RouteType;
use FWK\Enums\TwigContentTypes;
use FWK\Twig\TwigLoader;
use FWK\Core\Theme\Theme;

/**
 * This is the base controller for all the PDF controllers.
 *
 * This class extends Controller, see this class.
 *
 * @abstract
 * 
 * @responseType: PDF
 * 
 * @twigContent: \themes\Internal\Content\Pdf\default.pdf.twig
 * 
 * @see BasePdfController::run()
 *
 * @see Controller
 *
 * @package FWK\Core\Controllers
 */
abstract class BasePdfController extends Controller {

    public const CONTENT_DISPOSITION_INLINE = 'inline';

    public const CONTENT_DISPOSITION_ATTACHMENT = 'attachment';

    private string $fileName = 'document';

    private string $contentDisposition = self::CONTENT_DISPOSITION_INLINE;

    protected function alterTheme(): void {
        $theme = Theme::getInstance();
        $theme->setName(INTERNAL_THEME);
        $theme->setVersion('');
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
    }

    /**
     * @see \FWK\Core\Controllers\Controller::addResponseHeaders()
     */
    protected function addResponseHeaders(): void {
        // ob_clean();
        parent::addResponseHeaders();
        // Response::addHeader('Content-type:application/pdf');
        Response::addHeader('Content-type:application/pdf');
        // It will be called downloaded.pdf
        // Response::addHeader('Content-Disposition:attachment;filename=' . $this->getFileName());
        Response::addHeader('Content-Disposition:' . $this->getContentDisposition() . ';filename=' . $this->getFileName());
    }

    /**
     *
     * @param string $filename
     */
    protected function setFileName(string $fileName): void {
        $this->fileName = $fileName;
    }

    /**
     * This function retuns the file name for the pdf response
     *
     * @return string
     */
    public function getFileName(): string {
        return $this->fileName . '.pdf';
    }

    /**
     *
     * @param string $filename
     */
    protected function setContentDisposition(string $contentDisposition): void {
        $this->contentDisposition = $contentDisposition;
    }

    /**
     * This function retuns the contentDisposition to
     *
     * @return string
     */
    public function getContentDisposition(): string {
        return $this->contentDisposition;
    }

    /**
     * @see \FWK\Core\Controllers\Controller::setType()
     */
    protected function setType(array $additionalData = [], string $header = null): void {
        Response::setType(Response::TYPE_PDF);
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::addTwigBaseFunctions()
     */
    protected function addTwigBaseFunctions(TwigLoader $twig) {
        Loader::twigFunctions(TwigContentTypes::PDF)->addFunctions($twig->getTwigEnvironment());
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::addTwigBaseExtensions()
     */
    protected function addTwigBaseExtensions(TwigLoader $twig) {
        Loader::twigExtensions(TwigContentTypes::PDF)->addExtensions($twig->getTwigEnvironment());
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::setTwig()
     */
    protected function setTwig(array $data = [], bool $loadCore = true, int $autoescape = 0): TwigLoader {
        return parent::setTwig([], false, $autoescape);
    }

    /**
     *
     *  Override content to 'Content/Json/default.json.twig', and set format to 'json'
     *              
     * @see \FWK\Core\Controllers\Controller::render()
     */
    protected function render(String $content = null, String $layout = null, String $format = 'html'): string {
        return parent::render('Content/' . ucfirst(TwigContentTypes::PDF) . '/default.' . TwigContentTypes::PDF . '.twig', null, TwigContentTypes::PDF);
    }

    /**
     * This method validate if the session is logged in. Else Redirect to User path
     *
     * @return void
     */
    protected function validateLoggedIn(): void {
        if (!Utils::isSessionLoggedIn($this->getSession())) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        }
    }

    /**
     * This method validate if the session is sales agent. Else Redirect to User path
     *
     * @return void
     */
    protected function validateSalesAgent(): void {
        if (!Utils::isSalesAgent($this->getSession())) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        }
    }

    /**
     * This method validate if the company accounts are enabled. Else Redirect to User path
     *
     * @return void
     */
    protected function validateCompanyAccounts(): void {
        if (!Utils::isCompanyAccounts()) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        }
    }

    /**
     * This method launch forbidden response if the user is simulated. Else Redirect to User path
     *
     * @return void
     */
    protected function forbiddenSimulatedUser(): void {
        if (Utils::isSimulatedUser($this->getSession())) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        }
    }
}
