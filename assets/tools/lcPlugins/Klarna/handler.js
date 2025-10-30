var KLARNA_PAYMENT_ITEMTYPE = 42;

LC.resources.addPluginListener('initializePaymentsCallback', function(form, oneStepCheckout) {
  if (form.find('.paymentSystemSelector:checked').length == 0 || !window.Klarna) {
    return false;
  }

  var selected = form.find('.paymentSystemSelector:checked');

  var paymentSystemId = selected.val() || 0;
  if (paymentSystemId == 0) {
    return false;
  }

  if (selected.attr("data-itemType") != KLARNA_PAYMENT_ITEMTYPE) {
    return false;
  }


  var paymentType = selected.attr("data-method") || "";
  var availableMethods = ["pay_later", "pay_over_time", "direct_debit"];
  if (!availableMethods.includes(paymentType) || !window.Klarna) {
    return false; // TODO *
  }

  $("#basketEndOrder").attr('disabled', true);
  
  if ($('div.oneStepCheckoutModule').length > 0)
    $('div.oneStepCheckoutModule').each(function(i, el) { 
      if ($(el).attr('data-lc-checkout') == "payments" || $(el).attr('data-lc-checkout') == "buttons") {
          $(el).addClass("loading");
      }
    });

  var times = 0;
  var check = setInterval(checkingToken, 200);
  function checkingToken() {
    if (times == 10) {
      clearInterval(check);
      stopEvent();
      LC.notify("Payment system not available! Please, try again or change another payment method.", {type:'danger'});
    }

    if (typeof PAYKLRNToken == "undefined" || !window.Klarna) {
      times++;
      return false;
    }

    if (PAYKLRNToken.length > 0) {
      clearInterval(check);
      window.Klarna.Payments.init({client_token: PAYKLRNToken});
      window.Klarna.Payments.load({
          container: "#klarnaIframe_" + paymentSystemId,
          instance_id: 'klarna-' + paymentSystemId,
          payment_method_categories: [paymentType],
        }, function (response) {
          stopEvent();
          if (!response.show_form) {
            LC.notify("Payment system not available! Please, try again or change another payment method.", {type:'danger'});
          }
      });      
      return true;
    }
  }

}, true);

LC.resources.addPluginListener('setPaymentSystem', function(event, data, oneStepCheckout) {
  var paymentSystemId = data.id || 0;
  if (paymentSystemId == 0) {
    return false;
  }

  var itemType = $(event.currentTarget).attr("data-itemType") || 0;
  if (itemType != KLARNA_PAYMENT_ITEMTYPE) {
    return false;
  }

  var availableMethods = ["pay_later", "pay_over_time", "direct_debit"];
  var paymentType = $(event.currentTarget).attr("data-method") || "";

  if (!availableMethods.includes(paymentType) || !window.Klarna) {
    return false; // TODO *
  }
}, true);

LC.resources.addPluginListener('beforeSubmitEndOrder', function(ev, data, oneStepCheckout) {  
  data.preventSubmit = false;

  if (data.find('.paymentSystemSelector:checked').length == 0)
    return false;

  var selected = data.find('.paymentSystemSelector:checked');
  if (selected.attr("data-itemType") == KLARNA_PAYMENT_ITEMTYPE) {

    var paymentSystemId = selected.val() || 0;
    if (paymentSystemId == 0) {
      return false;
    }

    if (typeof PAYKLRNToken == "undefined") {
      return false; // * TODO
    }

    ev.preventDefault();
    data.preventSubmit = true;

    /* TODO -> LC-770 */ $.post('/checkout/authorizeOrder', {token:PAYKLRNToken}, function(r) {
      if (r.response.error == 0) {
        window.Klarna.Payments.authorize({
          instance_id: 'klarna-' + paymentSystemId,
        }, r.response.data, function(res) {
          if (res.show_form && res.approved) {
            $.post(LC.global.routePaths.CHECKOUT_END_ORDER, {token:res.authorization_token}, function(response) {
              if (response.statusCode == 200) {
                window.location.href = response.response.url;
              } else {
                window.location.href = LC.global.routePaths.CHECKOUT_DENIED_ORDER;                
              }
            });
          } else {
            window.location.href = LC.global.routePaths.CHECKOUT_DENIED_ORDER;
          }
        });
      } else {
        window.location.href = LC.global.routePaths.CHECKOUT_DENIED_ORDER;
      }
    });

  } 
  return false;
}, true);

function stopEvent() {
  if ($('div.oneStepCheckoutModule').length > 0) {
    $('div.oneStepCheckoutModule').each(function(i, el) { 
        $(el).removeClass("loading");
    });    
  }
  $("#basketEndOrder").removeAttr("disabled");
}