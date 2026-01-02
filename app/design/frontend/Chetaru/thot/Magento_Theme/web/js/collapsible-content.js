require(['jquery'], function($){ 
  "use strict";
   $(document).ready(function($)
 {
  $('.default_open').addClass('active');
      $('.default_open').next().slideToggle('fast');
      
    $('.accordion .collapsible').click(function(){

      var isActive = $(this).hasClass("active");
            $('.accordion .collapsible').removeClass('active')
      if (!isActive) {
        $(this).toggleClass('active');
      }
         
      $(this).next().slideToggle('fast');     

      $(".accordion .content").not($(this).next()).slideUp('fast');

    });
  });
});
