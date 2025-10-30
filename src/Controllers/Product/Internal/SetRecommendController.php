<?php

namespace FWK\Controllers\Product\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\Controllers\Traits\BuidlProductOptionParametersGroupTrait;
use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Product\RecommendParametersGroup;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Enums\Parameters;
use FWK\Enums\LanguageLabels;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Core\Form\FormFactory;
use FWK\Services\ProductService;
use SDK\Enums\RecommendItemType;
use SDK\Services\Parameters\Groups\Basket\BundleItemParametersGroup;
use SDK\Services\Parameters\Groups\Product\RecommendItemParametersGroup;

/**
 * This is the SetRecommendController class.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @uses BuidlProductOptionParametersGroupTrait
 * 
 * @package FWK\Controllers\Product\Internal
 */
class SetRecommendController extends BaseJsonController {
    use BuidlProductOptionParametersGroupTrait, CheckCaptcha;

    protected ?ProductService $productService = null;

    /**
     * This attribute is an RecommendParametersGroup instance needed to communicate with the SDK.
     */
    protected $recommendParameters;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->productService = Loader::service(Services::PRODUCT);
        $this->recommendParameters = new RecommendParametersGroup();
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::ITEM_RECOMMEND_RESPONSE_OK);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::ITEM_RECOMMEND_RESPONSE_KO);
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getRecommend()->getInputFilterParameters();
    }

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     *
     * @see FilterInputHandler
     */
    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_POST_DATA_OBJECT;
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->checkCaptcha();

        $this->appliedParameters += $this->productService->generateParametersGroupFromArray($this->recommendParameters, $this->getRequestParams());
        $this->appliedParameters[Parameters::ID] = $this->getRequestParam(Parameters::ID, true);
        $this->appliedParameters[Parameters::TYPE] = $this->getRequestParam(Parameters::TYPE, true);

        $recommendItemParametersGroup = new RecommendItemParametersGroup();

        $options = $this->getRequestParam(Parameters::OPTIONS, false, '');

        if (strlen($options)) {
            $options = json_decode($options, true);
        } else {
            $options = [];
        }

        if (!empty($options)) {
            $this->appliedParameters[Parameters::OPTIONS] = [];
            $productOptionsParameters = [];
            $appliedProductOptions = [];
            $bundleOptionsParameters = [];
            $appliedBundleOptions = [];

            if ($this->appliedParameters[Parameters::TYPE] === RecommendItemType::PRODUCT) {
                $this->parseOptions($options, $productOptionsParameters, $appliedProductOptions);
            } else if ($this->appliedParameters[Parameters::TYPE] === RecommendItemType::BUNDLE) {
                foreach ($options as $bundleOption) {
                    $bundleItemParametersGroup = new BundleItemParametersGroup();
                    if (!empty($bundleOption[Parameters::OPTIONS])) {
                        $bundleOptionsReferenceParameters = [];
                        $bundleOptionAppliedOptions = [];
                        $this->parseOptions($bundleOption[Parameters::OPTIONS], $bundleOptionsReferenceParameters, $bundleOptionAppliedOptions);
                        $appliedBundleOptions[] = array_merge(
                            $this->productService->generateParametersGroupFromArray(
                                $bundleItemParametersGroup,
                                array_merge($bundleOption, [Parameters::OPTIONS => $bundleOptionsReferenceParameters])
                            ),
                            [Parameters::OPTIONS => $bundleOptionAppliedOptions]
                        );
                        $bundleOptionsParameters[] = $bundleItemParametersGroup;
                    }
                }
            }

            $this->appliedParameters[Parameters::OPTIONS] = $this->productService->generateParametersGroupFromArray(
                $recommendItemParametersGroup,
                array_merge(
                    $options,
                    [Parameters::PRODUCT_OPTIONS => $productOptionsParameters],
                    [Parameters::BUNDLE_OPTIONS => $bundleOptionsParameters]
                )
            );
            $this->appliedParameters[Parameters::OPTIONS][Parameters::PRODUCT_OPTIONS] = $appliedProductOptions;
            $this->appliedParameters[Parameters::OPTIONS][Parameters::BUNDLE_OPTIONS] = $appliedBundleOptions;
            $requestParams[Parameters::OPTIONS] = $recommendItemParametersGroup;
        }

        $recommendItemParametersGroup->setId($this->appliedParameters[Parameters::ID]);
        $recommendItemParametersGroup->setType($this->appliedParameters[Parameters::TYPE]);
        $this->recommendParameters->addItem($recommendItemParametersGroup);
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        return $this->productService->recommend($this->recommendParameters);
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
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
