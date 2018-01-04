(function($) {

Drupal.behaviors.image_caption = {
  attach: function (context, settings) {
    $("img.caption:not(.caption-processed)").each(function(i) {
      var imgwidth = $(this).width() ? $(this).width() : false;
      var imgheight = $(this).height() ? $(this).height() : false;
      var imgmargin = $(this).css('margin');
		  var imgpadding = $(this).css('padding');

      // Get caption from title attribute.
      var captiontext = $(this).attr('title');

      // Get image alignment and style to apply to container.
      if (captiontext) {
        if($(this).attr('align')){
          var alignment = $(this).attr('align');
          $(this).css({'float':alignment}); // add to css float
          $(this).removeAttr('align');
        }else if($(this).css('float')){
          var alignment = $(this).css('float');
        }else{
          var alignment = 'normal';
        }
        var style = $(this).attr('style') ? $(this).attr('style') : '';

        // Heights are removed from the style variable here.
        var style = style.replace(/height.*; /, '')

        // Reset img styles as are added to container instead.
        $(this).removeAttr('width');
        $(this).removeAttr('height');
        $(this).css('width', '');
        $(this).css('height', '');
        $(this).removeAttr('align');
        $(this).removeAttr('style');

        // Display inline block as figure element.
        $(this).wrap("<figure class=\"image-caption-container\" style=\"display:inline-block;" + style + "\"></figure>");
        $(this).parent().addClass('image-caption-container-' + alignment);

        // Add dimensions, if available.
        if(imgwidth){
          $(this).width(imgwidth);
          $(this).parent().width(imgwidth);
        }
        if(imgheight){
          $(this).height(imgheight);
        }
        if(imgmargin){
		      $(this).parent().css('margin', imgmargin);
		    }
		    if(imgpadding){
		      $(this).parent().css('padding', imgpadding);
		    }

        // Append caption as a figcaption element.
        $(this).parent().append("<figcaption style=\"display:block;\" class=\"image-caption\">" + captiontext + "</figcaption>");

        // Resize the height of the parent container after the caption is added
	      $(this).parent().css('height', $(this).height() + $(this).next().height() );

        // Add "caption-processed" class to prevent duplicate caption adding.
        $(this).addClass('caption-processed');

      }
    });
  }
};

})(jQuery);

