/**
 * Used in loadCountry();
 */
/* state 1: active - 0: hidden */
function setCountryFormFields(state, fieldName) {
  if (!fieldName) return;
  if (!state) state = 0;
  
  if (window.countryUserFields) {
    window.countryUserFields[fieldName].state = state;

    if (window.countryUserFields[fieldName].formFields) {
      var countryUserFields = window.countryUserFields[fieldName].formFields;
      for (var i=0; i < countryUserFields.length; i++) {
        var fieldContainer = F('#userField'+ countryUserFields[i]+'Container');
        if (fieldContainer) {
          if (state) fieldContainer.style.display='block';
          else fieldContainer.style.display='none';
        }
      }
    }   
  }
};
 
function loadCountry(countryId, uniqueId, fieldName, className, inputType) {
  if (!inputType) inputType = 0;

  if (inputType == 1) {
    setCountryFormFields(1, fieldName);
    var container = $('#countriesSelectorContainer_'+uniqueId).parent('.userFormFields').find('.availableCountries');
    container.set('html', 'loading...<input type="text" class="required" value="" style="border:none; width:1px; height:1px; display:inline; padding:0px; margin:0px;" />');
    var parameterEncoder = new urlParameterEncoder()
      .addParameter('countryId', countryId)
      .addParameter('fieldName', fieldName)
      .addParameter('className', className)
      .addParameter('uniqueId',uniqueId)
      .addParameter('countryManualInput', 1)
      .addParameter('htmlFramework', window.htmlFramework);
    container.load('/templates/common/users/subcountriesSelector.cfm' + parameterEncoder.getParameters());
  }
  else {
    setCountryFormFields(0, fieldName);
    var container = $('countriesSelectorContainer_'+uniqueId);
    container.getParent('.userFormFields').getElements('.countryManualInputFields').destroy();
    container.set('html', 'loading...<input type="text" class="required" value="" style="border:none; width:1px; height:1px; display:inline; padding:0px; margin:0px;" />');
    var parameterEncoder = new urlParameterEncoder()
      .addParameter('countryId',countryId)
      .addParameter('subcountryId',0)
      .addParameter('fieldName',fieldName)
      .addParameter('className',className)
      .addParameter('uniqueId', uniqueId);
    container.load('/templates/common/users/subcountriesSelector.cfm'+parameterEncoder.getParameters());
  }
};

function loadSubcountries(countryId, subcountryId, uniqueId, fieldName, className, storedCountryId, showSubselects) {
  var $container = $('#countriesSelectorContainer_'+uniqueId);
  var $parent = $container.closest('.addressUserField');

  updateAddressDataBlock($parent);

  var $manualTab = $parent.find('[href^="#countryManualTab"]');
  var $suggestTab = $parent.find('[href^="#countrySuggestTab"]');

  // Control argument showSubselects
  if (arguments.length < 6) var showSubselects = 1;

  if(subcountryId.length == 0) {
    $container.html("");
    localizeCallbacks(fieldName);
    $manualTab.hide();
    $suggestTab.hide();
    return;
  }
  
  if(subcountryId == 0 && countryId.length == 0) {
    $container.html("");
    $('#subcountriesSearch_'+uniqueId).html("");
    localizeCallbacks(fieldName);
    $manualTab.hide();
    $suggestTab.hide();
    return;
  }

  if (fieldName == 'country' && settings.forceBillingAddressCountry && $('#useShippingAddress').prop('checked') == true && $('#userShippingCountryField'))
  {
    if ($('#userFieldShippingCountryContainer .countriesSelector'))
    {
      $('#userShippingCountryField').find('option').remove().end().append($('<option>', {value:countryId}).text($("#userCountryField option[value='"+countryId+"']").text()));
      loadSubcountries(countryId, 0, $('#userShippingCountryField').attr('data-field'), 'shippingCountry', 'userField');      
    }
  }

  $container.closest('div.userFormFields').find('.countryManualInputFields').remove();
  $container.html('loading...<input type="text" class="required" value="" style="border:none; width:1px; height:1px; display:inline; padding:0px; margin:0px;" />');

  var userType = $container.closest('form').data('userType');

  var parameterEncoder = new urlParameterEncoder()
    .addParameter('countryId',countryId)
    .addParameter('subcountryId',subcountryId)
    .addParameter('fieldName',fieldName)
    .addParameter('className',className)
    .addParameter('uniqueId', uniqueId)
    .addParameter('onlyNextLevel', true)
    .addParameter('showSubselects', showSubselects)
    .addParameter('htmlFramework', window.htmlFramework)
    .addParameter('userType', userType);

  $container.load('/user/subcountriesSelector/' + parameterEncoder.getParameters(), function(){
    validateCountryFields(fieldName);
    localizeCallbacks(fieldName);
    setAddressCompleted($parent);
  });

  if(!subcountryId) {
    $manualTab.show();
    $suggestTab.show();

    parameterEncoder.addParameter('itemId',countryId)
      .addParameter('onlyNextLevel', true);

    $('#subcountriesSearch_'+uniqueId).html('').load('/user/subcountriesSearch/' + parameterEncoder.getParameters(), function() {
      if($('#subcountriesSearch_'+uniqueId).html() == '') {
        $manualTab.tab('show');
        $suggestTab.hide();
      }
    });
  }
};

function loadPostalCodes(element, countryId, value, uniqueId, fieldName, className, exec) {
  var container = F('#postalCodes_'+uniqueId);

  if(exec) {
    var parameterEncoder = new urlParameterEncoder()
      .addParameter('countryId',countryId)
      .addParameter('zip',value)
      .addParameter('uniqueId',uniqueId)
      .addParameter('fieldName',fieldName)
      .addParameter('className',className)
      .addParameter('width', $(element).parent().width())
      .addParameter('htmlFramework', window.htmlFramework);

    //Get
    $(container).load('/user/postalCodesSelector/'+encodeURI(parameterEncoder.getParameters()));
  }

  else if(value.length > 2) {
    if(container.timeout) clearTimeout(container.timeout);
    if(container.innerHTML.length < 30) container.innerHTML = '<div />';

    if(container.connect && container.connect.request.readyState != 0 && container.connect.request.readyState != 4)
      loadPostalCodes(element, countryId, value, uniqueId, fieldName, className, true)
    else
      container.timeout = setTimeout(function(){
        loadPostalCodes(element, countryId, value, uniqueId, fieldName, className, true);
      }, 400);
  }
  else {
    container.innerHTML = '';
  }
};

function selectPostalCode(countryId, subcountryId, uniqueId, fieldName, className) {
  var field = F('#zipField_'+uniqueId);
  var container = F('#countriesSelectorContainer_'+uniqueId);
  var selectorContainer = F('#postalCodes_'+uniqueId);

  validateCountryFields(fieldName);
  var $parent = $(field).closest('.addressUserField');

  selectorContainer.innerHTML = '';
  if (field.classList.contains('zipOrCitySearch')) {
    field.value = '';
    $parent.find('[href^="#countryManualTab"]').tab('show');
  }
  else {
    field.value = languageSheet.zipOrCity;
  }

  var parameterEncoder = new urlParameterEncoder()
    .addParameter('countryId',countryId)
    .addParameter('subcountryId',subcountryId)
    .addParameter('fieldName',fieldName)
    .addParameter('className',className);

  container.innerHTML = 'loading...<input type="text" class="required" value="" style="border:none; width:1px; height:1px; display:inline; padding:0px; margin:0px;" />';

  //Get
  $(container).load('/user/selectPostalCode/'+parameterEncoder.getParameters(), function(){
    localizeCallbacks(fieldName);
    if ($parent) {
      setAddressCompleted($parent);
    }
  });
};

function updateAddressDataBlock(parent) {
  var $container = parent.find('.addressBlockDetails');
  var $addressFields = getAddressFields(parent);

  $container.html("");

  $addressFields.each(function(index, el) {
    $addressBlock = $('<div />');
    if (index === 0) {
      $addressBlock.addClass('firstAddressBlock');
    }
    else if (index === ($addressFields.length - 1)) {
      $addressBlock.addClass('lastAddressBlock');
    }
    $addressBlock.addClass('addressBlock').addClass('addressBlock' + (index+1));

    var val = $(el).text();
    if (!val) {
      val = $(el).val();
    }

    $addressBlock.html(val);

    $addressBlock.appendTo($container);
  });
}

function setAddressCompleted(parent) {
  parent.removeClass('address-complete');

  var completed = true;

  getAddressFields(parent).each(function(index, el) {
    if (!$(el).val()) {
      completed = false;
      return;
    }
  });

  if (completed) {
    parent.addClass('address-complete');
  }

  updateAddressDataBlock(parent);
}

function resetCountrySelector(parent) {
  parent.find('select[name$="ountry_1"]').change(); // to avoid case-sensitive "c/C" of "country" selector
  setAddressCompleted(parent);
}

function getAddressFields(parent) {
  return parent.find('select > option:selected, input.userField').not(".subcountrySearchField");
}

var localizeEvents = {
  selectPostalCode : function(){},
  selectShippingPostalCode : function(){},
  selectAddressBookPostalCode : function(){}
};

function localizeCallbacks(fieldName){
  if (fieldName == 'country')
    localizeEvents.selectPostalCode();
  else if (fieldName == 'shippingCountry')
    localizeEvents.selectShippingPostalCode();
  else if (fieldName == 'addressBookCountry')
    localizeEvents.selectAddressBookPostalCode();
}

$(document).click(function(event) {
  if(!$(event.target).closest('.subcountrySearch').length) {
    if($('.citiesSelector').is(":visible"))
        $('.citiesSelector').html("")
  }
});

function validateCountryFields (fieldName){
  // Initialize Validation form
  if (LC)
    $.validate(LC.validateFormConf);
}

function onChangeCountry(countryId, uniqueId, fieldName, className, showSubselects) {

  // Control argument showSubselects
  if (arguments.length < 5) var showSubselects = 1;

  if (fieldName == 'country' && settings.forceBillingAddressCountry && $('#useShippingAddress').prop('checked') == true && $('#userShippingCountryField')) {
    if ($('#userFieldShippingCountryContainer .countriesSelector')) {
      $('#userShippingCountryField').find('option').remove().end().append($('<option>', {value:countryId}).text($("#userCountryField option[value='"+countryId+"']").text()));
      loadSubcountries(countryId, 0, $('#userShippingCountryField').attr('data-field'), 'shippingCountry', 'userField');      
    }
  }
  else {
    loadSubcountries(countryId, 0, uniqueId, fieldName, className, 0, showSubselects);
  }
}

/* ------------------------------------------------------------------------------*/
/* ------------------------------------------------------------------------------*/
/* ------------------------------------------------------------------------------*/
function onChangeCountryFlTgCountrySelector(countryId, uniqueId, fieldName, className, showSubselects) {

  // Control argument showSubselects
  if (arguments.length < 5) var showSubselects = 1;

  if (fieldName == 'country' && settings.forceBillingAddressCountry && $('#useShippingAddress').prop('checked') == true && $('#userShippingCountryField')) {
    if ($('#userFieldShippingCountryContainer .countriesSelector')) {
      $('#userShippingCountryField').find('option').remove().end().append($('<option>', {value:countryId}).text($("#userCountryField option[value='"+countryId+"']").text()));
      loadSubcountriesFlTgCountrySelector(countryId, 0, $('#userShippingCountryField').attr('data-field'), 'shippingCountry', 'userField');      
    }
  }
  else {
    loadSubcountriesFlTgCountrySelector(countryId, 0, uniqueId, fieldName, className, 0, showSubselects);
  }
}

function loadSubcountriesFlTgCountrySelector(countryId, subcountryId, uniqueId, fieldName, className, storedCountryId, showSubselects) {
  var $container = $('#countriesSelectorContainer_'+uniqueId);

  // Control argument showSubselects
  if (arguments.length < 6) var showSubselects = 1;

  if(subcountryId.length == 0) {
    $container.html("");
    localizeCallbacks(fieldName);
    return;
  }
  
  if(subcountryId == 0 && countryId.length == 0) {
    $container.html("");
    $('#subcountriesSearch_'+uniqueId).html("");
    localizeCallbacks(fieldName);
    return;
  }

  if (fieldName == 'country' && settings.forceBillingAddressCountry && $('#useShippingAddress').prop('checked') == true && $('#userShippingCountryField')) {
    if ($('#userFieldShippingCountryContainer .countriesSelector')) {
      $('#userShippingCountryField').find('option').remove().end().append($('<option>', {value:countryId}).text($("#userCountryField option[value='"+countryId+"']").text()));
      loadSubcountriesFlTgCountrySelector(countryId, 0, $('#userShippingCountryField').attr('data-field'), 'shippingCountry', 'userField');      
    }
  }

  $container.closest('div.userFormFields').find('.countryManualInputFields').remove();
  $container.html('loading...<input type="text" class="required" value="" style="border:none; width:1px; height:1px; display:inline; padding:0px; margin:0px;" />');

  var parameterEncoder = new urlParameterEncoder()
    .addParameter('countryId',countryId)
    .addParameter('subcountryId',subcountryId)
    .addParameter('fieldName',fieldName)
    .addParameter('className',className)
    .addParameter('uniqueId', uniqueId)
    .addParameter('onlyNextLevel', true)
    .addParameter('showSubselects', showSubselects)
    .addParameter('htmlFramework', window.htmlFramework);

  $container.load('/user/countrySelectorSubcountriesSelector/' + parameterEncoder.getParameters(), function(){
    validateCountryFields(fieldName);
    localizeCallbacks(fieldName);
  });

  if(!subcountryId) {
    parameterEncoder
      .addParameter('itemId',countryId)
      .addParameter('onlyNextLevel', true);

    $('#subcountriesSearch_'+uniqueId).html('').load('/user/countrySelectorSubcountriesSearch/' + parameterEncoder.getParameters());
  }
};

function onFocusNoSelectZip(input) {
  $(input).closest('div.subcountrySearchField').removeClass('has-error');
}
function onBlurNoSelectZip(input) {
  $(input).val('');
  if(!$(input).closest('.addressUserField').hasClass('address-complete')) {
    $(input).closest('div.subcountrySearchField').addClass('has-error');
  }
}
$('.countrySelectMode a[href*="#countrySuggestTab"]').on('hide.bs.tab', function(){
  var id = $(this).attr('href');
  $(id).find('div.subcountrySearchField').removeClass('has-error');
});
$('.addressUserField:visible').not('.address-complete').each(function(index, el) {
    var $selectCountry = $(el).find('.__selectCountry__');
    if ($selectCountry.length && $selectCountry.val().length > 0) {
        if (parseInt($(el).find('[id^="countrySuggestTab"] [type="hidden"][name$="ountry"]').val()) === -1) {
            $(el).find('[href^="#countryManualTab"]').hide().tab('show');
            $(el).find('[href^="#countrySuggestTab"]').hide();
        }
    }
});
