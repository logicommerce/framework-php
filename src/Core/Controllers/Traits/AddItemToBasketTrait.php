<?php

declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use SDK\Core\Dtos\Element;
use SDK\Services\Parameters\Groups\PluginAccountIdParametersGroup;

/**
 * This is the set delivery trait.
 *
 * @see AddItemToBasketTrait::parseResponseData()
 *
 * @uses BuidlProductOptionParametersGroupTrait
 * 
 * @package FWK\Core\Controllers\Traits
 */
trait AddItemToBasketTrait {
    use BuidlProductOptionParametersGroupTrait;

    private function expressCheckoutRedirect(?Element $response): null|Element {
        if (is_null($response->getError())) {
            $pluginAccountId = intval($this->getRequestParam(Parameters::PLUGIN_ACCOUNT_ID, false, 0));
            if ($pluginAccountId > 0) {
                $pluginAccountIdParametersGroup = new PluginAccountIdParametersGroup();
                $pluginAccountIdParametersGroup->setPluginAccountId($pluginAccountId);
                $expressCheckoutUrl = Loader::service(Services::BASKET)->getExpressCheckoutUrl($pluginAccountIdParametersGroup);
                if (!is_null($expressCheckoutUrl) && !empty($expressCheckoutUrl->getUrl())) {
                    return new class($expressCheckoutUrl->getUrl()) extends Element {
                        private ?string $expressCheckoutUrl = null;
                        public function __construct(?string $expressCheckoutUrl) {
                            $this->expressCheckoutUrl = $expressCheckoutUrl;
                        }
                        public function jsonSerialize(): mixed {
                            return [
                                'expressCheckoutUrl' => $this->expressCheckoutUrl
                            ];
                        }
                    };
                }
            }
        }
        return $response;
    }
}
