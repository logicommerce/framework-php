/**
 * Returns locations data from country and parentId
 * @param {string} countryCode
 * @param {(number|undefined)} parentId
 * @param {function} [callback]
 * @param {function} [errorCallback]
 */
function getLocations(countryCode, parentId, callback, errorCallback) {
    var params = new urlParameterEncoder();
    params.addParameter('countryCode', countryCode);

    if (parentId) params.addParameter('parentId', parentId);

    var url = LC.global.routePaths.GEOLOCATION_INTERNAL_GET_LOCATIONS + '/' + params.getParameters();
    $.get(url, callback ? callback : $.noop, 'json').fail(errorCallback ? errorCallback : $.noop);
}

/**
 * Returns localities data from country and query
 * @param {string} countryCode
 * @param {string} q
 * @param {function} [callback]
 * @param {function} [errorCallback]
 */
function getLocationsLocalities(countryCode, q, callback, errorCallback) {
    var params = new urlParameterEncoder();
    params.addParameter('countryCode', countryCode);
    params.addParameter('q', q);

    var url = LC.global.routePaths.GEOLOCATION_INTERNAL_GET_LOCATIONS_LOCALITIES + '/' + params.getParameters();
    $.get(url, callback ? callback : $.noop, 'json').fail(errorCallback ? errorCallback : $.noop);
}

/**
 * Returns locations path from country and location id
 * @param {string} countryCode
 * @param {number} locationId
 * @param {function} [callback]
 * @param {function} [errorCallback]
 */
function getLocationsPath(countryCode, locationId, callback, errorCallback) {
    var params = new urlParameterEncoder();
    params.addParameter('countryCode', countryCode);
    params.addParameter('locationId', locationId);

    var url = LC.global.routePaths.GEOLOCATION_INTERNAL_GET_LOCATIONS_PATH + '/' + params.getParameters();
    $.get(url, callback ? callback : $.noop, 'json').fail(errorCallback ? errorCallback : $.noop);
}

/**
 * Load locations and append html form elements
 * @param {string} [countryCode]
 * @param {number} [locationId]
 */
function loadLocations(countryCode, locationId) {
    var element = this,
        $scope = $(this).closest('.addressUserField'),
        $countrySelect = $scope.find('.countryField'),
        fieldName = $(this).data('field-name') ? $(this).data('field-name') : $countrySelect.data('field-name'),
        data = $(this).find('option:selected').data('lc'),
        $container = $(this).is('.countryField') ? $scope.find('.locationSelector') : $(this).next('.locationSelector'),
        $manualTab = $scope.find('.countryManualTab'),
        $suggestTab = $scope.find('.countrySuggestTab'),
        _countryCode = countryCode ? countryCode : data.code,
        _locationId = data.locationId ? data.locationId : (locationId ? locationId : undefined),
        _postalCodeType = data.postalCodeType ? data.postalCodeType : $countrySelect.find('option:selected').data('lc').postalCodeType;

    if (_locationId) {
        $suggestTab.removeClass('active'); // tab
        $suggestTab.parent('li').removeClass('active'); // tab
        $($suggestTab.attr('href')).removeClass('active show'); // pane
        $manualTab.addClass('active'); // tab
        $manualTab.parent('li').addClass('active'); // tab
        $($manualTab.attr('href')).addClass('active show'); // pane
    } else {
        $scope.find('.locationSearch').data('lc-country', _countryCode);
        $suggestTab.removeClass('hidden');
        $suggestTab.attr('hidden', false);
        $suggestTab.addClass('active');
        $suggestTab.parent('li').addClass('active'); // tab
        $($suggestTab.attr('href')).addClass('active show'); // pane
        $manualTab.removeClass('hidden');
        $manualTab.attr('hidden', false);
        $manualTab.removeClass('active');
        $manualTab.parent('li').removeClass('active'); // tab
        $($manualTab.attr('href')).removeClass('active show'); // pane
    }
    $container.html('<div class="locationLoading">' + LC.global.languageSheet.locationLoading + '</div>');

    getLocations(_countryCode, _locationId, (function (result) {
        $container.html('');

        $(element).closest('.addressUserField').find('.locationAdditionalFields').remove();

        if (result.data.data.items.length) {
            var $select = getLocationsSelect(result.data.data.items, _countryCode, fieldName),
                level = result.data.data.items[0].level;
            if (result.data.data.items.length > 0) {
                var locationlabels = [LC.global.languageSheet.state, LC.global.languageSheet.city, LC.global.languageSheet.postalCode];
                $container.append('<label for="' + fieldName + 'location_level_' + level + '">' + locationlabels[level - 1] + '<span class="required">*</span></label>');
            }
            $container.append($select);
            $container.append('<div class="locationSelector"></div>');
            if (result.data.data.items.length == 1 && result.data.data.items[0].level > 1) {
                $select.val(result.data.data.items[0].locationId);
                $select.change();
            }
        } else if (_locationId) {
            selectLocationResult(element, _countryCode, _locationId, fieldName, true);
        }
    }).bind(this), function (result) {
        // ajax error
    });
}

/**
 * Returns jquery select object
 * @param {array} locations - Non empty array
 * @param {string} countryId
 * @param {string} fieldName
 * @returns {object}
 */
function getLocationsSelect(locations, countryId, fieldName) {

    var level = locations[0].level ? locations[0].level : 0;

    var $select = $('<select/>', {
        class: FORM_SELECT_CLASS + ' locationField formField',
        id: fieldName + 'location_level_' + level,
        name: fieldName + '_locationList',
        autocomplete: 'off',
        onChange: 'loadLocations.bind(this)("' + countryId + '")'
    });

    $select.append($('<option/>', {
        disabled: '',
        selected: '',
        text: LC.global.languageSheet.locationSelectAnOption
    }));

    $.each(locations, function (index, location) {
        $select.append($('<option/>', {
            text: location.value,
            value: location.locationId,
            'data-lc': JSON.stringify(location)
        }));
    });

    return $select;
}

/**
 * Returns jquery select object
 * @param {object} element - input element
 * @param {string} value - input value
 * @param {bool} execute - execute ajax call
 */
function searchLocations(element, value, execute) {
    var $container = $(element).closest('.locationSearchContainer').find('.locationsResultsContainer'),
        container = $container[0],
        countryId = $(element).data('lc-country'),
        fieldName = $(element).data('field-name');

    if (execute) {
        getLocationsLocalities(countryId, value, function (result) {
            $container.html('');
            var $html = getLocationsSearchResult(result.data.data.items, countryId, fieldName);
            $container.append($html);

            $('.selectableCity').off('keydown.selectableCity').on('keydown.selectableCity', function (event) {
                if ($(document.activeElement).is('.selectableCity') && (event.keyCode === 13 || event.keyCode === 32)) {
                    event.preventDefault();
                    $(document.activeElement).click();
                }
            });
        }, function (result) {
            // ajax error
        });

    } else if (value.length > 2) {
        if (container.timeout) clearTimeout(container.timeout);

        if (container.connect && container.connect.request.readyState != 0 && container.connect.request.readyState != 4)
            searchLocations(element, value, true);
        else
            container.timeout = setTimeout(function () {
                searchLocations(element, value, true);
            }, 400);
    } else {
        container.innerHTML = '';
    }
}

/**
 * Returns jquery div list object
 * @param {array} location
 * @param {string} countryId
 * @param {string} fieldName
 * @returns {object}
 */
function getLocationsSearchResult(locations, countryId, fieldName) {
    var html = '';

    if (locations.length) {
        $.each(locations, function (index, location) {
            html +=
                `<div class="selectableCity" tabindex="0" role="link" title="${location.name}" onclick="selectLocationResult(this, '${countryId}', ${location.locationId}, '${fieldName}', false);">
                    <div class="selectableCityZip">${location.postalCode}</div>
                    <div class="selectableCityName">${location.name}</div>
                </div>`;
        });
    } else {
        html += `<div class="notFound">${LC.global.languageSheet.locationNotFound}</div>`;
    }

    return $container = $('<div/>', {
        class: 'citiesSelectorContent',
        html: html
    });
}

/**
 * Get locations path and fill selected selects
 * @param {object} element
 * @param {string} countryCode
 * @param {number} locationId
 * @param {string} fieldName
 * @param {bool} fromGetLocations
 */
function selectLocationResult(element, countryCode, locationId, fieldName, fromGetLocations, city = '', postalCode = '') {
    var params = new urlParameterEncoder();
    params.addParameter('countryCode', countryCode);
    params.addParameter('locationId', locationId);
    params.addParameter('fieldName', fieldName);
    params.addParameter('city', city);
    params.addParameter('postalCode', postalCode);

    var $parent = $(element).closest('.addressUserField'),
        $selectorParent = $parent.find('.locationSelectorParent');

    $.ajax({
        type: "GET",
        url: LC.global.routePaths.USER_INTERNAL_LOCATIONS_PATH + '/' + params.getParameters(),
        async: false
    }).done(function (result) {
        $selectorParent.find('.locationAdionditionalFields').remove();
        $selectorParent.find('.resultLocationsPath').remove();
        var $result = $(result);
        if ($result.find('.locationAdditionalField.required').length && fromGetLocations) {
            $locationAdditionalFields = $result.find('.locationAdditionalFields');
            $locationAdditionalFields.appendTo($selectorParent);
            firstFocus = true;
            ($locationAdditionalFields.find("input")).each(function (index, locationAdditionalField) {
                if (firstFocus) {
                    $(locationAdditionalField).focus();
                }
                firstFocus = false;
            });
            $result.hide();
            $result.appendTo($parent);
        } else {
            if ($selectorParent.length) {
                $selectorParent.html('<div class="locationLoading">' + LC.global.languageSheet.locationLoading + '</div>');
                $parent.find('.locationsResultsContainer').html('');
                $parent.find('.addressBlockDetails').html('<div class="locationLoading">' + LC.global.languageSheet.locationLoading + '</div>');
                $parent.find('.countryManualTab').tab('show');
                $parent.find('.addressData').show();
                $parent.find('.countrySelectGroup').hide();
                $parent.find('.resetCountrySelector').attr('disabled', true);
                $form = $parent.closest('form');
                $form.find('button[type=submit]').attr('disabled', true);
                $selectorParent.html(result);
                setLocationComplete($parent);
                if ($form.find('button[type=submit]').length > 0) {
                    $form.find('button[type=submit]').attr('disabled', false);
                    $parent.find('.resetCountrySelector').attr('disabled', false);
                }
            } else {
                var locations = $.parseJSON($(result).find('.locationCompletePath').attr('lc-data')),
                    search = $(element).closest('.locationSearchContainer').find('#locationSearch').data('lc-search'),
                    lat = 0,
                    lng = 0,
                    level = 0;

                $(element).closest('.locationsResultsContainer').html('');

                for (let index = 0; index < locations.length; index++) {
                    if (locations[index].level > level) {
                        lat = locations[index].coordinate.latitude;
                        lng = locations[index].coordinate.longitude;
                    }
                }
                LC.maps.centerLatLngOnMap(lat, lng, search.searchResultZoom, search.searchMinResults, search.searchMaxResults);
            }
        }
        $.validate(LC.validateFormConf);
    }).fail(function (result) {
        resetCountrySelector($parent);
    });

    localizeCallbacks(element, fieldName);
}

/**
 * Set location complete and update selected data
 * @param {object} $parent - .addressUserField jquery node
 */
function setLocationComplete($parent) {
    $parent.removeClass('address-complete');
    data = $parent.find('option:selected').data('lc');

    var completed = true,
        $fields = getLocationFields($parent);

    required = 0;
    notRequired = 0;

    $locationAdditionalFieldsRequired = $parent.find('.locationAdditionalField.required');
    var reqlength = $locationAdditionalFieldsRequired.length;

    if (reqlength > 0 || $fields.length < 2) {
        var value = $locationAdditionalFieldsRequired.filter(function () {
            return this.value != '';
        });
        completed = reqlength > 0 && value.length == reqlength;
    } else {
        $fields.each(function (index, el) {
            if (!$(el).val()) {
                completed = false;
                if ($(el).prop('required')) {
                    required += 1;
                } else {
                    notRequired += 1;
                }
            }
        });
    }

    if (completed || (required == 0 && notRequired > 0)) {
        checkAllowDifferentCountriesOnBillingAndShippingAddress($parent);
        $parent.addClass('address-complete');
        $parent.removeClass('address-incomplete');
    }

    updateLocationBlockData($parent, completed);
}

/**
 * Checks and block the shipping country if the configuration not allowDifferentCountriesOnBillingAndShippingAddress
 * @param {object} $parent - .addressUserField jquery node
 */
function checkAllowDifferentCountriesOnBillingAndShippingAddress($parent) {
    const $formContent = $parent.closest('[data-lc-form="userForm"]'),
        userType = $formContent.attr('data-lc-user-type'),
        selectdCountry = $formContent.find('select[name="' + userType + '_user_country"]').val();

    if (!themeConfiguration?.commerce?.allowDifferentCountriesOnBillingAndShippingAddress && selectdCountry?.length) {
        const $shippingSelect = $formContent.find('select[name="' + userType + '_shipping_country"]'),
            originalShippingCountry = $shippingSelect?.val();

        if (!$shippingSelect.prop('lc-locked-country')) {
            $shippingSelect.hide();
            $shippingSelect.closest('.addressUserField').find('span.userAddressBookField').after('<span class="lockedCountryName"></span>');
            $shippingSelect.prop('lc-locked-country', true);
        }

        $shippingSelect.each((index, el) => {
            $(el).find('option').each((index, option) => {
                if ($(option).val() != selectdCountry) {
                    $(option).attr('disabled', true);
                    $(option).hide();
                    $(option).removeAttr('selected');
                } else {
                    $(option).removeAttr('disabled');
                    $(option).show();
                    $(option).attr('selected', 'selected');
                    $shippingSelect.closest('.addressUserField').find('span.lockedCountryName').html($(option).html());
                }
            });
        });

        if (originalShippingCountry != $shippingSelect?.val()) {
            resetCountrySelector($shippingSelect.closest('.addressUserField'));
        }
    }
}


/**
 * Update html data of .addressData
 * @param {object} $parent - .addressUserField jquery node
 * @param bool completed - location completed
 */
function updateLocationBlockData($parent, completed) {
    var $container = $parent.find('.addressBlockDetails'),
        $locationAdditionalFields = $parent.find('.locationAdditionalFields'),
        $locationAdditionalContainer = $parent.find('.addressBlockAdditionalFields'),
        $addressFields = getLocationFields($parent),
        $addressBlock = $parent.find('.locationCompletePath');

    $container.html('');

    if ($addressBlock.length > 0) {
        $parent.find('.addressData').show();
        if (completed) {
            $($locationAdditionalFields.find('.formFieldGroup')).each(function (index, locationAdditionalField) {
                $locationAdditionalField = $(locationAdditionalField);
                $inputField = $locationAdditionalField.find("input");
                $addressBlock.find('.locationText' + $inputField.attr("name")).show();
                $addressBlock.html($addressBlock.html().replace('{{' + $inputField.attr("name") + '}}', $inputField.val()));
                $locationAdditionalField.hide();
            });
            $parent.find('.countryManualTab').tab('show');
            $parent.find('.countrySelectGroup').hide();
        } else {
            $locationAdditionalContainer.html('');
            $locationAdditionalContainer.show();
            firstFocus = true;
            $($locationAdditionalFields.find('.formFieldGroup')).each(function (index, locationAdditionalField) {
                $locationAdditionalField = $(locationAdditionalField);
                $addressBlock.find('.locationText' + $locationAdditionalField.find("input").attr("name")).hide();
                if (firstFocus) {
                    $locationAdditionalField.find("input").focus();
                }
                firstFocus = false;
            });
            $locationAdditionalFields.appendTo($locationAdditionalContainer);
        }
        $addressBlock.appendTo($container);
    } else {
        $addressFields.each(function (index, el) {
            $addressBlock = $('<div/>');
            if (index === 0) {
                $addressBlock.addClass('firstAddressBlock');
            } else if (index === ($addressFields.length - 1)) {
                $addressBlock.addClass('lastAddressBlock');
            }
            $addressBlock.addClass('addressBlock').addClass('addressBlock' + (index + 1));

            var val = $(el).text();
            if (!val) val = $(el).val();

            $addressBlock.html(val);
            $addressBlock.appendTo($container);
        });
    }
}

/**
 * Return all valid locations fields
 * @param {object} $parent - .addressUserField jquery node
 * @return {object}
 */
function getLocationFields($parent) {
    return $parent.find('select option:selected, input.locationField').not('.locationSearch');
}

/**
 * Reset country and location completed block data
 * @param {object} $parent - .addressUserField jquery node
 */
function resetCountrySelector($parent) {
    if ($parent.find('.addressUserCountryField').length) {
        const countryCode = $parent.find('.addressUserCountryField').val();
        $parent.find('.addressUserCountryField').val(countryCode ? countryCode : LC.global.session.countryId).change();
    } else {
        $parent.find('.addressData').hide();
        $parent.find('.addressBlockAdditionalFields').html('');
        $parent.find('.locationCompletePath').remove();
        $parent.find('.resultLocationsPath').remove();
        $parent.find('.countrySelectGroup').show();
        const countryCode = $parent.find('.countryField').val();
        $parent.find('.countryField').val(countryCode ? countryCode : LC.global.session.countryId).change();
        setLocationComplete($parent);
    }
}

/**
 * Clean location search error class on focus event
 * @param {object} input
 */
function onFocusNoSelectLocationSearch(input) {
    $(input).closest('.locationSearchGroup').removeClass('has-error');
}

/**
 * Add location search error on blur if no select location and addres isn't complete
 * @param {object} input
 */
function onBlurNoSelectLocationSearch(input) {
    $(input).val('');
    if (!$(input).closest('.addressUserField').hasClass('address-complete')) {
        $(input).closest('.locationSearchGroup').addClass('has-error');
    }
}

/**
 * check the value in required LocationAdditionalFields and on complete repaint
 * @param {string} postalCodeType
 */
function checkLocationAdditionalFields() {
    var $parent = $(this).closest('.addressUserField');
    var $locationAdditionalFields = $parent.find('.locationAdditionalField');
    var reqlength = $locationAdditionalFields.length;
    var value = $locationAdditionalFields.filter(function () {
        return this.value != '';
    });
    if (value.length == reqlength) {
        setLocationComplete($parent);
    }
}

/**
 * Return if country has search locations input
 * @param {string} postalCodeType
 */
function locationShowSearch(postalCodeType) {
    return postalCodeType === 'STATE_CITY_POSTAL_CODE';
}

/**
 * Close event of locations search result dropdown
 */
$(document).click(function (event) {
    if (!$(event.target).closest('.locationSearchContainer').length) {
        var $locResCont = $('.locationsResultsContainer');
        if ($locResCont.is(":visible")) $locResCont.html('')
    }
});

var localizeEvents = {
    selectPostalCode: function () { },
    selectShippingPostalCode: function () { },
    selectAddressBookPostalCode: function () { }
};

function localizeCallbacks(element, fieldName) {
    if ($(element).parents('.addressBookForm').length) {
        localizeEvents.selectAddressBookPostalCode();
    } else if (fieldName.indexOf('_user') !== -1) {
        localizeEvents.selectPostalCode();
    } else if (fieldName.indexOf('_shipping') !== -1) {
        localizeEvents.selectShippingPostalCode();
    }
}


function changeCountryFields(countryCode = '', fromInit = false) {
    $this = $(this);
    if (typeof $this.find(':selected').attr('data-lc') === 'undefined') {
        return;
    }
    var data = $.parseJSON($this.find(':selected').attr('data-lc'));

    $parent = $this.closest('.formFields');
    if ($parent.length == 0) {
        $parent = $this.closest('form');
    }

    $postalCodeRequired = $parent.find('.userFieldGroupPostalCodeRequired');
    $postalCode = $parent.find('.userFieldGroupPostalCode');

    if (!fromInit) {
        $parent.find('.userFieldGroupState').find('input').val('');
        $parent.find('.userFieldGroupCity').find('input').val('');
        $postalCodeRequired.find('input').val('');
        $postalCode.find('input').val('');
    }

    if (data.postalCodeType == 'STATE_CITY_WITHOUT_POSTAL_CODE') {
        $postalCodeRequired.hide();
        $postalCodeRequired.find('input').attr('disabled', true);
        $postalCode.hide();
        // disable = false, to save '' as postal code
        $postalCode.find('input').attr('disabled', false);
    } else if (data.postalCodeType == 'POSTAL_CODE_OPTIONAL') {
        $postalCodeRequired.hide();
        $postalCodeRequired.find('input').attr('disabled', true);
        $postalCode.show();
        $postalCode.find('input').attr('disabled', false);
    } else {
        $postalCodeRequired.show();
        $postalCodeRequired.find('input').attr('disabled', false);
        $postalCode.hide();
        $postalCode.find('input').attr('disabled', true);
    }
}


