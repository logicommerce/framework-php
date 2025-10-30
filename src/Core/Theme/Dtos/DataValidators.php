<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'DataValidators' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Element
 *
 * @package FWK\Core\Theme\Dtos
 */
class DataValidators extends Element {
	use ElementTrait;

	public const NEW_USER_FAST_REGISTER_PARTICULAR = 'newUserFastRegisterParticular';

	public const NEW_USER_FAST_REGISTER_BUSINESS = 'newUserFastRegisterBusiness';

	public const NEW_USER_FAST_REGISTER_FREELANCE = 'newUserFastRegisterFreelance';

	public const NEW_USER_PARTICULAR = 'newUserParticular';

	public const NEW_USER_BUSINESS = 'newUserBusiness';

	public const NEW_USER_FREELANCE = 'newUserFreelance';

	public const UPDATE_USER_PARTICULAR = 'updateUserParticular';

	public const UPDATE_USER_BUSINESS = 'updateUserBusiness';

	public const UPDATE_USER_FREELANCE = 'updateUserFreelance';

	public const UPDATE_USER_BILLING_ADDRESS_PARTICULAR = 'updateUserBillingAddressParticular';

	public const UPDATE_USER_BILLING_ADDRESS_BUSINESS = 'updateUserBillingAddressBusiness';

	public const UPDATE_USER_BILLING_ADDRESS_FREELANCE = 'updateUserBillingAddressFreelance';

	public const BILLING_ADDRESS_PARTICULAR = 'billingAddressParticular';

	public const BILLING_ADDRESS_BUSINESS = 'billingAddressBusiness';

	public const BILLING_ADDRESS_FREELANCE = 'billingAddressFreelance';

	public const SHIPPING_ADDRESS = 'shippingAddress';

	public const CONTACT = 'contact';

	public const PRODUCT_CONTACT = 'productContact';

	protected string $newUserFastRegisterParticular = '';

	protected string $newUserFastRegisterBusiness = '';

	protected string $newUserFastRegisterFreelance = '';

	protected string $newUserParticular = '';

	protected string $newUserBusiness = '';

	protected string $newUserFreelance = '';

	protected string $updateUserParticular = '';

	protected string $updateUserBusiness = '';

	protected string $updateUserFreelance = '';

	protected string $updateUserBillingAddressParticular = '';

	protected string $updateUserBillingAddressBusiness = '';

	protected string $updateUserBillingAddressFreelance = '';

	protected string $billingAddressParticular = '';

	protected string $billingAddressBusiness = '';

	protected string $billingAddressFreelance = '';

	protected string $shippingAddress = '';

	protected string $contact = '';

	protected string $productContact = '';

	/**
	 * This method returns validation name form new User Fast Register Particular
	 *
	 * @return string
	 */
	public function getNewUserFastRegisterParticular(): string {
		return $this->newUserFastRegisterParticular;
	}

	/**
	 * This method returns validation name form new User Fast Register Business
	 *
	 * @return string
	 */
	public function getNewUserFastRegisterBusiness(): string {
		return $this->newUserFastRegisterBusiness;
	}

	/**
	 * This method returns validation name form new User Fast Register Freelance
	 *
	 * @return string
	 */
	public function getNewUserFastRegisterFreelance(): string {
		return $this->newUserFastRegisterFreelance;
	}

	/**
	 * This method returns validation name form new User Particular
	 *
	 * @return string
	 */
	public function getNewUserParticular(): string {
		return $this->newUserParticular;
	}

	/**
	 * This method returns validation name form new User Business
	 *
	 * @return string
	 */
	public function getNewUserBusiness(): string {
		return $this->newUserBusiness;
	}

	/**
	 * This method returns validation name form new User Freelance
	 *
	 * @return string
	 */
	public function getNewUserFreelance(): string {
		return $this->newUserFreelance;
	}

	/**
	 * This method returns validation name form update User Particular
	 *
	 * @return string
	 */
	public function getUpdateUserParticular(): string {
		return $this->updateUserParticular;
	}

	/**
	 * This method returns validation name form update User Business
	 *
	 * @return string
	 */
	public function getUpdateUserBusiness(): string {
		return $this->updateUserBusiness;
	}

	/**
	 * This method returns validation name form update User Freelance
	 *
	 * @return string
	 */
	public function getUpdateUserFreelance(): string {
		return $this->updateUserFreelance;
	}

	/**
	 * This method returns validation name form Update User Billing Address Particular
	 *
	 * @return string
	 */
	public function getUpdateUserBillingAddressParticular(): string {
		return $this->updateUserBillingAddressParticular;
	}

	/**
	 * This method returns validation name form Update User Billing Address Business
	 *
	 * @return string
	 */
	public function getUpdateUserBillingAddressBusiness(): string {
		return $this->updateUserBillingAddressBusiness;
	}

	/**
	 * This method returns validation name form Update User Billing Address Freelance
	 *
	 * @return string
	 */
	public function getUpdateUserBillingAddressFreelance(): string {
		return $this->updateUserBillingAddressFreelance;
	}

	/**
	 * This method returns validation name form Billing Address Particular
	 *
	 * @return string
	 */
	public function getBillingAddressParticular(): string {
		return $this->billingAddressParticular;
	}

	/**
	 * This method returns validation name form Billing Address Business
	 *
	 * @return string
	 */
	public function getBillingAddressBusiness(): string {
		return $this->billingAddressBusiness;
	}

	/**
	 * This method returns validation name form Billing Address Freelance
	 *
	 * @return string
	 */
	public function getBillingAddressFreelance(): string {
		return $this->billingAddressFreelance;
	}

	/**
	 * This method returns validation name form shipping Address
	 *
	 * @return string
	 */
	public function getShippingAddress(): string {
		return $this->shippingAddress;
	}

	/**
	 * This method returns validation name form Contact
	 *
	 * @return string
	 */
	public function getContact(): string {
		return $this->contact;
	}

	/**
	 * This method returns validation name form productContact
	 *
	 * @return string
	 */
	public function getProductContact(): string {
		return $this->productContact;
	}

}
