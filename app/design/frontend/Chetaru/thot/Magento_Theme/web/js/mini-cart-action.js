define([
    'jquery'
], function ($) {
    return function () {
        $('[data-block="minicart"]').on('contentLoading', function () {
            $('[data-block="minicart"]').on('contentUpdated', function () {
                $('html, body').animate({ scrollTop: 0 }, '4000');
                $('[data-block="minicart"]').find('[data-role="dropdownDialog"]').dropdownDialog("open");
            });
        });
    }
});