<?php

namespace FWK\Core\Resources;

use SDK\Core\Resources\ApiRequest;
use SDK\Core\Resources\Cookie;
use SDK\Core\Resources\Server;
use SDK\Core\Resources\VarnishManagement;

/**
 * This is the Response class. 
 * 
 * This class encapsulates the response information that will be delivered to the request, 
 * so the responses to each request had to be prepared through this class.
 *
 * @see Response::getInstance()
 * @see Response::setType()
 * @see Response::getType()
 * @see Response::addHeader()
 * @see Response::getHeaders()
 * @see Response::addController()
 * @see Response::redirect()
 * @see Response::output()
 * 
 * @package FWK\Core\Resources
 */
final class Response {

    public const TYPE_JS = 'js';

    public const TYPE_CSS = 'css';

    public const TYPE_CSV = 'csv';

    public const TYPE_JSON = 'json';

    public const TYPE_JSONP = 'jsonp';

    public const TYPE_PDF = 'pdf';

    public const TYPE_HTML = 'html';

    public const TYPE_TEXT = 'text';

    public const TYPE_XML = 'xml';

    public const TYPE_GZIP = 'gzip';

    public const MIME_TYPE_JS = 'application/javascript';

    public const MIME_TYPE_CSV = 'text/csv';

    public const MIME_TYPE_JSON = 'application/json';

    public const MIME_TYPE_TEXT = 'text/plain';

    public const MIME_TYPE_XML = 'text/xml';

    public const MIME_TYPE_GZIP = 'application/gzip';

    private static $instance = null;

    private static $headers = [];

    private static $type = self::TYPE_HTML;

    private static $controller = [];

    private static $commerceId = '0';

    /**
     * This method returns the instance of the Response.
     *
     * @return self
     */
    public static function getInstance(): self {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * This constructor prevents to initialize this object. To get an instance use Response::getInstance()
     */
    private function __construct() {
        $this->addHeader('Request-Id: ' . REQUEST_ID);
    }

    /**
     * Prevent clone this object
     */
    private function __clone() {
    }

    /**
     * This method sets the type of the response.
     *
     * @param string $type to indicate the type of the response (@see Response Types)
     *
     * @return void
     * 
     * @see Response::TYPE_HTML
     * @see Response::TYPE_JSON
     * @see Response::TYPE_CSS
     * @see Response::TYPE_JS
     * @see Response::TYPE_PDF 
     */
    public static function setType(string $type): void {
        self::$type = $type;
    }

    /**
     * This method returns the type of the response (@see Response Types)
     *
     * @return string
     * 
     * @see Response::TYPE_HTML
     * @see Response::TYPE_JSON
     * @see Response::TYPE_CSS
     * @see Response::TYPE_JS
     * @see Response::TYPE_PDF
     */
    public static function getType(): string {
        return self::$type;
    }

    /**
     * This method adds a header to the response.
     * 
     * @param string $header example: 'Content-Type: application/json'
     *
     * @return void
     */
    public static function addHeader(?string $header): void {
        if (!is_null($header)) {
            self::$headers[] = $header;
        }
    }

    /**
     * This method adds the given controller name to the response. This is used by the response to add an specific header to the response informing about the controllers used by the request resolution.
     *
     * @param string $controller
     *
     * @return void
     */
    public static function addController(string $controller): void {
        self::$controller[] = $controller;
    }

    /**
     * This method adds the given commerceId name to the response. This is used by the response to add an specific header to the response informing about the commerceId.
     *
     * @param string $commerceId
     *
     * @return void
     */
    public static function addCommerceId(string $commerceId): void {
        self::$commerceId = $commerceId;
    }

    /**
     * This method sets the headers associated with the Cache Control.
     *
     * @param bool $isCacheable
     *
     * @return void
     */
    public static function addCacheHeaders(bool $isCacheable = true): void {
        if ($isCacheable) {
            self::addHeader('Cache-Control: public, max-age=' . (VarnishManagement::getXttl() < DEFAULT_CACHE_CONTROL_MAX_AGE ? VarnishManagement::getXttl() : DEFAULT_CACHE_CONTROL_MAX_AGE));
            if (!empty(self::$controller)) {
                VarnishManagement::addXkeys(self::$commerceId . '-CONTROLLER-' . implode(self::$controller));
            }
            VarnishManagement::addXkeys(self::$commerceId . '-PATH-' . (isset($_GET[URL_ROUTE]) ? strtolower($_GET[URL_ROUTE]) : ''));
            if (!VarnishManagement::isAddedTtl()) {
                VarnishManagement::setXttl(DEFAULT_CACHE_TTL, implode(self::$controller));
            }
        } else {
            self::addHeader('Cache-Control: no-store');
        }
    }

    /**
     * This method returns the headers of the response.
     *
     * @return Array where each position is an string that contains one of the response headers.
     */
    public static function getHeaders(): array {
        return self::$headers;
    }

    /**
     * This method returns the headers of the response.
     *
     * @return Array where each position is an string that contains one of the response headers.
     */
    public static function getStructHeaders(): array {
        $headers = [];
        foreach (self::getHeaders() as $header) {
            $auxHeader = explode(":", $header, 2);
            if (count($auxHeader) === 2) {
                $headers[$auxHeader[0]][] = trim($auxHeader[1]);
            }
        }
        return $headers;
    }

    /**
     * This method redirects to the given url with the given status.
     *
     * @return void
     */
    public static function redirect(string $url, int $status = 302, bool $cacheRedirect = false): void {
        self::beforeOutput();
        if ($cacheRedirect) {
            header('x-fwk-redirect: redirect from FWK', true);
        }
        header('Location: ' . str_replace(['&amp;', "\n", "\r"], ['&', '', ''], $url), true, $status);
        Commerce::end();
    }

    /**
     * This method set forbidden response.
     *
     * @return void
     */
    public static function forbidden(): void {
        self::beforeOutput();
        header(Server::get('SERVER_PROTOCOL') . ' ' . 403, true);
        Commerce::end();
    }

    private static function beforeOutput() {
        if (!headers_sent()) {
            Cookie::send();
            if (DEVEL_HEADER) {
                self::addHeader('devel-request: 1');
            }
            self::addHeader('controller: ' . implode(',', self::$controller));
            self::addHeader('commerce-id: ' . self::$commerceId);
            self::addHeader('api-total-requests: ' . ApiRequest::getApiRequestCount());
            self::addHeader('api-total-content-length: ' . ApiRequest::getApiRequestContentLength());
            foreach (self::$headers as $header) {
                header($header, true);
            }
        }
    }

    /**
     * This method executes the return/output of the response to the current request.
     *
     * @return void
     */
    public static function output(string $output): void {
        self::beforeOutput();
        if (TIMER_DEBUG) {
            $output = rtrim(trim($output), '}');
        }
        echo $output;
    }

    /**
     * @deprecated
     */
    public static function outputJson(array $data): void {
        self::addHeader('Content-Type: application/json');
        self::output(json_encode($data));
    }
}
