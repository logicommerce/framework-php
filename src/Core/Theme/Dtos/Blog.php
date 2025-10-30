<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'blog' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see Element
 *
 * @package FWK\Core\Theme\Dtos
 */
class Blog extends Element {
	use ElementTrait;

	public const POST_LIST = 'postList';

	private ?ItemList $postList = null;

	/**
	 * This method returns the postList configuration.
	 *
	 * @return ItemList|NULL
	 */
	public function getPostList(): ?ItemList {
	    return $this->postList;
	}

	private function setPostList(array $postList): void {
	    $this->postList = new ItemList($postList);
	}
}
