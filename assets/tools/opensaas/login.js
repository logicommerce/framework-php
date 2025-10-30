window.addEvent('load', initLoginForm);

function initLoginForm(){
  var form = $('loginForm');
  form.validation = new Form.Validator(form, {onFormValidate: submitForm});

  form.getElements('input[placeholder]').each(
    function(el){new LoginField(el);}
  );

  form.username.focus();

  (function(){
    if(form.UserName.value.length)
      form.Password._loginFieldObject.label.addClass('up');
  }).delay(50);
}

function submitForm(isValid, form, submitEvent){
  if(!isValid) {
    if (form.username.hasClass('validation-failed')){
      form.username.parentElement.addClass('error');
      form.username.focus();
    }
    else if (form.password.hasClass('validation-failed')){
      form.password.parentElement.addClass('error');
      form.password.focus();
    }
    return;
  }

  form.submit();
  return;
}

LoginField = new Class({
  initialize:function(field)
  {
    field._loginFieldObject = this;
    this.field = field;
    this.container = field.getParent();

    this.onBlur = this.onBlur.bind(this);
    this.onFocus = this.onFocus.bind(this);

    var fieldId = field.getAttribute('id');
    if(!fieldId)
    {
      fieldId = field.name + getUniqueId();
      field.setAttribute('id', fieldId);
    }

    this.label = new Element('label', {'for':fieldId, 'class':'up'} ).inject(this.container, 'top');
    this.label.innerHTML = field.getAttribute('placeholder');
    this.field.removeAttribute('placeholder');

    field.addEvent('blur', this.onBlur);
    field.addEvent('focus', this.onFocus);

    this.onBlur();
    
  },
  onFocus:function()
  {
    this.label.addClass('up');
    this.container.addClass('focused');
  },
  onBlur:function()
  {
    this.container.removeClass('focused');
    this.container.removeClass('error');
    if(!this.field.value.length) {
      this.label.removeClass('up');
    }
    else{
      this.label.addClass('up');
    }
      
  }
});