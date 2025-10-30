<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the NewsletterSubscriptionActions enumeration class.
 * This class declares enumerations related to theme configurations.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 * 
 * @see NewsletterSubscriptionActions::CHECK_STATUS
 * @see NewsletterSubscriptionActions::SUBSCRIBE
 * @see NewsletterSubscriptionActions::UNSUBSCRIBE
 *  
 * @see Enum
 * 
 * @package FWK\Enums
 */
abstract class NewsletterSubscriptionActions extends Enum {

    public const CHECK_STATUS = 'CHECK_STATUS';
    
    public const SUBSCRIBE = 'SUBSCRIBE';
    
    public const UNSUBSCRIBE = 'UNSUBSCRIBE';
    
}
