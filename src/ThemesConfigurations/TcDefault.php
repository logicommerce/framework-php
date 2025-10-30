<?php

namespace FWK\ThemesConfigurations;

use FWK\Core\Theme\Tc;

/**
 * This is the TcDefault (default theme configuration) class.
 * The purpose of this class is to represent a default theme configuration.
 * <br>This class extends FWK\Core\Theme\Tc, see this class.
 *
 * @abstract
 *
 * @see Tc
 * 
 * @package FWK\ThemesConfigurations
 */
if (class_exists('SITE\Core\Theme\Tc')) {
    abstract class TcDefault extends \SITE\Core\Theme\Tc {
    }
} else {
    abstract class TcDefault extends Tc {
    }
}
