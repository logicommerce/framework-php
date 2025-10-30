<?php

namespace FWK\Controllers\User\Internal;

use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Resources\Loader;
use FWK\Core\Form\FormFactory;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use FWK\Enums\LanguageLabels;
use FWK\Core\Controllers\BaseJsonController;
use FWK\Services\UserService;
use FWK\Enums\Parameters;
use FWK\Core\Resources\Utils;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\User\AddShoppingListRowParametersGroup;
use SDK\Services\Parameters\Groups\User\AddShoppingListRowReferenceParametersGroup;
use FWK\Core\Controllers\Traits\BuidlProductOptionParametersGroupTrait;
use FWK\Core\FilterInput\FilterInput;
use SDK\Core\Enums\MethodType;
use SDK\Services\Parameters\Groups\User\AddShoppingListRowsParametersGroup;
use FWK\Core\Controllers\Traits\CheckCaptcha;
use FWK\Core\Resources\Session;
use FWK\Twig\TwigLoader;
use SDK\Services\Parameters\Groups\Basket\BundleItemParametersGroup;
use FWK\Dtos\User\ShoppingListRow;

/**
 * This is the SetShoppingListRow controller class.
 * This class extends FWK\Core\Controllers\BaseJsonController, see this class.
 *
 * @see BaseJsonController
 * 
 * @uses BuidlProductOptionParametersGroupTrait
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\User\Internal
 */
class SetShoppingListRowController extends BaseJsonController {
    use BuidlProductOptionParametersGroupTrait, CheckCaptcha;

    protected bool $loggedInRequired = true;

    private ?UserService $userService = null;

    protected ?AddShoppingListRowParametersGroup $addShoppingListRowParametersGroup = null;

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        parent::__construct($route);
        $this->userService = Loader::service(Services::USER);
        $this->appliedParameters = [];
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::SAVED, $this->responseMessage);
        $this->addShoppingListRowParametersGroup = new AddShoppingListRowParametersGroup();
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FormFactory::getShoppingListRowNotes()->getInputFilterParameters() + [
            Parameters::REFERENCE => new FilterInput([
                FilterInput::CONFIGURATION_NO_FILTER => true
            ])
        ];
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
        $requestParams = $this->getRequestParams();

        $referenceAppliedParameters = [];
        $reference = $this->getRequestParam(Parameters::REFERENCE, false, []);
        if (!empty($reference)) {
            $productOptions = isset($reference[Parameters::PRODUCT_OPTIONS]) ? $reference[Parameters::PRODUCT_OPTIONS] : [];
            $productOptionsParameters = [];
            $appliedProductOptions = [];
            if (isset($productOptions) && !empty($productOptions)) {
                $this->parseOptions($productOptions, $productOptionsParameters, $appliedProductOptions);
            }

            $bundleOptionsParameters = [];
            $appliedBundleOptions = [];
            $referenceBundleItemsOptions = isset($reference[Parameters::BUNDLE_OPTIONS]) ? $reference[Parameters::BUNDLE_OPTIONS] : [];
            foreach ($referenceBundleItemsOptions as $bundleOption) {
                if (!empty($bundleOption[Parameters::OPTIONS])) {
                    $bundleItemParametersGroup = new BundleItemParametersGroup();
                    $bundleOptionsReferenceParameters = [];
                    $bundleOptionAppliedOptions = [];
                    $this->parseOptions($bundleOption[Parameters::OPTIONS], $bundleOptionsReferenceParameters, $bundleOptionAppliedOptions);
                    $appliedBundleOptions[] = array_merge(
                        $this->userService->generateParametersGroupFromArray(
                            $bundleItemParametersGroup,
                            array_merge($bundleOption, [Parameters::OPTIONS => $bundleOptionsReferenceParameters])
                        ),
                        [Parameters::OPTIONS => $bundleOptionAppliedOptions]
                    );
                    $bundleOptionsParameters[] = $bundleItemParametersGroup;
                }
            }

            $addShoppingListRowReferenceParametersGroup = new AddShoppingListRowReferenceParametersGroup();
            $referenceAppliedParameters = $this->userService->generateParametersGroupFromArray(
                $addShoppingListRowReferenceParametersGroup,
                array_merge(
                    $reference,
                    [Parameters::PRODUCT_OPTIONS => $productOptionsParameters],
                    [Parameters::BUNDLE_OPTIONS => $bundleOptionsParameters]
                )
            );
            $referenceAppliedParameters[Parameters::PRODUCT_OPTIONS] = $appliedProductOptions;
            $referenceAppliedParameters[Parameters::BUNDLE_OPTIONS] = $appliedBundleOptions;
            $requestParams[Parameters::REFERENCE] = $addShoppingListRowReferenceParametersGroup;
        }

        if ($this->getRequestParam(Parameters::TYPE, true) === MethodType::PUT) {
            $this->appliedParameters = $this->userService->generateParametersGroupFromArray($this->addShoppingListRowParametersGroup, $requestParams);
        } else {
            unset($requestParams[Parameters::SHOPPING_LIST_ID]);
            $this->appliedParameters = $this->userService->generateParametersGroupFromArray($this->addShoppingListRowParametersGroup, $requestParams);
        }
        $this->appliedParameters[Parameters::REFERENCE] = $referenceAppliedParameters;
        $this->appliedParameters[Parameters::TYPE] = $this->getRequestParam(Parameters::TYPE, true);
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        if (empty($this->getRequestParam(Parameters::REFERENCE, false, []))) {
            // check captcha for ShoppingListRowNotesForm
            $this->checkCaptcha();
        }
        if ($this->appliedParameters[Parameters::TYPE] === MethodType::PUT) {
            $this->appliedParameters[Parameters::ID] = $this->getRequestParam(Parameters::ID, true);
            $response = $this->userService->updateShoppingListRow($this->appliedParameters[Parameters::ID], $this->addShoppingListRowParametersGroup);
        } else {
            $addShoppingListRowsParametersGroup = new AddShoppingListRowsParametersGroup();
            $addShoppingListRowsParametersGroup->setItems([$this->addShoppingListRowParametersGroup]);
            $shoppingListId = $this->getRequestParam(Parameters::SHOPPING_LIST_ID, false, $this->getSession()->getShoppingList()->getDefaultOneId());
            $this->appliedParameters[Parameters::SHOPPING_LIST_ID] = $shoppingListId;
            $response = $this->userService->createShoppingListRow($shoppingListId, $addShoppingListRowsParametersGroup);
        }

        $this->responseMessageError = Utils::getErrorLabelValue($response);

        if (method_exists($response, 'getIncidences') && !empty($response->getIncidences())) {
            $this->responseMessage = '';
            $labels = $this->language->getLabels();
            foreach ($response->getIncidences() as $incidence) {
                $this->responseMessage .= $this->language->getLabelValue($labels['ERROR_CODE_' . $incidence->getDetail()->getCode()]) . '. ';
            }
        } else {
            $this->appliedParameters[Parameters::TEMPLATE] = $this->getRequestParam(Parameters::TEMPLATE, false);
            if (!is_null($this->appliedParameters[Parameters::TEMPLATE]) && !empty($response->getItems())) {
                $richRow = ShoppingListRow::fillFromParent($response->getItems()[0]);
                $data = [
                    'row' => $richRow,
                    'totalItems' => $richRow->getPriority(),
                    'shoppingListId' => $richRow->getShoppingListId(),
                    'movableShoppingLists' => array_filter($this->getSession()->getShoppingList()->getShoppingLists()->getItems(), fn ($shoppingList) => $shoppingList->getId() != $richRow->getShoppingListId())
                ];
                $twig = new TwigLoader(Session::getInstance()->getDefaultTheme());
                $twig->load($data +  $this->getDefaultData(), 0, true);
                $this->addTwigBaseFunctions($twig);
                $this->addTwigBaseExtensions($twig);
                $render = $twig->render($this->appliedParameters[Parameters::TEMPLATE], null, 'html');
                $this->responseMessage = Utils::outputJsonHtmlString($render);
            }
        }

        return $response;
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
