// onAddProduct
LC.resources.addPluginListener('onAddProduct', function(ev, data) {
	if ($(ev.currentTarget).attr("data-product")) {
		var productData = JSON.parse($(ev.currentTarget).attr("data-product"));
    var productName = productData.name || "";

		ga("ec:addProduct", {
		  "id": productData.id || 0,
      "name": productName,
      "category":productData.mainCategoryName || '',
      "brand":productData.brandName || '',
		  "price": productData.definition.price || 0.00,
		  "quantity": data.quantity || 1
		});

		ga("ec:setAction", "add");
		ga("send", "event", "Cart", "click", "Add to Cart product: " + productName);	
	}
});

// onProductClick
LC.resources.addPluginListener('onProductClick', function(event, data) {
  var productId = data.productId || 0;
  if (productId == 0)
    return false;

  if ($(document.body).find('.productsData').length > 0) {
    var arrProductsData = JSON.parse($('.productsData').val());
    if (arrProductsData.length > 0) {
      var result = $.grep(arrProductsData, function(e){ return e.id == productId; });
      var productName = result[0].name || "";

      if (result.length > 0) {
        ga('ec:addProduct', {
          'id': data.productId || 0,
          'name': productName,
          'category': result[0].category || '',
          'brand': result[0].brand || '',
          'position': result[0].position || 0
        });
        ga('ec:setAction', 'click', {'list': result[0].list || ''});
        ga("send", "event", "Product", "click", "Product Click: " + productName); 
      }
    }
  }
});

// setPaymentSystems
LC.resources.addPluginListener('setPaymentSystem', function(event, data, oneStepCheckout) {
  var paymentSystemId = data.id || 0;
  if (paymentSystemId == 0)
    return false;

  if ($('label.paymentSystemSelectorName[for=paymentSystem_'+paymentSystemId+']').length == 0)
    var value = paymentSystemId;
  else {
    var value = $('label.paymentSystemSelectorName[for=paymentSystem_'+paymentSystemId+']').html().trim();
  }

  ga('ec:setAction', 'checkout_option', {
    'option' : value || 0
  });
  ga("send", "event", "Cart", "Payment", "Select Payment", paymentSystemId); 
});

// setShippingSection
LC.resources.addPluginListener('setShippingSection', function(el, data, oneStepCheckout) {
  var shippingSectionId = data.id || 0;
  if (shippingSectionId == 0)
    return false;

  if ($('label.shippingSelectorName[for=shippingType_'+shippingSectionId+']').length == 0)
    var value = shippingSectionId;
  else 
    var value = $('label.shippingSelectorName[for=shippingType_'+shippingSectionId+']').find('span.shipperName').html();

  ga('ec:setAction', 'checkout_option', {
    'option' : value || 0
  });
  ga("send", "event", "Cart", "Shipping", "Select Shipping", shippingSectionId); 
});