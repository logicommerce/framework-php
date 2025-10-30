var ADYEN_PAYMENT_ITEMTYPE = 43;
var checkout = {}
var paymentValid = false;
var paymentData = {};

LC.resources.addPluginListener('initializePaymentsCallback', function(form, oneStepCheckout) {
	setTimeout(function() {
	  if (form.find('.paymentSystemSelector:checked').length == 0) {
	    return false;
	  }

	  var selected = form.find('.paymentSystemSelector:checked');

	  var paymentSystemId = selected.val() || 0;
	  if (paymentSystemId == 0) {
	    return false;
	  }

	  if (selected.attr("data-itemType") != ADYEN_PAYMENT_ITEMTYPE) {
	    return false;
	  }

		if (!$('#adyenCheckoutAPI').attr('data-payments') || !$('#adyenCheckoutAPI').attr('data-config')) { 
			return false; 
		}

	  var paymentType = selected.attr("data-method") || "";

		var paymentMethods = $('#adyenCheckoutAPI').attr('data-payments');
		paymentMethods = JSON.parse(paymentMethods);

		var dataConfig = $('#adyenCheckoutAPI').attr('data-config');
		dataConfig = JSON.parse(dataConfig);

		var configuration = {
		    locale: dataConfig.locale,
		    environment: dataConfig.environment,
		    originKey: dataConfig.originKey,
		    paymentMethodsResponse: paymentMethods,
		    onChange: handleOnChange,
		    onError:handleOnError
		};

		var adyenData = {}
		if (typeof adyenDataConfig != "undefined" && typeof adyenDataConfig[paymentType] != "undefined") {
			adyenData = adyenDataConfig[paymentType];
    }

		var checkout = new AdyenCheckout(configuration);
		checkout.create(paymentType, adyenData).mount("#adyen_" + paymentSystemId);

	}, 500);

}, true);	

LC.resources.addPluginListener('beforeSubmitEndOrder', function(ev, data, oneStepCheckout) {  
  data.preventSubmit = false;

  if (data.find('.paymentSystemSelector:checked').length == 0)
    return false;

  var selected = data.find('.paymentSystemSelector:checked');
  if (selected.attr("data-itemType") == ADYEN_PAYMENT_ITEMTYPE) {

    var paymentSystemId = selected.val() || 0;
    if (paymentSystemId == 0) {
      return false;
    }

    ev.preventDefault();
    data.preventSubmit = true;

    if (!paymentValid) {
    	$('#basketValidationMessage').remove();
    	$('.basketButtons').prepend($('<div />',{id:'basketValidationMessage', class:'basketButtonsError errorPaymentAndShipping',html:LC.global.languageSheet.completePaymentInformation}));
      return false;
    }

    $.post(LC.global.routePaths.CHECKOUT_END_ORDER, paymentData, function(response) {
      if (response.statusCode == 200) {
        window.location.href = response.response.url;
      } else {
      	window.location.href = LC.global.routePaths.CHECKOUT_DENIED_ORDER;                
      }
    });

  } 

  return false;
}, true);

function handleOnChange(state, component) {
	$('#basketValidationMessage').remove();
	if (state.data.paymentMethod && state.isValid) {		
		paymentValid = true;
		paymentData = state.data.paymentMethod;
	}
}

function handleOnError() {
	paymentValid = false;
}