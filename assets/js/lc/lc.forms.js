/**
 * Classes extending from LC.Form
 */

'use strict';

/**
 * @class LC.BuyForm
 * @memberOf LC
 * @extends {LC.Form}
 * @description Form extended from LC.Form
 */
LC.BuyForm = LC.Form.extend({
    /**
     * internal name of form
     * @type {String}
     */
    name: 'buyForm',

    /**
     * object with options get of buyForm data
     * @type {object}
     */
    options: {},

    /**
     * Initialize
     * @memberOf LC.BuyForm
     */
    initialize: function (form) {
        if (this.el.form.initialized) return;
        this.el.form.initialized = true;

        this.el.form.module = this;
        this.data = JSON.parse(this.el.$form.attr('data-product'));
        this.stockWarehouses = JSON.parse(this.el.$form.attr('data-stock-warehouses'));
        this.quantityField = this.el.$form.find('input[data-lc-field="quantity"]').get(0);
        this.alternativeImageField = this.el.$form.find('input[data-lc-field="alternativeImage"]').get(0);
        this.linkedSectionId = this.el.$form.find('input[name="sectionId"]').val();

        this.productsOptions = $(this.el.$form.find('div.productOptions'));
        this.useUrlOptionsParams = false;
        this.productsOptionsData = {};

        if (this.productsOptions.length) {
            this.productsOptionsData = JSON.parse($(this.productsOptions).attr('data-lc-data'));
            this.useUrlOptionsParams = this.productsOptionsData.useUrlOptionsParams;
        }

        // Before trigger
        this.trigger('initializeBefore');

        if (!this.quantityField) {
            this.quantityField = this.el.$form.find('input[name=quantity]').get(0);
        } else {
            this.quantityField = this.el.$form.find('.quantitySelect').get(0);
            this.quantityField = this.quantityField.options[this.quantityField.selectedIndex].value;
        }

        this.el.$priceByQuantityBox = this.el.$form.find('.priceByQuantity');
        // Adding 1 unit prices to price by quantity
        this.data.priceByQuantity.unshift({
            from: 1,
            optionValueId: 0,
            basePrice: this.data.definition.productBasePrice,
            retailPrice: this.data.definition.productRetailPrice,
        });

        // Stock Alerts
        this.$stockSubscriptionButton = this.el.$form.find('.stockAlertButton');
        this.$stockSubscriptionButton.click(this.clickStockAlertButton.bind(this));

        if (this.data.definition.availability) {
            this.productAvailabilities = this.data.definition.availability.intervals;
        } else {
            this.productAvailabilities = [];
        }

        this.el.$productOptionAttachment = this.el.$form.find('div.lcProductOptionAttachment');

        this.callback = this.callback.bind(this);
        this.optionsInitialized = false;
        this.initOptions();

        // Init trigger
        this.trigger('init');

        // Wishlist init
        this.el.$wishlistDelete = this.el.$form.find('[data-wishlist-delete]');
        if (this.el.$wishlistDelete.length) this.wishlist(this.el.$wishlistDelete, 'delete');
        this.el.$wishlistAdd = this.el.$form.find('[data-wishlist-add]');
        if (this.el.$wishlistAdd.length) this.wishlist(this.el.$wishlistAdd, 'add');
        this.el.$wishlistAccountRequired = this.el.$form.find('[data-wishlist-account_required]');
        if (this.el.$wishlistAccountRequired.length) this.wishlist(this.el.$wishlistAccountRequired, 'account_required');

        // ShoppingList init
        this.el.$shoppingListDelete = this.el.$form.find('[data-shopping-list-delete]');
        if (this.el.$shoppingListDelete.length) this.shoppingList(this.el.$shoppingListDelete, 'delete');
        this.el.$shoppingListAdd = this.el.$form.find('[data-shopping-list-add]');
        if (this.el.$shoppingListAdd.length) this.shoppingList(this.el.$shoppingListAdd, 'add');
        this.el.$shoppingListAccountRequired = this.el.$form.find('[data-shopping-list-account_required]');
        if (this.el.$shoppingListAccountRequired.length) this.shoppingList(this.el.$shoppingListAccountRequired, 'account_required');

        // productComparison init
        this.el.$productComparison = this.el.$form.find('[data-product-comparison]');
        if (this.el.$productComparison.length) this.productComparison();

        // Init calendar
        this.el.$form.find('[data-datetimepicker]').each(
            (index, el) => {
                var $calendar = $(el),
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
                    minDate: startDate,
                    endDate: endDate
                });

                $(el).on('dp.change', (e) => {
                    var $optionsubmitValue = this.el.$form.find('[name="' + $calendar.data('submit') + '"]'),
                        previous = $optionsubmitValue.val();
                    if (e.date) {
                        $optionsubmitValue.val(moment(e.date).format('YYYY-MM-DD'));
                    } else {
                        $optionsubmitValue.val('');
                    }
                    if (previous != $optionsubmitValue.val()) {
                        previous = $optionsubmitValue.val();
                        $optionsubmitValue.change();
                        $optionsubmitValue.val(previous);
                    }
                });

            }
        );

        // Callback trigger
        this.trigger('initializeCallback');

        // Add special product submit
        this.el.$pseudoSubmits = this.el.$form.find('[data-lc-formButton="addSpecialProduct"]');
        if (this.el.$pseudoSubmits.length) this.pseudoSubmits();

        if (typeof pmtSimulator != 'undefined') pmtSimulator.simulator_app.reload();
    },

    addOptionsToProductLink: function () {
        if ((this.optionsInitialized || this.useUrlOptionsParams) && this.productsOptionsData.addOptionsToProductLink) {
            var options = this.el.$options.serializeArray(),
                optionsParams = {},
                pathParams = '';

            for (var i = 0; i < options.length; i++) {
                var $optionElement = $(this.el.$form.find('[name="' + options[i].name + '"]'));
                if (!($optionElement.hasClass('productOptionDateValue') || $optionElement.hasClass('productOptionAttachmentValue') ||
                    $optionElement.hasClass('productOptionTextValue'))
                ) {
                    if (!(options[i].name in optionsParams)) {
                        optionsParams[options[i].name] = [];
                    }
                    optionsParams[options[i].name].push(options[i].value);
                }
            }

            if (Object.keys(optionsParams).length > 0) {
                pathParams = '?';
            }
            for (var element in optionsParams) {
                if (optionsParams.hasOwnProperty(element)) {
                    if (optionsParams[element].length == 1) {
                        if (pathParams != '?') pathParams = pathParams + '&';
                        pathParams = pathParams + encodeURI(element.replace('optionValue', 'optionId_')) + "=" + encodeURI(optionsParams[element][0]);
                    } else {
                        for (var i = 0; i < optionsParams[element].length; i++) {
                            if (pathParams != '?') pathParams = pathParams + '&';
                            pathParams = pathParams + encodeURI(element.replace('optionValue', 'optionId_')) + '_' + i + "=" + encodeURI(optionsParams[element][i]);
                        }
                    }
                }
            }

            this.el.$form.find(`a[href^="${this.data.language.urlSeo}"]`).each((i, element) => {
                $(element).prop('href', $(element).prop('href').split('?')[0] + pathParams);
            });
        }
    },

    /**
     * Init options
     * @memberOf LC.BuyForm
     */
    initOptions: function () {
        // Before trigger
        this.trigger('initOptionsBefore');

        // get combination grid quantity inputs
        this.el.$gridCombinations = this.el.$form.find('[lc-data-grid-combination-id]');

        if (this.el.$productOptionAttachment.length) this.attachmentFields(this.el.$productOptionAttachment);

        this.el.$gridOption = this.el.$form.find('[data-lc-grid-options]');
        if (this.el.$gridOption.length) {
            var num = this.el.$gridOption.data('lc-grid-options');

            if (num == 1) {
                this.el.gridOptionsIds = [this.el.$gridOption.data('lc-product-option').id];
                this.el.gridOptionValues1 = this.el.$form.find('.gridOptionsInfo').data('grid-options1');
            } else {
                this.el.gridOptionsIds = [
                    this.el.$gridOption.data('lc-product-option').id1,
                    this.el.$gridOption.data('lc-product-option').id2,
                ];
                this.el.gridOptionValues1 = this.el.$form.find('.gridOptionsInfo').data('grid-options1');
                this.el.gridOptionValues2 = this.el.$form.find('.gridOptionsInfo').data('grid-options2');
            }
        }

        this.el.$options = this.el.$form.find(
            'select.productOptionSelectValue, input.productOptionRadioValue, input.productOptionCheckboxValue, input.productOptionBooleanValue, input.productOptionTextValue, textarea.productOptionLongTextValue, input.productOptionDateValue, input.productOptionAttachmentValue'
        );

        this.el.$options.change(this.changeOption.bind(this));

        this.el.$form.find('img.productOptionValueImage').each(function (a, b) {
            $(this).click(function (a, b, c) {
                $(a.target)
                    .parent('label')
                    .click();
            });
        });

        if (typeof this.quantityField === 'undefined') this.quantityField = this.el.$form.find('.quantitySelect').get(0);

        $(this.quantityField).change(this.changeQuantity.bind(this)); // Change event quantity

        // Getting required options
        this.requiredOptions = [];
        this.requiredGridOptions = [];
        for (var option in this.data.options) {
            if (this.data.options[option].required) {
                const optionTypes = [
                    'MULTIPLE_SELECTION_IMAGE',
                    'MULTIPLE_SELECTION',
                    'SELECTOR',
                    'SINGLE_SELECTION_IMAGE',
                    'SINGLE_SELECTION',
                ];

                if (optionTypes.includes(this.data.options[option].valueType))
                    this.requiredOptions.push(this.data.options[option].id);
            }
            // Adding 1 unit prices to price by quantity
            for (var key in this.data.options[option].values) {
                var optionValue = this.data.options[option].values[key];
                this.data.priceByQuantity.unshift({
                    from: 1,
                    optionValueId: optionValue.id,
                    basePrice: optionValue.basePrice,
                    retailPrice: optionValue.retailPrice,
                });
            }
        }

        if (this.el.$gridOption.length != 0) {
            // When there are grid options
            this.el.$gridOptions = this.el.$form.find('input.productGridQuantityValue');
            this.el.$gridOptions.change(this.changeGridOption.bind(this));

            this.el.$options.change(this.changeGridOption.bind(this));
        }

        if (this.el.$gridCombinations && this.el.$gridCombinations.length != 0) {
            // When there are grid combinations
            if (this.quantityField) this.quantityField.remove();

            this.el.$gridCombinations.change(this.changeGridCombinations.bind(this));
            this.el.$gridCombinationsType = this.el.$form.find('input[name=gridCombinationsType]');
        }

        this.el.$form
            .find('input.productOptionRadioValue:checked')
            .parent('div.productOptionRadioValue')
            .addClass('productOptionSelected');
        this.el.$form
            .find('input.productOptionCheckboxValue:checked')
            .parent('div.productOptionCheckboxValue')
            .addClass('productOptionSelected');

        this.addOptionsToProductLink();

        this.onChange(true);

        // Callback trigger
        this.trigger('initOptionsCallback');
        this.optionsInitialized = true;
    },

    /**
     * Change grid options
     * @memberOf LC.BuyForm
     * @param  {object} eventData
     */
    changeGridOption: function (eventData) {
        this.trigger('changeOptionBefore');

        // Callback trigger
        this.trigger('changeOptionCallback', eventData.target);

        this.onChange();
    },

    /**
     * Change grid combinations
     * @memberOf LC.BuyForm
     * @param  {object} eventData
     */
    changeGridCombinations: function (eventData) {
        this.trigger('changeCombinationBefore');

        // Callback trigger
        this.trigger('changeCombinationCallback', eventData.target);

        this.onChange();
    },

    /**
     * Change options
     * @memberOf LC.BuyForm
     * @param  {object} eventData
     */
    changeOption: function (eventData) {
        // Before trigger
        this.trigger('changeOptionBefore');

        this.addOptionsToProductLink();

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

        if ($(eventData.target).hasClass('productOptionAttachmentValue')) this.parseFile(eventData.target);

        // Callback trigger
        this.trigger('changeOptionCallback', eventData.target);

        this.onChange(true);
    },

    /**
     * Change quantity
     * @memberOf LC.BuyForm
     * @param  {object} eventData
     */
    changeQuantity: function (eventData) {
        var quantityValue = $(this.quantityField).val();
        // Before trigger
        this.trigger('changeQuantityBefore');

        // Callback trigger
        this.trigger('changeQuantityCallback', { quantity: quantityValue });

        this.onChange(true);
    },

    /**
     * Get form values
     * @memberOf LC.BuyForm
     */
    getFormValues: function () {
        var formValues;

        if (this.el.$options.length) {
            // Product detail (options in form)
            formValues = this.el.$options.serializeArray();
        } else {
            // List of products (default options selection)
            formValues = [];
            var selectedOptions = [];

            // Getting first available combination
            if (LC.global.settings.stockManagement && this.data.definition.stockManagement) {
                for (var key in this.data.stocks) {
                    if (this.data.stocks[key] > 0) {
                        var optionValues = key.split('_');
                        if (optionValues.length == 1) break;
                        optionValues = optionValues[1].split('-');
                        for (var i = 0; i < optionValues.length; i++) {
                            for (var option in this.data.options) {
                                if (this.data.options[option].values['id' + optionValues[i]]) {
                                    // Found option + value
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

        if (this.el.$productOptionAttachment.length) {
            var attachedFile = this.el.$form.find('input.productOptionAttachmentHiddenValue').serializeArray();
            for (var i = 0; i < attachedFile.length; i++) formValues.push(attachedFile[i]);
        }

        return formValues;
    },

    /**
     * Update buyFormSubmit
     * @memberOf LC.BuyForm
     * @param {object} button
     * @param {object} properties
     */
    updateButton: function (button, properties) {
        button.removeClass('selectOption notAvailable reserve buy');
        button.addClass(properties.className);
        button.prop('disabled', properties.disabled);
        button.data('buyFormSubmitName', properties.name);

        if (button.data('show-label') == true) button.html(properties.name);
        else button.html('');
    },

    /**
     * On change
     * @memberOf LC.BuyForm
     */
    onChange: function (baseStockForSubscription) {
        var selectedOptions = [];
        var selectedValues = [];
        var selectedStockValues = [];
        var optionId, optionValueId, currentOption;
        var baseStockForSubscription = baseStockForSubscription || false;

        // Before trigger
        this.trigger('onChangeBefore', formValues);

        this.setOptionRestrictions();

        var formValues = this.getFormValues();
        var quantityValue = parseInt($(this.quantityField).val());

        if (!quantityValue || isNaN(quantityValue)) quantityValue = 1;

        var validateCombinationStock = true;

        if (this.el.$gridCombinations && this.el.$gridCombinations.length != 0) {
            validateCombinationStock = false;
        } else if (this.el.$gridOptions) {
            var gridOptions = [];
            var totalQty = 0;
            validateCombinationStock = false;
            for (var i = 0; i < this.el.$gridOptions.length; i++) {
                var $e = $(this.el.$gridOptions[i]);
                var qty = $e.val();
                gridOptions.push($e.attr('name'));
                if ($.isNumeric(qty) && qty > 0) totalQty += qty;
            }
        }

        for (var i = 0; i < formValues.length; i++) {
            optionId = formValues[i].name.replace('optionValue', '');
            optionValueId = formValues[i].value;
            currentOption = this.data.options['id' + optionId];
            if (Object.getLength(currentOption.values)) {
                if (currentOption.values['id' + optionValueId]) {
                    selectedOptions.push(parseInt(optionId));
                    selectedValues.push(currentOption.values['id' + optionValueId]);
                    selectedValues[selectedValues.length - 1].uniquePrice = currentOption.uniquePrice;
                    if (currentOption.combinable) selectedStockValues.push(parseInt(optionValueId));
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
            if (selectedOptions.indexOf(this.requiredOptions[i]) == -1)
                if (!this.el.$form.find('.productOption' + this.requiredOptions[i]).hasClass('_restricted_'))
                    /*checking if option is restricted*/
                    requiredOptions.push(this.requiredOptions[i]);

        var basePrice = this.data.definition.productBasePrice,
            retailPrice = this.data.definition.productRetailPrice,
            price = 0;
        var alternativeBasePrice = this.data.definition.productAlternativeBasePrice,
            alternativeRetailPrice = this.data.definition.productAlternativeRetailPrice,
            alternativePrice = 0;

        var quantityMultiplier = 1;

        if (requiredOptions.length || !this.el.$options.length) {
            // Set default product prices when options are missing
            basePrice = this.data.definition.basePrice;
            retailPrice = this.data.definition.retailPrice;
            alternativeBasePrice = this.data.definition.alternativeBasePrice;
            alternativeRetailPrice = this.data.definition.alternativeRetailPrice;
        } else {
            for (var i = 0; i < selectedValues.length; i++) {
                if (!selectedValues[i].uniquePrice) {
                    basePrice += selectedValues[i].basePrice;
                    retailPrice += selectedValues[i].retailPrice;
                    alternativeBasePrice += selectedValues[i].alternativeBasePrice;
                    alternativeRetailPrice += selectedValues[i].alternativeRetailPrice;
                }
            }
        }

        var stock = 999999999,
            lockedStock = 0,
            backorderPrevision = 999999999,
            combinationFound = true,
            combinationId = 0,
            stockKeys = [],
            backorderKeys = [],
            sku = '',
            ean = '',
            stockMatch = 'WH[0-9]+_',
            availableWarehouses = '',
            warehousesIdArray = [];

        warehousesIdArray = this.stockWarehouses.map(function (el) {
            return el.warehousesStructureId;
        });

        if (warehousesIdArray.length) availableWarehouses = warehousesIdArray.join('|');
        else stockMatch = null; // no match

        if (stockMatch !== null && availableWarehouses.length > 0) {
            stockMatch = '(' + availableWarehouses + ')+_';
        }

        if (validateCombinationStock) {
            for (var key in this.data.combinations) {
                if (key.match('PC_' + selectedStockValues + '$')) {
                    sku = this.data.combinations[key].sku;
                    ean = this.data.combinations[key].ean;
                    combinationId = this.data.combinations[key].id;
                    break;
                }
            }

            if (LC.global.settings.stockManagement && this.data.definition.stockManagement) {
                (stock = 0), (backorderPrevision = 0), (combinationFound = false);


                for (var key in this.data.stocks) {
                    if (key.match(stockMatch + selectedStockValues + '$')) {
                        if (stockKeys.indexOf(key) == -1) stockKeys.push(key); // Collect used stock keys
                        stock += this.data.stocks[key];
                        combinationFound = true;
                    }
                }

                // adding stock previsions
                for (i = 0; i < this.data.stockPrevisions.length; i++) {
                    var key = this.data.stockPrevisions[i].warehousesStructureId;
                    if (key.match(stockMatch + selectedStockValues + '$')) {
                        if (stockKeys.indexOf(key) == -1) stockKeys.push(key); // Collect used stock keys
                        stock += this.data.stockPrevisions[i].stock;
                        combinationFound = true;
                    }
                }

                // adding backorder previsions
                for (i = 0; i < this.data.backorderPrevisions.length; i++) {
                    var key = this.data.backorderPrevisions[i].warehousesStructureId;
                    if (key.match(stockMatch + selectedStockValues + '$')) {
                        if (backorderKeys.indexOf(key) == -1) backorderKeys.push(key); // Collect used backorder stock keys
                        backorderPrevision += this.data.backorderPrevisions[i].stock;
                    }
                }

                if (!combinationFound) stock = -1;
            }

            if (combinationId && combinationId in this.data.stockLocks) {
                lockedStock = this.data.stockLocks[combinationId].totalQuantity;
                lockedStockExpiration = this.data.stockLocks[combinationId].expirations[0];
            }
        }

        // Stock Alerts
        if (this.$stockSubscriptionButton.length) {
            this.$stockSubscriptionButton.attr('combinationId', combinationId);
            if (stock === 0) {
                this.$stockSubscriptionButton
                    .addClass('product-stock-alert-active')
                    .removeClass('product-stock-alert-hidden');
            } else {
                this.$stockSubscriptionButton
                    .addClass('product-stock-alert-hidden')
                    .removeClass('product-stock-alert-active');
            }
        } else {
            if (!window.productStocks) window.productStocks = {};
            window.productStocks[this.data.id] = selectedStockValues;
        }

        // Prices outputs
        if (this.data.definition.offer && basePrice > retailPrice) {
            price = retailPrice;
            alternativePrice = alternativeRetailPrice;
            this.el.$form.find('.product-basePrice').show();
            this.el.$form.find('.product-basePrice[data-lc-show-alternative-price="true"]').show();
            this.el.$form.find('.product-saving').show();
            var saving = basePrice - retailPrice;
            this.el.$form.find('.product-saving .price').replaceWith(outputHtmlCurrency(saving));
            this.el.$form.find('.product-saving .percent').html(((saving * 100) / basePrice).toFixed(0));
        } else {
            price = basePrice;
            alternativePrice = alternativeBasePrice;
            this.el.$form.find('.product-basePrice').hide();
            this.el.$form.find('.product-basePrice[data-lc-show-alternative-price="true"]').hide();
            this.el.$form.find('.product-saving').hide();
        }

        this.el.$form.find('.product-price .price').replaceWith(outputHtmlCurrency(price));
        this.el.$form
            .find('.product-price[data-lc-show-alternative-price="true"] .price')
            .replaceWith(outputHtmlCurrency(alternativePrice));
        this.el.$form.find('.product-basePrice .price').replaceWith(outputHtmlCurrency(basePrice));
        this.el.$form
            .find('.product-basePrice[data-lc-show-alternative-price="true"] .price')
            .replaceWith(outputHtmlCurrency(alternativeBasePrice));


        if (stock >= 0) {
            this.el.$form.find('.product-stock').removeClass('not-init').find('.stock').html(stock);

            if (stock > 0) {
                this.el.$form.find('.product-stock').removeClass('no-stock').addClass('stock-ok');
                this.el.$form.find('.product-stock .stock').html(stock);

                if (stock === 1) {
                    this.el.$form.find('.stockText').html(LC.global.languageSheet.stockSingular.replace('{{stock}}', stock));
                } else {
                    this.el.$form.find('.stockText').html(LC.global.languageSheet.stockPlural.replace('{{stock}}', stock));
                }
            } else {
                this.el.$form.find('.product-stock').removeClass('stock-ok').addClass('no-stock');
                this.el.$form.find('.stockText').html(LC.global.languageSheet.stockNone.replace('{{stock}}', stock));
            }
        } else {
            this.el.$form.find('.product-stock').addClass('not-init');
        }

        if (sku.length > 0) this.el.$form.find('.product-combinations-sku').html(sku);
        if (ean.length > 0) this.el.$form.find('.product-combinations-ean').html(ean);

        // Getting availability interval
        var availabilityInterval;
        for (var i = this.productAvailabilities.length - 1; i > -1; i--) {
            if (this.productAvailabilities[i].stock < stock) break;
            availabilityInterval = this.productAvailabilities[i];
        }
        // Printing availability
        if (availabilityInterval) {
            this.el.$form.find('.product-stock').removeClass('not-init');
            this.el.$form.find('.product-stock .availabilityImage')
                .html('<img src="' + availabilityInterval.language.image + '" onerror="$(this).remove();">');
            this.el.$form.find('.product-stock .availabilityName').html(availabilityInterval.language.name.replace('{{stock}}', stock));
        } else {
            this.el.$form.find('.product-stock .availabilityImage').hide();
            this.el.$form.find('.product-stock .availabilityName').hide();
        }
        if (this.data.definition.onRequest && availabilityInterval) {
            this.el.$form.find('.product-stock .availabilityName').html(availabilityInterval.language.name.replace('{{onRequestDays}}', this.data.definition.onRequestDays));
        }

        // Buy Button properties
        var buyButtonProps = {};
        var displaySubscription = null;

        if ('countdown' in this) this.countdown.destroy();

        this.el.$form.find('.lcStockLock').removeClass('active');
        this.el.$form.find('.lcStockLockText').empty();

        var lsBUY = LC.global.languageSheet.buy;
        if (this.el.$gridCombinationsType && this.el.$gridCombinationsType.val() == 'update') {
            lsBUY = LC.global.languageSheet.update;
        }

        if (requiredOptions.length) {
            buyButtonProps.className = 'selectOption';
            buyButtonProps.name = LC.global.languageSheet.selectOption.replace(
                '{{option}}',
                this.data.options['id' + requiredOptions[0]].name
            );
            buyButtonProps.disabled = true;
            displaySubscription = 'none';
        } else if (!validateCombinationStock && totalQty == 0) {
            // grid options
            buyButtonProps.className = 'selectOption';
            buyButtonProps.name = LC.global.languageSheet.selectOption.replace(
                '{{option}}',
                this.data.options['id' + this.requiredGridOptions[0]].name
            );
            buyButtonProps.disabled = true;
            displaySubscription = 'none';
        } else if (this.data.definition.onRequest && stock >= 0) {
            // onRequest products will be always available
            buyButtonProps.className = 'buy';
            buyButtonProps.name = lsBUY;
            buyButtonProps.disabled = false;
            displaySubscription = 'none';
        } else if (stock < 0) {
            buyButtonProps.className = 'notAvailable';
            buyButtonProps.name = LC.global.languageSheet.notAvailable;
            buyButtonProps.disabled = true;
            displaySubscription = 'none';
        } else if (stock == 0 || quantityValue > stock) {
            if (LC.global.settings.allowReservations === true && this.data.definition.backorder != 'NONE') {
                if (
                    (this.data.definition.backorder == 'WITH_PREVISION' && backorderPrevision + stock >= quantityValue) ||
                    this.data.definition.backorder == 'WITH_AND_WITHOUT_PREVISION' || this.data.definition.backorder == 'WITHOUT_PREVISION'
                ) {
                    // allow reserve
                    buyButtonProps.className = 'reserve';
                    buyButtonProps.name = LC.global.languageSheet.reserve;
                    buyButtonProps.disabled = false;
                } else {
                    // disallow reserve
                    buyButtonProps.className = 'notAvailable';
                    buyButtonProps.name = LC.global.languageSheet.notAvailable;
                    buyButtonProps.disabled = true;

                    if (baseStockForSubscription && stock == 0) displaySubscription = 'block';
                    else displaySubscription = 'none';
                }

                if (baseStockForSubscription && stock == 0) displaySubscription = 'block';
                else displaySubscription = 'none';
            } else {
                buyButtonProps.className = 'notAvailable';
                buyButtonProps.name = LC.global.languageSheet.notAvailable;
                buyButtonProps.disabled = true;

                if (baseStockForSubscription && stock == 0) displaySubscription = 'block';
                else displaySubscription = 'none';
            }
        } else if (stock - lockedStock <= 0) {
            buyButtonProps.className = 'notAvailable reserved';
            buyButtonProps.name = LC.global.languageSheet.stockReserved;
            buyButtonProps.disabled = true;
            displaySubscription = 'none';

            this.el.$form.find('.lcStockLock').addClass('active');
            if (lockedStockExpiration) {
                this.countdown = new LC.combinationCountdown({
                    endDate: lockedStockExpiration.expires,
                    quantity: lockedStockExpiration.quantity,
                });
                this.el.$form.find('.lcStockLockText').append(this.countdown.$container);
            } else {
                this.el.$form.find('.lcStockLockText').append(LC.global.languageSheet.stockReservedUserText);
            }
        } else {
            buyButtonProps.className = 'buy';
            buyButtonProps.name = lsBUY;
            buyButtonProps.disabled = false;
            displaySubscription = 'none';
        }

        this.el.$buyFormSubmit = this.el.$form.find('button[type="submit"]');
        this.updateButton(this.el.$buyFormSubmit, buyButtonProps);

        this.enabledButtons = this.el.$buyFormSubmit.filter(':enabled');

        this.updatePriceByQuantity(selectedValues);

        var offsetContainer = this.el.$form.find('.productOffsetMessage'),
            stcStockOffset = {};

        if (offsetContainer.length && stockKeys.length && this.stockWarehouses.length) {
            offsetContainer.html('');

            var acumulatedStock = 0,
                offsetDays = 0,
                requestOffset = 0,
                previsionDate = new Date(),
                productData = this.data;


            if (this.data.definition.onRequest) requestOffset = this.data.definition.onRequestDays;

            $.each(this.stockWarehouses, function (i, wStructure) {
                for (i = 0; i < stockKeys.length; i++) {
                    if (stockKeys[i].indexOf(wStructure.warehousesStructureId) != -1) {
                        // we're using stock of this warehouse
                        stcStockOffset[stockKeys[i]] = wStructure.offsetDays;
                        if (productData.stocks[stockKeys[i]]) {
                            acumulatedStock += productData.stocks[stockKeys[i]];
                            if (wStructure.offsetDays > offsetDays) offsetDays = wStructure.offsetDays;
                        }
                    }
                    if (acumulatedStock >= quantityValue) {
                        return false; // left loop
                    }
                }
            });
            previsionDate.setDate(previsionDate.getDate() + offsetDays);

            if (acumulatedStock < quantityValue) {
                // We need to use the previsions
                var auxPrevisionDate = new Date();
                productData.stockPrevisions.sort(function (a, b) {
                    return new Date(a.incomingDate) > new Date(b.incomingDate);
                }); // sort the previsions by the incoming date

                $.each(productData.stockPrevisions, function (i, prevision) {
                    for (i = 0; i < stockKeys.length; i++) {
                        if (stockKeys[i].indexOf(prevision.warehousesStructureId) != -1 && prevision.stock > 0) {
                            acumulatedStock += prevision.stock;
                            offsetDays = stcStockOffset[prevision.warehousesStructureId] || offsetDays;
                            auxPrevisionDate = new Date(prevision.incomingDate);
                        }
                        if (acumulatedStock >= quantityValue) {
                            return false; // left loop
                        }
                    }
                });
                auxPrevisionDate.setDate(auxPrevisionDate.getDate() + offsetDays);

                if (auxPrevisionDate > previsionDate) previsionDate = auxPrevisionDate;
            }

            if (acumulatedStock < quantityValue && LC.global.settings.allowReservations && this.data.definition.backorder != 'NONE') {
                // We need to use the backorder previsions if we have them
                var auxPrevisionDate = new Date();
                productData.backorderPrevisions.sort(function (a, b) {
                    return new Date(a.incomingDate) > new Date(b.incomingDate);
                }); // sort the previsions by the incoming date

                $.each(productData.backorderPrevisions, function (i, prevision) {
                    for (i = 0; i < backorderKeys.length; i++) {
                        if (backorderKeys[i].indexOf(prevision.warehousesStructureId) != -1 && prevision.stock > 0) {
                            acumulatedStock += prevision.stock;
                            offsetDays = stcStockOffset[prevision.warehousesStructureId] || offsetDays;
                            auxPrevisionDate = new Date(prevision.incomingDate);
                        }
                        if (acumulatedStock >= quantityValue) {
                            return false; // left loop
                        }
                    }
                });
                auxPrevisionDate.setDate(auxPrevisionDate.getDate() + offsetDays);

                if (auxPrevisionDate > previsionDate) previsionDate = auxPrevisionDate;
            }

            if (
                acumulatedStock >= quantityValue &&
                previsionDate.setHours(0, 0, 0, 0) > new Date().setHours(0, 0, 0, 0)
            ) {
                var formattedDate = moment(previsionDate).format(CALENDAR_PLUGIN_DATE_FORMAT);
                offsetContainer.html(
                    LC.global.languageSheet.warehouseOffsetMessage
                        .replace('{{offsetDays}}', formattedDate)
                        .replace('{{previsionDate}}', formattedDate)
                );
            } else if (acumulatedStock < quantityValue && requestOffset)
                offsetContainer.html(LC.global.languageSheet.onRequestProductMessage.replace('{{days}}', requestOffset));
        }

        // Callback Pmt if exists
        if (LC.global.settings.showTaxesIncluded === true) var sendPrice = alternativePrice * quantityValue;
        else var sendPrice = price * quantityValue;

        if (this.data.restrictions && this.data.restrictions.length) {
            this.setOptionRestrictions();
        }

        // Callback trigger
        this.trigger('onChangeCallback', {
            price: price,
            basePrice: basePrice,
            requiredOptions: requiredOptions,
            selectedOptions: selectedOptions,
            stock: stock,
            quantity: quantityValue,
        });
    },

    setOptionRestrictions: function () {
        var form = this.el.$form;

        var getField = function (valueId) {
            return form.find('*[value=' + valueId + ']');
        };
        var getChecked = function (valueId) {
            var field = getField(valueId).first();

            return field.length ? field[0].checked || field[0].selected : false;
        };
        var disableField = function (valueId) {
            var field = getField(valueId);
            var ind, i, k;

            if (!field) return;

            field
                .addClass('_restricted_')
                .attr('disabled', 1)
                .css('display', 'none')
                .removeAttr('checked');

            if (field.parents('tr.productOptionValueTable').length == 1) {
                field
                    .parents('tr.productOptionValueTable')
                    .css('display', 'none')
                    .removeClass('productOptionSelected');
            } else if (field[0].tagName.toLowerCase() != 'option')
                parentNode = field
                    .parent('.productOptionValue')
                    .css('display', 'none')
                    .removeClass('productOptionSelected');
        };

        var disableOption = function (optionId) {
            var option = form.find('.productOption' + optionId);

            option.addClass('_restricted_').css('display', 'none');

            option
                .find('select')
                .addClass('_restricted_')
                .attr('disabled', 1)
                .attr('selectedIndex', -1);
            //option.find('option').addClass('_restricted_').attr('disabled',1).attr('selected',0);
            option
                .find('input.productOptionRadioValue')
                .addClass('_restricted_')
                .attr('disabled', 1)
                .removeAttr('checked')
                .parent('.productOptionRadioValue')
                .removeClass('productOptionSelected');
        };

        /*reset all options*/
        form.find('._restricted_')
            .removeClass('_restricted_')
            .removeAttr('disabled')
            .css('display', '')
            .css('display', '')
            .each(function (index, field) {
                if (field.tagName.toLowerCase() != 'option') {
                    $(field)
                        .parent('.productOptionValue')
                        .css('display', '');
                    if ($(field).parents('tr.productOptionValueTable').length == 1) {
                        $(field)
                            .parents('tr.productOptionValueTable')
                            .css('display', '');
                    }
                }
            });

        if (this.data.restrictionsMain && this.data.restrictionsMain.length) {
            for (var ind = 0; ind < this.data.restrictionsMain.length; ind++) {
                var restriction = this.data.restrictionsMain[ind];

                if (!getChecked(restriction.MAIN)) continue;

                for (i = 0; i < restriction.OPTIONS.length; i++) disableOption(restriction.OPTIONS[i]);

                for (i = 0; i < restriction.VALUES.length; i++) disableField(restriction.VALUES[i]);
            }

            var anyChecked = 0;
            form.find('.productOption').each(function (index, div) {
                div = $(div);
                var someChecked = div.find(':checked');
                if (!someChecked.length) {
                    var somethingChecked = div.find(':enabled').first();
                    somethingChecked
                        .prop('checked', 1)
                        .attr('selected', 1)
                        .parent('.productOptionRadioValue, .productOptionCheckboxValue')
                        .addClass('productOptionSelected');
                    anyChecked += somethingChecked.length;
                }
            });
            if (anyChecked) this.onChange();
        }

        if (this.data.restrictions && this.data.restrictions.length) {
            for (i = 0; i < this.data.restrictions.length; i++) {
                var limitation = this.data.restrictions[i];

                var matching = [],
                    noMatching = [];

                for (k = 0; k < limitation.length; k++) {
                    var valueId = limitation[k];

                    if (getChecked(valueId)) matching.push(valueId);
                    else noMatching.push(valueId);
                }

                if (noMatching.length == 1) disableField(noMatching[0]);
            }
        }
    },

    updatePriceByQuantity: function (selectedOptions) {
        if (!this.el.$priceByQuantityBox.length) return;

        selectedOptions = selectedOptions.map(function (i) {
            return i.id;
        });

        this.el.$priceByQuantityBox.html('');

        var prices = this.getPrices(selectedOptions),
            table = '<table>',
            label = '',
            pricesArray = new Array();

        for (var key in prices)
            pricesArray.push({
                from: prices[key].from,
                retailPrice: prices[key].retailPrice,
                basePrice: prices[key].basePrice,
            });

        if (pricesArray.length < 2) return;

        for (var i = 0; i < pricesArray.length; i++) {

            if (i == pricesArray.length - 1)
                label = LC.global.languageSheet.moreThanNUnits.replace('{{n}}', pricesArray[i].from - 1);
            else if (i == 0 && pricesArray[i + 1].from == 2)
                label = LC.global.languageSheet.oneUnit.replace('{{n}}', pricesArray[i].from);
            else if (i == 0)
                label = LC.global.languageSheet.fromNToMUnits
                    .replace('{{n}}', 1)
                    .replace('{{m}}', pricesArray[i + 1].from - 1);
            else if (pricesArray[i].from + 1 == pricesArray[i + 1].from)
                label = LC.global.languageSheet.nUnits.replace('{{n}}', pricesArray[i].from);
            else
                label = LC.global.languageSheet.fromNToMUnits
                    .replace('{{n}}', pricesArray[i].from)
                    .replace('{{m}}', pricesArray[i + 1].from - 1);

            var price = pricesArray[i].basePrice,
                basePriceData = '';
            if (
                this.data.definition.offer &&
                pricesArray[i].basePrice > pricesArray[i].retailPrice &&
                pricesArray[i].retailPrice != 0
            ) {
                price = pricesArray[i].retailPrice;
                basePriceData = ' data-base-price="' + pricesArray[i].basePrice + '"';
            }
            table += '<tr><td class="messageColumn">' + label + '</td><td class="priceColumn"' + basePriceData + '>' + outputHtmlCurrency(price) + '</td></tr>';
        }

        table += '</table>';

        this.el.$priceByQuantityBox.html(table);
    },

    getPrices: function (optionValues) {
        optionValues = optionValues || [];
        optionValues.unshift(0);

        var prices = {};

        for (var i = 0; i < this.data.priceByQuantity.length; i++) {
            var priceData = this.data.priceByQuantity[i];

            if (optionValues.indexOf(priceData.optionValueId) > -1) {
                prices['p' + priceData.from] = prices['p' + priceData.from] || {
                    from: priceData.from,
                    basePrice: 0,
                    retailPrice: 0,
                };
            }
        }

        for (var key in prices) {
            for (var i = 0; i < optionValues.length; i++) {
                var interval = this.getPriceInterval(prices[key].from, optionValues[i]);
                prices[key].basePrice += interval.basePrice;
                prices[key].retailPrice += interval.retailPrice;
            }
        }

        return prices;
    },

    getPriceInterval: function (from, optionValueId) {
        for (var i = this.data.priceByQuantity.length - 1; i > -1; i--) {
            if (this.data.priceByQuantity[i].from > from) continue;

            if (this.data.priceByQuantity[i].optionValueId != optionValueId) continue;

            return this.data.priceByQuantity[i];
        }

        return { from: from, basePrice: 0, retailPrice: 0 };
    },

    /**
     * Returns min quantity buy without quantity field
     * @param {object} def - product definition data object
     * @returns {number}
     */
    getQuantityValue: function (def) {
        var minQuantity = 1;

        if (def.minOrderQuantity)
            minQuantity = def.minOrderQuantity;

        if (def.multipleOrderQuantity > 0) {
            if (def.multipleActsOver > 0) {
                if (minQuantity >= def.multipleActsOver && minQuantity % def.multipleOrderQuantity !== 0) {
                    var difference = minQuantity % def.multipleOrderQuantity;
                    minQuantity = minQuantity + (def.multipleOrderQuantity - difference);
                }
            } else {
                if (minQuantity < def.multipleOrderQuantity) {
                    minQuantity = def.multipleOrderQuantity;
                } else {
                    if (minQuantity % def.multipleOrderQuantity !== 0) {
                        var difference = minQuantity % def.multipleOrderQuantity;
                        minQuantity = minQuantity + (def.multipleOrderQuantity - difference);
                    }
                }
            }
        }
        return minQuantity;
    },

    /**
     * Get form data
     * @memberOf LC.BuyForm
     */
    getFormData: function () {
        var formValues = this.getFormValues();
        var options = {},
            optionsArray = [],
            value = '';

        for (var i = 0; i < formValues.length; i++) {
            if (!options[formValues[i].name]) options[formValues[i].name] = [];
            value = formValues[i].value;
            try {
                value = JSON.parse(formValues[i].value);
            } catch (error) { }
            if ($.type(value) === 'object') {
                options[formValues[i].name].push(value);
            } else {
                options[formValues[i].name].push({ value: formValues[i].value });
            }
        }

        for (var option in options)
            optionsArray.push({ id: option.replace('optionValue', ''), values: options[option] });

        if (typeof this.quantityField === 'undefined') {
            this.quantityField = this.el.$form.find('.quantitySelect').get(0);
        }

        var response = {
            id: this.data.id,
            quantity: this.quantityField ? this.quantityField.value : this.getQuantityValue(this.data.definition),
            options: optionsArray,
            alternativeImage: this.alternativeImageField ? this.alternativeImageField.value : '',
            sectionId: this.linkedSectionId,
            parentHash: this.el.$form.find('input[name="parentHash"]')?.val() || '',
            mode: this.el.$form.find('#mode').length ? this.el.$form.find('#mode').val() : ''
        };

        if (this.el.$gridOption.length > 0) {
            response.gridValues = {};
            this.el.$gridOptions.each(function (i, e) {
                var val = $(e).val();
                if (val.length == 0) val = 0;
                response.gridValues[$(e).attr('name')] = val;
            });

            response.gridOptionsIds = this.el.gridOptionsIds;
            response.gridOptionValues1 = this.el.gridOptionValues1;

            if (this.el.gridOptionValues2) {
                response.gridOptionValues2 = this.el.gridOptionValues2;
            }
        }

        if (this.el.$gridCombinations && this.el.$gridCombinations.length > 0) {
            response.gridCombinationsValues = {};
            this.el.$gridCombinations.each(function (i, e) {
                response.gridCombinationsValues[$(e).attr('name')] = parseInt($(e).val());
            });
        }

        return response;
    },

    /**
     * Submit event
     * @memberOf LC.BuyForm
     * @param {object} event
     */
    submit: function (event) {
        event.preventDefault();

        // Before trigger
        this.trigger('submitBefore', event);

        if (!this.formIsValid()) return false;

        var data = this.getFormData();

        // Before submit
        LC.resources.pluginListener('onAddProduct', event, data);

        var submitUrl = '';
        if (this.linkedSectionId > 0) {
            submitUrl = LC.global.routePaths.BASKET_INTERNAL_ADD_LINKED;
        } else {
            submitUrl = LC.global.routePaths.BASKET_INTERNAL_ADD_PRODUCT;
        }

        // Send form
        $.post(
            submitUrl,
            { data: JSON.stringify(data) },
            this.callback,
            'json'
        );

        // Disable buy button
        this.el.$buyFormSubmit.prop('disabled', true);

        // Callback trigger
        this.trigger('submitCallback', event);
    },

    /**
     * Return form is valid
     * @memberOf LC.BuyForm
     * @return {boolean}
     */
    formIsValid: function () {
        if (!this.el.$form.isValid()) return false;

        if (this.el.$gridCombinationsType && this.el.$gridCombinationsType.val() == 'buy') {
            var sumCombination = 0;
            this.el.$gridCombinations.each(function (index, el) {
                sumCombination += parseInt($(el).val());
            });
            if (sumCombination === 0) return false;
        }

        return true;
    },

    /**
     * Callback
     * @memberOf LC.BuyForm
     * @param {object} data
     */
    callback: function (data) {
        this.trigger('callbackBefore', data.data);

        var page = $('body').data('lc-page');

        if (data.data.response.success) {
            if (
                this.linkedSectionId > 0 &&
                (page === 'checkoutBasket' || page === 'checkoutPaymentAndShipping')
            ) {
                // Refresk Basket
                $('html, body').animate({ scrollTop: 0 }, 'slow', function () {
                    window.location.reload(true);
                });
            } else {
                // Reload miniBasket
                LC.miniBasket.reload();
                LC.dataEvents.reloadCustomize();
            }
        } else {
            LC.notify(data.data.response.message, { type: 'danger' });
        }

        // Enable buy button
        this.enabledButtons.prop('disabled', false);

        if (this.el.$gridCombinations && this.el.$gridCombinations.length > 0) {
            var qtText = 'quantity-';

            this.el.$gridCombinations.each(
                function (index, el) {
                    var inputData = JSON.parse(
                        $(el)
                            .attr('name')
                            .substring(qtText.length)
                    );
                    this.updateCombinationsFields([inputData.productHash], parseInt($(el).val()));
                }.bind(this)
            );

            totalProdsInBasket = this.getCombinationsFormTotalQuantity();

            this.updateCombinationsFormType(totalProdsInBasket);
        }

        // Callback trigger
        this.trigger('callback', data.data);

        if (LC.config.showModalBasket && (LC.global.settings.isMobile === true || window.innerWidth < MEDIA_MOBILE)) {
            var modalContent = '';

            if (data.data.stockLock) {
                modalContent +=
                    `<div class="basketCountdown" data-lc-basket-expires='{"expires": "${data.data.stockLock.expires}"}'>
                         <div class="active">${LC.global.languageSheet.lockedStockRemainingTimePopup}</div>
                         <div class="expired">${LC.global.languageSheet.lockedStockExpiredTimePopUp}</div>
                     </div>`;
            }
            modalContent +=
                `<div id="modalBasketButtons">
                     <a href="${LC.global.routePaths.CHECKOUT_BASKET}" class="modalBasketEndOrder ${BTN_PRIMARY_CLASS}">${LC.global.languageSheet.basketEndOrder}</a>
                     <a data-dismiss="modal" data-bs-dismiss="modal" class="modalBasketContinueShopping ${BTN_SECONDARY_CLASS}">${LC.global.languageSheet.basketContinueShopping}</a>
                     <a href="${LC.global.routePaths.USER}" class="modalBasketMyAccount ${BTN_SECONDARY_CLASS}">${LC.global.languageSheet.myAccount}</a>
                 </div>`;

            this.el.$buyFormSubmit.box({
                uid: 'mobileBasketModal',
                source: modalContent,
                showFooter: false,
                triggerOnClick: false,
                type: 'html',
                size: 'small',
            });
        }
    },

    /**
     * updateCombinationsFields method
     * @memberOf LC.BuyForm
     * @param {array} hashes
     * @param {numeric} value
     */
    updateCombinationsFields: function (hashes, value) {
        if (hashes.length) {
            $('[id="buyForm' + this.data.id + '"]').each(function (index, buyForm) {
                var qtText = 'quantity-';

                if (buyForm.module.el.$gridCombinations.length) {
                    buyForm.module.el.$gridCombinations.each(function (index, inputCombination) {
                        var $inputCombination = $(inputCombination);
                        var hash = $inputCombination.data('lcProductHash');

                        if ($.inArray(hash, hashes) != -1) {
                            var inputData = JSON.parse($inputCombination.attr('name').substring(qtText.length));
                            inputData.quantity = value;

                            $inputCombination.attr('name', qtText + JSON.stringify(inputData));
                            $inputCombination.val(value);
                        }
                    });
                }
            });
        }
    },

    /**
     * getCombinationsFormTotalQuantity method
     * @memberOf LC.BuyForm
     */
    getCombinationsFormTotalQuantity: function () {
        var totalQuantity = 0;

        this.el.$gridCombinations.each(function (i, e) {
            totalQuantity += parseInt($(e).val());
        });

        return totalQuantity;
    },

    /**
     * updateCombinationsFormType method
     * @memberOf LC.BuyForm
     * @param {numeric} totalProdsInBasket
     */
    updateCombinationsFormType: function (totalProdsInBasket) {
        var getCombinationsFormType = function (totalProdsInBasket) {
            if (totalProdsInBasket > 0) return 'update';

            return 'buy';
        };

        if (totalProdsInBasket == null) {
            totalProdsInBasket = this.getCombinationsFormTotalQuantity($('#buyForm' + this.data.id).get(0));
        }

        $('[id="buyForm' + this.data.id + '"]').each(function (index, buyForm) {
            if (buyForm.module.el.$gridCombinationsType) {
                buyForm.module.el.$gridCombinationsType.val(getCombinationsFormType(totalProdsInBasket));
                buyForm.module.onChange();
            }
        });
    },

    attachmentFields: function (element) {
        var me = this;
        this.target = element;

        if (!this.target) return;

        this.options = {};
        this.options = JSON.parse(element.attr('data-options'));
        this.fieldName = element.attr('data-option-value');

        this.parseFile = function (field) {
            var files = field.files;
            if (files.length > 0) {
                var options = $(field).closest('.lcProductOptionAttachment').data('options');
                if (files[0].size > (options.maxSize * 1024 * 1000)) {
                    LC.notify(LC.global.languageSheet.attachFileMaxSize.replace('{{maxSize}}', options.maxSize), { type: 'danger' });
                    $(field).val('');
                    $(field).closest('.productOption').find('input.productOptionAttachmentHiddenValue').val('');
                } else {
                    var reader = new FileReader();
                    reader.readAsDataURL(files[0]);
                    reader.onload = function () {
                        var optionValue = {
                            extension: files[0].name.split('.').pop(),
                            fileName: files[0].name,
                            value: reader.result
                        };
                        $(field).closest('.productOption').find('input.productOptionAttachmentHiddenValue').val(JSON.stringify(optionValue));
                    };
                    reader.onerror = function (error) {
                        $(field).val('');
                        $(field).closest('.productOption').find('input.productOptionAttachmentHiddenValue').val('');
                        LC.notify(LC.global.languageSheet.attachFileError.replace('{{maxSize}}', options.maxSize), { type: 'danger' });
                    };
                }
            }
        }

    },

    clearComparisonTable: function ($trigger) {
        var $comparisonTableItem = $trigger.parents('[data-lc-product-comparison-item]');
        if ($comparisonTableItem.length) {
            var productId = $comparisonTableItem.data('product');
            var $table = $('#comparison-table');
            $table.find(`[data-lc-product-comparison-item="${productId}"]`)
                .fadeOut('slow', function () {
                    this.remove();
                    if ($table.find('[data-lc-product-comparison-item]').length === 0) {
                        $table.before(`<div class="empty-text">${LC.global.languageSheet.noProductsInComparison}</div>`);
                        $table.remove();
                    }
                });
        }
    },

    pseudoSubmits: function () {
        this.el.$pseudoSubmits.on('click', function (event) {
            event.preventDefault();
            this.custom_callback = function () {
                $('html,body').animate({ scrollTop: 0 }, 'slow', function () {
                    window.location.reload(true);
                });
            };
            this.submit(event);
        }.bind(this)
        );
    },
}, LC.buyFormProperties);

/**
 * @class LC.ShoppingListForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.AddToNewShoppingListForm = LC.Form.extend({
    name: 'addToNewShoppingListForm',

    initialize: function () {
        this.el.$form.find('button[type="submit"]').prop('disabled', false);
    },

    submit: function (event) {
        event.preventDefault();

        this.el.$form.find('button[type="submit"]').prop('disabled', true);

        // Call submit from parent class
        this.superForm('submit', event);
    },

    /**
     * Callback
     * @memberOf LC.LoginForm
     */
    callback: function (response) {
        if (!this.trigger('callbackSubsitute')) {
            // Before trigger
            this.trigger('callbackBefore');

            if (response.data.response.success === 1) {
                LC.global.session.shoppingLists.push(response.data.data);
                $('#' + this.el.$form.attr('data-lc-uidModal')).attr('data-lc-new-list', JSON.stringify(response.data.data));
                $('#' + this.el.$form.attr('data-lc-uidModal')).modal('hide');
                this.el.$form.find('button[type="submit"]').prop('disabled', false);
                this.el.form.reset();
            } else {
                this.showMessage(response.data.response.message, 'danger');
            }

            // Callback trigger
            this.trigger('callback', response);
        }
    },

});


/**
 * @class LC.LoginForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.LoginForm = LC.Form.extend({
    name: 'loginForm',

    initialize: function () {
        let lcUserWarnings = this.el.$form.data('lc-user-warnings'),
            userWarnings = (lcUserWarnings && lcUserWarnings.length > 0) ? lcUserWarnings.split(",") : [];

        if (userWarnings.length > 0) {
            let modalId = 'modalVerifyUser',
                modalClass = 'verifyUser',
                message = '';

            if (userWarnings.indexOf('USER_NOT_VERIFIED') !== -1) {
                message = LC.global.languageSheet.lblGenericWaitForEmailVerification;
            } else if (userWarnings.indexOf('USER_NOT_ACTIVED') !== -1) {
                message = LC.global.languageSheet.lblGenericWaitForActivation;
            }

            var boxMainDiv = $('<div/>', {
                class: 'question ' + modalId + ' ' + modalClass,
                html: '<div class="questionText ' + modalClass + 'Text">' + message + '</div>',
            });
            $('#userVerifyAccountFormContainer #verifyAccountForm').appendTo(boxMainDiv);

            boxMainDiv.box({
                uid: modalId,
                showFooter: false,
                type: 'internal',
                size: 'medium',
                triggerOnClick: false,
            });
        }
    },

    /**
     * Callback
     * @memberOf LC.LoginForm
     */
    callback: function (response) {
        if (!this.trigger('callbackSubsitute')) {
            // Before trigger
            this.trigger('callbackBefore');
            if (response.data.response.success === 1) {
                if (response.data.data.warning.length > 0) {
                    this.showMessage(response.data.data.warning, 'danger');
                } else {
                    LC.resources.pluginListener('onUserLogin', this.el.$form);
                    if (response.data.data.redirect) {
                        window.location.href = response.data.data.redirect;
                    } else {
                        window.location.reload(true);
                    }
                }
            } else {
                if (response.data.response.message == 'A01000-MULTIPLE_USABLE_ACCOUNTS') {
                    $('#userLogin').on('shown.bs.modal', function () {
                        var $switchModal = $('#usedAccountSwitchPopup');
                        if ($switchModal.length && $switchModal.closest('#userLogin').length) {
                            $switchModal.appendTo('body');
                        }
                    });

                    $('.usedAccountSwitchPopupOpen')[0].dispatchEvent(new MouseEvent('click', { bubbles: true }));
                } else {
                    this.showMessage(response.data.response.message, 'danger');
                }
            }
            // Callback trigger
            this.trigger('callback', response);
        }
    },
});

/**
 * @class LC.productSubscribeStockForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.ProductSubscribeStockForm = LC.Form.extend({

    name: 'productSubscribeStockForm',

    options: {},

    initialize: function (form) {
        // Before trigger
        this.trigger('initOptionsBefore');
        this.data = this.el.$form.data('productStock');
        this.initElements();
        // Callback trigger
        this.trigger('initializeCallback');
        $.validate(LC.validateFormConf);

    },

    initElements: function () {
        // Callback trigger
        this.trigger('initOptionsBefore');
        this.el.$submit = this.el.$form.find('[type="submit"]');
        this.el.$email = this.el.$form.find('[name="email"]');
        this.el.$combinationId = this.el.$form.find('[name="combinationId"]');
        // TODO reallow agreement
        // this.el.agreement = F('input[type="checkbox"]', this.el.form);
        // Callback trigger
        this.trigger('initOptionsCallback');
    },

    getFormValues: function () {
        // Before trigger
        this.trigger('getFormValuesBefore');
        var formValues = {};
        formValues.email = this.el.$email.val();
        // TODO reallow agreement
        // formValues.agreement = this.el.agreement.checked;

        formValues.combinationId = this.el.$combinationId.val();
        // Callback trigger
        this.trigger('getFormValuesCallback');
        //dataProductStock = null;
        return formValues;
    },

    getFormData: function () {
        const formDataValues = this.getFormValues();
        return {
            combinationId: formDataValues.combinationId,
            email: formDataValues.email,
            captchaToken: this.inputCaptchaToken.val()
            // TODO reallow agreement
            // agreement: formDataValues.agreement
        };
    },

    submit: function (event) {
        event.preventDefault();
        // Before trigger
        this.trigger('submitBefore');

        if (this.setCaptchaToken(event)) return;

        if (!this.el.$form.isValid()) {
            return false;
        }
        this.el.$submit.prop('disabled', true);
        var formData = this.getFormData();

        $.post(LC.global.routePaths.PRODUCT_INTERNAL_SUBSCRIBE_STOCK, { data: JSON.stringify(formData) }, this.callback.bind(this), 'json');
        // Callback trigger
        this.trigger('submitCallback');
    },

    callback: function (data) {
        this.el.$submit.prop('disabled', false);
        if (!data.data) {
            return;
        }
        this.showMessage(data.data.response.message, data.data.response.success == 1 ? 'success' : 'danger');
        $('#stockAlert').modal('hide');
        // Callback trigger
        this.trigger('callback', data);
    }
});

/**
 * @class LC.UserForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.UserForm = LC.Form.extend({
    /**
     * internal name of form
     * @type {String}
     */
    name: 'userForm',

    /**
     * updated property with selected userType field
     * @type {String|NULL}
     */
    userType: null,

    /**
     * Used for get DOM elements
     * @type {String}
     */
    prefix: 'user',

    /**
     * Initialize form method
     * @memberOf LC.UserForm
     * @return {void}
     */
    initialize: function () {

        // Before trigger
        this.trigger('initializeBefore');

        // Init calendar
        var dateFields = this.el.$form.find('[data-datetimepicker]').each(
            (index, el) => {
                var $calendar = $(el),
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

                $(el).on('dp.change blur', (e) => {
                    var $ctSubmitValue = this.el.$form.find('[name="' + $calendar.data('submit') + '"]');
                    if (e.date) {
                        $ctSubmitValue.val(moment(e.date).format('YYYY-MM-DD'));
                    } else {
                        $ctSubmitValue.val('');
                    }
                });

            }
        );

        // Init Attachments
        this.el.$customTagAttachment = this.el.$form.find('.lcCustomTagAttachment');
        if (this.el.$customTagAttachment.length)
            this.attachmentFields(this.el.$customTagAttachment);

        // Get Elements
        var self = this;
        this.userType = this.el.$form.data('lc-user-type');

        var tabPane = '#' + this.el.$form.data('lc-address') + '_tabPane_' + this.userType;
        this.el.$userTabPanes = this.el.$form.find('.tabPaneUserType');
        this.el.$userFieldElements = this.el.$form.find(tabPane + ' .userFormFields .formField').not('.createAccountField');
        this.el.$shippingFieldElements = this.el.$form.find(tabPane + ' .shippingFormFields .formField');
        var $useShippingAddressCheck = this.el.$form.find(tabPane + ' .useShippingAddressCheck');
        this.el.$useShippingAddress = $useShippingAddressCheck.length ? $useShippingAddressCheck : this.el.$form.find('.useShippingAddressCheck');
        this.el.$createAccountCheck = this.el.$form.find(tabPane + ' .userFieldCreateAccountCheck');
        this.el.$createAccountFieldElements = this.el.$form.find(tabPane + ' .createAccountField');

        // Save original disableds
        this.el.$form.find('.formField').each(function () {
            if (typeof $(this).data('lc-disabled') === 'undefined')
                $(this).data('lc-disabled', $(this).prop('disabled'));
        });

        // Change userType
        this.setUserType();

        this.el.$form.find('.userTypeNavTabs [data-toggle="tab"], .userTypeNavTabs [data-bs-toggle="tab"]').on('shown.bs.tab', function (event) {
            // Update attr and data, for front and back
            self.el.$form.attr('data-lc-user-type', $(event.target).data('lc-user-type'));
            self.el.$form.data('lc-user-type', $(event.target).data('lc-user-type'));

            var $tabPane = $($(event.target).attr('href'));
            self.userType = self.el.$form.data('lc-user-type');
            self.el.$userFieldElements = $tabPane.find('.userFormFields .formField').not('.createAccountField');
            self.el.$shippingFieldElements = $tabPane.find('.shippingFormFields .formField');

            var $useShippingAddressCheck = $tabPane.find('.useShippingAddressCheck');
            self.el.$useShippingAddress = $useShippingAddressCheck.length ? $useShippingAddressCheck : self.el.$form.find('.useShippingAddressCheck');
            self.el.$createAccountCheck = $tabPane.find('.userFieldCreateAccountCheck');
            self.el.$createAccountFieldElements = $tabPane.find('.createAccountField');

            self.setUserType();
            self.toggleCreateAccount(self.el.$createAccountCheck);
            self.toggleShippingAddress(self.el.$useShippingAddress, false);

            // Events useShippingAddress
            self.el.$useShippingAddress.on('change', function (event) {
                self.toggleShippingAddress($(this), true);
            });
            // Create Account events
            self.el.$createAccountCheck.on('change', function (event) {
                self.toggleCreateAccount($(this));
            });
        });

        // Events useShippingAddress
        if (this.el.$useShippingAddress.length) {
            self.toggleShippingAddress(this.el.$useShippingAddress, false);
        }
        this.el.$useShippingAddress.on('change', function (event) {
            self.toggleShippingAddress($(this), true);
        });
        // Create Account events
        if (this.el.$createAccountCheck.length) {
            self.toggleCreateAccount(this.el.$createAccountCheck);
        }
        this.el.$createAccountCheck.on('change', function (event) {
            self.toggleCreateAccount($(this));
        });

        this.subscribedField();
    },

    /**
     * Init attachmentFields
     * @memberOf LC.UserForm
     */
    attachmentFields: function ($elements) {
        $elements.each(
            (index, element) => {
                let $element = $(element);
                $element.find('.new-attachment').click(function (event) {
                    $element.find('input.customTagAttachmentHiddenValue').val('');
                    $element.find('.customTagAttachmentValue').show();
                    $element.find('input[name="customTagAttachmentValue"]').removeAttr('disabled');
                    $element.find('.customTagAttachmentButtons').hide();
                }.bind(this));
                $element.on("change", function (event) {
                    var files = event.target.files,
                        fileNameRegex = new RegExp('^[À-ÿ\\w\\-. ]+$');

                    if (files.length > 0) {
                        var options = $(event.target).closest('.lcCustomTagAttachment').data('options');
                        if (files[0].size > (options.maxSize * 1024 * 1000)) {
                            LC.notify(LC.global.languageSheet.attachFileMaxSize.replace('{{maxSize}}', options.maxSize), { type: 'danger' });
                            $(event.target).val('');
                        } else if (!fileNameRegex.test(files[0].name)) {
                            LC.notify(LC.global.languageSheet.attachFileRegexError, { type: 'danger' });
                            $(event.target).val('');
                        }
                        else {
                            var reader = new FileReader();
                            reader.readAsDataURL(files[0]);
                            reader.onload = function () {
                                var optionValue = {
                                    extension: files[0].name.split('.').pop(),
                                    fileName: files[0].name,
                                    value: reader.result
                                };
                                $(event.target).closest('.lcCustomTagAttachment').find('input.customTagAttachmentHiddenValue').val(JSON.stringify(optionValue));
                            };
                            reader.onerror = function (error) {
                                $(event.target).val('');
                                $(event.target).closest('.lcCustomTagAttachment').find('input.customTagAttachmentHiddenValue').val('');
                                LC.notify(LC.global.languageSheet.attachFileError.replace('{{maxSize}}', options.maxSize), { type: 'danger' });
                            };
                        }
                    }
                });
            }
        );
    },

    /**
     * Change event of subscribed field
     * @memberOf LC.UserForm
     */
    subscribedField: function () {
        var $subscribedField = this.el.$form.find('[name="subscribed"]');
        if ($subscribedField.prop("type") == 'button') {
            $.post(
                LC.global.routePaths.USER_INTERNAL_NEWSLETTER,
                { data: $subscribedField.attr('data-lc') },
                this.subscribeCheckStatusCallback.bind(this, $subscribedField),
                'json'
            );
            $subscribedField.click(function (event) {
                var data = JSON.parse($subscribedField.attr('data-lc'));
                $.post(
                    LC.global.routePaths.USER_INTERNAL_NEWSLETTER,
                    { data: $subscribedField.attr('data-lc') },
                    this.subscribedCallback.bind(this, data, $subscribedField),
                    'json'
                );
            }.bind(this));
        } else {
            $subscribedField.val($subscribedField.prop('checked') ? 1 : 0);
            $subscribedField.change(function (event) {
                $(this).val($(this).prop('checked') ? 1 : 0);
            });
        }
    },

    /**
     * subscribed chech status ajax callback method
     * @memberOf LC.UserForm
     * @param {object} $subscribedField subscribed field
     * @param {object} response ajax object response
     */
    subscribeCheckStatusCallback: function ($subscribedField, response) {
        var data = JSON.parse($subscribedField.attr('data-lc'));
        if (response.data.response.success) {
            if (response.data.data.status == "SUBSCRIBED") {
                data.type = 'UNSUBSCRIBE';
                $subscribedField.html(LC.global.languageSheet.unsubscribe);
            } else {
                data.type = 'SUBSCRIBE';
                $subscribedField.html(LC.global.languageSheet.subscribe);
            }
            $subscribedField.attr("data-lc", JSON.stringify(data));
            $subscribedField.attr('disabled', false);
        }

        // Callback trigger
        this.trigger('subscribeCheckStatusCallback', data, $subscribedField, response);

    },

    /**
     * subscribed subscribe/unsubscribe ajax callback method
     * @memberOf LC.UserForm
     * @param {object} data subscribed action data
     * @param {object} $subscribedField subscribed field
     * @param {object} response ajax object response
     */
    subscribedCallback: function (data, $subscribedField, response) {
        if (response.data.response.success) {
            if (response.data.data.status == "SUBSCRIBED") {
                data.type = 'UNSUBSCRIBE';
                $subscribedField.html(LC.global.languageSheet.unsubscribe);
            } else {
                data.type = 'SUBSCRIBE';
                $subscribedField.html(LC.global.languageSheet.subscribe);
            }
            if (response.data.data.messageType == "SCRIPT") {
                eval(response.data.data.message);
            } else {
                this.showMessage(response.data.response.message, 'success');
            }
            $subscribedField.attr("data-lc", JSON.stringify(data));
            $subscribedField.attr('disabled', false);
        } else {
            this.showMessage(response.data.response.message, 'danger');
        }

        // Callback trigger
        this.trigger('subscribedCallback', data, $subscribedField, response);
    },

    /**
     * Set userType of form and enable / disable other userType fields
     * @memberOf LC.UserForm
     */
    setUserType: function () {
        this.el.$form.find('[name*="userType"]').val(this.userType);

        // Disable all unused inputs
        this.el.$form.find('.userFormFields .formField').not('.createAccountField').not(this.el.$userFieldElements)
            .prop('disabled', true);
        this.el.$form.find('.shippingFormFields .formField').not(this.el.$shippingFieldElements)
            .prop('disabled', true);
        this.el.$form.find('.createAccountField').not(this.el.$createAccountFieldElements)
            .prop('disabled', true);
        this.el.$form.find('.locationField').not('[name^="' + this.userType + '"]').filter('input[type="hidden"]')
            .prop('disabled', true);

        // Enable used userType location hiddens
        this.el.$form.find('.locationField[name^="' + this.userType + '"]').filter('input[type="hidden"]')
            .prop('disabled', false);

        // Enable selected userType inputs
        this.el.$userFieldElements.prop('disabled', function () {
            return $(this).data('lc-disabled');
        });

        // Enable selected userType shipping inputs if check is checked
        if (this.el.$useShippingAddress.length && this.el.$useShippingAddress.prop('checked') === true) {
            this.el.$shippingFieldElements.prop('disabled', function () {
                return $(this).data('lc-disabled');
            });
        } else {
            this.el.$shippingFieldElements.prop('disabled', true);
        }

        // Enable selected userType createAccount inputs if check is checked or not exists
        if ((this.el.$createAccountCheck.length && this.el.$createAccountCheck.prop('checked') === true) || this.el.$createAccountCheck.length == 0) {
            this.el.$createAccountFieldElements.prop('disabled', function () {
                return $(this).data('lc-disabled');
            });
        }

        this.el.$form.find('.addressUserField').each((index, el) => {
            this.initLocations($(el));
        });

        this.el.$form.find('.addressUserCountryField').each((index, el) => {
            this.initCountryField($(el));
        });

        if (!themeConfiguration?.commerce?.allowDifferentCountriesOnBillingAndShippingAddress) {
            let $parent = this.el.$form.find('select[name="' + this.userType + '_user_country"]').closest('.addressUserField');
            checkAllowDifferentCountriesOnBillingAndShippingAddress($parent);
        }

    },

    initLocations: function ($el) {
        var $countrySelect = $el.find('.countryField');
        if (!$countrySelect.length) return;
        var $locationInput = $('input[name="' + $countrySelect.attr('name').replace('country', 'locationList') + '"]');
        if (!$countrySelect.prop('disabled') && !$countrySelect.prop('lc-init')) {
            $countrySelect.prop('lc-init', true);
            if ($locationInput.length && $locationInput.val() > 0) {
                loadLocations.bind($countrySelect)($countrySelect.val(), $locationInput.val());
                $locationInput.remove();
            } else {
                loadLocations.bind($countrySelect)($countrySelect.val() ? $countrySelect.val() : LC.global.session.countryId);
            }
        }
    },

    initCountryField: function ($el) {
        if (!$el.prop('disabled') && !$el.prop('lc-init')) {
            $el.prop('lc-init', true);
            changeCountryFields.bind($el)(LC.global.session.countryId, true);
        }
    },

    /**
     * Show / hide shipping address
     * @memberOf LC.UserForm
     * @param  {object} $useShippingAddress jQuery object, check useShippingAddress
     * @param  {bool} $selectAddressBookAction jQuery object, check useShippingAddress
     */
    toggleShippingAddress: function ($useShippingAddress, $selectAddressBookAction) {
        var $shippingFormFields = $useShippingAddress.closest('.tabPaneUserType').find('.shippingFormFields'),
            $shippingFormFieldsStartSeparator = $('#shippingAddressContainerStartSectionSeparator'),
            $shippingFormFieldsEndSeparator = $('#shippingAddressContainerEndSectionSeparator');

        if (!$shippingFormFields.length)
            $shippingFormFields = $('#shippingAddressContainer');

        if ($useShippingAddress.prop('checked')) {
            if ($selectAddressBookAction) {
                LC.addressBookForm.selectAddressBook(1, 'shipping', 'check_use_shipping');
            }
            $useShippingAddress.val(1);
            $shippingFormFields.removeClass('shippingFormFieldsDisabled');
            $shippingFormFieldsStartSeparator.removeClass('shippingFormFieldsDisabled');
            $shippingFormFieldsEndSeparator.removeClass('shippingFormFieldsDisabled');
            $shippingFormFields.find('.formField').prop('disabled', function () {
                return $(this).data('lc-disabled');
            });
            this.initLocations($shippingFormFields);
            this.initCountryField($shippingFormFields);
        } else {
            if ($selectAddressBookAction) {
                LC.addressBookForm.selectAddressBook(0, 'shipping', 'check_use_shipping');
            }
            $useShippingAddress.val(0);
            $shippingFormFields.addClass('shippingFormFieldsDisabled');
            $shippingFormFieldsStartSeparator.addClass('shippingFormFieldsDisabled');
            $shippingFormFieldsEndSeparator.addClass('shippingFormFieldsDisabled');
            $shippingFormFields.find('.formField').prop('disabled', true);
        }

    },

    /**
     * TODO revision/edit for PHP (addressBook missing part)
     * Disable user form
     * @memberOf LC.UserForm
     * @param {object} ev event type
     */
    disableForm: function () {
        // Disable all elements from form.
        this.el.$form
            .find('input:not([name="editable"], [name="callback"], [name="formContext"])')
            .prop('disabled', 'disabled')
            .addClass('disabled');
        this.el.$form
            .find('select')
            .prop('disabled', 'disabled')
            .addClass('disabled');
        this.el.$form.find('[data-lc-function="editAddressBook"]').remove();
        this.el.$form.find('[data-lc-function="addAddressBook"]').remove();
        // Destroy action buttons from form
        this.el.$form.find('.legalTextLinks').remove();
        this.el.$form.find('.basketButtons').remove();
    },

    /**
     * Show / hide create account fields
     * @memberOf LC.UserForm
     * @param {object} $check jQuery object
     */
    toggleCreateAccount: function ($check) {
        if ($check.length) {
            if ($check.prop('checked')) {
                this.el.$createAccountFieldElements.prop('disabled', function () {
                    return $(this).data('lc-disabled');
                }).closest('.createAccountFieldGroup').removeClass('createAccountFieldGroupHide');
                this.el.$form.find('[name="' + $check.data('lc-target') + '"]').val(1);
            } else {
                this.el.$createAccountFieldElements.prop('disabled', true)
                    .closest('.createAccountFieldGroup').addClass('createAccountFieldGroupHide');
                this.el.$form.find('[name="' + $check.data('lc-target') + '"]').val(0);
            }
        }
    },

    /**
     * TODO revision/edit for PHP
     * setValidationData
     * @param {object} $formField jQuery DOM Element
     * @param {boolean} required  set required field
     */
    setValidationData: function ($formField, required) {
        var dataValidation = $formField.data('validation') || '';
        var dataValidationTypes = $formField.data('validationTypes');
        var arrDataValidationTypes = [];

        if (dataValidationTypes && typeof dataValidationTypes == 'object')
            for (var key in dataValidationTypes)
                arrDataValidationTypes.push(dataValidationTypes[key].toLowerCase());
        else var arrDataValidationTypes = [];

        var arrElements = arrDataValidationTypes.concat(['email', 'phone', 'vat', 'vat_es']);

        var arrValidation = required ? ['required'] : [];
        for (var j = 0; j < arrElements.length; j++) {
            if (dataValidation.toLowerCase().indexOf(arrElements[j]) >= 0) arrValidation.push(arrElements[j]);
        }
        return arrValidation.join(',');
    },

    /**
     * TODO revision/edit for PHP (addressBook missing part)
     * Callback function. Redirects on success or show error message
     * @memberOf LC.UserForm
     * @param  {object} response object from JSON response
     */
    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);

        // Before trigger
        this.trigger('callbackBefore', response);

        var message = LC.global.languageSheet.error,
            success = 0;

        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;
        }

        if (success) {

            if (
                Object.hasOwn(response.data.data.billingValidate, 'valid') && response.data.data.billingValidate.valid === false
                || Object.hasOwn(response.data.data.shippingValidate, 'valid') && response.data.data.shippingValidate.valid === false
            ) {
                let type = '', validationData = null, $addressInput = null, validAddresses = '';
                if (response.data.data.billingValidate.valid === false) {
                    type = 'user';
                    validationData = response.data.data.billingValidate;
                } else {
                    type = 'shipping';
                    validationData = response.data.data.shippingValidate;
                }

                if (type == 'user') {
                    $addressInput = this.el.$form.find('[name="address"]');
                    $addressInput = this.el.$form.find(`input[name="address"], input[name="${this.userType}_${type}_address"]`);
                } else {
                    $addressInput = this.el.$form.find(`input[name="${this.userType}_${type}_address"]`);
                }
                $addressInput.parent().addClass('has-error');

                if (validationData.validAddresses.length) {
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
                this.showMessage(message, 'success');

                if (response.data.data.redirect && response.data.data.redirect.length) {
                    window.location = response.data.data.redirect;
                }

            }
        } else {
            var mainAddressError = false,
                shippingAddressError = false;

            this.el.$form
                .find('input[name="billingAddress"]')
                .parent('div.billingAddress')
                .removeClass('has-error');
            this.el.$form
                .find('input[name="shippingAddress"]')
                .parent('div.shippingAddress')
                .removeClass('has-error');

            if (mainAddressError) {
                this.el.$form
                    .find('input[name="billingAddress"]:checked')
                    .parent('div.billingAddress')
                    .addClass('has-error');
            } else if (shippingAddressError) {
                this.el.$form
                    .find('input[name="shippingAddress"]:checked')
                    .parent('div.shippingAddress')
                    .addClass('has-error');
            }

            this.showMessage(message, 'danger');

        }
        this.el.$form.find('[type="submit"]').attr('disabled', false);

        LC.resources.pluginListener('onUserSignUp', this.el.$form, response);
        // Callback trigger
        this.trigger('callback', response);
    },

    /**
     * TODO revision/edit for PHP
     * Callback function. Redirects on success or show error message
     * @memberOf LC.UserForm
     * @param {object} form
     */
    validateAdditionalShippings: function (form) {
        if (
            form.find('input[name="useShippingAddress"]').attr('value') == 1 &&
            form.find('.shippingAddress.addressBookActive').length == 0
        ) {
            this.showMessage(LC.global.languageSheet.insertOneAddress, 'danger');
            return false;
        }
        return true;
    },

    /**
     * TODO revision/edit for PHP (addressBook missing part)
     * Submit form
     * @memberOf LC.UserForm
     * @param  {Object} event
     */
    submit: function (event) {
        // Checking if this commerce has multiples address book

        if (!this.validateAdditionalShippings(this.el.$form)) return false;
        if (this.el.$form.find('.address-complete:visible').length != this.el.$form.find('.userFieldGroupCountry:visible').length) {
            var $result = this.el.$form.find('.userFieldGroupCountry:visible').not('.address-complete:visible');
            $result.addClass('address-incomplete');
            $([document.documentElement, document.body]).animate({
                scrollTop: $result.offset().top - 200
            }, 1000);
            return false;
        }

        // Get method, continue default event
        if (this.el.form.method.toLowerCase() == 'get') return;

        event.preventDefault();

        if (this.setCaptchaToken(event)) return;

        // Validate form
        if (!this.el.$form.isValid()) return false;

        // Get form data
        var arrDataForm = this.el.$form.serializeArray();

        if (this.el.$form.find('[name="subscribed"]').length && this.el.$form.find('[name="subscribed"]').val() == 0) {
            arrDataForm.push({ name: "subscribed", value: "0" });
        }

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
        if (this.el.form.method.toLowerCase() == 'post')
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

}, LC.userFormResources);

/**
 * @class addressForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.AddressForm = LC.Form.extend({
    /**
     * internal name of form
     * @type {string}
     */
    name: 'addressForm',

    initialize: function () {
        this.initializeForm();
    }

}, LC.addressBookForm);

/**
 * @class LC.BillingAddressBookForm
 * @memberOf LC
 * @extends {LC.Form} and {LC.addressBookForm}
 * @see LC.addressBookForm
 */
LC.BillingAddressBookForm = LC.billingAddressBookForm = LC.Form.extend({
    /**
     * internal name of form
     * @type {string}
     */
    name: 'billingAddressBookForm',

    /**
     * AddressBookForm address type prefix variation
     * @type {string}
     */
    prefix: 'billing',

    initialize: function () {
        this.initializeBook();
    }

}, LC.addressBookForm);

/**
 * @class LC.ShippingAddressBookForm
 * @memberOf LC
 * @extends {LC.Form} and {LC.addressBookForm}
 * @see LC.addressBookForm
 */
LC.ShippingAddressBookForm = LC.shippingAddressBookForm = LC.Form.extend({
    /**
     * internal name of form
     * @type {String}
     */
    name: 'shippingAddressBookForm',

    /**
     * AddressBookForm address type prefix variation
     * @type {String}
     */
    prefix: 'shipping',

    initialize: function () {
        this.initializeBook();
    }

}, LC.addressBookForm);

/**
 * @class LC.ContactForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.ContactForm = LC.Form.extend({
    name: 'contactForm',

    callback: function (data) {
        this.message = '';
        this.success = 0;

        if (!data.data) {
            this.message = data.data.status.message ? data.data.status.message : 'Error';
            this.success = 0;
        } else {
            this.message = data.data.response.message;
            this.success = data.data.response.success ? data.data.response.success : 0;
            if (this.success) {
                this.el.form.reset();
            }
            this.showMessage(this.message, this.success ? 'success' : 'danger');
        }
        this.trigger('callback');
    },

    onComplete: function (data) { },
});

/**
 * @class LC.ChangePasswordForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.ChangePasswordForm = LC.Form.extend({
    name: 'changePasswordForm',
    callback: function (response) {
        this.showMessage(
            response.data.response.message,
            response.data.response.success && response.data.response.success === 1 ? 'success' : 'danger'
        );

        if (response.data.response.success && response.data.response.success === 1)
            setTimeout(function () {
                window.location = LC.global.routePaths.USER;
            }, 3000);
    },
});

/**
 * @class LC.LostPasswordForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.LostPasswordForm = LC.Form.extend({
    name: 'lostPasswordForm',
    callback: function (response) {
        if (!this.trigger('callback', response)) {
            this.showMessage(
                response.data.response.message,
                response.data.response.success && response.data.response.success === 1 ? 'success' : 'danger'
            );
            if (response?.data?.data?.redirect?.length)
                setTimeout(function () {
                    window.location = response.data.data.redirect;
                }, 3000);
        }
    },
});

/**
 * @class LC.DeleteAccountForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.DeleteAccountForm = LC.Form.extend({
    name: 'deleteAccountForm',
    callback: function (response) {
        this.showMessage(
            response.data.response.message,
            response.data.response.success && response.data.response.success === 1 ? 'success' : 'danger'
        );

        if (response.data.response.success && response.data.response.success === 1)
            setTimeout(function () {
                window.location = LC.global.routePaths.USER;
            }, 1000);
    },
});

/**
 * @class LC.SearchForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.SearchForm = LC.Form.extend({

    name: 'searchForm',

    initialize: function () {
    },

    submit: function (event, el) {
        event.preventDefault();

        var action = this.el.$form[0].action;
        var data = {};
        var arrDataForm = this.el.$form.serializeArray();
        var minCharacters = this.el.$form.data('min-characters');

        // Fills dataForm
        for (var i = 0; i < arrDataForm.length; i++) {
            if (arrDataForm[i].name == 'q') {
                if (arrDataForm[i].value.length < minCharacters && !arrDataForm[i].value.match('[^\x00-\x7F]'))
                    return false;

                data.q = arrDataForm[i].value;
            } else {
                data[arrDataForm[i].name] = arrDataForm[i].value;
            }
        }

        // Get the data form values. We have empty values but we want to fill it perhaps that.
        var dataSearch = this.el.$form.data('search');
        data.searchProducts = dataSearch.products;
        data.searchCategories = dataSearch.categories;
        data.searchBlog = dataSearch.blog;
        data.searchPages = dataSearch.pages;
        data.searchNews = dataSearch.news;
        data.minCharacters = minCharacters;

        window.location = action + (!$.isEmptyObject(data) ? '?' + $.param(data) : '');
        return false;
    },
});

/**
 * @class  LC.ProductAddCommentForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.ProductAddCommentForm = LC.Form.extend({

    initialize: function () {
        this.maxRating = 5;
        this.minRating = 1;
        this.rating();
    },

    rating: function () {
        this.el.$rating = this.el.$form.find('input[name="rating"]');
        this.el.$ratingContainer = this.el.$form.find('.lcRating.editable');
        this.el.$ratingText = this.el.$form.find('#valorationValue');
        this.el.icon = this.el.$ratingContainer.find('.starIcon').first().prop('outerHTML');

        this.rating = parseFloat(this.el.$rating.val());
        if (this.rating > this.maxRating) this.rating = this.maxRating;
        if (this.rating < this.minRating) this.rating = this.minRating;

        // Rating text
        this.el.$ratingText.text(this.rating);

        // Add icons
        this.el.$ratingContainer.html('');
        for (var i = 0; i < this.maxRating; i++) {
            this.el.$ratingContainer.append($(this.el.icon).attr({
                class: i < this.maxRating - this.rating ? 'starIcon inactive' : 'starIcon',
                rel: i
            }));
        }

        // Event click
        this.el.$ratingContainer.find('.starIcon').click(
            function (event) {
                // Get Position
                var starPosition = parseInt($(event.currentTarget).attr('rel'));

                // Update view
                this.el.$ratingContainer.find('.starIcon').removeClass('inactive');
                for (var i = 0; i < starPosition; i++) $(this.el.$ratingContainer.find('.starIcon')[i]).addClass('inactive');

                // Update this.rating
                this.rating = Math.abs(starPosition - this.maxRating);
                this.el.$rating.val(this.rating);
                this.el.$ratingText.text(this.rating);
            }.bind(this)
        );
    }
});

/**
 * @class LC.PostAddCommentForm
 * @memberOf LC
 * @extends {LC.Form}
 * @TODO: Suscribe to post comments feature
 * @TODO: Response comments
 */
LC.PostAddCommentForm = LC.Form.extend({

    initialize: function (form) {
        // this.el.$checkbox = this.el.$form.find('input[type=checkbox]');
        // this.el.$email = this.el.$form.find('.blogEmail');

        // if (this.el.$checkbox) {
        //     this.el.$checkbox.click(this.toggleSubscription.bind(this));
        // }

        // $.validate(LC.validateFormConf);
    },

    // toggleSubscription: function (ev) {
    //     if (ev.target.checked) {
    //         this.el.$email.show();
    //         this.el.$email.find('input').data('validation', 'required,email');
    //     } else {
    //         this.el.$email.hide();
    //         this.el.$email.find('input').data('validation', '');
    //     }
    // },

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
            this.el.$form.find('textarea[name=comment]').val('');
        } else {
            LC.notify(message, { type: 'danger' });
        }
    },
});

/**
 * @class LC.CheckoutForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.CheckoutForm = LC.Form.extend({
    name: 'checkoutForm',
    options: {},
    updateBasketRows: {},
    enabledRecaulculateBasket: false,

    initialize: function () {
        // Before trigger
        this.trigger('initializeBefore');

        // Initialize quantity elements
        this.el.quantityElements = this.el.$form.find('input.basketQuantity:text');
        this.el.quantityElements.change(this.onChangeQuantity.bind(this));

        // Initialize delete elements
        this.el.deleteElements = this.el.$form.find('[data-lc-basketdeleterow],[data-lc-basketdeleterows]');
        this.el.deleteElements.click(this.onClickDelete.bind(this));

        // Initialize SaveForLaterRow elements
        this.el.saveForLaterRowElements = this.el.$form.find('[data-lc-basketsaveforlaterrow]');
        this.el.saveForLaterRowElements.click(this.onClickSaveForLater.bind(this));

        // Initialize options elements
        this.initOptions();
        this.initActions();
        this.initVoucher();
        this.initDeleteVoucher();
        this.initRewardPoints();
        this.ticketCodesEmpty();

        this.el.$form.find('#basketRecalculate').prop('disabled', true);

        // Payment and Shipping
        this.el.paymentSystemSelectors = $('.basketSelectorPaymentInput');
        this.el.shippingSection = $('input.shippingTypeSelector:radio, button.savePickingSelectionButton');
        this.el.basketSelectors = this.el.paymentSystemSelectors.add(this.el.shippingSection);
        this.initPayment();
        this.initShipping();

        this.initFapiao();
        this.initCalendar();

        // Init Attachments
        this.el.$customTagAttachment = this.el.$form.find('.lcCustomTagAttachment');
        if (this.el.$customTagAttachment.length)
            this.attachmentFields(this.el.$customTagAttachment);

        // Callback trigger
        this.trigger('initializeCallback');

        // Binding event for send action in post.
        var self = this;
        this.submitButton = this.el.$form.find('button[type="submit"], input[type="submit"]');
        this.submitButton.on('click', self.onSubmit.bind(self));
    },

    /**
     * Init attachmentFields
     * @memberOf LC.CheckoutForm
     */
    attachmentFields: function ($elements) {
        $elements.each(
            (index, element) => {
                let $element = $(element);
                $element.find('.new-attachment').click(function (event) {
                    $element.find('input.customTagAttachmentHiddenValue').val('');
                    $element.find('.customTagAttachmentValue').show();
                    $element.find('input[name="customTagAttachmentValue"]').removeAttr('disabled');
                    $element.find('.customTagAttachmentButtons').hide();
                    $element.parent().find('.form-check-label').show();
                });
                $element.on("change", function (event) {
                    var files = event.target.files,
                        fileNameRegex = new RegExp('^[À-ÿ\\w\\-. ]+$');

                    if (files.length > 0) {
                        var options = $(event.target).closest('.lcCustomTagAttachment').data('options');
                        if (files[0].size > (options.maxSize * 1024 * 1000)) {
                            LC.notify(LC.global.languageSheet.attachFileMaxSize.replace('{{maxSize}}', options.maxSize), { type: 'danger' });
                            $(event.target).val('');
                        } else if (!fileNameRegex.test(files[0].name)) {
                            LC.notify(LC.global.languageSheet.attachFileRegexError, { type: 'danger' });
                            $(event.target).val('');
                        }
                        else {
                            var reader = new FileReader();
                            reader.readAsDataURL(files[0]);
                            reader.onload = function () {
                                var optionValue = {
                                    extension: files[0].name.split('.').pop(),
                                    fileName: files[0].name,
                                    value: reader.result
                                };
                                $(event.target).closest('.lcCustomTagAttachment').find('input.customTagAttachmentHiddenValue').val(JSON.stringify(optionValue));
                            };
                            reader.onerror = function (error) {
                                $(event.target).val('');
                                $(event.target).closest('.lcCustomTagAttachment').find('input.customTagAttachmentHiddenValue').val('');
                                LC.notify(LC.global.languageSheet.attachFileError.replace('{{maxSize}}', options.maxSize), { type: 'danger' });
                            };
                        }
                    }
                });
            }
        );
    },

    /**
     * Init options
     * @memberOf LC.CheckoutForm
     */
    initOptions: function () {
        // Before trigger
        this.trigger('initOptionsBefore');

        this.el.$options = this.el.$form.find(
            'select.productOptionSelectValue, input.productOptionCheckboxValue, input.productOptionBooleanValue, input.productOptionTextValue, textarea.productOptionLongTextValue'
        );
        this.el.$options.change(this.onChangeOption.bind(this));

        // Callback trigger
        this.trigger('initOptionsCallback');
    },

    /**
     * Init button actions
     * @memberOf LC.CheckoutForm
     */
    initActions: function () {
        this.el.$form.find('#basketContinueShopping').click(this.onClickContinueShopping.bind(this));
        this.el.$form.find('#basketClear').click(this.onClickClearBasket.bind(this));
    },

    /**
     * Init voucher code
     * @memberOf LC.CheckoutForm
     */
    initVoucher: function () {
        this.el.voucherButton = $('#voucherButton');
        this.el.voucherField = $('#voucherField');

        if (this.el.voucherButton.length && this.el.voucherField.length) {
            this.el.voucherButton.click(this.onClickVoucherButton.bind(this));
            this.el.voucherField.keypress(this.onKeypressVoucherField.bind(this));
        }
    },

    /**
     * Init Reward Points
     * @memberOf LC.CheckoutForm
     */
    initRewardPoints: function () {
        // Set rewardPoint action
        this.el.$form.find('.rewardPointButton').each(
            function (index, el) {
                el.addEventListener('click', this.redeemRewardPoints.bind(this, el));
                el.disabled = false;
            }.bind(this)
        );
    },

    /**
     * Init payment
     * @memberOf LC.CheckoutForm
     */
    initPayment: function () {
        LC.resources.pluginListener('initializePaymentsBefore', this.el.$form, false);

        this.el.paymentSystemSelectors.each(
            function (index, el) {
                if (!$(el).data('lc-express-checkout')) {
                    el.addEventListener('click', this.setPaymentSystem.bind(this, el.value));
                    el.disabled = false;
                }
            }.bind(this)
        );

        LC.resources.pluginListener('initializePaymentsCallback', this.el.$form, false);
    },

    /**
     * Init shipping
     * @memberOf LC.CheckoutForm
     */
    initShipping: function () {
        this.el.shippingSection.each(
            function (index, el) {
                el.addEventListener('click', this.setShippingSection.bind(this, el));
                el.disabled = false;
            }.bind(this)
        );
    },

    /**
     * Fapiao required field if checkbox is checked
     * @memberOf LC.CheckoutForm
     */
    initFapiao: function () {
        if ($('#inputFapiaoActived').is(':checked')) {
            $('#inputInvoicenameFapiao').attr('data-validation', 'required');
            $('#selectInvoicenameFapiao').attr('data-validation', 'required');
        } else {
            $('#inputInvoicenameFapiao').attr('data-validation', '');
            $('#selectInvoicenameFapiao').attr('data-validation', '');
        }
    },

    /**
     * Initialize calendar. Ex: Used by orderCustomTag
     * @memberOf LC.CheckoutForm
     */
    initCalendar: function () {
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
                    var $inputSubmitValue = this.el.$form.find('[name="' + $calendar.data('submit') + '"]');
                    if (e.date) {
                        $inputSubmitValue.val(moment(e.date).format('YYYY-MM-DD'));
                    } else {
                        $inputSubmitValue.val('');
                    }
                });

            }
        );
    },

    /**
     * Init delete balance codes events
     * @memberOf LC.CheckoutForm
     */
    initDeleteVoucher: function () {
        // Binding click event on each trash icon once a giftCode has been added.
        var deleteVoucherCode = $('span.deleteVoucherCode');
        deleteVoucherCode.each(
            function (index, el) {
                $(el).on('click', this.deleteVoucherCode.bind(this, el));
            }.bind(this)
        );
        // End Binding Click Event
    },

    /**
     * Click continue shopping
     * @memberOf LC.CheckoutForm
     * @param  {object} event
     */
    onClickContinueShopping: function (event) {
        $.post(
            LC.global.routePaths.CHECKOUT_INTERNAL_CONTINUE_SHOPPING,
            this.el.$form.serializeArray(),
            this.continueShoppingCallback,
            'json'
        );
    },

    /**
     * Continue shopping callback
     * @memberOf LC.CheckoutForm
     * @param  {object} response
     */
    continueShoppingCallback: function (response) {
        var data = response.data;
        if (data && data.response && data.response.success == 1) {
            if (data.data && data.data.redirect) {
                window.location = data.data.redirect;
            }
        }
    },

    /**
     * Click clear basket
     * @memberOf LC.CheckoutForm
     * @param  {object} event
     */
    onClickClearBasket: function (event) {
        $.post(LC.global.routePaths.CHECKOUT_INTERNAL_CLEAR_BASKET, this.clearBasketCallback, 'json');
    },

    /**
     * Clear basket callback
     * @memberOf LC.CheckoutForm
     * @param  {object} response
     */
    clearBasketCallback: function (response) {
        var data = response.data;
        if (data && data.response && data.response.success == 1) {
            window.location.reload(true);
        }
    },

    /**
     * Recalculate basket
     * @memberOf LC.CheckoutForm
     * @param  {object} event
     */
    recalculateBasket: function (event) {
        if (this.enabledRecaulculateBasket) {
            var data = this.updateBasketRows;
            this.tmpProducts = globalThis?.lcCommerceSession?.basket?.rows;
            if (this.tmpProducts) {
                LC.dataEvents.changeQuantityEvent(this.tmpProducts, data);
                this.tmpProducts = [];
            }
            $.post(
                LC.global.routePaths.CHECKOUT_INTERNAL_RECALCULATE_BASKET,
                {
                    data: JSON.stringify({ data: data }),
                },
                this.recalculateBasketCallback,
                'json'
            );
        }
    },

    /**
     * Recalculate basket callback
     * @memberOf LC.CheckoutForm
     * @param  {object} response
     */
    recalculateBasketCallback: function (response) {
        var data = response.data;
        if (data && data.response && data.response.success == 1) {
            this.updateBasketRows = {};
            window.location.reload(true);
        }
    },

    /**
      * Delete row callback
     * @memberOf LC.CheckoutForm
     * @param  {object} response
     */
    deleteRowCallback: function (response) {
        var data = response.data;
        if (data && data.response && data.response.success == 1) {
            window.location.reload(true);
        }

        this.trigger('onDeleteRowCallback', response);
    },

    /**
      * Save for later row callback
     * @memberOf LC.CheckoutForm
     * @param  {object} response
     */
    saveForLaterRowCallback: function (response) {
        var data = response.data;
        if (data?.response?.success == 1 && !response.data.data.incidences.length) {
            window.location.reload(true);
        } else {
            this.showMessage(response.data.response.message, 'danger');
        }
    },

    /**
     * Remove title of removeDiscountsSection if no elements
     * @memberOf LC.CheckoutForm
     */
    ticketCodesEmpty: function () {
        var discountsLen = $('.ticketCodesGroup .outputDiscountName').length;
        if (discountsLen == 0) {
            $('.ticketCodesTitle').remove();
        }
    },

    /**
     * Change options
     * @memberOf LC.CheckoutForm
     * @param  {object} event
     */
    onChangeOption: function (event) {
        // Change Option trigger
        if (!this.trigger('onChangeOption')) {
            // Before trigger
            this.trigger('onChangeOptionBefore');

            this.onChangeActions();

            // Callback trigger
            this.trigger('onChangeOptionCallback');
        }
    },

    /**
     * Change quantity
     * @memberOf LC.CheckoutForm
     * @param  {object} eventData
     */
    onChangeQuantity: function (eventData) {
        // Change Option trigger
        if (!this.trigger('onChangeOption')) {
            // Before trigger
            this.trigger('onChangeOptionBefore');

            this.onChangeActions();

            let name = $(eventData.target).attr('name').replace('quantity', '');

            if (name.length == 0) {
                name = 'grid' + $(eventData.target).data('lc-grid-combination-id');
            }

            this.updateBasketRows[name] = {
                type: $(eventData.target).data('lc-row-type'),
                quantity: $(eventData.target).val(),
                options: $(eventData.target).data('lc-row-options'),
                id: $(eventData.target).closest('[data-lc-grid-product-id]').data('lc-grid-product-id'),
            };

            // Callback trigger
            this.trigger('onChangeOptionCallback');
        }
    },

    /**
     * Click delete
     * @memberOf LC.CheckoutForm
     * @param  {object} event
     */
    onClickDelete: function (event) {
        event.preventDefault();
        let hash = $(event.delegateTarget).data('lcBasketdeleterow'),
            hashes = $(event.delegateTarget).data('lcBasketdeleterows'),
            data = '',
            path = '';

        if (hash) {
            data = JSON.stringify({ hash: hash });
            path = LC.global.routePaths.BASKET_INTERNAL_DELETE_ROW;
        } else {
            data = JSON.stringify({ hashes: hashes });
            path = LC.global.routePaths.BASKET_INTERNAL_DELETE_ROWS;
        }

        $.post(
            path,
            { data: data },
            this.deleteRowCallback.bind(this),
            'json'
        );
    },


    /**
     * Click save for later
     * @memberOf LC.CheckoutForm
     * @param  {object} event
     */
    onClickSaveForLater: function (event) {
        event.preventDefault();
        var hash = $(event.delegateTarget).data('lcBasketsaveforlaterrow');
        $.post(
            LC.global.routePaths.BASKET_INTERNAL_SAVE_FOR_LATER_ROW,
            {
                data: JSON.stringify({ hash: hash }),
            },
            (response) => this.saveForLaterRowCallback(response),
            'json'
        );
    },



    /**
     * on change actions
     * @memberOf LC.CheckoutForm
     */
    onChangeActions: function () {
        this.showChangeMessage();
        this.enableRecalculateBasket();
    },

    /**
      * enable recalculate basket
      * @memberOf LC.CheckoutForm
      */
    enableRecalculateBasket: function () {
        $('#basketRecalculate').prop('disabled', false);
        this.enabledRecaulculateBasket = true;
    },

    /**
     * Show change message
     * @memberOf LC.CheckoutForm
     */
    showChangeMessage: function () {
        var $formMessage = $('form[data-lc-form="' + this.name + '"] .form-message');
        $formMessage.addClass('alert alert-info');
        $formMessage.html(LC.global.languageSheet.changeOptionBasket);
    },

    /**
     * Submit event
     * @memberOf LC.CheckoutForm
     * @param  {object} event
     */
    onSubmit: function (event) {
        event.preventDefault();

        if ($(event.target).val() === 'recalculate') {
            this.recalculateBasket(event);
        } else {
            $(event.target).prop('disabled', true);

            this.el.$form.prepend($('<input/>', { type: 'hidden', name: 'action', value: $(event.target).val() }));

            if (this.el.$form.isValid()) {
                // Plugin Listener
                this.el.$form.preventSubmit = false;
                LC.resources.pluginListener('beforeSubmitEndOrder', event, this.el.$form, false);

                if (!this.el.$form.preventSubmit) {
                    this.fillDataForm();
                    $.post(
                        LC.global.routePaths.CHECKOUT_INTERNAL_NEXT_STEP,
                        {
                            data: JSON.stringify($.extend(this.dataForm, { updateBasketRows: this.updateBasketRows }))
                        },
                        (response) => {
                            if (response.data.response.success === 1) {
                                window.location = response.data.data.redirect;
                            } else {
                                this.showMessage(response.data.response.message, 'danger');
                            }
                            $(event.target).prop('disabled', false);
                        },
                        'json'
                    );
                }
            } else {
                $(event.target).prop('disabled', false);
            }
        }
    },

    /**
     * Fills dataForm
     * @memberOf LC.CheckoutForm
     */
    fillDataForm: function () {
        // Get form data
        var arrDataForm = this.el.$form.serializeArray();

        // 
        this.dataForm = {};
        for (var i = 0; i < arrDataForm.length; i++) {
            if (!(arrDataForm[i].name in this.dataForm)) this.dataForm[arrDataForm[i].name] = [];
            this.dataForm[arrDataForm[i].name].push(arrDataForm[i].value);
        }

        for (var i in this.dataForm) this.dataForm[i] = this.dataForm[i].join();
    },

    /**
     * Submit current data, preventing lost data
     * @memberOf LC.CheckoutForm
     */
    submitCurrentData: function () {
        this.fillDataForm();

        $.post(
            LC.global.routePaths.CHECKOUT_INTERNAL_NEXT_STEP,
            {
                data: JSON.stringify($.extend(this.dataForm, { updateBasketRows: this.updateBasketRows }))
            },
            (response) => {
                if (response.data.response.success === 1) {
                    window.location.reload(true);
                } else {
                    this.showMessage(response.data.response.message, 'danger');
                    setTimeout(function () {
                        window.location.reload(true);
                    }, 2000);
                }
            },
            'json'
        );
    },

    /**
     * Click voucher button
     * @memberOf LC.CheckoutForm
     * @param  {object} event
     */
    onClickVoucherButton: function (event) {
        var field = $('#voucherField');
        this.addVoucher(field.val());
    },

    /**
     * Keypress voucher field
     * @memberOf LC.CheckoutForm
     * @param  {object} event
     */
    onKeypressVoucherField: function (event) {
        if (event.keyCode == 13) {
            var field = $('#voucherField');
            this.addVoucher(field.val());
            return false;
        }
    },

    /**
     * Add voucher to basket
     * @memberOf LC.CheckoutForm
     * @param  {string} code
     */
    addVoucher: function (code) {
        if (code.length > 0) {

            // Ajax callback function
            var callbackAddVoucher = function (response) {
                if (response.data.response.success === 1) {
                    this.showMessage(response.data.response.message, 'success');
                    this.submitCurrentData();
                } else {
                    this.showMessage(response.data.response.message, 'danger');
                }
            }.bind(this);

            // Prepare the values to be sent to the controller. The code is sent always but maybe the other params doesn't exist. We can also do jsonToSend.code = escape(code);
            var jsonToSend = { code: encodeURI(code) };
            $.post(
                LC.global.routePaths.BASKET_INTERNAL_ADD_VOUCHER,
                {
                    data: JSON.stringify(jsonToSend),
                },
                callbackAddVoucher,
                'json'
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
                this.showMessage(response.data.response.message, 'success');
                this.submitCurrentData();
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
     * Redeem reward points to basket
     * @memberOf LC.CheckoutForm
     * @param  {object} el
     */
    redeemRewardPoints: function (el) {

        var id = $(el).attr('id').replace('rewardPointButton_', ''),
            value = $('#rewardPointQuantity_' + id).val();

        // Ajax callback function
        var callbackAddVoucher = function (response) {
            if (response.data.response.success === 1) {
                this.showMessage(response.data.response.message, 'success');
                this.submitCurrentData();
            } else {
                this.showMessage(response.data.response.message, 'danger');
            }
        }.bind(this);

        // Prepare the values to be sent to the controller. The code is sent always but maybe the other params doesn't exist. We can also do jsonToSend.code = escape(code);
        var jsonToSend = { id: id, value: value };

        $.post(
            LC.global.routePaths.BASKET_INTERNAL_REDEEM_REWARD_POINTS,
            {
                data: JSON.stringify(jsonToSend),
            },
            callbackAddVoucher,
            'json'
        );

    },

    /**
     * Set payment system
     * @memberOf LC.CheckoutForm
     * @param {string} value
     */
    setPaymentSystem: function (value) {
        this.el.basketSelectors.prop('disabled', true);

        var data = JSON.parse(value);
        LC.resources.pluginListener('setPaymentSystem', {}, value, true);

        var inputsForAdditionalData = {};
        $('.basketSelectorPaymentAdditionalData_' + data.id).each(
            (index, el) => {
                inputsForAdditionalData[el.name] = el.value;
            }
        )
        data['additionalData'] = JSON.stringify(inputsForAdditionalData);

        $.post(
            LC.global.routePaths.BASKET_INTERNAL_SET_PAYMENT_SYSTEM,
            {
                data: JSON.stringify(data),
            },
            (response) => {
                if (response.data.response.success) {
                    this.showMessage(response.data.response.message, 'success');
                    this.submitCurrentData();
                } else {
                    this.showMessage(response.data.response.message, 'danger');
                    this.el.basketSelectors.prop('disabled', false);
                }
            },
            'json'
        );
    },

    /**
     * Set shipping section
     * @memberOf LC.CheckoutForm
     * @param {object} el DOM object
     */
    setShippingSection: function (el) {

        if ($(el).closest('.shippingSelectorSelected').length) {
            return;
        }

        this.el.basketSelectors.prop('disabled', true);

        const $el = $(el);
        let data = {};

        if ($el.hasClass('savePickingSelectionButton')) {
            const lcData = $el.closest('.modal-content').find('input[name="physicalLocation"]:checked').data('lc');
            if (lcData?.hash) {
                data = {
                    type: 'PICKING',
                    deliveryHash: lcData.hash,
                    providerPickupPointHash: lcData.mode == 'PROVIDER_PICKUP_POINT' ? lcData.delivery.mode.providerPickupPoint.hash : ''
                };
                bootstrap.Modal.getOrCreateInstance($el.closest('.modal-content').parent()).hide();
            } else {
                LC.notify(LC.global.languageSheet.deliveryPickingNoSelectedError, { type: 'danger' });
            }
        } else {
            data = {
                type: $el.data('lc-delivery-type'),
                deliveryHash: $el.data('lc-delivery-hash'),
                providerPickupPointHash: $el.data('lc-provider-pickup-point-hash') ? $el.data('lc-provider-pickup-point-hash') : '',
                shipments: [],
            };
            $el.closest('.delivery').find('input.shippingTypeSelector:checked').each((index, shipping) => {
                data.shipments.push({
                    shippingHash: $(shipping).data('lc-shipping-hash'),
                    shipmentHash: $(shipping).data('lc-shipment-hash'),
                });
            });
        }

        LC.resources.pluginListener('setShippingSection', el, { id: el.value }, true);

        $.post(LC.global.routePaths.BASKET_INTERNAL_SET_DELIVERY, {
            data: JSON.stringify(data),
        }, (response) => {
            if (response.data.response.success) {
                this.showMessage(response.data.response.message, 'success');
                this.submitCurrentData();
            } else {
                this.showMessage(response.data.response.message, 'danger');
                this.el.basketSelectors.prop('disabled', false);
            }
        }, 'json');
    },
});

/**
 * @class LC.UnsubscribeStockAlertForm
 * @description Unsubscribe Stock Alert Form
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.UnsubscribeStockAlertForm = LC.Form.extend({
    name: 'unsubscribeStockAlertForm',
    initialize: function (form) {
        // Before trigger
        this.trigger('initializeBefore');

        this.initSubmitButtons();

        // Callback trigger
        this.trigger('initializeCallback');
    },
    initSubmitButtons: function () {
        this.el.$buttons = this.el.$form.find('button');
        this.el.$buttons.click(this.submit.bind(this));
    },
    submit: function (event) {
        event.preventDefault();

        this.id = $(event.target).data('lcStockAlertId');

        $.post(
            LC.global.routePaths.USER_INTERNAL_DELETE_STOCK_ALERT,
            { data: JSON.stringify({ id: this.id }) },
            this.onReceive.bind(this),
            'json'
        );
    },
    onReceive: function (data) {
        if (data.data.response.success == 1) {
            this.el.$form
                .find('div[data-lc-stock-alert-id=' + this.id + ']')
                .fadeOut('slow', function () {
                    var stockAlertForm = $(this).parents('[data-lc-form="unsubscribeStockAlertForm"]');
                    this.remove();
                    $(this).find('.stockAlertsDelete button.unsubscribeStockAlertButton').detach().tooltip('hide');
                    if (!stockAlertForm.find('div.stockAlertsRecord').length) {
                        stockAlertForm.append('<div class="stockAlertsNoSubscriptions">' + LC.global.languageSheet.stockAlertsNoSubscriptions + '</div>');
                    }
                });
        }
        // Callback
        if (this.callback && typeof this.callback === 'function') {
            this.callback(data.data);
        }
    },
});

/**
 * @class LC.ProductsFilter
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.ProductsFilter = LC.Form.extend({

    name: 'productsFilter',

    $priceRangeSlider: null,

    priceSliderData: {},

    categories: [],

    submitted: false,

    initialize: function (form) {
        if (this.el.$form.data('lc-autosubmit')) {
            this.el.$form.on('change', 'input:not(.filterSliderRange), select', (event) => {
                this.checkChangeCustomTagFilter(event);
                this.submit(event);
            });
        } else {
            this.el.$form.on('change', 'input:not(.filterSliderRange), select', (event) => {
                this.checkChangeCustomTagFilter(event);
            });
            this.el.$form.on('submit', this.submit.bind(this));
        }

        this.$priceRangeSlider = this.el.$form.find('input.filterSliderRange[name="priceRange"]');
        this.otherRangeSliders = this.el.$form.find('input.filterSliderRange:not([name="priceRange"])');

        if (this.$priceRangeSlider.length > 0) {
            this.priceSliderData = this.$priceRangeSlider.data();
        } else if ($('.pricesFilterBlock').length > 0) {
            // Has prices and not slider
            this.$categories = this.el.$form.find('.categoriesFilterBlock');

            if (this.$categories.length > 0) {
                this.categories = this.getSelectedCategories();
            }
        }

        if (LC.config.productsFilterCustomTagGroupsExclusion) {
            this.el.$customTagGroupInputs = this.el.$form.find('input[type="checkbox"][id*="CTGFE"]');
            this.el.$customTagGroupInputs.on('change', (event) => {
                if ($(event.currentTarget).prop('checked')) {
                    $(event.currentTarget)
                        .closest('.customTagsGroupFilterBlock')
                        .find('input[type="checkbox"]')
                        .not(event.currentTarget)
                        .prop('checked', false);
                }
            });
        }
    },

    checkChangeCustomTagFilter: function (event) {
        if ($(event.currentTarget).attr('name').startsWith("filterCustomTag_")) {
            this.el.$form.find(`[name="${$(event.currentTarget).attr('name')}"]`).not(event.currentTarget).each((i, e) => {
                if ($(e).val() == $(event.currentTarget).val()) {
                    if ($(e).attr('type') == 'checkbox') {
                        $(e).prop('checked', $(event.currentTarget).prop('checked'));
                    } else if ($(e).attr('type') == 'hidden') {
                        $(e).remove();
                    }
                }
            })
        }
    },

    getSelectedCategories: function () {
        var categories = [],
            $select = this.$categories.find('select');

        if ($select.length > 0) {
            categories = [$select.val()];
        } else {
            this.$categories.find('input:checked').each(function (i, e) {
                categories.push($(e).val());
            });
        }

        return categories.join();
    },

    hasBeenSubmitted: function () {
        return this.submitted;
    },

    submit: function (event) {
        // Before trigger
        this.trigger('submitBefore');

        event.preventDefault();
        this.submitted = true;

        this.otherRangeSliders.each((index, otherRangeSlider) => {
            let $otherRangeSlider = $(otherRangeSlider);
            if ($otherRangeSlider.data('min') == $otherRangeSlider.data('from') && $otherRangeSlider.data('max') == $otherRangeSlider.data('to')) {
                $otherRangeSlider.remove();
            }
        });

        if (this.$priceRangeSlider.length) {
            const values = this.$priceRangeSlider.val().split(';');
            if (parseFloat(values[0]) === this.priceSliderData.min && parseFloat(values[1]) === this.priceSliderData.max) {
                this.$priceRangeSlider.remove();
            }
        } else {
            if (this.categories.length) {
                const filteredCategories = this.getSelectedCategories();

                if (this.categories !== filteredCategories) {
                    this.el.$form.find('.maxPrice, .minPrice, *[name=priceRange]').each(function (i, e) {
                        $(e).attr('name', '');
                    });
                }
            }
        }

        /*
            On twig, url encoding is done to avoid breaking html. (fwk/themes/core/macros/modes/bootstrap5/product/filter/elements/option.html.twig)
            Here it is decoded to avoid double encoding, that breaks filter functionality with for example spaces
        */
        this.el.$form.find('input[name^="filterOption"]').each(function (i, e) {
            $(e).attr('name', decodeURIComponent($(e).attr('name')));
            $(e).attr('value', decodeURIComponent($(e).attr('value')));
        });

        this.el.form.submit();

        // Callback trigger
        this.trigger('submitCallback');
    },
});

/**
 * @class LC.DeletePaymentCardForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.DeletePaymentCardForm = LC.Form.extend({
    name: 'DeletePaymentCardForm',
    callback: function (data) {

        if (!this.trigger('callbackSubsitute', data.data)) {
            // Before trigger
            this.trigger('callbackBefore', data.data);

            // Default action
            if (typeof data.data === 'undefined') return;

            if (!data.data.response) {
                var message = data.data.response.message ? data.data.response.message : 'Error';
                var success = 0;
            } else {
                var message = data.data.response.message;
                var success = data.data.response.success ? data.data.response.success : 0;
            }

            if (success) {
                token = '#paymentCard_' + this.dataForm.id + '_' + $.escapeSelector(this.dataForm.token);
                $(token).remove()
                this.showMessage(message, 'success');
            } else {
                this.showMessage(message, 'danger');
            }

            // Callback trigger
            this.trigger('callback', data.data);
        }
    },
});

/**
* @class LC.ReturnRequestForm
* @description Order Return Form
* @memberOf LC
* @extends {LC.Form}
*/
LC.ReturnRequestForm = LC.Form.extend({
    name: 'orderReturnForm',
    elementId: 'requestFormModal',
    options: {},
    initialized: false,
    initialize: function (form) {
        // Before trigger
        this.trigger('initializeBefore');
        this.returnDeliveryOptionsView();
        this.initOptions();
        this.data = JSON.parse(this.el.$form.attr('data-lc'));
        this.el.form.initialized = true;
        this.returnDeliveryEvents();
        // Callback trigger
        this.trigger('initializeCallback');
    },

    initOptions: function () {
        // Before trigger
        this.trigger('initOptionsBefore');

        this.submitted = false;

        this.el.$submit = this.el.$form.find('#returnSubmitContainer');
        this.el.$tableReturn = this.el.$form.find('table');

        this.el.$quantity = this.el.$form.find('input.returnQuantity');
        this.el.$quantity.change(this.changeInput.bind(this));

        this.el.$checkbox = this.el.$form.find('input[type="checkbox"][name*="returnCheck"]');
        this.el.$checkbox.click(this.clickCheckBox.bind(this));

        this.el.$rmaReasons = this.el.$form.find('select[name*="rmaReasonId"]');
        this.el.$rmaReasons.change(this.changeRmaReason.bind(this));

        this.el.returnPoints = this.el.$form.find('input[name="returnDelivery"]');
        this.el.$map = this.el.$form.find('.physicalLocationsMap');
        this.el.$motive = this.el.$form.find('.returnComment');

        this.el.$physicalLocationSelectors = this.el.$form.find('.physicalLocationSelectors');

        this.enableFormElements(false);

        // Callback trigger
        this.trigger('initOptionsCallback');
    },

    clickCheckBox: function (event) {
        // Before trigger
        this.trigger('clickCheckBoxBefore');
        let $checkbox = $(event.target),
            quantityId = '#' + $(event.target).attr('name').replace('Check', 'Quantity'),
            $rmaReasonId = $('#' + $(event.target).attr('name').replace('returnCheck', 'rmaReasonId')),
            $rmaReasonComment = $('#' + $(event.target).attr('name').replace('returnCheck', 'rmaReasonComment')),
            $quantity = $(quantityId),
            formActive = this.el.$checkbox.filter((index, el) => el.checked).length > 0;

        if ($checkbox.prop('checked')) {
            $quantity.prop('disabled', false).removeClass('disabled');
            $rmaReasonId.change();
            $rmaReasonId.prop('disabled', false).removeClass('disabled');
            $rmaReasonComment.prop('disabled', false).removeClass('disabled');
        } else {
            $quantity.prop('disabled', true).addClass('disabled');
            $rmaReasonId.prop('disabled', true).addClass('disabled');
            $rmaReasonComment.prop('disabled', true).addClass('disabled');
        }

        this.enableFormElements(formActive);

        // Callback trigger
        this.trigger('clickCheckBoxCallback');
    },

    changeRmaReason: function (event) {
        // Before trigger
        this.trigger('changeRmaReasonBefore');
        let $rmaReason = $(event.target),
            $rmaReasonComment = $('#' + $(event.target).attr('name').replace('rmaReasonId', 'rmaReasonComment')),
            $rmaReasonCommentLabel = $('label[for="' + $rmaReasonComment.attr('id') + '"]');

        if ($rmaReason.find('option:selected').data('lc').requiresComment) {
            $rmaReasonCommentLabel.html($rmaReasonCommentLabel.html() + LC.global.settings.requiredFlag);
            $rmaReasonComment.attr('required', true);
            $rmaReasonComment.attr('data-validation', 'required');
        } else {
            $rmaReasonCommentLabel.html($rmaReasonCommentLabel.html().replace(LC.global.settings.requiredFlag, ''));
            $rmaReasonComment.removeAttr('required');
            $rmaReasonComment.removeAttr('data-validation');
        }

        // Callback trigger
        this.trigger('changeRmaReasonCallback');
    },

    enableFormElements: function (formActive) {
        this.el.$physicalLocationSelectors.find('select').prop('disabled', !formActive)[!formActive ? 'addClass' : 'removeClass']('disabled');
        this.el.$motive.prop('disabled', !formActive);
        this.el.returnPoints.prop('disabled', !formActive);
        this.el.$submit.prop('disabled', !formActive);
        this.el.$map[!formActive ? 'addClass' : 'removeClass']('disabled');
        this.el.$form.find('#returnDeliverySelectPL').prop('disabled', !formActive);
    },

    changeInput: function (eventData) {
        // Before trigger
        this.trigger('initOptionsBefore');

        const objInput = $(eventData.target),
            dataInput = objInput.data('lc');

        if (isNaN(objInput.val()) || objInput.val() < 1 || objInput.val() > dataInput.quantity) {
            objInput.val(dataInput.quantity);
        }

        // Callback trigger
        this.trigger('changeInputCallback');
    },

    submit: function (event) {
        // Get method, continue default event
        if (this.el.form.method === 'get') return;
        this.el.$submit.prop('disabled', true);

        event.preventDefault();

        // Validate form
        if (!this.el.$form.isValid()) return false;

        if (this.setCaptchaToken(event)) return;

        // Get form data
        var arrDataForm = this.el.$form.serializeArray();

        // Fills dataForm
        this.dataForm = {};
        for (var i = 0; i < arrDataForm.length; i++) this.dataForm[arrDataForm[i].name] = arrDataForm[i].value;
        this.dataForm['formData'] = this.data;

        // Avoid mutiple submit
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', true);
        this.submitted = true;

        if (this.el.form.method == 'post')
            $.post(this.el.form.action, { data: JSON.stringify(this.dataForm) }, this.onReceive.bind(this), 'json')
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
    },

    hasBeenSubmitted: function () {
        return this.submitted;
    },

    callback: function (data) {
        this.el.$submit.prop('disabled', false);

        if (typeof data === 'undefined') return;

        if (!data.data.response) {
            var message = data.data.response.message ? data.data.response.message : 'Error';
            var success = 0;
        } else {
            var message = data.data.response.message;
            var success = data.status.code ? data.status.code : 0;
        }
        this.showMessage(message, success ? 'success' : 'danger');

        if (success) {
            let $products = this.el.$form.find('input[type=checkbox]:checked').parents('tr');
            $products.each((index, el) => {
                let $element = $(el),
                    $inputQuantity = $element.find('input:not([type="hidden"]).returnQuantity'),
                    $ckeckbox = $element.find('input[type="checkbox"]'),
                    dataInputQuantity = $inputQuantity.data('lc'),
                    quantityValue = parseInt($inputQuantity.val());

                if (quantityValue === dataInputQuantity.quantity) {
                    $element.parents('tbody')
                        .find(`input[type=hidden][value=bundle${$ckeckbox.val()}]`)
                        .parents('tr')
                        .remove();
                    $element.remove();
                } else {
                    $ckeckbox.prop('checked', false);
                    dataInputQuantity.quantity -= quantityValue;
                    $inputQuantity.prop('disabled', true).val(dataInputQuantity.quantity);
                    this.el.$motive.prop('disabled', true).val(-1);
                    $inputQuantity.data('lc-product', dataInputQuantity);
                }
            });

            if (LC.config.orderReturnRequestReload === true) {
                location.reload();
            }
        }
        // Callback trigger
        this.trigger('submitFormCallback', data, this);
    },

    /**
     * TODO: Mirar si esta deprecated, no s'usa enlloc?
     * @param {*} data 
     * @returns 
     */
    addTracingLine: function (data) {
        var rma = data.data.data.data;
        var $tr = $('<tr>', {
            class: 'grid userRmaRequest rmaRequestsGroup rmaRequestsGroup' + rma.id
        });
        var $tdOrderNumber = $('<td>', {
            class: 'grid userRmaRequest userRmaRequestOrderNumber'
        });
        var $divOrderNumber = $('<div>', {
            class: 'clearfix wrp',
            html: rma.documentNumber,
        });
        var $tdDateOrdered = $('<td>', {
            class: 'grid userRmaRequest userRmaRequestDateOrdered',
        });
        var date = new Date(rma.date);
        var $divDateOrdered = $('<div>', {
            class: 'clearfix wrp',
            html: date.toLocaleDateString(), //21/9/21 12:16
        });
        var $tdStatus = $('<td>', {
            class: 'grid userRmaRequest userRmaRequestStatus',
        });
        var status = rma.status.toLowerCase();
        status = status.charAt(0).toUpperCase() + status.slice(1);
        status = 'rmaStatus' + status;
        status = LC.global.languageSheet[status];
        if (rma.substatus.length > 0) {
            status += ' / ' + rma.substatus;
        }
        var $divStatus = $('<div>', {
            class: 'clearfix wrp',
            html: status,
        });
        var $tdActions = $('<td>', {
            class: 'grid userRmaRequest userRmaRequestActions',
        });
        var $divLinks = $('<div>', {
            class: 'order-links',
        });
        var $buttonViewRma = $('<button>', {
            class: BTN_SECONDARY_CLASS + ' userOrderAction rma',
            type: 'button',
            onClick: LC.dataEvents.viewDocument(this),
        });
        rma['pdf'] = false;
        $buttonViewRma.data = rma;
        var $imgViewRma = $('<img>', {
            src: LC.global.settings.commerceCdnPath + 'img/viewOrder.png',
            alt: LC.global.languageSheet.viewRma,
        });
        var $spanViewRma = $('<span>', {
            html: LC.global.languageSheet.viewRma,
        });
        rma['pdf'] = true;
        var $buttonViewRmaPdf = $('<button>', {
            class: BTN_SECONDARY_CLASS + ' userOrderAction rmaPdf',
            type: 'button',
            data: rma,
            onClick: LC.dataEvents.viewDocument(this),
        });
        $buttonViewRmaPdf.data = rma;
        var $imgViewRmaPdf = $('<img>', {
            src: LC.global.settings.commerceCdnPath + 'img/viewOrderPdf.png',
            alt: LC.global.languageSheet.viewRmaPdf,
        });
        var $spanViewRmaPdf = $('<span>', {
            html: LC.global.languageSheet.viewRmaPdf,
        });
        $tr.append(
            $tdOrderNumber.append($divOrderNumber),
            $tdDateOrdered.append($divDateOrdered),
            $tdStatus.append($divStatus),
            $tdActions.append(
                $divLinks.append(
                    $buttonViewRma.append(
                        $imgViewRma,
                        $spanViewRma,
                    ),
                    $buttonViewRmaPdf.append(
                        $imgViewRmaPdf,
                        $spanViewRmaPdf,
                    ),
                ),
            ),
        );
        return $tr;
    },

    returnDeliveryOptionsView: function (data) {
        const $physicalLocations = this.el.$form.find('.physicalLocations'),
            $returnPoints = $('<div/>').addClass('returnPoints'),
            $returnMethodTitle = $('<div/>', {
                class: 'returnMethodTitle',
                text: LC.global.languageSheet.returnMethod,
            });

        $returnPoints.append($returnMethodTitle);

        const $returnDelivery0 = this.el.$form.find('.returnDeliveryOption#returnDelivery0, label[for="returnDelivery0"]');

        if ($returnDelivery0.length) {
            $returnPoints.append($returnDelivery0);
            $returnDelivery0.wrapAll(`<div class="form-check"></div>`);
        }

        if ($physicalLocations.length) {
            const $returnDeliverySelectPL = $(`
                 <div class="form-check">
                     <input type="radio" id="returnDeliverySelectPL" class="returnDeliveryOption form-check-input" value="-1">
                     <label class="form-check-label" for="returnDeliverySelectPL">
                         ${LC.global.languageSheet.deliveryTitlePicking}
                         <span>${LC.global.languageSheet.deliveryPickingNoSelectedError}</span>
                     </label>
                 </div>`);
            $returnPoints.append($returnDeliverySelectPL);
            $returnPoints.append($physicalLocations);
            $physicalLocations.find('[data-lc-event]').dataEvent();

            this.el.$form.find('.defaultOptions').remove();
        } else {
            this.el.$form.find('.returnDeliveryOption').not('#returnDelivery0').each((index, el) => {
                const $input = $(el),
                    $label = $(`label[for="${$(el).attr('id')}"]`);

                $input.add($label).wrapAll(`<div class="form-check"></div>`);
            });
        }

        $returnPoints.appendTo(this.el.$form.find('#returnDeliveryContainer'));
        this.el.$form.find('input#returnDelivery0').prop('checked', true);
    },

    returnDeliveryEvents() {
        this.el.$form.find('#returnDeliverySelectPL').on('change', (event) => {
            if ($(event.target).prop('checked')) {
                this.el.$form.find('.physicalLocations').slideDown(() => {
                    if (this.el.$map.length) {
                        LC.maps.whereCenterTheMap();
                    }
                });
                this.el.$form.find('#returnDelivery0').prop('checked', false);
                $(event.target).removeClass('physicalLocationSelected');
            }
        });
        this.el.$form.find('#returnDelivery0').on('change', (event) => {
            if ($(event.target).prop('checked')) {
                this.el.$form.find('.physicalLocations').slideUp();
                this.el.$form.find('#returnDeliverySelectPL')
                    .removeClass('physicalLocationSelected')
                    .prop('checked', false);
            }
        });
        this.el.$form.find('[name="returnDelivery"]').on('change', (event) => {
            if ($(event.target).prop('checked')) {
                this.el.$form.find('#returnDeliverySelectPL').addClass('physicalLocationSelected');
            }
        });
    },
});

/**
 * @class LC Sales Agent Customers
 * @description Sales Agent Customers Form
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.SalesAgentCustomersForm = LC.Form.extend({
    name: 'salesAgentCustomersForm',
    elementId: 'requestFormModal',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        this.initCalendar();
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    initCalendar: function () {
        this.el.$form.find('[data-datetimepicker]').each(
            (index, el) => {
                var $calendar = $(el),
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
                    var $optionsubmitValue = this.el.$form.find('[name="' + $calendar.data('submit') + '"]');
                    if (e.date) {
                        $optionsubmitValue.val(moment(e.date).format('YYYY-MM-DDTHH:mm:ssZ'));
                    } else {
                        $optionsubmitValue.val('');
                    }
                });

            }
        );
    },
    submit: function (event) {
        event.preventDefault();
        var enableSubmit = true;
        this.el.$form.find('input[type="hidden"]').each((i, input) => {
            var date = moment($(input).val(), 'YYYY-MM-DDTHH:mm:ssZ', true);
            if (!date.isValid()) {
                $(input).closest('div').find('.viewDateValue').val('');
                $(input).val('');
                enableSubmit = false;
            };
        });

        if (enableSubmit) {
            var q = this.el.$form.find('#searchClient').val() || '';
            var fromDate = this.el.$form.find('#fromDate').val() || '';
            var toDate = this.el.$form.find('#toDate').val() || '';
            var includeSubordinates = this.el.$form.find('#includeSubordinates').val() || 0;
            var params = new URLSearchParams();
            if (q) params.append('q', q);
            if (fromDate) params.append('fromDate', fromDate.substring(0, 10));
            if (toDate) params.append('toDate', toDate.substring(0, 10));
            if (includeSubordinates) params.append('includeSubordinates', includeSubordinates);
            var baseUrl = window.location.pathname;
            var newUrl = baseUrl + '?' + params.toString();
            window.location.href = newUrl;
        }
        return enableSubmit;
    },
    hasBeenSubmitted: function () {
        return this.submitted;
    },
    callback: function (data) {
    },
});

/**
 * @class LC Sales Agent Sales
 * @description Sales Agent Sales Form
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.SalesAgentSalesForm = LC.Form.extend({
    name: 'salesAgentSalesForm',
    elementId: 'requestFormModal',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        this.initCalendar();
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    initCalendar: function () {
        this.el.$form.find('[data-datetimepicker]').each(
            (index, el) => {
                var $calendar = $(el),
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
                    var $optionsubmitValue = this.el.$form.find('[name="' + $calendar.data('submit') + '"]');
                    if (e.date) {
                        $optionsubmitValue.val(moment(e.date).format('YYYY-MM-DDTHH:mm:ssZ'));
                    } else {
                        $optionsubmitValue.val('');
                    }
                });
            }
        );
    },
    submit: function (event) {
        var enableSubmit = true;
        this.el.$form.find('input[type="hidden"]').each((i, input) => {
            var date = moment($(input).val(), 'YYYY-MM-DDTHH:mm:ssZ', true);
            if (!date.isValid()) {
                $(input).closest('div').find('.viewDateValue').val('');
                $(input).val('');
                enableSubmit = false;
            };
        });
        return enableSubmit;
    },
    hasBeenSubmitted: function () {
        return this.submitted;
    },
    callback: function (data) {
    },
});

/**
 * @class LC.BundleBuyForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.BuyBundleForm = LC.Form.extend({
    name: 'BuyBundleForm',
    options: {},

    type: 'BUNDLE',

    /**
     * Initialize
     * @memberOf LC.BuyBundleForm
     */
    initialize: function (form) {

        if (this.el.form.initialized) return;

        this.trigger('initializeBefore');
        this.dataBundle = JSON.parse(this.el.$form.attr('data-lc-bundle'));
        this.dataProducts = JSON.parse($(this.el.form).closest('.product-bundle-' + this.dataBundle.bundleId).attr('data-lc-main-products'));
        this.combinationData = this.dataBundle.grouping.combinationData;

        this.idField = this.el.$form.find('input[name="id"]');
        this.bundleProducts = this.el.$form.find('input[name="bundleProduct"]');

        this.el.$quantityField = this.el.$form.find('input[name="quantity"],select[name="quantity"]');
        this.el.$quantityField.change(this.onChangeQuantity.bind(this));

        this.el.$buyFormSubmit = this.el.$form.find('button[type="submit"]');
        this.useUrlOptionsParams = false;
        this.combinationDataOptionChanged = false;

        this.initShoppingList();

        this.$buttonRecommendBundle = this.el.$form.find('#buttonRecommendBundle_' + this.dataBundle.grouping.id);
        this.$buttonRecommendBundle.on("click", (event) => {
            const $modal = $(this.$buttonRecommendBundle.attr('data-bs-target'));
            $modal.find('input[name="id"]').val(this.dataBundle.grouping.id);
            $modal.find('input[name="options"]').val(JSON.stringify(this.getFormData().items));
        });

        this.optionsInitialized = false;
        this.initOptions();
        this.callback = this.callback.bind(this);

        this.initializeFilePlugin();

        this.el.form.initialized = true;

    },

    /**
     * Returns bundle item id from data bundle definition
     * @memberOf LC.BuyBundleForm
     * @param  {int} productId
     */
    getBundleItemId: function (productId) {
        for (var i = 0; i < this.dataBundle.grouping.items.length; i++) {
            if (this.dataBundle.grouping.items[i].productId == productId) {
                return this.dataBundle.grouping.items[i].id;
            }
        }
        return 0;
    },

    /**
     * Returns option name from the given productId and optionId 
     * @memberOf LC.BuyBundleForm
     * @param  {int} productId
     * @param  {int} optionId
     */
    getOptionName: function (productId, optionId) {
        for (var i = 0; i < this.dataProducts[productId].options.length; i++) {
            if (this.dataProducts[productId].options[i].id == optionId) {
                return this.dataProducts[productId].options[i].language.name;
            }
        }
        return '';
    },

    /**
     * on change actions
     * @memberOf LC.BundleBuyForm
     * @param  {bool} doRequest
     */
    onChange: function (doRequest) {
        var combinationData = this.combinationData;

        this.updateButton(combinationData, this.el.$buyFormSubmit, 'notAvailable');

        if (doRequest) {
            $.ajax(
                {
                    type: 'POST',
                    url: LC.global.routePaths.PRODUCT_INTERNAL_GET_BUNDLE_COMBINATION_DATA,
                    data: { data: JSON.stringify(this.getFormData()) },
                    success: (response) => {
                        combinationData = response;
                        if (response.data.response.success) {
                            combinationData = response.data.data;
                        }
                    },
                    async: false,
                    dataType: 'json'
                }
            );
        }

        this.combinationData = combinationData;
        combinationData.items.forEach(combinationDataItem => {
            var $productOptionsContainer = $(this.el.$form.find('div.product-options-' + combinationDataItem.productId));
            if ($productOptionsContainer.length > 0) {
                var productOptionsData = JSON.parse($productOptionsContainer.find('div.productOptions').attr('data-lc-data'));
                this.checkOptionsAvailability(combinationDataItem, $productOptionsContainer, productOptionsData);
                if ((this.optionsInitialized || this.useUrlOptionsParams) && productOptionsData.addOptionsToProductLink) {
                    this.addOptionsToProductLink(productOptionsData, $($productOptionsContainer.closest('.bundle-item-product')));
                }

            }
        });

        this.setPrices(combinationData, 'bundleGrouping');

        this.updateButton(combinationData, this.el.$buyFormSubmit, 'bundleGrouping');

        if (this.shoppingListRowId > 0 && this.el.form.initialized) {
            this.updateShoppingListRow();
        }

    },

    /**
     * getBundleSelectOptionText
     * @memberOf LC.BundleBuyForm
     * @param  {bool} doRequest
     */
    getBundleSelectOptionText: function (combinationData, dataProducts) {
        var bundleSelectOptionText = '';

        combinationData.items.forEach(item => {
            bundleSelectOptionText += this.getSelectOptionText(item, dataProducts[item.productId]);
        });

        return bundleSelectOptionText;
    },

    /**
     * set prices
     * @memberOf LC.BundleBuyForm
     * @param {object} combinationData current data 
     */
    setPrices: function (combinationData, itemType) {
        this.el.$form.find('.bundleGrouping-price, .bundleGrouping-basePrice, .bundleGrouping-saving').each((index, el) => {
            const $property = $(el);

            let price, basePrice, retailPrice,
                lcShowTax = $property.attr('data-lc-show-tax') ?? 'true';

            if (lcShowTax === 'false') {
                basePrice = combinationData.prices.basePrice;
                retailPrice = combinationData.prices.retailPrice;
            } else {
                basePrice = combinationData.pricesWithTaxes.basePrice;
                retailPrice = combinationData.pricesWithTaxes.retailPrice;
            }

            if (basePrice > retailPrice) {
                const saving = basePrice - retailPrice;
                price = retailPrice;
                if ($property.is('.bundleGrouping-basePrice') || $property.is('.bundleGrouping-saving')) {
                    $property.show();
                }
                if ($property.is('.bundleGrouping-saving')) {
                    $property.find('.price').replaceWith(outputHtmlCurrency(saving));
                    $property.find('.percent').html(((saving * 100) / basePrice).toFixed(0));
                }
            } else {
                price = basePrice;
                if ($property.is('.bundleGrouping-basePrice') || $property.is('.bundleGrouping-saving')) {
                    $property.hide();
                }
            }
            if ($property.is('.bundleGrouping-price')) {
                $property.find('.price').replaceWith(outputHtmlCurrency(price));
            } else if ($property.is('.bundleGrouping-basePrice')) {
                $property.find('.price').replaceWith(outputHtmlCurrency(basePrice));
            }
        });
    },

    /**
     * getFormData
     * @memberOf LC.BundleBuyForm
     * @param  {bool} doRequest
     */
    getFormData: function () {

        var options = this.el.$options.not(".productOptionAttachmentHiddenValue").serializeArray(),
            items = {},
            responseItems = [];

        this.bundleProducts.each(function (index, field) {
            items[$(field).val()] = {};
        });

        options.forEach(option => {
            var item = option.name.split("_");
            if (!('options' in items[item[1]])) {
                items[item[1]]['options'] = {};
            }
            if (!(item[2] in items[item[1]]['options'])) {
                items[item[1]]['options'][item[2]] = [];
            }
            items[item[1]]['options'][item[2]].push({ value: option.value });
        });

        if (this.el.$productOptionAttachment.length) {
            this.el.$form.find('input.productOptionAttachmentHiddenValue').each((index, el) => {
                var item = $(el).attr("name").split("_");
                if (!('options' in items[item[1]])) {
                    items[item[1]]['options'] = {};
                }
                if (!(item[2] in items[item[1]]['options'])) {
                    items[item[1]]['options'][item[2]] = [];
                }
                if ($(el).val().length) {
                    items[item[1]]['options'][item[2]].push(JSON.parse($(el).val()));
                }
            });
        }

        for (var key in items) {
            var responseItem = {};
            responseItem['id'] = key;
            responseItem['options'] = [];
            for (var optKey in items[key]['options']) {
                responseItem['options'].push({ id: optKey, values: items[key]['options'][optKey] });
            }
            responseItems.push(responseItem);
        }

        return {
            id: this.idField.val(),
            quantity: this.el.$quantityField ? this.el.$quantityField.val() : 1,
            items: responseItems,
            fromShoppingListRow: this.shoppingListRowId
        };
    },

    /**
     * Return form is valid
     * @memberOf LC.BundleBuyForm
     * @return {boolean}
     */
    formIsValid: function () {
        for (const [key, value] of this.filePluginIsOnError.entries()) {
            if (value !== '') {
                LC.notify(value, { type: 'danger' });
                return false;
            }
        }
        return this.el.$form.isValid();
    },

    submit: function (event) {
        event.preventDefault();
        // Before trigger
        this.trigger('submitBefore', event);

        if (!this.formIsValid()) return false;

        let formData = this.getFormData();
        if ($(event.originalEvent.submitter).data('lc-express-checkout-plugin')) {
            formData.pluginAccountId = $(event.originalEvent.submitter).data('lc-express-checkout-plugin').id;
        }

        // Send form
        $.post(
            LC.global.routePaths.BASKET_INTERNAL_ADD_BUNDLE,
            { data: JSON.stringify(formData) },
            this.callback,
            'json'
        );

        // Disable buy button
        this.el.$buyFormSubmit.prop('disabled', true);

        // Callback trigger
        this.trigger('submitCallback', event);
    },

    callback: function (data) {

        // Express checkout redirect
        if (data.data.data?.expressCheckoutUrl && data.data.data.expressCheckoutUrl.length > 0) {
            window.location.href = data.data.data.expressCheckoutUrl;
            return;
        }

        // Reload miniBasket
        LC.miniBasket.reload();

        // Enable buy button
        this.el.$buyFormSubmit.prop('disabled', false);

    },
}, LC.buyFormResources);

/**
 * @class LC.SendMailForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.SendMailForm = LC.Form.extend({
    name: 'SendMailForm',

    /**
     * Initialize
     * @memberOf LC.SendMailForm
     */
    initialize: function (form) {
        if (this.el.form.initialized) return;
        this.el.form.initialized = true;
        this.el.$attachments = this.el.$form.find('input:file');
        this.el.$attachments.change(this.onChangeAttachment.bind(this));
        this.el.$submit = this.el.$form.find('button[type="submit"]');
        this.el.$type = this.el.$form.find('input[name="type"]');
    },

    /**
     * Change attachment
     * @memberOf LC.SendMailForm
     * @param  {object} eventData
     */
    onChangeAttachment: function (eventData) {
        var $field = $(eventData.target);
        var files = eventData.target.files;
        var $parent = $field.closest('.sendMailFormField');
        var $hiddenInput = $parent.find('.attachmentHiddenValue');
        if (files.length > 0) {
            var attachmentData = $hiddenInput.data('attachemnt');
            if (files[0].size > (attachmentData.maxSize * 1024 * 1000)) {
                LC.notify(LC.global.languageSheet.attachFileMaxSize.replace('{{maxSize}}', attachmentData.maxSize), { type: 'danger' });
                $field.val('');
                $hiddenInput.val('');
            } else {
                var reader = new FileReader();
                reader.readAsDataURL(files[0]);
                reader.onload = function () {
                    var attachmentValue = {
                        fileName: files[0].name,
                        data: reader.result
                    };
                    $hiddenInput.val(JSON.stringify(attachmentValue));
                };
                reader.onerror = function (error) {
                    $field.val('');
                    $hiddenInput.val('');
                    LC.notify(LC.global.languageSheet.attachFileError.replace('{{maxSize}}', attachmentData.maxSize), { type: 'danger' });
                };
            }
        }
    },

    submit: function (event) {
        event.preventDefault();

        if (!this.el.$form.isValid()) return false;

        if (this.setCaptchaToken(event)) return;

        let data = {
            attachments: [],
        };
        $.each(this.el.$form.serializeArray(), (index, obj) => {
            const $el = this.el.$form.find(`[name="${obj.name}"]`);

            if ($el.hasClass("attachmentHiddenValue")) {
                data.attachments.push(obj.value);
            } else if ($el.attr('type') !== 'file') {
                data[obj.name] = obj.value;
            }
        });
        $.post(
            LC.global.routePaths.RESOURCES_INTERNAL_SEND_MAIL,
            { data: JSON.stringify(data) },
            this.callback.bind(this),
            'json'
        );
        this.el.$submit.prop('disabled', true);
    },

    callback: function (data) {
        this.el.$submit.prop('disabled', false);

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
});

/**
 * @class LC.BlogSubscribeForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.BlogSubscribeForm = LC.Form.extend({

    name: 'blogSubscribeForm',

    callback: function (data) {
        if (typeof data === 'undefined') return;

        let message = data?.data?.response?.message ?? 'Error',
            success = data?.data?.response?.success === 1;

        message = message.replace('{{name}}', document.title ?? '');

        this.showMessage(message, success ? 'success' : 'danger');

        if (success) {
            this.el.$form.closest('#subscriptionFormContainer').remove();
        }
    },
});

/**
 * @class LC.NewsletterForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.NewsletterForm = LC.Form.extend({

    name: 'newsletterForm',

    /**
     * Initialize
     * @memberOf LC.NewsletterForm
     */
    initialize: function (form) {
        if (this.el.form.initialized) return;
        this.el.form.initialized = true;
        this.el.$submit = this.el.$form.find('button[type="submit"]');
        this.el.$type = this.el.$form.find('input[name="type"]');
        this.el.$submit.prop('disabled', true);
        if (this.el.$type.val() == 'CHECK_STATUS') {
            $.post(
                LC.global.routePaths.USER_INTERNAL_NEWSLETTER,
                { data: this.el.$type.attr('data-lc') },
                this.subscribeCheckStatusCallback.bind(this, this.el.$type),
                'json'
            );
        } else {
            this.el.$submit.prop('disabled', false);
        }
    },

    /**
     * subscribed chech status ajax callback method
     * @memberOf LC.NewsletterForm
     * @param {object} $type type field
     * @param {object} response ajax object response
     */
    subscribeCheckStatusCallback: function ($type, response) {
        var data = JSON.parse($type.attr('data-lc'));
        if (response.data.response.success) {
            if (response.data.data.status == "SUBSCRIBED") {
                data.type = 'UNSUBSCRIBE';
                this.el.$submit.html(LC.global.languageSheet.unsubscribe);
            } else {
                data.type = 'SUBSCRIBE';
                this.el.$submit.html(LC.global.languageSheet.subscribe);
            }
            $type.attr("data-lc", JSON.stringify(data));
            this.el.$type.val(data.type);
            this.el.$submit.prop('disabled', false);
        }
        // Callback trigger
        this.trigger('subscribeCheckStatusCallback', data, $type, response);
    },

    /**
     * Submit
     * @memberOf LC.NewsletterForm
     * @param {object} event submit event object
     */
    submit: function (event) {
        event.preventDefault();
        if (this.setCaptchaToken(event)) {
            return;
        }
        if (!this.el.$form.isValid()) {
            return false;
        }
        this.el.$submit.prop('disabled', true);
        var arrDataForm = this.el.$form.serializeArray(),
            dataForm = {};
        for (var i = 0; i < arrDataForm.length; i++) {
            dataForm[arrDataForm[i].name] = arrDataForm[i].value;
        }
        $.post(
            LC.global.routePaths.USER_INTERNAL_NEWSLETTER,
            { data: JSON.stringify(dataForm) },
            this.callback.bind(this),
            'json'
        );
        this.el.$submit.prop('disabled', false);
    },

    /**
     * Callback
     * @memberOf LC.NewsletterForm
     * @param {object} response ajax object response
     */
    callback: function (response) {
        if (typeof response === 'undefined') return;
        var data = JSON.parse(this.el.$type.attr('data-lc'));
        if (response.data.response.success) {
            if (response.data.data.status == "SUBSCRIBED") {
                data.type = 'UNSUBSCRIBE';
                this.el.$submit.html(LC.global.languageSheet.unsubscribe);
            } else {
                data.type = 'SUBSCRIBE';
                this.el.$submit.html(LC.global.languageSheet.subscribe);
            }
            if (response.data.data.messageType == "SCRIPT") {
                eval(response.data.data.message);
            } else {
                this.showMessage(response.data.response.message, 'success');
            }
            this.el.$type.attr("data-lc", JSON.stringify(data));
            this.el.$type.val(data.type);
            this.el.$submit.prop('disabled', false);
        } else {
            this.showMessage(response.data.response.message, 'danger');
        }
    },
});

/**
 * @class LC.BuyProductForm
 * @memberOf LC
 * @extends {LC.Form}
 * @description Form extended from LC.Form
 */
LC.BuyProductForm = LC.Form.extend({
    /**
     * internal name of form
     * @type {String}
     */
    name: 'buyProductForm',

    type: 'PRODUCT',

    /**
     * Initialize
     * @memberOf LC.BuyProductForm
     */
    initialize: function (form) {
        if (this.el.form.initialized) return;

        this.el.form.module = this;
        this.data = JSON.parse(this.el.$form.attr('data-product'));
        this.expirationTimeoutLockedStockReserved = null;
        this.combinationData = this.data.combinationData;

        this.el.$gridCombinations = this.el.$form.find('[data-lc-grid-combinations]');
        this.gridData = this.el.$gridCombinations.length ? this.el.$gridCombinations.data('lcGridCombinations') : null;
        this.filledForm = false;

        this.el.$buyFormSubmit = this.el.$form.find('button[type="submit"]');

        // this.quantityField = this.el.$form.find('input[data-lc-quantity="quantity"]').get(0);
        this.el.$quantityField = this.el.$form.find('input[name="quantity"],select[name="quantity"]');
        this.el.$quantityField.change(this.onChangeQuantity.bind(this));

        this.el.$sku = this.el.$form.find('.product-combinations-sku');
        this.el.$ean = this.el.$form.find('.product-combinations-ean');

        // Stocks
        this.el.$productStock = this.el.$form.find('.product-stock');
        this.el.$stockAlert = this.el.$form.find('div.stockAlertButton');
        this.el.$stockAlertButton = this.el.$form.find('button.stockAlertButton');
        if (this.el.$stockAlertButton.length) this.el.$stockAlertButton.click(this.clickStockAlertButton.bind(this));

        this.alternativeImageField = this.el.$form.find('input[data-lc-field="alternativeImage"]').get(0);
        this.linkedSectionId = this.el.$form.find('input[name="sectionId"]').val();
        this.discountSelectableGiftId = this.el.$form.find('input[name="discountSelectableGiftId"]').val();

        this.productOptions = $(this.el.$form.find('div.productOptions'));
        this.useUrlOptionsParams = false;
        this.combinationDataOptionChanged = false;
        this.productOptionsData = {};

        // Price by quantity
        this.el.$priceByQuantityBox = this.el.$form.find('.priceByQuantity');

        if (this.productOptions.length) {
            this.productOptionsData = JSON.parse($(this.productOptions).attr('data-lc-data'));
            this.useUrlOptionsParams = this.productOptionsData.useUrlOptionsParams;
        }

        this.el.$offsetContainer = this.el.$form.find('.productOffsetMessage');

        // Before trigger
        this.trigger('initializeBefore');

        this.initShoppingList();

        this.optionsInitialized = false;
        this.initOptions();
        this.callback = this.callback.bind(this);

        this.initializeFilePlugin();

        // Init trigger
        this.trigger('init');

        // Wishlist init
        this.el.$wishlistDelete = this.el.$form.find('[data-wishlist-delete]');
        if (this.el.$wishlistDelete.length) this.wishlist(this.el.$wishlistDelete, 'delete');
        this.el.$wishlistAdd = this.el.$form.find('[data-wishlist-add]');
        if (this.el.$wishlistAdd.length) this.wishlist(this.el.$wishlistAdd, 'add');
        this.el.$wishlistAccountRequired = this.el.$form.find('[data-wishlist-account_required]');
        if (this.el.$wishlistAccountRequired.length) this.wishlist(this.el.$wishlistAccountRequired, 'account_required');

        // productComparison init
        this.el.$productComparison = this.el.$form.find('[data-product-comparison]');
        if (this.el.$productComparison.length) this.productComparison();

        this.el.form.initialized = true;

        // Callback trigger
        this.trigger('initializeCallback');
    },

    /**
     * Get form values
     * @memberOf LC.BuyProductForm
     * @returns {array}
     */
    getFormValues: function () {
        let formValues = [];

        if (this.el.$options.length) {
            // Product detail (options in form)
            formValues = this.el.$options.not(".productOptionAttachmentHiddenValue").serializeArray();
        } else {
            // List of products (default options selection)
            let selectedOptions = [];

            // Getting first available combination
            if (LC.global.settings.stockManagement && this.data.definition.stockManagement) {
                for (let key in this.data.stocks) {
                    if (this.data.stocks[key] > 0) {
                        let optionValues = key.split('_');
                        if (optionValues.length == 1) break;
                        optionValues = optionValues[1].split('-');

                        for (let i = 0; i < optionValues.length; i++) {
                            for (let option in this.data.options) {
                                if (this.data.options[option].values['id' + optionValues[i]]) {
                                    // Found option + value
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
        }

        if (this.el.$productOptionAttachment.length) {
            const attachedFile = this.el.$form.find('input.productOptionAttachmentHiddenValue').serializeArray();
            for (let i = 0; i < attachedFile.length; i++) formValues.push(attachedFile[i]);
        }
        formValues.productType = this.el.$form.find('input.buyProductFormProductType').val() || 'PRODUCT';
        var email = this.el.$form.find('input.buyFormVoucherPurchaseEmail').val();
        if (email && email.length > 0) {
            formValues.recipients = [{
                email: email,
                message: this.el.$form.find('textarea.buyFormVoucherPurchaseMessage').val(),
            }];
        }
        return formValues;
    },

    /**
     * Update buyFormSubmit
     * @memberOf LC.BuyProductForm
     * @param {object} button
     * @param {object} properties
     */
    updateButton: function (button, properties) {
        button.removeClass('selectOption notAvailable reserve buy');
        button.addClass(properties.className);
        button.prop('disabled', properties.disabled);
        button.data('buyFormSubmitName', properties.name);

        if (button.data('show-label') == true) button.html(properties.name);
        else button.html('');
    },

    /**
     * On change
     * @memberOf LC.BuyProductForm
     * @param  {bool} doRequest
     */
    onChange: function (doRequest) {
        var formData = this.getFormData();

        // Before trigger
        this.trigger('onChangeBefore', formData);

        clearTimeout(this.expirationTimeoutLockedStockReserved);
        this.el.$form.find('.stock-lock').remove();

        this.updateButton(this.combinationData, this.el.$buyFormSubmit, 'notAvailable');

        if (this.gridData) {
            this.el.$form.find('[data-lc-grid-combination]').addClass('disabled');
            // this.el.$form.find('.grid-combinations-info').show();
            this.el.$form.find('.combination-stock').removeClass('no-stock');
            this.el.$form.find('.combination-stock').removeClass('stock-ok');
            this.el.$form.find('.combination-stock').addClass('uncompleted');
        }

        if (doRequest) {
            $.ajax(
                {
                    type: 'POST',
                    url: LC.global.routePaths.PRODUCT_INTERNAL_GET_PRODUCT_COMBINATION_DATA,
                    data: { data: JSON.stringify(formData) },
                    success: (response) => {
                        this.combinationData = response;
                        if (response.data.response.success) {
                            this.combinationData = response.data.data;
                        }
                    },
                    async: false,
                    dataType: 'json'
                }
            );
        }

        if (this.el.$options.length) {
            this.checkOptionsAvailability(this.combinationData, this.productOptions, this.productOptionsData);
            if ((this.optionsInitialized || this.useUrlOptionsParams) && this.productOptionsData.addOptionsToProductLink) {
                this.addOptionsToProductLink(this.productOptionsData, $($productOptionsContainer.closest('.bundle-item-product')));
            }
        }

        if (!this.gridData || this.gridData.prices.showGridPrice == 'showGridPriceAnyOption') {
            this.setPrices(this.combinationData, 'product');
        }

        if (this.gridData) {
            this.updateGrid();
            this.updateCombinationData();
        }

        this.updateButton(this.combinationData, this.el.$buyFormSubmit, 'product');

        if (this.combinationData.productCodes.sku.length > 0) this.el.$sku.html(this.combinationData.productCodes.sku);
        if (this.combinationData.productCodes.ean.length > 0) this.el.$ean.html(this.combinationData.productCodes.ean);

        // Stock Alerts
        if (this.combinationData.status != 'SELECT_OPTION' && this.el.$stockAlertButton.length) {
            this.el.$stockAlertButton.attr('combinationId', this.combinationData.stock.combinationId);
            if (this.combinationData.showStockAlert) {
                this.el.$stockAlertButton
                    .add(this.el.$stockAlert)
                    .addClass('product-stock-alert-active')
                    .removeClass('product-stock-alert-hidden');
            } else {
                this.el.$stockAlertButton
                    .add(this.el.$stockAlert)
                    .addClass('product-stock-alert-hidden')
                    .removeClass('product-stock-alert-active');
            }
        }

        // Stock Info
        if (this.combinationData.status != 'SELECT_OPTION' && this.combinationData.stock.units >= 0) {
            this.el.$productStock.removeClass('not-init').find('.stock').html(this.combinationData.stock.units);
            if (this.combinationData.stock.units > 0) {
                this.el.$productStock.removeClass('no-stock').addClass('stock-ok');
                this.el.$form.find('.product-stock .stock').html(this.combinationData.stock.units);
                if (this.combinationData.stock.units === 1) {
                    this.el.$form.find('.stockText').html(LC.global.languageSheet.stockSingular.replace('{{stock}}', this.combinationData.stock.units));
                } else {
                    this.el.$form.find('.stockText').html(LC.global.languageSheet.stockPlural.replace('{{stock}}', this.combinationData.stock.units));
                }
            } else {
                this.el.$productStock.removeClass('stock-ok').addClass('no-stock');
                this.el.$form.find('.stockText').html(LC.global.languageSheet.stockNone.replace('{{stock}}', this.combinationData.stock.units));
            }
        } else {
            this.el.$productStock.addClass('not-init');
        }

        // Availability
        let availabilityInterval = null;
        if (this.combinationData.status != 'SELECT_OPTION' && this.data.definition.availability) {
            for (let i = this.data.definition.availability.intervals.length - 1; i > -1; i--) {
                const interval = this.data.definition.availability.intervals[i];
                if (this.combinationData.stock.units <= interval.stock) {
                    availabilityInterval = interval;
                }
            }
        }

        if (availabilityInterval) {
            this.el.$form.find('.product-stock').removeClass('not-init');
            var name = availabilityInterval.language.name.replace('{{stock}}', this.combinationData.stock.units);
            if (this.data.definition.onRequest) {
                name = name.replace('{{onRequestDays}}', this.data.definition.onRequestDays);
            }
            this.el.$form.find('.product-stock .availabilityImage').html(`<img src="${availabilityInterval.language.image}" class="availabilityImage" onerror="$(this).remove();">`);
            this.el.$form.find('.product-stock .availabilityName').html(name);
            this.el.$form.find('.product-stock .availabilityImage').show();
            this.el.$form.find('.product-stock .availabilityName').show();
        } else {
            this.el.$form.find('.product-stock .availabilityImage').hide();
            this.el.$form.find('.product-stock .availabilityName').hide();
        }

        if (this.el.$offsetContainer.length) {
            this.el.$offsetContainer.html('');
            if (this.combinationData.status === "AVAILABLE" || this.combinationData.status === "RESERVE") {
                var offsetDays = this.combinationData.stock.offsetDays,
                    quantity = this.getQuantityValue(this.data.definition);
                if (quantity <= this.combinationData.stock.units && this.combinationData.stock.previsionDate != null) {
                    var previsionDate = new Date(this.combinationData.stock.previsionDate);
                    previsionDate.setDate(previsionDate.getDate() + offsetDays);
                    var formattedDate = moment(previsionDate).format(CALENDAR_PLUGIN_DATE_FORMAT);
                    this.el.$offsetContainer.html(
                        LC.global.languageSheet.warehouseOffsetMessage
                            .replace('{{offsetDays}}', formattedDate)
                            .replace('{{previsionDate}}', formattedDate)
                    );
                } else if (quantity > this.combinationData.stock.units && this.data.definition.onRequest) {
                    this.el.$offsetContainer.html(LC.global.languageSheet.onRequestProductMessage.replace('{{days}}', this.data.definition.onRequestDays + offsetDays));
                }
            }
        }

        if (this.shoppingListRowId > 0 && this.el.form.initialized) {
            this.updateShoppingListRow();
        }

        $('#' + this.type.toLowerCase() + 'RecommendOptions_' + this.data.id).val(JSON.stringify(this.getFormData().options));

        this.enabledButtons = this.el.$buyFormSubmit.filter(':enabled');

        this.updatePriceByQuantity(this.combinationData.options);

        // Callback trigger
        this.trigger('onChangeCallback', {
            combinationData: this.combinationData,
            gridData: this.gridData,
            quantity: this.getQuantityValue(this.data.definition)
        });
    },

    /**
     * Update grid
     * @memberOf LC.buyFormResources
     */
    updateGrid: function () {
        this.updateGridData();

        if (this.gridData.combinations.status == 'AVAILABLE') {
            this.el.$form.find('[data-lc-grid-combination]').each((i, el) => {
                var data = $(el).data('lc-grid-combination'),
                    currentCombinationValues = [...data.values];
                $.each(this.optionsSummary.selected.values, (j, value) => {
                    if (this.optionsSummary.combinableValues.includes(value)) currentCombinationValues.push(value);
                });
                currentCombinationValues = currentCombinationValues.sort().join('-');
                for (const combinationId in this.gridData.combinations.values) {
                    const combination = this.gridData.combinations.values[combinationId];
                    if (currentCombinationValues == combination.optionValueIds.sort().join('-')) {
                        data.stock = combination.stock;
                        data.combinationId = combinationId;
                        $(el).find('.combination-quantity input,.combination-quantity select').val(combination.quantity);
                    }
                }
                const quantity = parseInt($(el).find('.combination-quantity input,.combination-quantity select').val());

                // Availability
                let availabilityInterval = null;
                $(el).find('.combination-stock').removeClass('uncompleted');
                $(el).find('.combination-stock').addClass(data.stock > 0 ? 'stock-ok' : 'no-stock');
                for (let i = this.data.definition.availability?.intervals.length - 1; i > -1; i--) {
                    const interval = this.data.definition.availability.intervals[i];
                    if (data.stock <= interval.stock) {
                        availabilityInterval = interval;
                    }
                }
                if (availabilityInterval) {
                    $(el).find('.combination-stock').removeClass('not-init');
                    var name = availabilityInterval.language.name.replace('{{stock}}', data.stock);
                    if (this.data.definition.onRequest) {
                        name = name.replace('{{onRequestDays}}', this.data.definition.onRequestDays);
                    }
                    $(el).find('.combination-stock .availabilityImage').html(`<img src="${availabilityInterval.language.image}" class="availabilityImage" onerror="$(this).remove();">`);
                    $(el).find('.combination-stock .availabilityName').html(name);
                    $(el).find('.combination-stock .availabilityImage').show();
                    $(el).find('.combination-stock .availabilityName').show();
                } else {
                    $(el).find('.combination-stock .availabilityImage').hide();
                    $(el).find('.combination-stock .availabilityName').hide();
                }

                if (data.stock > 0 || this.gridData.purchasableWithoutStock) {
                    $(el).removeClass('disabled');
                }
            });

            // Prices 
            this.el.$form.find('[data-lc-grid-price]').each((i, el) => {
                const data = $(el).data('lc-grid-price'),
                    currentCombinationValues = [...data.values].concat(this.optionsSummary.selected.values).sort();
                let price = this.gridData.prices.product.price,
                    quantity = 1;

                if (this.gridData.prices.showGridPrice == 'showGridPriceBothOptions') {
                    quantity = parseInt($(el).parent().find('.combination-quantity input').val());
                }

                if (quantity > 1) {
                    var priceByQuantityRange = 0;
                    $.each(this.gridData.prices.product.pricesByQuantity, (i, priceByQuantity) => {
                        if (quantity >= parseInt(i)) {
                            priceByQuantityRange = priceByQuantity;
                        }
                    });
                    if (priceByQuantityRange > 0) price = priceByQuantityRange;
                }
                $.each(currentCombinationValues, (i, value) => {
                    if (this.gridData.prices.values[value]) {
                        let valuePrice = this.gridData.prices.values[value].price;
                        if (quantity > 1) {
                            var priceByQuantityRange = 0;
                            $.each(this.gridData.prices.values[value].pricesByQuantity, (i, priceByQuantity) => {
                                if (quantity >= parseInt(i)) {
                                    priceByQuantityRange = priceByQuantity;
                                }
                            });
                            if (priceByQuantityRange > 0) valuePrice = priceByQuantityRange;
                        }
                        price += valuePrice;
                    }
                });
                $(el).find('.combination-price .price').replaceWith(outputHtmlCurrency(price));
            });

        }

    },

    /**
     * Update grid
     * @memberOf LC.buyFormResources
     */
    updateGridData: function () {

        if (!this.optionsSummary) {
            this.optionsSummary = {};
            this.optionsSummary.valuesOption = {};
            this.optionsSummary.combinableOptions = {};
            this.optionsSummary.combinableValues = [];
            this.optionsSummary.required = [];
            this.optionsSummary.showAsGrid = [];
            $.each(this.data.options, (i, option) => {
                if (option.required) {
                    this.optionsSummary.required.push(option.id);
                }
                if (option.showAsGrid) {
                    this.optionsSummary.showAsGrid.push(option.id);
                }

                if (option.combinable) this.optionsSummary.combinableOptions[option.id] = [];
                $.each(option.values, (j, value) => {
                    if (option.combinable) {
                        this.optionsSummary.combinableOptions[option.id].push(value);
                        this.optionsSummary.combinableValues.push(value.id);
                    }
                    this.optionsSummary.valuesOption[value.id] = option.id;
                });
            });
        }

        this.optionsSummary.selected = [];
        this.optionsSummary.selected.options = [];
        this.optionsSummary.selected.values = [];
        $.each(this.getFormData().options, (i, option) => {
            this.optionsSummary.selected.options.push(parseInt(option.id));
            $.each(option.values, (j, value) => {
                if (value.value) this.optionsSummary.selected.values.push(parseInt(value.value));
            });
        });

        this.checkGridStatus();
    },

    /**
     * Get grid status
     * @memberOf LC.BuyProductForm
     */
    checkGridStatus: function () {
        this.gridData.combinations.status = (this.gridData.combinations.totalStock == 0 && !this.gridData.purchasableWithoutStock) ? 'UNAVAILABLE' : 'AVAILABLE';
        $.each(this.optionsSummary.required, (i, option) => {
            if (!this.optionsSummary.selected.options.includes(option) && !this.optionsSummary.showAsGrid.includes(option)) {
                this.gridData.combinations.status = 'SELECT_OPTION';
            }
        });
    },

    /**
     * Get grid status
     * @memberOf LC.BuyProductForm
     * @returns {object}
     */
    updateCombinationData: function () {
        $.each(this.combinationData.options, (i, option) => {
            option.missed = !this.optionsSummary.selected.options.includes(option.id) && !this.optionsSummary.showAsGrid.includes(option.id);
        });
        this.combinationData.status = this.gridData.combinations.status;
    },

    /**
     * Returns min quantity buy without quantity field
     * @memberOf LC.BuyProductForm 
     * @param {object} def - product definition data object
     * @returns {number}
     */
    getQuantityValue: function (def) {
        let minQuantity = 1;
        if (this.el.$quantityField.val()) {
            minQuantity = this.el.$quantityField.val();
        } else {
            if (def.minOrderQuantity && !def.groupQuantityByOptions) {
                minQuantity = def.minOrderQuantity;
            }
            if (def.multipleOrderQuantity > 0) {
                if (def.multipleActsOver > 0) {
                    if (minQuantity >= def.multipleActsOver && minQuantity % def.multipleOrderQuantity !== 0) {
                        let difference = minQuantity % def.multipleOrderQuantity;
                        minQuantity = minQuantity + (def.multipleOrderQuantity - difference);
                    }
                } else {
                    if (minQuantity < def.multipleOrderQuantity) {
                        minQuantity = def.multipleOrderQuantity;
                    } else {
                        if (minQuantity % def.multipleOrderQuantity !== 0) {
                            let difference = minQuantity % def.multipleOrderQuantity;
                            minQuantity = minQuantity + (def.multipleOrderQuantity - difference);
                        }
                    }
                }
            }
        }
        return parseInt(minQuantity);
    },

    /**
     * Get form data
     * @memberOf LC.BuyProductForm
     * @returns {object}
     */
    getFormData: function () {
        const formValues = this.getFormValues();

        let options = {},
            optionsArray = [],
            value = '',
            gridOptions = [],
            attachmentOptions = [];

        $.each(this.data.options, (i, option) => {
            if (option.type == "ATTACHMENT") {
                attachmentOptions.push(option.id);
            }
        });

        for (let i = 0; i < formValues.length; i++) {
            if (!options[formValues[i].name]) {
                options[formValues[i].name] = [];
            }
            value = formValues[i].value;

            let isAttachmentOption = attachmentOptions.includes(parseInt(formValues[i].name.replace('optionValue', '')));
            try {
                value = JSON.parse(formValues[i].value);
            } catch (error) { }

            if ($.type(value) === 'object') {
                options[formValues[i].name].push(value);
            } else if (!isAttachmentOption || (isAttachmentOption && formValues[i].value.length > 0)) {
                options[formValues[i].name].push({ value: formValues[i].value });
            }
        }

        for (let option in options) {
            let $optionField = this.el.$form.find('#' + option);
            optionsArray.push({ id: option.replace('optionValue', ''), values: options[option] });
        }

        if (this.gridData) {
            $.each(this.gridData.combinations.values, (i, el) => {
                gridOptions.push({
                    id: i,
                    values: el.optionValueIds,
                    quantity: el.quantity
                });
            });
        }

        const response = {
            id: this.data.id,
            quantity: this.getQuantityValue(this.data.definition),
            options: optionsArray,
            gridOptions: gridOptions,
            alternativeImage: this.alternativeImageField ? this.alternativeImageField.value : '',
            sectionId: this.linkedSectionId,
            parentHash: this.el.$form.find('input[name="parentHash"]')?.val() || '',
            discountSelectableGiftId: this.discountSelectableGiftId,
            mode: this.el.$form.find('#mode').length ? this.el.$form.find('#mode').val() : '',
            fromShoppingListRow: this.shoppingListRowId,
            combinationId: this.combinationData.stock.combinationId,
            stock: this.combinationData.stock.units,
            productType: formValues.productType || 'PRODUCT',
            recipients: formValues.recipients || [],
        };
        return response;
    },

    /**
     * Return form is valid
     * @memberOf LC.BuyProductForm
     * @return {boolean}
     */
    formIsValid: function () {
        for (const [key, value] of this.filePluginIsOnError.entries()) {
            if (value !== '') {
                LC.notify(value, { type: 'danger' });
                return false;
            }
        }
        return this.el.$form.isValid();
    },

    /**
     * Submit event
     * @memberOf LC.BuyProductForm
     * @param {object} event
     */
    submit: function (event) {
        event.preventDefault();

        // Before trigger
        this.trigger('submitBefore', event);

        if (!this.formIsValid()) return false;

        const data = this.getFormData();

        let sendData = data;

        let retailPrice = this.el.$form.find('.product-price .price .integerPrice').attr("content");
        let price = this.el.$form.find('.product-basePrice .price .integerPrice').attr("content");
        if (retailPrice != undefined && price != undefined) {
            sendData.prices = {
                basePrice: parseFloat(price),
                retailPrice: parseFloat(retailPrice)
            }
        }

        // Before submit
        LC.resources.pluginListener('onAddProduct', event, sendData);

        let submitUrl = LC.global.routePaths.BASKET_INTERNAL_ADD_PRODUCT;

        if (this.gridData) {
            submitUrl = LC.global.routePaths.BASKET_INTERNAL_ADD_PRODUCTS;
            sendData.mode = 'UPDATE';
            sendData.type = 'GRID';
            sendData.products = [];
            sendData.products = [];

            for (let idx in sendData.options)
                if (Object.hasOwn(this.optionsSummary.combinableOptions, sendData.options[idx].id))
                    delete sendData.options[idx];

            $.each(this.gridData.combinations.values, (i, combination) => {
                var product = {};
                product.id = sendData.id;
                product.options = [];
                $.each(combination.optionValueIds, (j, optionValueId) => {
                    product.options.push({
                        id: `${this.optionsSummary.valuesOption[optionValueId]}`,
                        values: [
                            { 'value': `${optionValueId}` }
                        ]
                    });
                });
                product.options = product.options.concat(sendData.options);
                product.quantity = combination.quantity;
                sendData.products.push(product);
            });

            delete sendData.gridOptions;
            delete sendData.alternativeImage;
            delete sendData.fromShoppingListRow;
            delete sendData.quantity;
            delete sendData.sectionId;

        } else if (this.linkedSectionId > 0) {
            submitUrl = LC.global.routePaths.BASKET_INTERNAL_ADD_LINKED;
        } else if (this.discountSelectableGiftId > 0) {
            submitUrl = LC.global.routePaths.BASKET_INTERNAL_ADD_GIFT;
            sendData.productId = data.id;
        }

        if ($(event.originalEvent?.submitter).data('lc-express-checkout-plugin')) {
            sendData.pluginAccountId = $(event.originalEvent.submitter).data('lc-express-checkout-plugin').id;
        }

        // Send form
        $.post(
            submitUrl,
            { data: JSON.stringify(sendData) },
            this.callback,
            'json'
        );

        // Disable buy button
        this.el.$buyFormSubmit.prop('disabled', true);

        // Callback trigger
        this.trigger('submitCallback', event);
    },

    /**
     * Callback
     * @memberOf LC.BuyProductForm
     * @param {object} data
     */
    callback: function (data) {
        this.trigger('callbackBefore', data.data);

        var page = $('body').data('lc-page');

        if (data.data.response.success) {

            if (data.data.data.code == "lockedStock") {
                this.el.$buyFormSubmit.prop('disabled', true);
                let expires = data.data.data.expireTime,
                    tipContent =
                        `<div class="stock-lock" id="stockLockTip_${this.data.id}" data-lc-basket-expires='${expires}'>
                         <span>${data.data.data.response}</span>
                     </div>`,
                    remainingSeconds = moment(expires)
                        .diff(moment(), 'seconds');

                clearTimeout(this.expirationTimeoutLockedStockReserved);
                this.expirationTimeoutLockedStockReserved = setTimeout(() => { this.onChange(); }, remainingSeconds * 1000);
                this.el.$buyFormSubmit.first().html(LC.global.languageSheet.lockedStockReserved);
                this.el.$buyFormSubmit.first().before(tipContent);

                let options = {
                    container: this.el.$form.find('.stock-lock'),
                    endDate: expires
                };
                new LC.countdown(options);

                return;
            }

            if (data.data.data.expressCheckoutUrl && data.data.data.expressCheckoutUrl.length > 0) {
                window.location.href = data.data.data.expressCheckoutUrl;
                return;
            }

            if (
                (this.linkedSectionId > 0 || this.discountSelectableGiftId > 0) &&
                (page === 'checkoutBasket' || page === 'checkoutPaymentAndShipping')
            ) {
                // Refresk Basket
                $('html, body').animate({ scrollTop: 0 }, 'slow', function () {
                    window.location.reload(true);
                });
            } else {
                // Reload miniBasket
                LC.miniBasket.reload();
                LC.dataEvents.reloadCustomize();
            }
        } else {
            LC.notify(data.data.response.message, { type: 'danger' });
        }

        // Enable buy button
        this.enabledButtons.prop('disabled', false);


        // Callback trigger
        this.trigger('callback', data.data);

        if (LC.config.showModalBasket && (LC.global.settings.isMobile === true || window.innerWidth < MEDIA_MOBILE)) {
            var modalContent = '';

            if (data.data.stockLock) {
                modalContent +=
                    `<div class="basketCountdown" data-lc-basket-expires='{"expires": "${data.data.stockLock.expires}"}'>
                         <div class="active">${LC.global.languageSheet.lockedStockRemainingTimePopup}</div>
                         <div class="expired">${LC.global.languageSheet.lockedStockExpiredTimePopUp}</div>
                     </div>`;
            }
            modalContent +=
                `<div id="modalBasketButtons">
                     <a href="${LC.global.routePaths.CHECKOUT_BASKET}" class="modalBasketEndOrder ${BTN_PRIMARY_CLASS}">${LC.global.languageSheet.basketEndOrder}</a>
                     <a data-dismiss="modal" data-bs-dismiss="modal" class="modalBasketContinueShopping ${BTN_SECONDARY_CLASS}">${LC.global.languageSheet.basketContinueShopping}</a>
                     <a href="${LC.global.routePaths.USER}" class="modalBasketMyAccount ${BTN_SECONDARY_CLASS}">${LC.global.languageSheet.myAccount}</a>
                 </div>`;

            this.el.$buyFormSubmit.box({
                uid: 'mobileBasketModal',
                source: modalContent,
                showFooter: false,
                triggerOnClick: false,
                type: 'html',
                size: 'small',
            });
        }
    },

    /**
     * set prices
     * @memberOf LC.BuyProductForm
     * @param {object} combinationData current data 
     */
    setPrices: function (combinationData) {
        this.el.$form.find('.product-price, .product-basePrice, .product-saving').each((index, el) => {
            const $property = $(el);

            let price, basePrice, retailPrice, prices = null,
                lcShowTax = $property.attr('data-lc-show-tax') ?? 'true';

            if (lcShowTax === 'false') {
                prices = combinationData.prices.prices;
            } else {
                prices = combinationData.pricesWithTaxes.prices;
            }

            if (combinationData.prices.pricesByQuantity.length > 0) {
                let quantity = this.getQuantityValue(this.data.definition),
                    pricesByQuantity;
                if (lcShowTax === 'false') {
                    pricesByQuantity = combinationData.prices.pricesByQuantity;
                } else {
                    pricesByQuantity = combinationData.pricesWithTaxes.pricesByQuantity;
                }
                pricesByQuantity.forEach(priceByQuantity => {
                    if (quantity >= priceByQuantity.quantity) {
                        prices = priceByQuantity.prices;
                    }
                });
            }

            basePrice = prices.basePrice;
            retailPrice = prices.retailPrice;

            if (this.data.definition.offer && basePrice > retailPrice) {
                const saving = basePrice - retailPrice;
                price = retailPrice;

                if ($property.is('.product-basePrice') || $property.is('.product-saving')) {
                    $property.show();
                }
                if ($property.is('.product-saving')) {
                    $property.find('.price').replaceWith(outputHtmlCurrency(saving));
                    $property.find('.percent').html(((saving * 100) / basePrice).toFixed(0));
                }
            } else {
                price = basePrice;
                if ($property.is('.product-basePrice') || $property.is('.product-saving')) {
                    $property.hide();
                }
            }
            if ($property.is('.product-price')) {
                $property.find('.price').replaceWith(outputHtmlCurrency(price));
            } else if ($property.is('.product-basePrice')) {
                $property.find('.price').replaceWith(outputHtmlCurrency(basePrice));
            }
        });
    },

    /**
     * Update priceByQuantity macro output
     * @memberOf LC.BuyProductForm
     * @param {object} selectedOptions 
     */
    updatePriceByQuantity: function (selectedOptions) {
        if (!this.el.$priceByQuantityBox.length) return;

        const selectedIds = [],
            tableClass = this.el.$priceByQuantityBox.find('table').attr('class'),
            data = this.el.$priceByQuantityBox.data('lc-data'),
            incrementRanges = [];
        let tbody = '';

        selectedOptions.forEach(option => option.values.forEach(value => {
            if (value.selected) {
                selectedIds.push(value.id);
                for (var key in data) {
                    if (key === 'optionValueId' + value.id) incrementRanges.push(data[key]);
                }
            }
        }));

        this.el.$priceByQuantityBox.html('');

        let ranges = {};
        data.base.forEach(range => {
            if (typeof ranges[range.from] === 'undefined')
                ranges[range.from] = [];
            ranges[range.from].push({ ...range, ...{ increment: false } });
        });
        incrementRanges.forEach(rangeGroup => {
            rangeGroup.forEach(range => {
                if (typeof ranges[range.from] === 'undefined')
                    ranges[range.from] = [];
                ranges[range.from].push({ ...range, ...{ increment: true } });
            });
        });

        let froms = Object.keys(ranges),
            rangesResult = {};
        froms.forEach(key => rangesResult[key] = []);
        let nRangesPerRow = selectedOptions.length + 1;

        for (let i = 0; i < froms.length; i++) {
            const from = froms[i];
            // Group with all range slices
            if (ranges[from].length === nRangesPerRow) {
                rangesResult[from] = ranges[from].reduce((acc, obj) => {
                    acc.basePrice = (acc.basePrice || 0) + obj.basePrice;
                    acc.retailPrice = (acc.retailPrice || 0) + obj.retailPrice;
                    acc.from = obj.from;
                    acc.message = undefined;
                    return acc;
                }, {});
            }
            // Group with missing range slices
            if (ranges[from].length < nRangesPerRow) {
                const thisRanges = ranges[from];
                let missingItems = nRangesPerRow - ranges[from].length,
                    beforeIndex = i - 1,
                    toReduceArray = thisRanges;

                while (missingItems > 0 && beforeIndex >= 0) {
                    const beforeRanges = ranges[froms[beforeIndex]];
                    const validBeforeRanges = beforeRanges.filter(beforeRange => beforeRange.optionValueId !== thisRanges[0].optionValueId);
                    toReduceArray = [...toReduceArray, ...validBeforeRanges];
                    beforeIndex--;
                    missingItems -= validBeforeRanges.length;
                }

                rangesResult[from] = toReduceArray.reduce((acc, obj) => {
                    acc.basePrice = (acc.basePrice || 0) + obj.basePrice;
                    acc.retailPrice = (acc.retailPrice || 0) + obj.retailPrice;
                    acc.from = parseInt(from);
                    acc.message = undefined;
                    return acc;
                }, {});
            }
        }

        // Set final messages and HTML
        const rangesResultArr = Object.values(rangesResult);
        for (let i = 0; i < rangesResultArr.length; i++) {
            const range = rangesResultArr[i];

            if (i === rangesResultArr.length - 1) {
                range.message = LC.global.languageSheet.equalOrGreaterNUnits.replace('{{n}}', range.from);
            } else if (i === 0 && rangesResultArr[i + 1].from === 2) {
                range.message = LC.global.languageSheet.oneUnit.replace('{{n}}', range.from);
            } else if (i === 0) {
                range.message = LC.global.languageSheet.equalOrGreaterNUnits.replace('{{n}}', 1);
            } else if (range.from + 1 === rangesResultArr[i + 1].from) {
                range.message = LC.global.languageSheet.nUnits.replace('{{n}}', range.from);
            } else {
                range.message = LC.global.languageSheet.equalOrGreaterNUnits.replace('{{n}}', range.from);
            }

            let price = rangesResultArr[i].basePrice, basePriceData = '';
            if (
                this.data.definition.offer &&
                rangesResultArr[i].basePrice > rangesResultArr[i].retailPrice &&
                rangesResultArr[i].retailPrice != 0
            ) {
                price = rangesResultArr[i].retailPrice;
                basePriceData = `data-base-price="${rangesResultArr[i].basePrice}"`;
            }
            tbody += `<tr><td class="messageColumn">${range.message}</td><td class="priceColumn" ${basePriceData}>${outputHtmlCurrency(price)}</td></tr>`;
        }

        this.el.$priceByQuantityBox.html(`<table class="${tableClass}"><tbody>${tbody}</tbody></table>`);
    },

    /**
         * Fill form with sended data
         * @memberOf LC.BuyProductForm
         * @param {object} data
         */
    fillForm: function (data) {
        this.trigger('fillDataBefore', data);
        let totalQuantity = 0;
        for (var combinationId in data.combinations) {
            this.gridData.combinations.values[combinationId].quantity = data.combinations[combinationId].quantity;
            totalQuantity += data.combinations[combinationId].quantity;
            this.el.$form.find(`#gridCombination${combinationId}`).find('input[type="text"][name="quantity"]').attr('value', data.combinations[combinationId].quantity);
        }
        this.setGridTotalQuantity(totalQuantity);
        this.gridData.hasNoCombinableOptions = data.noCombinableOptions.length ? true : false;

        data.noCombinableOptions.forEach(noCombinableOption => {
            let optionInput = this.el.$form.find(`[name="optionValue${noCombinableOption.id}"]`);
            optionInput.each((index, el) => {
                if ($(el).prop('checked')) {
                    $(el).click();
                }
            });
            if (['MULTIPLE_SELECTION', 'MULTIPLE_SELECTION_IMAGE'].includes(noCombinableOption.type)) {
                noCombinableOption.valueList.forEach(
                    (value) => {
                        optionInput.each((index, el) => {
                            if (($(el).val() == value.id)) {
                                $(el).click();
                            }
                        });
                    }
                );
            } else if (['BOOLEAN', 'SINGLE_SELECTION', 'SINGLE_SELECTION_IMAGE', 'SELECTOR'].includes(noCombinableOption.type)) {
                optionInput.each((index, el) => {
                    if (($(el).val() == 'true' && noCombinableOption.value) || ($(el).val() == 'false' && !noCombinableOption.value)) {
                        $(el).click();
                    }
                });
            } else if (noCombinableOption.type == 'SHORT_TEXT' || noCombinableOption.type == 'LONG_TEXT') {
                optionInput.val(noCombinableOption.value);
            } else if (noCombinableOption.type == 'DATE') {
                optionInput.val(noCombinableOption.value);
                this.el.$form.find(`[id="viewDateValue${noCombinableOption.id}"]`)
                    .val(
                        moment(noCombinableOption.value).format(optionInput.parent().find('[data-format]').attr('data-format'))
                    );
            } else if (noCombinableOption.type == 'ATTACHMENT') {
                // Disable attachment input
                this.el.$form.find(`[id="productOptionAttachmentValue${noCombinableOption.id}"]`).hide();
                this.el.$form.find(`[id="optionValueInputFile${noCombinableOption.id}"]`).prop('disabled', true);
                // show attachment buttons
                this.el.$form.find(`[id="productOptionAttachmentButtons${noCombinableOption.id}"]`).show();

                // fill attachment values
                this.el.$form.find(`[id^="viewOptionAttachment${noCombinableOption.id}_"]`).hide();
                noCombinableOption.values.forEach(
                    (value, index) => {
                        this.el.$form.find(`[id="optionValue${noCombinableOption.id}_${index + 1}"]`).val(value);
                        let $viewOptionAttachment = this.el.$form.find(`[id="viewOptionAttachment${noCombinableOption.id}_${index + 1}"]`);
                        $viewOptionAttachment.attr('data-lc-attachment-path', value);
                        $viewOptionAttachment.html(value.split('_', 2)[1]);
                        $viewOptionAttachment.show();
                        LC.global.languageSheet.attachFileOpen.replace('{{fileName}}', value);
                    }
                );
            }
        });

        if (totalQuantity > 0) {
            this.filledForm = true;
        }

        this.onChange(false);

        // Callback trigger
        this.trigger('callback', data);
    },


}, LC.buyFormResources);


/**
 * @class LC.FilterShoppingListRows
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.FilterShoppingListRows = LC.Form.extend({

    name: 'filterShoppingListRows',

    submitted: false,

    initialize: function (form) {
        if (this.el.$form.data('lc-autosubmit')) {
            this.el.$form.on('change', 'select', (event) => {
                this.submit(event);
            });
        } else {
            this.el.$form.on('submit', this.submit.bind(this));
        }
    },

    hasBeenSubmitted: function () {
        return this.submitted;
    },

    submit: function (event) {
        // Before trigger
        this.trigger('submitBefore');

        event.preventDefault();
        this.submitted = true;

        this.el.form.submit();

        // Callback trigger
        this.trigger('submitCallback');
    },
});

/**
 * @class LC.CountriesLinksForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.CountriesLinksForm = LC.Form.extend({

    name: 'countriesLinksForm',

    initialize: function (form) {
        this.$country = this.el.$form.find('#country');
        this.$country.prop('disabled', false);
        this.$language = this.el.$form.find('#language');
        this.$languageOptions = this.$language.find('option');
        this.$languageOptions.hide();
        this.submitButton = this.el.$form.find('button[type="submit"], input[type="submit"]');

        if (this.$country.val() != 'default') {
            this.$language.prop('disabled', false);
            if (this.$language.val() != 'default') {
                this.submitButton.prop('disabled', false);
            }
        } else {
            this.submitButton.prop('disabled', true);
        }

        this.$country.on('change', (event) => {
            this.$languageOptions.hide();
            this.submitButton.prop('disabled', true);
            if ($(event.target).val() == 'default') {
                this.$language.prop('disabled', true);
            } else {
                this.$languageOptions.each((index, el) => {
                    if ($(el).val().startsWith($(event.target).val() + '-') || $(el).val() == 'default') {
                        $(el).show();
                    }
                });
                this.$language.prop('disabled', false);
            }
            this.$language.val('default');
        });

        this.$language.on('change', (event) => {
            this.submitButton.prop('disabled', false);
            if ($(event.target).val() == 'default') {
                this.submitButton.prop('disabled', true);
            }
        });

    },

    submit: function (event) {
        // Before trigger
        this.trigger('submitBefore', this);

        if (this.el.$form.data('lc-accept-route-warning')) {
            LC.dataEvents.acceptRouteWarning();
        }

        event.preventDefault();
        this.data = this.el.$form.find(`[value='${this.$language.val()}']`).data('lc');
        if (this.data.needCallSetCountry) {
            this.el.$form.find('input[name="countryCode"]').val(this.$country.val());
            this.superForm('submit', event);
        } else {
            window.location = this.data.url;
        }
    },

    onReceive: function (data) {
        // Before trigger
        this.trigger('onReceiveBefore', data);

        if (data.data.response.success == 1) {
            window.location = this.data.url;
        }
        this.superForm('onReceive', data);

        // After trigger
        this.trigger('onReceiveAfter', data);
    }

});

/**
 * @class LC.UsedAccountSwitchForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.UsedAccountSwitchForm = LC.Form.extend({

    name: 'usedAccountSwitchForm',

    initialize: function (form) {
        this.$accountType = this.el.$form.find('#accountType');
        this.$account = this.el.$form.find('#account');
        this.$accountOptions = this.$account.find('option');
        this.submitButton = this.el.$form.find('button[type="submit"], input[type="submit"]');

        if (this.$accountType.val() != 'default') {
            this.$account.prop('disabled', false);
            if (this.$account.val() != 'default') {
                this.submitButton.prop('disabled', false);
            } else {
                this.submitButton.prop('disabled', true);
            }
        } else {
            this.submitButton.prop('disabled', true);
        }

        this.$accountType.on('change', (event) => {
            this.$accountOptions.hide();
            this.submitButton.prop('disabled', true);
            if ($(event.target).val() == 'default') {
                this.$account.prop('disabled', true);
            } else {
                this.$accountOptions.each((index, el) => {
                    var data = "";
                    if ($(el).attr('data-lc') != "") {
                        data = JSON.parse($(el).attr('data-lc'));
                    }
                    if ((data != "" && data.type.startsWith($(event.target).val())) || $(el).val() == 'default') {
                        $(el).show();
                    }
                });
                this.$account.prop('disabled', false);
            }
            this.$account.val('default');
        });

        this.$account.on('change', (event) => {
            this.submitButton.prop('disabled', false);

            if ($(event.target).val() == 'default') {
                this.submitButton.prop('disabled', true);
            }
        });
    },

    submit: function (event) {
        // Before trigger
        this.trigger('submitBefore', this);

        if (this.el.$form.data('lc-accept-route-warning')) {
            LC.dataEvents.acceptRouteWarning();
        }

        event.preventDefault();
        $.post(
            LC.global.routePaths.ACCOUNT_INTERNAL_USED_ACCOUNT + '?accountId=' + this.$account.val(),
            {},
            function (data) {
                if (data.data.response.success === 1) {
                    if (data.data.data.redirect) {
                        window.location.href = data.data.data.redirect;
                    } if (window.location.href == LC.global.routePaths.ACCOUNT_INTERNAL_USED_ACCOUNT_SWITCH) {
                        window.location.href = LC.global.routePaths.HOME;
                    } else {
                        window.location.reload(true);
                    }
                }
            },
            'json'
        );
    },

    onReceive: function (data) {
        // Before trigger
        this.trigger('onReceiveBefore', data);

        if (data.data.response.success == 1) {
            window.location = this.data.url;
        }
        this.superForm('onReceive', data);

        // After trigger
        this.trigger('onReceiveAfter', data);
    }

});

/**
 * @class LC.OrdersForm
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.OrdersForm = LC.Form.extend({
    name: 'ordersForm',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        initCalendar(this.el.$form);
        this.initMultiselect();
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    initMultiselect: function () {
        this.el.$form.find('.multiselect-dropdown').on('click', function (e) {
            e.stopPropagation();
            var $this = $(this);
            var $dropdown = $this.find('.multiselect-dropdown-list-wrapper');
            $('.multiselect-dropdown-list-wrapper').not($dropdown).slideUp(150);
            $dropdown.stop(true, true).slideToggle(150);
        });

        this.el.$form.find('.multiselect-dropdown-list-wrapper').on('click', function (e) {
            e.stopPropagation();
        });

        $(window).on('click', function (e) {
            if (!$(e.target).closest('.multiselect-dropdown').length) {
                $('.multiselect-dropdown-list-wrapper').slideUp(150);
            }
        });

        this.el.$form.find('.multiselect-dropdown-list input[type="checkbox"]').on('change', function () {
            var $dropdown = $(this).closest('.multiselect-dropdown');
            var $list = $dropdown.find('.multiselect-dropdown-list');
            var $selected = $list.find('input[type="checkbox"]:checked');

            var selectedValues = [];
            $selected.each(function () {
                var value = $(this).val().trim();
                selectedValues.push(value);
            });

            $('#multiSelectValue').val(selectedValues.join(','));

            var $label = $dropdown.find('.multiselect-label .selected');
            $label.text('(' + selectedValues.length + ')');
        });
    },
    submit: function (event) {
        event.preventDefault();
        var enableSubmit = true;
        this.el.$form.find('input[type="hidden"].DateValue').each((i, input) => {
            if ($(input).val() != '') {
                var date = moment($(input).val(), 'YYYY-MM-DDTHH:mm:ssZ', true);
                if (!date.isValid()) {
                    $(input).closest('div').find('.viewDateValue').val('');
                    $(input).val('');
                    enableSubmit = false;
                }
            }
        });

        if (enableSubmit) {
            var addedFrom = this.el.$form.find('#addedFrom').val() || '';
            var addedTo = this.el.$form.find('#addedTo').val() || '';
            var onlyCreatedByMe = this.el.$form.find('#onlyCreatedByMe').val() || '';
            var statuses = this.el.$form.find('#statusIdList > #multiSelectValue').val() || '';
            var includeSubCompanyStructure = this.el.$form.find('#includeSubCompanyStructure').val() || '';

            var oldParams = new URLSearchParams(window.location.search);
            var sort = oldParams.get("sort");

            var params = new URLSearchParams();
            if (sort) params.set('sort', sort);
            if (addedFrom) params.set('addedFrom', addedFrom.substring(0, 10)); else params.delete('addedFrom');
            if (addedTo) params.set('addedTo', addedTo.substring(0, 10)); else params.delete('addedTo');
            if (onlyCreatedByMe) params.set('onlyCreatedByMe', onlyCreatedByMe);
            if (statuses) params.set('statusIdList', statuses); else params.delete('statusIdList');
            if (includeSubCompanyStructure) params.set('includeSubCompanyStructure', includeSubCompanyStructure);

            var baseUrl = window.location.pathname;
            var newUrl = baseUrl + '?' + params.toString();
            window.location.href = newUrl;
        }
        return enableSubmit;
    }
});
/**
 * @class LC Registered Users
 * @description Registered Users Form
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.AccountRegisteredUsersForm = LC.Form.extend({
    name: 'accountRegisteredUsersForm',
    elementId: 'requestFormModal',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        initCalendar(this.el.$form);
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    submit: function (event) {
        event.preventDefault();
        var enableSubmit = true;
        this.el.$form.find('input[type="hidden"].DateValue').each((i, input) => {
            if ($(input).val() != '') {
                var date = moment($(input).val(), 'YYYY-MM-DDTHH:mm:ssZ', true);
                if (!date.isValid()) {
                    $(input).closest('div').find('.viewDateValue').val('');
                    $(input).val('');
                    enableSubmit = false;
                }
            }
        });

        var jsonResult = {};
        this.el.$form.serializeArray().forEach(item => {
            jsonResult[item.name] = item.value;
        });

        if (enableSubmit) {
            var oldParams = new URLSearchParams(window.location.search);
            var sort = oldParams.get("sort");
            var params = new URLSearchParams();
            if (sort) params.set('sort', sort);
            Object.entries(jsonResult).forEach(([key, value]) => {
                if (value && value !== '-') {
                    if (key === 'addedFrom' || key === 'addedTo') {
                        params.set(key, value.substring(0, 10));
                    } else {
                        params.set(key, value);
                    }
                } else {
                    params.delete(key);
                }
            });


            var baseUrl = window.location.pathname;
            var newUrl = baseUrl + '?' + params.toString();
            window.location.href = newUrl;
        }
        return enableSubmit;
    },
    hasBeenSubmitted: function () {
        return this.submitted;
    },
    callback: function (data) {
    },
});
/**
 * @class LC Registered User Create
 * @description Create Registered User Form
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.RegisteredUserCreateForm = LC.Form.extend({
    name: 'registeredUserCreateForm',
    elementId: 'requestFormModal',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        initCalendar(this.el.$form);
        this.setImageUpload();
        this.initTabSwitcher();
        this.searchClientExtern();
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    setImageUpload: function () {
        const $form = this.el.$form,
            $fileInput = $form.find('input[name="registeredUserImageUpload"]'),
            $textInput = $form.find('input[name="image"]');
        $fileInput.off('change.lcImageUpload')
            .on('change.lcImageUpload', function () {
                const fileName = this.files.length ? this.files[0].name : '';
                $textInput.val(fileName);
            });
        $textInput
            .attr('readonly', true)
            .css('cursor', 'pointer')
            .off('click.lcOpenFile')
            .on('click.lcOpenFile', () => $fileInput.trigger('click'));
    },
    initTabSwitcher: function () {
        var $form = this.el.$form;
        var $panes = $form.find('.tab-pane');
        $panes.not('.active').each((i, pane) => $(pane).find(':input').prop('disabled', true));
        $form.find('a[data-bs-toggle="tab"]').on('shown.bs.tab', (e) => {
            var $newPane = $($(e.target).attr('href'));
            var $oldPane = $($(e.relatedTarget).attr('href'));
            $oldPane.find(':input').prop('disabled', true);
            $newPane.find(':input').prop('disabled', false);
        });
    },
    searchClientExtern: function () {
        const $input = this.el.$form.find('#searchClient');
        if (!$input.length) return;

        $input.off('keyup', this.boundSearchHandler);

        this.boundSearchHandler = this.searchHandler.bind(this);

        this.boundSearchHandler({ target: $input[0] });

        $input.on('keyup', this.boundSearchHandler);
    },
    searchHandler: function (e) {
        const input = e.target;
        const value = input.value.trim();

        this.searchClientIDValidation(input, value, false);
    },
    searchClientIDValidation: function (element, value, execute) {
        const route = LC.global.routePaths.ACCOUNT_INTERNAL_GET_REGISTERED_USER_EXISTS;
        const input = element;
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
        if (execute) {
            $.ajax({
                url: route,
                method: 'GET',
                data: { q: value },
                success: (data) => {
                    if (data?.data?.data?.exists == true) {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                        this.el.$form.find('#registered_user_tabPane_external #registeredUserId').val(data?.data?.data?.id);
                    } else {
                        input.classList.remove('is-valid');
                        input.classList.add('is-invalid');
                        this.el.$form.find('#registered_user_tabPane_external #registeredUserId').val("");
                    }

                },
                error: () => {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                    this.el.$form.find('#registered_user_tabPane_external #registeredUserId').val("");
                }
            });

        } else if (value.length === 0) {
            input.classList.remove('is-valid', 'is-invalid');

        } else if (value.length > 0) {
            if (input.timeout) clearTimeout(input.timeout);

            if (input.connect && input.connect.request.readyState !== 0 && input.connect.request.readyState !== 4) {
                this.searchClientIDValidation(input, value, true);
            } else {
                input.timeout = setTimeout(() => {
                    this.searchClientIDValidation(input, value, true);
                }, 400);
            }
        } else {
            input.classList.remove('is-valid', 'is-invalid');
        }
    },
    submit: function (event) {
        if (this.el.form.prop('method').toLowerCase() == 'get') return;

        this.el.$form.find('input[type="hidden"].DateValue:not(:disabled)').each((i, input) => {
            if ($(input).val() != '') {
                var date = moment($(input).val(), 'YYYY-MM-DDTHH:mm:ssZ', true);
                if (!date.isValid()) {
                    $(input).closest('div').find('.viewDateValue').val('');
                    $(input).val('');
                    return false;
                }
            }
        });
        var hasSearchClient = this.el.$form.find("#searchClient:not(:disabled)").length > 0;
        var validSearchClient = this.el.$form.find("#searchClient.is-valid:not(:disabled)").length > 0;

        if (hasSearchClient && !validSearchClient) {
            return false;
        }


        event.preventDefault();

        if (this.setCaptchaToken(event)) return;

        // Validate form
        if (!this.el.$form.isValid()) return false;

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
        if (this.el.form.prop('method').toLowerCase() == 'post') {
            $.post(
                this.el.form.attr('action'),
                {
                    data: JSON.stringify(this.dataForm),
                },
                this.onReceive.bind(this),
                'json'
            )
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
        }
    },
    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);
        this.trigger('callbackBefore', response);
        var message = LC.global.languageSheet.error,
            success = 0;
        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;

            if (success) {
                this.showMessage(message, 'success');
                if (response.data.data.redirect && response.data.data.redirect.length) {
                    window.location = response.data.data.redirect;
                }
            } else {
                this.showMessage(message, 'danger');
            }
        }
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', false);

        this.trigger('callback', response);
    },
});

LC.SaveCompanyDivisionForm = LC.RegisteredUserCreateForm.extend({
    name: 'saveCompanyDivisionForm',
    elementId: 'requestFormModal',
    initialized: false,
    initialize: function (form) {
        LC.RegisteredUserCreateForm.prototype.initialize.call(this, form);
        this.trigger('initializeBefore');
        this.setImageUpload2();
        this.validateEmail();

        this.el.$form.find('.addressUserField').each((i, el) => {
            this.initLocations($(el));
        });

        // >>> FIX: fuerza habilitar buscador en todos los flujos
        this.ensureSearchEnabledOn(this.el.$form);
        this.monkeyPatchLocalize();

        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    validateEmail: function () {
        var $form = this.el.$form;
        var $emailInput = $form.find('input[name="email"]');
        var debounceMs = 1000;
        var timer;

        function isValidEmail(v) {
            return /^[\w-+]+(\.[\w-+]+){0,50}@[\w-]+(\.[\w-]+){0,50}(\.[a-zA-Z]{2,})$/.test(v);
        }

        $emailInput.off('.emailCheck');              // evita duplicar handlers
        $form.off('submit.emailCheck');

        $emailInput.on('input.emailCheck blur.emailCheck', function () {
            var typed = $emailInput.val().trim();
            clearTimeout(timer);

            if (typed.length < 4) {                    // menos de 4 chars => limpia estado
                $emailInput.removeClass('is-valid is-invalid');
                $form.find('[data-email-msg]').empty();
                return;
            }

            timer = setTimeout(function () {           // valida si no cambió en debounceMs
                var now = $emailInput.val().trim();
                if (now !== typed) return;               // el usuario siguió escribiendo
                var ok = isValidEmail(now);

                $emailInput.toggleClass('is-valid', ok)
                    .toggleClass('is-invalid', !ok);

                $emailInput.trigger(ok ? 'email:valid' : 'email:invalid', [now]);
            }, debounceMs);
        });

        // respaldo al enviar
        $form.on('submit.emailCheck', function (e) {
            var v = $emailInput.val().trim();
            if (!isValidEmail(v)) { e.preventDefault(); $emailInput.focus(); }
        });
    },
    initLocations: function ($el) {
        var $countrySelect = $el.find('.countryField');
        if (!$countrySelect.length) return;

        // evita doble init
        if ($countrySelect.prop('lc-init')) {
            // asegúrate de que el buscador no quede disabled
            $el.find('.locationSearch').prop('disabled', false)
                .closest('.locationSearchGroup').removeClass('has-error');
            return;
        }
        $countrySelect.prop('lc-init', true);

        var $locationInput = $('input[name="' + $countrySelect.attr('name').replace('country', 'locationList') + '"]');

        if ($locationInput.length && Number($locationInput.val()) > 0) {
            loadLocations.bind($countrySelect)($countrySelect.val(), $locationInput.val());
            $locationInput.remove();
        } else {
            loadLocations.bind($countrySelect)($countrySelect.val() || LC.global.session.countryId);
        }

        // garantiza que el input de búsqueda quede habilitado tras la carga
        setTimeout(function () {
            $el.find('.locationSearch').prop('disabled', false)
                .closest('.locationSearchGroup').removeClass('has-error');
        }, 0);
    },
    ensureSearchEnabledOn: function ($scope) {
        function enable($ctx) {
            $ctx.find('.locationSearch').prop('disabled', false)
                .closest('.locationSearchGroup').removeClass('has-error');
        }
        // inicial
        enable($scope);

        // volver a elegir
        $scope.off('click.lcResetCountry', '.resetCountrySelector')
            .on('click.lcResetCountry', '.resetCountrySelector', function () {
                enable($(this).closest('.addressUserField'));
            });

        // cambio de país
        $scope.off('change.lcCountryField', '.countryField')
            .on('change.lcCountryField', '.countryField', function () {
                enable($(this).closest('.addressUserField'));
            });

        // activar pestaña de sugerencias
        $scope.off('shown.bs.tab.lcSuggest', 'a.countrySuggestTab')
            .on('shown.bs.tab.lcSuggest', 'a.countrySuggestTab', function () {
                enable($(this).closest('.addressUserField'));
            });
    },

    monkeyPatchLocalize: function () {
        if (window.__lcLocPatched) return;
        window.__lcLocPatched = true;

        // wrap resetCountrySelector
        var _reset = window.resetCountrySelector;
        if (typeof _reset === 'function') {
            window.resetCountrySelector = function ($parent) {
                var r = _reset.apply(this, arguments);
                try {
                    $parent.find('.locationSearch').prop('disabled', false)
                        .closest('.locationSearchGroup').removeClass('has-error');
                } catch (e) { }
                return r;
            };
        }

        // wrap loadLocations
        var _load = window.loadLocations;
        if (typeof _load === 'function') {
            window.loadLocations = function (countryCode, locationId) {
                var r = _load.apply(this, arguments);
                try {
                    var $scope = $(this).closest('.addressUserField');
                    $scope.find('.locationSearch').prop('disabled', false)
                        .closest('.locationSearchGroup').removeClass('has-error');
                } catch (e) { }
                return r;
            };
        }
    },
    setImageUpload2: function () {
        const $form = this.el.$form,
            $fileInput = $form.find('input[name="companyDivisionImageUpload"]'),
            $textInput = $form.find('input[name="image2"]');
        $fileInput.off('change.lcImageUpload')
            .on('change.lcImageUpload', function () {
                const fileName = this.files.length ? this.files[0].name : '';
                $textInput.val(fileName);
            });
        $textInput
            .attr('readonly', true)
            .css('cursor', 'pointer')
            .off('click.lcOpenFile')
            .on('click.lcOpenFile', () => $fileInput.trigger('click'));
    },
    submit: function (event) {
        event.preventDefault();
        if (this.el.form.prop('method').toLowerCase() == 'get') return;

        this.el.$form.find('input[type="hidden"].DateValue:not(:disabled)').each((i, input) => {
            if ($(input).val() != '') {
                var date = moment($(input).val(), 'YYYY-MM-DDTHH:mm:ssZ', true);
                if (!date.isValid()) {
                    $(input).closest('div').find('.viewDateValue').val('');
                    $(input).val('');
                    return false;
                }
            }
        });
        var hasSearchClient = this.el.$form.find("#searchClient:not(:disabled)").length > 0;
        var validSearchClient = this.el.$form.find("#searchClient.is-valid:not(:disabled)").length > 0;

        if (hasSearchClient && !validSearchClient) {
            return false;
        }

        if (this.setCaptchaToken(event)) return;
        // Validate form
        if (!this.el.$form.isValid()) return false;
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
        if (this.el.form.prop('method').toLowerCase() == 'post') {
            $.post(
                this.el.form.attr('action'),
                {
                    data: JSON.stringify(this.dataForm),
                },
                this.onReceive.bind(this),
                'json'
            )
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
        }
    },
    hasBeenSubmitted: function () {
        return this.submitted;
    },
    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);
        this.trigger('callbackBefore', response);
        var message = LC.global.languageSheet.error,
            success = 0;

        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;

            if (success) {
                this.showMessage(message, 'success');
                if (response.data.data.redirect && response.data.data.redirect.length) {
                    window.location = response.data.data.redirect;
                }
            } else {
                this.showMessage(message, 'danger');
            }
        }
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', false);
        this.trigger('callback', response);
    },

});

/**
 * @class LC Account Registered User Update Form
 * @description Update Account Registered User Form
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.AccountRegisteredUserUpdateForm = LC.Form.extend({
    name: 'accountRegisteredUserUpdateForm',
    elementId: 'requestFormModal',
    initialized: false,
    unmodifiedData: {},
    initialize: function (form) {
        this.trigger('initializeBefore');
        this.trigger('initializeCallback');
        this.unmodifiedData = this.convertToJson($(form));
        this.setupRoleFilter();
        this.el.form.initialized = true;
    },
    setupRoleFilter: function () {
        if (!this.el.$form) return;

        var $role = this.el.$form.find('select[name="roleId"]');
        var $master = this.el.$form.find('input[name="master"]');
        if (!$role.length || !$master.length) return;

        // Obtener el tipo de cuenta desde el atributo data-account-type
        var accountType = ($role.attr('data-account-type') || '').trim();

        // Clonar TODAS las opciones disponibles (master y no-master) que vienen del PHP
        var $all = $role.find('option').clone();

        var rebuild = function (isMaster) {
            // Guardar la selección actual antes de filtrar
            var current = $role.val();

            // Determinar qué tipo de roles mostrar: '1' para master, '0' para no-master
            var wanted = isMaster ? '1' : '0';

            // Filtrar opciones según el tipo de cuenta y estado master
            var $opts;
            if (isMaster && accountType === 'COMPANY') {
                // CASO ESPECIAL: COMPANY + master → solo mostrar opción por defecto (rol base)
                $opts = $all.filter(function () {
                    return this.value === '0';
                });
            } else {
                // CASO NORMAL: Filtrar por data-master
                $opts = $all.filter(function () {
                    // La opción por defecto siempre se muestra
                    if (this.value === '0') return true;
                    // Filtrar por data-master
                    return (this.getAttribute('data-master') === wanted);
                });
            }

            // Reemplazar las opciones del select con las filtradas
            $role.empty().append($opts.clone());

            // Actualizar el texto de la opción por defecto según el tipo de cuenta y estado master
            var $defaultOption = $role.find('option[value="0"]');
            if ($defaultOption.length) {
                var labelBase = $defaultOption.attr('data-label-base');
                var labelCompanyMaster = $defaultOption.attr('data-label-company-master');

                // COMPANY + master → "Control total"
                // COMPANY_DIVISION + master → "Rol base"
                // Cualquier tipo + NO master → "Rol base"
                if (isMaster && accountType === 'COMPANY' && labelCompanyMaster) {
                    $defaultOption.text(labelCompanyMaster);
                } else if (labelBase) {
                    $defaultOption.text(labelBase);
                }
            }

            // Intentar mantener la selección actual si todavía existe después del filtrado
            if ($role.find('option[value="'+current+'"]').length) {
                $role.val(current);
            } else {
                // Si la opción actual ya no es válida, seleccionar la opción por defecto
                $role.val('0').trigger('change');
            }

            // Lógica de disabled según tipo de cuenta:
            // - COMPANY + master → deshabilitar (solo puede usar rol base)
            // - COMPANY_DIVISION + master → habilitar (puede elegir roles master)
            // - No master → siempre habilitar
            var shouldDisable = isMaster && accountType === 'COMPANY';
            $role.prop('disabled', shouldDisable);
        };

        // Aplicar filtrado inicial basado en el estado actual del checkbox master
        rebuild($master.is(':checked'));

        // Escuchar cambios en el checkbox master para refiltrar dinámicamente
        $master.on('change', function () {
            rebuild($(this).is(':checked'));
        });
    },
    convertToJson: function (form) {
        var o = {};
        form.serializeArray().forEach(item => { o[item.name] = item.value; });
        return o;
    },

    difference: function (unmodifiedData, modifiedData) {
        var out = {};
        var ignoreKeys = ['accountId', 'registeredUserId'];
        var keys = new Set([...Object.keys(unmodifiedData), ...Object.keys(modifiedData)]);
        keys.forEach(key => {
            if (ignoreKeys.includes(key)) {
                out[key] = unmodifiedData[key];
                return;
            }
            if ((unmodifiedData[key] ?? null) !== (modifiedData[key] ?? null)) out[key] = modifiedData[key];
        });
        return out;
    },

    submit: function (event) {
        if (this.el.$form.prop('method').toLowerCase() == 'get') return;

        event.preventDefault();
        if (this.setCaptchaToken(event)) return;

        // Validate form
        if (!this.el.$form.isValid()) return false;

        var modifiedData = this.convertToJson($(this.el.$form));
        var jsonResult = this.difference(this.unmodifiedData, modifiedData);
        jsonResult.redirect = location.href;
        if (jsonResult.master) {
            $('#registeredUserUpdateConfirmPopup').modal('show');
            var $confirm = $('#updateRegisteredUserButtonConfirm');
            var submit = this;
            $confirm.off('click').one('click', function () {
                if (submit.el.$form.prop('method').toLowerCase() == 'post') {
                    $.post(
                        submit.el.$form.attr('action'),
                        {
                            data: JSON.stringify(jsonResult),
                        },
                        submit.onReceive.bind(submit),
                        'json'
                    )
                        .fail(submit.onFail.bind(submit))
                        .always(submit.onComplete(submit));
                }
            });
        } else {
            if (this.el.$form.prop('method').toLowerCase() == 'post') {
                this.el.$form.find('[type="submit"]').attr('disabled', true);
                $.post(
                    this.el.$form.attr('action'),
                    {
                        data: JSON.stringify(jsonResult),
                    },
                    this.onReceive.bind(this),
                    'json'
                )
                    .fail(this.onFail.bind(this))
                    .always(this.onComplete(this));
            }
        }
    },
    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);
        this.trigger('callbackBefore', response);
        var message = LC.global.languageSheet.error,
            success = 0;
        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;

            if (success) {
                this.showMessage(message, 'success');
                if (response.data.data.redirect && response.data.data.redirect.length) {
                    window.location = response.data.data.redirect;
                }
            } else {
                this.showMessage(message, 'danger');
            }
        }
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', false);
        this.trigger('callback', response);
    },
});

LC.RegisteredUserForm = LC.Form.extend({
    name: 'registeredUserForm',
    elementId: 'requestFormModal',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        initCalendar(this.el.$form);
        this.searchClientExtern();
        this.setImageUpload();
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    searchClientExtern: function () {
        const $input = this.el.$form.find('#username');
        if (!$input.length) return;

        $input.off('keyup', this.boundSearchHandler);

        this.boundSearchHandler = this.searchHandler.bind(this);

        this.boundSearchHandler({ target: $input[0] });

        $input.on('keyup', this.boundSearchHandler);
    },
    setImageUpload: function () {
        const $form = this.el.$form,
            $fileInput = $form.find('input[name="registeredUserFormImageUpload"]'),
            $textInput = $form.find('input[name="image"]');
        $fileInput.off('change.lcImageUpload')
            .on('change.lcImageUpload', function () {
                const fileName = this.files.length ? this.files[0].name : '';
                $textInput.val(fileName);
            });
        $textInput
            .attr('readonly', true)
            .css('cursor', 'pointer')
            .off('click.lcOpenFile')
            .on('click.lcOpenFile', () => $fileInput.trigger('click'));
    },
    searchHandler: function (e) {
        const input = e.target;
        const value = input.value.trim();

        this.searchClientUsernameValidation(input, value, false);
    },
    searchClientUsernameValidation: function (element, value, execute) {
        const route = LC.global.routePaths.ACCOUNT_INTERNAL_GET_REGISTERED_USER_EXISTS;
        const input = element;
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
        if (execute) {
            $.ajax({
                url: route,
                method: 'GET',
                data: { q: value, registeredUserSearchType: "USERNAME" },
                success: (data) => {
                    if (data?.data?.data?.id != this.el.$form.find('#registeredUserId').val()) {
                        if (data?.data?.data?.exists == true) {
                            input.classList.remove('is-valid');
                            input.classList.add('is-invalid');
                        } else {
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                        }
                    }
                },
                error: () => {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                }
            });

        } else if (value.length === 0) {
            input.classList.remove('is-valid', 'is-invalid');

        } else if (value.length > 0) {
            if (input.timeout) clearTimeout(input.timeout);

            if (input.connect && input.connect.request.readyState !== 0 && input.connect.request.readyState !== 4) {
                this.searchClientUsernameValidation(input, value, true);
            } else {
                input.timeout = setTimeout(() => {
                    this.searchClientUsernameValidation(input, value, true);
                }, 400);
            }
        } else {
            input.classList.remove('is-valid', 'is-invalid');
        }
    },
    submit: function (event) {
        event.preventDefault();
        if (this.el.$form.prop('method').toLowerCase() == 'get') return;

        if (this.setCaptchaToken(event)) return;

        // Validate form
        if (!this.el.$form.isValid()) return false;

        // Avoid mutiple submit
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', true);

        var jsonResult = {};
        this.el.$form.serializeArray().forEach(item => {
            jsonResult[item.name] = item.value;
        });

        // Post
        if (this.el.$form.prop('method').toLowerCase() == 'post') {
            $.post(
                this.el.$form.attr('action'),
                {
                    data: JSON.stringify(jsonResult),
                },
                this.onReceive.bind(this),
                'json'
            )
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
        }
    },
    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);
        this.trigger('callbackBefore', response);
        var message = LC.global.languageSheet.error,
            success = 0;
        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;

            if (success) {
                this.showMessage(message, 'success');
                if (response.data.data.redirect && response.data.data.redirect.length) {
                    window.location = response.data.data.redirect;
                }
            } else {
                this.showMessage(message, 'danger');

            }
        }
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', false);
        this.trigger('callback', response);
    }
});

/**
 * @class LC Registered User Update Form
 * @description Update Registered User Form
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.RegisteredUserApproveForm = LC.Form.extend({
    name: 'registeredUserApproveForm',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        initCalendar(this.el.$form);
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    submit: function (event) {
        if (this.el.$form.prop('method').toLowerCase() == 'get') return;
        event.preventDefault();
        if (this.setCaptchaToken(event)) return;

        // Validate form
        if (!this.el.$form.isValid()) return false;

        // Avoid mutiple submit
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', true);

        var jsonResult = {};
        this.el.$form.serializeArray().forEach(item => {
            jsonResult[item.name] = item.value;
        });

        // Post
        if (this.el.$form.prop('method').toLowerCase() == 'post') {
            $.post(
                this.el.$form.attr('action'),
                {
                    data: JSON.stringify(jsonResult),
                },
                this.onReceive.bind(this),
                'json'
            )
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
        }
    },
    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);
        this.trigger('callbackBefore', response);
        var message = LC.global.languageSheet.error,
            success = 0;
        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;

            if (success) {
                var approvedStatus = $('.approvalStatus');
                var dataFormat = $(approvedStatus).find(".approveRegisteredUser").attr('data-format');
                var payload = response?.data?.data;
                var root = payload?.registeredUser;
                if (!root) return;
                const toLowerCamel = s =>
                    s.toLowerCase().replace(/_+([a-z0-9])/g, (_, c) => c.toUpperCase());
                var account = root.account || {};

                $(".title-user-section").text(LC.global.languageSheet.accountRegisteredUserRelationshipApprovalTitle);
                $(".description-user-section").text(LC.global.languageSheet.accountRegisteredUserRelationshipApprovalDescription);

                $(approvedStatus).find('.accountName .form-control-plaintext').text(account.name || '');

                var label = toLowerCamel('ACCOUNT_STATUS_' + account.status);
                $(approvedStatus).find('.accountStatus .form-control-plaintext').text(LC.global.languageSheet?.[label] ?? label);

                label = toLowerCamel('ACCOUNT_TYPE_' + account.type);
                approvedStatus.find('.accountType .form-control-plaintext').text(LC.global.languageSheet?.[label] ?? label);

                var dt = account?.dateAdded;
                $(approvedStatus).find('.accountDateAdded .form-control-plaintext').text(dt ? moment(dt).format(dataFormat) : '-');

                var master = account.master || {};
                $(approvedStatus).find('.accountMaster .form-control-plaintext').text([master.firstName, master.lastName].filter(Boolean).join(' '));
                $(approvedStatus).find('.accountEmail .form-control-plaintext').text(master.email || '-');

                var registeredUser = root.registeredUser || {};
                $(approvedStatus).find('.registeredUserName .form-control-plaintext').text(registeredUser.email || '-');
                $(approvedStatus).find('.registeredUserFirstName .form-control-plaintext').text(registeredUser.firstName || '-');
                $(approvedStatus).find('.registeredUserUsername .form-control-plaintext').text(registeredUser.username || '-');
                $(approvedStatus).find('.registeredUserLastName .form-control-plaintext').text(registeredUser.lastName || '-');
                $(approvedStatus).find('.registeredUserPid .form-control-plaintext').text(registeredUser.pId || '-');

                label = toLowerCamel(registeredUser.gender);
                $(approvedStatus).find('.registeredUserGender .form-control-plaintext').text(LC.global.languageSheet?.[label] ?? label);

                dt = registeredUser?.birthday;
                $(approvedStatus).find('.registeredUserBirthDate .form-control-plaintext').text(dt ? moment(dt).format(dataFormat) : '-');

                label = toLowerCamel('ACCOUNT_REGISTERED_USER_STATUS_' + root.status);
                $(approvedStatus).find('.accountRegisteredUserStatus .form-control-plaintext').text(LC.global.languageSheet?.[label] ?? label);

                label = root.master ? "yes" : "no";
                $(approvedStatus).find('.accountRegisteredUserMaster .form-control-plaintext').text(LC.global.languageSheet?.[label] ?? label);

                dt = root.dateAdded;
                $(approvedStatus).find('.accountRegisteredUserDateAdded .form-control-plaintext').text(dt ? moment(dt).format(dataFormat) : '-');

                $(approvedStatus).find('.accountRegisteredUserJob .form-control-plaintext').text(root.job || '-');
                $(approvedStatus).find('.accountRegisteredUserRole .form-control-plaintext').text(root.role?.name || '-');


                this.showMessage(message, 'success');
                $('.pendingApprovalStatus').hide();
                $(approvedStatus).removeClass('d-none').show();

                return;

            } else {
                this.showMessage(message, 'danger');
            }
        }
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', false);
        this.trigger('callback', response);
    },
});

/*
 * @class LC Registered User Update Form
 * @description Update Registered User Form
 * @memberOf LC
 * @extends {LC.Form}
 */
LC.RegisteredUserMoveForm = LC.Form.extend({
    name: 'registeredUserMoveForm',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    submit: function (event) {
        if (this.el.$form.prop('method').toLowerCase() == 'get') return;
        event.preventDefault();
        if (this.setCaptchaToken(event)) return;

        // Validate form
        if (!this.el.$form.isValid()) return false;

        // Avoid mutiple submit
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', true);

        var jsonResult = {};
        this.el.$form.serializeArray().forEach(item => {
            jsonResult[item.name] = item.value;
        });

        // Post
        if (this.el.$form.prop('method').toLowerCase() == 'post') {
            $.post(
                this.el.$form.attr('action'),
                {
                    data: JSON.stringify(jsonResult),
                },
                this.onReceive.bind(this),
                'json'
            )
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
        }
    },
    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);
        this.trigger('callbackBefore', response);
        var message = LC.global.languageSheet.error,
            success = 0;
        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;

            if (success) {
                this.showMessage(message, 'success');
                if (response.data.data.redirect && response.data.data.redirect.length) {
                    window.location = response.data.data.redirect;
                }
            } else {
                this.showMessage(message, 'danger');

            }
        }
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', false);
        this.trigger('callback', response);
    },
});

LC.warnings = {
    show: function () {
        if (globalThis?.lcCommerceSession?.basket?.warnings != undefined
            && globalThis.lcCommerceSession.basket.warnings != null) {
            let message = globalThis.lcCommerceSession.basket.warnings.map((warning) => {
                return warning.message + '<br>';
            });
            if (message.length) {
                LC.Form.prototype.showMessage(message, 'danger');
            }
        }
    }
};

LC.AccountCompanyRolesFilterForm = LC.Form.extend({
    name: 'accountCompanyRolesFilterForm',
    elementId: 'requestFormModal',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        initCalendar(this.el.$form);
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    submit: function (event) {
        event.preventDefault();
        var enableSubmit = true;

        var jsonResult = {};
        this.el.$form.serializeArray().forEach(item => {
            jsonResult[item.name] = item.value;
        });
        var oldParams = new URLSearchParams(window.location.search);
        var sort = oldParams.get("sort");
        var params = new URLSearchParams();
        if (sort) params.set('sort', sort);

        Object.entries(jsonResult).forEach(([key, value]) => {
            if (value && value !== '-') {
                params.set(key, value);
            } else {
                params.delete(key);
            }
        });
        var baseUrl = window.location.pathname;
        var newUrl = baseUrl + '?' + params.toString();
        window.location.href = newUrl;
        return enableSubmit;
    },
    hasBeenSubmitted: function () {
        return this.submitted;
    },
    callback: function (data) {
    },
});

LC.SaveCompanyRoleForm = LC.Form.extend({
    name: 'saveCompanyRoleForm',
    elementId: 'requestFormModal',
    initialized: false,
    autoSubmit: false,

    initialize: function (form) {
        this.trigger('initializeBefore');
        this.initDropdowns(this.el.$form);
        this.initDivisionMasterRules();      // 1º: fija disabled/checked
        this.initPermissionsPropagation();   // 2º: engancha propagación
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },

    // ---- Reglas para TARGET = COMPANY_DIVISION_MASTER ----
    initDivisionMasterRules: function () {
        var $form = this.el.$form;
        var $target = $form.find('#target'); // <select name="target" id="target">

        var forceDisabledIds = [
            'permissionThisAccountUpdate',
            'permissionThisAccountDelete',
            'permissionThisAccountEmployeesRead',
            'permissionThisAccountEmployeesUpdate',
            'permissionThisAccountEmployeesCreate',
            'permissionThisAccountEmployeesDelete',
            'permissionThisAccountEmployeesRoleUpdate',
            'permissionSubCompanyStructureRead',
            'permissionSubCompanyStructureUpdate',
            'permissionSubCompanyStructureCreate',
            'permissionSubCompanyStructureDelete',
            'permissionSubCompanyStructureEmployeesRead',
            'permissionSubCompanyStructureEmployeesUpdate',
            'permissionSubCompanyStructureEmployeesCreate',
            'permissionSubCompanyStructureEmployeesDelete',
            'permissionSubCompanyStructureEmployeesRoleUpdate',
            'permissionSubCompanyStructureMasterUpdate',
            'permissionOrdersReadOwn',
            'permissionOrdersReadAllEmployees',
            'permissionOrdersReadThisAccount',
            'permissionOrdersReadSubAccounts',
            'allowDirectOrderCreation',
            'allowDirectOrderApprovalThisAccount',
            'allowDirectOrderApprovalSubAccounts'
        ];

        function applyRules() {
            var isMaster = ($target.val() === 'COMPANY_DIVISION_MASTER');
            forceDisabledIds.forEach(function (id) {
                var $el = $form.find('#' + id);
                if (!$el.length) return;
                if (isMaster) {
                    $el.prop('checked', true).prop('disabled', true);
                } else {
                    $el.prop('disabled', false);
                }
            });
        }

        $target.on('change', applyRules);
        applyRules();
    },

    // ---- UI de desplegables de filas ----
    initDropdowns: function (form) {
        function toggleRows($parentRow, isExpanded) {
            var parentId = $parentRow.attr('data-id');
            if (!parentId) return;

            var parentLevel = parseInt($parentRow.attr('data-level') || 0);
            var $arrow = $parentRow.find('.expand-arrow');

            if (isExpanded) $arrow.removeClass('expanded');
            else $arrow.addClass('expanded');

            var $current = $parentRow.next();
            var childrenToToggle = [];

            while ($current.length) {
                var currentLevel = parseInt($current.attr('data-level') || 0);
                if (currentLevel <= parentLevel) break;

                if ($current.attr('data-parent')) {
                    childrenToToggle.push($current);
                    if (!isExpanded && $current.find('.expand-arrow').length && !$current.find('.expand-arrow').hasClass('expanded')) {
                        $current.find('.expand-arrow').addClass('expanded');
                    }
                }
                $current = $current.next();
            }

            childrenToToggle.forEach(function ($row) {
                if (isExpanded) {
                    $row.hide().addClass('hidden-by-parent');
                } else {
                    var shouldShow = true;
                    var directParentId = $row.attr('data-parent');
                    var $directParent = form.find('tr[data-id="' + directParentId + '"]');
                    if ($directParent.length) {
                        var $parentArrow = $directParent.find('.expand-arrow');
                        if ($parentArrow.length && !$parentArrow.hasClass('expanded')) shouldShow = false;
                    }
                    if (shouldShow) $row.show().removeClass('hidden-by-parent');
                }
            });
        }

        form.on('click', '.expand-arrow', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $row = $(this).closest('tr');
            var isCurrentlyExpanded = $(this).hasClass('expanded');
            toggleRows($row, isCurrentlyExpanded);
        });

        form.on('click', '.companyRoleItemName', function (e) {
            if (!$(e.target).is('input, label')) {
                var $arrow = $(this).find('.expand-arrow');
                if ($arrow.length) $arrow.click();
            }
        });
    },

    // ---- Propagación de permisos ----
    initPermissionsPropagation: function () {
        var self = this;
        var $form = this.el.$form;

        function getPermissionType(name) {
            if (!name) return null;
            if (name.includes('RoleUpdate')) return 'RoleUpdate';
            if (name.includes('Read')) return 'Read';
            if (name.includes('Create')) return 'Create';
            if (name.includes('Delete')) return 'Delete';
            if (name.includes('Update')) return 'Update';
            return null;
        }

        function getParentRow($row) {
            var parentId = $row.attr('data-parent');
            if (!parentId) return null;
            var $current = $row.prev();
            while ($current.length) {
                if ($current.attr('data-id') === parentId) return $current;
                $current = $current.prev();
            }
            return null;
        }

        function getAllDescendantRows($row) {
            var rowLevel = parseInt($row.attr('data-level') || 0);
            var descendants = [];
            var $current = $row.next();
            while ($current.length) {
                var currentLevel = parseInt($current.attr('data-level') || 0);
                if (currentLevel <= rowLevel) break;
                descendants.push($current[0]);
                $current = $current.next();
            }
            return $(descendants);
        }

        function setChecked($el, val) {
            if (!$el || !$el.length) return;
            if ($el.is(':disabled')) return; // prioridad a reglas de Master
            if ($el.prop('checked') === val) return;
            $el.prop('checked', val).trigger('change');
        }

        $form.find('.companyRolesTable input[type="checkbox"]').on('change', function () {
            var $checkbox = $(this);
            if ($checkbox.is(':disabled')) return; // nunca tocar bloqueados

            var name = $checkbox.attr('name');
            var type = getPermissionType(name);
            if (!type) return;

            var isChecked = $checkbox.is(':checked');
            var $row = $checkbox.closest('tr');

            if (isChecked) {
                // Dentro de la fila
                if (type === 'Update' || type === 'Create' || type === 'Delete' || type === 'RoleUpdate') {
                    var $read = $row.find('input[type="checkbox"]').filter(function () {
                        return $(this).attr('name')?.includes('Read');
                    }).first();
                    setChecked($read, true);
                }
                if (type === 'Create' || type === 'Delete' || type === 'RoleUpdate') {
                    var $upd = $row.find('input[type="checkbox"]').filter(function () {
                        var n = $(this).attr('name') || '';
                        return n.includes('Update') && !n.includes('RoleUpdate');
                    }).first();
                    setChecked($upd, true);
                }

                // Hacia el padre
                var $parentRow = getParentRow($row);
                if ($parentRow && (type === 'Update' || type === 'Create' || type === 'Delete' || type === 'RoleUpdate')) {
                    var $parentUpd = $parentRow.find('input[type="checkbox"]').filter(function () {
                        var n = $(this).attr('name') || '';
                        return n.includes('Update') && !n.includes('RoleUpdate');
                    }).first();
                    setChecked($parentUpd, true);
                }
                if ($parentRow && type === 'Read') {
                    var $parentRead = $parentRow.find('input[type="checkbox"]').filter(function () {
                        return $(this).attr('name')?.includes('Read');
                    }).first();
                    setChecked($parentRead, true);
                }

                // Reglas especiales: Orders
                if (name === 'permissionOrdersReadAllEmployees'
                    || name === 'permissionOrdersReadThisAccount'
                    || name === 'permissionOrdersReadSubAccounts') {
                    var $myOrders = $form.find('input[name="permissionOrdersReadOwn"]').first();
                    setChecked($myOrders, true);
                }

            } else { // desmarcar
                if (type === 'Read') {
                    // desmarcar todo lo demás de la misma fila
                    $row.find('input[type="checkbox"]').each(function () {
                        var $other = $(this);
                        if ($other[0] === $checkbox[0]) return;
                        setChecked($other, false);
                    });
                } else if (type === 'Update') {
                    // desmarcar Create/Delete/RoleUpdate de la misma fila
                    $row.find('input[type="checkbox"]').filter(function () {
                        var n = $(this).attr('name') || '';
                        return n.includes('Create') || n.includes('Delete') || n.includes('RoleUpdate');
                    }).each(function () { setChecked($(this), false); });
                }

                // Hacia abajo
                var $desc = getAllDescendantRows($row);
                if (type === 'Read') {
                    $desc.each(function () {
                        $(this).find('input[type="checkbox"]').filter(function () {
                            return ($(this).attr('name') || '').includes('Read');
                        }).each(function () { setChecked($(this), false); });
                    });
                } else if (type === 'Update') {
                    $desc.each(function () {
                        $(this).find('input[type="checkbox"]').filter(function () {
                            return ($(this).attr('name') || '').includes('Update');
                        }).each(function () { setChecked($(this), false); });
                    });
                }

                // Reglas especiales: Orders
                if (name === 'permissionOrdersReadOwn') {
                    var $otherEmployees = $form.find('input[name="permissionOrdersReadAllEmployees"]').first();
                    setChecked($otherEmployees, false);
                }
            }
        });

        // Inicialización: dispara solo los checks activos y habilitados
        setTimeout(function () {
            $form.find('.companyRolesTable input[type="checkbox"]:checked:not(:disabled)').each(function () {
                $(this).trigger('change');
            });
        }, 100);
    },

    submit: function (event) {
        if (this.el.$form.prop('method').toLowerCase() == 'get') return;
        event.preventDefault();
        if (this.setCaptchaToken(event)) return;
        if (!this.el.$form.isValid()) return false;

        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', true);

        var jsonResult = {};
        var permissions = {};
        this.el.$form.serializeArray().forEach(function (item) {
            if (item.name.startsWith('permission') || item.name.startsWith('orders')) {
                var key = item.name;
                if (key.startsWith('permission')) {
                    key = key.replace('permission', '');
                    key = key.charAt(0).toLowerCase() + key.slice(1);
                }
                permissions[key] = item.value;
            } else {
                jsonResult[item.name] = item.value;
            }
        });
        if (Object.keys(permissions).length) jsonResult['permissions'] = permissions;

        if (this.el.$form.prop('method').toLowerCase() == 'post') {
            $.post(
                this.el.$form.attr('action'),
                { data: JSON.stringify(jsonResult) },
                this.onReceive.bind(this),
                'json'
            )
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
        }
    },

    hasBeenSubmitted: function () { return this.submitted; },

    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);
        this.trigger('callbackBefore', response);

        var message = LC.global.languageSheet.error, success = 0;
        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;
            if (success) {
                if (!this.autoSubmit) this.showMessage(message, 'success');
                else LC.notify(message || 'Role duplicated successfully', { type: 'success' });

                if (response.data.data.redirect && response.data.data.redirect.length) {
                    window.location = response.data.data.redirect;
                }
            } else {
                this.showMessage(message, 'danger');
            }
        }

        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', false);
        this.trigger('callback', response);

        if (this.onCompleteCallback) this.onCompleteCallback(response);
    }
});

LC.DeleteAccountADVCAForm = LC.Form.extend({
    name: 'deleteAccountForm',
    elementId: 'requestFormModal',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    submit: function (event) {
        if (this.el.$form.prop('method').toLowerCase() == 'get') return;
        event.preventDefault();
        if (this.setCaptchaToken(event)) return;

        // Validate form
        if (!this.el.$form.isValid()) return false;

        // Avoid mutiple submit
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', true);

        var jsonResult = {};
        this.el.$form.serializeArray().forEach(item => {
            jsonResult[item.name] = item.value;
        });

        // Post
        if (this.el.$form.prop('method').toLowerCase() == 'post') {
            $.post(
                this.el.$form.attr('action'),
                {
                    data: JSON.stringify(jsonResult),
                },
                this.onReceive.bind(this),
                'json'
            )
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
        }
    },
    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);
        this.trigger('callbackBefore', response);
        var message = LC.global.languageSheet.error,
            success = 0;
        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;

            if (success) {
                this.showMessage(message, 'success');
                if (response.data.data.redirect && response.data.data.redirect.length) {
                    window.location = response.data.data.redirect;
                }
            } else {
                this.showMessage(message, 'danger');

            }
        }
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', false);
        this.trigger('callback', response);
    }
});

LC.EditAccountForm = LC.Form.extend({
    name: 'editAccountForm',
    elementId: 'requestFormModal',
    initialized: false,
    initialize: function (form) {
        this.trigger('initializeBefore');
        initCalendar(this.el.$form);
        this.setImageUpload();
        this.setImageUpload2();
        this.validateEmail();
        this.trigger('initializeCallback');
        this.el.form.initialized = true;
    },
    validateEmail: function () {
        var $form = this.el.$form;
        var $emailInput = $form.find('input[name="email"]');
        var debounceMs = 1000;
        var timer;

        function isValidEmail(v) {
            return /^[\w-+]+(\.[\w-+]+){0,50}@[\w-]+(\.[\w-]+){0,50}(\.[a-zA-Z]{2,})$/.test(v);
        }

        $emailInput.off('.emailCheck');              // evita duplicar handlers
        $form.off('submit.emailCheck');

        $emailInput.on('input.emailCheck blur.emailCheck', function () {
            var typed = $emailInput.val().trim();
            clearTimeout(timer);

            if (typed.length < 4) {                    // menos de 4 chars => limpia estado
                $emailInput.removeClass('is-valid is-invalid');
                $form.find('[data-email-msg]').empty();
                return;
            }

            timer = setTimeout(function () {           // valida si no cambió en debounceMs
                var now = $emailInput.val().trim();
                if (now !== typed) return;               // el usuario siguió escribiendo
                var ok = isValidEmail(now);

                $emailInput.toggleClass('is-valid', ok)
                    .toggleClass('is-invalid', !ok);

                $emailInput.trigger(ok ? 'email:valid' : 'email:invalid', [now]);
            }, debounceMs);
        });

        // respaldo al enviar
        $form.on('submit.emailCheck', function (e) {
            var v = $emailInput.val().trim();
            if (!isValidEmail(v)) { e.preventDefault(); $emailInput.focus(); }
        });
    },
    setImageUpload: function () {
        const $form = this.el.$form,
            $fileInput = $form.find('input[name="accountFormImageUpload"]'),
            $textInput = $form.find('input[name="image"]');
        $fileInput.off('change.lcImageUpload')
            .on('change.lcImageUpload', function () {
                const fileName = this.files.length ? this.files[0].name : '';
                $textInput.val(fileName);
            });
        $textInput
            .attr('readonly', true)
            .css('cursor', 'pointer')
            .off('click.lcOpenFile')
            .on('click.lcOpenFile', () => $fileInput.trigger('click'));
    },
    setImageUpload2: function () {
        const $form = this.el.$form,
            $fileInput = $form.find('input[name="registeredUserFormImageUpload"]'),
            $textInput = $form.find('input[name="image2"]');
        $fileInput.off('change.lcImageUpload')
            .on('change.lcImageUpload', function () {
                const fileName = this.files.length ? this.files[0].name : '';
                $textInput.val(fileName);
            });
        $textInput
            .attr('readonly', true)
            .css('cursor', 'pointer')
            .off('click.lcOpenFile')
            .on('click.lcOpenFile', () => $fileInput.trigger('click'));
    },
    submit: function (event) {
        event.preventDefault();
        if (this.el.$form.prop('method').toLowerCase() == 'get') return;
        if (this.setCaptchaToken(event)) return;

        var $emailInput = this.el.$form.find('input[name="email"]');

        // si no tiene la clase is-valid, bloquea
        if ($emailInput.hasClass('is-invalid')) {
            $emailInput.focus();
            return false;
        }

        // Validate form
        if (!this.el.$form.isValid()) return false;

        // Avoid mutiple submit
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', true);

        var jsonResult = {};
        this.el.$form.serializeArray().forEach(item => {
            if (!item.name.startsWith('customTags_')) {
                jsonResult[item.name] = item.value;
            }
        });

        // Obtener todos los elementos del form cuyo name empiece por "customTags_"
        var customTagsElements = this.el.$form.find('[name^="customTags_"]:not(:disabled)');
        // Añadir los valores al jsonResult:
        customTagsElements.each(function () {
            jsonResult[$(this).attr('name')] = $(this).val();
        });

        // Post
        jsonResult['redirect'] = location.href;
        if (this.el.$form.prop('method').toLowerCase() == 'post') {
            $.post(
                this.el.$form.attr('action'),
                {
                    data: JSON.stringify(jsonResult),
                },
                this.onReceive.bind(this),
                'json'
            )
                .fail(this.onFail.bind(this))
                .always(this.onComplete(this));
        }
    },
    callback: function (response) {
        this.el.$form.find('[type="submit"]').attr('disabled', true);
        this.trigger('callbackBefore', response);
        var message = LC.global.languageSheet.error,
            success = 0;
        if (response && response.data.response) {
            message = response.data.response.message;
            success = response.data.response.success;

            if (success) {
                this.showMessage(message, 'success');
                if (response.data.data.redirect && response.data.data.redirect.length) {
                    window.location = response.data.data.redirect;
                }
            } else {
                this.showMessage(message, 'danger');
            }
        }
        this.el.$form.find('button[type="submit"], input[type="submit"]').attr('disabled', false);
        this.trigger('callback', response);
    },
});

LC.CompanyStructure = {
    // State management
    expandedNodes: new Set(),
    loadingNodes: new Set(),
    movingNodes: new Set(),
    draggedNode: null,
    dragOverNode: null,

    // Configuration
    config: {
        containerSelector: '#companyStructureTree',
        nodeSelector: '.userCompanyStructureNode',
        toggleSelector: '.userCompanyStructureToggleBtn.expandable',
        loadingOverlaySelector: '#companyStructureLoading'
    },

    // Initialize the company structure tree
    init: function () {
        const container = document.querySelector(this.config.containerSelector);
        if (!container) return;

        this.bindDragDropEvents();
        this.initializeExpandedNodes();
    },

    // Bind drag and drop events (non-LC events)
    bindDragDropEvents: function () {
        const container = document.querySelector(this.config.containerSelector);
        if (!container) return;

        // Drag and drop events
        container.addEventListener('dragstart', this.handleDragStart.bind(this));
        container.addEventListener('dragend', this.handleDragEnd.bind(this));
        container.addEventListener('dragover', this.handleDragOver.bind(this));
        container.addEventListener('dragenter', this.handleDragEnter.bind(this));
        container.addEventListener('dragleave', this.handleDragLeave.bind(this));
        container.addEventListener('drop', this.handleDrop.bind(this));
    },

    // Initialize expanded nodes from DOM state
    initializeExpandedNodes: function () {
        const nodes = document.querySelectorAll(this.config.nodeSelector);
        nodes.forEach(node => {
            const children = node.querySelector('.userCompanyStructureChildren');
            if (children && !children.classList.contains('d-none')) {
                this.expandedNodes.add(node.dataset.nodeId);
            }
        });
    },

    // Toggle node expand/collapse state
    toggleNode: async function (nodeId, nodeData) {
        const nodeElement = document.getElementById('companyNode' + nodeId);
        if (!nodeElement) return;

        const isExpanded = this.expandedNodes.has(nodeId);

        if (isExpanded) {
            // Collapse
            this.collapseNode(nodeId, nodeElement);
        } else {
            // Expand (potentially with lazy loading)
            await this.expandNode(nodeId, nodeElement, nodeData);
        }
    },

    // Collapse a node
    collapseNode: function (nodeId, nodeElement) {
        this.expandedNodes.delete(nodeId);

        // Hide children
        const children = nodeElement.querySelector('.userCompanyStructureChildren');
        if (children) {
            children.classList.add('d-none');
        }

        // Update icon state to collapsed (show plus, hide minus)
        nodeElement.classList.remove('expanded');
        nodeElement.classList.add('collapsed');

        // Update toggle indicator
        this.updateToggleIndicator(nodeElement, false);
    },

    // Expand a node (with potential lazy loading)
    expandNode: async function (nodeId, nodeElement, nodeData) {
        // Check if this needs lazy loading
        if (nodeData.hasSubDivisionsToLoad) {
            await this.lazyLoadNode(nodeId, nodeElement, nodeData);
        } else {
            // Just show existing children
            const children = nodeElement.querySelector('.userCompanyStructureChildren');
            if (children) {
                children.classList.remove('d-none');
            }
        }

        this.expandedNodes.add(nodeId);

        // Update icon state to expanded (show minus, hide plus)
        nodeElement.classList.remove('collapsed');
        nodeElement.classList.add('expanded');

        this.updateToggleIndicator(nodeElement, true);
    },

    // Lazy load node children
    lazyLoadNode: async function (nodeId, nodeElement, nodeData) {
        if (this.loadingNodes.has(nodeId)) return;

        this.loadingNodes.add(nodeId);
        this.showNodeLoading(nodeId);

        try {
            // Call LogiCommerce API to load sub-divisions
            const response = await this.fetchSubDivisions(nodeId);

            if (response.success && response.children) {
                await this.insertLazyLoadedChildren(nodeId, response.children);
            } else {
                throw new Error(response.message || 'Failed to load divisions');
            }
        } catch (error) {
            console.error('Error loading sub-divisions:', error);
            this.showError('Failed to load sub-divisions: ' + error.message);
        } finally {
            this.loadingNodes.delete(nodeId);
            this.hideNodeLoading(nodeId);
        }
    },

    // Fetch sub-divisions from API using LogiCommerce patterns
    fetchSubDivisions: async function (accountId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: `${LC.global.routePaths.ACCOUNT_INTERNAL_COMPANY_STRUCTURE}?accountId=${accountId}&structureFilter=SUB_STRUCTURE`,
                success: function (response) {
                    if (response && response.data && response.data.response) {
                        if (response.data.response.success === 1) {
                            resolve({
                                success: true,
                                children: response.data.data.subCompanyDivisions?.items || []
                            });
                        } else {
                            resolve({
                                success: false,
                                message: response.data.response.message || 'Failed to load divisions'
                            });
                        }
                    } else {
                        resolve({ success: false, message: 'Invalid response format' });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error loading sub-divisions:', error);
                    resolve({ success: false, message: 'Network error occurred' });
                }
            });
        });
    },

    // Insert lazy loaded children into the DOM
    insertLazyLoadedChildren: async function (nodeId, children) {
        const container = document.getElementById('lazyChildrenContainer' + nodeId);
        if (!container) return;

        // TODO: Render children using the same Twig macro pattern
        // For now, create basic HTML structure
        let childrenHTML = '';
        children.forEach((child, index) => {
            const isLast = index === children.length - 1;
            childrenHTML += this.createChildNodeHTML(child, isLast);
        });

        container.innerHTML = childrenHTML;
        container.classList.remove('d-none');

        // Re-initialize LC events for new elements
        $(container).find('[data-lc-event]').dataEvent();
    },


    // Update toggle indicator (+/- icons)
    updateToggleIndicator: function (nodeElement, isExpanded) {
        const plusIcon = nodeElement.querySelector('.toggle-plus');
        const minusIcon = nodeElement.querySelector('.toggle-minus');

        if (plusIcon && minusIcon) {
            if (isExpanded) {
                plusIcon.classList.add('d-none');
                minusIcon.classList.remove('d-none');
            } else {
                plusIcon.classList.remove('d-none');
                minusIcon.classList.add('d-none');
            }
        }
    },

    // Show loading indicator for a node
    showNodeLoading: function (nodeId) {
        const loadingElement = document.getElementById('lazyLoading' + nodeId);
        if (loadingElement) {
            loadingElement.classList.remove('d-none');
        }
    },

    // Hide loading indicator for a node
    hideNodeLoading: function (nodeId) {
        const loadingElement = document.getElementById('lazyLoading' + nodeId);
        if (loadingElement) {
            loadingElement.classList.add('d-none');
        }
    },

    // Drag and Drop Handlers
    handleDragStart: function (event) {
        const nodeElement = event.target.closest(this.config.nodeSelector);
        if (!nodeElement || !nodeElement.draggable) {
            event.preventDefault();
            return;
        }

        const nodeData = this.getNodeData(nodeElement);
        this.draggedNode = nodeData;

        nodeElement.classList.add('being-dragged');
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/html', nodeElement.outerHTML);
    },

    handleDragEnd: function (event) {
        const nodeElement = event.target.closest(this.config.nodeSelector);
        if (nodeElement) {
            nodeElement.classList.remove('being-dragged');
        }

        // Clear drag over states
        document.querySelectorAll('.drag-over').forEach(el => {
            el.classList.remove('drag-over');
        });

        this.draggedNode = null;
        this.dragOverNode = null;
    },

    handleDragOver: function (event) {
        event.preventDefault();
        event.stopPropagation();
    },

    handleDragEnter: function (event) {
        event.preventDefault();
        const nodeElement = event.target.closest(this.config.nodeSelector);

        if (nodeElement && this.draggedNode) {
            const nodeData = this.getNodeData(nodeElement);

            if (nodeData && nodeData.accountId !== this.draggedNode.accountId) {
                // Only allow drop within used account scope (simplified check)
                if (this.canDropOnNode(nodeData)) {
                    this.dragOverNode = nodeData.accountId;
                    nodeElement.classList.add('drag-over');
                }
            }
        }
    },

    handleDragLeave: function (event) {
        event.preventDefault();
        const nodeElement = event.target.closest(this.config.nodeSelector);

        if (nodeElement && !nodeElement.contains(event.relatedTarget)) {
            nodeElement.classList.remove('drag-over');
        }
    },

    handleDrop: async function (event) {
        event.preventDefault();
        event.stopPropagation();

        const targetElement = event.target.closest(this.config.nodeSelector);
        if (!targetElement || !this.draggedNode) return;

        const targetData = this.getNodeData(targetElement);
        if (!targetData || targetData.accountId === this.draggedNode.accountId) return;

        targetElement.classList.remove('drag-over');

        if (!this.canDropOnNode(targetData)) {
            this.showError('Cannot move outside of used account scope');
            return;
        }

        await this.moveNode(this.draggedNode.accountId, targetData.accountId);
    },

    // Check if a node can be a drop target
    canDropOnNode: function (nodeData) {
        // Simplified check - in real implementation, this would verify
        // that the target is within the used account scope
        return !nodeData.isRoot;
    },

    // Move a node to a new parent
    moveNode: async function (draggedNodeId, targetNodeId) {
        if (this.movingNodes.has(draggedNodeId)) return;

        this.movingNodes.add(draggedNodeId);
        this.showGlobalLoading();

        // Add moving state to the node
        const nodeElement = document.getElementById('companyNode' + draggedNodeId);
        if (nodeElement) {
            nodeElement.classList.add('moving');
        }

        try {
            // Call LogiCommerce API to move the node
            const response = await this.moveNodeAPI(draggedNodeId, targetNodeId);

            if (response.success) {
                // Optimistically update the DOM
                await this.updateDOMAfterMove(draggedNodeId, targetNodeId);
                this.showSuccess('Division moved successfully');
            } else {
                throw new Error(response.message || 'Move failed');
            }
        } catch (error) {
            console.error('Error moving node:', error);
            this.showError(this.getErrorMessage(error));
        } finally {
            this.movingNodes.delete(draggedNodeId);
            this.hideGlobalLoading();

            // Remove moving state
            if (nodeElement) {
                nodeElement.classList.remove('moving');
            }
        }
    },

    // API call to move a node using LogiCommerce patterns
    // Based on README: PUT /accounts/{id} changing the parentAccountId
    moveNodeAPI: async function (nodeId, parentId) {
        return new Promise((resolve) => {
            $.post(
                LC.global.routePaths.ACCOUNT_INTERNAL_MOVE_ACCOUNT,
                {
                    data: JSON.stringify({
                        accountId: nodeId,
                        parentAccountId: parentId
                    })
                },
                function (response) {
                    if (response && response.data && response.data.response) {
                        resolve({
                            success: response.data.response.success === 1,
                            message: response.data.response.message,
                            code: response.data.data?.errorCode
                        });
                    } else {
                        resolve({ success: false, message: 'Invalid response format' });
                    }
                },
                'json'
            ).fail(function (xhr, status, error) {
                console.error('Error moving node:', error);
                resolve({ success: false, message: 'Network error occurred' });
            });
        });
    },

    // Update DOM after successful move
    updateDOMAfterMove: async function (draggedNodeId, targetNodeId) {
        // For now, just reload the page to refresh the tree
        // In a more sophisticated implementation, this would manipulate the DOM directly
        location.reload();
    },

    // Utility functions
    getNodeData: function (element) {
        const nodeElement = element.closest(this.config.nodeSelector);
        if (!nodeElement) return null;

        try {
            return JSON.parse(nodeElement.dataset.lc || '{}');
        } catch (e) {
            console.error('Failed to parse node data:', e);
            return null;
        }
    },

    getErrorMessage: function (error) {
        if (error.code === 'PARENT_ACCOUNT_MOVEMENT_INCOMPATIBLE_INHERITANCE') {
            return error.message;
        }
        return 'An error occurred while moving the division. Please try again.';
    },

    // UI feedback methods
    showGlobalLoading: function () {
        const loadingOverlay = document.querySelector(this.config.loadingOverlaySelector);
        if (loadingOverlay) {
            loadingOverlay.classList.remove('d-none');
        }
    },

    hideGlobalLoading: function () {
        const loadingOverlay = document.querySelector(this.config.loadingOverlaySelector);
        if (loadingOverlay) {
            loadingOverlay.classList.add('d-none');
        }
    },

    showSuccess: function (message) {
        LC.notify(message, { type: 'success' });
    },

    showError: function (message) {
        LC.notify(message, { type: 'danger' });
    },

    // Initialize handlers for create division modal
    initCreateDivisionHandlers: function (modal, bootstrapModal, parentAccountId) {
        const modalContent = modal.querySelector('#createDivisionModalContent');
        const form = modalContent.querySelector('form');

        if (!form) return;

        // Handle form submission
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Get the active tab to determine mode
            const activeTab = modalContent.querySelector('.tab-pane.active');
            const divisionMode = activeTab && activeTab.id === 'division_user_content_existing' ? 'existing' : 'new';

            // Update hidden field
            const modeField = form.querySelector('input[name="divisionMode"]');
            if (modeField) {
                modeField.value = divisionMode;
            }

            // Update parent account ID
            const parentField = form.querySelector('input[name="parentAccountId"]');
            if (parentField) {
                parentField.value = parentAccountId;
            }

            // Prepare form data
            const formData = new FormData(form);
            const data = {};

            for (const [key, value] of formData.entries()) {
                data[key] = value;
            }

            // Submit via AJAX
            $.ajax({
                type: 'POST',
                url: form.action,
                data: { data: JSON.stringify(data) },
                success: function (response) {
                    if (response?.data?.response) {
                        LC.notify(response.data.response.message, {
                            type: response.data.response.success === 1 ? 'success' : 'danger'
                        });

                        if (response.data.response.success === 1) {
                            bootstrapModal.hide();
                            // Reload to show new division
                            setTimeout(() => location.reload(), 1000);
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error creating division:', error);
                    LC.notify('Error creating division', { type: 'danger' });
                }
            });
        });

        // Handle field visibility based on customerType
        const customerTypeField = form.querySelector('select[name="customerType"]');
        const companyField = form.querySelector('[data-show-when="customerType:COMPANY"]');

        if (customerTypeField && companyField) {
            customerTypeField.addEventListener('change', function () {
                if (this.value === 'COMPANY') {
                    companyField.style.display = '';
                } else {
                    companyField.style.display = 'none';
                }
            });
        }
    }
};