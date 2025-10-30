/**
 * Resources
 * Logicommerce Ecommerce http://logicommerce.com/
 *
 * Copyright (C) Logicommerce Ecommerce S.L.
 * Licence: Proprietary
 *
 * Requires: jQuery.js
 *
 * Date: 2021-11-11
 */

/**
 * Search a object position from an Array by abject key and its value
 * @param  {string} key   Object's key
 * @param  {string} value Value of key
 * @return {integer}      position in array.
 */
Array.prototype.objectSearch = function (key, value) {
    for (var i = 0; i < this.length; i++) {
        try {
            if (typeof this[i] === 'object' && this[i][key] == value) {
                return i;
            }
        }
        catch (e) {
            // If the object does not have the key, it will throw an error
        }
    }
    return -1;
};

function readCookie(k) {
    return (document.cookie.match('(^|; )' + k + '=([^;]*)') || 0)[2];
}

function writeCookie(name, value) {
    const fecha = new Date();
    fecha.setTime(fecha.getTime() + (2 * 60 * 60 * 1000));
    const expiracion = "expires=" + fecha.toUTCString();
    document.cookie = `${name}=${value}; ${expiracion}; path=/`;
}

/**
 * Shortcut getElementBy Id for DOM only on document
 * @param {string} a id
 */
window.E = function (a) {
    return document.getElementById(a);
};

/**
 * Advanced query Selector.
 * Allows search multiple items and restrict for specified element on DOM.
 *
 * @params: Allows 3 params. See examples
 *
 * @examples *
 * F('span');                   // Returns one (first) span on document
 * F('span', true);             // Returns NodeLsit of all span on document
 * F('span', container);        // Returns one (first) span on container
 * F('span', container, true);  // Returns NodeList of span on container
 */
window.F = function () {
    var qry = arguments[0] || '';
    var all = false;
    var domE = document;

    if (!qry) return null;

    if (arguments.length == 2 && typeof arguments[1] === 'boolean') all = arguments[1];
    else if (arguments.length == 2 && typeof arguments[1] === 'object') domE = arguments[1];
    else if (arguments.length == 3) {
        domE = arguments[1];
        all = arguments[2];
    }

    return domE[all ? 'querySelectorAll' : 'querySelector'](qry);
};

/**
 * Shortcut for console.log
 */
window.log = window.console ? console.log : function () { };

/**
 * Detects bootstrap
 */
window.hasBootstrap = function () {
    return typeof $().modal === 'function';
};

/**
 * @class Params encoder for URL
 *
 * @method addParameter, add parameter to object
 * @method getParameters, return encoded parameters
 * @method clear, clean paramters
 *
 * @property {array} parameters
 */
window.urlParameterEncoder = function () {
    this.addParameter = function (a, b) {
        this.parameters.push(a + '=' + b);
        return this;
    };
    this.getParameters = function (c) {
        return (!c || c.toUpperCase() != 'POST' ? '?' : '') + this.parameters.join('&');
    };
    this.clear = function () {
        this.parameters = [];
    };
    this.clear();
};

/**
 * Number.prototype.formatAsPrice();
 */
Number.prototype.formatAsPrice = function () {
    var number = this,
        locale = LC.global.session.locale.replace('_', '-'),
        intPrice = '',
        minimumFractionDigits = LC.global.settings.currencyFormat.minDecimalsLength != undefined ? LC.global.settings.currencyFormat.minDecimalsLength : 2,
        maximumFractionDigits = LC.global.settings.currencyFormat.maxDecimalsLength != undefined ? LC.global.settings.currencyFormat.maxDecimalsLength : 2,
        numberFormat = new Intl.NumberFormat(locale, { style: 'currency', currency: LC.global.session.currencyId, minimumFractionDigits: minimumFractionDigits, maximumFractionDigits: maximumFractionDigits }),
        formatToParts = numberFormat.formatToParts(number),
        formattedIntPrice = '',
        decimalPrice = '',
        formattedDecimalPrice = '',
        currencySymbol = '',
        currencyFirst = formatToParts[formatToParts.length - 1].type === 'currency' ? false : true;

    for (let i = 0; i < formatToParts.length; i++) {
        var type = formatToParts[i].type,
            value = formatToParts[i].value;

        if (type === 'integer') {
            intPrice += value;
            //fix spanish thousands separator for <10.000
            if (locale == 'es-ES' && value >= 1000 && value < 10000) {
                value = value.substring(0, 1) + '.' + value.substring(1);
            }
            formattedIntPrice += value;
        }
        if (type === 'group') {
            formattedIntPrice += value;
        }
        if (type === 'decimal' && maximumFractionDigits > 0) {
            decimalPrice += '.';
            formattedDecimalPrice += value;
        }
        if (type === 'fraction' && maximumFractionDigits > 0) {
            decimalPrice += value;
            formattedDecimalPrice += value;
        }
        if (type === 'currency') {
            currencySymbol += value;
        }
    }

    if (LC.global.settings.currencies[LC.global.session.currencyId] != undefined) {
        currencySymbol = LC.global.settings.currencies[LC.global.session.currencyId].symbol;
    }

    var output = '<span class="price">';
    output += currencyFirst ? '<span class="currencySymbol">' + currencySymbol + '</span>' : '';
    output += '<span class="integerPrice" content="' + intPrice + decimalPrice + '">' + formattedIntPrice + '</span>';
    if (maximumFractionDigits > 0) {
        output += '<span class="decimalPrice">' + formattedDecimalPrice + '</span>';
    }
    output += !currencyFirst ? '<span class="currencySymbol">' + currencySymbol + '</span>' : '';
    output += '</span>';

    return output;
};

/**
 * Return locale price with html
 * @param  {float} value
 */
window.outputHtmlCurrency = function (value) {
    if (isNaN(value)) {
        value = 0;
    }
    return value.formatAsPrice();
};

Object.getLength = function (obj) {
    return Object.keys(obj).length;
};

/**
 * Get style on DOM elements
 * @param  {String} style    Style property
 * @param  {object} elements Optional, default: ALL
 * @return {array}           Array of values from style property on elements.
 */
window.getStyles = function (style, elements) {
    if (!elements) elements = F('*', true);
    var result = [];
    for (var i = 0; i < elements.length; i++) {
        var $el = $(elements[i]);
        if ($el.css(style)) result.push($el.css(style));
    }
    return result;
};

/**
 * Load web fonrs
 * @param  {string} basePath base path to fonts
 * @deprecated
 */
window.loadWebFonts = function (basePath) {
    // Get all font-family parameters for all elements in the document and init other stuff
    var allFonts = getStyles('font-family'),
        baseFonts = ['monospace', 'sans-serif', 'serif', 'cursive', 'helvetica neue', 'fontawesome'],
        webFonts = [],
        cssCode = '';

    // Create an array of unique font families
    allFonts.forEach(function (fonts) {
        fonts.split(',').forEach(function (font) {
            font = font
                .trim()
                .replace(/'|"/g, '')
                .toLowerCase();

            if (webFonts.indexOf(font) == -1 && baseFonts.indexOf(font) == -1) webFonts.push(font);
        });
    });

    if (webFonts.length) {
        // Define font types depending on the client browser
        if (navigator.userAgent.indexOf('Safari') > 0) {
            var extension = '.ttf',
                format = 'truetype';
        } else if (navigator.userAgent.indexOf('MSIE') > 0) {
            var extension = '.eot',
                format = 'eot';
        } else {
            var extension = '.woff',
                format = 'woff';
        }

        /* Init font detector by Lalit Patel (http://www.lalit.org/lab/javascript-css-font-detect) */
        var testString = 'mmmmmmmmmmlli',
            testSize = '72px',
            defaultWidth = {},
            defaultHeight = {},
            h = document.getElementsByTagName('body')[0],
            s = document.createElement('span');

        s.style.visibility = 'hidden';
        s.style.fontSize = testSize;
        s.innerHTML = testString;

        for (var index in baseFonts) {
            s.style.fontFamily = baseFonts[index];
            h.appendChild(s);
            defaultWidth[baseFonts[index]] = s.offsetWidth;
            defaultHeight[baseFonts[index]] = s.offsetHeight;
            h.removeChild(s);
        }

        detectFont = function (font) {
            var detected = false;

            for (var index in baseFonts) {
                s.style.fontFamily = font + ',' + baseFonts[index];
                h.appendChild(s);
                var matched =
                    s.offsetWidth != defaultWidth[baseFonts[index]] ||
                    s.offsetHeight != defaultHeight[baseFonts[index]];
                h.removeChild(s);
                detected = detected || matched;
            }

            return detected;
        };
        /* End font detector by Lalit Patel */

        // Create @font-face declarations for every unique font not detected on the client browser/system
        webFonts.forEach(function (font) {
            if (!detectFont(font))
                cssCode +=
                    '@font-face { font-family:"' +
                    font +
                    '"; src:url("' +
                    basePath +
                    font.replace(/ /g, '-') +
                    extension +
                    '") format("' +
                    format +
                    '"); font-style: normal; font-weight: normal; }\n';
        });

        // Finally, if there's any new @font-face declarations add them to the document
        if (cssCode.length) {
            if (navigator.userAgent.indexOf('Chrome') > 0) cssCode = '@media screen { ' + cssCode + ' }';

            var head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');

            style.type = 'text/css';
            head.appendChild(style);

            if (style.styleSheet)
                // Workaround for IE
                style.styleSheet.cssText = cssCode;
            else style.appendChild(document.createTextNode(cssCode));
        }
    }
};

//determines if a variable is defined an not null
$.defined = function (a) {
    if (typeof a === 'undefined') return false;
    if (a == null) return false;
    return true;
};
