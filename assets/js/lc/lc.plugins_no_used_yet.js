/**
 * ATTENTION
 * 
 * If you take a block of JS from here, remove it from the list or if you deprecate it,
 * make a note of it.
 * 
 * File classes:
 * 
 * LC.basketCountdown
 * LC.basketLockCountdown
 * LC.combinationCountdown
 * LC.countdown (@deprecated?)
 * LC.objIncrementCounter
 *
 * LC.dataEvents.externalConnectionLogin
 * LC.dataEvents.setCustomer
 * LC.dataEvents.getReturn
 * LC.dataEvents.changeCurrency
 * LC.dataEvents.addProduct (@deprecated)
 * LC.dataEvents.toggleBlogAnswer
 * LC.dataEvents.viewMoreReturnRequests (@deprecated)
 * LC.dataEvents.viewMoreReturns (@deprecated)
 * LC.dataEvents.viewMoreReturnInvoices (@deprecated)
 * LC.dataEvents.setPaymentSystemToken
 * LC.dataEvents.updateBasket
 * LC.dataEvents.actionShipperIntegration
 * LC.dataEvents.incidenceForm
 * LC.dataEvents.downloadIncidenceAttachment
 * LC.dataEvents.onProductClick (@deprecated)
 * LC.dataEvents.link (@deprecated)
 * LC.dataEvents.setBasketFapiao
 */


/**
 * @TODO implement
 * LC.combinationCountdown 
 * @param {Object} element
 */
LC.combinationCountdown = function (params) {
    var container = $('<span></span>');
    var options = {
        endDate: params.endDate,
        template: languageSheet.stockReservedText.replace(/{{units}}/gi, params.quantity),
        container: container,
        callback: function () {
            if ($('body').data('lc-page') === 'product') location.reload();
            else container.html(languageSheet.lockedStockReloadNeeded);
        },
    };

    return new LC.countdown(options);
};



/**
 * @TODO implement
 * LC.objIncrementCounter 
 * @param {Object} element
 */
LC.objIncrementCounter = function (type, listIds, item) {
    // type 1 = view, type 2 = click
    $.ajax({
        type: 'POST',
        url: '/incrementCounter',
        data: JSON.stringify('{"type": ' + type + ', "listIds": "' + listIds + '", "item": "' + item + '" }'),
        dataType: 'JSON',
    });
};

/**
 * @TODO implement
 * ExternalConnectionLogin event
 * @param {Object} event
 */
LC.dataEvents.externalConnectionLogin = function (event) {
    event.preventDefault();

    var legalCheck = $(event.currentTarget)
        .parent('div.socialLoginCheck')
        .find("input[name='agreement']")
        .get(0);

    if (legalCheck && !$(legalCheck).is(':hidden') && !$(legalCheck).is(':checked')) {
        $(legalCheck.parentNode)
            .removeClass('has-success')
            .addClass('has-error')
            .find('span.form-error')
            .remove()
            .end()
            .append('<span class="help-block form-error">' + $(legalCheck).data('validationErrorMsg') + '</span>');

        return false;
    } else {
        if (legalCheck)
            $(legalCheck.parentNode)
                .removeClass('has-error')
                .addClass('has-success')
                .find('span.form-error')
                .remove();

        var externalConnectionData = $(event.currentTarget).data('lcData');
        var externalConnection = externalConnectionData.externalConnection;
        var externalConnectionHeight = externalConnectionData.windowHeight ? externalConnectionData.windowHeight : 450;
        var externalConnectionWidth = externalConnectionData.windowWidth ? externalConnectionData.windowWidth : 400;
        window.open(
            '/user/oauth/' + externalConnection,
            externalConnection + 'LoginPopup',
            'height=' + externalConnectionWidth + ',width=' + externalConnectionWidth + ',scrollbars=yes,resizable=yes'
        );
    }
};

/**
 * @TODO rename this function here and html
 * UserAgent customer simulation event
 * @param {Object} event
 */
LC.dataEvents.setCustomer = function (event) {
    var userId = $(event.target).data('lcData').id;
    var setCustomerCallback = function (data) {
        if (data.response.REDIRECT) location.href = '/user';
        else LC.notify(data.response.message, { type: 'danger' });
    };
    $.post('/user/salesAgent/setCustomer/', JSON.stringify({ userId: userId }), setCustomerCallback, 'json');
};

/**
 * @TODO implement
 * GetReturn event
 * @param {Object} event
 */
LC.dataEvents.getReturn = function (event) {
    var objData = $(event.target).data('lcData');

    var urlReturnForm = '//' + location.host + '/orders/return/' + objData.orderId + '/' + objData.token + '/';
    var modalReturnForm = $.fn.box({
        uid: 'returnFormModal',
        source: urlReturnForm,
        showFooter: false,
        triggerOnClick: false,
        type: 'url',
    });
};


/**
 * @TODO implement
 * changeCurrency event
 * @param {Object} event
 */
LC.dataEvents.changeCurrency = function (event) {
    event.preventDefault();

    var objData = $(event.target).data('lcData');

    var currencyCallback = function (data) {
        if (data.response.ERROR == 0) location.reload();
    };

    $.post('/currency/' + objData.currencyId, JSON.stringify([]), currencyCallback, 'json');

    return false;
};

/**
 * @deprecated ?? de on es aquest addproduct? especiales cesta? gifts?
 * addProduct event
 * @param {Object} event
 */
LC.dataEvents.addProduct = function (event) {
    e.preventDefault();

    var formData = event.currentTarget.form.dataElement;

    formData.custom_callback = function () {
        window.location.reload();
    };
    formData.submit(event);
};

/**
 * @TODO implement
 * toggleBlogAnswer event
 * @param {Object} event
 */
LC.dataEvents.toggleBlogAnswer = function (event) {
    var objData = $(event.target).data('lcData');
    this.container = $(event.target)
        .parent('.answer')
        .children('.answerForm' + objData.id);

    if (this.container.find('form').length) {
        this.container.toggle();
    } else {
        $.ajax({
            url: '/blog/actions/addReplyForm/',
            type: 'post',
            data: JSON.stringify(objData),
            success: function (result) {
                this.container.html(result).toggle(); // fill the content
                new LC.blogCommentForm(this.container.find('form').get(0)); // initialize the comment form
            }.bind(this),
        });
    }
};

/**
 * @deprecated fer un nou sistema de returns (front)
 * viewMoreReturnRequests event
 * @param {Object} event
 */
LC.dataEvents.viewMoreReturnRequests = function (event) {
    var objData = $(event.target).data('lcData');

    if ($('.' + objData.idReturnRequests).css('display') === 'none') {
        $('.' + objData.idReturnRequests)
            .css('display', 'table-row')
            .addClass('rmaRequestsGroupOpened');
        $(event.target).addClass('caret-reversed');
    } else {
        $('.' + objData.idReturnRequests)
            .css('display', 'none')
            .removeClass('rmaRequestsGroupOpened');
        $(event.target).removeClass('caret-reversed');
    }
};

/**
 * @deprecated fer un nou sistema de returns (front)
 * viewMoreReturns event
 * @param {Object} event
 */
LC.dataEvents.viewMoreReturns = function (event) {
    var objData = $(event.target).data('lcData');

    if ($('.' + objData.idReturns).css('display') === 'none') {
        $('.' + objData.idReturns)
            .css('display', 'table-row')
            .addClass('returnGroupOpened');
        $(e.target).addClass('caret-reversed');
    } else {
        $('.' + objData.idReturns)
            .css('display', 'none')
            .removeClass('returnGroupOpened');
        $(event.target).removeClass('caret-reversed');
    }
};

/**
 * @deprecated fer un nou sistema de returns (front)
 * viewMoreReturnInvoices event
 * @param {Object} event
 */
LC.dataEvents.viewMoreReturnInvoices = function (event) {
    var objData = $(event.target).data('lcData');

    if ($('.' + objData.idReturnInvoices).css('display') === 'none') {
        $('.' + objData.idReturnInvoices)
            .css('display', 'table-row')
            .addClass('returnGroupOpened');
        $(event.target).addClass('caret-reversed');
    } else {
        $('.' + objData.idReturnInvoices)
            .css('display', 'none')
            .removeClass('returnGroupOpened');
        $(event.target).removeClass('caret-reversed');
    }
};

/**
 * @TODO implement
 * setPaymentSystemToken event
 * @param {Object} event
 */
LC.dataEvents.setPaymentSystemToken = function (event) {
    var objData = $(event.target).data('lcData');

    $('.paymentSystemToken').removeClass('selected');
    $(event.target)
        .parent()
        .parent()
        .addClass('selected');
    if (objData.token.length) {
        $('#paymentReference').val(objData.token);
        $('.tokenize')
            .find('input')
            .prop('checked', false);
        $('.tokenize')
            .find('input')
            .prop('disabled', true);
    } else {
        $('#paymentReference').val('');
        $('.tokenize')
            .find('input')
            .prop('checked', true);
        $('.tokenize')
            .find('input')
            .prop('disabled', false);
    }
};

/**
 * @TODO implement
 * updateBasket event
 * @param {Object} event
 */
LC.dataEvents.updateBasket = function (event) {
    var data = $(event.delegateTarget).data('data');

    switch (location.pathname) {
        case '/checkout':
        case '/checkout/oneStepCheckout':
            var callback = function () {
                lcOneStepCheckout.moduleCalls('refreshModule');
                if (LC.hasOwnProperty('miniBasket')) LC.miniBasket.reload();
            };
            break;
        default:
            var callback = function () {
                location.reload();
            };
    }

    $.post('/basket/updateBasket/', JSON.stringify(data), callback, 'json');
};

/**
 * @TODO implement
 * actionShipperIntegration event
 * @param {Object} event
 */
LC.dataEvents.actionShipperIntegration = function (event) {
    var data = {};
    data.orderId = $(event.delegateTarget).attr('data-orderId');
    data.action = $(event.delegateTarget).attr('data-action');
    data.return = $(event.delegateTarget).attr('data-return');

    var callback = function (data) {
        // lcOneStepCheckout.moduleCalls('refreshModule');
        // location.reload();
        if (data.response.error == 1) LC.notify(data.response.message, { type: 'danger' });
        else LC.notify(data.response.message, { type: 'success' });
    };

    $.post('/orders/shipperIntegration/', JSON.stringify(data), callback, 'json');
};

/**
 * @TODO implement
 * incidenceForm event
 * @param {Object} event
 */
LC.dataEvents.incidenceForm = function (event) {
    e.preventDefault();
    var data = $(event.currentTarget).data('lcData');

    //show answer/create popup
    $(e.target).box({
        // this.warningBox = $('<span/>').appendTo($(document.body)).box({
        uid: 'popupIncidenceForm',
        source: '/snippets/incidenceForm' + (data ? '?' + $.param(data) : ''),
        showFooter: false,
        type: 'url',
        callback: 'incidenceFormCallback',
        triggerOnClick: false,
    });
};

/**
 * @TODO implement
 * downloadIncidenceAttachment event
 * @param {Object} event
 */
LC.dataEvents.downloadIncidenceAttachment = function (event) {
    e.preventDefault();
    var data = $(event.currentTarget).data('lcData'),
        attachment = '',
        token = '';

    if (data.attachment) attachment = data.attachment;
    if (data.token) token = data.token;

    var newWindow = window.open(
        '//' + location.host + '/user/incidenceAttachment/' + encodeURIComponent(attachment) + '/' + token + '/',
        '_blank'
    );
    newWindow.window.onload = function () {
        // Manually close the window. Useful when the attachment doesn't exist anymore
        newWindow.window.close();
    };
};

/**
 * @deprecated what is this
 * onProductClick event
 * @param {Object} event
 */
LC.dataEvents.onProductClick = function (event) {
    var data = {};
    data.productId = $(event.delegateTarget).attr('data-id');

    LC.resources.pluginListener('onProductClick', event, data);
};

/**
 * @deprecated what is this
 * link event
 * @param {Object} event
 * @param {Object} options
 */
LC.dataEvents.link = function (event, options) {
    event.preventDefault();

    var objData = $(event.target).data('lcData');
    if (!objData.href) return false;

    window.location = objData.href;
};

/**
 * @TODO implement
 * setBasketFapiao event
 * @param {Object} event
 */
LC.dataEvents.setBasketFapiao = function (event) {
    if ($('#inputFapiaoActived').is(':checked')) {
        $('#inputInvoicenameFapiao').attr('data-validation', 'required');
        $('#selectInvoicenameFapiao').attr('data-validation', 'required');
        $('#fapiaoContent').show();
    } else {
        $('#inputInvoicenameFapiao').attr('data-validation', '');
        $('#selectInvoicenameFapiao').attr('data-validation', '');
        $('#fapiaoContent').hide();
    }
};