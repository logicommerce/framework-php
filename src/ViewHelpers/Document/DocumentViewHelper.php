<?php

namespace FWK\ViewHelpers\Document;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\ViewHelpers\Document\Macro\BillingInformation;
use FWK\ViewHelpers\Document\Macro\Buttons;
use FWK\ViewHelpers\Document\Macro\ConfirmOrder;
use FWK\ViewHelpers\Document\Macro\HeadquarterInformation;
use FWK\ViewHelpers\Document\Macro\DocumentInformation;
use FWK\ViewHelpers\Document\Macro\Document;
use FWK\ViewHelpers\Document\Macro\ShippingInformation;
use FWK\ViewHelpers\Document\Macro\PickingInformation;
use FWK\ViewHelpers\Document\Macro\RewardPoints;

/**
 * This is the DocumentViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of the order's view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see ViewHelper
 *
 * @see DocumentViewHelper::confirmOrderMacro()
 * @see DocumentViewHelper::buttonsMacro()
 * @see DocumentViewHelper::documentInformationMacro()
 * @see DocumentViewHelper::headquarterInformationMacro()
 * @see DocumentViewHelper::billingInformationMacro()
 * @see DocumentViewHelper::shippingInformationMacro()
 * @see DocumentViewHelper::pickingInformationMacro()
 * @see DocumentViewHelper::documentMacro()
 *
 * @package FWK\ViewHelpers\Document
 */
class DocumentViewHelper extends ViewHelper {

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the confirmOrder.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>order</li>
     * <li>showTransactionId</li>
     * <li>showAuthNumber</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function confirmOrderMacro(array $arguments = []): array {
        $confirmOrder = new ConfirmOrder($arguments);
        return $confirmOrder->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttons.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>orderId</li>
     * <li>showBackButton</li>
     * <li>showPrintButton</li>
     * <li>classList</li>
     * <li>token</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buttonsMacro(array $arguments = []): array {
        $buttons = new Buttons($arguments);
        return $buttons->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the order documentInformation output.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>document</li>
     * <li>showTransactionId</li>
     * <li>showAuthNumber</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function documentInformationMacro(array $arguments = []): array {
        $documentInformation = new DocumentInformation($arguments);
        return $documentInformation->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the headquarter information output.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>document</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function headquarterInformationMacro(array $arguments = []): array {
        $headquarterInformation = new HeadquarterInformation($arguments);
        return $headquarterInformation->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the billing address output.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>document</li>
     * <li>fields</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function billingInformationMacro(array $arguments = []): array {
        $billingInformation = new BillingInformation($arguments);
        return $billingInformation->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the shipping address output.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>document</li>
     * <li>fields</li>
     * <li>pickingFields</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function shippingInformationMacro(array $arguments = []): array {
        $shippingInformation = new ShippingInformation($arguments);
        return $shippingInformation->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the picking address output.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>document</li>
     * <li>fields</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function pickingInformationMacro(array $arguments = []): array {
        $pickingInformation = new PickingInformation($arguments);
        return $pickingInformation->getViewParameters();
    }

    /**
     * This method merges the given arguments and calculates and returns the view parameters for the output.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>document</li>
     * <li>mode</li>
     * <li>linkable</li>
     * <li>showOptions</li>
     * <li>showOptionValuesName</li>
     * <li>showOptionValuesSku</li>
     * <li>showZeroDiscount</li>
     * <li>giftsPresentation</li>
     * <li>showTaxDisclosure</li>
     * <li>showTaxIncluded</li>
     * <li>tableClass</li>
     * <li>showPrices</li>
     * <li>showImage</li>
     * <li>showSku</li>
     * <li>showManufacturerSku</li>
     * <li>showCustomTags</li>
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
     * <li>productsTemplate</li>
     * <li>productsBundleTemplate</li>
     * <li>productsBundleItemTemplate</li>
     * <li>productsGiftTemplate</li>
     * <li>productSelectableGiftTemplate</li>
     * <li>routeType</li>
     * <li>footerRows</li>
     * <li>disclosure</li>
     * <li>mergeRows</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function documentMacro(array $arguments = []): array {
        $documentDocument = new Document($arguments, $this->languageSheet);
        return $documentDocument->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the rewardPoints.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>document</li>
     * <li>class</li>
     * <li>showHeader</li>
     * <li>showTotalRedeemed</li>
     * <li>showTotalEarned</li>
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
