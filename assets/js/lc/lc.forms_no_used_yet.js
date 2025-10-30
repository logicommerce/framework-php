/**
 * ATTENTION
 * 
 * If you take a block of JS from here, remove it from the list or if you deprecate it,
 * make a note of it.
 * 
 * File classes:
 * 
 * LC.BuyGiftForm
 * LC.DeleteWishlistForm (@deprecated)
 * LC.SponsorshipForm
 * LC.ConfirmAccountForm
 * LC.AffiliateOrdersForm
 * LC.NewsletterPopupForm
 * LC.CustomersForm
 * LC.shippingPrices
 * LC.productComments (@deprecated)
 * LC.OrderReturnForm
 * LC.RemoveStockAlertForms (@deprecated)
 * LC.blogCommentForm
 * LC.FilterForm (@deprecated)
 * LC.ComparisonCustomTagsForm
 * LC.CustomForm
 * LC.PollForm
 * LC.CountrySelectorForm
 * LC.saveBasketForm
 * LC.verifyAccountForm
 * LC.addRecommendedBasket
 * LC.incidenceForm
 * LC.ConfirmAgePopupForm
 */



/**
 * TODO
 * @class LC.BuyGiftForm
 * @memberOf LC
 * @extends {LC.Form}
 * @description Form extended from LC.Form
 */
LC.BuyGiftForm = LC.Form.extend({
    name: 'buyGiftForm',
    options: {},

    /**
     * Initialize
     * @memberOf LC.BuyGiftForm
     */
    initialize: function (form) {
        this.data = JSON.parse(this.el.$form.attr('data-product'));

        this.quantityField = this.el.$form.find('input[data-lc-field="quantity"]').get(0);

        // Before trigger
        this.trigger('initializeBefore');

        this.productAvailabilities = [];
        if (this.data.availabilityId && productAvailabilities['availability' + this.data.availabilityId])
            this.productAvailabilities = productAvailabilities['availability' + this.data.availabilityId];

        this.callback = this.callback.bind(this);
        this.initOptions();

        // Callback trigger
        this.trigger('initializeCallback');
    },

    /**
     * Initialize options
     * @memberOf LC.BuyGiftForm
     */
    initOptions: function () {
        // Before trigger
        this.trigger('initOptionsBefore');

        this.el.$buyGiftFormSubmit = this.el.$form.find('button[type="submit"]');
        this.el.$options = this.el.$form.find(
            'input.productOptionRadioValue, select.productOptionSelectValue, input.productOptionCheckboxValue, input.productOptionBooleanValue, input.productOptionTextValue, textarea.productOptionLongTextValue'
        );
        this.el.$options.change(this.changeOption.bind(this));

        if (this.quantityField) $(this.quantityField).change(this.changeQuantity.bind(this));

        /*getting required options*/
        this.requiredOptions = [];
        for (var option in this.data.options) {
            if (
                ['SINGLE_SELECTION', 'SINGLE_SELECTION_IMAGE', 'SELECTOR'].indexOf(
                    this.data.options[option].valueType
                ) > -1
            ) {
                this.requiredOptions.push(this.data.options[option].id);
            }
        }

        this.onChange();

        this.el.$form
            .find('input.productOptionRadioValue:checked')
            .parent('div.productOptionRadioValue')
            .addClass('productOptionSelected');

        // Callback trigger
        this.trigger('initOptionsCallback');
    },

    /**
     * Change quantity
     * @memberOf LC.BuyGiftForm
     * @param  {object} eventData
     */
    changeQuantity: function (eventData) {
        var quantityValue = $(this.quantityField).val();

        // Before trigger
        this.trigger('changeQuantityBefore');

        this.onChange();

        // Callback trigger
        this.trigger('changeQuantityCallback', { quantity: quantityValue });
    },

    /**
     * Change option
     * @memberOf LC.BuyGiftForm
     * @param  {object} eventData
     */
    changeOption: function (eventData) {
        // Before trigger
        this.trigger('changeOptionBefore');

        if ($(eventData.target).hasClass('productOptionRadioValue')) {
            $(eventData.target)
                .parents('div.productOptionValues')
                .find('div.productOptionRadioValue')
                .removeClass('productOptionSelected');
            $(eventData.target)
                .parent('div.productOptionRadioValue')
                .addClass('productOptionSelected');
        }

        // Callback trigger
        this.trigger('changeOptionCallback', eventData.target);

        this.onChange();
    },

    /**
     * On change
     * @memberOf LC.BuyGiftForm
     */
    onChange: function () {
        var selectedOptions = [];
        var selectedValues = [];
        var selectedStockValues = [];
        var optionId, optionValueId;

        var formValues = this.getFormValues();
        var quantityValue = $(this.quantityField).val();

        // Before trigger
        this.trigger('onChangeBefore', formValues);

        selectedOptions = [];
        for (var i = 0; i < formValues.length; i++) {
            optionId = formValues[i].name.replace('optionValue', '');
            optionValueId = formValues[i].value;
            if (Object.getLength(this.data.options['id' + optionId].values)) {
                if (this.data.options['id' + optionId].values['id' + optionValueId]) {
                    selectedOptions.push(parseInt(optionId));
                    selectedValues.push(this.data.options['id' + optionId].values['id' + optionValueId]);
                    if (this.data.options['id' + optionId].combinable)
                        selectedStockValues.push(parseInt(optionValueId));
                }
            } else {
                selectedOptions.push(parseInt(optionId));
            }
        }
        selectedStockValues = selectedStockValues
            .sort(function (a, b) {
                return a - b;
            })
            .join('-');

        var requiredOptions = [];
        for (var i = 0; i < this.requiredOptions.length; i++)
            if (selectedOptions.indexOf(this.requiredOptions[i]) == -1) requiredOptions.push(this.requiredOptions[i]);

        var stock = 999999999,
            combinationFound = true;
        if (settings.stockManagement && this.data.definition.stockManagement) {
            stock = 0;
            combinationFound = false;
            for (var key in this.data.stocks) {
                if (key.match('WH[0-9]+_' + selectedStockValues + '$')) {
                    stock += this.data.stocks[key];
                    combinationFound = true;
                }
            }
            if (!combinationFound) stock = -1;
        }

        if (stock >= 0) {
            this.el.$form.find('.product-stock .stock').html(stock);
            if (stock > 0)
                this.el.$form
                    .find('.product-stock')
                    .removeClass('no-stock')
                    .addClass('stock-ok')
                    .show();
            else
                this.el.$form
                    .find('.product-stock')
                    .removeClass('stock-ok')
                    .addClass('no-stock')
                    .show();
        } else {
            this.el.$form.find('.product-stock').hide();
        }

        /*getting availability interval*/
        var availabilityInterval;
        for (var i = this.productAvailabilities.length - 1; i > -1; i--) {
            if (this.productAvailabilities[i].stock < stock) break;
            availabilityInterval = this.productAvailabilities[i];
        }

        // Buy Button properties
        var buyButtonProps = {};
        if (requiredOptions.length) {
            buyButtonProps.className = 'selectOption';
            buyButtonProps.name = languageSheet.selectOption.replace(
                '{{option}}',
                this.data.options['id' + requiredOptions[0]].name
            );
            buyButtonProps.disabled = true;
        } else if (stock < 0) {
            buyButtonProps.className = 'notAvailable';
            buyButtonProps.name = languageSheet.notAvailable;
            buyButtonProps.disabled = true;
        } else if (stock == 0 || quantityValue > stock) {
            buyButtonProps.className = 'notAvailable';
            buyButtonProps.name = languageSheet.notAvailable;
            buyButtonProps.disabled = true;
        } else {
            buyButtonProps.className = 'add';
            buyButtonProps.name = languageSheet.addGiftToCart;
            buyButtonProps.disabled = false;
        }

        this.el.$buyGiftFormSubmit.removeClass('selectOption notAvailable reserve buy');
        this.el.$buyGiftFormSubmit.addClass(buyButtonProps.className);
        this.el.$buyGiftFormSubmit.prop('disabled', buyButtonProps.disabled);
        this.el.$buyGiftFormSubmit.data('buyFormSubmitName', buyButtonProps.name);

        if (this.el.$buyGiftFormSubmit.data('show-label') == true) this.el.$buyGiftFormSubmit.html(buyButtonProps.name);
        else this.el.$buyGiftFormSubmit.html('');

        // Callback trigger
        this.trigger('onChangeCallback');
    },

    /**
     * Get form data
     * @memberOf LC.BuyGiftForm
     */
    getFormData: function () {
        var discountName = this.el.$form.data('lcDiscountName');
        if (typeof discountName === 'undefined') discountName = '';

        var formValues = this.getFormValues();
        var options = {},
            optionsArray = [];

        for (var i = 0; i < formValues.length; i++) {
            if (!options[formValues[i].name]) options[formValues[i].name] = [];
            options[formValues[i].name].push(formValues[i].value);
        }

        for (var option in options)
            optionsArray.push({ id: option.replace('optionValue', ''), values: options[option] });

        return {
            id: this.data.id,
            sku: this.data.sku,
            quantity: this.quantityField ? this.quantityField.value : this.data.definition.minOrderQuantity || 1,
            discount: discountName,
            options: optionsArray,
        };
    },

    /**
     * Get form values
     * @memberOf LC.BuyGiftForm
     */
    getFormValues: function () {
        var formValues;

        if (this.el.$options.length) {
            formValues = this.el.$options.serializeArray();
        } else {
            formValues = [];
            var selectedOptions = [];

            if (settings.stockManagement && this.data.definition.stockManagement) {
                for (var key in this.data.stocks) {
                    if (this.data.stocks[key] > 0) {
                        var optionValues = key.split('_');
                        if (optionValues.length == 1) break;
                        optionValues = optionValues[1].split('-');

                        for (var i = 0; i < optionValues.length; i++) {
                            for (var option in this.data.options) {
                                if (this.data.options[option].values['id' + optionValues[i]]) {
                                    formValues.push({
                                        name: 'optionValue' + this.data.options[option].id,
                                        value: optionValues[i],
                                    });
                                    selectedOptions.push(this.data.options[option].id);
                                }
                            }
                        }

                        break;
                    }
                }
            }

            var requiredOptions = [];
            for (var i = 0; i < this.requiredOptions.length; i++)
                if (selectedOptions.indexOf(this.requiredOptions[i]) == -1)
                    requiredOptions.push(this.requiredOptions[i]);

            for (var i = 0; i < requiredOptions.length; i++) {
                for (var value in this.data.options['id' + requiredOptions[i]].values) {
                    formValues.push({
                        name: 'optionValue' + requiredOptions[i],
                        value: this.data.options['id' + requiredOptions[i]].values[value].id,
                    });
                    break;
                }
            }
        }

        return formValues;
    },

    /**
     * Submit event
     * @memberOf LC.BuyGiftForm
     * @param {object} event
     */
    submit: function (event) {
        event.preventDefault();

        // Before trigger
        this.trigger('submitBefore', event);

        if (!this.el.$form.isValid()) return false;

        $.post('/basket/addGift/', { data: JSON.stringify([this.getFormData()]) }, this.callback, 'json');
        // Disable buy button
        this.el.$buyGiftFormSubmit.prop('disabled', true);

        // Callback trigger
        this.trigger('submitCallback', event);
    },

    /**
     * Callback
     * @memberOf LC.BuyGiftForm
     */
    callback: function (data) {
        // Before trigger
        this.trigger('callbackBefore');

        // Enable buy button
        this.el.$buyGiftFormSubmit.prop('disabled', false);

        if (data.status.code == 200 && data.response.ADDED == 0) location.reload();

        // Callback trigger
        this.trigger('callback');
    },
});

/**
 * DEPRECATED
 * @class LC.DeleteWishlistForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.DeleteWishlistForm = LC.Form.extend({
    name: 'deleteWishlistForm',
    options: {},
    initialize: function (form) {
        // Before trigger
        this.trigger('initOptionsBefore');

        this.initElements();

        // Callback trigger
        this.trigger('initializeCallback');
    },
    initElements: function () {
        // Before trigger
        this.trigger('initOptionsBefore');

        this.el.$submit = this.el.$form.find('button[type="submit"]');
        this.el.$options = this.el.$form.find('input[type="checkbox"]');

        // Callback trigger
        this.trigger('initOptionsCallback');
    },
    getFormValues: function () {
        // Before trigger
        this.trigger('getFormValuesBefore');

        formValues = [];
        if (this.el.$options.length) {
            this.el.$options.each(function (i, obj) {
                if (obj.checked) formValues.push(obj.value);
            });
        }

        // Callback trigger
        this.trigger('getFormValuesCallback');

        return formValues;
    },
    getFormData: function () {
        return {
            productIdList: this.getFormValues(),
        };
    },
    submit: function (event) {
        // Before trigger
        this.trigger('submitBefore');

        event.preventDefault();

        var formData = this.getFormData();

        // 20160506BVFL-01
        if (formData.productIdList.length > 0) {
            $.post('/user/deleteWishlist', { data: JSON.stringify(formData) }, this.callback.bind(this), 'json');
            this.el.$submit.prop('disabled', true);
        }

        // Before trigger
        this.trigger('submitCallback');
    },
    callback: function (data) {
        this.el.$submit.prop('disabled', false);

        if (!data.data) return;

        // Reload
        window.location = '/user/wishlist';
    },
});

/**
 * TODO
 * @class LC.SponsorshipForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.SponsorshipForm = LC.Form.extend({
    name: 'sponsorshipForm',
    emails: {},
    initialize: function (form) {
        // Before trigger
        this.trigger('initOptionsBefore');

        this.initElements();

        // Callback trigger
        this.trigger('initializeCallback');
    },
    initElements: function () {
        // Before trigger
        this.trigger('initOptionsBefore');

        this.el.$submit = this.el.$form.find('button[type="submit"]');
        this.el.$emails = this.el.$form.find('input[type="text"]');
        this.el.$message = this.el.$form.children('.form-message');

        // Callback trigger
        this.trigger('initOptionsCallback');
    },
    callback: function (data) {
        if (!data || data.status.code != 200) return;

        this.el.$submit.prop('disabled', false);
        this.showMessage(data.response.message, data.response.SENT == 1 ? 'success' : 'danger');
    },
});


/**
 * TODO
 * @class LC.ConfirmAccountForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.ConfirmAccountForm = LC.Form.extend({
    name: 'confirmAccountForm',
    callback: function (data) {
        this.showMessage(
            data.response.message,
            data.response.success && data.response.success == 1 ? 'success' : 'danger'
        );

        if (data.response.success && data.response.success == 1)
            setTimeout(function () {
                window.location = '/user';
            }, 5000);
    },
});

/**
 * TODO
 * @class LC.AffiliateOrdersForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.AffiliateOrdersForm = LC.Form.extend({
    //buyForm - $.post
    name: 'affiliateOrdersForm',
    initialize: function () {
        // Initialize calendar
        var $calendarStartDate = this.el.$form.find('div[data-datetimePicker="startDate"]');
        var $calendarEndDate = this.el.$form.find('div[data-datetimePicker="endDate"]');

        $calendarStartDate.datetimepicker({
            format: CALENDAR_PLUGIN_DATE_FORMAT, // FIXME: Use custom format!
        });

        $calendarEndDate.datetimepicker({
            // useCurrent: false, // Important! See issue #1075
            format: CALENDAR_PLUGIN_DATE_FORMAT, // FIXME: Use custom format!
        });
        $calendarStartDate.on('dp.change', function (e) {
            $calendarEndDate.data('DateTimePicker').minDate(e.date);
        });
        $calendarEndDate.on('dp.change', function (e) {
            $calendarStartDate.data('DateTimePicker').maxDate(e.date);
        });
    },
    submit: function (event) {
        event.preventDefault();

        // Initialize and set vars
        var data = {};
        var arrDataForm = this.el.$form.serializeArray();

        //Fills dataForm
        for (var i = 0; i < arrDataForm.length; i++) {
            if (arrDataForm[i].name == 'searchCriteria') {
                if (arrDataForm[i].value.length < 3) return false;
                action =
                    '/search/' +
                    escape(arrDataForm[i].value)
                        .split('%')
                        .join('__');
            } else {
                data[arrDataForm[i].name] = arrDataForm[i].value;
            }
        }

        var affiliateOrdersFormCallback = function (response) {
            $('#affiliateOrders').html(response);
        };

        var affiliateOrdersURL = '/user/affiliateOrders';
        $.post(affiliateOrdersURL, { data: JSON.stringify(data) }, affiliateOrdersFormCallback, 'html');

        return false;
    },
});




/**
 * LC Shipping Prices
 *
 * @description Prepare data to send for calulate shipping
 *              price and prints the data
 * @author Francesc Requesens
 * @version 1 - 2015-09-15
 *
 */
LC.shippingPrices = LC.Form.extend({
    name: 'shippingPrices',
    options: {},
    initialize: function (form) {
        // Initialize vars
        this.data = this.el.$form.data('lcShippingcalculator');
        this.el.$submit = this.el.$form.find('#btnShippingCalculator');
        this.el.$country = this.el.$form.find('select');
        this.el.$zip = this.el.$form.find('input:text');
        this.el.$resultContainer = this.el.$form.find('#basketShippingCalculatorResponse');

        //Add submit event
        this.el.$submit.on('click', this.submit.bind(this));

        this.el.$zip.on(
            'keypress',
            function (event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    this.submit();
                }
            }.bind(this)
        );
    },
    submit: function () {
        // Get data
        this.data.countryId = parseInt(this.el.$country.val());
        this.data.zip = this.el.$zip.val();

        // Clean container
        this.el.$resultContainer.html('');

        // Post
        $.post('/checkout/shippingPrices', { data: JSON.stringify(this.data) }, this.callback.bind(this), 'json');
    },
    callback: function (data) {
        // Callback trigger
        this.trigger('callback');

        if (data.response.success) this.el.$resultContainer.html(data.response.message);
        else this.showMessage(data.response.message, 'danger');
    },
});

/**
 * DEPRECATED
 * @class LC Product Comments
 * @description Makes AJAX calls so that comments are dinamically when the orderBy select input changes
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.productComments = LC.Form.extend({
    name: 'productComments',
    options: {},
    initialize: function (form) {
        // Initialize vars
        var container = $('#productCommentDivContainerResults');
        var input = $('#commentOrderBy')[0];
        var prodId = $('.showCommentsOrderBy input[name="id"]').val();
        var orderBy = null;

        // Add event listener to selector
        input.addEventListener('change', function (select) {
            var orderBy = this.value;

            // Get request to /products/:prodId/comments -> controllerSnippets.controllerSnippetsRenderComments()
            $.get(
                '/products/' + prodId + '/comments?orderby=' + orderBy + '',
                function (response) {
                    if (response.length > 0) {
                        // Replace current content with html from AJAX request
                        $(container)
                            .find('.productRateCommentsContaienr')
                            .replaceWith($(response));
                    }
                },
                'html'
            );
        });
    },
});


/**
 * DEPRECATED
 * LC Remove Stock Alert Form
 *
 * @description LC Remove Stock Alert Form
 * @version 1 - 2015-10-13
 *
 */
LC.RemoveStockAlertForm = LC.Form.extend({
    name: 'removeStockAlertForm',
    options: {},
    initialize: function (form) { },
    callback: function (data) { },
});



/**
 * TODO
 * @class LC.blogCommentForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.blogCommentForm = LC.Form.extend({
    name: 'blogCommentForm',
    initialize: function (form) {
        this.el.$checkbox = this.el.$form.find('input[type=checkbox]');
        this.el.$email = this.el.$form.find('.blogEmail');

        if (this.el.$checkbox) {
            this.el.$checkbox.click(this.toggleSubscription.bind(this));
        }

        $.validate(LC.validateFormConf);
    },
    toggleSubscription: function (ev) {
        if (ev.target.checked) {
            this.el.$email.show();
            this.el.$email.find('input').data('validation', 'required,email');
        } else {
            this.el.$email.hide();
            this.el.$email.find('input').data('validation', '');
        }
    },
    callback: function (data) {
        if (typeof data === 'undefined') return;

        if (!data.status) {
            var message = data.status.message ? data.status.message : 'Error';
            var success = 0;
        } else {
            var message = data.response.message;
            var success = data.response.SENT ? data.response.SENT : 0;
        }

        this.showMessage(message, success ? 'success' : 'danger');

        if (success) {
            this.el.$form.find('textarea[name=comment]').val('');
        }
    },
});


/**
 * TODO
 * @class LC.CountrySelectorForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.CountrySelectorForm = LC.Form.extend({
    name: 'countrySelectorForm',
    popupName: '#selectCountryPopup',
    cookiePopupName: COOKIE_COUNTRY_SELECTOR_POPUP,

    initialize: function (form) {
        if (this.el.form.initialized) return;

        this.levels = this.el.$form.data('levels');

        if (this.levels > 0) {
            this.onChange();
            this.el.$form.on('mouseenter', '.countriesSelector', this.onChange.bind(this));
        }

        //Save the cookie expiration time into THIS superVar.
        this.cookieExpireTime = this.el.$form.data('cookieexpiretime');

        var objCountryPopup = $('#selectCountryPopup');

        if (objCountryPopup.length == 1) {
            this.el.popup = objCountryPopup;
        } else {
            this.el.popupInclusted = $(E(this.includedPopupName));
            this.el.popupMark = $(E(this.includedPopupMarkName));
        }

        this.el.form.initialized = true;
        this.trigger('initializeBefore');
        this.callback = this.callback.bind(this);
    },

    onChange: function () {
        var selects = this.el.$form.find('select:not([name="languagesSelector"])');
        if (this.levels == 1) {
            selects.attr('onchange', '');
        } else if (selects.length >= this.levels - 1) {
            var last = $(selects[this.levels - 1]);

            if (last.length) {
                last.attr('onchange', '');
            }

            // dirty fix
            if ($(selects[this.levels]).length > 0) {
                $(selects[this.levels]).remove();
            }
        }
    },
    submit: function (event) {
        //Setting cookie if not exist only when the user click submit. if we put it into the init section or onLoad the cookie, with f5 we can bypass the "coockie restriction" because
        //it's defined without enter the information.
        if (typeof this.cookieExpireTime != 'undefined') {
            if (this.cookieExpireTime != 0) {
                var timeToExpireInHours = this.cookieExpireTime / 24;
                Cookies(this.cookiePopupName, 0, { path: '/', expires: timeToExpireInHours });
            } else {
                Cookies(this.cookiePopupName, 0, { path: '/' });
            }
        }
        // After set the cookie we call the super element function submit to submit the form.
        this.superForm('submit', event);
    },

    callback: function (response) {
        //This variable is for the LC Tag attribute internalRedirectUrl (/home,/categories/.....)
        var internalRedirectUrl = $('#internalRedirectUrl').val();
        // If isset in the LC Tag countrySelector we need to concatenate with the internalRedirectUrl. ATENTION! If a externalRedirectUrl is set, externalRedirectUrl WINS!
        var languageURL = $('select[name="languagesSelector"]').val();
        // If the externalRedirectUrl attribute in Lc tag is defined this redirect wins! Now is not IMPLEMENTED
        var externalRedirectUrl = $('#externalRedirectUrl').val();
        // We save the url to get the correct url in case the user comes to http://home.com/pants and not to http://home.com directly
        var urlComeFrom = window.location.href;

        // en languageURL si esta seleccionado el tag showLanguages=true en el tag devuelve: http://1135.igd.development/es  , por ejemplo.

        var resultOfTypeOfPageValidation = this.internalRedirectUrlValidator(internalRedirectUrl);

        if (typeof response !== 'undefined') {
            if (response.status.code == 200) {
                if (response.response.success == 1) {
                    //NOT USED YET. ALREADY PREPARED IF A CLIENT ASK FOR IT
                    if (externalRedirectUrl != null && externalRedirectUrl.length > 0) {
                        window.location.href = externalRedirectUrl;
                        return;
                    } else if (languageURL != null && languageURL.length > 0) {
                        if (
                            internalRedirectUrl != null &&
                            internalRedirectUrl.length > 0 &&
                            resultOfTypeOfPageValidation == true
                        )
                            window.location.href = languageURL + internalRedirectUrl;
                        else window.location.href = languageURL;
                        return;
                    } else if (
                        internalRedirectUrl != null &&
                        internalRedirectUrl.length > 0 &&
                        resultOfTypeOfPageValidation == true
                    ) {
                        window.location.href = internalRedirectUrl;
                        return;
                    } else {
                        window.location.href = urlComeFrom;
                    }
                } else {
                    window.location.href = urlComeFrom;
                }

                if (typeof this.el.popup !== 'undefined') {
                    this.el.popup.modal('hide');
                }
            } else {
                this.showMessage(response.response.message, 'danger');
            }
        }
    },

    externalRedirectUrlValidator: function (urlToValidate) {
        var myRegExp = /^(?:(?:https?|ftp):\/\/)?(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}) {3})(?!127(?:\.\d{1,3}) {3})(?!169\.254(?:\.\d{1,3}) {2})(?!192\.168(?:\.\d{1,3}) {2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}) {2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])) {2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;

        if (!myRegExp.test(urlToValidate)) {
            return false;
        } else {
            return true;
        }
    },

    internalRedirectUrlValidator: function (urlToValidate) {
        var myRegExp = /^[\/][\w]+/gim;

        if (!myRegExp.test(urlToValidate)) {
            return false;
        } else {
            return true;
        }
    },
});

/**
 * TODO
 * @class LC.saveBasketForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.saveBasketForm = LC.Form.extend({
    name: 'saveBasketForm',
    callback: function (response) {
        if (response.status.code == 200) {
            if (response.response.success == 1) {
                LC.notify(languageSheet.saved, { type: 'success', title: languageSheet.basket });
            } else {
                this.showMessage(languageSheet.errorTitle, 'danger');
            }
        }
    },
});

/**
 * TODO
 * @class LC.verifyAccountForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.verifyAccountForm = LC.Form.extend({
    name: 'verifyAccountForm',
    /**
     * initialize
     * @memberOf LC.verifyAccountForm
     */
    initialize: function (form) {
        var sendSMSLink = this.el.$form.find('a.sendSMSLink');

        sendSMSLink.on(
            'click',
            function (event) {
                var email = this.el.$form.find('input[name="email"]').val();
                var id = this.el.$form.find('input[name="id"]').val();

                if (!(email && email.length) && !id) {
                    var message = languageSheet.incorrectEmail;
                    if (id) message = languageSheet.userVerifyError;

                    LC.notify(message, { type: 'danger', title: languageSheet.verificationCode });
                } else
                    $.post(
                        '/user/action/sendVerificationMessage',
                        { data: JSON.stringify({ email: email, id: id }) },
                        'json'
                    ).always(this.sendSMSCallback);
            }.bind(this)
        );
    },

    /**
     * sendSMSCallback
     * @memberOf LC.verifyAccountForm
     */
    sendSMSCallback: function (response) {
        if (response.responseJSON)
            //403
            var data = response.responseJSON;
        //200
        else var data = response;

        var message = languageSheet.invalidEmail;
        var type = 'danger';
        if (data.response) {
            message = data.response.message;
            type = data.response.success >= 1 ? 'success' : 'danger';
        }

        if (data.status.code == 200 || data.status.code == 403) {
            // valid status codes
            LC.notify(message, { type: type, title: languageSheet.resendSms });
        }
    },

    /**
     * callback
     * @memberOf LC.verifyAccountForm
     */
    callback: function (response) {
        if (response.status.code == 200 || response.status.code == 403) {
            // valid status codes
            LC.notify(response.response.message, {
                type: response.response.success >= 1 ? 'success' : 'danger',
                title: languageSheet.verificationCode,
            });

            if (response.response.DIRECTVALIDATION && !response.response.success)
                setTimeout(function () {
                    window.location = '/user/verifyAccount';
                }, 3000);
            else if (response.response.success)
                setTimeout(function () {
                    window.location = '/user';
                }, 3000);
        }
    },
});

/**
 * TODO
 * @class LC.addRecommendedBasket
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.addRecommendedBasket = LC.Form.extend({
    name: 'addRecommendedBasket',
    /**
     * initialize
     * @memberOf LC.addRecommendedBasket
     */
    initialize: function (form) {
        this.setViewedButton = this.el.$form.find('button[type="button"].addRecommendedBasketSetViewed');
        this.addButton = this.el.$form.find('button[type="submit"].addRecommendedBasketFormSubmit');
        this.data = this.el.$form.data('recommendedBasket');

        this.setViewedButton.on(
            'click',
            function (event) {
                this.setViewedButton.prop('disabled', true);
                $.post(
                    '/user/recommendedBaskets/setViewed',
                    { data: JSON.stringify({ id: this.data.id, viewed: this.data.viewed }) },
                    'json'
                ).always(this.setViewedCallback.bind(this));
            }.bind(this)
        );
    },

    /**
     * setViewedCallback
     * @memberOf LC.addRecommendedBasket
     */
    setViewedCallback: function (response) {
        if (response.responseJSON)
            //403
            var data = response.responseJSON;
        //200
        else var data = response;

        // if success
        this.data.viewed = !this.data.viewed;
        var spanBaskets = $('span.userPanelAlert.userPanelRecommendedBasketsAlert');
        var spanBasketsValue = parseInt($(spanBaskets).html());
        if (isNaN(spanBasketsValue)) spanBasketsValue = 0;

        if (this.data.viewed) {
            this.setViewedButton.text(languageSheet.setRecommendedBasketUnviewed);
            if (spanBasketsValue == 1) $(spanBaskets).text('');
            else $(spanBaskets).text(spanBasketsValue - 1);
        } else {
            this.setViewedButton.text(languageSheet.setRecommendedBasketViewed);
            $(spanBaskets).text(spanBasketsValue + 1);
        }

        this.showMessage(data.response.message, data.response.CHANGED == 0 ? 'danger' : 'success');
        this.setViewedButton.prop('disabled', false);
    },

    /**
     * submit
     * @memberOf LC.addRecommendedBasket
     */
    submit: function (event) {
        event.preventDefault();

        // Before trigger
        this.trigger('submitBefore', event);

        if (!this.el.$form.isValid()) return false;

        $.post(
            '/basket/addRecommendedBasket/',
            { data: JSON.stringify({ id: this.data.id }) },
            this.callback.bind(this),
            'json'
        );

        // Disable buy button
        this.addButton.prop('disabled', true);

        // Callback trigger
        this.trigger('submitCallback', event);
    },

    /**
     * callback
     * @memberOf LC.addRecommendedBasket
     */
    callback: function (data) {
        // Reload miniBasket
        LC.miniBasket.reload();

        // Enable buy button
        this.addButton.prop('disabled', false);

        if (LC.config.showModalBasket && (settings.isMobile === true || window.innerWidth < MEDIA_MOBILE)) {
            var localPath = settings && settings.checkoutPath ? settings.checkoutPath : '/checkout/basket';
            var modalContent = '';

            if (data.data.stockLock) {
                modalContent +=
                    '<div class="basketCountdown" data-lc-basket-expires=\'{"expires":' + data.data.stockLock.expires + '}\'>' +
                    '<div class="active">' + languageSheet.lockedStockRemainingTime + '</div>' +
                    '<div class="expired">' + languageSheet.lockedStockExpiredTime + '</div>' +
                    '</div>';
            }
            modalContent +=
                '<div id="modalBasketButtons">' +
                '<a href="' + localPath + '" class="modalBasketEndOrder ' + BTN_PRIMARY_CLASS + '">' + languageSheet.basketEndOrder + '</a>' +
                '<a data-dismiss="modal" data-bs-dismiss="modal" class="modalBasketContinueShopping ' + BTN_SECONDARY_CLASS + '">' + languageSheet.basketContinueShopping + '</a>' +
                '<a href="/user" class="modalBasketMyAccount ' + BTN_SECONDARY_CLASS + '">' + languageSheet.myAccount + '</a>' +
                '</div>';

            this.addButton.box({
                uid: 'mobileBasketModal',
                source: modalContent,
                showFooter: false,
                triggerOnClick: false,
                type: 'html',
            });
        }
    },
});


