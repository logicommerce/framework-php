/*!
 * LC Library v2.0.0 | (C) 2020 Logicommerce | License
 */

'use strict';

/**
 * LC JavaScript Library
 *
 * @file
 *
 * @version v2.0.0
 * @author Logicommerce https://www.logicommerce.com
 * @requires jQuery.js
 * @date 2020-04-15
 */

/**
 * LC Main
 * @namespace LC
 */
var LC = (window.LC = {});

/**
 * LC Main global (DEPRECATED only for transition)
 */
if (logicommerceGlobal !== undefined) {
    LC.global = {
        session: logicommerceGlobal.session,
        settings: logicommerceGlobal.settings,
        avoidTrackings: logicommerceGlobal.avoidTrackings,
        languageSheet: logicommerceGlobal.languageSheet,
        routePaths: logicommerceGlobal.routePaths,
        countries: logicommerceGlobal.countries
    };
    if (LC.global.session.user == undefined) {
        LC.global.session.user = { userAdditionalInformation: { simulatedUser: false } };
    }
} else {
    LC.global = {
        session: session,
        settings: settings,
        avoidTrackings: avoidTrackings,
        languageSheet: languageSheet,
        routePaths: routePaths,
        countries: countries
    };
}

/**
 * LC Version
 * @property {string} VERSION
 * @memberOf LC
 */
LC.VERSION = '2.0.0';

/**
 * LC Main Config (overwritable)
 * @property {object} config
 * @memberOf LC
 */
LC.config = {
    /**
     * Show modal after succes buy
     * @property {bool}
     */
    showModalBasket: true,
    /**
     * Default notify plugin settings (lc.plugins.js)
     * @property {object}
     */
    notify: {
        type: 'notes',
        speed: 500,
        delay: 3000,
        easing: 'swing',
        effect: 'fade',
        removeIcon: '<svg class="icon"><use xlink:href="#icon-close"></use></svg>',
        successIcon: '',
        dangerIcon: '',
    },
    /**
     * Show form messages with notify plugin
     * @property {bool}
     */
    notifyMode: true,
    forceLoad: false,
    avt: false,
    avoid: false,

    /**
     * Reloads page after success return request
     * @see LC.ReturnRequestForm
     * @property {bool}
     */
    orderReturnRequestReload: true,

    /**
     * Adds products filter input check exclusion between customTags of same customTags group
     * Prevent multiple check selection on custom tag groups
     * @see LC.ProductsFilter
     * @property {bool}
     */
    productsFilterCustomTagGroupsExclusion: true,
};

/**
 * LC Validate Form Configuration
 */
LC.validateFormConf = {
    borderColorOnError: '',
    addValidClassOnAll: true,
    scrollToTopOnError: false,
    onError: function ($form) {
        const form = $form[0],
            errorEl = form.querySelector('.has-error');

        window.scrollTo(0, LC.events.scrollTop);

        if (errorEl) {
            const intoHeader = form.closest('header') !== null,
                isBuyForm = form.matches('[data-lc-form="buyProductForm"]');

            if (!intoHeader && !isBuyForm) {
                errorEl.scrollIntoView({ block: "center", behavior: 'smooth' });
            }
        }
    },
};

/**
 * LC Require 
 * Utility for include files
 */
LC.require = {
    /**
     * LC.require.css
     * @param  {string} source Path to source
     * @return {void}
     */
    css: function (source) {
        var cssfile = document.createElement('link');

        cssfile.setAttribute('rel', 'stylesheet');
        cssfile.setAttribute('type', 'text/css');
        cssfile.setAttribute('href', source);

        if (typeof cssfile != 'undefined') document.getElementsByTagName('head')[0].appendChild(cssfile);
    },

    /**
     * LC.require.js
     * @param  {string} source Path to source
     * @param  {function} load Function callback after load script
     * @return {void}
     */
    js: function (source, load) {
        // Prepare load
        if (!load) load = function () { };

        // Call
        $.getScript(source, load);
    },

    /**
     * LC.require.cachedJs
     * @param  {string} ul Path to source
     * @param  {object} options jquery ajax options
     * @return {void}
     */
    cachedJs: function (url, options) {
        // Allow user to set any option except for dataType, cache, and url
        options = $.extend(options || {}, {
            dataType: 'script',
            cache: true,
            url: url,
        });

        // Use $.ajax() since it is more flexible than $.getScript
        // Return the jqXHR object so we can chain callbacks
        return jQuery.ajax(options);
    },
};

/**
 * @method LC.extend
 * @memberOf LC
 *
 * @description
 * This function extends the mixin's functions into our "Class"
 *
 * Clona un element i el retorna amb les propietats que s'han volgut modificar.
 * Ideal per a crear objectes Tipus nous
 *
 * @param  {object} protoProps   Prototype data
 * @param  {object} staticProps  Static properties
 * @return {object} returns      variable
 */
LC.extend = function (protoProps, staticProps) {
    var parent = this;
    var child;

    if (protoProps && protoProps.hasOwnProperty('constructor')) child = protoProps.constructor;
    else
        child = function () {
            return parent.apply(this, arguments);
        };

    $.extend(child, parent);
    var Surrogate = function () {
        this.constructor = child;
    };
    Surrogate.prototype = parent.prototype;
    child.prototype = new Surrogate();
    if (protoProps) $.extend(child.prototype, protoProps, staticProps);
    child.__super__ = parent.prototype;

    return child;
};

/**
 * Unique id generator object
 */
LC.uniqueId = {
    /**
     * Save uniqueId prefixes
     */
    objPrefixes: {},

    /**
     * LC.uniqueId.get Generate unique integer id (valid only on navigator "request")
     * @param  {number} prefix Prefix name for id
     * @return {string}        Returns uniqueId
     */
    get: function (prefix) {
        if (prefix in LC.uniqueId.objPrefixes) LC.uniqueId.objPrefixes[prefix]++;
        else LC.uniqueId.objPrefixes[prefix] = 1;

        var id = LC.uniqueId.objPrefixes[prefix] + '';
        return prefix ? prefix + id : id;
    }
};

/**
 * Execute object path function
 * @example LC.carryMethod("path.to.function", window);
 * @param {string} path     Object path to function
 * @param {object} carry    Scope path function variable
 */
LC.carryMethod = function (path, carry) {
    if (!path) return false;

    carry = carry || window;
    var arrCall = path.split('.');

    for (var i = 0; i < arrCall.length; i++) {
        if (!carry[arrCall[i]]) return false;

        if (typeof carry[arrCall[i]] === 'function') return carry[arrCall[i]];

        carry = carry[arrCall[i]];
    }
    return false;
};

/**
 * @property {array} LC.pluginEvents
 * Global plugin events array
 */
LC.pluginEvents = [];

/**
 * @property {object} LC.resources It has main prototyping functions
 * @memberOf LC
 */
LC.resources = {
    initialize: function () { },

    /**
     * Return parent object (pseudo super)
     * @return {Object}
     */
    parent: function () {
        return this.constructor.__super__;
    },

    /**
     * Super Call
     * @param {string} method Name of method to call
     * @param {any} "noname" Others params are accepted
     * @return {void} Call method directly
     */
    superForm: function (method) {
        // Prevent call this method without arguments
        if (!method) return;

        // Get all arguments without the first (method)
        var args = Array.prototype.slice.call(arguments, 1);

        // Get main "class" (super constructor)
        var obj = this.constructor.__super__;

        // Invoke method if exists and is a function
        if (obj && $.isFunction(obj[method])) obj[method].apply(this, args);
    },

    /**
     * Trigger Events
     * @param  {String} triggerName Function added on "addEvent" or "addTrigger"
     * @param  {"MULTIPLE"} *       Multiple params... it are not required
     * @return {Boolean}
     */
    trigger: function (triggerName) {
        var result = false;

        // Get function
        var functionTrigger = this['custom_' + triggerName];

        // Arguments
        var args = Array.prototype.slice.call(arguments, 1);

        // Call function
        if (this && $.isFunction(functionTrigger)) {
            result = true;
            functionTrigger.apply(this, args);
        }

        return result;
    },

    /**
     * Add Plugin Listener
     * @param  {string}    listener Listener name
     * @param  {function}  method   Function to call
     * @param  {boolean}   force    Force load
     * @return {boolean}
     */
    addPluginListener: function (listener, method, force) {
        var allowedListeners = [
            'onUserLogin',
            'onUserSignUp',
            'onAddProduct',
            'onRemoveProduct',
            'onProductClick',
            'onAddWishList',
            'onAddShoppingList',
            'setPaymentSystem',
            'setShippingSection',
            'deleteRow',
            'beforeSubmitEndOrder',
            'initializePaymentsBefore',
            'initializePaymentsCallback',
            'beforeExpressCheckoutRedirect'
        ];
        if (allowedListeners.indexOf(listener) < 0) return false;

        if (!$.isFunction(method)) return false;

        if (LC.pluginEvents.indexOf(listener) < 0) {
            LC.pluginEvents.push(listener);
            LC.pluginEvents[listener] = [];
        }

        LC.config.forceLoad = force || false;
        LC.config.avt = LC.global.avoidTrackings || false;
        LC.config.avoid = LC.config.forceLoad ? false : LC.config.avt;
        if (!LC.config.avoid) LC.pluginEvents[listener].push(method);
        return true;
    },

    /**
     * Plugin Listener
     * @param  {string} listener Listener name
     */
    pluginListener: function (listener) {
        var allowedListeners = [
            'onUserLogin',
            'onUserSignUp',
            'onAddProduct',
            'onRemoveProduct',
            'onProductClick',
            'onAddWishList',
            'onAddShoppingList',
            'setPaymentSystem',
            'setShippingSection',
            'deleteRow',
            'beforeSubmitEndOrder',
            'initializePaymentsBefore',
            'initializePaymentsCallback',
            'beforeExpressCheckoutRedirect'
        ];
        if (allowedListeners.indexOf(listener) < 0) return false;

        if (!LC.pluginEvents[listener] || LC.pluginEvents.length == 0) return false;

        var args = Array.prototype.slice.call(arguments, 1);

        LC.pluginEvents[listener].forEach(function (el, index) {
            if ($.isFunction(el)) el.apply(this, args);
        });
    },
};

/**
 * LC.Form
 * @class LC.Form
 * @memberOf LC
 *
 * @param {object} form DOM form
 */
LC.Form = function (form) {

    // Main vars
    this.name = this.name || 'form';
    this.initCaptchaToken = false;
    this.inputCaptchaToken = null;

    // DOM Basic Elements
    this.el = {
        form: form,
        $form: $(form),
    };

    if (this.el.form) {
        // Get message container
        this.el.$message = this.el.$form.find('.form-message');

        if (!this.el.$message.length)
            this.el.$message = $('<div />', {
                class: 'form-message',
            }).prependTo(this.el.$form);

        // Add Submit event
        if (this.submit) this.el.$form.on('submit', this.submit.bind(this));

        this.inputCaptchaToken = this.el.$form.find('[name="captchaToken"]');

    }

    // Initialize
    this.initialize.apply(this, [form]);
};

// Extend Public Methods on LC.Form
$.extend(true, LC.Form.prototype, LC.resources, {

    /**
     * LC.Form.setCaptchaToken
     * @class LC.Form
     * @memberOf LC
     *
     * @param {object} event submit event object
     */
    setCaptchaToken: function (event) {
        var form = this;
        function setCaptchaTokenCallback(token) {
            form.inputCaptchaToken.val(token);
            form.submit(event);
        }
        if (!this.initCaptchaToken && this.inputCaptchaToken.length) {
            this.inputCaptchaToken.val('');
            setCaptchaToken(setCaptchaTokenCallback);
            this.initCaptchaToken = true;
            return true;
        }
        this.initCaptchaToken = false;
        return false;
    },

    /**
     * LC.Form.submit
     * @class LC.Form
     * @memberOf LC
     *
     * @param {object} event submit event object
     */
    submit: function (event) {
        // Get method, continue default event
        if (this.el.form.method.toLowerCase() == 'get') return;

        event.preventDefault();

        // Validate form
        if (!this.el.$form.isValid()) return false;

        if (this.setCaptchaToken(event)) return;

        if (this.el.$form.data('lcFormdata')) {
            this.postFormData(event);
            return;
        }

        // Get form data
        var arrDataForm = this.el.$form.serializeArray();

        // Fills dataForm
        this.dataForm = {};
        for (var i = 0; i < arrDataForm.length; i++) {
            if (!(arrDataForm[i].name in this.dataForm)) this.dataForm[arrDataForm[i].name] = [];

            this.dataForm[arrDataForm[i].name].push(arrDataForm[i].value);
        }

        for (var i in this.dataForm) this.dataForm[i] = this.dataForm[i].join();

        // Avoid mutiple submit
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', true);

        // Post
        if (this.el.form.method.toLowerCase() == 'post') {
            this.trigger('submitBeforeSend', event);

            $.post(
                this.el.form.action, {
                data: JSON.stringify(this.dataForm),
            },
                this.onReceive.bind(this),
                'json'
            )
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
        }
    },

    onFail: function (error) {
        // Allow submit
        this.el.$form.find('button[type=submit], input[type=submit]').attr('disabled', false);

        // Callback
        if (this.callback && typeof this.callback === 'function') this.callback(error.responseJSON);
    },

    onReceive: function (data) {
        // Allow submit
        this.el.$form.find('button[type=submit], input[type=submit]').attr('disabled', false);

        // Callback
        if (this.callback && typeof this.callback === 'function') this.callback(data);
    },

    onComplete: function (data) {
        // Allow submit
        // this.el.$form.find('button[type=submit], input[type=submit]').attr('disabled', false);
    },

    callback: function (data) {
        var message = LC.global.languageSheet.error,
            success = 0;

        if (typeof data !== 'undefined') {
            if (data.data) data = data.data;
            if (data.response) {
                message = data.response.message;
                success = data.response.success ? data.response.success : 0;
            }
        }

        this.el.$message.text(message);

        if (success) {
            this.el.$message.removeClass('alert-danger').addClass('alert alert-success');
        } else {
            this.el.$message.removeClass('alert-success').addClass('alert alert-danger');
        }

        // Callback trigger
        this.trigger('callback', data);
    },

    showMessage: function (message, type, notifyMode) {
        if (!type) type = 'danger';
        if (typeof notifyMode !== 'boolean') notifyMode = LC.config.notifyMode;
        if (notifyMode) LC.notify(message, { type: type });
        else
            this.el.$message
                .html(message)
                .removeClass('alert-danger alert-success alert-info')
                .addClass('alert alert-' + type);
    },

    postFormData: function (event) {
        event.preventDefault();

        // Get form data
        var arrDataForm = this.el.$form.serializeArray();

        // Create FormData
        var formData = new FormData(this.el.$form[0]);
        for (var i = 0; i < arrDataForm.length; i++) {
            formData.append(arrDataForm[i].name, arrDataForm[i].value);
        }

        // Check input files length and accept formats.
        var isValidFormInput = true;
        this.el.$form.find('input:file').each(
            function (index, el) {
                if (el.files.length > 0) {
                    var suffixes = $(el).data('lcAccept');
                    joinedSuffixes = suffixes.split(',').join('|');
                    var re = new RegExp('.*.(' + joinedSuffixes + ')', 'i');
                    if (!re.test(el.value)) {
                        isValidFormInput = false;
                        this.showMessage(LC.global.languageSheet.validExtensions + ' ' + suffixes.split(',').join(', '));
                    }
                    maxFileSize = $(el).data('lcMaxSize');
                    if (el.files[0].size / (1024 * 1024) > maxFileSize) {
                        isValidFormInput = false;
                        this.showMessage(LC.global.languageSheet.validSize + ' ' + maxFileSize + 'Mb');
                    }
                    formData.append(el.name, el.files[0], el.value);
                }
            }.bind(this)
        );

        if (!isValidFormInput) return;

        var callback = this.onReceive.bind(this);

        $.ajax({
            url: this.el.form.action,
            data: formData,
            async: true,
            mimeType: 'multipart/form-data',
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (data) {
                callback($.parseJSON(data));
            },
        });
    },
});

/**
 * Extend LC Form for create "superClass"
 */
LC.Form.extend = LC.extend;

/**
 * @method  LC.Form.addEvent
 * @memberOf LC.Form
 * @description Add Methods to Form
 *
 * @param {string} name Name of method
 * @param {function()} method Function
 * @return {boolean}
 */
LC.Form.addEvent = function (name, event) {
    if (typeof name !== 'string' || typeof event !== 'function') return false;
    this.prototype['custom_' + name] = event;
    return true;
};

/**
 * UserForm Resources
 * It has userForm prototyping functions for userForm & userAddressBookForm
 * @property {object} LC.userFormResources
 * @memberOf LC
 */
LC.userFormResources = {
    /**
     * TODO
     * Prepare country fields
     * @memberOf LC.UserForm
     * @param {number} state     1: active 0:hidden
     * @param {string} fieldName Name of field
     */
    setCountryFormFields: function (state, fieldName) {
        // Check arguments
        if (!fieldName) return;
        if (!state) state = 0; // state 1: active - 0: hidden

        // Ensures prefix exists
        this.getPrefix();

        if (this.countryUserFields) {
            this.countryUserFields[fieldName].state = state;

            if (this.countryUserFields[fieldName].formFields) {
                var countryUserFields = this.countryUserFields[fieldName].formFields;
                for (var i = 0; i < countryUserFields.length; i++) {
                    var fieldContainer = F('#' + this.prefix + 'Field' + countryUserFields[i] + 'Container');
                    if (fieldContainer) fieldContainer.style.display = state ? 'block' : 'none';
                }
            }
        }
    },

    /**
     * TODO
     * setValidationData
     * @param {object} $formField jQuery DOM Element
     * @param {boolean} required  set required field
     */
    setValidationData: function ($formField, required) {
        var i, dataValidation, arrElements, arrValidation;

        dataValidation = $formField.data('validation') || '';
        arrElements = ['email', 'phone', 'vat', 'idcard'];
        arrValidation = required ? ['required'] : [];

        for (i = 0; i < arrElements.length; i++)
            if (dataValidation.toLowerCase().indexOf(arrElements[i]) >= 0) arrValidation.push(arrElements[i]);

        return arrValidation.join(',');
    },

    /**
     * TODO
     * Select Postal Code Callback
     * @param  {String}   type     userInfo | shippingAddress
     * @param  {Function} callback
     * @return {void}
     */
    selectPostalCodeCallback: function (type, callback) {
        if (!$.isFunction(callback)) return;

        // Type: userInfo | shippingAddress | addressBook
        if (type === 'userInfo') localizeEvents.selectPostalCode = callback.bind(this);
        else if (type === 'shippingAddress') localizeEvents.selectShippingPostalCode = callback.bind(this);
        else if (type === 'addressBook') localizeEvents.selectAddressBookPostalCode = callback.bind(this);
    },
};

/**
 * AddressBookForm
 * It has addressBookForm prototyping functions for billingAddressBookForm and shippingAddressBookForm
 * @memberOf LC
 */
LC.addressBookForm = {

    /**
     * Selected userType field form value
     * @type {string}
     */
    userType: null,

    /**
     * Initialize form method
     * @memberOf LC.addressBookForm
     * @return {void}
     */
    initializeBook: function () {
        // Init associatet form modal
        this.el.$modal = $('#' + this.prefix + 'AddressBookModal');

        this.initModalOnShow();
        this.el.$form.find('.addressUserCountryField').each(function (index, el) {
            changeCountryFields.bind(el)(LC.global.session.countryId, true);
        });
        this.changeUserTypeEvent();
        this.selectAddressEvents();
        this.deleteAddressEvents($('.' + this.prefix + 'Address [data-lc-action="deleteAddressBook"]'));

        this.type = this.el.$form.find('[name="type"]').val();
        this.module = this.el.$form.find('[name="module"]').val();
    },

    /**
     * Initialize form method
     * @memberOf LC.addressBookForm
     * @return {void}
     */
    initializeForm: function () {
        var data = $.parseJSON(this.el.$form.attr('data-lc'));
        this.prefix = this.el.$form.attr('data-lc-address');
        this.module = this.el.$form.find('[name="module"]').val();
        this.fillFormData(data);
        this.changeUserTypeEvent();
        this.selectAddressEvents();
    },

    /**
     * Init address form when modal start open
     * @memberOf LC.addressBookForm
     * @see LC.addressBookForm.actionEditAddress
     * @see LC.addressBookForm.actionAddAddress
     */
    initModalOnShow: function () {
        var self = this;
        self.el.$modal.on('show.bs.modal', function (event) {
            self.el.$form.find('.validAddress').remove();
            self.el.$form.find('.addressUserCountryField').each((index, el) => {
                changeCountryFields.bind($(el))(LC.global.session.countryId, true);
            });
            var $button = $(event.relatedTarget);
            if ($button.data('lc-action') == 'editAddressBook') {
                self.actionEditAddress($button);
            } else if ($button.data('lc-action') == 'addAddressBook') {
                self.actionAddAddress($button);
            }
        });
    },

    /**
     * Init address form on add new address [data-lc-action="addAddressBook"] action
     * @memberOf LC.addressBookForm
     * @see LC.addressBookForm.initModalOnShow
     * @see LC.addressBookForm.fillFormData
     
     * @param {object} $trigger jQuery object relatedTarget from show modal event
     */
    actionAddAddress: function ($trigger) {
        var data = $trigger.data('lc-data');
        this.el.$form[0].reset();
        this.el.$form.find('.formField').not('[type="radio"]').val('');
        this.el.$form.find('[name="mode"]').val('add');
        this.el.$form.find('[name="id"]').val('');
        this.el.$form.find('[name="accountId"]').val(data.accountId);
        this.el.$form.selectMode = data.selectMode;
        this.fillFormData(data);
        $(this.el.$form.find('.userFieldGroupCountry')).each(function (index, el) {
            resetCountrySelector($(el));
        });
    },

    /**
     * Init address form on edit address [data-lc-action="editAddressBook"] action
     * @memberOf LC.addressBookForm
     * @see LC.addressBookForm.initModalOnShow
     * @see LC.addressBookForm.fillFormData
     
     * @param {object} $trigger jQuery object relatedTarget from show modal event
     */
    actionEditAddress: function ($trigger) {
        var data = $.parseJSON($trigger.attr('data-lc-data'));
        this.el.$form[0].reset();
        this.el.$form.find('.formField').not('[type="radio"]').val('');
        this.el.$form.find('[name="mode"]').val('update');
        this.el.$form.find('[name="id"]').val(data.id);
        $(this.el.$form.find('.userFieldGroupCountry')).each(function (index, el) {
            resetCountrySelector($(el));
        });
        this.fillFormData(data);
    },

    /**
     * On change and update userType tabs event, update property data
     * @memberOf LC.addressBookForm
     */
    changeUserTypeEvent: function () {
        var self = this;
        // Change userType
        // this.el.$form.find('.userTypeNavTabs [data-toggle="tab"]').on('shown.bs.tab', function(event) {
        this.el.$form.find('.userTypeNavTabs [data-toggle="tab"], .userTypeNavTabs [data-bs-toggle="tab"]').on('shown.bs.tab', function (event) {

            // Update attr and data, for front and back
            self.el.$form.attr('data-lc-user-type', $(event.target).data('lc-user-type'));
            self.el.$form.data('lc-user-type', $(event.target).data('lc-user-type'));

            var $tabPane = $($(event.target).attr('href'));
            self.userType = self.el.$form.data('lc-user-type');
            self.el.$userFieldElements = $tabPane.find('.formField');

            self.setUserType();
        });
    },

    /**
     * Set userType property and enable/disable other userType unused fields
     * @memberOf LC.addressBookForm
     */
    setUserType: function () {
        if (this.module == undefined) {
            this.el.$form.find('[name*="userType"]').val(this.userType);
            // Disable all unused inputs
            this.el.$form.find('.formField').not(this.el.$userFieldElements)
                .prop('disabled', true);

            // Disable unused location hiddens
            this.el.$form.find('.locationField').not('[name^="' + this.userType + '"]').filter('input[type="hidden"]')
                .prop('disabled', true);

            // Enable used userType location hiddens
            this.el.$form.find('.locationField[name^="' + this.userType + '"]').filter('input[type="hidden"]')
                .prop('disabled', false);

            // Enable selected userType inputs
            this.el.$userFieldElements.prop('disabled', function () {
                return $(this).data('lc-disabled');
            });
        }
    },

    /**
     * Select address global events listeners, on select address action buttons.
     * @memberOf LC.addressBookForm
     * @see LC.modalCallbacks.deleteAddressBookConfirmCallback
     */
    selectAddressEvents: function () {
        var self = this;
        // On click select button, trigger select address event
        $(document)
            .on('click', '.' + self.prefix + 'Address .addressBookAction.addressSelect', function () {
                var data = $(this).data('lc-data');
                self.selectAddressBook(data.id, data.type, data.action);
            });
    },

    deleteAddressEvents: function ($button) {
        $button.box({
            uid: 'deleteAddressBookConfirm',
            showFooter: false,
            source: `
                <div class="titleDeleteAddressConfirm">${LC.global.languageSheet.deleteAddressBookConfirmTitle}</div>
                <div class="textDeleteAddressConfirm">${LC.global.languageSheet.deleteAddressBookConfirmText}</div>
                <div class="deleteAddressButtons">
                    <button type="button" class="${BTN_SECONDARY_CLASS} deleteAddressButton deleteAddressButtonDismiss" data-dismiss="modal" data-bs-dismiss="modal">${LC.global.languageSheet.cancel}</button>
                    <button type="button" class="${BTN_DANGER_CLASS} deleteAddressButton deleteAddressButtonConfirm">${LC.global.languageSheet.delete}</button>
                </div>`,
            type: 'html',
            showClose: true,
            size: 'small',
            callback: 'deleteAddressBookConfirmCallback',
        });
    },

    /**
     * Only update an address like set default actions, for example "set default" click button event.
     * @memberOf LC.addressBookForm
     * @see LC.addressBookForm.selectAddressEvents
     *
     * @param {number} addressId Id of address
     * @param {string} type 'billing'|'shipping'
     * @param {string} action 'default'|'select'
     */
    selectAddressBook: function (addressId, type, action) {
        var actionPath = LC.global.routePaths.CHECKOUT_INTERNAL_SELECT_ADDRESS_BOOK;
        if (action === 'set_default_address') {
            actionPath = LC.global.routePaths.USER_INTERNAL_SET_ADDRESS_BOOK;
        }
        if (LC.global.session.login) {
            $.ajax(
                {
                    type: 'POST',
                    url: actionPath,
                    data: {
                        data: JSON.stringify({
                            id: addressId,
                            type: type,
                            action: action,
                        })
                    },
                    success: (response) => {
                        if (response.data.response.success !== 0) {
                            this.selectAddressBookCallback(response);
                        }
                        if (this.trigger) {
                            this.trigger('selectAddressBookCallback', response);
                        }
                    },
                    async: false,
                    dataType: 'json'
                }
            );
        }
    },

    /**
     * Update selected address and non select addresses group (billing or shipping groups). 
     * @memberOf LC.addressBookForm
     * @see LC.addressBookForm.selectAddressEvents
     * @see LC.addressBookForm.callback
     *
     * @param {object} response Ajax response object
     */
    selectAddressBookCallback: function (response) {
        var data = response.data.data;
        var $address = $('#addressBookContainer_' + data.id),
            $allGroupAddresses = $('.' + data.type.toLowerCase() + 'Address'),
            $radio = $address.find('[name="' + data.type.toLowerCase() + 'Address"]');

        // Unselect other group addresses
        $allGroupAddresses
            .removeClass('addressBookActive')
            .find('.addressSelect, .deleteAddressAction')
            .removeClass('addressBookHidden');

        $allGroupAddresses
            .find('.default')
            .addClass('addressBookHidden');

        // Check last edited
        $radio.prop('checked', true);

        // Hide remove and select address actions to selected address
        $address
            .addClass('addressBookActive')
            .find('.addressSelect, .deleteAddressAction').addClass('addressBookHidden');

        $address.find('.default').removeClass('addressBookHidden')

        // Update this buttons data json
        var $thisDataButtons = $address.find('[data-lc-action][data-lc-data]');
        $thisDataButtons.data('lc-data', data);

        // Update all rest of buttons data json
        $allGroupAddresses.find('[data-lc-action][data-lc-data]').not($thisDataButtons).each(function (index, el) {
            $(el).data('lc-data').defaultAddress = false;
        });
    },

    /**
     * Override Form class callback method. Edit DOM on success or show error message.
     * @memberOf LC.addressBookForm
     * @see LC.Form (lc.core.js)
     * @see LC.addressBookForm.addNewAddressBlock
     * @see LC.addressBookForm.selectAddressBookCallback
     *
     * @param {object} response Ajax response object
     */
    callback: function (response) {
        // Before trigger
        this.trigger('oscCallbackBefore', response);
        this.trigger('callbackBefore', response);

        // Default action
        if (typeof response === 'undefined') return;

        var message = LC.global.languageSheet.error,
            success = 0;

        if (response.data) {
            message = response.data.response.message;
            success = response.data.response.success ? response.data.response.success : 0;
        }

        if (success) {
            var type = this.el.$form.find('[name="type"]').val(),
                mode = this.el.$form.find('[name="mode"]').val();

            if (Object.hasOwn(response.data.data.data, 'valid') && response.data.data.data.valid === false) {
                let validationData = response.data.data.data;

                if (validationData.validAddresses.length) {
                    let $addressInput = this.el.$form.find(`[name="${this.userType}_${type}_address"]`),
                        validAddresses = '';
                    $addressInput.parent().addClass('has-error');
                    validAddresses += '<div class="validAddress">';
                    validAddresses += `<div class="validAddress validAddressTitle">${LC.global.languageSheet.userAddressSelectValidAdress}</div>`;
                    validationData.validAddresses.forEach(validAddress => {
                        validAddresses += '<div type="button" class="selectValidAddress" >' + validAddress.address + '</div>';
                    });
                    validAddresses += '</div>';
                    $addressInput.after(validAddresses);
                    this.el.$form.find('.selectValidAddress').on('click', (event) => {
                        $addressInput.val(event.target.innerText);
                        this.el.$form.find('.validAddress').remove();
                        this.el.$form.submit();
                    });
                    this.showMessage(LC.global.languageSheet.userAddressInvalid, 'danger');
                } else {
                    let validationMessage = '';
                    validationData.messages.forEach(message => {
                        validationMessage += message.detail + '<br>';
                    })
                    this.showMessage(validationMessage, 'danger');
                }
            } else {

                // Close modal and trigger notify
                this.el.$form.closest('.addressBookModal').modal('hide');
                this.showMessage(LC.global.languageSheet.lblGenericUserAddressBookSaveData, 'success');

                // Updating elements related with selected address by action response.
                if (mode === 'update') {
                    // Refresh address
                    this.updateAddressHtmlData(response.data.data);
                } else if (mode === 'add') {
                    // Generate new address html
                    var $html = this.addNewAddressBlock(response.data.data, type),
                        $container = $('#' + type + 'AddressContainer .content'),
                        $lastAddress = $container.find('.addressBook').last();

                    $container.find('.notAvailableAddress').remove();

                    if ($lastAddress.length) {
                        $lastAddress.after($html);
                    } else {
                        $container.prepend($html);
                    }

                    if (response.data.data.data.defaultAddress) {
                        this.selectAddressBookCallback(response.data);
                    }

                    // Add background delete event
                    this.deleteAddressEvents($html.find('[data-lc-action="deleteAddressBook"]'));
                }
            }
        } else {
            this.showMessage(message, 'danger');
        }

        // Callback trigger
        this.trigger('oscCallback', response);
        this.trigger('callback', response);
    },

    /**
     * Update address html block with updated data
     * @memberOf LC.addressBookForm
     * @see LC.addressBookForm.callback
     * @see LC.addressBookForm.getAddressHtml
     *
     * @param {object} data address data ajax response
     */
    updateAddressHtmlData: function (data) {
        var defaultAlias = LC.global.languageSheet.noAliasAddress;
        if (data.data.defaultAddress) {
            defaultAlias = LC.global.languageSheet.mainAddress;
        }
        if (data && !isNaN(data.id) && data.id > 0) {
            var $address = $('#addressBookContainer_' + data.id),
                html = this.getAddressHtml(data.data);
            $address.find('label').html(data.data.alias.length ? data.data.alias : defaultAlias);
            $address.find('.addressDataBox').html(html);
            $address.find('.editAddressAction').attr("data-lc-data", JSON.stringify(data.data));
            $address.find('.deleteAddressAction').attr("data-lc-data", JSON.stringify(data.data));
        }
    },

    /**
     * Create a new html address info block
     * @memberOf LC.addressBookForm
     * @see LC.addressBookForm.updateAddressHtmlData
     * @see LC.addressBookForm.addNewAddressBlock
     *
     * @param {object} data address data
     * @return {string}
     */
    getAddressHtml: function (data) {
        var html = '',
            showCompany = data.company.length && data.userType == 'BUSINESS' || data.userType == 'FREELANCE',
            addresNameLabel = showCompany ? LC.global.languageSheet.company : LC.global.languageSheet.addressBookName,
            addressName = showCompany ? data.company : data.firstName + (data.lastName.length ? ' ' + data.lastName : '');
        html += addressName.length ? '<div class="field name" data-lc-label="' + addresNameLabel + '">' + addressName + '</div>' : '';
        html += '<div class="field address">';
        html += data.address.length ? '<span class="address" data-lc-label="' + LC.global.languageSheet.address + '">' + data.address + '</span>' : '';
        html += data.addressAdditionalInformation.length ? '<span class="addressAdditionalInformation" data-lc-label="' + LC.global.languageSheet.addressAditionalInformation + '">' + data.addressAdditionalInformation + '</span>' : '';
        html += data.number.length ? '<span class="number" data-lc-label="' + LC.global.languageSheet.number + '">' + data.number + '</span>' : '';
        html += data.city.length ? '<span class="city" data-lc-label="' + LC.global.languageSheet.city + '">' + data.city + '</span>' : '';
        html += data.postalCode.length ? '<span class="postalCode" data-lc-label="' + LC.global.languageSheet.postalCode + '">' + data.postalCode + '</span>' : '';
        html += data.state.length ? '<span class="state" data-lc-label="' + LC.global.languageSheet.state + '">' + data.state + '</span>' : '';
        html += data.location.geographicalZone.countryCode.length ? '<span class="location" data-lc-label="' + LC.global.languageSheet.country + '">' + LC.global.countries[data.location.geographicalZone.countryCode].name + '</span>' : '';
        html += '</div>';
        html += '<div class="field extraInfo">';
        if (data.company.length && data.userType == 'BUSINESS') {
            html += data.vat.length ? '<span class="vat" data-lc-label="' + LC.global.languageSheet.vat + '">' + data.vat + '</span>' : '';
        } else {
            html += data.nif.length ? '<span class="nif" data-lc-label="' + LC.global.languageSheet.nif + '">' + data.nif + '</span>' : '';
        }
        html += data.phone.length ? '<span class="phone" data-lc-label="' + LC.global.languageSheet.phone + '">' + data.phone + '</span>' : '';
        html += data.mobile.length ? '<span class="mobile" data-lc-label="' + LC.global.languageSheet.mobile + '">' + data.mobile + '</span>' : '';
        html += data.fax.length ? '<span class="fax" data-lc-label="' + LC.global.languageSheet.fax + '">' + data.fax + '</span>' : '';
        html += '</div>';
        return html;
    },

    /**
     * Add addres html block with data
     * @memberOf LC.addressBookForm
     * @see LC.addressBookForm.callback
     *
     * @param {object} data address data ajax response
     * @param {string} type 'billing'|'shipping'
     */
    addNewAddressBlock: function (data, type) {
        const $container = $('<div/>', {
            id: `addressBookContainer_${data.data.id}`,
            class: `addressBook ${type}Address${data.data.defaultAddress ? ' addressBookActive' : ''}`
        });
        const $wrap = $('<div/>', {
            class: 'wrap'
        });
        let $default = '',
            $defaultClass = 'default addressBookHidden',
            action = '',
            selectedLabel = '';

        let lcPage = $('body').data('lc-page');
        if (lcPage == 'userAddressBook' ||
            lcPage == 'accountCompanyStructure' ||
            lcPage == 'account') {
            action = 'set_default_address';
            selectedLabel = LC.global.languageSheet.defaultAddress;
        } else {
            action = 'select';
            selectedLabel = LC.global.languageSheet.selectedAddress;
        }
        if (data.data.defaultAddress) {
            $defaultClass = 'default';
        }
        $default = $('<div/>', {
            class: $defaultClass,
            html: selectedLabel
        });

        const $label = $('<label/>', {
            name: `${type}Address_${data.data.id}`,
            for: `${type}Address_${data.data.id}`,
            class: 'field',
            html: data.data.alias.length ? data.data.alias : LC.global.languageSheet.mainAddress,
        });
        const $actionButtons = $('<div/>', {
            class: 'actionButtons',
        });
        let $edit = '',
            $delete = '';

        let bs5 = LC.global.settings.coreMode === 'bootstrap5' ? '-bs' : '';
        $edit = $('<a/>', {
            href: '#',
            title: LC.global.languageSheet.editAddress,
            class: 'addressBookAction editAddressAction',
            html: '<svg class="icon"><use xlink:href="#icon-pencil"></use></svg>',
            'data-lc-data': JSON.stringify(data.data),
            'data-lc-action': 'editAddressBook',
            [`data${bs5}-toggle`]: 'modal',
            [`data${bs5}-target`]: `#${type}AddressBookModal`
        });
        $delete = $('<a/>', {
            href: '#',
            title: LC.global.languageSheet.deleteAddress,
            class: `addressBookAction deleteAddressAction${data.data.defaultAddress ? ' addressBookHidden' : ''}`,
            html: '<svg class="icon"><use xlink:href="#icon-trash"></use></svg>',
            'data-lc-data': JSON.stringify(data.data),
            'data-lc-action': 'deleteAddressBook'
        });

        const $dataBox = $('<div/>', {
            class: `addressDataBox ${type}AddressDataBox`,
            html: this.getAddressHtml(data.data)
        });
        let $selectButton = '',
            $selectRadio = '';

        if (this.el.$form.selectMode == 'selectModeRadio') {
            $selectRadio = $('<div/>', {
                class: 'form-check form-check-inline'
            });
            $selectRadio.append($('<input/>', {
                id: `${type}Address_${data.data.id}`,
                class: `form-check-input ${type}Address addressBookAction addressSelect${data.data.defaultAddress ? ' addressBookHidden' : ''}`,
                type: 'radio',
                html: LC.global.languageSheet.selectAddressBook,
                name: `${type}Address`,
                'data-lc-data': JSON.stringify({ id: data.data.id, type: type, mode: 'update', action: action })
            }));
        } else {
            let btnClass = BTN_LINK_CLASS;
            $selectButton = $('<button/>', {
                class: `${type}Address addressBookAction addressSelect ${btnClass}${data.data.defaultAddress ? ' addressBookHidden' : ''}`,
                type: 'button',
                html: LC.global.languageSheet.selectAddressBook,
                'data-lc-data': JSON.stringify({ id: data.data.id, type: type, mode: 'update', action: action })
            });
        }

        $container.append($wrap.append([
            $default,
            $selectRadio,
            $label,
            $actionButtons.append([
                $edit,
                $delete
            ]),
            $dataBox,
            $selectButton
        ]));

        return $container;
    },


    /**
     * Fill addressBook fields with address data
     * @memberOf LC.addressBookForm
     * @see LC.addressBookForm.actionAddAddress
     * @see LC.addressBookForm.actionEditAddress
     *
     * @param {object} data Address data object
     */
    fillFormData: function (data) {
        // Set userType input
        if (this.module != undefined) {
            data.userType = this.module;
        }
        this.el.$form.attr('lc-user-type', data.userType).data('lc-user-type', data.userType);

        var self = this;
        $.each(data, function (key, value) {

            if (key === 'location') {
                // Country <select>
                let fieldName = data.userType + '_' + self.el.$form.data('lc-address'),
                    selector = '[name="' + fieldName + '_country"]',
                    $select = self.el.$form.find(selector);
                if (value != null) {
                    $select.val(value.geographicalZone.countryCode); // .change();
                    if (value.geographicalZone.locationId > 0) {
                        selectLocationResult($select, value.geographicalZone.countryCode, value.geographicalZone.locationId, fieldName, false, data.city, data.postalCode);
                    }
                }
            } else if (key == 're') {
                let selector = '[id="' + data.userType + '_' + self.el.$form.data('lc-address') + '_' + key + '{{value}}' + '"]';
                if (value) {
                    self.el.$form.find(selector.replace("{{value}}", '1')).attr('checked', true);
                    self.el.$form.find(selector.replace("{{value}}", '0')).attr('checked', false);
                } else {
                    self.el.$form.find(selector.replace("{{value}}", '1')).attr('checked', false);
                    self.el.$form.find(selector.replace("{{value}}", '0')).attr('checked', true);
                }
            }
            else {
                let selector = '[name="' + data.userType + '_' + self.el.$form.data('lc-address') + '_' + key + '"]';
                self.el.$form.find(selector).val(value);
            }
        });

        // Update elements & properties
        this.userType = this.el.$form.data('lc-user-type');
        var tabPane = '#' + this.el.$form.data('lc-address') + '_tabPane_' + this.userType;
        this.el.$userTabPanes = this.el.$form.find('.tabPaneUserType');
        this.el.$userFieldElements = this.el.$form.find(tabPane + ' .formField');

        // Save original disableds
        this.el.$form.find('.formField').each(function () {
            if (typeof $(this).data('lc-disabled') === 'undefined')
                $(this).data('lc-disabled', $(this).prop('disabled'));
        });

        // Change userType
        this.el.$form.find('.userTypeNavTabs [data-lc-user-type="' + data.userType + '"]').tab('show');
        this.setUserType();
        this.trigger('afterFillFormData', this.el.$form);
    },

    /**
     * Submit form
     * @memberOf LC.addressBook
     * @param  {Object} event
     */
    submit: function (event) {

        if (this.el.$form.find('.address-complete:visible').length != this.el.$form.find('.userFieldGroupCountry:visible').length) {
            var $result = this.el.$form.find('.userFieldGroupCountry:visible').not('.address-complete:visible');
            $result.addClass('address-incomplete');
            $([document.documentElement, document.body]).animate({
                scrollTop: $result.offset().top - 200
            }, 1000);

            return false;
        } else {
            this.el.$form.find('.userFieldGroupCountry:visible').removeClass('address-incomplete');
        }
        this.superForm('submit', event);
    }

};

/**
 * @class LC.MiniBasketClass
 * @memberOf LC
 */
LC.MiniBasketClass = function () {
    this.el = {};

    this.events = {
        before: null,
        callback: null,
    };

    // Initialize
    this.initialize.apply(this);
};

// Extend Public Methods on LC.MiniBasketClass
$.extend(true, LC.MiniBasketClass.prototype, {
    firstReload: true,

    /**
     * Initialize miniBasket
     * @memberOf LC.MiniBasketClass
     */
    initialize: function () {
        this.el.$container = $('#miniBasket');
        this.el.$miniBasketContent = $('#miniBasketContent');
        this.data = this.el.$miniBasketContent.data('lc-mini-basket');

        this.reload();

        // Add Event delete item
        this.el.$container.on('click', '[data-lc-mini-basket-delete]', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let data = {};
            data.id = $(event.currentTarget).parent().attr('id');
            data.hash = $(event.currentTarget).data('lc-mini-basket-delete');
            LC.resources.pluginListener('onRemoveProduct', event, data);
            this.deleteItem($(event.currentTarget).data('lc-mini-basket-delete'));
        }.bind(this));

        // Add Event delete grid
        this.el.$container.on('click', '[data-lc-mini-basket-delete-grid], [data-lc-basketdeleterows]', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let data = {},
                hashes = $(event.currentTarget).data('lc-mini-basket-delete-grid');
            if (!hashes) {
                hashes = $(event.currentTarget).data('lc-basketdeleterows');
            }
            data.id = $(event.currentTarget).parent().attr('id');
            hashes.forEach(hash => {
                data.hash = hash;
                LC.resources.pluginListener('onRemoveProduct', event, data);
            });
            this.deleteGrid(hashes);
        }.bind(this));

        this.initializeQuantityElements();
    },

    initializeQuantityElements: function () {
        try {
            this.el.$container.find('input[data-lc-quantity]').quantity();
        } catch (err) {
            // Pray God's will call quantity plugin
        }

        this.el.quantityElements = this.el.$container.find('input.basketQuantity:text');

        // Select box
        if (!this.el.quantityElements.length)
            this.el.quantityElements = this.el.$container.find('select.basketQuantity');

        this.el.quantityElements.on('click', function (ev) {
            ev.stopPropagation();
        });

        this.el.quantityElements.on('change', this.onChangeQuantity.bind(this));
    },

    exists: function () {
        return typeof this.data === 'object';
    },

    /**
     * Add events for customize the commerce performance
     * @memberOf LC.MiniBasketClass
     * @param {String}   name Name of type of function
     * @param {Function} fn   Function to call
     */
    addEvent: function (name, fn) {
        if ((name == 'before' || name == 'callback') && fn && typeof fn === 'function') this.events[name] = fn;
    },

    /**
     * Change quantity
     * @memberOf LC.MiniBasketClass
     * @param  {object} eventData
     */
    onChangeQuantity: function (eventData) {
        //Call before
        this.before();

        var qtText = 'quantity';
        var productHash = eventData.currentTarget.name.substring(qtText.length);

        if (productHash.startsWith('Grid')) {
            let name = $(eventData.target).attr('name').replace('quantity', ''),
                sendData = {};
            if (name.length == 0) {
                name = 'grid' + $(eventData.target).data('lc-grid-combination-id');
            }
            sendData[name] = {
                mode: 'UPDATE',
                type: $(eventData.target).data('lc-row-type'),
                quantity: $(eventData.target).val(),
                options: $(eventData.target).data('lc-row-options'),
                id: $(eventData.target).closest('[data-lc-grid-product-id]').data('lc-grid-product-id'),
            };

            $.post(
                LC.global.routePaths.CHECKOUT_INTERNAL_RECALCULATE_BASKET,
                { data: JSON.stringify({ data: sendData }) },
                function (response) {
                    if (response.data.response.success != 1) {
                        LC.notify(response.data.response.message, { type: 'danger' });
                    }
                    this.reload();
                }.bind(this),
                'json'
            );
            return;
        }

        var prodCombinationInput = $(
            'form.buyForm input[data-lc-product-hash="' + productHash + '"]'
        ).get(0);

        if (prodCombinationInput) {
            var itemForm = prodCombinationInput.closest('form');
            itemForm.module.updateCombinationsFields([productHash], eventData.currentTarget.value);
            itemForm.module.updateCombinationsFormType();
        }

        // Reload miniBasket
        if (!this.el.container) this.el.container = $('#miniBasket');

        this.el.container.load(
            LC.global.routePaths.BASKET_INTERNAL_MINI_BASKET + '?hash=' + productHash + '&quantity=' + eventData.currentTarget.value + '&type=' + $(eventData.currentTarget).data('lc-row-type') + ' #miniBasketContent',
            function () {
                this.oneStepCheckoutRefresh();
                this.loadComplete();
            }.bind(this)
        );
    },

    /**
     * Refresh miniBasket view
     * @memberOf LC.MiniBasketClass
     */
    reload: function (callback) {
        if (this.firstReload) {
            this.action = 'load';
            this.firstReload = false;
        } else {
            this.action = 'reload';
        }

        // Call before
        this.before();

        // Force reload changing miniBasket data on checkout
        var page = $('body').data('lc-page');
        if ($('form.basketForm').length && (page === 'checkoutBasket' || page === 'checkoutPaymentAndShipping') && this.action == 'reload') {
            $('html, body').animate({ scrollTop: 0 }, 'slow', function () {
                window.location.reload(true);
            });
        }

        // No cache param
        var ncParam = 'nc=' + Math.floor(100 + Math.random() * 899);

        // Action
        if (!this.el.$container) this.el.$container = $('#miniBasket');

        this.el.$container.load(
            LC.global.routePaths.BASKET_INTERNAL_MINI_BASKET + '?' + ncParam + ' #miniBasketContent',
            function () {
                this.loadComplete(callback);
            }.bind(this)
        );
    },

    /**
     * Delete product from miniBasket
     * @memberOf LC.MiniBasketClass
     * @param  {string} hash ProductRow hash
     */
    deleteItem: function (hash) {
        this.action = 'delete';

        // Call before
        this.before();

        this.deleteRow = hash;

        // Reload miniBasket
        if (!this.el.container) this.el.container = $('#miniBasket');

        this.el.container.load(
            LC.global.routePaths.BASKET_INTERNAL_MINI_BASKET + '?hash=' + this.deleteRow + ' #miniBasketContent',
            function () {
                this.oneStepCheckoutRefresh();
                this.loadComplete();
            }.bind(this)
        );
    },

    /**
     * Delete Grid from miniBasket
     * @memberOf LC.MiniBasketClass
     * @param  {array} hashes GridRow hashes
     */
    deleteGrid: function (hashes) {
        this.action = 'delete';

        // Call before
        this.before();

        // Reload miniBasket
        if (!this.el.container) this.el.container = $('#miniBasket');

        this.el.container.load(
            LC.global.routePaths.BASKET_INTERNAL_MINI_BASKET + '?hashes=' + hashes + ' #miniBasketContent',
            function () {
                this.oneStepCheckoutRefresh();
                this.loadComplete();
            }.bind(this)
        );
    },

    /**
     * Event trigger before post to server
     * @memberOf LC.MiniBasketClass
     */
    before: function () {
        if (!this.el.container) this.el.container = $('#miniBasket');

        // Add loadingClass
        this.el.$container.addClass('miniBasketLoading');

        if (this.events.before && typeof this.events.before === 'function') this.events.before.bind(this)();
    },

    /**
     * Callback function
     * @memberOf LC.MiniBasketClass
     */
    loadComplete: function (callback) {
        if (this.el.$container && this.el.$container.hasClass('miniBasketLoading'))
            this.el.$container.removeClass('miniBasketLoading');

        this.el.$miniBasketContent = this.el.$container.find('#miniBasketContent');
        this.data = this.el.$miniBasketContent.data('lc-mini-basket');

        var saveBasketForm = this.el.$miniBasketContent.find('form');
        if (saveBasketForm && saveBasketForm.data('lcForm') == 'saveBasketForm')
            new LC.saveBasketForm(saveBasketForm[0]);

        // Callbacks
        if (this.events.callback && typeof this.events.callback === 'function') this.events.callback.bind(this)(this.data, this.action);

        LC.initializeCountdowns();
        LC.basketExpiration.checkNewExpirationDate();
        $('[data-lc-event]').dataEvent();

        if (this.deleteRow && this.deleteRow.length) {
            var hashes = this.deleteRow.split('-');
            this.deleteRow = '';

            var prodCombinationInput = $('form.buyForm input[data-lc-row-hash="' + hashes[0] + '"]').get(0);
            if (prodCombinationInput) {
                var itemForm = prodCombinationInput.closest('form');

                itemForm.module.updateCombinationsFields(hashes, 0);
                itemForm.module.updateCombinationsFormType();
            }
        }

        this.initializeQuantityElements();

        if (callback && typeof callback === 'function') {
            callback();
        }
    },

    /**
     * On Recieve
     * @memberOf LC.MiniBasketClass
     */
    onReceive: function (data) {
        if (!this.el.container) this.el.container = $('#miniBasket');

        this.el.container.replaceWith(data);

        // Update this.el.container
        this.el.container = $('#miniBasket');

        if (this.events.callback && typeof this.events.callback === 'function') this.events.callback(data);
    },

    oneStepCheckoutRefresh: function () {
        if (this.oneStepCheckoutCallback && typeof this.oneStepCheckoutCallback === 'function') {
            this.oneStepCheckoutCallback(this);
        }
    },
});

/**
 * LC.miniBasket
 * @description Generate miniBasket Object
 * @type {LC.MiniBasketClass}
 */
LC.miniBasket = new LC.MiniBasketClass();

/**
 * @class LC.ProductComparisonDetailClass
 * @memberOf LC
 */
LC.ProductComparisonDetailClass = function () {
    this.el = {};

    this.events = {
        before: null,
        callback: null,
    };

    // Initialize
    this.initialize.apply(this);
};

// Extend Public Methods on LC.ProductComparisonDetailClass
$.extend(true, LC.ProductComparisonDetailClass.prototype, {
    /**
     * Initialize productComparisonDetail
     * @memberOf LC.ProductComparisonDetailClass
     */
    initialize: function () {
        this.el.$container = $('#productComparisonDetailContent');
        this.action = 'load';

        this.reload();

        this.el.$container.on('click', '[data-lc-function="deleteProductComparison"]', function (event) {
            event.preventDefault();
            this.deleteItem($(event.currentTarget).data('lc-data'));
        }.bind(this));
    },

    /**
     * Add events for customize the commerce performance
     * @memberOf LC.ProductComparisonDetailClass
     * @param {string} name Name of type of function
     * @param {function} fn Function to call
     */
    addEvent: function (name, fn) {
        var boolCheckEventName = name == 'before' || name == 'callback' || name == 'beforeReloadBadge' || name == 'callbackReloadBadge';
        if (boolCheckEventName && fn && typeof fn === 'function') this.events[name] = fn;
    },

    /**
     * Refresh notificationProductComparison view
     * @memberOf LC.ProductComparisonDetailClass
     */
    reload: function (response) {
        // Call before
        this.before();

        if (!this.el.$container) this.el.$container = $('#productComparisonDetailContent');
        // No cache param
        var ncParam = 'nc=' + Math.floor(100 + Math.random() * 899);

        this.el.$container.load(
            LC.global.routePaths.PRODUCT_COMPARISON_INTERNAL_PRODUCT_COMPARISON_PREVIEW + '?' + ncParam,
            function () {
                this.loadComplete();
            }.bind(this)
        );

    },

    /**
     * Delete product from productComparison
     * @memberOf LC.ProductComparisonDetailClass
     * @param  {number} productId Id from product to delete
     */
    deleteItem: function (productId) {
        // Call before
        this.before();

        // Reload productComparisonDetail
        if (!this.el.$container) this.el.$container = $('#productComparisonDetail');

        this.action = 'delete';

        $.post(
            LC.global.routePaths.PRODUCT_COMPARISON_INTERNAL_DELETE_COMPARISON_PRODUCT,
            { data: JSON.stringify({ id: productId }) },
            this.loadComplete.bind(this),
            'json'
        ).done(() => {
            this.el.$container.find('.productComparisonPreviewItem' + productId).remove();
            if (this.el.$container.find('.preview-item').length == 0) {
                this.el.$container.find('.product-comparison-second-title, .items-wrapper, .product-comparison-link').remove();
                this.el.$container.append(`<div class="empty-text">${LC.global.languageSheet.noProductsInComparison}</div>`);
            }
            this.deleteTableItem(productId);
            this.updateProductComparisonButtons(productId);
            this.updateBadge();
        });
    },

    /**
     * Delete product from productComparison table
     * @memberOf LC.ProductComparisonDetailClass
     * @param  {number} productId Id from product to delete
     */
    deleteTableItem: function (productId) {
        var $table = $('#comparison-table');
        $table.find(`[data-lc-product-comparison-item="${productId}"]`)?.fadeOut('slow', function () {
            this.remove();
            if ($table.find('[data-lc-product-comparison-item]').length === 0) {
                $table.before(`<div class="empty-text">${LC.global.languageSheet.noProductsInComparison}</div>`);
                $table.remove();
            }
        });
    },

    /**
     * Event trigger before post to server
     * @memberOf LC.ProductComparisonDetailClass
     */
    before: function () {
        if (!this.el.$container) this.el.$container = $('#productComparisonDetailContent');

        if (this.events.before && typeof this.events.before === 'function') this.events.before.bind(this)();
    },

    /**
     * Callback function
     * @memberOf LC.ProductComparisonDetailClass
     */
    loadComplete: function (callback) {
        if (this.events.callback && typeof this.events.callback === 'function') this.events.callback.bind(this)(data, this.action);
        this.el.$container = $('#productComparisonDetailContent');
        this.updateBadge();
    },

    updateBadge: function () {
        let nComparisonProducts = document.querySelectorAll('#productComparisonDetailContent .preview-item').length;
        $('.product-comparison-number-badge')
            .removeClass((i, className) =>
                (className.match(/n\-[0-9]*/g) || []).join(' ')
            )
            .addClass('n-' + nComparisonProducts)
            .html(nComparisonProducts);
    },

    updateProductComparisonButtons: function (productId) {
        var $comparablePageButtons = $('[data-product-comparison]').filter((i, item) => {
            return JSON.parse(item.dataset.productComparison).id == productId;
        });

        if ($comparablePageButtons.length !== 0) {
            $comparablePageButtons.each((index, item) => {
                var nodeData = $(item).data('product-comparison');
                nodeData.type = 'add';
                $(item).removeClass('productComparisonButtonDelete').addClass('productComparisonButtonAdd');
                if (nodeData.showLabel) {
                    $(item).html(nodeData.labelDelete);
                }
            });
        }
    },
});
/**
 * LC.productComparisonDetail
 * @description Generate productComparisonDetail Object
 * @type {LC.ProductComparisonDetailClass}
 */
LC.productComparisonDetail = new LC.ProductComparisonDetailClass();

/**
 * Initialize all countdowns
 * @todo lock-countdown and basket-expires
 */
LC.initializeCountdowns = function () {
    if (!('localServerOffsetTime' in LC.global.settings))
        LC.global.settings.localServerOffsetTime = moment().unix() - moment(LC.global.settings.serverTime).unix();

    // Initialize Product Count Down
    $('[data-lc-countdown]').each(function (index, element) {
        new LC.productCountdown(element.id);
    });

    $(document.body).removeClass('basket-check-availability');
    $('[data-lock-countdown]').each(function (index, element) {
        if (!element.basketLockCountdown) element.basketLockCountdown = new LC.basketLockCountdown(element);
    });

    $('[data-lc-basket-expires]').each(function (index, element) {
        if (!element.basketCountdown) element.basketCountdown = new LC.basketCountdown(element);
    });
};

/**
 * LC Range Sliders.
 * Initialize ionRangeSlider plugin
 *
 * @method LC.initializeRangeSliders
 * @memberOf LC
 */
LC.initializeRangeSliders = function () {
    if (typeof $.fn.ionRangeSlider === 'function') {
        $('[data-lc-range-slider]').ionRangeSlider({
            prettify: function (number) {
                return outputHtmlCurrency(number);
            },
            onFinish: function (data) {
                if ($(data.input).closest('form').data('lc-autosubmit')) {
                    $(data.input).closest('form').submit();
                }
            }
        });
    }
};

/**
 * BuyForm Resources
 * It has buyForm prototyping functions for buyForm & BuyBundleForm
 * Works with base product Beyond format
 * @property {object} LC.buyFormResources
 * @description Form extended from LC.buyFormProperties
 * @memberOf LC
 */
LC.buyFormResources = {

    filePlugin: [],
    filePluginIsOnError: new Map(),

    initializeFilePlugin: function () {

        this.el.$form.find('[data-lc-file-plugin]').each((index, element) => {
            document.addEventListener('dragover', (e) => e.preventDefault());
            document.addEventListener('drop', (e) => e.preventDefault());
            let data = $(element).data('lc-file-plugin');
            this.filePlugin[data.optionId] = FilePond.create(element, {
                id: data.id,
                name: data.id,
                required: data.required,
                allowMultiple: data.maxValues > 1 ? true : false,
                disable: data.disable,
                dropOnPage: false,
                allowPaste: false,
                server: null,
                credits: false,
                labelIdle: LC.global.languageSheet.jsInputFileIdle,
                labelInvalidField: LC.global.languageSheet.jsInputFileInvalidField,
                labelFileWaitingForSize: LC.global.languageSheet.jsInputFileFileWaitingForSize,
                labelFileSizeNotAvailable: LC.global.languageSheet.jsInputFileFileSizeNotAvailable,
                labelFileLoading: LC.global.languageSheet.jsInputFileFileLoading,
                labelFileLoadError: LC.global.languageSheet.jsInputFileFileLoadError,
                labelFileProcessing: LC.global.languageSheet.jsInputFileFileProcessing,
                labelFileProcessingComplete: LC.global.languageSheet.jsInputFileFileProcessingComplete,
                labelFileProcessingAborted: LC.global.languageSheet.jsInputFileFileProcessingAborted,
                labelFileProcessingError: LC.global.languageSheet.jsInputFileFileProcessingError,
                labelFileProcessingRevertError: LC.global.languageSheet.jsInputFileFileProcessingRevertError,
                labelFileRemoveError: LC.global.languageSheet.jsInputFileFileRemoveError,
                labelTapToCancel: LC.global.languageSheet.jsInputFileTapToCancel,
                labelTapToRetry: LC.global.languageSheet.jsInputFileTapToRetry,
                labelTapToUndo: LC.global.languageSheet.jsInputFileTapToUndo,
                labelButtonRemoveItem: LC.global.languageSheet.jsInputFileButtonRemoveItem,
                labelButtonAbortItemLoad: LC.global.languageSheet.jsInputFileButtonAbortItemLoad,
                labelButtonRetryItemLoad: LC.global.languageSheet.jsInputFileButtonRetryItemLoad,
                labelButtonAbortItemProcessing: LC.global.languageSheet.jsInputFileButtonAbortItemProcessing,
                labelButtonUndoItemProcessing: LC.global.languageSheet.jsInputFileButtonUndoItemProcessing,
                labelButtonRetryItemProcessing: LC.global.languageSheet.jsInputFileButtonRetryItemProcessing,
                labelButtonProcessItem: LC.global.languageSheet.jsInputFileButtonProcessItem,
                oninit: () => {
                    $($.find(`#${data.id} input[type="file"]`)[0]).addClass(data.attachedClassName);
                    if (data.accept.length) {
                        $($.find(`#${data.id} input[type="file"]`)[0]).attr('accept', data.accept);
                    }
                },
                onaddfile: (error, file) => {
                    let $inputFile = $($.find(`#${data.id} input[type="file"]`)[0]);
                    this.filePlugin[data.optionId].changeInputFile = true;
                },
                onremovefile: (error, file) => {
                    let $inputFile = $($.find(`#${data.id} input[type="file"]`)[0]);
                    this.filePlugin[data.optionId].changeInputFile = true;
                },
                onupdatefiles: (fileItems) => {
                    let $inputFile = $($.find(`#${data.id} input[type="file"]`)[0]);
                    if (this.filePlugin[data.optionId].changeInputFile) {
                        let files = [];
                        fileItems.forEach(item => {
                            files.push(item.file);
                        });
                        $inputFile.files = files;
                        $inputFile.optionId = data.optionId;
                        $inputFile.optionName = data.optionName;
                        this.parseFile($inputFile);
                        this.filePlugin[data.optionId].changeInputFile = false;
                    }
                }
            });
        });
    },

    /**
     * Update buyFormSubmit
     * @memberOf LC.buyFormResources
     * @param {object} combinationData combination data
     * @param {object} button
     * @param {string} itemType item type (product|bundleGrouping|notAvailable)
     */
    updateButton: function (combinationData, button, itemType) {
        const status = combinationData.status;

        var properties = {};
        if (itemType == 'notAvailable') {
            properties.className = 'notAvailable';
            properties.name = LC.global.languageSheet.notAvailable;
            properties.disabled = true;
        } else {
            if (status == "AVAILABLE" || status == "RESERVE") {
                properties.disabled = false;
                if (status == "AVAILABLE") {
                    properties.className = 'buy';
                    if (this.filledForm) {
                        properties.name = LC.global.languageSheet.update;
                        properties.className += ' update';
                    } else {
                        properties.name = LC.global.languageSheet.buy;
                    }
                } else {
                    properties.className = 'reserve';
                    properties.name = LC.global.languageSheet.reserve;
                }
            } else {
                properties.disabled = true;
                if (status == "SELECT_OPTION") {
                    properties.className = 'selectOption';
                    properties.disabled = true;
                    var selectOptionText = '';
                    if (itemType == 'product') {
                        selectOptionText = this.getSelectOptionText(combinationData, this.data);
                    } else {
                        selectOptionText = this.getBundleSelectOptionText(combinationData, this.dataProducts);
                    }
                    properties.name = LC.global.languageSheet.selectOption.replace('{{option}}', selectOptionText);
                } else {
                    properties.className = 'notAvailable';
                    properties.name = LC.global.languageSheet.notAvailable;
                }
            }
        }

        button.removeClass('selectOption notAvailable reserve buy');
        button.addClass(properties.className);
        button.prop('disabled', properties.disabled);
        button.data('buyFormSubmitName', properties.name);

        button.each((index, el) => {
            if (!$(el).data('lc-express-checkout-plugin')) {
                if ($(el).data('show-label') == true) $(el).html(properties.name);
                else $(el).html('');
            }
        });

        if (this.gridData) {
            if (!this.gridData.totalQuantity || this.gridData.totalQuantity <= 0) {
                button.addClass('notAvailable');
                button.prop('disabled', true);
            }
            let gridCombinationsInfo = this.el.$form.find('.grid-combinations-info');
            gridCombinationsInfo.removeClass('selectOption notAvailable reserve buy');
            gridCombinationsInfo.addClass(properties.className);
            gridCombinationsInfo.html(`<span>${properties.name}</span>`);
        }

    },

    /**
     * @memberOf LC.buyFormResources
     */
    attachmentFields: function ($elements) {
        $elements.each(
            (index, element) => {
                let $element = $(element);
                $element.find('.new-attachment').click(function (event) {
                    $element.find('input.productOptionAttachmentHiddenValue').val('');
                    $element.find('.productOptionAttachmentValue').show();
                    $element.find('input.productOptionAttachmentValue').removeAttr('disabled');
                    $element.find('.productOptionAttachmentButtons').hide();
                }.bind(this));
            }
        );
    },

    /**
     * @memberOf LC.buyFormResources
     */
    parseFile: function (field) {
        var files = field.files,
            fileNameRegex = new RegExp('^[À-ÿ\\w\\-. ]+$'),
            options = $(field).closest('.lcProductOptionAttachment').data('options'),
            optionId = '',
            filePlugin = null;
        if (field.optionId) {
            optionId = field.optionId;
            filePlugin = this.filePlugin[field.optionId];
        } else {
            optionId = $(field).attr('id').replace('optionValueInputFile', '');
        }
        this.filePluginIsOnError.set(field.optionId, '');
        $(field).closest('.productOption').find('input.productOptionAttachmentHiddenValue').val('').change();
        if (files.length > 0) {
            const totalSize = Array.from(files).reduce((sum, file) => sum + file.size, 0);
            if (totalSize > (options.maxSize * 1024 * 1000)) {
                LC.notify(LC.global.languageSheet.attachFileMaxSize.replace('{{maxSize}}', options.maxSize), { type: 'danger' });
                $(field).val('');
                if (filePlugin) filePlugin.removeFiles();
            } else if ((files.length < options.minValues || files.length > options.maxValues) && !filePlugin) {
                LC.notify(LC.global.languageSheet.attachFileQuantityError.replace('{{minValues}}', options.minValues).replace('{{maxValues}}', options.maxValues), { type: 'danger' });
                $(field).val('');
            } else {
                if ((files.length < options.minValues || files.length > options.maxValues) && filePlugin) {
                    LC.notify(LC.global.languageSheet.attachFileQuantityError.replace('{{minValues}}', options.minValues).replace('{{maxValues}}', options.maxValues), { type: 'danger' });
                    const files = filePlugin.getFiles();
                    if (files.length > options.maxValues) {
                        if (files.length > options.maxValues) {
                            const excessFiles = files.slice(0, files.length - options.maxValues);
                            excessFiles.forEach(file => {
                                filePlugin.removeFile(file.id);
                            });
                        }
                    } else if (files.length < options.minValues) {
                        this.filePluginIsOnError.set(field.optionId, field.optionName + ': ' + LC.global.languageSheet.attachFileQuantityError.replace('{{minValues}}', options.minValues).replace('{{maxValues}}', options.maxValues));
                    }
                }
                // Check file names
                for (var i = 0; i < files.length; i++) {
                    if (!fileNameRegex.test(files[i].name)) {
                        LC.notify(LC.global.languageSheet.attachFileRegexError, { type: 'danger' });
                        $(field).val('');
                        if (filePlugin) filePlugin.removeFiles();
                    }
                }
                // process files names
                for (var i = 0; i < files.length; i++) {
                    (function (file, i) {
                        var name = file.name,
                            reader = new FileReader();
                        reader.onload = function () {
                            var optionValue = {
                                extension: name.split('.').pop(),
                                fileName: name,
                                value: reader.result
                            };
                            $(field).closest('.productOption').find('#optionValue' + optionId + '_' + (i + 1)).val(JSON.stringify(optionValue)).change();
                        };
                        reader.onerror = function (error) {
                            $(field).val('');
                            $(field).closest('.productOption').find('input.productOptionAttachmentHiddenValue').val('').change();
                            LC.notify(LC.global.languageSheet.attachFileError, { type: 'danger' });
                        };
                        reader.readAsDataURL(file);
                    })(files[i], i);
                }
            }
        }
    },

    /**
     * @memberOf LC.buyFormResources
     */
    initDatetimepickers: function () {
        // Init calendar
        this.el.$form.find('[data-datetimepicker]').each(
            (index, el) => {
                let $calendar = $(el),
                    language = $calendar.data('language') ? $calendar.data('language') : 'en',
                    format = $calendar.data('format'),
                    startDate = $calendar.data('startdate'),
                    endDate = $calendar.data('enddate'),
                    weekstart = $calendar.data('weekstart');

                moment.locale(language, {
                    week: { dow: weekstart }
                });

                $calendar.datetimepicker({
                    locale: language,
                    format: format ? format : CALENDAR_PLUGIN_DATE_FORMAT,
                    minDate: startDate ? startDate : false,
                    maxDate: endDate ? endDate : false
                });

                $(el).on('dp.change', (e) => {
                    let $optionsubmitValue = this.el.$form.find('[name="' + $calendar.data('submit') + '"]');
                    if (e.date) {
                        $optionsubmitValue.val(moment(e.date).format('YYYY-MM-DD'));
                    } else {
                        $optionsubmitValue.val('');
                    }
                });

            }
        );
    },

    /**
     * return required select option text
     * @memberOf LC.buyFormResources
     * @param {object} item current data item
     * @param {object} product Product detaill
     * @return {string} select option text
     */
    getSelectOptionText: function (item, product) {
        var selectOptionText = '';
        item.options.forEach(option => {
            if (option.missed) {
                product.options.forEach(productOption => {
                    if (productOption.id == option.id) {
                        if (selectOptionText.length) {
                            selectOptionText += ', ';
                        }
                        selectOptionText += productOption.language.name;
                    };
                });
            }
        });
        return selectOptionText;
    },

    /**
     * Init options
     * @memberOf LC.buyFormResources
     */
    initOptions: function () {
        // Before trigger
        this.trigger('initOptionsBefore');

        this.el.$productOptionAttachment = this.el.$form.find('.lcProductOptionAttachment');

        if (this.el.$productOptionAttachment.length)
            this.attachmentFields(this.el.$productOptionAttachment);

        this.el.$options = this.el.$form.find(
            'select.productOptionSelectValue, input.productOptionRadioValue, input.productOptionCheckboxValue, input.productOptionBooleanValue, input.productOptionTextValue, textarea.productOptionLongTextValue, input.productOptionDateValue, input.productOptionAttachmentValue, input.productOptionAttachmentHiddenValue'
        );

        this.el.$options.change(this.onChangeOption.bind(this));

        this.initDatetimepickers();

        const $productsOptions = $(this.el.$form.find('div.productOptions'));
        $productsOptions.each((i, productOptions) => {
            if (!this.useUrlOptionsParams) {
                this.useUrlOptionsParams = JSON.parse($(productOptions).attr('data-lc-data')).useUrlOptionsParams;
            }
            if (!this.combinationDataOptionChanged) {
                this.combinationDataOptionChanged = JSON.parse($(productOptions).attr('data-lc-data')).combinationDataOptionChanged;
            }
        });

        this.el.$form
            .find('input.productOptionRadioValue:checked')
            .parent('div.productOptionRadioValue')
            .addClass('productOptionSelected');
        this.el.$form
            .find('input.productOptionCheckboxValue:checked')
            .parent('div.productOptionCheckboxValue')
            .addClass('productOptionSelected');

        this.onChange(this.useUrlOptionsParams || this.combinationDataOptionChanged);

        // Callback trigger
        this.trigger('initOptionsCallback');
        this.optionsInitialized = true;
    },

    /**
     * Change options
     * @memberOf LC.buyFormResources
     * @param  {object} eventData
     */
    onChangeOption: function (eventData) {
        // Before trigger
        this.trigger('onChangeOptionBefore');

        let getCombinationData = true;

        if ($(eventData.target).hasClass('productOptionRadioValue')) {
            $(eventData.target)
                .parents('div.productOptionValues')
                .find('div.productOptionRadioValue')
                .removeClass('productOptionSelected');
            $(eventData.target)
                .parent('div.productOptionRadioValue')
                .addClass('productOptionSelected');
        }

        if ($(eventData.target).hasClass('productOptionCheckboxValue')) {
            if (eventData.target.checked)
                $(eventData.target)
                    .parent('div.productOptionCheckboxValue')
                    .addClass('productOptionSelected');
            else
                $(eventData.target)
                    .parent('div.productOptionCheckboxValue')
                    .removeClass('productOptionSelected');
        }

        var useOnChange = true;
        if ($(eventData.target).hasClass('productOptionAttachmentValue')) {
            this.parseFile(eventData.target);
            useOnChange = false;
        }

        // combinationData do not change in boolean, date, attachment and text, or use grid
        if (
            $(eventData.target).hasClass('productOptionBooleanValue') ||
            $(eventData.target).hasClass('productOptionDateValue') ||
            $(eventData.target).hasClass('productOptionAttachmentValue') ||
            $(eventData.target).hasClass('productOptionAttachmentHiddenValue') ||
            $(eventData.target).hasClass('productOptionTextValue') ||
            this.gridData
        ) getCombinationData = false;

        // Callback trigger
        this.trigger('onChangeOptionCallback', eventData.target);

        if (useOnChange) {
            this.onChange(getCombinationData);
        }

    },

    /**
     * check de options availability
     * @memberOf LC.buyFormResources
     * @param {object} combinationDataItem current data item
     * @param {object} $productOptionsDiv product options div
     * @param {object} productOptionsData product options data
     */
    checkOptionsAvailability: function (combinationDataItem, $productOptionsDiv, productOptionsData) {

        const bundleDefinitionSectionItemId = productOptionsData.bundleDefinitionSectionItemId;

        combinationDataItem.options.forEach(combinationDataItemOption => {

            const optionName = "optionValue" + (bundleDefinitionSectionItemId > 0 ? '_' + bundleDefinitionSectionItemId + '_' : '') + combinationDataItemOption.id,
                itemsOption = $productOptionsDiv.find(`[name="${optionName}"]`);

            combinationDataItemOption.values.forEach(combinationDataItemOptionValue => {
                itemsOption.each((i, itemOption) => {
                    if (itemOption.tagName.toLowerCase() == 'select') {
                        $(itemOption).find('option').each((j, itemOptionValue) => {
                            if ($(itemOptionValue).val() == combinationDataItemOptionValue.id) {
                                $(itemOptionValue).attr('data-lc-availability', combinationDataItemOptionValue.available ? 'true' : 'false');
                            }
                            return;
                        });
                    } else {
                        if ($(itemOption).val() == combinationDataItemOptionValue.id) {
                            if (combinationDataItemOptionValue.available) {
                                $(itemOption).addClass('available');
                                $(itemOption).parent().addClass('available');
                                $(itemOption).removeClass('notAvailable');
                                $(itemOption).parent().removeClass('notAvailable');
                            } else {
                                $(itemOption).removeClass('available');
                                $(itemOption).parent().removeClass('available');
                                $(itemOption).addClass('notAvailable');
                                $(itemOption).parent().addClass('notAvailable');
                                if ($(itemOption).prop("checked")) {
                                    $('.checkbox').prop('checked', false);
                                }
                            }
                            $(itemOption).attr('data-lc-availability', combinationDataItemOptionValue.available ? 'true' : 'false');
                        }
                    }
                });
            });
        });
    },

    /**
     * add options to product link
     * @memberOf LC.buyFormResources
     * @param {object} $itemContainer current data 
     * @param {object} productOptionsData product options data
     */
    addOptionsToProductLink: function (productOptionsData, $itemContainer) {
        var options = $itemContainer.find('select.productOptionSelectValue, input.productOptionRadioValue, input.productOptionCheckboxValue, input.productOptionBooleanValue').serializeArray(),
            optionsParams = {},
            pathParams = '',
            bundleDefinitionSectionItemId = productOptionsData.bundleDefinitionSectionItemId,
            prefixOptionName = "optionValue";

        if (bundleDefinitionSectionItemId > 0) {
            prefixOptionName = prefixOptionName + '_' + bundleDefinitionSectionItemId + '_';
        }

        for (var i = 0; i < options.length; i++) {
            if (!(options[i].name in optionsParams)) {
                optionsParams[options[i].name] = [];
            }
            optionsParams[options[i].name].push(options[i].value);
        }

        if (Object.keys(optionsParams).length > 0) {
            pathParams = '?';
        }
        for (var element in optionsParams) {
            if (optionsParams.hasOwnProperty(element)) {
                if (optionsParams[element].length == 1) {
                    if (pathParams != '?') pathParams = pathParams + '&';
                    pathParams = pathParams + encodeURI(element.replace(prefixOptionName, 'optionId_')) + "=" + encodeURI(optionsParams[element][0]);
                } else {
                    for (var i = 0; i < optionsParams[element].length; i++) {
                        if (pathParams != '?') pathParams = pathParams + '&';
                        pathParams = pathParams + encodeURI(element.replace(prefixOptionName, 'optionId_')) + '_' + i + "=" + encodeURI(optionsParams[element][i]);
                    }
                }
            }
        }

        this.el.$form.find(`a[href^="${this.data.language.urlSeo}"]`).each((i, element) => {
            $(element).prop('href', $(element).attr('href').split('?')[0] + pathParams);
        });
    },

    /**
     * Change quantity
     * @memberOf LC.buyFormResources
     * @param  {object} eventData
     */
    onChangeQuantity: function (eventData) {
        const quantityValue = $(this.el.$quantityField).val();
        // Before trigger
        this.trigger('onChangeQuantityBefore');

        let totalQuantity = 0;

        if (this.gridData) {
            const combinationId = $(eventData.target).closest('[data-lc-grid-combination]').data('lc-grid-combination').combinationId,
                quantityGrid = parseInt($(eventData.target).closest('select,input').val());
            this.gridData.combinations.values[combinationId].quantity = quantityGrid;
            $(eventData.target).attr('value', quantityGrid);
            totalQuantity = 0;
            $.each(this.gridData.combinations.values, (i, el) => {
                totalQuantity += el.quantity;
            });

            this.setGridTotalQuantity(totalQuantity);
        }

        // Callback trigger
        this.trigger('onChangeQuantityCallback', { quantity: this.gridData ? this.gridData.totalQuantity : parseInt(quantityValue) });

        this.onChange(false);
    },

    /**
     * Set grid total quantity
     * @memberOf LC.buyFormResources
     * @param  {int} totalQuantity
     */
    setGridTotalQuantity: function (totalQuantity) {
        this.gridData.totalQuantity = totalQuantity;
        this.el.$form.find('.product-grid-total-units').html(this.gridData.totalQuantity);
    },

    /**
     * clickStockSubscriptionButton
     * @memberOf LC.buyFormResources
     * @param  {object} eventData
     */
    clickStockSubscriptionButton: function (eventData) {
        $('#stockAlert').find('#combinationId').attr('value', $(eventData.currentTarget).attr("combinationid"));
    },

    /**
     * wishlist initialize method
     * @memberOf LC.buyFormResources
     */
    wishlist: function (wishlist, dataType) {
        const $this = wishlist;
        const data = $this.data('wishlist-' + dataType);
        if (data.type === 'accountRequired') {
            $this.box({
                uid: 'wishlistAccountRequiredModal',
                showFooter: false,
                source:
                    `<div class="question wishlistQuestion lcWishlist">
                        <div class="questionText wishlistQuestionText">${LC.global.languageSheet.wishlistAccountRequired}</div>
                        <div class="questionButtons">
                            <button type="button" class="${BTN_SECONDARY_CLASS} pull-left questionButton questionButtonLeft wishlistQuestionButton" id="wishlistQuestionButton1" data-dismiss="modal" data-bs-dismiss="modal">${LC.global.languageSheet.no}</button>
                            <button type="button" class="${BTN_PRIMARY_CLASS} pull-right questionButton questionButtonRight wishlistQuestionButton" id="wishlistQuestionButton2" onclick="location.href='${LC.global.routePaths.USER}';">${LC.global.languageSheet.yes}</button>
                        </div>
                    </div>`,
                type: 'html',
                showClose: false,
                size: 'small',
                callback: 'wishlistAccountRequired',
            });
        } else {
            $this.on('click', function (event) {
                if (data.type === 'add') {
                    LC.resources.pluginListener('onAddWishList', event, this.data);
                    event.preventDefault();
                    $this.attr('disabled', 'disabled');

                    // Blueknow
                    if (window.BlueknowTracker) BlueknowTracker.trackExamined(data.itemId);

                    this.trigger('wishlistBefore');

                    $.post(
                        LC.global.routePaths.USER_INTERNAL_ADD_WISHLIST_PRODUCT,
                        { data: JSON.stringify({ id: this.data.id }) },
                        this.wishlistCallback.bind(this, data, $this),
                        'json'
                    );
                } else {
                    event.preventDefault();
                    $this.attr('disabled', 'disabled');
                    this.trigger('deleteWishlistItemBefore');
                    $.post(
                        LC.global.routePaths.USER_INTERNAL_DELETE_WISHLIST_PRODUCT,
                        { data: JSON.stringify({ productIdList: data.productIdList }) },
                        this.wishlistCallback.bind(this, data, $this),
                        'json'
                    );
                }
            }.bind(this));
        }
    },

    /**
     * wishlist add/delete ajax callback method
     * @memberOf LC.buyFormResources
     * @param {object} nodeData data from [data-wishlist] trigger items html attribute
     * @param {object} $trigger trigger items
     * @param {object} response ajax object response
     */
    wishlistCallback: function (nodeData, $trigger, response) {
        $trigger.removeAttr('disabled');

        if (response.data.response && response.data.response.success == 1) {
            // Update data
            $trigger.data('wishlist', nodeData);

            // Classes and label
            let label = '';
            if (nodeData.type == 'delete') {
                label = nodeData.labelDeleted;
                $trigger.removeClass('wishlistButtonAdded wishlistButtonDelete').addClass('wishlistButtonAdd');
                this.showMessage(LC.global.languageSheet.wishlistDeleted, 'danger');
                nodeData.type = 'add';
            } else {
                label = nodeData.labelAdded;
                $trigger.addClass('wishlistButtonAdded wishlistButtonDelete').removeClass('wishlistButtonAdd');
                this.showMessage(LC.global.languageSheet.wishlistAdded, 'success');
                nodeData.type = 'delete';
            }

            // Change label
            if (nodeData.showLabel) $trigger.html(label);
        }

        // Callback trigger
        this.trigger('wishlistCallback', nodeData, $trigger, response);
    },

    /**
     * @memberOf LC.buyFormResources
     */
    initShoppingList: function () {
        this.shoppingListRowId = this.el.$form.find('input[name="shoppingListRowId"]').val();

        // ShoppingList init
        this.el.$shoppingListDelete = this.el.$form.find('[data-shopping-list-delete]');
        if (this.el.$shoppingListDelete.length) this.shoppingList(this.el.$shoppingListDelete, 'delete');
        this.el.$shoppingListAdd = this.el.$form.find('[data-shopping-list-add]');
        if (this.el.$shoppingListAdd.length) this.shoppingList(this.el.$shoppingListAdd, 'add');
        this.el.$shoppingListAccountRequired = this.el.$form.find('[data-shopping-list-account_required]');
        if (this.el.$shoppingListAccountRequired.length) this.shoppingList(this.el.$shoppingListAccountRequired, 'account_required');

        // ShoppingLists init        
        this.el.$shoppingListsAccountRequired = this.el.$form.find('[data-shopping-list-lists-account_required]');
        if (this.el.$shoppingListsAccountRequired.length) this.shoppingList(this.el.$shoppingListsAccountRequired, 'lists-account_required');
        this.el.$shoppingListsAdd = this.el.$form.find('[data-shopping-list-lists-add]');
        if (this.el.$shoppingListsAdd.length) this.shoppingList(this.el.$shoppingListsAdd, 'lists-add');
        this.el.$shoppingListsMenuList = this.el.$form.find('[data-shopping-list-lists-menu-list]');
        if (this.el.$shoppingListsMenuList.length) this.shoppingList(this.el.$shoppingListsMenuList, 'lists-menu-list');
        this.el.$shoppingListsNew = this.el.$form.find('[data-shopping-list-lists-new]');
        if (this.el.$shoppingListsNew.length) this.shoppingList(this.el.$shoppingListsNew, 'lists-new');
    },

    /**
     * shoppingList initialize method
     * @memberOf LC.buyFormProperties
     */
    shoppingList: function (shoppingListElement, dataType) {

        var $shoppingListElement = shoppingListElement;
        var data = $shoppingListElement.data('shoppingList-' + dataType);

        if (data.type === 'accountRequired') {
            $shoppingListElement.box({
                uid: 'shoppingListAccountRequiredModal',
                showFooter: false,
                source:
                    `<div class="question shoppingListQuestion lcshoppingList">
                        <div class="questionText shoppingListQuestionText">${LC.global.languageSheet.shoppingListAccountRequired}</div>
                        <div class="questionButtons">
                            <button type="button" class="${BTN_SECONDARY_CLASS} pull-left questionButton questionButtonLeft shoppingListQuestionButton" id="shoppingListQuestionButton1" data-dismiss="modal" data-bs-dismiss="modal">${LC.global.languageSheet.no}</button>
                            <button type="button" class="${BTN_PRIMARY_CLASS} pull-right questionButton questionButtonRight shoppingListQuestionButton" id="shoppingListQuestionButton2" onclick="location.href='${LC.global.routePaths.USER}';">${LC.global.languageSheet.yes}</button>
                        </div>
                    </div>`,
                type: 'html',
                showClose: false,
                size: 'small',
                callback: 'shoppingListAccountRequired',
            });
        } else if (data.type === 'newList') {

            let $boxContent =
                $(`<div class="question shoppingListQuestion lcshoppingList">
                    <div class="questionText shoppingListQuestionText">${LC.global.languageSheet.shoppingListAddNewText}</div>
                    ${data.form}
                </div>`),
                uidModal = 'shoppingListNewListModal_' + this.type;

            if (this.type === 'PRODUCT') {
                uidModal += '_' + this.data.id;
            } else {
                uidModal += '_' + this.dataBundle.grouping.id;
            }

            const form = $boxContent.find('form')[0];
            $(form).attr('data-lc-uidModal', uidModal);

            $shoppingListElement.box({
                uid: uidModal,
                showFooter: false,
                source: $boxContent,
                type: 'html',
                showClose: true,
                size: 'medium',
                keepSrc: true,
                modalClass: 'shoppingListNewListModal',
            });

            $('#' + uidModal).on('show.bs.modal', () => {
                new LC.AddToNewShoppingListForm(form);
            });

            $('#' + uidModal).on('hidden.bs.modal', () => {
                const newList = $('#' + uidModal).attr('data-lc-new-list');
                if (newList) {
                    this.el.$shoppingListsMenuList = $('[data-shopping-list-lists-menu-list]');
                    if (this.el.$shoppingListsMenuList.length) this.shoppingList(this.el.$shoppingListsMenuList, 'lists-menu-list');
                    this.el.$shoppingListsNewElement.click();
                    $('#' + uidModal).removeAttr('data-lc-new-list');
                }
            });

        } else if (data.type === 'list') {
            if (!data.shoppingLists) {
                data.shoppingLists = [];
            }
            LC.global.session.shoppingLists
                .sort((a, b) => a.priority - b.priority)
                .forEach(shoppingList => {
                    if (!data.shoppingLists.includes(shoppingList.id)) {
                        this.el.$shoppingListsNewElement = $('<button />').addClass('dropdown-item shopping-list-lists-add').html(shoppingList.name);
                        let newElementData = { ...data };
                        newElementData.shoppingListId = shoppingList.id;
                        newElementData.type = 'addList';
                        this.el.$shoppingListsNewElement.attr('data-shopping-list-lists-add', JSON.stringify(newElementData));
                        this.shoppingList(this.el.$shoppingListsNewElement, 'lists-add');
                        $shoppingListElement.find('.divider').before($('<li />').append(this.el.$shoppingListsNewElement));
                        data.shoppingLists.push(shoppingList.id);
                    }
                });
        } else if (data.type === 'add' || data.type === 'addList' || data.type === 'delete') {
            $shoppingListElement.on('click', function (event) {
                if (data.type === 'add' || data.type === 'addList') {
                    event.preventDefault();
                    $shoppingListElement.attr('disabled', 'disabled');
                    // Blueknow
                    if (window.BlueknowTracker) BlueknowTracker.trackExamined(data.itemId);
                    this.trigger('shoppingListBefore');

                    if (this.type === 'PRODUCT') {
                        var reference = {
                            id: this.data.id,
                            type: this.type
                        }
                        reference.productOptions = this.getFormData().options;
                    } else {
                        var reference = {
                            id: this.dataBundle.grouping.id,
                            type: this.type
                        }
                        reference.bundleOptions = this.getFormData().items;
                    }

                    let requestData = { reference: reference, type: 'POST' };
                    if (data.shoppingListId) {
                        requestData.shoppingListId = data.shoppingListId;
                    }

                    $.post(
                        LC.global.routePaths.USER_INTERNAL_SET_SHOPPING_LIST_ROW,
                        { data: JSON.stringify(requestData) },
                        this.shoppingListCallback.bind(this, data, $shoppingListElement),
                        'json'
                    );
                } else {
                    event.preventDefault();
                    $shoppingListElement.attr('disabled', 'disabled');
                    this.trigger('deleteShoppingListItemBefore');

                    let requestData = {};
                    if (this.type == 'PRODUCT') {
                        requestData.productIdList = data.id;
                    } else {
                        requestData.bundleIdList = data.id;
                    }

                    $.post(
                        LC.global.routePaths.USER_INTERNAL_DELETE_SHOPPING_LIST_ROWS,
                        { data: JSON.stringify(requestData) },
                        this.shoppingListCallback.bind(this, data, $shoppingListElement),
                        'json'
                    );
                }
            }.bind(this));
        }
    },

    /**
     * Update shoppingListRow method
     * @memberOf LC.buyFormProperties
     */
    updateShoppingListRow: function () {
        let reference = {
            id: this.shoppingListRowId,
            type: this.type
        }
        if (this.type == 'PRODUCT') {
            reference.productOptions = this.getFormData().options
        } else {
            reference.bundleOptions = this.getFormData().items;
        }
        $.post(
            LC.global.routePaths.USER_INTERNAL_SET_SHOPPING_LIST_ROW,
            {
                data: JSON.stringify({
                    reference: reference,
                    type: 'PUT',
                    id: this.shoppingListRowId
                })
            },
            (response) => {
                if (response.data.response.success === 0) {
                    this.showMessage(response.data.response.message, 'danger');
                } else {
                    this.showMessage(response.data.response.message, 'success');
                }
            },
            'json'
        );
    },

    /**
     * shoppingList add/delete ajax callback method
     * @memberOf LC.buyFormProperties
     * @param {object} nodeData data from [data-shopping-list] trigger items html attribute
     * @param {object} $trigger trigger items
     * @param {object} response ajax object response
     */
    shoppingListCallback: function (nodeData, $trigger, response) {
        $trigger.removeAttr('disabled');

        if (response.data.response && response.data.response.success == 1) {
            // Update data
            $trigger.data('shoppingList', nodeData);
            // Classes and label
            var label = '';
            if (nodeData.type == 'delete') {
                label = nodeData.labelDeleted;
                $trigger.removeClass('shoppingListButtonAdded shoppingListButtonDelete').addClass('shoppingListButtonAdd');
                this.showMessage(LC.global.languageSheet.shoppingListDeleted, 'danger');
                nodeData.type = 'add';
                this.el.$shoppingListAdd = this.el.$shoppingListDelete;
                this.el.$shoppingListAdd.attr('data-shopping-list-add', JSON.stringify(nodeData));
            } else if (nodeData.type == 'add') {
                label = nodeData.labelAdded;
                LC.resources.pluginListener('onAddShoppingList', {}, nodeData);
                $trigger.addClass('shoppingListButtonAdded shoppingListButtonDelete').removeClass('shoppingListButtonAdd');
                this.showMessage(LC.global.languageSheet.shoppingListAdded, 'success');
                nodeData.type = 'delete';
            } else if (nodeData.type == 'addList') {
                const defaultShoppingListId = LC.global.session.shoppingLists.find(shoppingList => shoppingList.defaultOne).id,
                    shoppingListId = nodeData.shoppingListId ? nodeData.shoppingListId : defaultShoppingListId,
                    shoppingListName = LC.global.session.shoppingLists.find(shoppingList => shoppingList.id == shoppingListId).name;
                label = nodeData.labelAdded;
                if (defaultShoppingListId === shoppingListId && this.el.$shoppingListAdd.length) {
                    this.el.$shoppingListAdd.addClass('shoppingListButtonAdded shoppingListButtonDelete').removeClass('shoppingListButtonAdd');
                    let data = this.el.$shoppingListAdd.data('shoppingList-add');
                    data.type = 'delete';
                }
                if (response.data.data.incidences.length) {
                    this.showMessage(response.data.response.message, 'danger');
                } else {
                    this.showMessage(LC.global.languageSheet.shoppingListsAdded.replace('{{listName}}', shoppingListName), 'success');
                }
            }

            // Change label
            if (nodeData.showLabel) $trigger.html(label);
        } else {
            this.showMessage(response.data.response.message, 'danger');
        }

        // Callback trigger
        this.trigger('shoppingListCallback', nodeData, $trigger, response);
    },

    /**
     * productComparison initialize method
     * @memberOf LC.buyFormResources
     */
    productComparison: function () {
        const $this = this.el.$productComparison,
            data = $this.data('product-comparison');

        if (data.type === 'add' || data.type === 'delete') {
            $this.on('click', function (event) {
                event.preventDefault();
                $this.attr('disabled', 'disabled');
                let path = '';

                if (data.type === 'add') {
                    this.trigger('addComparisonProductBefore');
                    path = LC.global.routePaths.PRODUCT_COMPARISON_INTERNAL_ADD_COMPARISON_PRODUCT;
                } else {
                    this.trigger('deleteComparisonProductBefore');
                    path = LC.global.routePaths.PRODUCT_COMPARISON_INTERNAL_DELETE_COMPARISON_PRODUCT;
                }

                $.post(
                    path,
                    { data: JSON.stringify({ id: this.data.id }) },
                    this.productComparisonCallback.bind(this, data, $this),
                    'json'
                );
            }.bind(this));
        }
    },

    /**
     * productComparison add/delete ajax callback method
     * @memberOf LC.buyFormResources
     * @param {object} nodeData data from [data-product-comparison] trigger items html attribute
     * @param {object} $trigger trigger items
     * @param {object} response ajax object response
     */
    productComparisonCallback: function (nodeData, $trigger, response) {
        $trigger.removeAttr('disabled');

        let message = LC.global.languageSheet.error,
            success = 0,
            label = '';

        if (typeof response !== 'undefined') {
            if (response.data) {
                message = response.data.response.message;
                success = response.data.response.success ? response.data.response.success : 0;
            }
        }

        // Update data       
        $trigger.data('product-comparison', nodeData);

        // Ajax reload
        LC.productComparisonDetail.reload();
        if (response.data.response.success == 1) {
            // Classes and label
            if (nodeData.type == 'delete') {
                label = nodeData.labelDelete;
                $trigger.removeClass('productComparisonButtonDelete').addClass('productComparisonButtonAdd');
                nodeData.type = 'add';
            } else {
                label = nodeData.labelAdd;
                $trigger.addClass('productComparisonButtonDelete').removeClass('productComparisonButtonAdd');
                nodeData.type = 'delete';
            }
        }

        if (success) {
            LC.notify(message, { type: 'success' });
        } else {
            LC.notify(message, { type: 'danger' });
        }

        // Change label
        if (nodeData.showLabel && label.length) $trigger.html(label);

        // Callback trigger
        this.trigger('productComparisonCallback', nodeData, $trigger, response);
    },

    /**
     * clickStockAlertButton
     * @memberOf LC.buyFormResources
     * @param  {object} eventData
     */
    clickStockAlertButton: function (eventData) {
        $('#stockAlert').find('#combinationId').attr('value', $(eventData.currentTarget).attr("combinationid"));
    },
};


/**
 * BuyForm Properties
 * It has buyForm prototyping functions for buyForm
 * Works with base product Beyond format
 * @property {object} LC.buyFormProperties
 * @memberOf LC
 */
LC.buyFormProperties = {

    /**
     * clickStockSubscriptionButton
     * @memberOf LC.buyFormProperties
     * @param  {object} eventData
     */
    clickStockSubscriptionButton: function (eventData) {
        $('#stockAlert').find('#combinationId').attr('value', $(eventData.currentTarget).attr("combinationid"));
    },

    /**
     * wishlist initialize method
     * @memberOf LC.buyFormProperties
     */
    wishlist: function (wishlist, dataType) {
        var $this = wishlist;
        var data = $this.data('wishlist-' + dataType);
        if (data.type == 'accountRequired') {
            $this.box({
                uid: 'wishlistAccountRequiredModal',
                showFooter: false,
                source:
                    `<div class="question wishlistQuestion lcWishlist">
                        <div class="questionText wishlistQuestionText">${LC.global.languageSheet.wishlistAccountRequired}</div>
                        <div class="questionButtons">
                            <button type="button" class="${BTN_SECONDARY_CLASS} pull-left questionButton questionButtonLeft wishlistQuestionButton" id="wishlistQuestionButton1" data-dismiss="modal" data-bs-dismiss="modal">${LC.global.languageSheet.no}</button>
                            <button type="button" class="${BTN_PRIMARY_CLASS} pull-right questionButton questionButtonRight wishlistQuestionButton" id="wishlistQuestionButton2" onclick="location.href='${LC.global.routePaths.USER}';">${LC.global.languageSheet.yes}</button>
                        </div>
                    </div>`,
                type: 'html',
                showClose: false,
                size: 'small',
                callback: 'wishlistAccountRequired',
            });
        } else {
            $this.on('click', function (event) {
                if (data.type == 'add') {
                    event.preventDefault();
                    $this.attr('disabled', 'disabled');

                    // Blueknow
                    if (window.BlueknowTracker) BlueknowTracker.trackExamined(data.itemId);

                    this.trigger('wishlistBefore');

                    $.post(
                        LC.global.routePaths.USER_INTERNAL_ADD_WISHLIST_PRODUCT,
                        { data: JSON.stringify({ id: this.data.id }) },
                        this.wishlistCallback.bind(this, data, $this),
                        'json'
                    );
                } else {
                    event.preventDefault();
                    $this.attr('disabled', 'disabled');
                    this.trigger('deleteWishlistItemBefore');
                    $.post(
                        LC.global.routePaths.USER_INTERNAL_DELETE_WISHLIST_PRODUCT,
                        { data: JSON.stringify({ productIdList: data.productIdList }) },
                        this.wishlistCallback.bind(this, data, $this),
                        'json'
                    );
                }
            }.bind(this));
        }
    },

    /**
     * wishlist add/delete ajax callback method
     * @memberOf LC.buyFormProperties
     * @param {object} nodeData data from [data-wishlist] trigger items html attribute
     * @param {object} $trigger trigger items
     * @param {object} response ajax object response
     */
    wishlistCallback: function (nodeData, $trigger, response) {
        $trigger.removeAttr('disabled');

        if (response.data.response && response.data.response.success == 1) {
            // Update data
            $trigger.data('wishlist', nodeData);

            // Classes and label
            var label = '';
            if (nodeData.type == 'delete') {
                label = nodeData.labelDeleted;
                $trigger.removeClass('wishlistButtonAdded wishlistButtonDelete').addClass('wishlistButtonAdd');
                this.showMessage(LC.global.languageSheet.wishlistDeleted, 'danger');
                nodeData.type = 'add';
            } else {
                label = nodeData.labelAdded;
                $trigger.addClass('wishlistButtonAdded wishlistButtonDelete').removeClass('wishlistButtonAdd');
                this.showMessage(LC.global.languageSheet.wishlistAdded, 'success');
                nodeData.type = 'delete';
            }

            // Change label
            if (nodeData.showLabel) $trigger.html(label);
        }

        // Callback trigger
        this.trigger('wishlistCallback', nodeData, $trigger, response);
    },

    /**
     * shoppingList initialize method
     * @memberOf LC.buyFormProperties
     */
    shoppingList: function (shoppingList, dataType) {
        var $this = shoppingList;
        var data = $this.data('shoppingList-' + dataType);
        if (data.type == 'accountRequired') {
            $this.box({
                uid: 'shoppingListAccountRequiredModal',
                showFooter: false,
                source:
                    `<div class="question shoppingListQuestion lcshoppingList">
                        <div class="questionText shoppingListQuestionText">${LC.global.languageSheet.shoppingListAccountRequired}</div>
                        <div class="questionButtons">
                            <button type="button" class="${BTN_SECONDARY_CLASS} pull-left questionButton questionButtonLeft shoppingListQuestionButton" id="shoppingListQuestionButton1" data-dismiss="modal" data-bs-dismiss="modal">${LC.global.languageSheet.no}</button>
                            <button type="button" class="${BTN_PRIMARY_CLASS} pull-right questionButton questionButtonRight shoppingListQuestionButton" id="shoppingListQuestionButton2" onclick="location.href='${LC.global.routePaths.USER}';">${LC.global.languageSheet.yes}</button>
                        </div>
                    </div>`,
                type: 'html',
                showClose: false,
                size: 'small',
                callback: 'shoppingListAccountRequired',
            });
        } else {
            $this.on('click', function (event) {
                if (data.type == 'add') {
                    event.preventDefault();
                    $this.attr('disabled', 'disabled');

                    // Blueknow
                    if (window.BlueknowTracker) BlueknowTracker.trackExamined(data.itemId);

                    this.trigger('shoppingListBefore');

                    $.post(
                        LC.global.routePaths.USER_INTERNAL_SET_SHOPPING_LIST_ROW,
                        { data: JSON.stringify({ id: this.data.id }) },
                        this.shoppingListCallback.bind(this, data, $this),
                        'json'
                    );
                } else {
                    event.preventDefault();
                    $this.attr('disabled', 'disabled');
                    this.trigger('deleteshoppingListItemBefore');
                    $.post(
                        LC.global.routePaths.USER_INTERNAL_DELETE_SHOPPING_LIST_ROWS,
                        { data: JSON.stringify({ productIdList: data.id }) },
                        this.shoppingListCallback.bind(this, data, $this),
                        'json'
                    );
                }
            }.bind(this));
        }
    },

    /**
     * shoppingList add/delete ajax callback method
     * @memberOf LC.buyFormProperties
     * @param {object} nodeData data from [data-shopping-list] trigger items html attribute
     * @param {object} $trigger trigger items
     * @param {object} response ajax object response
     */
    shoppingListCallback: function (nodeData, $trigger, response) {
        $trigger.removeAttr('disabled');

        if (response.data.response && response.data.response.success == 1) {
            // Update data
            $trigger.data('shoppingList', nodeData);

            // Classes and label
            var label = '';
            if (nodeData.type == 'delete') {
                label = nodeData.labelDeleted;
                $trigger.removeClass('shoppingListButtonAdded shoppingListButtonDelete').addClass('shoppingListButtonAdd');
                this.showMessage(LC.global.languageSheet.shoppingListDeleted, 'danger');
                nodeData.type = 'add';
            } else {
                label = nodeData.labelAdded;
                $trigger.addClass('shoppingListButtonAdded shoppingListButtonDelete').removeClass('shoppingListButtonAdd');
                this.showMessage(LC.global.languageSheet.shoppingListAdded, 'success');
                nodeData.type = 'delete';
            }

            // Change label
            if (nodeData.showLabel) $trigger.html(label);
        }

        // Callback trigger
        this.trigger('shoppingListCallback', nodeData, $trigger, response);
    },

    /**
     * productComparison initialize method
     * @memberOf LC.buyFormProperties
     */
    productComparison: function () {
        var $this = this.el.$productComparison,
            data = $this.data('product-comparison');

        if (data.type == 'add' || data.type == 'delete') {
            $this.on('click', function (event) {
                event.preventDefault();
                $this.attr('disabled', 'disabled');
                var path = '';

                if (data.type == 'add') {
                    this.trigger('addComparisonProductBefore');
                    path = LC.global.routePaths.PRODUCT_COMPARISON_INTERNAL_ADD_COMPARISON_PRODUCT;
                } else {
                    this.trigger('deleteComparisonProductBefore');
                    path = LC.global.routePaths.PRODUCT_COMPARISON_INTERNAL_DELETE_COMPARISON_PRODUCT;
                }

                // Post
                $.post(
                    path,
                    { data: JSON.stringify({ id: this.data.id }) },
                    this.productComparisonCallback.bind(this, data, $this),
                    'json'
                );
            }.bind(this));
        }
    },

    /**
     * productComparison add/delete ajax callback method
     * @memberOf LC.buyFormProperties
     * @param {object} nodeData data from [data-product-comparison] trigger items html attribute
     * @param {object} $trigger trigger items
     * @param {object} response ajax object response
     */
    productComparisonCallback: function (nodeData, $trigger, response) {
        $trigger.removeAttr('disabled');
        var notifyType = response.data.response.success == 1 ? 'success' : 'danger',
            notifyMsg = response.data.response.message;

        if ('errorCode' in response.data.data) {
            notifyType = 'danger';
        }

        // Update data       
        $trigger.data('product-comparison', nodeData);
        // Ajax reload
        LC.productComparisonDetail.reload();
        if (response.data.response.success == 1 || response.data.data.errorCode === 'A01000-PRODUCT_NOT_FOUND' || response.data.data.errorCode === 'A01000-PRODUCT_COMPARISON_ADD_PRODUCT_EXISTS') {
            // Classes and label
            var label = '';
            if (nodeData.type == 'delete') {
                label = nodeData.labelDelete;
                $trigger.removeClass('productComparisonButtonDelete').addClass('productComparisonButtonAdd');
                nodeData.type = 'add';
            } else {
                label = nodeData.labelAdd;
                $trigger.addClass('productComparisonButtonDelete').removeClass('productComparisonButtonAdd');
                nodeData.type = 'delete';
            }
            if (response.data.data.errorCode === 'A01000-PRODUCT_NOT_FOUND') {
                notifyMsg = LC.global.languageSheet.errorCodeProductComparisonProductNotFound;
            } else if (response.data.data.errorCode === 'A01000-PRODUCT_COMPARISON_ADD_PRODUCT_EXISTS') {
                notifyMsg = LC.global.languageSheet.errorCodeProductComparisonProductAlreadyExists;
            }

        }

        LC.notify(notifyMsg, { type: notifyType });

        // Change label
        if (nodeData.showLabel) $trigger.html(label);

        // Callback trigger
        this.trigger('productComparisonCallback', nodeData, $trigger, response);
    },

    /**
     * clickStockAlertButton
     * @memberOf LC.buyFormProperties
     * @param  {object} eventData
     */
    clickStockAlertButton: function (eventData) {
        $('#stockAlert').find('#combinationId').attr('value', $(eventData.currentTarget).attr("combinationid"));
    }
};

/**
 * LC jQuery Interactor.
 * Generate plugin for DOM interactions
 *
 * @method LC.fn
 * @memberOf LC
 *
 * @param {string} name Name of plugin
 * @param {object} o Object of plugin
 *
 * @example
 *    //Create plugin:
 *    LC.fn('pluginName',{
 *      options : {
 *        enableAnimations : true,
 *      },
 *      Constructor: function(element, options) {
 *
 *        //Public methods
 *        this.init = function() { // it is not necessary
 *          //initialize
 *        };
 *
 *        // This public method, also removes his dataset.
 *        this.destroy = function() {
 *          element.remove();
 *        };
 *        this.clean = function() {
 *          element.html('');
 *        };
 *
 *        //Private methods
 *        function _dummy(){
 *          //I will return nothing.
 *        };
 *      }
 *    });
 *
 *    // Initialize
 *    $('#element').pluginName();
 *
 *    // Destroy or call other public methods
 *    $('#element').pluginName('destroy'[, func arguments]);
 */
LC.fn = function (name, o) {
    (function ($) {
        o.options = $.extend(o.options, {});

        if (!o.Constructor || typeof o.Constructor !== 'function') {
            throw 'Constructor is not defined!';
            return;
        }

        //Create plugin
        $.fn[name] = function (options) {
            // Clone object
            // Object Deep copy (Also you can use JSON.parse(JSON.stringify(o.options))); but then you'll lose the functions
            var defaults = $.extend(true, {}, o.options);

            // method calling
            if (typeof options === 'string') {
                var args = Array.prototype.slice.call(arguments, 1);
                var res;
                this.each(function () {
                    var obj = $.data(this, name);
                    if (obj && $.isFunction(obj[options])) {
                        var r = obj[options].apply(obj, args);
                        if (res === undefined) res = r;
                        if (options == 'destroy') $.removeData(this, name);
                    } else if (obj && obj[options]) res = obj[options];
                });
                if (res !== undefined) return res;

                return this;
            }
            options = $.extend(defaults, options || {});

            this.each(function (i, _element) {
                var element = $(_element);
                // Object Deep copy (Also you can use JSON.parse(JSON.stringify(options))); but then you'll lose the functions
                var obj = new o.Constructor(element, $.extend(true, {}, options));

                element.data(name, obj);

                if (obj.init && typeof obj.init === 'function') obj.init();
            });
            return this;
        };
    })(jQuery);
};

/**
 * @class  LC.Queue (FIFO)
 * @memberOf LC
 */
LC.Queue = function () {
    /**
     * lst Variable List
     * @type {Array}
     */
    var lst = [];

    /**
     * Returns queue
     * @return {Array}
     */
    this.getQueue = function () {
        return lst;
    };

    /**
     * Cleans queue
     * @return {Boolean}
     */
    this.clear = function () {
        lst = [];
        return true;
    };

    /**
     * Enqueues item to list
     * @param  {any} item
     * @return {Boolean}
     *
     * for (var i=0; i<length; i++)
     *      auxLst.push({item:item, priority:priority});
     */
    this.enqueue = function (item, priority) {
        if (priority == null) priority = 0;

        if (lst.length) {
            var auxLst = [];

            while (this.size()) {
                if (lst[0].priority > priority) {
                    break;
                }

                auxLst.push(lst.shift());
            }

            auxLst.push({ item: item, priority: priority });

            for (var i = 0; i > lst.length; i++) {
                auxLst.push(lst[i]);
            }

            lst = auxLst;
        } else {
            lst.push({ item: item, priority: priority });
        }

        return true;
    };

    /**
     * Dequeues item from list and returns it
     * @return {any}
     */
    this.dequeue = function () {
        return lst.pop();
    };

    /**
     * Destacks item from list and returns it
     * @return {any}
     */
    this.destack = function () {
        return lst.shift();
    };

    /**
     * Iterate queue without dequeue it. Each item is lanched by callback
     * @param  {Function} c Callback function
     * @return {void}
     */
    this.iterate = function (c) {
        if (c && typeof c === 'function')
            for (var i = 0; i < lst.length; i++) c(lst[i].item);
    };

    /**
     * Returns size of list
     * @return {Integer}
     */
    this.size = function () {
        return lst.length;
    };
};

/**
 * Create Queue object to queue functions for call on domready
 * @type {LC.Queue}
 */
LC.initQueue = new LC.Queue();

/**
 * Call queued functions on document ready
 */
$(function () {
    while (LC.initQueue.size()) {
        LC.initQueue.destack().item.apply();
    }
});

//------------------------------------------------------------------------------
// FI. No afegir res al final del document. Posem-ho a on toqui :)
//------------------------------------------------------------------------------