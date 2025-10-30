<?php

declare(strict_types=1);

namespace FWK\ViewHelpers\Util\Macro\LcCommerceSession;

use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Enums\UserType;

/**
 * This is the LcCommerceSession class, a DTO class for the utilViewHelper LcCommerceSession.
 *
 * @package FWK\ViewHelpers\Util\Macro\LcCommerceSession
 */
class LcCommerceSession implements \JsonSerializable {
    use ElementTrait;

    private ?Session $session = null;

    private int $userId = 0;

    private string $country = '';

    private string $currency = '';

    private string $language = '';

    private string $email = '';

    private string $name = '';

    private string $phone = '';

    private string $mobile = '';

    private int $locationId = 0;

    private string $postalCode = '';

    private ?Basket $basket = null;

    /**
     * Constructor method for LcCommerceSession
     *
     * @see LcCommerceSession
     *
     * @param array $arguments
     */
    public function __construct(Session $session) {
        $this->userId = $session->getUser()->getId();
        $seletecAddress = $session->getUser()->getAddress($session->getUser()->getSelectedBillingAddressId());
        if (Utils::isSessionLoggedIn($session) && !is_null($seletecAddress?->getLocation())) {
            $this->country = $seletecAddress->getLocation()->getGeographicalZone()->getCountryCode();
            $this->currency = $session->getUser()->getCurrencyCode();
        } else {
            $this->country = $session->getGeneralSettings()->getCountry();
            $this->currency = $session->getGeneralSettings()->getCurrency();
        }
        $this->language = $session->getGeneralSettings()->getLanguage();
        $this->email = $session->getUser()->getEmail();
        if (!is_null($seletecAddress)) {
            if (($seletecAddress->getUserType() == UserType::BUSINESS || $seletecAddress->getUserType() == UserType::FREELANCE) && !empty($seletecAddress->getCompany())) {
                $this->name = $seletecAddress->getCompany();
            } else {
                $this->name = $seletecAddress->getFirstName() . (!empty($seletecAddress->getLastName()) ? ' ' . $seletecAddress->getLastName() : '');
            }
            $this->phone = $seletecAddress->getPhone();
            $this->mobile = $seletecAddress->getMobile();
            $this->locationId = $seletecAddress->getLocation()->getGeographicalZone()->getLocationId();
            $this->postalCode = $seletecAddress->getPostalCode();
        } else {
            $this->name = '';
            $this->phone = '';
            $this->mobile = '';
            $this->locationId = 0;
            $this->postalCode = '';
        }
        $this->basket = new Basket($session->getBasket());
    }

    private function getObjectProperties(array $data = []): array {
        $properties = get_object_vars($this);
        unset($properties['session']);
        return $properties;
    }
}
