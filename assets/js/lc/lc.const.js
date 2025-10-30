/**
 * Development true|false
 * @type {bool}
 */
const DEVEL = true;

/**
 * Calendar plugin date format
 * @type {string}
 */
const CALENDAR_PLUGIN_DATE_FORMAT = 'DD/MM/YYYY';

/**
 * Time to close a modal on submit form or other modal close time uses
 * @type {number} milliseconds
 */
const MODAL_TIME_CLOSE = 4000;

/**
 * Framework
 */

/**
 * Framework basic button class
 * @type {string}
 */
const BTN_CLASS = 'btn';

/**
 * Framework default button class variant
 * @type {string}
 */
const BTN_SECONDARY_CLASS = BTN_CLASS + ' btn-secondary';

/**
 * Framework primary button class variant
 * @type {string}
 */
const BTN_PRIMARY_CLASS = BTN_CLASS + ' btn-primary';

/**
 * Framework primary button class variant
 * @type {string}
 */
const BTN_DANGER_CLASS = BTN_CLASS + ' btn-danger';

/**
 * Framework link button class variant
 * @type {string}
 */
const BTN_LINK_CLASS = BTN_CLASS + ' btn-link';

/**
 * Framework textual form controls—like <input>s, <select>s, and <textarea>s—are styled with the .form-control class.
 * @type {string}
 */
const FORM_CONTROL_CLASS = 'form-control';

/**
 * Framework textual form controls—like <select>s—are styled with the .form-select class in bootstrap 5.
 * @type {string}
 */
const FORM_SELECT_CLASS = 'form-select';

/**
 * Framework default table class
 * @type {string}
 */
const TABLE_CLASS = 'table';

/**
 * Framework default spinner html
 * @type {string}
 */
const DEFAULT_LOADING_SPINNER = '<div class="loading-document"><div class="spinner-border text-primary" role="status"></div></div>';

/**
 * Framework media query limit of 'md' --> media (min-width: 992px) used 4 eval if is mobile in some cases
 * @type {string}
 */
const MEDIA_MOBILE = 992;

/**
 * Cookies
 */

/**
 * Cookie used in lc.goDesktop.js
 * @type {string}
 */
const COOKIE_GO_DESKTOP = 'goDesktop';

/**
 * Cookie used in LC.NewsletterPopupForm
 * @type {string}
 */
const COOKIE_NEWSLETTER_POPUP = 'NEWSLETTERPOPUPALERT';

/**
 * Cookie used in LC.ComparisonCustomTagsForm
 * @type {string}
 */
const COOKIE_COMPARISON_CUSTOM_TAGS = 'COMPARISONCUSTOMTAGS';

/**
 * Cookie used in LC.CountrySelectorForm
 * @type {string}
 */
const COOKIE_COUNTRY_SELECTOR_POPUP = 'COUNTRYSELECTORPOPUP';

/**
 * Cookie used in LC.ConfirmAgePopupForm
 * @type {string}
 */
const COOKIE_CONFIRM_AGE_POPUP = 'CONFIRMAGEPOPUPALERT';

/**
 * Cookie used in LC.selectMapKindForm
 * @type {string}
 */
const COOKIE_SELECT_MAP_KIND_POPUP = 'SELECTMAPKINDMODALPOPUP';