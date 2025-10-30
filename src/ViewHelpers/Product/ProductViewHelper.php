<?php

namespace FWK\ViewHelpers\Product;

use SDK\Dtos\Catalog\Product\Product;
use FWK\Core\Theme\Dtos\ApplicableFilter;
use FWK\Core\Theme\Dtos\ApplicableFilters;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Enums\Parameters;
use FWK\ViewHelpers\Product\Macro\BundleGrouping;
use FWK\ViewHelpers\Product\Macro\Bundles;
use FWK\ViewHelpers\Product\Macro\ButtonClearProductsFilter;
use FWK\ViewHelpers\Product\Macro\ButtonProductComparison;
use FWK\ViewHelpers\Product\Macro\ButtonDiscounts;
use FWK\ViewHelpers\Product\Macro\ButtonProductContact;
use FWK\ViewHelpers\Product\Macro\ButtonRecommend;
use FWK\ViewHelpers\Product\Macro\ButtonShoppingList;
use FWK\ViewHelpers\Product\Macro\ButtonWishlist;
use FWK\ViewHelpers\Product\Macro\BuyBundleForm;
use FWK\ViewHelpers\Product\Macro\BuyForm;
use FWK\ViewHelpers\Product\Macro\BuyFormOptions;
use FWK\ViewHelpers\Product\Macro\BuyFormProductOffset;
use FWK\ViewHelpers\Product\Macro\BuyFormQuantity;
use FWK\ViewHelpers\Product\Macro\BuyFormSubmit;
use FWK\ViewHelpers\Product\Macro\BuyProductForm;
use FWK\ViewHelpers\Product\Macro\Comments;
use FWK\ViewHelpers\Product\Macro\CommentsForm;
use FWK\ViewHelpers\Product\Macro\ContactForm;
use FWK\ViewHelpers\Product\Macro\Countdown;
use FWK\ViewHelpers\Product\Macro\Discounts;
use FWK\ViewHelpers\Product\Macro\FilterForm;
use FWK\ViewHelpers\Product\Macro\PriceByQuantity;
use FWK\ViewHelpers\Product\Macro\ProductComparison;
use FWK\ViewHelpers\Product\Macro\ProductComparisonPreview;
use FWK\ViewHelpers\Product\Macro\Property;
use FWK\ViewHelpers\Product\Macro\Rate;
use FWK\ViewHelpers\Product\Macro\RecommendForm;
use FWK\ViewHelpers\Product\Macro\RewardPoints;
use FWK\ViewHelpers\Product\Macro\RichSnippets;
use FWK\ViewHelpers\Product\Macro\StaticOptions;
use FWK\ViewHelpers\Product\Macro\StockAlertForm;

/**
 * This is the ProductViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of the product's view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see ProductViewHelper::getJsonData()
 * @see ProductViewHelper::bundlesMacro
 * @see ProductViewHelper::buttonClearProductsFilterMacro()
 * @see ProductViewHelper::filterFormMacro()
 * @see ProductViewHelper::buttonRecommendMacro()
 * @see ProductViewHelper::buttonDiscountsMacro()
 * @see ProductViewHelper::buyFormMacro()
 * @see ProductViewHelper::buyFormOptionsMacro()
 * @see ProductViewHelper::buyFormProductOffsetMacro()
 * @see ProductViewHelper::buyFormQuantityMacro()
 * @see ProductViewHelper::buyFormSubmitMacro()
 * @see ProductViewHelper::commentsFormMacro()
 * @see ProductViewHelper::commentsMacro()
 * @see ProductViewHelper::priceByQuantityMacro()
 * @see ProductViewHelper::propertyMacro()
 * @see ProductViewHelper::rateMacro()
 * @see ProductViewHelper::countdownMacro()
 * @see ProductViewHelper::buttonProductContactMacro()
 * @see ProductViewHelper::buttonWishlistMacro()
 * @see ProductViewHelper::buttonProductComparisonMacro()
 * @see ProductViewHelper::getFiltering() 
 * @see ProductViewHelper::discountsMacro() 
 * @see ProductViewHelper::contactFormMacro() 
 * @see ProductViewHelper::recommendFormMacro()
 * @see ProductViewHelper::stockAlertFormMacro()
 *
 * @see ViewHelper
 *
 * @package FWK\ViewHelpers\Product
 */
class ProductViewHelper extends ViewHelper {

    /**
     * This method returns a json containing the information of the given Product.
     * 
     * @param Product $product
     * 
     * @return array
     */
    public function getJsonData(Product $product = null): array {
        $productJsonData = new ProductJsonData($product);
        return $productJsonData->output();
    }

    /**
     * This method returns a json containing the grid information of the given Product.
     * 
     * @param Product $product
     * 
     * @return array
     */
    public function getJsonGridData(Product $product = null): array {
        $productGirdJsonData = new ProductGridJsonData($product);
        return $productGirdJsonData->output();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the bundle macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>productBundles</li>
     *      <li>showBundleDefinition</li>
     *      <li>showBundleDefinitionName</li>
     *      <li>showBundleDefinitionDescription</li>
     *      <li>showUniqueUnit</li>
     *      <li>buyFormOptionsArgs</li>
     *      <li>buyBundleForm</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function bundlesMacro(array $arguments = []): array {
        $bundle = new Bundles($arguments);
        return $bundle->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the bundleGrouping macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>bundleGrouping</li>
     *      <li>showBundleDefinition</li>
     *      <li>showBundleDefinitionName</li>
     *      <li>showBundleDefinitionDescription</li>
     *      <li>showUniqueUnit</li>
     *      <li>buyFormOptionsArgs</li>
     *      <li>bundleId</li>
     *      <li>products</li>
     *      <li>shoppingListRowId</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function bundleGroupingMacro(array $arguments = []): array {
        $bundle = new BundleGrouping($arguments);
        return $bundle->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttonClearProductsFilter.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>class</li>
     *      <li>show</li>
     *      <li>hrefAllParams</li>
     *      <li>output</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buttonClearProductsFilterMacro(array $arguments = []): array {
        $buttonClearProductsFilter = new ButtonClearProductsFilter($arguments);
        return $buttonClearProductsFilter->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttonDiscounts.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>discounts</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buttonDiscountsMacro(array $arguments = []): array {
        $buttonDiscounts = new ButtonDiscounts($arguments);
        return $buttonDiscounts->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the richSnippets.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>richSnippets</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function richSnippetsMacro(array $arguments = []): array {
        $richSnippets = new RichSnippets($arguments);
        return $richSnippets->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the filterForm.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>filters</li>
     *      <li>applicableFilters</li>
     *      <li>appliedFilters</li>
     *      <li>defaultParametersValues</li>
     *      <li>autosubmit</li>
     *      <li>filterItemTemplate</li>
     *      <li>helperFilters</li>
     *      <li>notEnabledFilters</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function filterFormMacro(array $arguments = []): array {
        $filterForm = new FilterForm($arguments);
        return $filterForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttonRecommend.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>id</li>
     *      <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buttonRecommendMacro(array $arguments = []): array {
        $buttonRecommend = new ButtonRecommend($arguments);
        return $buttonRecommend->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buyForm.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>product</li>
     *      <li>class</li>
     *      <li>style</li>
     *      <li>content</li>
     *      <li>sectionId</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buyFormMacro(array $arguments = []): array {
        $buyForm = new BuyForm($arguments);
        return $buyForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buyBundleForm.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>class</li>
     *      <li>showQuantity</li>
     *      <li>showBundleBuyTitle</li>
     *      <li>showQuantitySelectableBox</li>
     *      <li>bundleGroupingContent</li>
     *      <li>bundleGrouping</li>
     *      <li>minQuantity</li>
     *      <li>maxQuantity</li>
     *      <li>priceWithTaxes</li>
     *      <li>showTaxText</li>
     *      <li>showPrice</li>
     *      <li>showBasePrice</li>
     *      <li>showSaving</li>
     *      <li>isAlternativePrice</li>
     *      <li>taxText</li>
     *      <li>appliedTaxes</li>
     *      <li>mainProducts</li>
     *      <li>quantityPlugin</li>
     *      <li>bundleId</li>
     *      <li>showLabel</li>
     *      <li>expressCheckout</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buyBundleFormMacro(array $arguments = []): array {
        $buyBundleForm = new BuyBundleForm($arguments, $this->languageSheet);
        return $buyBundleForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buyProductForm.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>product</li>
     *      <li>class</li>
     *      <li>style</li>
     *      <li>content</li>
     *      <li>sectionId</li>
     *      <li>shoppingListRowId</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buyProductFormMacro(array $arguments = []): array {
        $buyProductForm = new BuyProductForm($arguments);
        return $buyProductForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buyFormOptions.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>product</li>
     *      <li>showShortDescription</li>
     *      <li>showLongDescription</li>
     *      <li>showUnavailableLabel</li>
     *      <li>selectDefaults</li>
     *      <li>showImageOptions</li>
     *      <li>showBasePrice</li>
     *      <li>showRetailPrice</li>
     *      <li>showTitleInLabelUniqueImage</li>
     *      <li>bundleDefinitionSectionItemId</li>
     *      <li>useUrlOptionsParams</li>
     *      <li>addOptionsToProductLink</li>
     *      <li>optionReferences</li>
     *      <li>showGridFirst</li>
     *      <li>showGridImageValues</li>
     *      <li>showAsGridQuantityPlugin</li>
     *      <li>showAsGridUniqueDimension</li>
     *      <li>showGridAvailabilityImage</li>
     *      <li>showGridAvailabilityName</li>
     *      <li>showGridDisabled</li>
     *      <li>showOrderBox</li>
     *      <li>useFilePlugin</li>
     *      <li>attachmentAcceptAttribute</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buyFormOptionsMacro(array $arguments = []): array {
        $buyFormOptions = new BuyFormOptions($arguments);
        return $buyFormOptions->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buyFormProductOffset.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>class</li>
     *      <li>showOrderBox</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buyFormProductOffsetMacro(array $arguments = []): array {
        $buyFormProductOffset = new BuyFormProductOffset($arguments);
        return $buyFormProductOffset->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buyFormQuantity.
     * The array keys of the returned parameters are:
     * <ul>
     *		<li>product</li>
     *		<li>class</li>
     *		<li>quantityPlugin</li>
     *		<li>showSelectableBox</li>
     *		<li>selectableBoxRows</li>
     *		<li>manualMinQuantity</li>
     *		<li>manualMaxQuantity</li>
     *		<li>minOrderQuantity</li>
     *		<li>maxOrderQuantity</li>
     *		<li>multipleOrderQuantity</li>
     *		<li>multipleActsOver</li>
     *		<li>minQuantity</li>
     *		<li>maxQuantity</li>
     *		<li>forceMinQuantityZero</li>
     *		<li>showOrderBox</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buyFormQuantityMacro(array $arguments = []): array {
        $buyFormQuantity = new BuyFormQuantity($arguments);
        return $buyFormQuantity->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buyFormSubmit.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>id</li>
     *      <li>showOrderBox</li>
     *      <li>class</li>
     *      <li>showLabel</li>
     *      <li>formButtonHook</li>
     *      <li>expressCheckout</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buyFormSubmitMacro(array $arguments = []): array {
        $buyFormSubmit = new BuyFormSubmit($arguments);
        return $buyFormSubmit->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the commentsForm.
     * The array keys of the returned parameters are:
     * <ul>
     *		<li>form</li>
     *		<li>configuration</li>
     *		<li>output</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function commentsFormMacro(array $arguments = []): array {
        $commentsForm = new CommentsForm($arguments, $this->session);
        return $commentsForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the comments.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>comments</li>
     *      <li>roundedRating</li>
     *      <li>roundedIntRating</li>
     *      <li>worstRating</li>
     *      <li>bestRating</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function commentsMacro(array $arguments = []): array {
        $comments = new Comments($arguments);
        return $comments->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the priceByQuantity.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>product</li>
     *      <li>pricesByQuantity</li>
     *      <li>tableClassName</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function priceByQuantityMacro(array $arguments = []): array {
        $priceByQuantity = new PriceByQuantity($arguments, $this->languageSheet);
        return $priceByQuantity->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the property.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>product</li>
     *      <li>property</li>
     *      <li>showTaxText</li>
     *      <li>priceWithTaxes</li>
     *      <li>availabilities</li>
     *      <li>isAlternativePrice</li>
     *      <li>taxText</li>
     *      <li>appliedTaxes</li>
     *      <li>stockAlertButton</li>
     *      <li>stockAlertButtonClass</li>
     *      <li>showStock</li>
     *      <li>showAvailabilityName</li>
     *      <li>showStockText</li>
     *      <li>showAvailabilityImage</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function propertyMacro(array $arguments = []): array {
        $property = new Property($arguments, $this->languageSheet);
        return $property->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the rate.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>comments</li>
     *      <li>roundedRating</li>
     *      <li>roundedIntRating</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function rateMacro(array $arguments = []): array {
        $rate = new Rate($arguments);
        return $rate->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the countdown.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>product</li>
     *      <li>eventTime</li>
     *      <li>callback</li>
     *      <li>template</li>
     *      <li>categoryId</li>
     *      <li>endDate</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function countdownMacro(array $arguments = []): array {
        $countdown = new Countdown($arguments, $this->languageSheet);
        return $countdown->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttonProductContact.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>id</li>
     *      <li>class</li>
     *      <li>showLabel</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buttonProductContactMacro(array $arguments = []): array {
        $buttonProductContact = new ButtonProductContact($arguments);
        return $buttonProductContact->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttonShoppingList.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>item</li>
     *      <li>type</li>
     *      <li>showLabel</li>
     *      <li>allowDelete</li>
     *      <li>class</li>
     *      <li>showDefaultShoppingListButton</li>
     *      <li>showShoppingLists</li>
     *      <li>allowAddShoppingList</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buttonShoppingListMacro(array $arguments = []): array {
        $buttonShoppingList = new ButtonShoppingList($arguments);
        return $buttonShoppingList->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttonWishlist.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>product</li>
     *      <li>type</li>
     *      <li>showLabel</li>
     *      <li>allowDelete</li>
     *      <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     * @deprecated
     */
    public function buttonWishlistMacro(array $arguments = []): array {
        //trigger_error("The function 'buttonWishlistMacro' will be deprecated soon. you must use 'buttonShoppingListMacro'", E_USER_NOTICE);
        $buttonWishlist = new ButtonWishlist($arguments);
        return $buttonWishlist->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the buttonProductComparison.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>product</li>
     *      <li>type</li>
     *      <li>showLabel</li>
     *      <li>allowDelete</li>
     *      <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function buttonProductComparisonMacro(array $arguments = []): array {
        $buttonProductComparison = new ButtonProductComparison($arguments);
        return $buttonProductComparison->getViewParameters();
    }

    /**
     * This method returns true if any of the filters is applied (based on the given parameters: 
     * ApplicableFitlers object and productsFilter object (from FiltrableProductListTrait)).
     * It returns false otherwise.
     *
     * @param ApplicableFilters $applicableFilters
     * @param array $appliedFilters
     *
     * @return bool
     */
    public static function getFiltering(ApplicableFilters $applicableFilters, array $appliedFilters): bool {
        $brandList = self::isEnabledFilter($applicableFilters->getBrandsList(), isset($appliedFilters[Parameters::BRANDS_LIST]));
        $categoryList = self::isEnabledFilter($applicableFilters->getCategoryIdList(), isset($appliedFilters[Parameters::CATEGORY_ID_LIST]));
        $filterCustomTag = self::isEnabledFilter($applicableFilters->getFilterCustomTag(), isset($appliedFilters[Parameters::FILTER_CUSTOMTAG]));
        $filterCustomTagGroup = self::isEnabledFilter($applicableFilters->getFilterCustomTagGroup(), isset($appliedFilters[Parameters::FILTER_CUSTOMTAG]));
        $filterCustomTagInterval = self::isEnabledFilter($applicableFilters->getFilterCustomTag(), isset($appliedFilters[Parameters::FILTER_CUSTOMTAG_INTERVAL]));
        $filterOption = self::isEnabledFilter($applicableFilters->getFilterOption(), isset($appliedFilters[Parameters::FILTER_OPTION]));
        $onlyFeatured = self::isEnabledFilter($applicableFilters->getOnlyFeatured(), isset($appliedFilters[Parameters::ONLY_FEATURED]));
        $onlyOffers = self::isEnabledFilter($applicableFilters->getOnlyOffers(), isset($appliedFilters[Parameters::ONLY_OFFERS]));
        $priceRange = self::isEnabledFilter($applicableFilters->getPriceRange(), isset($appliedFilters[Parameters::FROM_PRICE]) && isset($appliedFilters[Parameters::TO_PRICE]));
        $q = self::isEnabledFilter($applicableFilters->getQ(), isset($appliedFilters[Parameters::Q]) && strlen($appliedFilters[Parameters::Q]));

        if ($brandList || $categoryList || $filterCustomTag || $filterCustomTagGroup || $filterCustomTagInterval || $filterOption || $onlyFeatured || $onlyOffers || $priceRange || $q) {
            return true;
        }
        return false;
    }

    /**
     * This method returns true if a is applied if exists.
     *
     * @param ?ApplicableFilter $applicableFilter
     * @param bool $existsInAppliedFilters
     *
     * @return bool
     */
    private static function isEnabledFilter(?ApplicableFilter $applicableFilter, bool $existsInAppliedFilters): bool {
        if (is_null($applicableFilter)) {
            return false;
        }
        return $applicableFilter->isEnabled() === true && $existsInAppliedFilters === true;
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the discounts macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>discounts</li>
     *      <li>productId</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function discountsMacro(array $arguments = []): array {
        $discounts = new Discounts($arguments);
        return $discounts->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the contactForm macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function contactFormMacro(array $arguments = []): array {
        $contactForm = new ContactForm($arguments);
        return $contactForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the recommend form macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function recommendFormMacro(array $arguments = []): array {
        $recommend = new RecommendForm($arguments);
        return $recommend->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the rewardPoints form macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>rewardPoints</li>
     *      <li>showHeader</li>
     *      <li>showRulesHeader</li>
     *      <li>showRulesCondition</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function rewardPointsMacro(array $arguments = []): array {
        $rewardPoints = new rewardPoints($arguments);
        return $rewardPoints->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the stockAlertForm macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function stockAlertFormMacro(array $arguments = []): array {
        $stockAlert = new StockAlertForm($arguments);
        return $stockAlert->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the productComparison macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function productComparisonMacro(array $arguments = []): array {
        $productComparison = new ProductComparison($arguments);
        return $productComparison->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the productComparisonPreview macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>form</li>
     *      <li>class</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function productComparisonPreviewMacro(array $arguments = []): array {
        $productComparisonPreview = new ProductComparisonPreview($arguments);
        return $productComparisonPreview->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the staticOptions macro.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>optionReferences</li>
     *      <li>product</li>
     *      <li>showImageValues</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function staticOptionsMacro(array $arguments = []): array {
        $staticOptions = new StaticOptions($arguments);
        return $staticOptions->getViewParameters();
    }
}
