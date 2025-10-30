<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;
use FWK\Enums\Services;

/**
 * This is the Check Captcha trait.
 *
 * @see CheckCaptcha::checkCaptcha()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait CheckCaptcha {

    /**
     * Checks if the captcha is in use and if it is correct
     * 
     * @throws CommerceException
     * 
     */
    protected function checkCaptcha(): void {
        if (isset($this->getFilterParams()[Parameters::CAPTCHA_TOKEN])) {
            $captchaTokent = $this->getRequestParam(Parameters::CAPTCHA_TOKEN, true);
            $catpchaProperties = Loader::service(Services::PLUGIN)->getCaptchaPluginProperties();
            $tokenValidatorClass = new ('Plugins\\' . Utils::getCamelFromSnake($catpchaProperties->getPluginModule(), '.') . '\\Core\\Resources\\TokenValidator');
            if (!$tokenValidatorClass::verifyToken($captchaTokent, $catpchaProperties, 'submit')) {
                throw new CommerceException('Captcha validation error on plugin: ' . $catpchaProperties->getPluginModule(), CommerceException::UTILS_NO_COUNTRIES_IN_RESPONSE);
            }
            if (isset($this->appliedParameters)) {
                $this->appliedParameters[Parameters::CAPTCHA_TOKEN] = $captchaTokent;
            }
        }
    }
}
