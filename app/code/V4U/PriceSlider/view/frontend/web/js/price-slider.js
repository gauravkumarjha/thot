define(['jquery', 'jquery/ui'], function ($) {
    "use strict";
    return function (config) {
        $("#price-slider-range").slider({
            range: true,
            min: 0,
            max: 5000,
            values: [100, 1000],
            slide: function (event, ui) {
                $("#amount-min").text(ui.values[0]);
                $("#amount-max").text(ui.values[1]);
                $("#min_price").val(ui.values[0]);
                $("#max_price").val(ui.values[1]);
            }
        });
    };
});
