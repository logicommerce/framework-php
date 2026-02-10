// Useful LC methods (Add if necessary)
// -----------------------------------------------------------------------------
'use strict';
/**
 * Creates modal
 *
 * @TODO Now it uses BOOTSTRAP component. It will be necessary use "own" modal
 *       component when theme hasn't bootstrap.
 */
LC.fn('box', {
    options: {
        uid: null,
        showFooter: true,
        showHeader: false,
        showClose: true,
        source: '',
        type: 'html', // html | url | internal
        triggerOnClick: true,
        params: false, // data params when type is url
        modalClass: '',
        verticalPosition: true,
        backdrop: true, // "static" (prevent close modal with a click outside modal)
        keyboard: true, // false (prevent close with esc key)
        size: 'medium', // large | medium | small
        callback: null,
        keepSrc: false,
        headerTitle: '',
    },

    Constructor: function (element, options) {
        // Initialize and prepare variables
        var type = element.data('lcModalType') || options.type;

        options.showFooter = element.data('lcModalShowfooter') || options.showFooter;
        options.showHeader = element.data('lcModalShowheader') || options.showHeader;
        options.size = element.data('lcModalSize') || options.size;
        options.triggerOnClick = element.data('lcModalTriggeronclick') || options.triggerOnClick;
        options.verticalPosition = element.data('lcModalVerticalposition') || options.verticalPosition;
        options.keepSrc = element.data('lcModalKeepSrc') || options.keepSrc;

        var modalSize = '';
        if (options.size == 'small') modalSize = ' modal-sm';
        if (options.size == 'large') modalSize = ' modal-lg';

        // Uid
        var uid = element.data('lcModalUid')
        if (!uid) uid = options.uid || String.fromCharCode(65 + Math.floor(Math.random() * 26)) + Date.now();

        // Initialize function
        this.init = function () {
            // Get Data
            var source = options.source || element.data('lcModal');

            // Containers
            var $box = (this.box = _createBox());
            var $container = $($box.find('div.lcModalContainer')[0]);
            // var $close = $($box.find('button.close')[0]);

            // Callback event
            var callback = options.callback || element.data('lcModalCallback');

            // Add target
            element.data('target', '#' + uid);

            // Open (load data)
            if (options.triggerOnClick) {
                element.off('click.boxTrigger').on('click.boxTrigger', (event) => {
                    event.preventDefault();
                    _trigger(type, source, $container, $box, callback);
                });
            } else {
                _trigger(type, source, $container, $box, callback);
                this.dataTrigger = {
                    type: type,
                    source: source,
                    container: $container,
                    box: $box,
                    callback: callback,
                };
            }

            element.data('boxuid', uid);

            // Close (clean)
            if (!options.keepSrc) {
                $box.on('hidden.bs.modal', function (ev) {
                    $container.html('');
                });
            }
        };

        this.close = function () {
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getInstance(this.box).hide();
            } else {
                this.box.modal('hide');
            }
        };

        this.openBox = function () {
            if (this.dataTrigger) {
                var data = this.dataTrigger;
                _trigger(data.type, data.source, data.container, data.box, data.callback);
            }
        };

        function _createBox() {
            var $body = $('body');
            var $box = $body.children('#' + uid);

            // Check box exists
            if (!$box.length) {
                // Create box
                var box = '<div id="' + uid + '" class="lcModal lcModalDynamic modal fade  ' + options.modalClass + '" tabindex="-1" role="dialog" aria-hidden="true">';
                box += '<div class="modal-dialog ' + modalSize + (options.verticalPosition ? ' modal-dialog-centered' : '') + '">';
                box += '<div class="modal-content">';
                if (options.showHeader) {
                    box += `<div class="modal-header">`;
                    if (options.headerTitle.length) {
                        box += `<div class="modal-title">${options.headerTitle}</div>`;
                    }
                    if (options.showClose && options.backdrop != 'static') {
                        box += `<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="${LC.global.languageSheet.close}"></button>`;
                    }
                    box += `</div>`;
                }
                box += '<div class="modal-body">';

                if (options.showClose && options.backdrop != 'static' && !options.showHeader) {
                    box += `<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="${LC.global.languageSheet.close}"></button>`;
                }
                box += '<div class="lcModalContainer"></div>';

                if (options.showFooter && options.backdrop != 'static') {
                    box += '<div class="modal-footer">';
                    box += '<button type="button" class="' + BTN_SECONDARY_CLASS + ' lcModalClose" data-dismiss="modal" data-bs-dismiss="modal">' + LC.global.languageSheet.close + '</button>';
                    box += '</div>';
                }
                box += '</div></div></div></div>';

                $body.append(box);
                $box = $body.children('#' + uid);
            }
            return $box;
        }

        function _trigger(type, source, $container, $box, callback) {
            const modalOptions = { show: true, keyboard: options.keyboard, backdrop: options.backdrop, keepSrc: options.keepSrc };

            switch (type) {
                case 'html':
                    $container.html(source);
                    modal($box, modalOptions);
                    _callback(callback, element, $box);
                    break;

                case 'internal':
                    $container.html(element);
                    modal($box, modalOptions);
                    _callback(callback, element, $box);
                    break;

                case 'url':
                default:
                    const loadCallback = () => {
                        if ($container.html().length > 1) {
                            modal($box, modalOptions);
                            $container.ready(function () {
                                _callback(callback, element, $box);
                            });
                        }
                    };
                    if ((!modalOptions.keepSrc) || (modalOptions.keepSrc && $container.html().length == 0)) {
                        const intTimeStamp = new Date().getTime();
                        source += (source.indexOf('?') > 0 ? '&' : '?') + 'noCache=' + intTimeStamp;
                        $container.load(source, options.params, loadCallback);
                    } else {
                        loadCallback();
                    }
                    break;
            }
        }

        function _callback(callback, element, $box) {
            if (!callback) return;
            if (typeof callback === 'function') callback(element, $box);
            else if (typeof LC.modalCallbacks[callback] === 'function') LC.modalCallbacks[callback](element, $box);
        }

        function modal(element, options) {
            if (typeof bootstrap !== 'undefined') {
                var modalInstance = bootstrap.Modal.getInstance(element) ?? new bootstrap.Modal(element, options);
                modalInstance.show();
            } else {
                element.modal(options);
            }
        }
    },
});

/**
 * Initialize static modal validations and callbacks
 * This function will be called in lc.initialize.js
 * @type {Object}
 */
LC.initializeModals = function () {
    // Validation macro modals required include
    $('[data-lc-required]').each(function (index, el) {
        if (!$($(this).data('lc-required').el).length)
            console.error('Missing "' + $(this).data('lc-required').el + '" modal of "' + $(this).data('lc-required').macro + '" macro!');
    });

    // Static modals callback calls
    $('.lcModal').not('.lcModalDynamic').on('show.bs.modal', function (event) {
        var callbackName = $(this).data('lc-modal-callback');
        if (typeof LC.modalCallbacks[callbackName] === 'function') LC.modalCallbacks[callbackName]($(event.target), $(this));
    });

    $('.legalPopup[data-lc-modal]').box({
        type: "url",
        size: "large",
    });
};

/**
 * @TODO repasar and doc
 * Modal callbacks.
 * This functions will be called when a modal has been loaded.
 * @type {Object}
 */
LC.modalCallbacks = {
    /**
     * Send wishlist to a friend, modal callback.
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    userSendWishlistCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({
                /**
                 * Submit sendWishlistForm modal
                 * @override
                 * @param {object} event submit event object
                 */
                submit: function (event) {
                    event.preventDefault();

                    // Control one product at least is selected
                    if (!this.el.$form.find('input[name="productId"]:checked').length) {
                        LC.notify(LC.global.languageSheet.sendWishlistSelectProductError, { type: 'danger' });
                        return false;
                    }

                    // Call submit from parent class
                    this.superForm('submit', event);
                },

                /**
                 * Submit ajax callback sendWishlistForm modal
                 * @override
                 * @param {object} result ajax response object
                 */
                callback: function (data) {
                    let message = LC.global.languageSheet.error,
                        success = 0;

                    if (typeof data !== 'undefined') {
                        if (data.data) data = data.data;
                        if (data.response) {
                            message = data.response.message;
                            success = data.response.success ? data.response.success : 0;
                        }
                    }

                    if (success) {
                        LC.notify(message, { type: 'success' });
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }

                        modal.find('form')[0].reset();
                        modal.find('.wishlistProduct .active').removeClass('active');
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });

            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);

            modal.find('input[type="checkbox"]').on('click', function () {
                const $label = $(this).closest('label');
                if (this.checked) $label.addClass('active');
                else $label.removeClass('active');

                // Set data input hidden productIdList
                const productIdList = Array.from(modal[0].querySelectorAll('input[type=checkbox]:checked'))
                    .map((el) => el.value)
                    .join(',');

                modal.find('[name="productIdList"]').val(productIdList);
            });
            modal.data('init', true);
        }
    },

    /**
     * Delete wishlist to a friend, modal callback.
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    userDeleteWishlistCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({
                /**
                 * Submit deleteWishlistForm modal
                 * @override
                 * @param {object} event submit event object
                 */
                submit: function (event) {
                    event.preventDefault();
                    // Control one row at least is selected
                    if (!this.el.$form.find('input[name="productIdList"]')[0].value.length) {
                        LC.notify(LC.global.languageSheet.deleteWishlistSelectProductError, { type: 'danger' });
                        return false;
                    }

                    // Call submit from parent class
                    this.superForm('submit', event);
                },

                /**
                 * @TODO update a response php
                 * Submit ajax callback deleteWishlistForm modal
                 * @override
                 * @param {object} result ajax response object
                 */
                callback: function (data) {
                    let message = LC.global.languageSheet.error,
                        success = 0;

                    if (typeof data !== 'undefined') {
                        if (data.data) data = data.data;
                        if (data.response) {
                            message = data.response.message;
                            success = data.response.success ? data.response.success : 0;
                        }
                    }

                    if (success) {
                        LC.notify(message, { type: 'success' });
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }
                        location.reload();
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });

            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);

            modal.find('input[type="checkbox"]').on('click', function () {
                const $label = $(this).closest('label');
                if (this.checked) $label.addClass('active');
                else $label.removeClass('active');

                // Set data input hidden productIdList
                const productIdList = Array.from(modal[0].querySelectorAll('input[type=checkbox]:checked'))
                    .map((el) => el.value)
                    .join(',');

                modal.find('[name="productIdList"]').val(productIdList);
            });
            modal.data('init', true);
        }
    },

    /**
         * Send shoppingList to a friend, modal callback.
         * @param {object} element button that calls modal
         * @param {object} modal   modal jQuery DOM element
         */
    userSendShoppingListRowsCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({
                /**
                 * Submit sendShoppingListRowsForm modal
                 * @override
                 * @param {object} event submit event object
                 */
                submit: function (event) {
                    event.preventDefault();

                    // Control one product at least is selected
                    if (!this.el.$form.find('input[name="shoppingListRow"]:checked').length) {
                        LC.notify(LC.global.languageSheet.sendShoppingListRowsSelectRowError, { type: 'danger' });
                        return false;
                    }

                    // Call submit from parent class
                    this.superForm('submit', event);
                },

                /**
                 * Submit ajax callback sendShoppingListRowsForm modal
                 * @override
                 * @param {object} result ajax response object
                 */
                callback: function (data) {
                    let message = LC.global.languageSheet.error,
                        success = 0;

                    if (typeof data !== 'undefined') {
                        if (data.data) data = data.data;
                        if (data.response) {
                            message = data.response.message;
                            success = data.response.success ? data.response.success : 0;
                        }
                    }

                    if (success) {
                        LC.notify(message, { type: 'success' });
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }

                        const $name = modal.find('input[name="name"]'),
                            $email = modal.find('input[name="email"]'),
                            $nameVal = $name.val(),
                            $emailVal = $email.val();

                        modal.find('form')[0].reset();

                        $name.val($nameVal);
                        $email.val($emailVal);

                        modal.find('.shoppingListRow .active').removeClass('active');
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });

            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);

            modal.find('input[type="checkbox"]').on('click', function () {
                const $label = $(this).closest('label');
                if (this.checked) $label.addClass('active');
                else $label.removeClass('active');

                var items = [];
                modal.find('input[name="shoppingListRow"]:checked').each(function (index, el) {
                    items.push(JSON.parse($(el).val()));
                });

                modal.find('[name="items"]').val(JSON.stringify(items));
            });
            modal.data('init', true);
        }
    },

    /**
     * Delete shoppingList Row
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    userDeleteShoppingListRowsCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({
                /**
                 * Submit deleteShoppingListRowForms modal
                 * @override
                 * @param {object} event submit event object
                 */
                submit: function (event) {
                    event.preventDefault();

                    // Control one product at least is selected
                    if (!this.el.$form.find('input[name="rowIdList"]')[0].value.length &&
                        !this.el.$form.find('input[name="productIdList"]')[0].value.length &&
                        !this.el.$form.find('input[name="bundleIdList"]')[0].value.length) {
                        LC.notify(LC.global.languageSheet.deleteShoppingListSelectRowError, { type: 'danger' });
                        return false;
                    }

                    // Call submit from parent class
                    this.superForm('submit', event);
                },

                /**
                 * @TODO update a response php
                 * Submit ajax callback deleteShoppingListRowForms modal
                 * @override
                 * @param {object} result ajax response object
                 */
                callback: function (data) {
                    let message = LC.global.languageSheet.error,
                        success = 0;

                    if (typeof data !== 'undefined') {
                        if (data.data) data = data.data;
                        if (data.response) {
                            message = data.response.message;
                            success = data.response.success ? data.response.success : 0;
                        }
                    }

                    if (success) {
                        LC.notify(message, { type: 'success' });
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }
                        location.reload();
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });

            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);

            modal.find('input[type="checkbox"]').on('click', function () {
                const $label = $(this).closest('label');
                if (this.checked) $label.addClass('active');
                else $label.removeClass('active');

                // Set data input hidden rowIdList
                const rowIdList = Array.from(modal[0].querySelectorAll('input[name=deleteShoppingListRowId][type=checkbox]:checked'))
                    .map((el) => el.value)
                    .join(',');
                modal.find('[name="rowIdList"]').val(rowIdList);

                // Set data input hidden productIdList
                const productIdList = Array.from(modal[0].querySelectorAll('input[name=deleteShoppingListProductId][type=checkbox]:checked'))
                    .map((el) => el.value)
                    .join(',');
                modal.find('[name="productIdList"]').val(productIdList);

                // Set data input hidden bundleIdList
                const bundleIdList = Array.from(modal[0].querySelectorAll('input[name=deleteShoppingListBundleId][type=checkbox]:checked'))
                    .map((el) => el.value)
                    .join(',');
                modal.find('[name="bundleIdList"]').val(bundleIdList);

            });
            modal.data('init', true);
        }
    },

    /**
     * Edit shoppingList Row notes
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    userEditShoppingListRowNotesCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({

                /**
                 * Submit editShoppingListRowNotesForm modal
                 * @override
                 * @param {object} event submit event object
                 */
                submit: function (event) {
                    event.preventDefault();

                    // Call submit from parent class
                    this.superForm('submit', event);
                },

                /**
                 * Submit ajax callback EditShoppingListRowNotesForm modal
                 * @override
                 * @param {object} result ajax response object
                 */
                callback: function (data) {
                    let message = LC.global.languageSheet.error,
                        success = 0;

                    if (typeof data !== 'undefined') {
                        if (data.data) data = data.data;
                        if (data.response) {
                            message = data.response.message;
                            success = data.response.success ? data.response.success : 0;
                        }
                    }
                    if (success) {
                        LC.notify(message, { type: 'success' });
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }
                        const shoppingListRow = data.data;
                        let importance = shoppingListRow.importance.toLowerCase();
                        importance = importance.charAt(0).toUpperCase() + importance.slice(1);

                        $('[data-lc=editShoppingListRowNotesComment' + shoppingListRow.id + ']').html(shoppingListRow.comment);
                        $('[data-lc=editShoppingListRowNotesQuantity' + shoppingListRow.id + ']').html(shoppingListRow.quantity);
                        $('[data-lc=editShoppingListRowNotesImportance' + shoppingListRow.id + ']').html(LC.global.languageSheet['shoppingListRowImportance' + importance]);
                        $('[data-lc=editShoppingListRowNotesButton' + shoppingListRow.id + ']').data("lc-data", shoppingListRow);
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });
            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);
            modal.data('init', true);
        }
    },


    /**
     * Add shoppingList Row note
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    userAddShoppingListRowNotesCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({

                /**
                 * Submit addShoppingListRowNotesForm modal
                 * @override
                 * @param {object} event submit event object
                 */
                submit: function (event) {
                    event.preventDefault();

                    // Call submit from parent class
                    this.superForm('submit', event);
                },

                /**
                 * Submit ajax callback AddShoppingListRowNotesForm modal
                 * @override
                 * @param {object} result ajax response object
                 */
                callback: function (data) {
                    let message = LC.global.languageSheet.error,
                        success = 0;

                    if (typeof data !== 'undefined') {
                        if (data.data) data = data.data;
                        if (data.response) {
                            message = data.response.message;
                            success = data.response.success ? data.response.success : 0;
                        }
                    }
                    if (success) {
                        LC.notify(LC.global.languageSheet.saved, { type: 'success' });
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }

                        let $newRow = $.parseHTML(JSON.parse(message).replaceAll('&#39;', "'"));
                        $('#' + $('#addShoppingListRowNotesButton').data("lc-rows-container-id")).prepend($newRow);

                        LC.initializeForms();
                        $('[data-lc-quantity]').quantity();
                        $('[data-lc-event]').dataEvent();
                        LC.initializeModals();

                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });
            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);
            modal.data('init', true);
        }
    },

    /**
     * Set shoppingList
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    userSetShoppingListCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({

                /**
                 * Submit SetShoppingListForm modal
                 * @override
                 * @param {object} event submit event object
                 */
                submit: function (event) {
                    event.preventDefault();

                    // Call submit from parent class
                    this.superForm('submit', event);
                },

                /**
                 * Submit ajax callback SetShoppingListForm modal
                 * @override
                 * @param {object} result ajax response object
                 */
                callback: function (data) {
                    let message = LC.global.languageSheet.error,
                        success = 0;

                    if (typeof data !== 'undefined') {
                        if (data.data) data = data.data;
                        if (data.response) {
                            message = data.response.message;
                            success = data.response.success ? data.response.success : 0;
                        }
                    }
                    if (success) {
                        LC.notify(message, { type: 'success' });
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }
                        const shoppingList = data.data;
                        $('[data-lc=setShoppingListName' + shoppingList.id + ']').html(shoppingList.name);
                        $('[data-lc=setShoppingListDescription' + shoppingList.id + ']').html(shoppingList.description);
                        $('[data-lc=setShoppingListKeepPurchasedItems' + shoppingList.id + ']').html(shoppingList.keepPurchasedItems ? LC.global.languageSheet.shoppingListKeepPurchasedItems : '');
                        $('[data-lc=setShoppingListDefaultOne' + shoppingList.id + ']').html(shoppingList.defaultOne ? LC.global.languageSheet.shoppingListDefaultOne : '');
                        $('[data-lc=setShoppingListButton' + shoppingList.id + ']').data("lc-data", shoppingList);
                        if (shoppingList.defaultOne) {
                            $('[data-lc=deleteShoppingListButton' + shoppingList.id + ']').hide();
                        } else {
                            $('[data-lc=deleteShoppingListButton' + shoppingList.id + ']').show();
                        }
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });
            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);
            modal.data('init', true);
        }
    },

    /**
     * @deprecated You must use recommendCallback
     * Send a product to a friend, modal callback.
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    productRecommendCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({
                /**
                 * Submit ajax callback productRecommendForm modal
                 * @override
                 * @param {object} data ajax response object
                 */
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

                    if (success) {
                        LC.notify(message, { type: 'success' });
                        // Close modal
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });

            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);
            modal.data('init', true);
        }
    },

    /**
     * Send a item to a friend, modal callback.
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    recommendCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({
                /**
                 * Submit ajax callback recommendForm modal
                 * @override
                 * @param {object} data ajax response object
                 */
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

                    if (success) {
                        LC.notify(message, { type: 'success' });
                        // Close modal
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });

            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);
            modal.data('init', true);
        }
    },

    /**
     * Send contact form about a product, modal callback.
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    productContactCallback: function (element, modal) {
        const initialized = modal.data('init');
        if (typeof initialized === 'undefined') {
            const Form = LC.Form.extend({
                /**
                 * Submit ajax callback productContactForm modal
                 * @override
                 * @param {object} data ajax response object
                 */
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

                    if (success) {
                        LC.notify(message, { type: 'success' });
                        // Close modal
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modal).hide();
                        } else {
                            modal.modal('hide');
                        }
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                },
            });

            $.validate(LC.validateFormConf);
            new Form(modal.find('form')[0]);
            modal.data('init', true);
        }
    },

    /**
     * Legal check links modal callback
     * @param {object} element 
     * @param {object} modal 
     */
    legalPopup: function (element, modal) {
        $(modal).find('[data-lc-event]').dataEvent();
    },

    /**
     * @TODO deprecated?
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    orderReturnCallback: function (element, modal) {
        modal.on('hidden.bs.modal', function () {
            if (modal.form.hasBeenSubmitted()) location.reload();
        });
        modal.form = new LC.OrderReturnForm(F('#orderReturnForm'));
    },

    /**
     * @TODO implementar
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    newsletterPopup: function (element, modal) { },

    /**
     * @TODO implementar
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    selectCountryPopup: function (element, modal) {
        new LC.CountrySelectorForm($(modal).find('form')[0]);
    },

    /**
     * @TODO implementar
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    confirmAgePopupFormCallback: function (element, modal) {
        new LC.ConfirmAgePopupForm($(modal).find('form')[0]);
    },

    /**
     * @TODO implementar
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    selectMapKind: function (element, modal) {
        new LC.SelectMapKindForm($(modal).find('form')[0]);
    },

    /**
     * @TODO deprecated
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    modalUserAddressBook: function (element, modal) {
        var objUserAddressBookForm = F('#userAddressBookForm');
        new LC.userAddressBookForm(objUserAddressBookForm);
    },

    /**
     * @TODO deprecated?
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    newsletterCustomRegistration: function (element, modal) {
        var objUserForm = F('#userForm');
        new LC.UserForm(objUserForm);
    },

    /**
     * @TODO implementar
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    stockLockPopup: function (element, modal) {
        LC.initializeCountdowns();
        modal.find('[data-lc-event]').dataEvent();
    },

    /**
     * @TODO implementar
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    incidenceFormCallback: function (element, modal) {
        new LC.incidenceForm(modal.find('form')[0]);
    },

    /**
     * @TODO implementar
     * @TODO documentar
     * TEXT DESC
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    settingsUserLostPasswordCallback: function (element, modal) {
        new LC.LostPasswordForm(modal.find('form')[0]);
    },

    /**
     * LC.modalCallbacks.deleteAddressBookConfirmCallback
     * @see LC.addressBookForm.selectAddressEvents
     * 
     * @param {object} element button that calls modal
     * @param {object} modal   modal jQuery DOM element
     */
    deleteAddressBookConfirmCallback: function (element, modal) {
        modal.find('.deleteAddressButtonConfirm').click(function (event) {
            var data = element.data('lc-data');

            if (data.defaultAddress) {
                LC.notify(LC.global.languageSheet.cantDeleteDefaultAddress, { type: 'danger' });
                return false;
            }
            var deleteAddressBookCallback = function (response) {
                if (response.data.response.success === 1) {
                    var addressId = response.data.data.id;
                    var $address = $('#addressBookContainer_' + addressId);
                    var $container = $address.closest('.content');
                    if (typeof bootstrap !== 'undefined') {
                        bootstrap.Modal.getInstance(modal).hide();
                    } else {
                        modal.modal('hide');
                    }
                    $address.remove();
                    if ($container.find('.addressBook').length === 0) {
                        $container.html('<span class="notAvailableAddress">' + LC.global.languageSheet.notAvailableAddress + '</span>');
                    }
                }
            };
            $.post(
                LC.global.routePaths.USER_INTERNAL_DELETE_ADDRESS_BOOK,
                {
                    data: JSON.stringify({ id: data.id })
                },
                deleteAddressBookCallback,
                'json'
            );
        });
    },
};

/**
 * LC Countdown
 * @description Initialize product countdown macro
 * @memberOf LC
 * @param {string} idAttr html attribute id without '#'
 */
LC.productCountdown = function (idAttr) {
    this.init(idAttr);
};

$.extend(true, LC.productCountdown.prototype, {
    counter: 0,
    template: null,
    elementId: null,
    categoryId: null,
    callbackType: 'reload',

    endDate: null,
    currentTime: null,
    diffTime: null,
    duration: null,
    interval: 1000,

    /**
     * Init
     * @memberOf LC.productCountdown
     * @param {string} idAttr html attribute id without '#'
     */
    init: function (idAttr) {
        if (typeof moment === 'undefined') {
            throw "Moment JS library doesn't exist";
        }

        this.$element = $(`#${idAttr}`);
        if (typeof this.$element.data('lc-initialized') !== 'undefined') return
        if (!this.$element.length) return;

        const data = this.$element.data('lc-countdown');
        this.$element.data('lc-initialized', true);

        if (typeof data !== 'object' || !'startDate' in data || !'endDate' in data || !'template' in data) {
            throw "[data-lc-countdown] error";
        }

        if ('callbackType' in data) this.callbackType = data.callbackType;
        if (this.callbackType == 'goBack' && 'categoryId' in data) this.categoryId = data.categoryId;

        this.template = data.template;

        this.endDate = moment(data.endDate).unix();
        this.currentTime = moment(data.startDate).unix();
        this.diffTime = this.endDate - this.currentTime;
        this.duration = moment.duration(this.diffTime * this.interval, 'milliseconds');

        this.$element.find('.countdown-content').html('');

        this.showTime();
    },

    /**
     * Print every second countdown html if duration is gt 0
     * @memberOf LC.productCountdown
     */
    showTime: function () {
        this.duration = moment.duration(this.duration - this.interval, 'milliseconds');

        if (this.duration.as('milliseconds') === 0) {
            switch (this.callbackType) {
                case 'reload':
                    location.reload();
                    break;
                case 'goBack':
                    if (!isNaN(this.categoryId)) location.href = LC.global.routePaths.CATEGORY + this.categoryId.toString();
                    else location.reload();
                    break;
                default:
                    this.$element.find('.countdown-content').html('');
            }
            return;

        }
        this.$element.find('.countdown-content').html(this.formatTime(this.duration));

        setTimeout(() => {
            this.showTime();
        }, this.interval);
    },

    /**
     * Format time with template
     * @memberOf LC.productCountdown
     * @param {object} duration moment js duration object
     */
    formatTime: function (duration) {
        return this.template
            .replace(/{{years}}/gi, duration.years())
            .replace(/{{months}}/gi, duration.months())
            .replace(/{{weeks}}/gi, duration.weeks())
            .replace(/{{days}}/gi, duration.days())
            .replace(/{{hours}}/gi, duration.hours() < 10 ? '0' + duration.hours() : duration.hours())
            .replace(/{{minutes}}/gi, duration.minutes() < 10 ? '0' + duration.minutes() : duration.minutes())
            .replace(/{{seconds}}/gi, duration.seconds() < 10 ? '0' + duration.seconds() : duration.seconds())
    },
});

/**
 * LC Data Events. Call functions from LC.dataEvents
 */
LC.fn('dataEvent', {
    Constructor: function (element, options) {
        var strElementEvent = element.data('lcEvent');
        var strElementFunction = element.data('lcFunction');

        if (typeof LC.dataEvents[strElementFunction] !== 'function') return;

        switch (strElementEvent) {
            case 'click':
                element.click(LC.dataEvents[strElementFunction].bind(this));
                break;
            case 'keypress':
                element.keypress(LC.dataEvents[strElementFunction].bind(this));
                break;
            case 'change':
                element.on('change', LC.dataEvents[strElementFunction].bind(this));
                break;
            case 'load':
                LC.dataEvents[strElementFunction](element, options);
                break;
        }
        // Reseting event to avoid duplicate triggers.
        element.data('lcEvent', null);
    },
});

/**
 * Data Events Object.
 * @type {Object}
 */
LC.dataEvents = {};

/**
 * View document event
 * @param {Object} event
 */
LC.dataEvents.viewDocument = function (event) {
    var data = $(event.currentTarget).data('lc-data'),
        documentPath = '',
        name = '_blank',
        properties = '';

    if (data.pdf === true) {
        if (data.documentType == 'order') documentPath = LC.global.routePaths.RESOURCES_INTERNAL_ORDER_PDF;
        else if (data.documentType == 'deliveryNote') documentPath = LC.global.routePaths.USER_INTERNAL_DELIVERY_NOTE_PDF;
        else if (data.documentType == 'invoice') documentPath = LC.global.routePaths.USER_INTERNAL_INVOICE_PDF;
        else if (data.documentType == 'rma') documentPath = LC.global.routePaths.USER_INTERNAL_RMA_PDF;
        else if (data.documentType == 'return') documentPath = LC.global.routePaths.USER_INTERNAL_RMA_RETURNS_PDF;
        else if (data.documentType == 'correctiveInvoice') documentPath = LC.global.routePaths.USER_INTERNAL_RMA_CORRECTIVE_INVOICE_PDF;
    } else {
        name = 'viewDocument',
            properties = 'menubar=1,resizable=1,scrollbars=1,width=800,height=600';
        if (data.documentType == 'order') documentPath = data.session ? LC.global.routePaths.USER_ORDER + data.documentId : LC.global.routePaths.USER_ORDER + data.documentId + '?token=' + data.token;
        else if (data.documentType == 'deliveryNote') documentPath = LC.global.routePaths.USER_INTERNAL_DELIVERY_NOTE;
        else if (data.documentType == 'returnTracing') documentPath = LC.global.routePaths.USER_INTERNAL_RETURN_TRACING;
        else if (data.documentType == 'invoice') documentPath = LC.global.routePaths.USER_INTERNAL_INVOICE;
        else if (data.documentType == 'returnForm') documentPath = LC.global.routePaths.USER_INTERNAL_RETURN;
        else if (data.documentType == 'rma') documentPath = LC.global.routePaths.USER_INTERNAL_RMA;
        else if (data.documentType == 'return') documentPath = LC.global.routePaths.USER_INTERNAL_RMA_RETURNS;
        else if (data.documentType == 'correctiveInvoice') documentPath = LC.global.routePaths.USER_INTERNAL_RMA_CORRECTIVE_INVOICE;
    }

    if (data.pdf === true || data.documentType != 'order') {
        documentPath = documentPath + '?id=' + data.documentId + (data.session ? '' : '&token=' + data.token);
    }

    window.open(documentPath, name, properties);
};

/**
 * Get return request form
 * @param {Object} event
 */
LC.dataEvents.getReturnRequestForm = function (event) {
    const data = $(event.currentTarget).data('lc-data');
    const documentPath = LC.global.routePaths.USER_INTERNAL_RETURN + '?id=' + data.documentId + (data.session ? '' : '&token=' + data.token);
    const url = documentPath;
    if (data.isAccount) {
        var returnOrderPopup = $(`#returnProductsPopup .lcModalContainer`).html(DEFAULT_LOADING_SPINNER);
    } else {
        var returnOrderPopup = $(`#returnProductsPopup${data.documentId} .lcModalContainer`).html(DEFAULT_LOADING_SPINNER);
    }

    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (data) {
                returnOrderPopup.html(data);
                const form = returnOrderPopup.find('[data-lc-form="returnRequestForm"]')[0];
                if (typeof form !== 'undefined') {
                    new LC.ReturnRequestForm(form);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
};

/**
 * Get return request form
 * @param {Object} event
 */
LC.dataEvents.returnTracingForm = function (event) {
    const data = $(event.currentTarget).data('lc-data');
    const documentPath = LC.global.routePaths.USER_INTERNAL_RETURN_TRACING_FORM + '?id=' + data.documentId + (data.session ? '' : '&token=' + data.token);
    const url = documentPath;

    if (data.isAccount) {
        var returnOrderPopup = $(`#returnTracingPopup #returnTracingContentPopup`).html(DEFAULT_LOADING_SPINNER);
    } else {
        var returnOrderPopup = $(`#returnTracingPopup${data.documentId} #returnTracingContentPopup`).html(DEFAULT_LOADING_SPINNER);
    }

    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (data) {
                returnOrderPopup.html($(data).find('.content-modules').html());
                returnOrderPopup.find('[data-lc-event]').dataEvent();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
};

/**
* Login with oauth plugin
* @param {Object} event
*/
LC.dataEvents.oauth = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    var name = '_blank';
    var documentPath = LC.global.routePaths.USER_OAUTH + '?pluginModule=' + data.plugin;
    var properties = 'menubar=1,resizable=1,scrollbars=1,width=800,height=600';
    window.open(documentPath, name, properties);
}

/**
* Login with oauth plugin
* @param {Object} event
*/
LC.dataEvents.expressCheckout = function (event) {
    event.preventDefault();
    var lcData = $(event.currentTarget).data('lc-data');
    $.post(
        LC.global.routePaths.CHECKOUT_INTERNAL_EXPRESS_CHECKOUT,
        {
            data: JSON.stringify({ id: lcData.id, action: lcData.action, pluginModule: lcData.pluginModule })
        },
        (data) => {
            lcData.preventSubmit = false;
            LC.resources.pluginListener('beforeExpressCheckoutRedirect', event, data, lcData);
            if (lcData.preventSubmit == true) {
                return;
            }
            if (data.data.response.success === 1 && data.data.data.redirectUrl && data.data.data.redirectUrl.length > 0) {
                window.location.href = data.data.data.redirectUrl;
                return;
            } else {
                LC.notify(data.data.response.message, { type: 'danger' });
            }
        },
        'json'
    );
}

/**
* View multiexpedition
* @param {Object} event
*/
LC.dataEvents.viewAllShipments = function (event) {
    event.preventDefault();

    const data = $(event.currentTarget).data('lc-data'),
        documentPath = LC.global.routePaths.USER_INTERNAL_ORDER_SHIPMENTS,
        popup = $('#viewShipmentsOrder' + data.documentId).html(DEFAULT_LOADING_SPINNER),
        url = documentPath + '?id=' + data.documentId;

    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                popup.html(html);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}

/**
* Return document view in popup format
* @param {Object} event
*/
LC.dataEvents.viewDocumentPopup = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    var documentType = data.documentType;
    var documentPath;
    var $documentPopupTitle;
    var documentPopupTitle;
    switch (documentType) {
        case 'order':
            documentPath = LC.global.routePaths.USER_ORDER;
            $documentPopupTitle = $('#popupOrder #popupOrderTitle');
            documentPopupTitle = data.popupTitle + ' ' + data.documentNumber;
            break;
        case 'invoice':
            documentPath = LC.global.routePaths.USER_INTERNAL_INVOICE;
            $documentPopupTitle = $('#popupInvoice #popupInvoiceTitle');
            documentPopupTitle = data.popupTitle + ' ' + data.documentNumber;
            break;
        case 'rma':
            documentPath = LC.global.routePaths.USER_INTERNAL_RMA;
            break;
        case 'return':
            documentPath = LC.global.routePaths.USER_INTERNAL_RMA_RETURNS;
            break;
        case 'correctiveInvoice':
            documentPath = LC.global.routePaths.USER_INTERNAL_RMA_CORRECTIVE_INVOICE;
            break;
    }

    var url = documentPath;
    if (documentType != 'order') {
        url += '?id=' + data.documentId;
    } else {
        url += data.documentId;
    }
    if (data.isAccount) {
        var popupPath = '#viewpopup' + documentType;
        if ($documentPopupTitle && documentPopupTitle) {
            $documentPopupTitle.text(documentPopupTitle);
        }
    } else {
        var popupPath = '#viewpopup' + documentType + data.documentId;
    }
    var popup = $(popupPath).html(DEFAULT_LOADING_SPINNER);
    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                var document = $(html).find('.document');
                popup.html(document);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
};

LC.dataEvents.accountSalesAgentSales = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    var tokenParam = (data.session ? '' : '&token=' + data.token);
    var url = `${LC.global.routePaths.ACCOUNT_INTERNAL_SALES_AGENT_CUSTOMER_ORDERS}?customerId=${data.customerId}&fromDate=${moment(data.fromDate).format('YYYY-MM-DD')}&toDate=${moment(data.toDate).format('YYYY-MM-DD')}${tokenParam}`;
    var popup = $('#salesAgentSales' + data.customerId).html(DEFAULT_LOADING_SPINNER);

    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                var salesAgentCustomersOrders = $(html).find('.salesAgentCustomersOrders');
                popup.html(salesAgentCustomersOrders);
                popup.find('[data-lc-event]').dataEvent();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}

LC.dataEvents.usedAccountSwitch = function (event) {
    event.preventDefault();
    var url = `${LC.global.routePaths.USED_ACCOUNT_SWITCH}`;
    var popup = $('#usedAccountSwitch').html(DEFAULT_LOADING_SPINNER);

    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                var usedAccountSwitchLinks = $(html).find('.usedAccountSwitchLinks');
                popup.html(usedAccountSwitchLinks);
                popup.find('[data-lc-event]').dataEvent();

                new LC.UsedAccountSwitchForm($(usedAccountSwitchLinks).find('form'));
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}

/**
 * @deprecated You must use accountSalesAgentSales
 */
LC.dataEvents.salesAgentSales = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    var tokenParam = (data.session ? '' : '&token=' + data.token);
    var url = `${LC.global.routePaths.USER_INTERNAL_SALES_AGENT_CUSTOMER_ORDERS}?customerId=${data.customerId}&fromDate=${moment(data.fromDate).format('YYYY-MM-DD')}&toDate=${moment(data.toDate).format('YYYY-MM-DD')}${tokenParam}`;
    var popup = $('#salesAgentSales' + data.customerId).html(DEFAULT_LOADING_SPINNER);

    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                var salesAgentCustomersOrders = $(html).find('.salesAgentCustomersOrders');
                popup.html(salesAgentCustomersOrders);
                popup.find('[data-lc-event]').dataEvent();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}

LC.dataEvents.salesAgentSaleOrder = function (event) {
    event.preventDefault();
    var name = '_blank';
    var data = $(event.currentTarget).data('lc-data');
    var documentPath = LC.global.routePaths.USER_ORDER + data.documentId + (data.session ? '' : '&token=' + data.token);
    var properties = 'menubar=1,resizable=1,scrollbars=1,width=1100,height=800';
    window.open(documentPath, name, properties);
}

/**
 * Recover order event
 * @param {Object} event
 */
LC.dataEvents.recoverOrder = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    $.post(
        LC.global.routePaths.USER_INTERNAL_RECOVERY_BASKET + '?id=' + data.documentId,
        {},
        function (data) {
            if (data.data.response.success === 1) {
                location.href = LC.global.routePaths.CHECKOUT_BASKET;
            }
        },
        'json'
    );
};

/**
 * Set Currency event
 * @param {Object} event
 */
LC.dataEvents.setCurrency = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    $.post(
        LC.global.routePaths.USER_INTERNAL_SET_CURRENCY,
        {
            data: JSON.stringify({
                code: escape(data.code)
            }),
        },
        function (data) {
            if (data.data.response.success === 1) {
                location.reload();
            }
        },
        'json'
    );
};

/**
 * Delete product comparison event
 * @param {Object} event
 */
LC.dataEvents.deleteProductComparison = function (event) {
    event.preventDefault();
    var id = $(event.currentTarget).data('lc-data');
    $.post(
        LC.global.routePaths.PRODUCT_COMPARISON_INTERNAL_DELETE_COMPARISON_PRODUCT,
        { data: JSON.stringify({ id: id }) },
        function (data) {
            var notifyType = data.data.response.success == 1 ? 'success' : 'danger',
                notifyMsg = data.data.response.message;

            if (data.data.response.success === 1) {
                LC.productComparisonDetail.deleteTableItem(id);
                LC.productComparisonDetail.reload();
            }

            if ('errorCode' in data.data.data) {
                notifyType = 'danger';
            }
            if (data.data.data.errorCode === 'A01000-PRODUCT_NOT_FOUND') {
                notifyMsg = LC.global.languageSheet.errorCodeProductComparisonProductNotFound;
            } else if (data.data.data.errorCode === 'A01000-PRODUCT_COMPARISON_ADD_PRODUCT_EXISTS') {
                notifyMsg = LC.global.languageSheet.errorCodeProductComparisonProductAlreadyExists;
            }
            LC.notify(notifyMsg, { type: notifyType });
        },
        'json'
    );
};

/**
 * Logout event
 * @param {Object} event
 */
LC.dataEvents.logout = function (event) {
    event.preventDefault();
    $.post(
        LC.global.routePaths.USER_INTERNAL_LOGOUT,
        {},
        function (data) {
            if (data.data.response.success === 1) {
                location.href = LC.global.routePaths.HOME;
            }
        },
        'json'
    );
};

/**
 * @deprecated You must use accountLoginSimulation
 * Login simulation event
 * @param {Object} event
 */
LC.dataEvents.loginSimulation = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    $.post(
        LC.global.routePaths.USER_INTERNAL_LOGIN_SIMULATION + '?customerId=' + data.customerId,
        {},
        function (data) {
            if (data.data.response.success === 1) {
                location.href = LC.global.routePaths.HOME;
            }
        },
        'json'
    );
};

/**
 * Login simulation event
 * @param {Object} event
 */
LC.dataEvents.accountLoginSimulation = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    $.post(
        LC.global.routePaths.ACCOUNT_INTERNAL_LOGIN_SIMULATION + '?customerId=' + data.customerId,
        {},
        function (data) {
            if (data.data.response.success === 1) {
                location.href = LC.global.routePaths.HOME;
            }
        },
        'json'
    );
};

/**
 * @deprecated You must use accountLogoutSimulation
 * Loggout simulation event
 * @param {Object} event
 */
LC.dataEvents.logoutSimulation = function (event) {
    event.preventDefault();
    $.post(
        LC.global.routePaths.USER_INTERNAL_LOGOUT_SIMULATION,
        {},
        function (data) {
            if (data.data.response.success === 1) {
                location.href = LC.global.routePaths.HOME;
            }
        },
        'json'
    );
};

/**
 * Loggout simulation event
 * @param {Object} event
 */
LC.dataEvents.accountLogoutSimulation = function (event) {
    event.preventDefault();
    $.post(
        LC.global.routePaths.ACCOUNT_INTERNAL_LOGOUT_SIMULATION,
        {},
        function (data) {
            if (data.data.response.success === 1) {
                location.href = LC.global.routePaths.HOME;
            }
        },
        'json'
    );
};

/**
 * LC Quantity number.
 */
LC.fn('quantity', {
    Constructor: function (element, options) {
        this.fieldName = LC.uniqueId.get(element.data('lcQuantity'));
        this.init = function () {
            this.forceMinZero = element.data('force-min-zero');
            this.minValue = parseInt(element.attr('min'));
            this.maxValue = parseInt(element.attr('max'));
            this.multipleValue = parseInt(element.attr('multipleValue')) || 1;
            this.multipleFrom = parseInt(element.attr('multipleFrom')) || 1;
            const quantityLabel = element.attr('aria-label') || (LC.global && LC.global.languageSheet && LC.global.languageSheet.quantity) || 'Quantity';
            if (!element.attr('aria-label')) {
                element.attr('aria-label', quantityLabel);
            }

            // Create vars
            this.$container = $('<div/>', { class: 'input-group input-group-quantity' });
            this.$minus = $('<span/>', { class: 'input-group-btn' });
            this.$plus = $('<span/>', { class: 'input-group-btn' });

            this.$minusBtn = $('<button />', {
                type: 'button',
                class: BTN_SECONDARY_CLASS + ' btn-number',
                'data-type': 'minus',
                'data-field': this.fieldName,
                html: '<span class="glyphicon glyphicon-minus"></span>',
            })
                .attr('aria-label', `${quantityLabel} -`)
                .appendTo(this.$minus)
                .click(this.clickBtn.bind(this));

            this.$plusBtn = $('<button />', {
                type: 'button',
                class: BTN_SECONDARY_CLASS + ' btn-number',
                'data-type': 'plus',
                'data-field': this.fieldName,
                html: '<span class="glyphicon glyphicon-plus"></span>',
            })
                .attr('aria-label', `${quantityLabel} +`)
                .appendTo(this.$plus)
                .click(this.clickBtn.bind(this));

            // Add name attribute if element does not have it...
            if (!element[0].hasAttribute('name')) {
                element.attr({ name: this.fieldName });
            }
            // Prepare element
            element
                .attr('type', 'text')
                .addClass(FORM_CONTROL_CLASS + ' input-number')
                .after(this.$container)
                .change(this.changeField.bind(this))
                .focus(this.focusField.bind(this))
                .blur(this.changeField.bind(this));

            // Prevents to relaunch init
            element.removeAttr('data-lc-quantity');

            // Append $container
            this.$container
                .append(this.$minus)
                .append(element)
                .append(this.$plus);
        };

        this.destroy = function () {
            this.$container.after(element);
            this.$container.remove();
        };

        this.clickBtn = function (event) {
            event.preventDefault();

            var $this = $(event.currentTarget),
                type = $this.attr('data-type'),
                currentVal = parseInt(element.val()),
                resultVal = !isNaN(currentVal) ? currentVal : this.minValue,
                difference = parseInt(currentVal) + parseInt(this.multipleValue),
                difference = difference % this.multipleValue;

            if (!isNaN(currentVal)) {
                if (type === 'minus') {
                    if (currentVal <= this.minValue) {
                        if (this.forceMinZero) {
                            resultVal = 0;
                        } else {
                            resultVal = this.minValue;
                        }
                    } else if (currentVal > this.minValue) {
                        if (this.multipleValue > 1) {
                            if (difference != 0) {
                                var value = currentVal - difference;
                            } else {
                                var value = currentVal - this.multipleValue - difference;
                            }
                            if (value < this.multipleFrom && currentVal > this.multipleFrom) {
                                resultVal = this.minValue;
                            } else if (value >= this.minValue) {
                                if (this.multipleFrom <= value) {
                                    resultVal = value;
                                } else {
                                    resultVal = currentVal - 1;
                                }
                            } else {
                                resultVal = currentVal - 1;
                            }
                        } else {
                            resultVal = currentVal - 1;
                        }
                    }
                } else if (type === 'plus') {
                    if (currentVal < this.minValue) {
                        resultVal = this.minValue;
                    } else if (currentVal < this.maxValue) {
                        if (this.multipleValue > 1) {
                            var value = (currentVal + this.multipleValue) - difference;
                            if (value <= this.maxValue) {
                                if (this.multipleFrom <= value && (currentVal + 1) >= this.multipleFrom) {
                                    resultVal = value;
                                } else {
                                    resultVal = currentVal + 1;
                                }
                            }
                        } else {
                            resultVal = currentVal + 1;
                        }
                    }
                }
                $this.attr('disabled', resultVal <= this.minValue);
                $this.attr('disabled', resultVal >= this.maxValue);
            }
            element.val(resultVal).change();
        };

        this.focusField = function () {
            element.data('oldValue', element.val());
            this.oldValue = element.val();
        };

        this.isValidCurrentValue = function (currentVal) {
            if (currentVal > this.maxValue) {
                return false;
            }
            if (currentVal < this.minValue) {
                return this.forceMinZero && currentVal === 0;
            }
            if (this.multipleValue > 1) {
                if (this.multipleFrom) {
                    if (currentVal >= this.multipleFrom) {
                        return currentVal % this.multipleValue === 0;
                    }
                    return true;
                }
                return currentVal % this.multipleValue === 0;
            }
            return true;
        };

        this.changeField = function () {
            var currentVal = parseInt(element.val());
            if (currentVal >= this.minValue) {
                this.$minusBtn.removeAttr('disabled');
            }
            if (currentVal <= this.maxValue) {
                this.$plusBtn.removeAttr('disabled');
            }
            if (!this.isValidCurrentValue(currentVal)) {
                if (this.oldValue != undefined) {
                    element.val(this.oldValue);
                }
            }
        };
    },
});

/**
 * LC notify
 */
LC.fn('notify', {
    options: LC.config.notify,

    Constructor: function (element, options) {
        var notify = this;

        /**
         * Initialize
         */
        notify.init = function () {
            // Force view when the view is mobile
            if (LC.global.settings.isMobile === true) options.type = 'info';

            element.addClass('lcNotify lcNotify-' + options.type);

            // Creates a notification notes counter so each notification can be identified
            notify.notes = 0;
        };

        /**
         * Show
         * Create a new notification
         *
         * @param message {String} Contains the message for the notification
         * @param opts {Object} Contains the LC.global.settings for the notification
         */
        notify.show = function (opts) {
            var opts = $.extend(
                {
                    id: 'note-' + notify.notes++,
                    type: false,
                    title: false,
                    message: '',
                    sticky: false,
                    speed: options.speed,
                    delay: options.delay,
                    easing: options.easing,
                    effect: options.effect,
                    icon: false,
                    successIcon: options.successIcon,
                    dangerIcon: options.dangerIcon,
                    removeIcon: options.removeIcon,
                },
                opts
            );

            // New notification is created
            var $note = $('<div />', {
                class: 'note ' + opts.id + (opts.type ? ' note-' + opts.type : ''),
            });

            // Icon is attached to the new notification if one is specified
            if (opts.icon) {
                $('<span />', {
                    class: 'icon',
                    html: $(opts.icon),
                }).appendTo($note);
            }

            // Specyfic type icon defined
            if (opts.type === 'success' && opts.successIcon.length) {
                $(opts.successIcon).prependTo($note);
            }
            if (opts.type === 'danger' && opts.dangerIcon.length) {
                $(opts.dangerIcon).prependTo($note);
            }

            // New content container is created for the notification
            var $content = $('<div>', {
                class: 'content',
            });

            // Add a notification title if one is specified
            if (opts.title) {
                $('<strong />', {
                    class: 'title',
                    html: opts.title,
                }).appendTo($content);
            }

            // Notification message is added to the content container
            $content.append(opts.message);

            // Content container is added to the notification
            $note.append($content);

            // Add remove button for the notification
            $('<button />', {
                type: 'button',
                class: 'notifyRemove',
                html: opts.removeIcon,
            }).appendTo($note);

            // Notification is added to the notification parent container
            element.prepend($note);

            // Ignore the close timer if the notification is a sticky
            if (!opts.sticky) {
                // Create a new close timer for the notification
                var noteTimer = new closeTimer($note, opts);

                // Pause the close timer if the mouse is over the notification
                element.on('mouseover', '.' + opts.id, function () {
                    noteTimer.pause();
                });

                // Resume the close timer from the paused position when the mouse is moved away from the notification
                element.on('mouseout', '.' + opts.id, function () {
                    noteTimer.resume();
                });
            }

            // Remove the notification if the remove button has been clicked
            element.on('click', '.notifyRemove', function () {
                $(this)
                    .closest('.note')
                    .animate({ opacity: 0 }, opts.speed, opts.easing)
                    .slideUp(opts.speed, function () {
                        $(this).remove();
                    });
            });
        };

        /**
         * Close
         * Closes the notification using the option effect
         *
         * @param content {Object} Contains the element that needs to be closed & removed
         * @param opts    {Object} Contains all the options associated with the particular notification
         */
        notify.close = function (content, opts) {
            // Determine which effect was specified and use run the appropriate close code
            switch (opts.effect) {
                case 'fade':
                    content
                        .animate(
                            {
                                opacity: 0,
                            },
                            opts.speed,
                            opts.easing
                        )
                        .slideUp(opts.speed, function () {
                            $(this).remove();
                        });
                    break;

                case 'slide':
                    content.slideToggle(opts.speed, function () {
                        $(this).remove();
                    });
                    break;

                default:
                    content.toggle(opts.speed, function () {
                        $(this).remove();
                    });
                    break;
            }
        };

        /**
         * Close Timer (private)
         * Takes in an element and its options to create a timeout function to close the notification
         *
         * @param element {Object} Contains the element that needs to be closed
         * @param opts    {Object} Contains all the options associated with the particular notification
         */
        function closeTimer(content, opts) {
            // Creates a unique id, start and remaining time using the option's delay var
            var timerId,
                start,
                remaining = opts.delay;

            // Pause function that clears the timeout and determines what the remaining time before the close is
            this.pause = function () {
                window.clearTimeout(timerId);
                remaining -= new Date() - start;
            };

            // Resume function starts the timeout function which will fire the close prototype function
            this.resume = function () {
                start = new Date();
                timerId = window.setTimeout(function () {
                    notify.close(content, opts);
                }, remaining);
            };

            // 3, 2, 1 ... START!
            this.resume();
        }
    },
});

LC.initQueue.enqueue(function () {
    var $notifyElement = $('#lcNotify');
    if (!$notifyElement.length) throw 'Notify error, container element "#lcNotify" not found!';
    var notifyElement = $notifyElement.notify(LC.config.notify);
    LC.notify = function (message, options) {
        notifyElement.notify('show', $.extend({ message: message }, options));
    };
});

LC.initQueue.enqueue(function () {
    $.each(LC.global.languageSheet, function (index, value) {
        if (index.startsWith('JsFormUtils_')) {
            $.formUtils.LANG[index.replace("JsFormUtils_", "")] = value;
        }
    });
});

/**
 * LC.countdown 
 * @param {Object} element
 */
LC.countdown = function (options) {
    this.init = function () {
        if (typeof moment === 'undefined') {
            throw "Moment JS library doesn't exist";
        }

        //this.startDate = moment(options.startDate).unix();
        this.endDate = moment(options.endDate).unix();
        this.callback =
            options.callback ||
            function () {
                this.$container.remove();
            }.bind(this);
        //this.offset = moment().unix() - this.startDate;
        //this.offset = LC.global.settings.localServerOffsetTime;

        this.$container = options.container || $('<span></span>');
        this.template = options.template || this.$container.html() || '{{hours}}:{{minutes}}:{{seconds}}';

        this.interval = setInterval(this.draw.bind(this), 1000);

        this.draw();
    };

    this.draw = function () {
        var JSDate = moment().unix();// - this.offset;
        var currentDate = this.endDate - JSDate;

        if (currentDate < 0) {
            this.destroy();
            this.callback();
            return;
        }

        var stcTimeObj = this.formatTime(currentDate);

        this.$container.html(
            this.template
                .replace(/{{days}}/gi, stcTimeObj.days)
                .replace(/{{hours}}/gi, stcTimeObj.hours)
                .replace(/{{minutes}}/gi, stcTimeObj.minutes)
                .replace(/{{seconds}}/gi, stcTimeObj.seconds)
        );
    };

    this.formatTime = function (ms) {
        var two = function (x) {
            return (x > 9 ? '' : '0') + x;
        };

        var response = {};
        response.days = parseInt(ms / 60 / 60 / 24);
        response.hours = two(parseInt(ms / 60 / 60) % 24);
        response.minutes = two(parseInt(ms / 60) % 60);
        response.seconds = two(parseInt(ms) % 60);

        return response;
    };

    this.destroy = function () {
        clearInterval(this.interval);
    };

    this.init();
};

/**
 * LC.basketCountdown 
 * @param {Object} element
 */
LC.basketCountdown = function (element) {
    element = $(element);
    var $elActive = element.find('.active'),
        options = {
            container: $elActive,
            endDate: element.attr('data-lc-basket-expires'),
            callback: function () {
                element.removeClass('active').addClass('expired');
            },
        };

    if (!element.hasClass('expired')) {
        element.addClass('active');
    }

    return new LC.countdown(options);
};

/**
 * @TODO implement
 * LC.basketLockCountdown 
 * @param {Object} element
 */
LC.basketLockCountdown = function (element) {
    element = $(element);

    var options = {
        container: element,
        endDate: element.data('lock-countdown').expires,
        callback: function () {
            $(document.body).addClass('basket-check-availability');

            var dummy = $('<span/>');
            var button = $('<button></button>')
                .html(languageSheet.lockedStockCheckAvailability)
                .attr('data-lc-event', 'click')
                .attr('data-lc-function', 'updateBasket')
                .attr('data-data', '{"action":"recalculate"}')
                .appendTo(dummy);

            element.html(
                languageSheet.lockedStockCheckProductAvailability
                    .replace(/{{name}}/gi, element.data('lock-countdown').productName)
                    .replace(/{{button}}/gi, dummy.html())
            );
            element.find('[data-lc-event]').dataEvent();
        },
    };

    return new LC.countdown(options);
};

/**
 * LC basketExpiration
 */
LC.basketExpiration = {
    expirationTimeout: 0,
    expirationPopupTimeout: 0,
    warningPopupTimeout: 0,
    initialized: false,
    warningBox: null,
    init: function () {
        if (this.initialized)
            return;
        if (!(LC.global.settings.basketStockLocking.active && logicommerceGlobal.session.lockedStocksAggregateData.length))
            return;
        this.initialized = true;
        const now = moment();
        const closestExpiresAt = logicommerceGlobal.session.lockedStocksAggregateData
            .filter(item => item.expiresAt)
            .map(item => ({
                ...item,
                expiresAtDate: moment(item.expiresAt) // Convertimos expiresAt a moment
            }))
            .reduce((closest, current) => {
                return (!closest || (current.expiresAtDate.diff(now) > 0 && current.expiresAtDate.diff(now) < closest.expiresAtDate.diff(now)))
                    ? current
                    : closest;
            }, null);
        if ($('[data-lc-basket-expires]').length) {
            this.checkNewExpirationDate();
        } else {
            this.setExpirationDate(closestExpiresAt.expiresAt);
        }
    },
    setExpirationDate: function (expDate) {
        if (!this.initialized) return;
        var remainingSeconds = moment(expDate)
            // .add(LC.global.settings.localServerOffsetTime, 'seconds')
            .diff(moment(), 'seconds'),
            expired = false;
        clearTimeout(this.expirationTimeout);
        clearTimeout(this.expirationPopupTimeout);
        clearTimeout(this.warningPopupTimeout);
        this.expirationTimeout = setTimeout(this.expireBasket, remainingSeconds * 1000);
        if (themeConfiguration?.commerce?.lockedStock.expiredPopup) {
            if (remainingSeconds > 0) {
                this.expirationPopupTimeout = setTimeout(this.adviceExpireBasket, remainingSeconds * 1000);
                expired = true;
            } else {
                this.adviceExpireBasket();
                expired = true;
            }
        }
        if (themeConfiguration?.commerce?.lockedStock.warningPopup) {
            const remainingWarningSeconds = remainingSeconds - themeConfiguration?.commerce?.lockedStock.warningPopupTimeThreshold * 60;
            if (remainingWarningSeconds > 0) {
                this.warningPopupTimeout = setTimeout(this.warnExpireBasket, remainingWarningSeconds * 1000);
            } else if (remainingWarningSeconds <= 0 && !expired) {
                this.warnExpireBasket();
            }
        }
    },
    checkNewExpirationDate: function () {
        const stockLockData = $('[data-lc-basket-expires]').data('lc-basket-expires');
        if (!stockLockData) return;
        LC.basketExpiration.setExpirationDate(stockLockData);
    },
    expireBasket: function () {
        $(document.body).addClass('basket-expired');
        $('[data-lc-basket-expired="remove"]').remove();
    },
    adviceExpireBasket: function () {
        if (!readCookie('closedPopupBasketExpired')) {
            $('#popupWarningBasketExpires').modal('hide');
            $(document.body)
                .box({
                    uid: 'popupBasketExpired',
                    source: LC.global.routePaths.BASKET_INTERNAL_LOCKED_STOCK + '?type=expired',
                    showFooter: false,
                    type: 'url',
                    callback: 'stockLockPopup',
                    triggerOnClick: false
                });
            $('#popupBasketExpired').find('.btn-close').click(
                function () { writeCookie('closedPopupBasketExpired', true); }
            );
        }
    },
    warnExpireBasket: function () {
        if (!readCookie('closedPopupWarningBasketExpires')) {
            $('#popupBasketExpired').modal('hide');
            $(document.body)
                .box({
                    uid: 'popupWarningBasketExpires',
                    source: LC.global.routePaths.BASKET_INTERNAL_LOCKED_STOCK + '?type=warning',
                    showFooter: false,
                    type: 'url',
                    callback: 'stockLockPopup',
                    triggerOnClick: false
                });
            $('#popupWarningBasketExpires').find('.btn-close').click(
                function () { writeCookie('closedPopupWarningBasketExpires', true); }
            );
        }
    },
};

/**
 * renewReserve event
 * @param {Object} event
 */
LC.dataEvents.renewReserve = function (event) {
    var data = $(event.target).data('lc-renewreserve');

    if (data.action == 'renewReserve') {
        if (globalThis?.lcCommerceData && lcCommerceData.navigation.type === 'CHECKOUT') {
            var callback = function () {
                lcOneStepCheckout.moduleCalls('refreshModule');
                if (LC.hasOwnProperty('miniBasket')) LC.miniBasket.reload();
                $('#popupWarningBasketExpires').modal('hide');
                $('#popupWarningBasketExpires').remove();
            };
        } else {
            var callback = function () {
                location.reload();
            };
        }
        $.post(
            LC.global.routePaths.BASKET_INTERNAL_LOCKED_STOCK_RENEW,
            { data: JSON.stringify(data) },
            callback,
            'json'
        );
    } else if (themeConfiguration?.commerce?.useOneStepCheckout) {
        window.location = LC.global.routePaths.CHECKOUT;
    } else {
        window.location = LC.global.routePaths.CHECKOUT_BASKET;
    }

};

/**
 * Recover order event
 * @param {Object} event
 */
LC.dataEvents.printContent = function (event) {
    var data = $(event.currentTarget).data('lc-data');
    var toPrint = document.getElementById(data.contentId);
    var popupWin = window.open('', data.hrefType, data.windowAttributes);
    popupWin.document.open();
    popupWin.document.write('<html>');
    popupWin.document.write('<title>' + data.title + '</title>');
    popupWin.document.write('<body onload="window.print()">');
    popupWin.document.write(toPrint.innerHTML);
    popupWin.document.write('</html>');
    popupWin.document.close();
};

/**
 * LC editShoppingListRowNotes
 * @description Edit shopping list row notes event
 * @memberOf LC
 * @param {Object} event
 */
LC.dataEvents.editShoppingListRowNotes = function (event) {

    const data = $.parseJSON($(event.currentTarget).attr('data-lc-data')),
        $popup = $($(event.currentTarget).data('bs-target')),
        $priority = $popup.find('input[name="priority"]');

    $popup.find('input[name="shoppingListId"]').val('');
    $popup.find('input[name="type"]').val('PUT');
    $popup.find('input[name="id"]').val(data.id);
    $popup.find('textarea[name="comment"]').val(data.comment);
    $popup.find('input[name="quantity"]').val(data.quantity);
    $popup.find('select[name="importance"]').val(data.importance);
    $popup.find('input[name="reference"]').val(data.reference);

    $priority.attr({ 'max': $(event.currentTarget).data('lc-total-items') });
    $priority.val(data.priority);

}

/**
 * LC addShoppingListRowNotes
 * @description add shopping list row notes event
 * @memberOf LC
 * @param {Object} event
 */
LC.dataEvents.addShoppingListRowNotes = function (event) {

    const data = $.parseJSON($(event.currentTarget).attr('data-lc-data')),
        $popup = $($(event.currentTarget).data('bs-target')),
        $priority = $popup.find('input[name="priority"]');

    $popup.find('input[name="shoppingListId"]').val(data.id);
    $popup.find('input[name="type"]').val('POST');
    $popup.find('input[name="id"]').val('');
    $popup.find('textarea[name="comment"]').val('');
    $popup.find('input[name="quantity"]').val(1);
    $popup.find('select[name="importance"]').val('MEDIUM');
    $popup.find('input[name="reference"]').val('');
    $popup.find('input[name="template"]').val($(event.currentTarget).data('lc-note-row-template'));

    let maxPriority = $(event.currentTarget).data('lc-total-items') + 1;
    $priority.attr({ 'max': maxPriority });
    $priority.val(maxPriority);

}

/**
 * LC setShoppingList
 * @description Set shopping list event
 * @memberOf LC
 * @param {Object} event
 */
LC.dataEvents.setShoppingList = function (event) {

    const data = $(event.currentTarget).data('lc-data'),
        $popup = $($(event.currentTarget).data('bs-target')),
        $priority = $popup.find('input[name="priority"]');

    $popup.find('input[name="name"]').val(data.name);
    $popup.find('input[name="description"]').val(data.description);

    $popup.find('input[name="keepPurchasedItems"]').val(data.keepPurchasedItems);
    $popup.find('input[name="keepPurchasedItemsCheckbox"]')[0].checked = data.keepPurchasedItems;
    $popup.find('input[name="keepPurchasedItemsCheckbox"]').click(function (e) {
        $popup.find('input[name="keepPurchasedItems"]').val($(e.target)[0].checked);
    });

    if ($(event.currentTarget).data('lc-total-items') <= 1) {
        $popup.find('input[name="defaultOne"]').val('true');
        $popup.find('input[name="defaultOneCheckbox"]').prop('disabled', true);
        $priority.val(1);
        $priority.attr({ 'max': 1 });
    } else {
        if (data.defaultOne) {
            $popup.find('input[name="defaultOneCheckbox"]').prop('disabled', true);
        }
        $popup.find('input[name="defaultOne"]').val(data.defaultOne);
        $popup.find('input[name="defaultOneCheckbox"]')[0].checked = data.defaultOne;
        $popup.find('input[name="defaultOneCheckbox"]').click(function (e) {
            $popup.find('input[name="defaultOne"]').val($(e.target)[0].checked);
        });
        $priority.attr({ 'max': $(event.currentTarget).data('lc-total-items') });
        $priority.val(data.priority);
    }

}

/**
 * Delete shopping list row event
 * @param {Object} event
 */
LC.dataEvents.deleteShoppingListRow = function (event) {
    event.preventDefault();
    const data = $.parseJSON($(event.currentTarget).attr('data-lc-data'));
    $.post(
        LC.global.routePaths.USER_INTERNAL_DELETE_SHOPPING_LIST_ROWS,
        {
            data: JSON.stringify({
                shoppingListId: data.shoppingListId,
                rowIdList: data.rowId
            })
        },
        function (response) {
            LC.notify(response.data.response.message, { type: response.data.response.success === 1 ? 'success' : 'danger' });
            if (response.data.response.success === 1) {
                $(`#${data.containerId}`).remove();
            }
        },
        'json'
    );
};

/**
 * LC moveShoppingListRow
 * @description Move shopping list row notes event
 * @memberOf LC
 * @param {Object} event
 */
LC.dataEvents.moveShoppingListRow = function (event) {
    const data = $(event.currentTarget).data('lc-data');
    let list = '';
    $.each(data.shoppingLists, (index, item) => {
        list += `<div class="form-check">
            <input class="form-check-input" type="radio" name="moveShoppingListRowId" id="moveShoppingListRowId${item.id}" data-lc-shopping-list-id="${item.id}">
            <label class="form-check-label" for="moveShoppingListRowId${item.id}">${item.name}</label>
        </div>`;
    });
    const $boxContent = $(`
        <div class="moveShoppingListRowContainer">${list}</div>
        <div class="moveShoppingListButtons">
            <button type="button" class="${BTN_SECONDARY_CLASS} moveShoppingListButton moveShoppingListButtonDismiss" data-dismiss="modal" data-bs-dismiss="modal">${LC.global.languageSheet.cancel}</button>
            <button type="button" class="${BTN_PRIMARY_CLASS} moveShoppingListButton moveShoppingListButtonConfirm">${LC.global.languageSheet.save}</button>
        </div>
    `);
    let $confirmButton = $($boxContent.find('.moveShoppingListButtonConfirm'));

    $confirmButton.on('click', function (event) {
        $.post(
            LC.global.routePaths.USER_INTERNAL_SET_SHOPPING_LIST_ROW,
            {
                data: JSON.stringify({
                    shoppingListId: $('[name="moveShoppingListRowId"]:checked').data('lc-shopping-list-id'),
                    id: data.rowId,
                    type: 'PUT'
                })
            },
            function (response) {
                LC.notify(response.data.response.message, { type: response.data.response.success === 1 ? 'success' : 'danger' });
                if (response.data.response.success === 1) {
                    $(`#${data.containerId}`).remove();
                }
            },
            'json'
        );
        $('#moveShoppingListRow' + data.rowId).modal('hide');
    });

    $(event.currentTarget).box({
        uid: 'moveShoppingListRow' + data.rowId,
        modalClass: 'moveShoppingListRow',
        showFooter: false,
        triggerOnClick: false,
        source: $boxContent,
        type: 'html',
        showClose: true,
        size: 'small',
        showHeader: true,
        headerTitle: LC.global.languageSheet.shoppingListRowMoveButton,
    });
}

/**
 * Set shopping list row event
 * @param {Object} event
 */
LC.dataEvents.setShoppingListRow = function (event) {
    event.preventDefault();
    const data = $.parseJSON($(event.currentTarget).attr('data-lc-data'));
    $.post(
        LC.global.routePaths.USER_INTERNAL_SET_SHOPPING_LIST_ROW,
        {
            data: JSON.stringify({
                shoppingListId: data.shoppingListId,
                id: data.rowId,
                type: 'PUT'
            })
        },
        function (response) {
            LC.notify(response.data.response.message, { type: response.data.response.success === 1 ? 'success' : 'danger' });
            if (response.data.response.success === 1) {
                $(`#${data.containerId}`).remove();
            }
        },
        'json'
    );
};

/**
 * Delete shopping list event
 * @param {Object} event
 */
LC.dataEvents.deleteShoppingList = function (event) {

    let $boxContent = $(`
        <div class="titleDeleteShoppingListConfirm">${LC.global.languageSheet.deleteShoppingListConfirmTitle}</div>
        <div class="textDeleteShoppingListConfirm">${LC.global.languageSheet.deleteShoppingListConfirmText}</div>
        <div class="deleteShoppingListButtons">
            <button type="button" class="${BTN_SECONDARY_CLASS} deleteShoppingListButton deleteShoppingListButtonDismiss" data-dismiss="modal" data-bs-dismiss="modal">${LC.global.languageSheet.cancel}</button>
            <button type="button" class="${BTN_DANGER_CLASS} deleteShoppingListButton deleteShoppingListButtonConfirm">${LC.global.languageSheet.delete}</button>
        </div>`
    );
    let $confirmButton = $($boxContent.find('.deleteShoppingListButtonConfirm'));

    var data = $(event.currentTarget).data('lc-data');
    $confirmButton.on('click', function (event) {
        $.post(
            LC.global.routePaths.USER_INTERNAL_DELETE_SHOPPING_LIST,
            { data: JSON.stringify({ id: data.id }) },
            function (response) {
                if (response.data.response.success === 1) {
                    location.href = LC.global.routePaths.USER_SHOPPING_LISTS;
                } else {
                    LC.notify(response.data.response.message, { type: 'danger' });
                }
            },
            'json'
        );
        $('#deleteShoppingListConfirm').modal('hide');
    });

    $(event.currentTarget).box({
        uid: 'deleteShoppingListConfirm',
        showFooter: false,
        triggerOnClick: false,
        source: $boxContent,
        type: 'html',
        showClose: true,
        size: 'small'
    });

};

/**
 * Delete save for later row
 * @param {Object} event
 */
LC.dataEvents.deleteFormSaveForLaterRow = function (event) {
    event.preventDefault();
    var id = $(event.currentTarget).data('lc-id');
    $.post(
        LC.global.routePaths.BASKET_INTERNAL_DELETE_SAVE_FOR_LATER_ROW,
        {
            data: JSON.stringify({
                id: id
            }),
        },
        function (data) {
            if (data.data.response.success === 1) {
                $('[data-lc-saveForLaterRow-id="' + id + '"]').remove();
            } else {
                LC.notify(response.data.response.message, { type: 'danger' });
            }
        },
        'json'
    );
};

/**
 * Transfer to basket save for later row
 * @param {Object} event
 */
LC.dataEvents.transferToBasketSaveForLaterRow = function (event) {
    event.preventDefault();
    var id = $(event.currentTarget).data('lc-id');
    $.post(
        LC.global.routePaths.BASKET_INTERNAL_TRANSFER_TO_BASKET_SAVE_FOR_LATER_ROW,
        {
            data: JSON.stringify({
                id: id
            }),
        },
        function (data) {
            if (data.data.response.success === 1) {
                location.reload();
            }
        },
        'json'
    );
};

/**
 * Open attachment path
 * @param {Object} event
 */
LC.dataEvents.openAttachmentPath = function (event) {
    event.preventDefault();
    var attachmentPath = $(event.currentTarget).data('lc-attachment-path');
    $.post(
        LC.global.routePaths.BASKET_INTERNAL_ATTACHMENT,
        { data: JSON.stringify({ path: attachmentPath }) },
        (response) => {
            if (response.data.response && response.data.response.success == 1) {
                let newWindow = window.open("");
                newWindow.document.write(
                    `<html><head><title>${response.data.data.fileName}</title></head><body><iframe frameborder="0" title="${response.data.data.fileName}"  width="100%" height="100%" src="${encodeURI(response.data.data.value)}" sandbox></iframe></body></html>`
                );
            } else {
                LC.notify(response.data.response.message, { type: 'danger' });
            }
        },
        'json'
    );
};

/**
 * unsubscribe subscription
 * @param {Object} event
 */
LC.dataEvents.unsubscribeSubscription = function (event) {
    const subscriptionType = $(event.currentTarget).data('lc-subscription-type');
    $.post(
        LC.global.routePaths.USER_INTERNAL_UNSUBSCRIBE_SUBSCRIPTION,
        { data: JSON.stringify({ subscriptionType }) },
        (response) => {
            if (response?.data?.response?.success === 1) {
                LC.notify(response.data.response.message, { type: 'success' });
                $(event.currentTarget).remove();
            } else {
                LC.notify(response.data.response.message, { type: 'danger' });
            }
        },
        'json'
    );

};

/**
 * reload customize JS
 */
LC.dataEvents.reloadCustomize = function () {
    if (globalThis?.lcCommerceData) {
        var params = new urlParameterEncoder();
        params.addParameter('type', globalThis.lcCommerceData.navigation.type);
        params.addParameter('id', globalThis.lcCommerceData.navigation.id);
        var url = LC.global.routePaths.RESOURCES_INTERNAL_CUSTOMIZE_JS + '/' + params.getParameters();
        $.get(url, {}, 'html');
    }
};

/**
 * add/Remove product events
 * @param {array} items
 * @param {object} newItems
 */
LC.dataEvents.changeQuantityEvent = function (items, newItems) {
    if (newItems && newItems.length) {
        newItems = LC.dataEvents.parseRows(newItems);
    }
    for (let i = 0; i < items.length; i++) {
        for (let j = 0; j < newItems.length; j++) {
            if (items[i].hash == newItems[j].hash) {
                let data = { hash: items[i].hash }
                let difference = items[i].quantity - newItems[j].quantity;
                data.quantity = Math.abs(difference);
                if (difference < 0) {
                    LC.resources.pluginListener('onAddProduct', {}, data);
                } else if (difference > 0) {
                    LC.resources.pluginListener('onRemoveProduct', {}, data);
                }
            }
        }
    }
};

/**
 * parse products struct to array
 * @param {object} rows
 */
LC.dataEvents.parseRows = function (rows) {
    let rowsArray = [];
    for (const [key, value] of Object.entries(rows)) {
        let row = { hash: key, quantity: value.quantity };
        rowsArray.push(row);
    }
    return rowsArray;
};

/**
 * AcceptRouteWarning event
 * @param {Object} event
 */
LC.dataEvents.acceptRouteWarning = function (event = null) {
    if (event) {
        event.preventDefault();
    }
    $.ajax(
        {
            type: 'POST',
            url: LC.global.routePaths.RESOURCES_INTERNAL_ACCEPT_ROUTE_WARNING,
            success: (data) => {
                if (data.data.response.success === 1) {
                    if (event && $(event.target).data('lc-warning-url')) {
                        window.location = $(event.target).data('lc-warning-url');
                    }
                    if (event) {
                        $('#routeWarning').modal('hide');
                    }
                }
            },
            async: false,
            dataType: 'json'
        }
    );
};

/**
 * resend event
 * @param {Object} event
 */
LC.dataEvents.userVerifyResend = function (event = null) {
    if (event) {
        event.preventDefault();
    }
    $.ajax(
        {
            type: 'POST',
            url: LC.global.routePaths.USER_INTERNAL_VERIFY_RESEND,
            data: { data: JSON.stringify({ 'username': $(event.currentTarget).attr('data-lc-username') }) },
            success: (response) => {
                if (response?.data?.response?.success === 1) {
                    let modalId = 'modalVerifyUser',
                        modalClass = 'verifyUser',
                        boxMainDiv = $('<div/>', {
                            class: 'question ' + modalId + ' ' + modalClass,
                            html: '<div class="questionText ' + modalClass + 'Text">' + response.data.response.message + '</div>',
                        });
                    $('#userVerifyAccountFormContainer #verifyAccountForm').appendTo(boxMainDiv);
                    boxMainDiv.box({
                        uid: modalId,
                        showFooter: false,
                        type: 'internal',
                        size: 'medium',
                        triggerOnClick: false,
                    });
                } else {
                    LC.notify(response.data.response.message, { type: 'danger' });
                }
            },
            async: false,
            dataType: 'json'
        }
    );
}
LC.dataEvents.registeredUserMove = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    var tokenParam = (data.session ? '' : '&token=' + data.token);
    var url = `${LC.global.routePaths.ACCOUNT_INTERNAL_ACCOUNT_REGISTERED_USER_MOVE}?accountId=${data.accountId}&registeredUserId=${data.registeredUserId}${tokenParam}`;
    var popup = $('#registeredUserMoveContent').html(DEFAULT_LOADING_SPINNER);
    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                var registeredUserMoveForm = $(html).find('.registeredUserMoveForm');
                popup.html(registeredUserMoveForm);
                popup.find('[data-lc-event]').dataEvent();
                var form = popup.find('form');
                if (form) {
                    new LC.RegisteredUserMoveForm(form);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}

LC.dataEvents.registeredUsersDelete = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    var $confirm = $('#deleteRegisteredUserButtonConfirm');
    $confirm.data('delete-payload', { data: data });
    $confirm.off('click').one('click', function () {
        var stored = $(this).data('delete-payload') || {};
        var payloadData = stored.data || {};
        var url = `${LC.global.routePaths.ACCOUNT_INTERNAL_DELETE_ACCOUNT_REGISTERED_USER}?accountId=${payloadData.accountId}&registeredUserId=${payloadData.registeredUserId}`;

        $().ready(function () {
            $.ajax({
                type: "GET",
                url: url,
                crossDomain: true,
                success: function (response) {
                    var message = LC.global.languageSheet.error,
                        success = 0;
                    if (response && response.data.response) {
                        message = response.data.response.message;
                        success = response.data.response.success;

                        if (success) {
                            LC.notify(message, { type: 'success' });

                            if ($('#registeredUsersDelete').length > 0) {
                                closeRegisteredUserModalAndReload($('#registeredUsersDelete .buttonDismiss'));
                            } else if (response.data.data.redirect && response.data.data.redirect.length) {
                                window.location = response.data.data.redirect;
                            } else {
                                window.location = window.location.href;
                            }
                        } else {
                            LC.notify(errorMessage, { type: 'danger' });
                        }
                    }
                },

                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('xHR: ' + xhr);
                    console.log('ajaxOption: ' + ajaxOptions);
                    console.log('thrownError: ' + thrownError);
                }
            });
        });
    });

}
LC.dataEvents.accountOrdresLink = function (event) {
    event.preventDefault();
    var loadUrl = $("#accountOrdersLoadUrl").val();
    var url = new URL(loadUrl);
    var baseUrl = url.origin + url.pathname;

    var $link = $(event.currentTarget);
    var params = $link.attr('href');
    accountOrdresReloadResults(baseUrl + params);
}

LC.dataEvents.accountRegisteredUsersLink = function (event) {
    event.preventDefault();
    var loadUrl = $("#accountRegisteredUsersLoadUrl").val();
    var url = new URL(loadUrl);
    var baseUrl = url.origin + url.pathname;

    var $link = $(event.currentTarget);
    var params = $link.attr('href');
    accountRegisteredUsersReloadResults(baseUrl + params);
}

LC.dataEvents.accountCompanyRoles = function (event) {
    event.preventDefault();
    var loadUrl = $("#companyRolesLoadUrl").val();
    var url = new URL(loadUrl);
    var baseUrl = url.origin + url.pathname;

    var $link = $(event.currentTarget);
    var params = $link.attr('href');
    companyRolesReloadResults(baseUrl + params);
}

LC.dataEvents.registeredUsersAdd = function (event) {
    event.preventDefault();
    var modalContent = $('#registeredUsersCreateContent').html(DEFAULT_LOADING_SPINNER);
    var data = $(event.currentTarget).data('lc-data');
    var url = `${LC.global.routePaths.ACCOUNT_REGISTERED_USER_CREATE}`.replace("used", `${data.accountId ?? "used"}`);

    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                modalContent.html(html);
                modalContent.find('[data-lc-event]').dataEvent();
                var form = modalContent.find('form');
                if (form) {
                    new LC.RegisteredUserCreateForm(form);
                }

            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}
LC.dataEvents.registeredUserUpdate = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    var tokenParam = (data.session ? '' : '?token=' + data.token);
    var url = `${LC.global.routePaths.ACCOUNT_REGISTERED_USER}`.replace("{accountId}", `${data.accountId}`).replace("{registeredUserId}", `${data.registeredUserId}`) + `${tokenParam}`;
    //.replace("{a}", a).replace("{b}", b)
    var popup = $('#registeredUserUpdateContent').html(DEFAULT_LOADING_SPINNER);
    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                var registeredUserUpdateForm = $(html).find('.registeredUserUpdateForm');
                popup.html(registeredUserUpdateForm);
                popup.find('[data-lc-event]').dataEvent();
                var form = popup.find('form');
                if (form) {
                    new LC.AccountRegisteredUserUpdateForm(form);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}


LC.dataEvents.registeredUserSelector = function (event) {
    event.preventDefault();
    var format = $(event.currentTarget).data('format');
    var accountId = $(event.currentTarget).data('account-id');
    var page = $(event.currentTarget).data('page');
    $('#registeredUserSelectorContent').slideToggle(200);

    registeredUserSelectorSearch("", accountId, format, page);

}

LC.dataEvents.saveDivision = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    const modal = document.getElementById('saveCompanyDivisionFormModal');
    const modalContent = document.getElementById('saveCompanyDivisionFormModalContent');
    if (!modal || !modalContent) {
        LC.notify('Modal not found on page', { type: 'danger' });
        return;
    }

    // Show modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();


    var url = `${LC.global.routePaths.ACCOUNT_INTERNAL_SAVE_COMPANY_DIVISION}?id=${data.accountId}`;

    var popup = $('#saveCompanyDivisionFormModalContent');
    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                popup.html(html);
                popup.find('[data-lc-event]').dataEvent();
                const form = popup.find('form');
                if (form) {
                    new LC.SaveCompanyDivisionForm(form);
                }

            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
};

LC.dataEvents.saveCompanyRole = function (event) {
    event.preventDefault();

    var $btn = $(event.currentTarget);
    var data = $btn.data('lc-data') || {};
    var modal = document.getElementById('saveCompanyRoleModal');
    var $content = $('#saveCompanyRoleContent');
    if (!modal || !$content.length) {
        LC.notify('Modal not found on page', { type: 'danger' });
        return;
    }

    // Abortar carga anterior si existiera
    if (LC.dataEvents._saveRoleXHR && LC.dataEvents._saveRoleXHR.readyState !== 4) {
        LC.dataEvents._saveRoleXHR.abort();
    }

    // 1) Limpiar y poner loader antes de abrir
    $content
        .empty()
        .html(DEFAULT_LOADING_SPINNER);

    // 2) Abrir modal con loader (o mueve este show() al success si quieres abrir solo con el HTML nuevo)
    var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
    bsModal.show();

    var url = LC.global.routePaths.ACCOUNT_COMPANY_ROLE.replace('{roleId}', data.roleId);

    // 3) Cargar HTML
    LC.dataEvents._saveRoleXHR = $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        success: function (html) {
            // Destruir instancia previa si la guardaste
            var prev = $content.data('saveRoleFormInstance');
            if (prev && typeof prev.destroy === 'function') prev.destroy();

            $content.html(html);
            $content.find('[data-lc-event]').dataEvent();

            var $form = $content.find('form');
            if ($form.length) {
                var inst = new LC.SaveCompanyRoleForm($form);
                $content.data('saveRoleFormInstance', inst);
            }

            if (bsModal && typeof bsModal.handleUpdate === 'function') bsModal.handleUpdate();
        },
        error: function () {
            console.log('xHR: ' + xhr);
            console.log('ajaxOption: ' + ajaxOptions);
            console.log('thrownError: ' + thrownError);
        }
    });
};

LC.dataEvents.duplicateCompanyRole = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');

    const modal = document.getElementById('saveCompanyRoleModal');
    const modalContent = document.getElementById('saveCompanyRoleContent');

    if (!modal || !modalContent) {
        var modalHtml = `
            <div class="modal fade" id="saveCompanyRoleModal" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="saveCompanyRoleContent"></div>
                    </div>
                </div>
            </div>
        `;
        $('body').append(modalHtml);
    }

    var url = `${LC.global.routePaths.ACCOUNT_COMPANY_ROLE}`.replace('{roleId}', data.roleId);
    var popup = $('#saveCompanyRoleContent');

    // Mostrar indicador de carga
    var $button = $(event.currentTarget);
    $button.attr('disabled', true);
    var originalHtml = $button.html();
    $button.html('<i class="fas fa-spinner fa-spin"></i> Duplicating...');

    $.ajax({
        type: "GET",
        url: url,
        crossDomain: true,
        success: function (html) {
            popup.html(html);
            popup.find('[data-lc-event]').dataEvent();

            const form = popup.find('form');
            if (form) {
                // Modificar para duplicación
                var $nameField = form.find('input[name="name"]');
                if ($nameField.length) {
                    $nameField.val($nameField.val() + ' (Copy)');
                }

                // Eliminar el ID
                form.find('input[name="id"]').remove();
                form.find('input[name="pId"]').remove();

                // HABILITAR EL CAMPO TARGET
                form.find('select[name="target"]').prop('disabled', false);

                // Inicializar con modo automático
                var formInstance = new LC.SaveCompanyRoleForm(form);
                formInstance.autoSubmit = true;
                formInstance.onCompleteCallback = function () {
                    $button.attr('disabled', false);
                    $button.html(originalHtml);
                };

                // Submit automático
                setTimeout(function () {
                    form.submit();
                }, 100);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error('Error:', thrownError);
            LC.notify('Error loading role data', { type: 'danger' });
            $button.attr('disabled', false);
            $button.html(originalHtml);
        }
    });
};

LC.dataEvents.deleteRoleEvents = function (event) {
    var data = $(event.currentTarget).data('lc-data');
    var $confirm = $('#deleteCompanyRolesButtonConfirm');
    $confirm.data('delete-payload', { data: data });
    $confirm.off('click').one('click', function (e) {
        event.preventDefault();
        var stored = $(this).data('delete-payload') || {};
        var data = stored.data || {};
        var roleId = data.roleId;
        var roleName = data.roleName;
        if (!roleId) {
            LC.notify('Invalid role ID', { type: 'danger' });
            return;
        }

        // Deshabilitar el botón y mostrar indicador de carga
        var $button = $(event.currentTarget);
        $button.attr('disabled', true);

        $.ajax({
            type: "GET",
            url: `${LC.global.routePaths.ACCOUNT_INTERNAL_DELETE_COMPANY_ROLE}?id=${roleId}`,
            crossDomain: true,
            success: function (response) {
                if (response && response.data && response.data.response && response.data.response.success) {
                    LC.notify(`Role "${roleName}" deleted successfully`, { type: 'success' });
                    closeCompanyRolesReloadResults($('#companyRolesDelete .buttonDismiss'));
                } else {
                    var errorMessage = response?.data?.response?.message || 'Error deleting role';
                    LC.notify(errorMessage, { type: 'danger' });

                    $button.attr('disabled', false);
                    $button.html(originalHtml);
                }
            },
            error: function (xhr, status, error) {
                console.error('Delete error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });

                var errorMessage = 'An error occurred while deleting the role';

                // Intentar obtener mensaje de error del servidor
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.data && errorResponse.data.response && errorResponse.data.response.message) {
                        errorMessage = errorResponse.data.response.message;
                    }
                } catch (e) {
                    // Usar mensaje genérico si no se puede parsear
                }

                LC.notify(errorMessage, { type: 'danger' });

                // Restaurar el botón
                $button.attr('disabled', false);
                $button.html(originalHtml);
            }
        });
    });
    /*if (arg?.preventDefault) arg.preventDefault();

    const $trigger = arg?.currentTarget ? $(arg.currentTarget) : $(arg);
    const roleData = $trigger.data('lc-data') || {};
    const modalId = 'deleteRoleConfirm_' + (roleData.roleId || Date.now());

    // limpia si existe
    $('#' + modalId).remove();

    const html = `
    <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered"><!-- centrado -->
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">${LC.global.languageSheet.popupConfirmTitle}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="${LC.global.languageSheet.cancel}"></button>
        </div>
        <div class="modal-body">
            <p>${LC.global.languageSheet.popupConfirmText}</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="${BTN_SECONDARY_CLASS}" data-bs-dismiss="modal">
            ${LC.global.languageSheet.cancel}
            </button>
            <button type="button" class="${BTN_DANGER_CLASS} js-delete-confirm"
                    data-lc-data='${JSON.stringify(roleData)}'>
            ${LC.global.languageSheet.delete}
            </button>
        </div>
        </div>
    </div>
    </div>`;

    $('body').append(html);

    const el = document.getElementById(modalId);
    let modal;
    if (window.bootstrap && bootstrap.Modal) {
        modal = bootstrap.Modal.getOrCreateInstance(el);
        modal.show();
    } else if ($.fn.modal) {
        $(el).modal('show');
    }

    // confirmar
    $(el).off('click', '.js-delete-confirm').on('click', '.js-delete-confirm', function (e) {
        LC.dataEvents.deleteCompanyRole(e);
        if (modal) modal.hide(); else $(el).modal('hide');
    });

    // limpieza
    $(el).one('hidden.bs.modal', function () { $(this).remove(); });*/
};


LC.dataEvents.editAccount = function (event) {
    event.preventDefault();
    const data = $(event.currentTarget).data('lc-data') || {};
    const modal = document.getElementById('editAccountFormModal');
    const $content = $('#editAccountFormModalContent');
    if (!modal || !$content.length) {
        LC.notify('Modal not found on page', { type: 'danger' });
        return;
    }

    // abortar carga anterior si sigue viva
    if (LC.dataEvents._editAccountXHR && LC.dataEvents._editAccountXHR.readyState !== 4) {
        LC.dataEvents._editAccountXHR.abort();
    }

    var url = `${LC.global.routePaths.ACCOUNT}`.replace("used", `${data.accountId}`);
    // destruir instancia previa del form si existe
    const prev = $content.data('editAccountFormInstance');
    if (prev && typeof prev.destroy === 'function') prev.destroy();
    $content.removeData('editAccountFormInstance');

    // limpiar y poner loader
    $content.empty().html(DEFAULT_LOADING_SPINNER);
    $('#editAccountFormModalTitle').html(data.title);

    const bsModal = bootstrap.Modal.getOrCreateInstance(modal);
    bsModal.show();

    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                const $html = $(html);
                const $formBlock = $html.find('.accountEditFormContent');
                $content.html($formBlock.length ? $formBlock : $html);

                new LC.BillingAddressBookForm($content.find('#billingAddressBookForm')[0]);
                new LC.ShippingAddressBookForm($content.find('#shippingAddressBookForm')[0]);

                $('#billingAddressBookModal').on('hidden.bs.modal', function (e) {
                    bsModal.show();
                });
                $('#shippingAddressBookModal').on('hidden.bs.modal', function (e) {
                    bsModal.show();
                });

                if ($("body >.addressBookModal").length > 0) {
                    $("body >.addressBookModal").remove();
                }
                $content.find('.addressBookModal').appendTo('body');
                // re-enlazar eventos declarativos
                $content.find('[data-lc-event]').dataEvent();

                const $form = $content.find('form');
                if ($form.length) {
                    const inst = new LC.EditAccountForm($form);
                    $content.data('editAccountFormInstance', inst);
                }

                if (bsModal && typeof bsModal.handleUpdate === 'function') bsModal.handleUpdate();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}

LC.dataEvents.viewOrders = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    $('#accountOrdresModalTitle').html(data.title);
    var url = LC.global.routePaths.ACCOUNT_ORDERS.replace("used", data.accountId);
    var $modalContent = $('#accountOrdresModalContent').html(DEFAULT_LOADING_SPINNER);
    var modal = document.getElementById('accountOrdresModal');
    var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
    bsModal.show();
    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,

            success: function (html) {
                if ($("body > .lc-accountOrdersModal").length > 0) {
                    $("body >.lc-accountOrdersModal").remove();
                }
                $(html).find('.lc-accountOrdersModal').appendTo('body');

                var accountOrdres = $(html).find('#accountOrdres');
                $modalContent.html(accountOrdres);
                $modalContent.find('.lc-accountOrdersModal').remove();
                $modalContent.find('[data-lc-event]').dataEvent();
                var form = $modalContent.find('#ordersForm');
                if (form) {
                    new LC.OrdersForm(form);
                }
                var loaderForm = $modalContent.find('#accountOrdersLoaderForm');
                if (loaderForm) {
                    new LC.AccountOrdresLoaderForm(loaderForm).initialize(loaderForm, url);
                }

                $('.lc-accountOrdersModal').on('hidden.bs.modal', function (e) {
                    bsModal.show();
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}

LC.dataEvents.manageEmployees = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    $('#manageEmployeesModalTitle').html(data.title);

    var url = LC.global.routePaths.ACCOUNT_REGISTERED_USERS.replace("used", data.accountId);
    var $modalContent = $('#manageEmployeesModalContent').html(DEFAULT_LOADING_SPINNER);
    var modal = document.getElementById('manageEmployeesModal');
    var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
    bsModal.show();
    $().ready(function () {
        $.ajax({
            type: "GET",
            url: url,
            crossDomain: true,
            success: function (html) {
                if ($("body >.lc-accountRegisteredUsersModal").length > 0) {
                    $("body >.lc-accountRegisteredUsersModal").remove();
                }
                $(html).find('.lc-accountRegisteredUsersModal').appendTo('body');

                var manageEmployees = $(html).find('#accountRegisteredUsers');
                $modalContent.html(manageEmployees);
                $modalContent.find('.lc-accountRegisteredUsersModal').remove();
                $modalContent.find('[data-lc-event]').dataEvent();
                var form = $modalContent.find('#registeredUsersForm');
                if (form) {
                    new LC.AccountRegisteredUsersForm(form);
                }
                var loaderForm = $modalContent.find('#accountRegisteredUsersLoaderForm');
                if (loaderForm) {
                    new LC.AccountRegisteredUserLoaderForm(loaderForm).initialize(loaderForm, url);
                }

                $('.lc-accountRegisteredUsersModal').on('hidden.bs.modal', function (e) {
                    bsModal.show();
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log('xHR: ' + xhr);
                console.log('ajaxOption: ' + ajaxOptions);
                console.log('thrownError: ' + thrownError);
            }
        });
    });
}

LC.dataEvents.ordersApprovalDecision = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    $("#ordersApprovalDecisionPopup #ordersApprovalDecisionButtonConfirm").text(LC.global.languageSheet["viewOrder" + data.decision.charAt(0).toUpperCase() + data.decision.slice(1).toLowerCase()]);

    var $confirm = $('#ordersApprovalDecisionButtonConfirm');
    $confirm.data('delete-payload', { data: data });
    $confirm.off('click').one('click', function () {
        var stored = $(this).data('delete-payload') || {};
        var payloadData = stored.data || {};
        $.post(
            LC.global.routePaths.ACCOUNT_INTERNAL_ORDERS_APPROVAL_DECISION,
            {
                data: JSON.stringify({ orderId: payloadData.orderId, decision: payloadData.decision })
            },
            (response) => {
                var message = LC.global.languageSheet.error,
                    success = 0;

                if (response && response.data.response) {
                    message = response.data.response.message;
                    success = response.data.response.success;

                    if (success) {
                        LC.notify(message, { type: 'success' });
                        if ($('#ordersApprovalDecision').length > 0) {
                            closeAccountOrdresReloadResults($('#ordersApprovalDecision .ordersApprovalDecisionButtonDismiss'));
                        } else if (response.data.data.redirect && response.data.data.redirect.length) {
                            window.location = response.data.data.redirect;
                        } else {
                            window.location = window.location.href;
                        }
                    } else {
                        LC.notify(message, { type: 'danger' });
                    }
                }
            },
            'json'
        );
    });
}

LC.dataEvents.deleteDivision = function (event) {
    event.preventDefault();
    var data = $(event.currentTarget).data('lc-data');
    const modal = document.getElementById('deleteAccountFormModal');
    const modalContent = document.getElementById('deleteAccountFormModalContent');
    if (!modal || !modalContent) {
        LC.notify('Modal not found on page', { type: 'danger' });
        return;
    }

    // Show modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();

    var url = `${LC.global.routePaths.ACCOUNT_DELETE}`;

    var popup = $('#deleteAccountFormModalContent');
    $.ajax({
        type: 'GET',
        url: url,
        success: function (html) {
            const $content = $(html).find('.deleteAccountFormContent');
            popup.html($content);
            popup.find('[data-lc-event]').dataEvent();

            const $submit = popup.find('#deleteAccountSubmitContainer');
            if ($submit.length) {
                $submit.css({ display: 'flex', justifyContent: 'center', alignItems: 'center' });
                $submit.find('button').removeAttr('disabled');
            }
            const $form = popup.find('.deleteAccountFormContent form').first();
            if ($form.length) {
                $form.attr('action', `${LC.global.routePaths.ACCOUNT_INTERNAL_DELETE_ACCOUNT}`);
                const accountId = data?.accountId ?? null;
                if (accountId !== null) {
                    let $hidden = $form.find('input[type="hidden"][name="id"]');
                    if ($hidden.length) {
                        $hidden.val(accountId);
                    } else {
                        const $wrapper = $('<div/>', { class: 'form-group formFieldGroup deleteAccountField' });
                        const $input = $('<input/>', {
                            type: 'hidden',
                            name: 'id',
                            id: 'id',
                            class: '',
                            value: accountId,
                            'data-lc': '',
                            autocomplete: 'off'
                        });
                        $wrapper.append($input);
                        $form.prepend($wrapper); // o .append($wrapper)
                    }
                }
                new LC.DeleteAccountADVCAForm($form);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log('xHR: ' + xhr);
            console.log('ajaxOption: ' + ajaxOptions);
            console.log('thrownError: ' + thrownError);
        }
    });
};

LC.dataEvents.loadMoreDivisions = function (event) {
    event.preventDefault();
    const paginationData = $(event.currentTarget).data('lc-data');
    if (!paginationData) return;

    const $button = $(event.currentTarget);
    const data = paginationData;

    if (!data.accountId) {
        console.error('Missing account data for load more operation');
        return;
    }

    // Disable button and show loading state
    $button.prop('disabled', true).addClass('loading');

    // Make AJAX request to load more divisions
    $.ajax({
        type: 'POST',
        url: '/account/company-structure/load-divisions',
        data: {
            accountId: data.accountId,
            page: data.nextPage
        },
        success: function (response) {
            if (response.success && response.html) {
                // Find the children container for this node
                const $node = $('#companyNode' + data.accountId);
                const $childrenContainer = $node.find('> .userCompanyStructureChildren');

                // Append new children
                $childrenContainer.append(response.html);

                // Re-initialize data events for new elements
                $childrenContainer.find('[data-lc-event]').dataEvent();

                // Check if there are more items to load
                if (!response.hasMore) {
                    // Remove the load more button
                    $button.closest('.userCompanyStructurePagination').remove();
                } else {
                    // Update button data with new page number
                    data.currentPage = data.nextPage;
                    data.nextPage = data.nextPage + 1;
                    $button.data('lc-data', data);
                    $button.prop('disabled', false).removeClass('loading');
                }

                LC.notify(response.message || 'Divisions loaded successfully', { type: 'success' });
            } else {
                $button.prop('disabled', false).removeClass('loading');
                LC.notify(response.message || 'Error loading more divisions', { type: 'danger' });
            }
        },
        error: function () {
            $button.prop('disabled', false).removeClass('loading');
            LC.notify('Error loading more divisions', { type: 'danger' });
        }
    });
};

/**
 * Toggle expand/collapse state of a company structure node
 * @param {Event} event - Click event
 */
LC.dataEvents.toggleCompanyNode = function (event) {
    event.preventDefault();
    event.stopPropagation();

    const $button = $(event.currentTarget);
    const lcData = $button.data('lc-data');

    if (!lcData || !lcData.accountId) {
        console.error('Missing account data for toggle operation');
        return;
    }

    const $node = $('#companyNode' + lcData.accountId);

    if (!$node.length) {
        console.error('Node not found:', lcData.accountId);
        return;
    }

    // Check if node is currently collapsed
    const isCollapsed = $node.hasClass('collapsed');

    if (isCollapsed) {
        // Check if we need to lazy load children
        if (lcData.hasSubDivisionsToLoad) {
            const $lazyContainer = $node.find('> .userCompanyStructureLazyChildren');
            if ($lazyContainer.hasClass('d-none')) {
                // Load children via lazy loading
                LC.dataEvents._loadLazyChildren($node, lcData, $button);
                return;
            }
        }
        // Expand node
        LC.dataEvents._expandCompanyNode($node, $button);
    } else {
        // Collapse node
        LC.dataEvents._collapseCompanyNode($node, $button);
    }
};

/**
 * Expand a company structure node
 * @private
 */
LC.dataEvents._expandCompanyNode = function ($node, $toggleBtn) {
    const $childrenContainer = $node.find('> .userCompanyStructureChildren');
    const $lazyChildrenContainer = $node.find('> .userCompanyStructureLazyChildren');

    // Remove collapsed class
    $node.removeClass('collapsed');

    // Toggle icons: hide plus, show minus
    $toggleBtn.find('.toggle-plus').addClass('d-none');
    $toggleBtn.find('.toggle-minus').removeClass('d-none');

    // Show children with animation
    if ($childrenContainer.length) {
        $childrenContainer.slideDown(200);
    }

    // Show lazy loaded children if they exist
    if ($lazyChildrenContainer.length && !$lazyChildrenContainer.hasClass('d-none')) {
        $lazyChildrenContainer.slideDown(200);
    }
};

/**
 * Collapse a company structure node
 * @private
 */
LC.dataEvents._collapseCompanyNode = function ($node, $toggleBtn) {
    const $childrenContainer = $node.find('> .userCompanyStructureChildren');
    const $lazyChildrenContainer = $node.find('> .userCompanyStructureLazyChildren');

    // Add collapsed class
    $node.addClass('collapsed');

    // Toggle icons: show plus, hide minus
    $toggleBtn.find('.toggle-minus').addClass('d-none');
    $toggleBtn.find('.toggle-plus').removeClass('d-none');

    // Hide children with animation
    if ($childrenContainer.length) {
        $childrenContainer.slideUp(200);
    }

    if ($lazyChildrenContainer.length && !$lazyChildrenContainer.hasClass('d-none')) {
        $lazyChildrenContainer.slideUp(200);
    }
};

/**
 * Load children via lazy loading
 * @private
 */
LC.dataEvents._loadLazyChildren = function ($node, data, $toggleBtn) {
    const $lazyContainer = $node.find('> .userCompanyStructureLazyChildren');
    const $loadingIndicator = $node.find('> .lazy-loading-indicator');

    // Show loading indicator
    $loadingIndicator.removeClass('d-none');

    // Make AJAX request to load children
    $.ajax({
        url: '/account/company-structure/load-divisions',
        method: 'POST',
        data: {
            accountId: data.accountId,
            page: 1
        },
        success: function (response) {
            // Hide loading indicator
            $loadingIndicator.addClass('d-none');

            if (response.success && response.html) {
                // Insert loaded children
                $lazyContainer.html(response.html);
                $lazyContainer.removeClass('d-none');

                // Re-initialize data events for new elements
                $lazyContainer.find('[data-lc-event]').dataEvent();

                // Update data to indicate children are loaded
                data.hasSubDivisionsToLoad = false;
                $toggleBtn.data('lc-data', data);

                // Now expand the node
                LC.dataEvents._expandCompanyNode($node, $toggleBtn);
            } else {
                LC.notify(response.message || 'Error loading company divisions', { type: 'danger' });
            }
        },
        error: function () {
            $loadingIndicator.addClass('d-none');
            LC.notify('Error loading company divisions', { type: 'danger' });
        }
    });
};