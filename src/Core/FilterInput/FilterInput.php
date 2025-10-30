<?php

namespace FWK\Core\FilterInput;

use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Utils;
use SDK\Enums\ProductSort;

/**
 * This is the FilterInput class.
 * The purpose of this class is to filter (validate and sanitize) the given values according to the given filter configuration.
 * <br>Use examples: It is commonly used to filter the parameters of a request,...
 *
 * @see FilterInput::getFilterValue()
 *
 * @package FWK\Core\FilterInput
 *         
 * @link https://www.php.net/manual/es/filter.filters.sanitize.php
 */
class FilterInput {

    private $configurationFilter;

    private $filterSanitizeId;

    private $filterFlags;

    private $filterValidateId;

    private $allowTags;

    private $allowEmpty;

    private $availableValues;

    private $regexValidate;

    private $strFormat;

    private $enableModification;

    private $functionValidator;

    private $functionValidatorExplondeParamSeparator;

    private $noFilter;

    private $dateTimeFormat;

    public const CONFIGURATION_NO_FILTER = 'noFilter';

    public const CONFIGURATION_ALLOW_EMPTY = 'allowEmpty';

    public const CONFIGURATION_DATE_TIME_FORMAT = 'dateTimeFormat';

    public const CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION = 'enableModification';

    public const CONFIGURATION_FILTER_KEY_FILTER_SANITIZE_ID = 'filterSanitizeId';

    public const CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID = 'filterValidateId';

    public const CONFIGURATION_FILTER_KEY_FILTER_FLAGS = 'filterFlags';

    public const CONFIGURATION_FILTER_KEY_ALLOW_TAGS = 'allowTags';

    public const CONFIGURATION_FILTER_KEY_REGEX_VALIDATE = 'regexValidate';

    public const CONFIGURATION_FILTER_KEY_STRING_FORMAT = 'strFormat';

    public const CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES = 'availableValues';

    public const CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR = 'functionValidator';

    public const CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR = 'functionValidatorExplondeParamSeparator';

    public const STR_FORMAT_TO_LOWER = 1;

    public const STR_FORMAT_TO_UPPER = 2;

    public const STR_FORMAT_CAPITAL = 3;

    public const STR_FORMAT_HTML_CLEAR = 4;

    public const REGEX_VALIDATE_SORT_DIRECTION = '/^' . ProductSort::SORT_DIRECTION_ASC . '$|^' . ProductSort::SORT_DIRECTION_DESC . '$/';

    public const REGEX_VALIDATE_RANGE_SEPARATOR = ';';

    public const REGEX_VALIDATE_FLOAT_RANGE = '/^([0-9]*[.])?[0-9]+' . self::REGEX_VALIDATE_RANGE_SEPARATOR . '([0-9]*[.])?[0-9]+$/';

    public const REGEX_VALIDATE_INT_RANGE = '/^[0-9]+' . self::REGEX_VALIDATE_RANGE_SEPARATOR . '[0-9]+$/';

    public const REGEX_VALIDATE_INT_LIST = '/^(([0-9](,)?)*)+$/';

    public const REGEX_VALIDATE_INTERVAL = '/^([0-9]*[.])?[0-9]+' . self::REGEX_VALIDATE_RANGE_SEPARATOR . '([0-9]*[.])?[0-9]+$|^(([0-9]*[.])?[0-9])$/';

    /**
     * Constructor method.
     * This method constructs a FilterInput with the given configuration.
     * <br>You can set the following configuration keys for the FilterInput:
     * <ul>
     * <li>FilterInput::CONFIGURATION_NO_FILTER
     * ->To indicate whether or not to apply the filter.
     * Possible values[true/false].
     * Default = false.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_ALLOW_EMPTY
     * ->To indicate whether or not allow empty values.
     * Possible values[true/false].
     * Default = false.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_DATE_TIME_FORMAT
     * ->To indicate that the value to sanitize must return a DateTime object and the date time format to apply.
     * Possible values: \DateTime const formats
     * Default = null.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION
     * ->To indicate wheter or not to allow the modification of the original given value to be filtered. If false, then some sanitization actions can not be applied and in these cases the value will not pass de filter.
     * Possible values[true/false].
     * Default = true.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_FILTER_SANITIZE_ID
     * ->To indicate the php sanitization to be applied.
     * Possible values[{@link http://www.php.net/manual/en/filter.constants.php}].
     * Default = FILTER_UNSAFE_RAW.
     * See reference {@link https://www.php.net/manual/en/function.filter-var.php}</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID
     * ->If set, then the indicated php filter will be applied as part of the filter.
     * Possible values[{@link http://www.php.net/manual/en/filter.constants.php}].
     * Default = null.
     * See reference {@link https://www.php.net/manual/en/function.filter-var.php}</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_FILTER_FLAGS
     * ->To indicate the php options for the SANITIZE_ID and VALIDATE_ID (if filter/sanitization accepts options, an array of php flags can be provided).
     * Possible values [see {@link https://www.php.net/manual/en/function.filter-var.php}]
     * Default = null.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_ALLOW_TAGS
     * ->To indicate whether or not apply filter to avoid tags (HTML, js, XML or PHP).
     * Possible values [true/false]
     * Default = false.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE
     * ->If set, the provided regular expression will be applied as part of the filter.
     * Possible values [The REGEX pattern to be checked, as a string].
     * Default = null.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES
     * ->If set, this filter considers the value to be filtered as a string list sepparated by commas, and checks if all the elements of this list matches with the given set of valid values (set in this configuration) and sanitizes those that not match.
     * Possible values [array of strings containing the set of possible values].
     * Default = null.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_STRING_FORMAT
     * ->If set, then the returned filtered value will be formatted with the format indicated in this configuration option.
     * Possible values [self::STR_FORMAT_CAPITAL, self::STR_FORMAT_TO_LOWER, self::STR_FORMAT_TO_UPPER, self::STR_FORMAT_HTML_CLEAR].
     * Default = null.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR
     * ->If set, then the function indicated in this configuration option will be passed as part of the filter.
     * Possible values [a callable function]
     * Default = null.</li>
     * <br>
     * <li>FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR
     * ->Related to FilterInput::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR.
     * If set, then the validated value will be splitted with the separator indicated in this configuration option and the splitted elements will be passed as a list of parameters to the function indicated in the FUNCTION_VALIDATOR configuration option.
     * Default = null.</li>
     * </ul>
     *
     * @param array $configurationFilter
     *            Configuration of the filter.
     */
    public function __construct(array $configurationFilter = []) {
        $this->configurationFilter = $configurationFilter;
        $defaultFilter = [
            self::CONFIGURATION_NO_FILTER => false,
            self::CONFIGURATION_ALLOW_EMPTY => false,
            self::CONFIGURATION_DATE_TIME_FORMAT => '',
            self::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES => null,
            self::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => true,
            self::CONFIGURATION_FILTER_KEY_FILTER_FLAGS => null,
            self::CONFIGURATION_FILTER_KEY_ALLOW_TAGS => false,
            self::CONFIGURATION_FILTER_KEY_FILTER_SANITIZE_ID => FILTER_UNSAFE_RAW,
            self::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => null,
            self::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR => null,
            self::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR => null,
            self::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE => null,
            self::CONFIGURATION_FILTER_KEY_STRING_FORMAT => null
        ];
        $filter = array_merge($defaultFilter, $this->configurationFilter);

        $this->noFilter = Utils::setValidateType(Utils::TYPE_BOOLEAN, $filter[self::CONFIGURATION_NO_FILTER]);

        $this->allowEmpty = Utils::setValidateType(Utils::TYPE_BOOLEAN, $filter[self::CONFIGURATION_ALLOW_EMPTY]);

        $this->dateTimeFormat = Utils::setValidateType(Utils::TYPE_STRING, $filter[self::CONFIGURATION_DATE_TIME_FORMAT]) ? $filter[self::CONFIGURATION_DATE_TIME_FORMAT] : null;

        $this->enableModification = Utils::setValidateType(Utils::TYPE_BOOLEAN, $filter[self::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION]);

        // filter_var
        $this->filterSanitizeId = $filter[self::CONFIGURATION_FILTER_KEY_FILTER_SANITIZE_ID] !== null ? Utils::setValidateType(Utils::TYPE_INT, $filter[self::CONFIGURATION_FILTER_KEY_FILTER_SANITIZE_ID]) : $filter[self::CONFIGURATION_FILTER_KEY_FILTER_SANITIZE_ID];
        $this->filterValidateId = $filter[self::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID] !== null ? Utils::setValidateType(Utils::TYPE_INT, $filter[self::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID]) : $filter[self::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID];
        $this->filterFlags = ($filter[self::CONFIGURATION_FILTER_KEY_FILTER_FLAGS] !== null && count($filter[self::CONFIGURATION_FILTER_KEY_FILTER_FLAGS]) ? Utils::setValidateType(Utils::TYPE_ARRAY, $filter[self::CONFIGURATION_FILTER_KEY_FILTER_FLAGS]) : 0);
        $this->allowTags = Utils::setValidateType(Utils::TYPE_BOOLEAN, $filter[self::CONFIGURATION_FILTER_KEY_ALLOW_TAGS]);
        // preg_match
        $this->regexValidate = ($filter[self::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE] !== null && strlen($filter[self::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE])) ? Utils::setValidateType(Utils::TYPE_STRING, $filter[self::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE]) : $filter[self::CONFIGURATION_FILTER_KEY_REGEX_VALIDATE];
        // in_array
        $this->availableValues = $filter[self::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES] !== null ? Utils::setValidateType(Utils::TYPE_ARRAY, $filter[self::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES]) : $filter[self::CONFIGURATION_FILTER_KEY_AVAILABLE_VALUES];
        // String format
        $this->strFormat = $filter[self::CONFIGURATION_FILTER_KEY_STRING_FORMAT] !== null ? Utils::setValidateType(Utils::TYPE_INT, $filter[self::CONFIGURATION_FILTER_KEY_STRING_FORMAT]) : $filter[self::CONFIGURATION_FILTER_KEY_STRING_FORMAT];
        // Function validator, return must be cast as boolean
        $this->functionValidator = $filter[self::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR] !== null ? Utils::setValidateType(Utils::TYPE_CALLABLE, $filter[self::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR]) : $filter[self::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR];
        $this->functionValidatorExplondeParamSeparator = $filter[self::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR] !== null ? Utils::setValidateType(Utils::TYPE_STRING, $filter[self::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR]) : $filter[self::CONFIGURATION_FILTER_KEY_FUNCTION_VALIDATOR_EXPLONDE_PARAM_SEPARATOR];
    }

    public function getConfigurationFilter(): array {
        return $this->configurationFilter;
    }

    private function setStrFormat(string $value): string {
        switch ($this->strFormat) {
            case self::STR_FORMAT_CAPITAL:
                $filterValue = ucfirst(strtolower($value));
                break;
            case self::STR_FORMAT_TO_LOWER:
                $filterValue = strtolower($value);
                break;
            case self::STR_FORMAT_TO_UPPER:
                $filterValue = strtoupper($value);
                break;
            case self::STR_FORMAT_HTML_CLEAR:
                $filterValue = strip_tags($value);
                break;
            default:
                throw new CommerceException("Undefined string format function: '" . $this->strFormat . "' check available conts RequestParam::STR_FORMAT*", CommerceException::FILTER_INPUT_UNDEFINED_STRING_FORMAT);
        }
        return $filterValue;
    }

    public function stripTags(string $string): string {
        return strip_tags($string);
    }

    /**
     * This method applies the filter (validation and sanitization) to the given value according to the set filter configuration.
     * If the value passes the filter then this method returns the filtered value, else it returns null.
     *
     * @param mixed $value
     *
     * @return NULL|string If the value don't passes the filter return null, else return the filtered value
     */
    public function getFilterValue($value) {
        if (is_string($value) && !$this->allowTags) {
            $regex = "/<([^>]*(<|$))/";
            do {
                $value = preg_replace($regex, "&lt;$1", $value);
            } while (preg_match($regex, $value));
            $value = html_entity_decode(strip_tags($value), ENT_NOQUOTES, "UTF-8");
        }

        if ($this->noFilter) {
            return $value;
        }

        if (is_array($value)) {
            return null;
        }

        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        $value = trim($value);
        if (!$this->allowEmpty && !strLen($value)) {
            return null;
        }
        $filterValue = $value;

        // set StrFormat
        if ($this->strFormat !== null) {
            $filterValue = $this->setStrFormat($filterValue);
        }

        // Apply filter sanitizer
        $filterValue = trim(filter_var($filterValue, $this->filterSanitizeId, $this->filterFlags));

        // checking enable modification
        if (!$this->enableModification && !(strtolower($value) === strtolower($filterValue))) {
            return null;
        }

        // checking filter validate
        if ($this->filterValidateId !== null && (
            (!filter_var($filterValue, $this->filterValidateId, $this->filterFlags) && $this->filterValidateId != FILTER_VALIDATE_BOOLEAN && $this->filterValidateId != FILTER_VALIDATE_INT) ||
            ($this->filterValidateId === FILTER_VALIDATE_BOOLEAN && is_null(filter_var($filterValue, $this->filterValidateId, FILTER_NULL_ON_FAILURE))) ||
            ($this->filterValidateId === FILTER_VALIDATE_INT && !(filter_var($filterValue, FILTER_VALIDATE_INT, $this->filterFlags) === 0 || filter_var($filterValue, FILTER_VALIDATE_INT, $this->filterFlags)))
        )) {
            return null;
        }

        // appliy function validator
        if ($this->functionValidator !== null) {
            $auxFilterValue = $filterValue;
            if ($this->functionValidatorExplondeParamSeparator !== null) {
                $auxFilterValue = explode($this->functionValidatorExplondeParamSeparator, $filterValue);
            }
            if (!call_user_func_array($this->functionValidator, [
                $auxFilterValue
            ])) {
                return null;
            }
        }

        // Validate by regex
        if ($this->regexValidate !== null && !preg_match($this->regexValidate, $filterValue)) {
            return null;
        }

        // Validate by list of values
        if ($this->availableValues !== null) {
            $listValues = explode(',', $filterValue);
            $filterValue = '';
            foreach ($listValues as $listValue) {
                $auxValue = '';
                if (in_array($listValue, $this->availableValues)) {
                    $auxValue = $listValue;
                } elseif (!$this->enableModification) {
                    return null;
                }
                if (strlen($filterValue) && strlen($auxValue)) {
                    $filterValue .= ',';
                }
                $filterValue .= $auxValue;
            }
        }

        // Apply date time format and build DateTime object
        if (!is_null($this->dateTimeFormat) && strLen($this->dateTimeFormat)) {
            $dateTime = \DateTime::createFromFormat($this->dateTimeFormat, $filterValue);
            if ($dateTime === false) {
                $filterValue = null;
            } else {
                $filterValue = $dateTime;
            }
        }

        return $filterValue;
    }
}
