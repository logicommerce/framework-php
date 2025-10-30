<?php

namespace FWK\Controllers\Resources\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use SDK\Dtos\Common\Route;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use FWK\Enums\Services;
use FWK\Services\FormService;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\CustomFormSendMailAttachmentParametersGroup;
use SDK\Services\Parameters\Groups\CustomFormSendMailParametersGroup;
use FWK\Core\Controllers\Traits\CheckCaptcha;

/**
 * This is the SendMailController controller.
 * This class extends BaseJsonController (FWK\Core\Controllers\BaseJsonController), see this class.
 *
 * @see BaseJsonController
 * 
 * @uses CheckCaptcha
 *
 * @package FWK\Controllers\Resources\Internal
 */
class SendMailController extends BaseJsonController {
    use CheckCaptcha;

    private ?FormService $formService = null;

    protected ?CustomFormSendMailParametersGroup $customFormSendMailParametersGroup = null;

    private array $filterParams = [];

    /**
     * Constructor method.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        $this->filterParams = FilterInputFactory::getSendMailParameters();
        parent::__construct($route);
        $this->responseMessage = $this->language->getLabelValue(LanguageLabels::SEND_MAIL_SUCCESS, $this->responseMessage);
        $this->responseMessageError = $this->language->getLabelValue(LanguageLabels::SEND_MAIL_ERROR, $this->responseMessageError);
        $this->formService = Loader::service(Services::FORM);
        $this->filterParams = FormFactory::getSendMail($this->getRequestParam(Parameters::TYPE, true))->getInputFilterParameters();
        $this->requestParams += FilterInputHandler::getFilterFilterInputs(
            $this->getOriginParams(),
            $this->filterParams
        );
        $this->customFormSendMailParametersGroup = new CustomFormSendMailParametersGroup();
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
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return $this->filterParams;
    }

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     *
     * @see FilterInputHandler
     */
    protected function getTo(): string {
        $this->appliedParameters[Parameters::TO] = $this->getRequestParam(Parameters::TO, true);
        return $this->appliedParameters[Parameters::TO];
    }

    protected function getSubject(): string {
        $this->appliedParameters[Parameters::SUBJECT] = $this->getRequestParam(Parameters::SUBJECT, true);
        return $this->appliedParameters[Parameters::SUBJECT];
    }

    protected function getBody(): string {
        $this->appliedParameters[Parameters::BODY] = $this->getRequestParam(Parameters::BODY, true);
        return $this->appliedParameters[Parameters::BODY];
    }

    protected function getMailAccountPId(): string {
        return self::getTheme()->getConfiguration()->getCommerce()->getMailAccountPId();
    }

    protected function getAttachments(): array {
        $attachments = $this->getRequestParam(Parameters::ATTACHMENTS, false, []);
        $customFormSendMailAttachmentsParametersGroup = [];
        $customFormSendMailAttachmentsApplied = [];
        foreach ($attachments as $attachment) {
            $attachmentJson = json_decode($attachment, true);
            if (!is_null($attachmentJson)) {
                $attachmentValueParameters = FilterInputHandler::getFilterFilterInputs($attachmentJson, FilterInputFactory::getSendMailAttachmentParameters());
                $customFormSendMailAttachmentParametersGroup = new CustomFormSendMailAttachmentParametersGroup();
                $customFormSendMailAttachmentApplied = $this->formService->generateParametersGroupFromArray($customFormSendMailAttachmentParametersGroup, $attachmentValueParameters);
                $customFormSendMailAttachmentsParametersGroup[] = $customFormSendMailAttachmentParametersGroup;
                if (strlen($customFormSendMailAttachmentApplied[Parameters::DATA]) > MAX_LENGTH_APPLIED_PARAMETER_VALUE) {
                    $customFormSendMailAttachmentApplied[Parameters::DATA] = substr($customFormSendMailAttachmentApplied[Parameters::DATA], 0, MAX_LENGTH_APPLIED_PARAMETER_VALUE) . '...';
                }
                $customFormSendMailAttachmentsApplied[] = $customFormSendMailAttachmentApplied;
            }
        }
        $this->appliedParameters[Parameters::ATTACHMENTS] = $customFormSendMailAttachmentsApplied;
        return $customFormSendMailAttachmentsParametersGroup;
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
        $this->customFormSendMailParametersGroup->setTo($this->getTo());
        $this->customFormSendMailParametersGroup->setSubject($this->getSubject());
        $this->customFormSendMailParametersGroup->setBody($this->getBody());
        $this->customFormSendMailParametersGroup->setMailAccountPId($this->getMailAccountPId());
        $attachments = $this->getAttachments();
        if (!empty($attachments)) {
            $this->customFormSendMailParametersGroup->setAttachments($attachments);
        }
    }

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @return Element
     */
    protected function getResponseData(): ?Element {
        $this->checkCaptcha();
        return $this->formService->customFormSendMail($this->customFormSendMailParametersGroup);
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
