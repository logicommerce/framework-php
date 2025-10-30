<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Services\Parameters\Groups\User\UserVouchersParametersGroup;
use SDK\Services\Parameters\Groups\User\UserCustomTagsParametersGroup;

/**
 * This is the 'User' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Element
 *
 * @package FWK\Core\Theme\Dtos
 */
class User extends Element {
	use ElementTrait;

	public const USER_VOURCHER_PARAMETERS_GROUP = 'userVouchersParametersGroup';
	
	public const USER_CUSTOM_TAGS_PARAMETERS_GROUP  = 'userCustomTagsParametersGroup';

	private ?UserVouchersParametersGroup $userVouchersParametersGroup = null;
	
	private ?UserCustomTagsParametersGroup $userCustomTagsParametersGroup = null;

	/**
	 * This method returns the userVouchersParametersGroup configuration.
	 *
	 * @return UserVouchersParametersGroup|NULL
	 */
	public function getUserVouchersParametersGroup(): ?UserVouchersParametersGroup {
	    return $this->userVouchersParametersGroup;
	}
	
	/**
	 * This method returns the userCustomTagsParametersGroup configuration.
	 *
	 * @return UserCustomTagsParametersGroup|NULL
	 */
	public function getUserCustomTagsParametersGroup(): ?UserCustomTagsParametersGroup {
	    return $this->userCustomTagsParametersGroup;
	}
	
}
