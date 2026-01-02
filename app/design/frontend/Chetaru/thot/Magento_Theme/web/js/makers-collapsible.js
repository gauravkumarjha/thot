require(['jquery'], function($){ 
  "use strict";
   jQuery(document).ready(function(){  
    jQuery(".panel-title").click(function(){  
        jQuery(".nav").slideToggle("hide");  
    });  
}); 
});