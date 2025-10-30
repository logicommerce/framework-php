/**
 * Initialize forms and validators
 * @param  {object} wrap html node object
 */
LC.initializeForms = function (wrap) {
    const container = wrap ?? document;

    // Enabled all submit buttons
    const submitButtons = document.querySelectorAll('button[type=submit]:not(.initializeDisabled)');
    if (submitButtons) {
        for (let i = 0; i < submitButtons.length; i++) {
            const el = submitButtons[i];
            el.disabled = false;
            el.removeAttribute("disabled");
        }
    }

    // Initialize Forms
    const forms = container.querySelectorAll('form:not([data-lc-form-ignore])');
    if (!forms) return;

    for (let i = 0; i < forms.length; i++) {
        const form = forms[i];

        if (form.initialized) continue;

        const dataLCForm = form.dataset.lcForm;
        if (dataLCForm) {
            if (dataLCForm === 'oneStepCheckout') {
                lcOneStepCheckout = new LC.OneStepCheckout(form);
                continue;
            }
            const dataLCFormClass = dataLCForm.charAt(0).toUpperCase() + dataLCForm.slice(1);
            if (LC[dataLCFormClass]) {
                new LC[dataLCFormClass](form);
                continue;
            }
        }
        const modal = form.closest('.modal');
        if (!modal || (modal && !modal.dataset.lcModalCallback)) {
            new LC.Form(form);
        }
    }
};

/**
 * This functions initialize LC components
 * @memberOf LC
 */
LC.initQueue.enqueue(function () {
    // Initialize Form Validator - https://igloczek.github.io/formvalidator-net-mirror/
    $.validate(LC.validateFormConf);

    // Initialize click and view object counter
    // LC.initializeIncrementCounter();

    // Initialize countdowns before forms. buyForm requires this
    LC.initializeCountdowns();

    LC.basketExpiration.init();

    LC.warnings.show();

    LC.initializeForms();

    LC.initializeRangeSliders();

    // Initialize Quantity Fields
    $('[data-lc-quantity]').quantity();

    // Initialize Data events
    $('[data-lc-event]').dataEvent();

    // TODO: no implemented yet
    // $('[data-lc-shippingcalculator]').each(function (index, el) {
    //     new LC.shippingPrices(el);
    // });

    LC.initializeModals();
});
