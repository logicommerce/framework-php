<?php

namespace FWK\Core\Theme\Dtos;

use FWK\Core\Form\Elements\Inputs\InputEmail;
use FWK\Core\Form\Elements\Inputs\InputFile;
use FWK\Core\Form\Elements\Inputs\InputHidden;
use FWK\Core\Form\Elements\Inputs\InputText;
use FWK\Core\Form\Elements\Textarea;
use FWK\Core\Form\FormFactory;
use FWK\Core\Form\FormItem;
use FWK\Core\Resources\Language;
use FWK\Core\Resources\LcFWK;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use SDK\Application;
use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Resources\Connection;

/**
 * This is the 'Commerce' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Element
 *
 * @package FWK\Core\Theme\Dtos
 */
class Commerce extends Element {
    use ElementTrait;

    public const USE_ONE_STEP_CHECKOUT = 'useOneStepCheckout';

    public const USE_OSC_ASYNC = 'useOSCAsync';

    public const IMAGE_OFFER_SMALL = 'imageOfferSmall';

    public const IMAGE_NOT_FOUND = 'imageNotFound';

    public const IMAGE_FEATURED_SMALL = 'imageFeaturedSmall';

    public const IMAGE_DISCOUNTS_SMALL = 'imageDiscountsSmall';

    public const AVAILABLE_FILL_DATA_FIELDS = 'availableFillDataFields';

    public const MAIL_ACCOUNT_P_ID = 'mailAccountPId';

    public const LOGIN_REQUIRED_AVAILABLE_ROUTES = 'loginRequiredAvailableRoutes';

    public const MAINTENANCE_AVAILABLE_ROUTES = 'maintenanceAvailableRoutes';

    public const ALLOW_DIFFERENT_COUNTRIES_ON_BILLING_AND_SHIPPING_ADDRESS = 'allowDifferentCountriesOnBillingAndShippingAddress';

    public const COUNTRY_NAVIGATION_ASSIGNAMENT = 'countryNavigationAssignament';

    public const COUNTRY_NAVIGATION_ASSIGNAMENT_BILLING = 'countryNavigationAssignamentBilling';

    public const COUNTRY_NAVIGATION_ASSIGNAMENT_SHIPPING = 'countryNavigationAssignamentShipping';

    public const COUNTRY_NAVIGATION_ASSIGNAMENT_NONE = 'countryNavigationAssignamentNone';

    public const DISABLE_SHOW_AS_GRID_PRODUCT_OPTIONS = 'disableShowAsGridProductOptions';

    public const SHOW_TAXES_INCLUDED = 'showTaxesIncluded';

    public const LOCKED_STOCK = 'lockedStock';

    protected bool $useOneStepCheckout = false;

    protected bool $useOSCAsync = false;

    protected string $imageOfferSmall = '';

    protected string $imageNotFound = '';

    protected string $imageDiscountsSmall = '';

    protected string $imageFeaturedSmall = '';

    protected string $availableFillDataFields = '';

    protected string $mailAccountPId = '';

    protected array $loginRequiredAvailableRoutes = [];

    protected array $maintenanceAvailableRoutes = [];

    protected bool $isEmbedded = false;

    protected string $webEmbedded = '';

    protected bool $allowDifferentCountriesOnBillingAndShippingAddress = true;

    protected string $countryNavigationAssignament = '';

    protected bool $disableShowAsGridProductOptions = false;

    protected ?bool $showTaxesIncluded = null;

    protected ?CommerceLockedStock $lockedStock = null;

    /**
     * This method returns if the commerce use one step checkout async.
     *
     * @return bool
     */
    public function getUseOSCAsync(): bool {
        return $this->useOSCAsync;
    }

    /**
     * This method returns if the commerce use one step checkout.
     *
     * @return bool
     */
    public function getUseOneStepCheckout(): bool {
        return $this->useOneStepCheckout;
    }

    /**
     * This method returns the image offer small.
     *
     * @return string
     */
    public function getImageOfferSmall(): string {
        return $this->imageOfferSmall;
    }

    /**
     * This method returns the image not found.
     *
     * @return string
     */
    public function getImageNotFound(): string {
        return $this->imageNotFound;
    }

    /**
     * This method returns the image discounts small.
     *
     * @return string
     */
    public function getImageDiscountsSmall(): string {
        return $this->imageDiscountsSmall;
    }

    /**
     * This method returns the image featured small.
     *
     * @return string
     */
    public function getImageFeaturedSmall(): string {
        return $this->imageFeaturedSmall;
    }

    /**
     * This method returns the available fill Data fields that will be supported data-fill-fn attribute
     *
     * @return string
     */
    public function getAvailableFillDataFields(): string {
        return $this->availableFillDataFields;
    }

    /**
     * This method returns the send forms mail configuration in this commerce.
     *
     * @return array
     */
    public function getSendMailFormsFields(): array {
        $languageSheet = Language::getInstance();
        $formsFields = [];
        $type = 'default';
        $formItems = [];
        $formItems[] = new FormItem(Parameters::TYPE, (new InputHidden($type))->setId('sendMailTypeField_' . $type));
        $formItems[] = new FormItem(Parameters::TO, (new InputEmail())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SEND_MAIL_TO))->setId('sendMailEmailField')->setClass(FormFactory::CLASS_WILDCARD)->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::SUBJECT, (new InputText())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SEND_MAIL_SUBJECT))->setId('sendMailNameField')->setClass(FormFactory::CLASS_WILDCARD)->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::BODY, (new Textarea())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SEND_MAIL_BODY))->setId('sendMailCommentField')->setClass(FormFactory::CLASS_WILDCARD)->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formItems[] = new FormItem(Parameters::ATTACHMENT, (new InputFile())->setLabelFor($languageSheet->getLabelValue(LanguageLabels::SEND_MAIL_ATTACHMENT))->setId('sendMailCommentField')->setClass(FormFactory::CLASS_WILDCARD)->setAttributeWildcard(FormFactory::ATTRIBUTE_WILDCARD)->setRequired(true));
        $formsFields[$type] = $formItems;
        return $formsFields;
    }

    /**
     * This method returns the send form mail configuration for the given type in this commerce.
     *
     * @param string $typeFormFields
     * 
     * @return array
     */
    public function getSendMailFormFields(string $typeFormFields = 'default'): array {
        return $this->getSendMailFormsFields()[$typeFormFields];
    }

    /**
     * This method returns the mail account public id
     *
     * @return string
     */
    public function getMailAccountPId(): string {
        return $this->mailAccountPId;
    }

    /**
     * This method returns the available routes when the login is required
     *
     * @return array
     */
    public function getLoginRequiredAvailableRoutes(): array {
        return $this->loginRequiredAvailableRoutes;
    }

    /**
     * This method returns if the commerce is closed for maintenance, excluding allowed conditions, for example LcFWK::getMaintenanceAllowIps()
     *
     * @return bool
     */
    public function getMaintenanceAllowAccess(): bool {
        $trimmedIps = array_map('trim', LcFWK::getMaintenanceAllowIps());
        if (LcFWK::getMaintenance() && !in_array(Connection::getIp(), $trimmedIps)) {
            return false;
        }
        return true;
    }

    /**
     * This method returns the available routes when the maintenance is enabled
     *
     * @return array
     */
    public function getMaintenanceAvailableRoutes(): array {
        return $this->maintenanceAvailableRoutes;
    }

    /**
     * This method returns is embedded request
     *
     * @return bool
     */
    public function getIsEmbedded(): bool {
        return !empty(REQUEST_HEADERS['X-IS-APP']);
    }

    /**
     * This method returns web embedded request
     *
     * @return string
     */
    public function getWebEmbedded(): string {
        return (isset(REQUEST_HEADERS['X-IS-APP']) && REQUEST_HEADERS['X-IS-APP'] != '0') ? REQUEST_HEADERS['X-IS-APP'] : '';
    }

    /**
     * This method returns if is allow different countries on billing and shipping address
     *
     * @return bool
     */
    public function isAllowDifferentCountriesOnBillingAndShippingAddress(): bool {
        return $this->allowDifferentCountriesOnBillingAndShippingAddress;
    }

    /**
     * This method returns the country navigation assignament
     *
     * @return string
     */
    public function getCountryNavigationAssignament(): string {
        return $this->countryNavigationAssignament;
    }

    /**
     * This method returns if is disable show grid options
     *
     * @return bool
     */
    public function isDisableShowAsGridProductOptions(): bool {
        return $this->disableShowAsGridProductOptions;
    }

    /**
     * This method returns if is show Tax Included
     *
     * @return bool
     */
    public function showTaxesIncluded(): bool {
        if (is_null($this->showTaxesIncluded)) {
            $showTaxesIncluded = Application::getInstance()->getEcommerceSettings()?->getCatalogSettings()?->getShowTaxesIncluded();
            if (!is_null($showTaxesIncluded)) {
                return $showTaxesIncluded;
            }
            return false;
        }
        return $this->showTaxesIncluded;
    }

    /**
     * This method returns the lockedStock configuration.
     *
     * @return CommerceLockedStock|NULL
     */
    public function getLockedStock(): ?CommerceLockedStock {
        return $this->lockedStock;
    }

    protected function setLockedStock(array $lockedStock): void {
        $this->lockedStock = new CommerceLockedStock($lockedStock);
    }
}
