var SEQURA_PAYMENT_ITEMTYPE = 40;

LC.resources.addPluginListener('initializePaymentsBefore', function (form, oneStepCheckout) {
    if (typeof Sequra == "undefined" || !window.Sequra) {
        return false;
    }
    window.Sequra.onLoad(function () { window.Sequra.refreshComponents(); });
}, true);

LC.resources.addPluginListener('beforeSubmitEndOrder', function (ev, data, oneStepCheckout) {
    data.preventSubmit = false;

    if (data.find('.paymentSystemSelector:checked').length == 0)
        return false;

    var selected = data.find('.paymentSystemSelector:checked');
    if (selected.attr("data-itemType") == SEQURA_PAYMENT_ITEMTYPE) {

        ev.preventDefault();
        data.preventSubmit = true;

        var paymentCallback = function (response) {
            if (response.length > 0) {
                $(".basketPaymentIframe" + SEQURA_PAYMENT_ITEMTYPE).html(response);
                $(".basketPaymentIframe" + SEQURA_PAYMENT_ITEMTYPE).css("display", "block");
                setTimeout(function () {
                    window.SequraFormInstance.show();
                    window.SequraFormInstance.setCloseCallback(function (e) { window.location.reload(true); });
                }, 1000);
            }
        };

        // Send form
        $.post(LC.global.routePaths.CHECKOUT_END_ORDER, {}, paymentCallback, 'html');
    }
    return false;
}, true);