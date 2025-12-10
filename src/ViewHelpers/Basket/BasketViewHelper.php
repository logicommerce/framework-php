<?php

namespace FWK\ViewHelpers\Basket;

use DateTime;
use FWK\Core\Resources\DateTimeFormatter;
use FWK\Core\Resources\Language;
use FWK\Core\Resources\Utils;
use FWK\Dtos\Basket\BasketWarning as ViewHelpersDtosBasketWarning;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Enums\LanguageLabels;
use FWK\ViewHelpers\Basket\Macro\AsyncOrder;
use FWK\ViewHelpers\Basket\Macro\Buttons;
use FWK\ViewHelpers\Basket\Macro\Comment;
use FWK\ViewHelpers\Basket\Macro\CustomTags;
use FWK\ViewHelpers\Basket\Macro\Deliveries;
use FWK\ViewHelpers\Basket\Macro\DeniedOrderMessage;
use FWK\ViewHelpers\Basket\Macro\EndOrder;
use FWK\ViewHelpers\Basket\Macro\BasketForm;
use FWK\ViewHelpers\Basket\Macro\MiniBasketWrap;
use FWK\ViewHelpers\Basket\Macro\MiniBasketContent;
use FWK\ViewHelpers\Basket\Macro\OSCForm;
use FWK\ViewHelpers\Basket\Macro\OSCModule;
use FWK\ViewHelpers\Basket\Macro\BasketContent;
use FWK\ViewHelpers\Basket\Macro\DeleteDiscountCodes;
use FWK\ViewHelpers\Basket\Macro\LockedStocksContent;
use FWK\ViewHelpers\Basket\Macro\PaymentSystems;
use FWK\ViewHelpers\Basket\Macro\RedeemRewardPoints;
use FWK\ViewHelpers\Basket\Macro\RewardPoints;
use FWK\ViewHelpers\Basket\Macro\Steps;
use FWK\ViewHelpers\Basket\Macro\VoucherForm;
use SDK\Dtos\Basket\Basket;
use SDK\Dtos\Basket\BasketWarnings\BasketWarning;
use SDK\Enums\BasketWarningAttributeType;
use SDK\Enums\BasketWarningCode;
use SDK\Enums\BasketWarningSeverity;

/**
 * This is the BasketViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of the basket's view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see BasketViewHelper::basketContentMacro()
 * @see BasketViewHelper::basketFormMacro()
 * @see BasketViewHelper::buttonsMacro()
 * @see BasketViewHelper::commentMacro()
 * @see BasketViewHelper::customTagsMacro()
 * @see BasketViewHelper::deliveriesMacro()
 * @see BasketViewHelper::deniedOrderMessageMacro()
 * @see BasketViewHelper::groupBasketWarnings()
 * @see BasketViewHelper::LockedStocksContentMacro()
 * @see BasketViewHelper::miniBasketContentMacro()
 * @see BasketViewHelper::miniBasketWrapMacro()
 * @see BasketViewHelper::oscFormMacro()
 * @see BasketViewHelper::oscModuleMacro()
 * @see BasketViewHelper::paymentSystemsMacro()
 * @see BasketViewHelper::redeemRewardPointsMacro()
 * @see BasketViewHelper::rewardPointsMacro()
 * @see BasketViewHelper::setBasketWarningMessage()
 * @see BasketViewHelper::stepsMacro()
 * @see BasketViewHelper::voucherFormMacro()
 * 
 * @see ViewHelper
 *
 * @package FWK\ViewHelpers\Basket
 */
class BasketViewHelper extends ViewHelper {

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the form.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>content</li>
     * <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function basketFormMacro(array $arguments = []): array {
        $basketForm = new BasketForm($arguments);
        return $basketForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the OSC form.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>content</li>
     * <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function oscFormMacro(array $arguments = []): array {
        $basketForm = new OSCForm($arguments);
        return $basketForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the OSC module.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>content</li>
     * <li>class</li>
     * <li>type</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function oscModuleMacro(array $arguments = []): array {
        $basketForm = new OSCModule($arguments);
        return $basketForm->getViewParameters();
    }

    /**
     * This method merges the given arguments and calculates and returns the view parameters for the output.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>basket</li>
     * <li>mode</li>
     * <li>editable</li>
     * <li>saveForLater</li>
     * <li>editableGifts</li>
     * <li>linkable</li>
     * <li>showOptionValuesName</li>
     * <li>showOptionValuesSku</li>
     * <li>showDiscountValue</li>
     * <li>showZeroDiscount</li>
     * <li>giftsPresentation</li>
     * <li>showTaxDisclosure</li>
     * <li>showTaxName</li>
     * <li>showTaxIncluded</li>
     * <li>quantityPlugin</li>
     * <li>tableClass</li>
     * <li>showWarningsBlock</li>
     * <li>errorWarningPosition</li>
     * <li>warningWarningPosition</li>
     * <li>infoWarningPosition</li>
     * <li>warningsBlockItems</li>
     * <li>showPrices</li>
     * <li>showImage</li>
     * <li>showSku</li>
     * <li>showManufacturerSku</li>
     * <li>showShippingSection</li>
     * <li>showCustomTags</li>
     * <li>showCustomTagPositions</li>
     * <li>showDiscounts</li>
     * <li>showDiscountName</li>
     * <li>showZeroShipping</li>
     * <li>showZeroPayment</li>
     * <li>showFreeTaxMessage</li>
     * <li>showPreviousPrice</li>
     * <li>showPercentDifference</li>
     * <li>showPriceDifference</li>
     * <li>showProductStockId</li>
     * <li>showFooter</li>
     * <li>showBrand</li>
     * <li>showSelectableBox</li>
     * <li>tableColumns</li>
     * <li>footerRows</li>
     * <li>disclosure</li>
     * <li>totalProductDiscounts</li>
     * <li>productsBundleTemplate</li>
     * <li>productsBundleItemTemplate</li>
     * <li>productsGiftTemplate</li>
     * <li>productSelectableGiftTemplate</li>
     * <li>mergeGridDiscounts</li>
     * <li>showLockedStocks</li>
     * <li>showLockedStocksDescription</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function basketContentMacro(array $arguments = []): array {
        $basketContent = new BasketContent($arguments, $this->languageSheet);
        return $basketContent->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttons.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>basket</li>
     * <li>showRecalculate</li>
     * <li>showContinue</li>
     * <li>showClear</li>
     * <li>submitAction</li>
     * <li>classList</li>
     * <li>backLocation</li>
     * <li>forceDisabled</li>
     * <li>routeType</li>
     * <li>validOutput</li>
     * <li>productsAreValid</li>
     * <li>basketNeedsShipping</li>
     * <li>basketNeedsPayment</li>
     * <li>basketAddressError</li>
     * <li>errorCode</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buttonsMacro(array $arguments = []): array {
        $basketButtons = new Buttons($arguments, $this->theme->getRouteType());
        return $basketButtons->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the customTags.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>customTags</li>
     * <li>showPositions</li>
     * <li>useCalendar</li>
     * <li>showFormFields</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function customTagsMacro(array $arguments = []): array {
        $basketCustomTaqs = new CustomTags($arguments);
        return $basketCustomTaqs->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the miniBasketWrap.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>basket</li>
     * <li>content</li>
     * <li>showTaxIncluded</li>
     * <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function miniBasketWrapMacro(array $arguments = []): array {
        $basketMiniBasketWrap = new MiniBasketWrap($arguments);
        return $basketMiniBasketWrap->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the lockedStocksContent.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>showRenewButton</li>
     * <li>showDescription</li>
     * <li>expiresAtExtendMinutes</li>
     * <li>expiresAtExtendMinutesUponUserRequest</li>
     * <li>class</li>
     * <li>popup</li>
     * <li>expired</li>
     * <li>lockedStockTimer</li>
     * </ul>
     * 
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function lockedStocksContentMacro(array $arguments = []): array {
        $lockedStocksContent = new LockedStocksContent($arguments);
        return $lockedStocksContent->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the miniBasketContent.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>basket</li>
     * <li>class</li>
     * <li>editable</li>
     * <li>linkable</li>
     * <li>quantityPlugin</li>
     * <li>selectableBoxRows</li>
     * <li>showCustomTagPositions</li>
     * <li>showCustomTags</li>
     * <li>showDeleteItem</li>
     * <li>showZeroDiscount</li>
     * <li>showFooter</li>
     * <li>showHeader</li>
     * <li>showImage</li>
     * <li>showItemNameDiscounts</li>
     * <li>showItemValueDiscounts</li>
     * <li>showOptions</li>
     * <li>showOptionValuesName</li>
     * <li>showPaymentSystem</li>
     * <li>showSelectableBox</li>
     * <li>showShipping</li>
     * <li>showSku</li>
     * <li>showTaxIncluded</li>
     * <li>showTotal</li>
     * <li>showTotalDiscounts</li>
     * <li>showTotalVouchers</li>
     * <li>gripOptionsClassPrefix</li>
     * <li>showLockedStocks</li>
     * <li>showLockedStocksDescription</li>
     * </ul>
     * 
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function miniBasketContentMacro(array $arguments = []): array {
        $miniBasketContent = new MiniBasketContent($arguments);
        return $miniBasketContent->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the comment.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>forceOutput</li>
     * <li>showPlaceholder</li>
     * <li>basketItems</li>
     * <li>commentValue</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function commentMacro(array $arguments = []): array {
        $basketComment = new Comment($arguments);
        return $basketComment->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the voucherForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>forceOutput</li>
     * <li>basketItems</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function voucherFormMacro(array $arguments = []): array {
        $basketVoucherForm = new VoucherForm($arguments);
        return $basketVoucherForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the paymentSystems.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>paymentSystems</li>
     * <li>showTaxIncluded</li>
     * <li>showTitle</li>
     * <li>showZeroPrice</li>
     * <li>showImage</li>
     * <li>showDescription</li>
     * <li>basketNeedsPayment</li>
     * <li>tokens</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function paymentSystemsMacro(array $arguments = []): array {
        $basketPaymentSystems = new PaymentSystems($arguments, $this->session);
        return $basketPaymentSystems->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the deliveries.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>deliveries</li>
     * <li>physicalLocations</li>
     * <li>showTitle</li>
     * <li>showLogo</li>
     * <li>showTaxIncluded</li>
     * <li>showDescription</li>
     * <li>showPickup</li>
     * <li>showProducts</li>
     * <li>showWarnings</li>
     * <li>empty</li>
     * <li>pickupPointProviders</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function deliveriesMacro(array $arguments = []): array {
        $basketDeliveriesSystems = new Deliveries($arguments);
        return $basketDeliveriesSystems->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the steps.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>stepsData</li>
     * <li>showNumbers</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function stepsMacro(array $arguments = []): array {
        $steps = new Steps($arguments, $this->languageSheet);
        return $steps->getViewParameters();
    }

    /**
     * This method adds to BasketWarning object a formatted output message
     * from languageSheet, and replace wildcards.
     *
     * @param BasketWarning $basketWarning
     * @param Basket $basket
     *
     * @return ViewHelpersDtosBasketWarning
     */
    public static function setBasketWarningMessage(BasketWarning $basketWarning, Basket $basket, string $prefix = 'WARNING_', bool $itemBasketWarning = false): ViewHelpersDtosBasketWarning {
        $languageLabels = new \ReflectionClass('FWK\\Enums\\LanguageLabels');
        $hashes = $basketWarning->getHashes();
        $isGrouped = (count($hashes) > 1);
        $label = $languageLabels->getConstant($prefix . ($isGrouped === true ? 'GROUPED_' : '') . $basketWarning->getCode());
        $message = Language::getInstance()->getLabelValue($label);

        // Code "STOCK_PREVISION" without offsetDays attribute
        if ($basketWarning->getCode() === BasketWarningCode::STOCK_PREVISION && count($basketWarning->getAttributes()) === 1) {
            $label = $languageLabels->getConstant($prefix . 'NO_' . $basketWarning->getCode());
            $message = Language::getInstance()->getLabelValue($label);
        }

        if ($basketWarning->getCode() === BasketWarningCode::USER_NOT_VERIFIED) {
            $message = str_replace('{{resend}}', '<a class="emailErrorLoginCall" data-lc-username="' . $basket->getBasketUser()->getUser()->getEmail() . '" onclick="LC.dataEvents.userVerifyResend(event)">' . Language::getInstance()->getLabelValue(LanguageLabels::RESEND_EMAIL) . '</a>', $message);
        }

        // Replace warning attributes with wildcards
        foreach ($basketWarning->getAttributes() as $attr) {
            if ($itemBasketWarning && $attr->getKey() === 'stock') {
                $message = '';
                break;
            } else {
                $value = $attr->getValue();
                if ($attr->getType() === BasketWarningAttributeType::LOCAL_DATE || $attr->getType() === BasketWarningAttributeType::LOCAL_DATE_TIME) {
                    $value = (new DateTimeFormatter(null, \IntlDateFormatter::NONE))->getFormattedDateTime($value);
                }
                $key = $attr->getKey();
                $message = str_replace('{{' . $key . '}}', $value, $message);
                if (in_array($key, ['offsetDays', 'onRequestDays'])) {
                    $value = (new DateTimeFormatter(null, \IntlDateFormatter::NONE))->getFormattedDateTime((new DateTime())->modify('+' . $value . ' day'));
                    $message = str_replace('{{' . $key . 'Date}}', $value, $message);
                }
            }
        }

        // Search product name and replace wildcard
        $messageOptions = '';
        foreach ($hashes as $hash) {
            $basketRow = $basket->getItem($hash);
            if (!is_null($basketRow)) {
                $message = str_replace('{{name}}', $basketRow->getName(), $message);
                $mOptions = trim(Utils::parseBasketRowOptions($basketRow->getOptions()));
                if ($mOptions !== '') {
                    $messageOptions .= ' (' . $mOptions . ')';
                }
            }
        }
        $message = str_replace('{{options}}', $messageOptions, $message);

        $viewHelpersBasketWarning = ViewHelpersDtosBasketWarning::fillFromParent($basketWarning);
        $viewHelpersBasketWarning->setMessage($message);
        $viewHelpersBasketWarning->setMessageHash(hash('md5', $message . '-' . implode($hashes) . '-' . $label));
        return $viewHelpersBasketWarning;
    }

    /**
     * This method groups BasketWarning array into BasketWarningSeverity groups,
     * and returns an array with these keys, each one containing the corresponding warnings:
     * <ul>
     * <li>BasketWarningSeverity::ERROR</li>
     * <li>BasketWarningSeverity::WARNING</li>
     * <li>BasketWarningSeverity::INFO</li>
     * </ul>
     *
     * @param array $basketWarnings
     *            Array of BasketWarning
     *            
     * @return array
     */
    public static function groupBasketWarnings(array $basketWarnings = []): array {
        $groups = [
            BasketWarningSeverity::ERROR => [],
            BasketWarningSeverity::WARNING => [],
            BasketWarningSeverity::INFO => []
        ];

        foreach ($basketWarnings as $basketWarning) {
            $groups[$basketWarning->getSeverity()][] = $basketWarning;
        }

        return $groups;
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the end order.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>payResponse</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function endOrderMacro(array $arguments = []): array {
        $endOrder = new EndOrder($arguments, $this->languageSheet);
        return $endOrder->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the async order.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>validationResponse</li>
     * <li>postParameters</li>
     * <li>getParameters</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function asyncOrderMacro(array $arguments = []): array {
        $asyncOrder = new AsyncOrder($arguments, $this->languageSheet);
        return $asyncOrder->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the deniedOrderMessage.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>basket</li>
     * <li>errorCode</li>
     * <li>errorFields</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function deniedOrderMessageMacro(array $arguments = []): array {
        $basketDeniedOrderMessage = new DeniedOrderMessage($arguments);
        return $basketDeniedOrderMessage->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the deleteDiscountCodes.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>basket</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function deleteDiscountCodesMacro(array $arguments = []): array {
        $deleteDiscountCodes = new DeleteDiscountCodes($arguments);
        return $deleteDiscountCodes->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the redeemRewardPoints.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>basket</li>
     * <li>rewardPoints</li>
     * <li>showSelectableBox</li>
     * <li>class</li>
     * <li>quantityPlugin</li>
     * <li>showRewardPointsHeader</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function redeemRewardPointsMacro(array $arguments = []): array {
        $rewardPoints = new RedeemRewardPoints($arguments);
        return $rewardPoints->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the rewardPoints.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>basket</li>
     * <li>class</li>
     * <li>showHeader</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function rewardPointsMacro(array $arguments = []): array {
        $rewardPoints = new RewardPoints($arguments);
        return $rewardPoints->getViewParameters();
    }
}
