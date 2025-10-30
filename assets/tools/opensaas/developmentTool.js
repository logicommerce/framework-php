LC.initQueue.enqueue(function(){
  var developmentTool = $('#development-tool'); 
  var developmentBox = $('#development-tool .development-box');
  var developmentItem = $('#development-tool .developmentItem');
  var developmentToggle = $('#development-tool .development-toggle');

  var boxTitle = developmentBox.find('.tool-header-title');
  var boxClose = developmentBox.find('.tool-header-close');
  var boxContent = developmentBox.find('.tool-body');

  developmentToggle.click(function(){
    if (developmentTool.hasClass('closed')){
      developmentTool.removeClass('closed');
    }
    else {
      developmentTool.addClass('closed');
    }
  });

  boxClose.click(function(){
    developmentBox.hide();
  });

  developmentItem.click(function(event){
    var element = $(this);

    // Clean
    developmentBox.find('.tool-body-content').hide();

    // Close
    if (element.hasClass('active')) {
      developmentBox.hide();
      element.removeClass('active');
      return
    }

    developmentItem.removeClass('active');
    element.addClass('active');

    boxTitle.text(element.data('title'));
    developmentBox.find('.tool-body-content.' + element.data('type')).show();
    developmentBox.show();
  });

})