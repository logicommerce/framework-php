/**
 * One Step Checkout
 *
 * @description One Step Checkout JS module
 * @author Logicommerce
 * @version 3.0 2021-11-11
 *
 * @TODO: Allowing multiple modules with identical types
 *
 *
 * Config properties example
 * -------------------------------
 * LC.oneStepCheckoutConfig = {
 *   emailInputDelay: 600,
 *   notifyMode: true
 * };
 *
 */

LC.OneStepCheckout = LC.Form.extend({
    name: 'oneStepCheckout',
    options: {},
    tracking: [],
    submitButton: [],

    /**
     * DataModules defines each module and its interactions
     * @type {Object}
     */
    dataModules: {
        basket: {
            autoRefresh: true,
            initializeMethod: 'initializeBasket',
            modulesToRefresh: [
                'basket',
                'buttons',
                'discounts',
                'gifts',
                'linkeds',
                'lockedStocks',
                'payments',
                'rewardPoints',
                'shippings',
                'discountPrediction',
                'saveForLater',
                'selectableGifts',
            ],
        },
        buttons: {
            autoRefresh: true,
            initializeMethod: 'initializeButtons',
        },
        comments: {},
        userForm: {
            initializeMethod: 'initializeUserForm',
            createAccount: true,
            modulesToRefresh: [
                'basket',
                'buttons',
                'discounts',
                'gifts',
                'linkeds',
                'lockedStocks',
                'payments',
                'rewardPoints',
                'shippings',
                'discountPrediction',
                'saveForLater',
                'selectableGifts'
            ],
        },
        discounts: {
            autoRefresh: true,
            initializeMethod: 'initializeDiscounts',
        },
        linkeds: {
            modulesToRefresh: [
                'basket',
                'buttons',
                'discounts',
                'gifts',
                'linkeds',
                'lockedStocks',
                'payments',
                'rewardPoints',
                'shippings',
                'saveForLater',
                'selectableGifts'
            ],
        },
        lockedStocks: {
            modulesToRefresh: [
                'basket',
                'buttons',
                'discounts',
                'gifts',
                'linkeds',
                'lockedStocks',
                'payments',
                'rewardPoints',
                'shippings',
                'saveForLater',
                'selectableGifts'
            ],
        },
        gifts: {
            autoRefresh: true,
            modulesToRefresh: [
                'basket',
                'buttons',
                'discounts',
                'gifts',
                'linkeds',
                'lockedStocks',
                'payments',
                'rewardPoints',
                'shippings',
                'saveForLater',
                'selectableGifts'
            ],
        },
        payments: {
            autoRefresh: true,
            initializeMethod: 'initializePayments',
            modulesToRefresh: [
                'basket',
                'buttons',
                'discounts',
                'gifts',
                'linkeds',
                'lockedStocks',
                'payments',
                'rewardPoints',
                'shippings',
                'saveForLater',
                'selectableGifts'
            ],
        },
        shippings: {
            autoRefresh: true,
            initializeMethod: 'initializeShippings',
            modulesToRefresh: [
                'basket',
                'buttons',
                'discounts',
                'gifts',
                'linkeds',
                'lockedStocks',
                'payments',
                'rewardPoints',
                'shippings',
                'saveForLater',
                'selectableGifts'
            ],
        },
        saveForLater: {
            modulesToRefresh: [
                'basket',
                'buttons',
                'discounts',
                'gifts',
                'linkeds',
                'lockedStocks',
                'payments',
                'rewardPoints',
                'shippings',
                'saveForLater',
                'selectableGifts'
            ],
        },
        rewardPoints: {
            autoRefresh: true,
            initializeMethod: 'initializeRewardPoints'
        },
        legalCheck: {
            initializeMethod: 'initializeLegalCheck',
        },
        discountPrediction: {
            autoRefresh: true,
            initializeMethod: 'initializeDiscountPrediction',
        },
        miniBasket: {
            autoRefresh: true,
            initializeMethod: 'initializeMiniBasket',
        },
        selectableGifts: {
            modulesToRefresh: [
                'basket',
                'buttons',
                'discounts',
                'gifts',
                'linkeds',
                'lockedStocks',
                'payments',
                'rewardPoints',
                'shippings',
                'saveForLater'
            ],
        },
    },

    /**
     * AdditionalContent defines each content, located outside OSC form
     * @type {Object}
     */
    additionalContent: {
        specialProducts: {
            autoRefresh: true,
            initializeMethod: 'initializeSpecialProducts',
        },
        discountPrediction: {
            autoRefresh: true,
            initializeMethod: 'initializeDiscountPrediction',
        },
        linkeds: {
            autoRefresh: true,
            initializeMethod: 'initializeLinkeds',
        },
        lockedStocks: {
            autoRefresh: true,
            initializeMethod: 'initializeLockedStocks',
        },
        saveForLater: {
            autoRefresh: LC.global.session.login || globalThis?.lcCommerceSession?.userId ? true : false,
            initializeMethod: 'initializeSaveForLater',
        },
        selectableGifts: {
            autoRefresh: true,
            initializeMethod: 'initializeSelectableGifts',
        },
    },

    /**
     * This flag allows edit
     * @type {Boolean}
     */
    userVerified: false,

    /**
     * This flag controllers end order
     * @type {Boolean}
     */
    endOrder: undefined,

    /**
     * Determines delay to launch validation in input email
     * @type {Number}
     */
    emailInputDelay: 500,

    /**
     * Control email to prevent multiple validation in same value
     * @type {String}
     */
    emailInputValue: '',

    /**
     * Determines if messages are shown as Notify or message
     * @type {Boolean}
     */
    notifyMode: LC.notifyMode,

    /**
     * User email exists error message
     * @type {Boolean}
     */
    userEmailExistsError: `${LC.global.languageSheet.userEmailExistsOsc} <a class="emailErrorLoginCall" data-bs-toggle="modal" data-bs-target="#userLogin" role="button">${LC.global.languageSheet.oneStepCheckoutLogin}</a>`,

    warningInvalidAddress: LC.global.languageSheet.warningInvalidBillingAddress,

    moduleCallsStatus: {},

    // Initialize methods
    // ---------------------------------------------------------------------------

    /**
     * Initialize Object
     * @memberOf LC.OneStepCheckout
     */
    initialize: function () {
        // Set Config
        if (LC.oneStepCheckoutConfig?.emailInputDelay && typeof LC.oneStepCheckoutConfig.emailInputDelay === 'number') {
            this.emailInputDelay = LC.oneStepCheckoutConfig.emailInputDelay;
        }
        if (LC.oneStepCheckoutConfig?.notifyMode && typeof LC.oneStepCheckoutConfig.notifyMode === 'boolean') {
            this.notifyMode = LC.oneStepCheckoutConfig.notifyMode;
        }

        // Before trigger
        this.trigger('initializeBefore');

        // MiniBasket module
        this.initializeMiniBasket();

        // Modules
        this.el.$form.find('[data-lc-checkout]').each(
            function (index, el) {
                var $el = $(el);
                var key = $el.data('lcCheckout');
                var module = this.dataModules[key];

                // Set module
                // If module does not exist, maybe it is custom module
                if (!module) {
                    // Custom Module
                    var moduleProperties = $el.data('lcCheckoutProperties');
                    if ($.isPlainObject(moduleProperties)) {
                        module = moduleProperties;

                        // RegisterModule
                        this.dataModules[key] = module;
                    }
                }

                // Initialize
                if (module) {
                    module.active = true;
                    module.el = $el;
                    module.params = $el.data('lcCheckoutParams');

                    // Initialize module
                    if (module.initializeMethod && $.isFunction(this[module.initializeMethod]))
                        this[module.initializeMethod](module);
                }
            }.bind(this)
        );

        // Additional content (outside form)
        $('div[data-lc-oscac]').each(
            function (index, el) {
                var $el = $(el);
                var key = $el.data('lcOscac');
                var content = this.additionalContent[key];

                // Initialize
                if (content) {
                    content.active = true;
                    content.el = $el;

                    // Initialize content
                    if (content.initializeMethod && $.isFunction(this[content.initializeMethod]))
                        this[content.initializeMethod](content);
                }
            }.bind(this)
        );

        const userLoginModal = document.getElementById('userLogin');
        if (userLoginModal) {
            userLoginModal.addEventListener('shown.bs.modal', (event) => {
                // var loginForm = new LC.LoginForm($box.find('form')[0]);
                const inputEmail = $(userLoginModal.querySelector('#username')),
                    inputPassword = $(userLoginModal.querySelector('#password')),
                    delaySetFocus = 400;

                // Set input email
                if (this.loginEmailValue) {
                    inputEmail.val(this.loginEmailValue);
                    this.loginEmailValue = '';
                    setTimeout(function () {
                        inputPassword.focus();
                    }, delaySetFocus);
                } else {
                    setTimeout(function () {
                        inputEmail.focus();
                    }, delaySetFocus);
                }
            });
        }

        // Edit callback method on BuyForm & BuyProductForm
        LC.BuyProductForm.prototype.callback = LC.BuyForm.prototype.callback = function (data) {
            if (data.status.code != 200) {
                this.notify(LC.global.languageSheet.oneStepCheckoutProductAddedError, 'danger');
                return false;
            } else if (data.data.response.success) {
                // Call notify
                this.notify(LC.global.languageSheet.oneStepCheckoutProductAdded, 'success');
                // Refresh modules
                this.moduleCalls('refreshModule');
            } else {
                //Hide loading manually
                this.moduleCalls('loadingIndicator', false);
                this.notify(LC.global.languageSheet.oneStepCheckoutProductAddedError, 'danger');
                return false;
            }
        }.bind(this);

        if (logicommerceGlobal.settings.useOSCAsync) {
            this.moduleCalls('refreshModule');
        }

        // Init Attachments
        this.el.$customTagAttachment = this.el.$form.find('.lcCustomTagAttachment');
        if (this.el.$customTagAttachment.length) {
            LC.CheckoutForm.prototype.attachmentFields(this.el.$customTagAttachment);
        }

        LC.CheckoutForm.prototype.initCalendar.call(this);

        // Callback
        this.trigger('initializeCallback');

        this.trigger('onLoad');

        // Clean unload
        this.el.$form.removeClass('unload');

        $.formUtils.addValidator({
            name: 'userEmailExists',
            validatorFunction: function (value, $elem, conf, language, $form) {
                const data = $elem.data('userEmailExistsOsc');
                if (typeof data === 'undefined') {
                    return true;
                } else if (data === true) {
                    return false
                }
                return true;
            },
            errorMessage: this.userEmailExistsError,
            errorMessageKey: 'userEmailExists'
        });

        $.formUtils.addValidator({
            name: 'userAddressValidation',
            validatorFunction: function (value, $elem, conf, language, $form) {
                const data = $elem.data('userAddressValidationFailedOsc');
                this.errorMessage = '';
                if (typeof data === 'undefined') {
                    return true;
                } else if (data === true) {
                    this.errorMessage = $elem.data('userAddressValidationMessagesOsc');
                    return false
                }
                return true;
            },
            errorMessageKey: 'userAddressValidation'
        });
    },

    /**
     * Initialize Buttons
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     */
    initializeButtons: function (module) {
        // Call trigger
        this.trigger('initializeButtonsBefore');

        // Submit
        this.submitButton = module.el.find('#basketEndOrder');

        // Disable submit (enabled after last module inicialized)
        if (this.submitButton.length) this.submitButton.each((index, $el) => {
            $el.disabled = true;
        });


        // Call trigger
        this.trigger('initializeButtonsCallback');
        return;
    },

    /**
     * Initialize Basket
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     */
    initializeBasket: function (module) {
        // Before trigger
        this.trigger('initializeBasketBefore');

        // Delete rows
        module.el.find('[data-lc-basketdeleterow],[data-lc-basketdeleterows]').click(this.deleteRow.bind(this));

        // Delete discounts
        module.el.find('[data-lc-basketdeletediscount]').click(this.deleteDiscount.bind(this));

        // Save for later rows
        module.el.find('[data-lc-basketsaveforlaterrow]').click(this.saveForLater.bind(this));

        // Quantity
        var quantityElements = module.el.find('input.basketQuantity:text,select.basketQuantity');
        quantityElements.change(this.onChangeOption.bind(this)).keypress(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                this.onChangeOption(event);
            }
        }.bind(this));

        // Initialize Quantity Fields if it is required
        module.el.find('input[data-lc-quantity]').quantity();
        module.el.find('select[data-lc-quantity]').quantity();

        // Options
        var optionsElements = module.el.find(
            'select.productOptionSelectValue, input.productOptionCheckboxValue, input.productOptionBooleanValue, input.productOptionTextValue'
        );
        optionsElements.change(this.onChangeOption.bind(this));

        // Prevent submit form on ENTER key
        module.el.find('input[type="text"]').keypress(function (event) {
            /* Act on the event */
            if (event.keyCode == 13) return false;
        });

        LC.initializeCountdowns();
        LC.basketExpiration.checkNewExpirationDate();

        // Init Attachments
        this.el.$customTagAttachment = this.el.$form.find('.lcCustomTagAttachment');
        if (this.el.$customTagAttachment.length) {
            LC.CheckoutForm.prototype.attachmentFields(this.el.$customTagAttachment);
        }

        LC.CheckoutForm.prototype.initCalendar.call(this);

        // Callback
        this.trigger('initializeBasketCallback');
    },

    initializeMiniBasket: function (module) {
        var key = 'miniBasket';
        module = module || this.dataModules[key];

        if (!module.active) {
            LC.miniBasket.oneStepCheckoutCallback = function () {
                // Call to refresh
                this.moduleCalls('refreshModule');
            }.bind(this);
            module.active = true;

            return;
        }

        if (LC.hasOwnProperty(key)) {
            if (LC.miniBasket.exists()) {
                // Refresh miniBasket
                LC.miniBasket.reload(function () {
                    this.moduleCallDone('refreshModule', key, function () {
                        this.trigger('onLoad');
                    }.bind(this));
                }.bind(this));
            } else {
                this.moduleCallDone('refreshModule', key, function () {
                    this.trigger('onLoad');
                }.bind(this));
            }
        }
    },

    /**
     * Initialize userForm
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     */
    initializeUserForm: function (module) {
        // Initialize Data events
        module.el.find('[data-lc-event]').dataEvent();

        // Add trigger to surrogate function, or "call" before/after
        this.trigger('initializeUserFormBefore');

        // Call LC.UserForm
        var userFormElement = module.el.find('.userForm ')[0];
        this.userForm = new LC.UserForm(userFormElement);

        var $signInCheckbox = module.el.find('#createAccountCheck');

        // Check email input
        this.inputEmail = this.getInputEmail();
        this.inputAddress = this.getInputAddress();

        // Turn off data-validation on email
        this.restoreUserFormValidation();

        // Own email validation
        this.userForm.el.$form.find('.userFieldGroupEmail input[type="email"]').on('input', (event) => {
            // Get element
            var inputEmail = event.target;

            $(inputEmail).data('userEmailExistsOsc', false);

            // Set value if it changes
            if (this.emailInputValue === inputEmail.value) return;

            // Clear email time out. (It prevents multiple calls to server)
            if (this.emailInputTimeout) clearTimeout(this.emailInputTimeout);

            // Get value
            this.emailInputValue = inputEmail.value;

            // Validate email - Exit if is invalid.
            if (!this.isValid('email', inputEmail.value)) {
                $(inputEmail.parentNode)
                    .removeClass('has-success')
                    .addClass('has-error')
                    .find('.form-error')
                    .remove()
                    .end()
                    .append('<span class="help-block form-error">' + LC.global.languageSheet.JsFormUtils_badEmail + '</span>');
                return;
            }

            // Clean error
            $(inputEmail.parentNode)
                .removeClass('has-error')
                .addClass('has-success')
                .find('.form-error')
                .remove();

            // Fill dataForm
            this.fillDataForm(this.dataModules.userForm);

            // Timeout Email input
            this.emailInputTimeout = setTimeout(function () {
                this.recalculateBasket(
                    this.dataModules.userForm,
                    true,
                    function (response) {
                        // Clean loading
                        $(inputEmail.parentNode).removeClass('lcLoading');
                    }.bind(this)
                );

            }.bind(this), this.emailInputDelay);
        });

        // Own address validation
        this.userForm.el.$form.find('.userFieldGroupAddress input[type="text"]').on('input', (event) => {
            var inputAddress = event.target;
            $(inputAddress).data('userAddressValidationFailedOsc', false);
            if (this.addressInputValue === inputAddress.value) {
                return;
            }
            if (this.addressInputTimeout) {
                clearTimeout(this.addressInputTimeout);
            }
            this.addressInputValue = inputAddress.value;
            this.fillDataForm(this.dataModules.userForm);
            const containers = document.querySelectorAll('.userFieldGroupCountry');
            let allInputsFilled = true;
            let locationInputs = 0;
            for (const container of containers) {
                const hiddenInputs = container.querySelectorAll('input[type="hidden"]');
                for (const input of hiddenInputs) {
                    locationInputs++;
                    if (!input.value || input.value.trim() === '') {
                        allInputsFilled = false;
                    }
                }
            }
            if (locationInputs === 0) {
                allInputsFilled = false;
            }
            if (allInputsFilled) {
                this.addressInputTimeout = setTimeout(function () {
                    this.recalculateBasket(
                        this.dataModules.userForm,
                        true,
                        function (response) {
                            $(inputAddress.parentNode).removeClass('lcLoading');
                        }.bind(this)
                    );

                }.bind(this), this.emailInputDelay);
            }
        });

        // Recalculate basket on select Postal Code
        this.userForm.selectPostalCodeCallback('userInfo', function () {
            this.recalculateBasket(module, true, this.restoreUserFormValidation.bind(this));
        }.bind(this));
        this.userForm.selectPostalCodeCallback('shippingAddress', function () {
            this.recalculateBasket(module, true, this.restoreUserFormValidation.bind(this));
        }.bind(this));

        // Use shipping address call on change
        this.userForm.el.$useShippingAddress.on('change', function () {
            this.recalculateBasket(module, true, this.restoreUserFormValidation.bind(this));
        }.bind(this));

        this.userForm.el.$form.find('.userFieldGroupBirthday .date').on('dp.change', function (event) {
            if (event.target.outerHTML.indexOf('required') >= 0) {
                event.target.name = 'birthDay';
                if (this.validateFilledUserForm(function () { }, event) === true) {
                    this.recalculateBasket(module, true, this.restoreUserFormValidation.bind(this));
                }
            }
        }.bind(this));

        // Create Account check control
        if (!module.createAccount && $signInCheckbox[0].checked) $signInCheckbox.click();

        // Sign in checkbox event
        $signInCheckbox.on('click', function (event) {
            module.createAccount = event.target.checked;
            // Call recalculate basket if email input is filled and valid
            if (this.inputEmail.value.length && this.isValid('email', this.inputEmail.value))
                this.recalculateBasket(module, true);
        }.bind(this));

        // On every change in input form it is necessary control userForm is filled
        this.el.$form.on('change', this.validateFilledUserForm.bind(this, function () {
            this.recalculateBasket(module, true);
        }.bind(this)));

        // AddressBook callbacks
        // Shipping address Book
        if (this.userForm.el.useShippingAddressBook) {
            this.userForm.el.useShippingAddressBook.addEventListener('change', function () {
                this.recalculateBasket(module, true, this.restoreUserFormValidation.bind(this));
            }.bind(this));
        }

        module.el.find('input:radio[name="shippingAddress"], input:radio[name="billingAddress"]').on('change', function () {
            this.recalculateBasket(module, true, this.restoreUserFormValidation.bind(this));
        }.bind(this));

        // Call
        var addressFormCallbackBefore = function (result) {
            this.userForm.el.$form.find('.blockAddressBook:not([class*="SectionSeparator"])').addClass('loading');
            if (result.data.response && result.data.response.success) {
                // Stop radio button listener
                this.userForm.el.$form.find('input:radio[name="shippingAddress"]').off('change');
            }
        };
        LC.BillingAddressBookForm.addEvent('oscCallbackBefore', addressFormCallbackBefore.bind(this));
        LC.ShippingAddressBookForm.addEvent('oscCallbackBefore', addressFormCallbackBefore.bind(this));

        var addressCallback = function (result) {
            if (result.data.response && result.data.response.success) {
                // Start radio button listener
                this.userForm.el.$form
                    .find('div.addressBook input#billingAddress_' + result.data.data.data.id + ', div.addressBook input#shippingAddress_' + result.data.data.data.id)
                    .on('change', function () {
                        this.recalculateBasket(module, true, this.restoreUserFormValidation.bind(this));
                    }.bind(this));
                // Recalculate basket AFTER inserting the new address
                this.recalculateBasket(module, true, this.restoreUserFormValidation.bind(this));
            } else {
                // On reached max addresses remove loading
                this.userForm.el.$form.find('.blockAddressBook').removeClass('loading');
            }
        };
        LC.BillingAddressBookForm.addEvent('oscCallback', addressCallback.bind(this));
        LC.ShippingAddressBookForm.addEvent('oscCallback', addressCallback.bind(this));

        // Callback
        this.trigger('initializeUserFormCallback');
    },

    getInputEmail: function () {
        const inputEmail = this.userForm.el.$form.find('.userFieldGroupEmail input[type="email"]:visible')[0];

        $(inputEmail).attr('data-validation', function () {
            const thisAttr = $(this).attr('data-validation');
            if (thisAttr.includes('userEmailExists')) {
                return thisAttr;
            } else {
                return (thisAttr ? thisAttr + ',' : '') + 'userEmailExists';
            }
        });

        return inputEmail;
    },

    getInputAddress: function () {
        const inputAddress = this.userForm.el.$form.find('.userFieldGroupAddress input[type="text"]:visible')[0];
        $(inputAddress).attr('data-validation', function () {
            const thisAttr = $(this).attr('data-validation');
            if (thisAttr.includes('userAddressValidation')) {
                return thisAttr;
            } else {
                return (thisAttr ? thisAttr + ',' : '') + 'userAddressValidation';
            }
        });
        return inputAddress;
    },

    /**
     * Initialize shippings
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     */
    initializeShippings: function (module) {
        // Trigger
        this.trigger('initializeShippingsBefore');

        $lcModalContainerPhysicalLocationsPoints = module.el.find('.physicalLocations.physicalLocationsPoints').parent();
        $physicalLocationsLcModalBody = $('#physicalLocations').find('.lcModalContainer');
        if ($lcModalContainerPhysicalLocationsPoints.length && $physicalLocationsLcModalBody.length) {
            $physicalLocationsLcModalBody.html($lcModalContainerPhysicalLocationsPoints);
        }

        $lcModalContainerPickupPointProviders = module.el.find('.physicalLocations.pickupPointProviders').parent();
        $pickupPointProvidersLcModalBody = $('#pickupPointProviders').find('.lcModalContainer');
        if ($lcModalContainerPickupPointProviders.length && $pickupPointProvidersLcModalBody.length) {
            $pickupPointProvidersLcModalBody.html($lcModalContainerPickupPointProviders);
        }

        this.shippingData = {};
        this.el.shippingSection = module.el.find('input.shippingTypeSelector:radio');
        this.el.shippingSection = $.merge($('#physicalLocations button.savePickingSelectionButton'), this.el.shippingSection);
        this.el.shippingSection.each(function (index, el) {
            let $el = $(el);
            if (!$el.prop('init')) {
                $el.prop('init', true);
                el.addEventListener('click', this.setShippingSection.bind(this, el));
                el.disabled = false;
            }
        }.bind(this));

        this.el.shippingSection = module.el.find('#pickupPointProviders').find('input.shippingTypeSelector:radio');
        this.el.shippingSection = $.merge($('#pickupPointProviders button.savePickingSelectionButton'), this.el.shippingSection);
        this.el.shippingSection.each(function (index, el) {
            let $el = $(el);
            if (!$el.prop('init')) {
                $el.prop('init', true);
                el.addEventListener('click', this.setShippingSection.bind(this, el));
                el.disabled = false;
            }
        }.bind(this));

        $('[data-lc-event]').dataEvent();

        // Trigger
        this.trigger('initializeShippingsCallback');
    },

    /**
     * Initialize Payments
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     */
    initializePayments: function (module) {
        // Call trigger
        this.trigger('initializePaymentsBefore');

        //Plugin Listener
        LC.resources.pluginListener('initializePaymentsBefore', this.el.$form, true);

        module.el.find('[data-lc-event]').dataEvent();
        this.el.paymentSystemSelectors = module.el.find('input.basketSelectorPaymentInput:radio');
        this.el.paymentSystemSelectors.each(function (index, el) {
            if (!$(el).data('lc-express-checkout')) {
                el.addEventListener('click', this.setPaymentSystem.bind(this));
                el.disabled = false;
            }
        }.bind(this));

        // Call trigger
        this.trigger('initializePaymentsCallback');

        //Plugin Listener
        LC.resources.pluginListener('initializePaymentsCallback', this.el.$form, true);
    },

    /**
     * Initialize discounts
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     * @return {void}
     */
    initializeDiscounts: function (module) {
        // Call trigger
        this.trigger('initializeDiscountsBefore');

        this.el.$discountCode = module.el.find('#voucherField');

        // Prevent auto submit on Enter key and call addDiscount
        this.el.$discountCode.keypress(function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                this.addDiscount();
            }
        }.bind(this));

        //Binding click event on each trash icon once a giftCode has been added.
        var deleteVoucherCode = module.el.find('span.deleteVoucherCode');
        deleteVoucherCode.each(
            function (index, el) {
                $(el).on('click', this.deleteVoucherCode.bind(this, el));
            }.bind(this)
        );

        // Set discount action
        module.el.find('#voucherButton').click(this.addDiscount.bind(this));

        //Remove title of removeDiscountsSection if no elements.
        if ($('.ticketCodesGroup .outputDiscountName').length == 0) {
            $('.ticketCodesTitle').remove();
        }

        this.loadingIndicator('loadingIndicator', module, false);

        // Call trigger
        this.trigger('initializeDiscountsCallback');
    },

    initializeDiscountPrediction: function (module) {
        module.el.find('[data-lc-event]').dataEvent();

        module.el.find('[data-lc-form="buyForm"]').each(function (index, form) {
            if (!form.initialized) new LC.BuyForm(form);
        });
        module.el.find('[data-lc-form="buyProductForm"]').each(function (index, form) {
            if (!form.initialized) new LC.BuyProductForm(form);
        });
    },

    /**
     * Initialize Legal check
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     * @return {void}
     */
    initializeLegalCheck: function (module) {
        // If module does not exist, get it from dataModules
        if (!module) module = this.dataModules.legalCheck;

        // Pass when module is not active
        if (!module.active) return;

        // Call trigger
        this.trigger('initializeLegalCheckBefore');

        // Find checkbox
        if (!module.elCheckbox) module.elCheckbox = module.el.find('input[name="agreement"]');

        // This field is always required
        module.elCheckbox[0].setAttribute('data-validation', 'required');

        // Event when it changes
        // This call enables/disables submitButton if it can
        module.elCheckbox.on('click', function (event) {
            // Submit
            if (!this.submitButton) this.submitButton = this.dataModules.buttons.el.find('#basketEndOrder');

            const endOrder = event.target.checked && (this.endOrder || this.submitButton.data('endOrder'));

            if (this.submitButton.length) this.submitButton.each((index, $el) => {
                $el.disabled = !endOrder;
            });

        }.bind(this));

        // Call trigger
        this.trigger('initializeLegalCheckCallback');
    },

    /**
     * Initialize SpecialProducts content
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     * @return {void}
     */
    initializeSpecialProducts: function (module) {
        // Call trigger
        this.trigger('initializeSpecialBefore');

        module.el.find('[data-lc-event]').dataEvent();
        module.el.find('[data-lc-form="buyForm"]').each(function (index, form) {
            if (!form.initialized) new LC.BuyForm(form);
        });
        module.el.find('[data-lc-form="buyProductForm"]').each(function (index, form) {
            if (!form.initialized) new LC.BuyProductForm(form);
        });
        module.needToRefresh = true;

        // Call trigger
        this.trigger('initializeSpecialCallback');
    },

    /**
     * Initialize linkeds content
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     * @return {void}
     */
    initializeLinkeds: function (module) {
        // Call trigger
        this.trigger('initializeLinkedsBefore');

        module.el.find('[data-lc-event]').dataEvent();
        module.el.find('[data-lc-form="buyForm"]').each(function (index, form) {
            if (!form.initialized) new LC.BuyForm(form);
        });
        module.el.find('[data-lc-form="buyProductForm"]').each(function (index, form) {
            if (!form.initialized) new LC.BuyProductForm(form);
        });
        module.needToRefresh = true;

        // Call trigger
        this.trigger('initializeLinkedCallback');
    },

    /**
     * Initialize locked stocks content
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     * @return {void}
     */
    initializeLockedStocks: function (module) {
        // Call trigger
        this.trigger('initializeLockedStocksBefore');

        module.el.find('[data-lc-event]').dataEvent();
        LC.initializeCountdowns();
        LC.basketExpiration.checkNewExpirationDate();
        module.needToRefresh = true;

        // Call trigger
        this.trigger('initializeLockedStocksLinkedCallback');
    },

    /**
     * Initialize saveForLater content
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     * @return {void}
     */
    initializeSaveForLater: function (module) {
        // Call trigger
        this.trigger('initializeSaveForLaterBefore');

        LC.dataEvents.transferToBasketSaveForLaterRow = (event) => {
            event.preventDefault();
            var id = $(event.currentTarget).data('lc-id');
            this.request(
                LC.global.routePaths.BASKET_INTERNAL_TRANSFER_TO_BASKET_SAVE_FOR_LATER_ROW,
                'POST',
                { id: id },
                function (result) {
                    this.moduleCalls('loadingIndicator', false);
                    if (result.status.code == 200 && result.data.response.success === 1) {
                        this.moduleCalls('refreshModule');
                    } else {
                        this.notify(result.data.response.message, 'danger');
                    }
                }.bind(this)
            );
        };

        module.el.find('[data-lc-event]').dataEvent();

        module.needToRefresh = true;

        // Call trigger
        this.trigger('initializeSaveForLaterCallback');
    },

    /**
     * Initialize selectable gifts content
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     * @return {void}
     */
    initializeSelectableGifts: function (module) {
        // Call trigger
        this.trigger('initializeSelectableGiftsBefore');

        module.el.find('[data-lc-event]').dataEvent();
        module.el.find('[data-lc-form="buyForm"]').each(function (index, form) {
            if (!form.initialized) new LC.BuyForm(form);
        });
        module.el.find('[data-lc-form="buyProductForm"]').each(function (index, form) {
            if (!form.initialized) new LC.BuyProductForm(form);
        });
        module.needToRefresh = true;

        // Call trigger
        this.trigger('initializeSelectableGiftsCallback');
    },

    /**
     * Initialize rewardPoints content
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module
     * @return {void}
     */
    initializeRewardPoints: function (module) {
        // Call trigger
        this.trigger('initializeRewardPointsBefore');

        // Set rewardPoint action
        module.el.find('.rewardPointButton').each(
            function (index, el) {
                el.addEventListener('click', this.applyRewardPoints.bind(this, $(el).data('lc-id')));
                el.disabled = false;
            }.bind(this)
        );

        // Quantity
        const quantityElements = module.el.find('input.rewardPointQuantity, select.rewardPointQuantity');
        quantityElements
            .keypress(function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    this.applyRewardPoints($(event.target).data('lc-id'))
                }
            }.bind(this));

        // Initialize Quantity Fields if it is required
        module.el.find('input[data-lc-quantity]').quantity();
        module.el.find('select[data-lc-quantity]').quantity();

        module.needToRefresh = true;

        // Call trigger
        this.trigger('initializeRewardPointsCallback');
    },

    // Object methods
    // ---------------------------------------------------------------------------

    /**
     * Call functions for each active and autoRefresh module
     * @memberOf LC.OneStepCheckout
     * @param  {Function} callback Internal function from THIS
     * @params {[any]}    Array of arguments
     */
    moduleCalls: function (callback) {
        // Get function
        var functionCallback = this[callback];

        // Check function
        if (!$.isFunction(functionCallback)) return;

        // Arguments
        var args = Array.prototype.slice.call(arguments, 1);

        // Loop modules
        for (var key in this.dataModules) {
            var module = this.dataModules[key];

            // Discriminate inactive, forbidden callbacks
            if (
                module.active &&
                ((callback == 'loadingIndicator' && module.autoRefresh) ||
                    (callback == 'refreshModule' && module.autoRefresh) ||
                    callback == 'lockModule')
            ) {
                // Array key and module
                var moduleArr = [key, module];

                this.moduleCallStart(callback, key);

                // Call function
                functionCallback.apply(this, moduleArr.concat(args));
            }
        }

        // Additional content
        for (var key in this.additionalContent) {
            var content = this.additionalContent[key];

            // Discriminate inactive, forbidden callbacks
            if (
                content.active &&
                ((callback == 'loadingIndicator' && content.autoRefresh) ||
                    (callback == 'refreshModule' && content.autoRefresh) ||
                    callback == 'lockModule')
            ) {
                this.moduleCallStart(callback, key);

                // Call function
                functionCallback.apply(this, [key, content].concat(args));
            }
        }
    },

    moduleCallStart: function (type, name) {
        if (!this.moduleCallsStatus[type]) this.moduleCallsStatus[type] = {};

        this.moduleCallsStatus[type][name] = true;
        return true;
    },

    moduleCallDone: function (type, name, lastCallback) {
        if (!this.moduleCallsStatus[type]) return false;
        if (this.moduleCallsStatus[type].hasOwnProperty(name) && this.moduleCallsStatus[type][name]) {
            delete this.moduleCallsStatus[type][name];
        }
        if (lastCallback && typeof lastCallback === 'function') {
            var objLen = Object.keys(this.moduleCallsStatus[type]).length;
            if (objLen === 1) { // if is the last item to check
                lastCallback();
                if (this.submitButton.length) this.submitButton.each((index, $el) => {
                    $el.disabled = !this.submitButton.data('endOrder');
                });
            } else {
                return false;
            }
        }
        return true;
    },

    /**
     * Define module is loading or not
     * @memberOf LC.OneStepCheckout
     * @param  {string} key    Module name
     * @param  {Object} module Module object
     * @param  {Boolean} show  Shows or hides loading
     */
    loadingIndicator: function (key, module, show) {
        // Ensure module exists
        if (!module || typeof module !== 'object') module = this.dataModules[key];

        // Check module is active
        if (!module.active || !module.hasOwnProperty('el')) return;

        if (show) module.el.addClass('loading');
        else module.el.removeClass('loading');
    },

    /**
     * Define if  module is blocked or not
     * @memberOf LC.OneStepCheckout
     * @param  {string} key    Module name
     * @param  {Object} module Module object
     * @param  {Boolean} show  Shows or hides loading
     */
    lockModule: function (key, module, blocked, forceChange) {
        // Ensure module exists
        if (!module || typeof module !== 'object') module = this.dataModules[key];

        // Check module is active
        if (!module.active) return;

        if (blocked) module.el.addClass('blocked');
        else module.el.removeClass('blocked');
    },

    /**
     * Tracking movements on One Step Checkout
     * @memberOf LC.OneStepCheckout
     * @param {string} moduleName Name of actions
     */
    setTracking: function (moduleName) {
        this.tracking.push(moduleName);
        this.trigger('tracking', moduleName, this.tracking);
    },

    // Interaction methods
    // ---------------------------------------------------------------------------

    /**
     * Change options
     * @memberOf LC.OneStepCheckout
     * @param  {object} event
     */
    onChangeOption: function (event) {
        //Set Tracking
        this.setTracking('changeOption');

        if (!this.trigger('onChangeOption')) {
            this.trigger('onChangeOptionBefore');
            this.recalculateBasket(this.dataModules.basket);
            this.trigger('onChangeOptionCallback', event.target);
        }
    },

    /**
     * Delete row
     * @memberOf LC.OneStepCheckout
     * @param  {object} event
     */
    deleteRow: function (event) {
        event.preventDefault();
        let position = $(event.currentTarget).data('lcBasketdeleterow'),
            positions = $(event.currentTarget).data('lcBasketdeleterows');

        // Show loading
        this.moduleCalls('loadingIndicator', true);

        // Set Tracking
        this.setTracking('deleteRow');

        // Fill data in order to send dataForm to POST.
        this.fillDataForm('basket');

        let data = {};
        data.id = $(event.currentTarget).parent().attr('id');
        data.hash = position;

        LC.resources.pluginListener('onRemoveProduct', event, data);

        // Post
        this.request(
            LC.global.routePaths.CHECKOUT_INTERNAL_OSC_RECALCULATE,
            'POST',
            { deleteRow: position, deleteRows: positions, dataForm: this.dataForm },
            function (result) {
                // Check status
                if (result.status.code != 200) {
                    //Hide loading manually
                    this.moduleCalls('loadingIndicator', false);
                    this.notify(LC.global.languageSheet.oneStepCheckoutProductDeletedError, 'danger');
                    return false;
                }
                if (result.data?.response?.success) {
                    // TODO move this to the basket refresh module
                    // Control totalItems in basket.
                    // If we have not any items, reload page
                    if (result.data?.data?.totalItems === 0) {
                        window.location = LC.global.routePaths.CHECKOUT;
                        return;
                    }
                    // Call notify
                    this.notify(LC.global.languageSheet.oneStepCheckoutProductDeleted, 'success');
                    // Refresh modules
                    this.moduleCalls('refreshModule');
                    LC.dataEvents.reloadCustomize();
                } else {
                    // Hide loading manually
                    this.moduleCalls('loadingIndicator', false);
                    this.notify(LC.global.languageSheet.oneStepCheckoutProductDeletedError, 'danger');
                }
                this.trigger('onDeleteRowCallback', result);
            }.bind(this)
        );
    },

    /**
     * Delete discount (GIFT)
     * @memberOf LC.OneStepCheckout
     * @param  {object} event
     */
    deleteDiscount: function (event) {
        // Prevent default
        event.preventDefault();

        //Set Tracking
        this.setTracking('deleteDiscount');

        // get Item
        var discountData = $(event.currentTarget).data('lcBasketdeletediscount');

        // Is gift
        if (discountData.hasOwnProperty('giftId')) {
            discountData.deleteGift = 1;
        }

        //Show loading
        this.moduleCalls('loadingIndicator', true);

        //Post
        this.request(
            LC.global.routePaths.CHECKOUT_INTERNAL_OSC_RECALCULATE,
            'POST',
            discountData,
            function (data) {
                // Check status
                if (data.statusCode != 200) {
                    //Hide loading manually
                    this.moduleCalls('loadingIndicator', false);
                    this.notify(LC.global.languageSheet.oneStepCheckoutProductDeletedError, 'danger');
                    return false;
                }

                if (data.response && data.response.DELETED) {
                    // Call notify
                    this.notify(LC.global.languageSheet.oneStepCheckoutProductDeleted, 'success');

                    // Refresh modules
                    this.moduleCalls('refreshModule');
                } else {
                    //Hide loading manually
                    this.moduleCalls('loadingIndicator', false);
                    this.notify(LC.global.languageSheet.oneStepCheckoutProductDeletedError, 'danger');
                }
            }.bind(this)
        );
    },

    /**
     * SaveForLater row
     * @memberOf LC.OneStepCheckout
     * @param  {object} event
     */
    saveForLater: function (event) {
        event.preventDefault();
        const hash = $(event.currentTarget).attr('data-lc-basketSaveForLaterRow');

        // Show loading
        this.moduleCalls('loadingIndicator', true);

        // Set Tracking
        this.setTracking('saveForLater');

        // Fill data in order to send dataForm to POST. This case is not the best
        // way and it is implemented to Ecoceutics multiple comments.
        this.fillDataForm('basket');

        // Post
        this.request(
            LC.global.routePaths.CHECKOUT_INTERNAL_OSC_RECALCULATE,
            'POST',
            { saveForLater: hash },
            function (result) {
                // Check status
                if (result.status.code != 200) {
                    //Hide loading manually
                    this.moduleCalls('loadingIndicator', false);
                    this.notify(LC.global.languageSheet.savedForLaterError, 'danger');
                    return false;
                }

                if ($.isEmptyObject(result.data?.data?.saveForLater?.incidences) && $.isEmptyObject(result.data?.data?.saveForLater?.error)) {
                    // TODO move this to the basket refresh module
                    // Control totalItems in basket.
                    // If we have not any items, reload page
                    if (result.data?.data?.totalItems === 0) {
                        window.location = LC.global.routePaths.CHECKOUT;
                        return;
                    }
                    // Call notify
                    this.notify(LC.global.languageSheet.savedForLater, 'success');
                    // Refresh modules
                    this.moduleCalls('refreshModule');
                } else {
                    // Hide loading manually
                    this.moduleCalls('loadingIndicator', false);
                    this.notify(LC.global.languageSheet.savedForLaterError, 'danger');
                }
            }.bind(this)
        );
    },

    /**
     * Set payment system
     * @memberOf LC.OneStepCheckout
     */
    setPaymentSystem: function (ev) {
        //Set Tracking
        this.setTracking('setPaymentSystem');

        //Plugin Listener
        LC.resources.pluginListener('setPaymentSystem', ev, ev.target.value, true);

        this.dataModules.payments.data = JSON.parse(ev.target.value);
        var inputsForAdditionalData = {};
        $('.basketSelectorPaymentAdditionalData_' + this.dataModules.payments.data.id).each(
            (index, el) => {
                inputsForAdditionalData[el.name] = el.value;
                el.remove();
            }
        )
        this.dataModules.payments.data['additionalData'] = JSON.stringify(inputsForAdditionalData);

        //Calls recalculate
        this.recalculateBasket(this.dataModules.payments);
    },

    /**
     * Set shipping section
     * @memberOf LC.OneStepCheckout
     * @param {Object} el Dom element
     */
    setShippingSection: function (el) {
        const $el = $(el);

        if ($el.closest('.shippingSelectorSelected').length) {
            return;
        }

        if ($el.hasClass('savePickingSelectionButton')) {
            const lcData = $el.closest('.modal-content').find('input[name="physicalLocation"]:checked').data('lc');
            if (lcData?.hash) {
                this.shippingData.deliveryHash = lcData.hash;
                this.shippingData.providerPickupPointHash = lcData.mode == 'PROVIDER_PICKUP_POINT' ? lcData.delivery.mode.providerPickupPoint.hash : 'no'
                this.shippingData.type = 'PICKING';
                bootstrap.Modal.getOrCreateInstance($el.closest('.modal-dialog').parent()).hide();
            } else {
                LC.notify(LC.global.languageSheet.deliveryPickingNoSelectedError, { type: 'danger' });
            }
        } else {
            this.shippingData.type = $el.data('lc-delivery-type');
            this.shippingData.deliveryHash = $el.data('lc-delivery-hash');
            this.shippingData.shipments = [];

            $el.closest('.delivery').find('input.shippingTypeSelector:checked').each((index, shipping) => {
                this.shippingData.shipments.push({
                    shippingHash: $(shipping).data('lc-shipping-hash'),
                    shipmentHash: $(shipping).data('lc-shipment-hash'),
                });
            });
        }

        // Set Tracking
        this.setTracking('setShippingSection');
        // Plugin Listener
        LC.resources.pluginListener('setShippingSection', el, { id: el.value }, true);
        // Recalculate
        this.recalculateBasket(this.dataModules.shippings);
    },

    /**
     * Set warehouse
     * @memberOf LC.OneStepCheckout
     */
    setWarehouse: function (event) {
        //Set Tracking
        this.setTracking('setWarehouse');

        //Calls recalculate
        this.recalculateBasket(this.dataModules.shippings);
    },

    /**
     * Add discount
     * @memberOf LC.OneStepCheckout
     */
    addDiscount: function () {
        // If discountCode element does not exist, ends method.
        if (!this.el.$discountCode) return;

        var code = this.el.$discountCode.val();

        if (code.length > 0) {
            //Show loading
            this.moduleCalls('loadingIndicator', true);

            //Set Tracking
            this.setTracking('addDiscount');

            //Prepare the values to be sent to the controller. The code is sent always
            //but maybe the other params doesn't exist. We can also do jsonToSend.code = escape(code);
            var jsonToSend = { code: escape(code), mode: 'ADD' };

            // Post
            this.request(
                LC.global.routePaths.CHECKOUT_INTERNAL_OSC_RECALCULATE,
                'POST',
                { voucher: jsonToSend },
                function (result) {
                    // Check status
                    this.moduleCalls('loadingIndicator', false);
                    if (result.status.code != 200) {
                        return false; // TODO: Show error to user
                    }
                    if (result.data.data.voucher.error) {
                        if (result.data.data.voucher.error.indexOf('VOUCHER_CODE_EXISTS') !== -1) {
                            this.notify(LC.global.languageSheet.errorCodeVoucherCodeExists, 'danger');
                        } else if (result.data.data.voucher.error.indexOf('DISCOUNT_CODE_EXISTS') !== -1) {
                            this.notify(LC.global.languageSheet.errorCodeDiscountCodeExists, 'danger');
                        } else {
                            this.notify(LC.global.languageSheet.errorCodeVoucherCodeNotFound, 'danger');
                        }
                    } else {
                        // Clean input text
                        this.el.$discountCode.val('');

                        // Refresh checkout
                        this.moduleCalls('refreshModule');

                        // Show discount message
                        this.showMessage(LC.global.languageSheet.voucherAdded, 'success');
                    }
                }.bind(this)
            );
        }
    },

    /**
     * Delete voucher code
     * @memberOf LC.CheckoutForm
     * @param  {object} el
     */
    deleteVoucherCode: function (el) {
        var data = $(el).data('lcData');

        // Ajax callback function
        var callbackDeleteVoucher = function (response) {
            if (response.data.response.success === 1) {
                this.moduleCalls('refreshModule');
            } else {
                this.showMessage(response.data.response.message, 'danger');
            }
        }.bind(this);

        $.post(
            LC.global.routePaths.BASKET_INTERNAL_DELETE_VOUCHER,
            {
                data: JSON.stringify({
                    code: data.code
                }),
            },
            callbackDeleteVoucher,
            'json'
        );
    },

    /**
     * Add reward points
     * @memberOf LC.OneStepCheckout
     * @param {number} id
     */
    applyRewardPoints: function (id) {
        const value = $('#rewardPointQuantity_' + id).val();

        if (value === undefined) return;

        //Show loading
        this.moduleCalls('loadingIndicator', true);

        //Set Tracking
        this.setTracking('applyRewardPoints');

        const jsonToSend = { id: id, value: value };

        // Post
        this.request(
            LC.global.routePaths.CHECKOUT_INTERNAL_OSC_RECALCULATE,
            'POST',
            { rewardPoints: jsonToSend },
            function (result) {
                // Check status
                this.moduleCalls('loadingIndicator', false);
                if (result.status.code != 200) {
                    return false; // TODO: Show error to user
                }
                if (result.data.data.rewardPoints.error.length) {
                    this.notify(result.data.data.rewardPoints.error, 'danger');
                } else {
                    // Refresh checkout
                    this.moduleCalls('refreshModule');
                    // Show discount message
                    this.showMessage(LC.global.languageSheet.oneStepCheckoutRewardPointsRedeemmed, 'success');
                }
            }.bind(this)
        );

    },

    // Connection methods
    // ---------------------------------------------------------------------------

    /**
     * Refresh Module.
     * Call initialize functions and loadingModule[false] when ends call
     * @memberOf LC.OneStepCheckout
     * @param  {string} key    Module name
     * @param  {Object} module Module object
     * @param {Boolean} force Reload module event without autorefresh
     */
    refreshModule: function (key, module, force) {
        // Ensure module exists
        if (!module || typeof module !== 'object') module = this.dataModules[key];

        // Show loading
        this.loadingIndicator(key, module, true);

        // Get url
        // transform camelCase to SNAKE_CASE
        var routeKey = key.replace(/\.?([A-Z]+)/g, (x, y) => "_" + y).replace(/^_/, "").toUpperCase();
        var url = '';
        if (LC.global.routePaths['CHECKOUT_INTERNAL_OSC_' + routeKey]) {
            url = LC.global.routePaths['CHECKOUT_INTERNAL_OSC_' + routeKey];
        } else if (LC.global.routePaths['BASKET_INTERNAL_' + routeKey]) {
            url = LC.global.routePaths['BASKET_INTERNAL_' + routeKey];
        }

        // Call load
        if (url.length && module.hasOwnProperty('el')) {
            module.el.load(
                url,
                module.params,
                function (data) {
                    // Hide loading
                    this.loadingIndicator(key, module, false);

                    // Initialize form (each module)
                    if (module.initializeMethod && $.isFunction(this[module.initializeMethod]))
                        this[module.initializeMethod](module);

                    // Clean module calls and trigger function when it removes the last
                    this.moduleCallDone('refreshModule', key, function () {
                        this.trigger('onLoad');
                    }.bind(this));
                }.bind(this)
            );
        } else if (module.initializeMethod && $.isFunction(this[module.initializeMethod])) {
            this[module.initializeMethod](module);
        } else {
            this.loadingIndicator(key, module, false);
        }
    },

    /**
     * Recalculate basket
     * @memberOf LC.OneStepCheckout
     * @param {object} module 
     * @param {bool} preventUserRefresh 
     * @param {function} callback 
     * @returns 
     */
    recalculateBasket: function (module, preventUserRefresh, callback) {
        // Prevent invalid or undefined functions
        preventUserRefresh = preventUserRefresh || false;
        callback = callback || function () { };
        if (typeof preventUserRefresh === 'function') {
            callback = preventUserRefresh;
            preventUserRefresh = false;
        }

        // Surrogate function trigger
        if (this.trigger('recalculateBasket')) {
            return;
        }

        // Before trigger
        this.trigger('recalculateBasketBefore');

        // Show loading
        this.moduleCalls('loadingIndicator', true);

        // Avoid multiple submit
        this.el.$form.find('button[type=submit]').attr('disabled', true);

        // Fill dataForm
        this.fillDataForm(module);

        // Avoid recalculate user when is a userForm who triggers the method
        this.dataForm.recalculateUser = !preventUserRefresh;

        this.tmpProducts = globalThis?.lcCommerceSession?.basket?.rows;

        this.trigger('initializePaymentsCallback');
        // Post
        this.request(
            LC.global.routePaths.CHECKOUT_INTERNAL_OSC_RECALCULATE,
            'POST',
            this.dataForm,
            function (result) {
                var response = result.response ? result.response : result;
                let items = response.data?.data?.basket.items;
                if (this.tmpProducts) {
                    LC.dataEvents.changeQuantityEvent(this.tmpProducts, items);
                    this.tmpProducts = [];
                }
                // Control user for verify it
                if (module && module.initializeMethod == 'initializeUserForm') {
                    //Internal callback
                    if (callback && typeof callback === 'function') callback(response);
                    // On error - Call notify TODO (??)
                    if (!response.success) {
                    }
                    if (response.user) {
                        //this.initializeLegalCheck();
                        if (!response.user.success) {
                            this.moduleCalls('loadingIndicator', false);
                            // Message "no selector or email check"
                            if (!preventUserRefresh) {
                                var message = response.user.message ? response.user.message : '__USER NOT VERIFIED__';
                                this.notify(message, 'danger');
                            }
                            return false;
                        }
                    }

                    let locationPath = '';
                    if (response.data?.data?.checkoutPath.final.search('//') >= 0) {
                        locationPath = window.location.protocol + "//" + window.location.host;
                    }
                    locationPath += window.location.pathname;
                    if (
                        response.data?.data?.checkoutPath.original != response.data?.data?.checkoutPath.final ||
                        (locationPath) != response.data?.data?.checkoutPath.final
                    ) {
                        window.location = response.data.data.checkoutPath.final;
                        return;
                    }
                }
                LC.dataEvents.reloadCustomize();
                // Refresh modules
                this.moduleCalls('refreshModule');

                // Actualize globalThis.lcCommerceData.folcsVersion to avoid refresh page when change tad
                if (response.data && globalThis?.lcCommerceData) {
                    globalThis.lcCommerceData.folcsVersion = response.data?.data.folcsVersion;
                }

                // Callback trigger
                this.trigger('recalculateBasketCallback');
            }.bind(this)
        );
    },

    /**
     * Clear basket
     * @memberOf LC.OneStepCheckout
     */
    clearBasket: function () {
        //Show loading
        this.moduleCalls('loadingIndicator', true);

        //Set Tracking
        this.setTracking('clearBasket');

        //Post
        $.post(
            LC.global.routePaths.CHECKOUT_INTERNAL_OSC_RECALCULATE,
            JSON.stringify({ clear: true }),
            function (data) {
                if (data.response && data.response.REDIRECT) window.location = data.response.REDIRECT;
            },
            'json'
        );
    },

    /**
     * Submit one step checkout form
     * @memberOf LC.OneStepCheckout
     * @return {void}
     */
    submit: function (ev) {
        ev.preventDefault();

        if (this.el.$form.find('.address-complete:visible').length != this.el.$form.find('.userFieldGroupCountry:visible').length) {
            var $result = this.el.$form.find('.userFieldGroupCountry:visible').not('.address-complete:visible');
            $result.addClass('address-incomplete');
            $([document.documentElement, document.body]).animate({
                scrollTop: $result.offset().top - 200
            }, 1000);
            return false;
        }

        //Validate form
        if (!this.el.$form.isValid(document.documentElement.lang || 'en', LC.validateFormConf)) {
            this.el.$form.find('button[type=submit]').attr('disabled', true);
            return false;
        }

        this.submitButton.prop('disabled', true);
        this.el.$form.preventSubmit = false;
        this.el.$form.activeButton = false;

        // Add hidden inputs
        $('<input />', { type: 'hidden', name: 'ACTION10', value: 1 }).appendTo(this.el.$form);
        $('<input />', { type: 'hidden', name: 'oneStepCheckout', value: 1 }).appendTo(this.el.$form);

        //Plugin Listener
        LC.resources.pluginListener('beforeSubmitEndOrder', ev, this, true);

        // Trigger
        this.trigger('beforeSubmit');

        // Tracking
        this.setTracking('endOrder');

        // Submit form
        if (this.userForm.el.$form.isValid() && this.el.$form.isValid() && !this.el.$form.preventSubmit) {
            if (!this.el.$form.preventSubmit) {
                if (this.dataForm.userForm && Object.keys(this.dataForm.userForm).length === 0) {
                    let arrDataUserForm = this.el.$form.find('.userForm *').serializeArray()
                    let arrAddress = ['locationList']
                    this.dataForm.userForm = this.getUserDataForm(arrDataUserForm, arrAddress, []);
                }
                // this.el.form.submit();
                $.post(
                    LC.global.routePaths.CHECKOUT_INTERNAL_NEXT_STEP,
                    { data: JSON.stringify($.extend(this.dataForm, { updateBasketRows: this.updateBasketRows, action: this.submitButton.val(), osc: true })) },
                    (function (response) {
                        if (response.status.code === 200) {
                            if (response.data.data.redirect.length) {
                                window.location = response.data.data.redirect;
                            }
                        } else {
                            this.submitButton.prop('disabled', false);
                            if (response.data?.response && response.data?.response?.message) {
                                this.notify(response.data.response.message, 'danger', true);
                            } else {
                                this.notify(LC.global.languageSheet.errorMessageGeneric, 'danger', true);
                            }
                        }
                    }).bind(this),
                    'json'
                );
            }
        }

        if (this.el.$form.activeButton) {
            this.submitButton.prop('disabled', false);
        }

        return;
    },

    /**
     * Fills dataForm Object with form fields values in this.dataForm
     * @memberOf LC.OneStepCheckout
     * @param  {Object} module Use it to discriminate userForm Module
     * @return {Object}        Returns dataForm.
     */
    fillDataForm: function (module) {

        // Clean dataForm
        this.dataForm = {};

        // Get form data
        const arrDataForm = this.el.$form.find(':not(.userForm *)').serializeArray(),
            arrDataUserForm = this.el.$form.find('.userForm *').serializeArray(),
            arrAddress = ['locationList'],
            noFillFields = [];

        // Fill with fields
        for (let i = 0; i < arrDataForm.length; i++) {
            let name = arrDataForm[i].name;

            if (this.dataForm[name] && !isNaN(this.dataForm[name]) && !isNaN(arrDataForm[i].value)) {
                this.dataForm[name] = parseInt(this.dataForm[name]) + parseInt(arrDataForm[i].value);
            } else {
                if (name.indexOf('quantity') === 0) {
                    name = name.replace('quantity', '');
                    if (name.startsWith("Grid")) {
                        let $el = this.el.$form.find(`[name="quantity${name}"]`);
                        this.dataForm[name] = {
                            type: $el.data('lc-row-type'),
                            quantity: arrDataForm[i].value,
                            options: $el.data('lc-row-options'),
                            id: $el.closest('[data-lc-grid-product-id]').data('lc-grid-product-id'),
                        };
                    } else {
                        this.dataForm[name] = {
                            type: $('[data-lc-hash="' + name + '"]').data('lc-type'),
                            quantity: arrDataForm[i].value
                        };
                    }
                } else {
                    this.dataForm[name] = arrDataForm[i].value;
                }
            }
        }

        if (this.shippingData && Object.keys(this.shippingData).length) {
            this.dataForm['shippingType'] = this.shippingData;
        }

        // FIXME: Possible conflict entre customtags de basket i customtags de user
        const inputCustomTags = this.el.$form.find('input[name^="customTags_"]').serializeArray();
        let customTags = [];
        for (let i = 0; i < inputCustomTags.length; i++) {
            customTags[inputCustomTags[i].name.replace("customTags_", "")] = inputCustomTags[i].value;
        }
        this.dataForm.customTags = customTags;
        this.dataForm.userForm = {}
        if (module && module.initializeMethod === 'initializeUserForm') {
            this.dataForm.userForm = this.getUserDataForm(arrDataUserForm, arrAddress, noFillFields);
        }
        if (module && module.initializeMethod === 'initializePayments') {
            this.dataForm.paymentSystem = module.data;
        }

        return this.dataForm;
    },

    /**
     * Get user data form
     * @memberOf LC.OneStepCheckout
     * @param {array} arrDataUserForm
     * @return {Object} Returns userDataForm.
     */
    getUserDataForm: function (arrDataUserForm, arrAddress, noFillFields) {
        let userDataForm = {};
        for (let i = 0; i < arrDataUserForm.length; i++) {
            const inNoFillFields = noFillFields.some(el => arrDataUserForm[i].name.includes(el));
            if (inNoFillFields) {
                continue;
            }
            const inArrAddress = arrAddress.some(el => arrDataUserForm[i].name.includes(el));
            if (arrDataUserForm[i].value.length || inArrAddress)
                userDataForm[arrDataUserForm[i].name] = arrDataUserForm[i].value;
        }
        return userDataForm;
    },

    /**
     * Call to server
     * @memberOf LC.OneStepCheckout
     * @param  {string}    url      Route to call
     * @param  {string}    method   GET or POST
     * @param  {Object}    data     Data to send
     * @param  {Boolean}   async    Set call type (default: true)
     * @param  {Function}  callback Function to call when finishes
     * @return {any}
     */
    request: function (url, method, data, async, callback) {
        if (typeof async == 'function') {
            callback = async;
            async = true;
        }
        $.ajax({
            url: url,
            type: method,
            async: async,
            dataType: 'json',
            data: { data: JSON.stringify(data) }
        }).done((result, status, xhr) => {
            this.responseActions(result);
        }).fail(function () {
            // Error. TODO: notify!
        }).always(callback.bind(this));
    },

    /**
     * Do action with error code received
     * @memberOf LC.OneStepCheckout
     * @param  {Object} response Response object from ajax call
     * @return {void}
     */
    responseActions: function (response) {
        this.validateAddressBook(response);

        // Registered email
        if (this.hasOwnProperty('inputEmail')) {
            this.inputEmail = this.getInputEmail();

            if (response?.data?.data?.user?.userExists === true) {
                const formError =
                    `<span class="help-block form-error">${this.userEmailExistsError}</span>`;

                $(this.inputEmail)
                    .data('userEmailExistsOsc', true)
                    .closest('.form-group')
                    .addClass('has-error')
                    .removeClass('has-success')
                    .find('span.form-error')
                    .remove()
                    .end()
                    .append(formError);

                if (document.getElementById('userLogin')) {
                    this.loginEmailValue = this.emailInputValue || '';
                }
            } else if (response?.data?.data?.user?.userExists === false) {
                $(this.inputEmail)
                    .data('userEmailExistsOsc', false)
                    .closest('.form-group')
                    .addClass('has-success')
                    .removeClass('has-error')
                    .find('span.form-error')
                    .remove();
            }
        }
    },

    validateAddressBook: function (response) {
        // Exists shipping address validation
        this.userForm.el.$form
            .find('.useShippingAddressGroup')
            .removeClass('has-error');

        let $useShippingAddress = this.userForm.el.$form.find('input[name="useShippingAddress"]'),
            $shippingAddress = this.userForm.el.$form.find('input[name="shippingAddress"]');
        if ($useShippingAddress.length && $useShippingAddress.prop('checked') && $shippingAddress.length == 0) {
            this.userForm.el.$form
                .find('.useShippingAddressGroup')
                .addClass('has-error');

            this.notify(LC.global.languageSheet.notAvailableAddress, 'danger', true);
        }
        this.inputAddress = this.getInputAddress();
        if (response?.data?.data?.user?.validAddress?.isValid === false) {
            this.notify(this.warningInvalidAddress, 'danger', true);
            $(this.inputAddress)
                .data('userAddressValidationFailedOsc', true)
                .data('userAddressValidationMessagesOsc', this.warningInvalidAddress)
                .closest('.form-group')
                .addClass('has-error')
                .removeClass('has-success')
                .find('span.form-error')
                .remove()
                .end()
                .append(`<span class="help-block form-error">${this.warningInvalidAddress}</span>`);
        } else if (response?.data?.data?.user?.validAddress?.isValid === true) {
            $(this.inputAddress)
                .data('userAddressValidationFailedOsc', false)
                .closest('.form-group')
                .addClass('has-success')
                .removeClass('has-error')
                .find('span.form-error')
                .remove();
        }
    },

    // Utils
    /**
     * Restore user data-validation.
     * It is necessary because localize.js or others forces validation init
     * @return {Void}
     */
    restoreUserFormValidation: function () {
        // Get element and call off method
        this.userForm.el.$form.find('#userEmailField').off('blur');
        this.userForm.el.$form.find('.blockAddressBook').removeClass('loading');
    },

    /**
     * Validate values
     * @memberOf LC.OneStepCheckout
     * @param  {String}  type email,number...
     * @param  {Any}  str
     * @return {Boolean}
     */
    isValid: function (type, value) {
        if (type == 'number') return !isNaN(value);
        if (type == 'email') return $.formUtils.validators.validate_email.validatorFunction(value);
        else return false;
    },

    /**
     * Show messages & notifications
     * @memberOf LC.OneStepCheckout
     * @param  {String} message    Message to show
     * @param  {String} type       Type of notify: danger,warning,info,success
     * @param  {Bolean} notifyMode If it is passed force manually notify mode
     * @return {Void}
     */
    notify: function (message, type, notifyMode) {
        type = type || 'danger';

        if (typeof notifyMode !== 'boolean') notifyMode = this.notifyMode;

        if (notifyMode) LC.notify(message, { type: type });
        else this.el.$message.html(message).addClass('alert alert-' + type);
    },

    /**
     * This method check all required inputs in userForm ars filled and returns
     * Also, when all required inputs are filled, call a function if it is passed.
     * @param  {Function} callback Call this function when inputs are filled
     * @param  {Object}   event    To get the triggerer
     * @return {Boolean}           Result
     */
    validateFilledUserForm: function (callback, event) {
        // Check arguments
        if (typeof callback !== 'function') callback = function () { };

        // Set variables
        const targetName = event ? event.target.name.toLowerCase() : '';
        // Flag that determines if changed input belongs to form
        let inputBelongsToUserForm = false;

        // Get input data
        this.fillDataForm(this.dataModules.userForm);

        // User form
        // Loop every item in user form fields
        for (let i = 0; i < this.userForm.el.$userFieldElements.length; i++) {
            let item = this.userForm.el.$userFieldElements[i];
            item.name = $(item).attr('name') || '';

            // Check unrequired items and auto trigger inputs
            let inArray = ['country', 'email'].some(el => item.name.includes(el));
            if (
                !item.hasOwnProperty('required') ||
                !item.hasOwnProperty('name') ||
                !item.required ||
                inArray
            ) {
                // When we change an unrequired input it is not necessary recalculate basket
                if (targetName === item.name.toLowerCase()) {
                    return false;
                }
                // Otherwise, jump to next iteration
                continue;
            }

            // If required input is not filled, we do not call to recalculate
            if (!this.dataForm.userForm.hasOwnProperty(item.name)) {
                // Ignore state, city and zip because in some cases are not required
                let inArray = ['locationList'].some(el => item.name.includes(el));
                if (inArray) {
                    return false;
                }
            } else if (!this.dataForm.userForm[item.name].length) {
                return false;
            }

            // Does input belong to userForm?
            if (targetName === item.name.toLowerCase()) {
                inputBelongsToUserForm = true;
            }
        }

        // User shipping form - Only if it is requested (via checkbox)
        // When addressBook is active, shippingFields property is undefined, we
        // can use it to determine userForm is addressBook or not
        if (
            this.userForm.el.hasOwnProperty('$shippingFieldElements') &&
            typeof this.userForm.el.$shippingFieldElements !== 'undefined' &&
            this.userForm.el.$shippingFieldElements.length &&
            this.userForm.el.hasOwnProperty('$useShippingAddress') &&
            this.dataForm.userForm.useShippingAddress
        ) {
            // Loop every item in user shipping form fields
            for (let i = 0; i < this.userForm.el.$shippingFieldElements[1].length; i++) {
                item = this.userForm.el.$shippingFieldElements[1][i];

                // Check unrequired items and auto trigger inputs
                let inArray = ['country', 'locationList'].some(el => item.name.includes(el));
                if (
                    !item.hasOwnProperty('required') ||
                    !item.hasOwnProperty('name') ||
                    !item.required ||
                    inArray
                ) {
                    // When we change an unrequired input it is not necessary recalculate basket
                    if (targetName === item.name.toLowerCase()) {
                        return false;
                    }
                    // Otherwise, jump to next iteration
                    continue;
                }

                // If required input is not filled, we do not call to recalculate
                if (!this.dataForm.userForm.hasOwnProperty(item.name)) {
                    // Ignore state, city and zip because in some cases are not required
                    let inArray = ['locationList'].some(el => item.name.includes(el));
                    if (!inArray) {
                        return false;
                    }
                } else if (!this.dataForm.userForm[item.name].length) {
                    return false;
                }

                // Does input belong to userForm?
                if (targetName === item.name.toLowerCase()) {
                    inputBelongsToUserForm = true;
                }
            }
        }

        // Exit when input changed does not belong to userForm
        if (!inputBelongsToUserForm) {
            return false;
        }
        if (typeof callback === 'function') {
            callback();
        }

        return true;
    },
});
