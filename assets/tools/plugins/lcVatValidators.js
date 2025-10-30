/**
 * jQuery Form Validator Module: VAT
 * ------------------------------------------------
 * Created by Logicommerce
 * Version: 0.2 - 2017-06-19
 * Version: 0.1 - 2016-06-06
 *
 * Adds validators for VATs:
 * - vatES (Spain)
 *
 */
(function ($) {

  'use strict';
  /**
   * Basic VAT module
   */
  $.formUtils.addValidator({
    name: 'vat',
    validatorFunction: function(value, $elem, conf, language, $form){
      var validationTypes = $elem.data('validationTypes');
      var $country = $form.find("select.__selectCountry__");
      var countryId = $country.length? $country.val() : LC.global.session.countryId;

      // Without countryId we can not validate
      if (!countryId && validationTypes.hasOwnProperty(countryId) && $.formUtils.vatValidators.hasOwnProperty(validationTypes[countryId])) {
        return true;
      }
      return $.formUtils.vatValidators[validationTypes[countryId]](value, $elem, conf, language, $form);
    },
    errorMessage : '',
    errorMessageKey: 'badVat'
  });

  /**
   * Basic IDCard module
   */
  $.formUtils.addValidator({
    name: 'idcard',
    validatorFunction: function(value, $elem, conf, language, $form){
      var validationTypes = $elem.data('validationTypes');
      var $country = $form.find("select.__selectCountry__");
      var countryId = $country.length? $country.val() : LC.global.session.countryId;

      // Without countryId we can not validate
      if (!countryId || !validationTypes.hasOwnProperty(countryId) || !$.formUtils.idCardValidators.hasOwnProperty(validationTypes[countryId])) {
        return true;
      }
      return $.formUtils.idCardValidators[validationTypes[countryId]](value, $elem, conf, language, $form);
    },
    errorMessage : '',
    errorMessageKey: 'badIdCard'
  });


  /**
   * idCard modules
   */
  $.formUtils.idCardValidators = {
    "es" : function(value, $elem, conf, language, $form) {
      if (value.length)
        return validateNIF(value); // Allow empty values
      else
        return true;

      function validateNIF(value){
        var lettersList='TRWAGMYFPDXBNJZSQVHLCKE';

        value = value.toUpperCase(); 

        // Prepare Value
        var firstChar = value.substr(0,1);

        if (/^[X]{1}/.test( firstChar ))
           value = value.replace('X','0')
        if (/^[Y]{1}/.test( firstChar ))
           value = value.replace('Y','1')
        if (/^[Z]{1}/.test( firstChar ))
           value = value.replace('Z','2')

        var modNum = value.substr(0,value.length-1) % 23;

        // Validate Format
        if (!/\d\d\d\d\d\d\d\d[A-Z]$/g.exec(value)) 
          return false;

        // Get Letter 
        var letter = value.substr(value.length-1, 1);

        return lettersList.substring(modNum, modNum+1) == letter;
      }
    }
  };

  /**
   * VAT modules
   */
  $.formUtils.vatValidators = {
    "es" : function(value, $elem, conf, language, $form) {
      if (value.length)
        return validateCIF(value); // Allow empty values
      else
        return true;

      function validateCIF(cif) {
        if (!cif || cif.length !== 9) {
          return false;
        }

        var letters = ['J', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        var digits = cif.substr(1, cif.length - 2);
        var letter = cif.substr(0, 1);
        var control = cif.substr(cif.length - 1);
        var sum = 0;
        var i;
        var digit;

        if (!letter.match(/[ABCDEFGHJNPQRSUVW]/)) {
          if (letter.match(/[XYZ]/) || letter.match(/[0-9]/))
            return $.formUtils.idCardValidators.es(cif);
          else
            return false;
        }

        for (i = 0; i < digits.length; ++i) {
          digit = parseInt(digits[i]);

          if (isNaN(digit))
            return false;

          if (i % 2 === 0) {
            digit *= 2;
            if (digit > 9)
              digit = parseInt(digit / 10) + (digit % 10);
            sum += digit;
          } else
            sum += digit;
        }
        sum %= 10;
        if (sum !== 0)
          digit = 10 - sum;
        else
          digit = sum;

        if (letter.match(/[ABEH]/))
          return String(digit) === control;
        
        if (letter.match(/[NPQRSW]/))
          return letters[digit] === control;                

        return String(digit) === control || letters[digit] === control;
      }

      function validatePassport(value){
        if (value.length < 29) 
          return false;

        var passport_no = value.substr(0,9);
        var passport_no_cs = value.substr(9,1);
        var date_of_birth = value.substr(13,6);
        var date_of_birth_cs = value.substr(19,1);
        var date_of_expiry = value.substr(21,6);
        var date_of_expiry_cs = value.substr(27,1);
        var all_numbers =  passport_no + passport_no_cs + date_of_birth + date_of_birth_cs + date_of_expiry + date_of_expiry_cs;
        var all_numbers_cs = value.substr(value.length-1,1);

        if (checkSum(passport_no) - passport_no_cs != 0) 
          return false;
        if (checkSum(date_of_birth) - date_of_birth_cs != 0) 
          return false;
        if (checkSum(date_of_expiry) - date_of_expiry_cs != 0) 
          return false;
        if (checkSum(all_numbers) - all_numbers_cs != 0) 
          return false;

        return true;
      }

      function checkSum(s) {
        var c, i, sum = 0;
        var multiplikator = [7,3,1];
        var mult = 0;

        for(i=0; i<s.length; i++) {
          c = s.substr(i,1);
          if (isNaN(c)) return -1;
          sum += c * multiplikator[mult];
          mult = ++mult % 3;
        }
        return sum % 10;
      }
    }
  };
     
})(jQuery);
