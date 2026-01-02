require(['jquery'], function($){
  "use strict";
   $(document).ready(function($)
 {
  // For sticky header
      $(window).scroll(function() {    
      var scroll = $(window).scrollTop();    
      if ($(document).scrollTop() > 50) {
        $("body").addClass("header-fixed");
      } else {
        $("body").removeClass("header-fixed");
      }
    });

  
    var base_url = window.location.origin;

    jQuery("body").on("blur	", '#customer-email, input[name="telephone"], input[name="firstname"], input[name="lastname"]', function (e) {
      var customerName = "";
      if (typeof jQuery("input[name='firstname']").val() !== "undefined") {
        customerName += jQuery("input[name='firstname']").val();
      }

      if (typeof jQuery("input[name='lastname']").val() !== "undefined") {
        customerName += " " + jQuery("input[name='lastname']").val();
      }

      //return false;
      //console.log('email: ' + jQuery("#customer-email").val() + ', telephone: ' + jQuery("input[name='telephone']").val());
      jQuery.ajax({
        url: "/utility/checkout/abandoned",
        type: "POST",
        data: { email: jQuery("#customer-email").val(), telephone: jQuery("input[name='telephone']").val(), customerName: customerName },
        success: function (data, textStatus, jqXHR) { },
      });
    });
  }); //Document Ready End
});