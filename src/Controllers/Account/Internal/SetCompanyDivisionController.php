<?php

namespace FWK\Controllers\Account\Internal;

use FWK\Core\Controllers\BaseJsonController;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Services;
use FWK\Enums\Parameters;
use FWK\Enums\RouteType;
use FWK\Services\AccountService;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\BatchRequests;
use SDK\Dtos\Common\Route;
use SDK\Services\Parameters\Groups\Account\CompanyDivisionsParametersGroup;
use SDK\Services\Parameters\Groups\Account\CompanyDivisionMasterParametersGroup;
use SDK\Services\Parameters\Groups\Account\RegisteredUserParametersGroup;
use SDK\Services\Parameters\Groups\Account\Addresses\AccountInvoicingAddressParametersGroup;
use SDK\Services\Parameters\Groups\LocationParametersGroup;

class SetCompanyDivisionController extends BaseJsonController {

    protected ?AccountService $accountService = null;

    public function __construct(Route $route) {
        parent::__construct($route);
        $this->accountService = Loader::service(Services::ACCOUNT);
    }

    protected function getFilterParams(): array {
        return FilterInputFactory::getRoleIdParameter()
            + FormFactory::getAccountCompanyDivisionCreate()->getInputFilterParameters()
            + FilterInputFactory::getLocationListParameters()
            + FilterInputFactory::getStateCityPostalParameters();
    }

    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_POST_DATA_OBJECT;
    }

    protected function getResponseData(): ?Element {
        $parentAccountId = $this->getRequestParam(Parameters::ID, true);
        $divisionParams = new CompanyDivisionsParametersGroup();

        $divisionImage = $this->getRequestParam(Parameters::IMAGE, false);
        if (trim($divisionImage) !== '') {
            $divisionParams->setImage($divisionImage);
        }

        $pId = $this->getRequestParam(Parameters::P_ID, false);
        if (trim($pId) !== '') {
            $divisionParams->setPId($pId);
        }

        $masterParams = new CompanyDivisionMasterParametersGroup();
        $job = $this->getRequestParam(Parameters::JOB, false);
        if (trim($job) !== '') {
            $masterParams->setJob($job);
        }

        $roleId = $this->getRequestParam(Parameters::ROLE_ID, false);
        if (trim($roleId) !== '') {
            $masterParams->setRoleId($roleId);
        }

        $registeredUserId = $this->getRequestParam(Parameters::REGISTERED_USER_ID, false);
        if (trim($registeredUserId) !== '') {
            $masterParams->setRegisteredUserId($registeredUserId);
        }

        $registeredUserParams = new RegisteredUserParametersGroup();
        $password = $this->getRequestParam(Parameters::PASSWORD, false);
        if (trim($password) !== '') {
            $registeredUserParams->setPassword($password);
        }
        $registeredUserPid = $this->getRequestParam(Parameters::REGISTERED_USER_P_ID, false);
        if (trim($registeredUserPid) !== '') {
            $registeredUserParams->setPId($registeredUserPid);
        }
        $gender = $this->getRequestParam(Parameters::GENDER, false);
        if (trim($gender) !== '') {
            $registeredUserParams->setGender($gender);
        }
        $firstName = $this->getRequestParam(Parameters::FIRST_NAME, false);
        if (trim($firstName) !== '') {
            $registeredUserParams->setFirstName($firstName);
        }
        $lastName = $this->getRequestParam(Parameters::LAST_NAME, false);
        if (trim($lastName) !== '') {
            $registeredUserParams->setLastName($lastName);
        }
        $email = $this->getRequestParam(Parameters::REGISTERED_USER_EMAIL, false);
        if (trim($email) !== '') {
            $registeredUserParams->setEmail($email);
        }
        $birthday = $this->getRequestParam(Parameters::BIRTHDAY, false);
        if (trim($birthday) !== '') {
            try {
                $birthdayDate = new \DateTime($birthday);
                $registeredUserParams->setBirthday($birthdayDate);
            } catch (\Exception $e) {
                // log or ignore invalid date
            }
        }
        $image = $this->getRequestParam(Parameters::REGISTERED_USER_IMAGE, false);
        if (trim($image) !== '') {
            $registeredUserParams->setImage($image);
        }

        $username = $this->getRequestParam(Parameters::REGISTERED_USER_USERNAME, false);
        if (trim($username) !== '') {
            $registeredUserParams->setUsername($username);
        }
        if (count($registeredUserParams->toArray()) > 0) {
            $masterParams->setRegisteredUser($registeredUserParams);
        }
        if (count($masterParams->toArray()) > 0) {
            $divisionParams->setMaster($masterParams);
        }

        $invoicingAddress = new AccountInvoicingAddressParametersGroup();

        $address = $this->getRequestParam(Parameters::ADDRESS, true);
        if (trim($address) !== '') {
            $invoicingAddress->setAddress($address);
        }

        $additionalInfo = $this->getRequestParam(Parameters::ADDRESS_ADDITIONAL_INFORMATION, false);
        if (trim($additionalInfo) !== '') {
            $invoicingAddress->setAddressAdditionalInformation($additionalInfo);
        }

        $number = $this->getRequestParam(Parameters::NUMBER, false);
        if (trim($number) !== '') {
            $invoicingAddress->setNumber($number);
        }

        $phone = $this->getRequestParam(Parameters::PHONE, false);
        if (trim($phone) !== '') {
            $invoicingAddress->setPhone($phone);
        }

        $mobile = $this->getRequestParam(Parameters::MOBILE, false);
        if (trim($mobile) !== '') {
            $invoicingAddress->setMobile($mobile);
        }

        $company = $this->getRequestParam(Parameters::COMPANY, false);
        if (trim($company) !== '') {
            $invoicingAddress->setCompany($company);
        }

        $vat = $this->getRequestParam(Parameters::VAT, false);
        if (trim($vat) !== '') {
            $invoicingAddress->setVat($vat);
        }
        $state = $this->getRequestParam(Parameters::STATE, false);
        if (trim($state) !== '') {
            $invoicingAddress->setState($state);
        }

        $city = $this->getRequestParam(Parameters::CITY, false);
        if (trim($city) !== '') {
            $invoicingAddress->setCity($city);
        }

        $postalCode = $this->getRequestParam(Parameters::POSTAL_CODE, false);
        if (trim($postalCode) !== '') {
            $invoicingAddress->setPostalCode($postalCode);
        }

        $location = new LocationParametersGroup();
        $country = $this->getRequestParam(Parameters::COUNTRY, false);
        if (trim($country) !== '' && $country != null) {
            $location->setCountryCode($country);
        }
        $locationId = $this->getRequestParam(Parameters::LOCATION_LIST, false);
        if (trim($locationId) !== '' && $locationId != null) {
            $location->setLocationId($locationId);
        }
        if (count($location->toArray()) > 0) {
            $invoicingAddress->setLocation($location);
        }

        $divisionParams->setInvoicingAddress($invoicingAddress);
        $response = $this->accountService->createCompanyDivisions($parentAccountId, $divisionParams);
        if (!is_null($response->getError())) {
            $this->responseMessageError = Utils::getErrorLabelValue($response);
        }

        $this->responseMessage = $this->language->getLabelValue(
            LanguageLabels::SAVED,
            $this->responseMessage
        );
        return $response;
    }

    protected function parseResponseData(Element $response): array {
        $data =  [
            Parameters::REDIRECT => RoutePaths::getPath(RouteType::ACCOUNT_COMPANY_STRUCTURE)
        ];
        return $data;
    }

    protected function setBatchData(BatchRequests $request): void {
        // No batch data needed
    }

    protected function setControllerBaseBatchData(BatchRequests $requests): void {
        // No base batch data
    }
}
