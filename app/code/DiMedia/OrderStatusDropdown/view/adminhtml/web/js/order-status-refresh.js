require(['jquery', 'mage/mage'], function ($) {
    $(document).ready(function () {
        $('#history_form').on('submit', function (event) {
            setTimeout(function () {
                location.reload();
            }, 1000); // Refresh after 1 second
        });
    });
});
