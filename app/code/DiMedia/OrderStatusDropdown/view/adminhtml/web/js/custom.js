require(['jquery'], function ($) {
    $(document).ready(function () {

        
        $('.order-history-comments-actions button').on('click', function (event) {
                setTimeout(function () {
                    location.reload();
                }, 2000); // Refresh after 1 second
            });
       

        var allowedTransitions = {
            'pending': ['processing', 'canceled'],
            'processing': ['shipped', 'partially_shipped'],
            'partially_shipped': ['processing','shipped'],
            'shipped': ['delivered', 'partially_delivered', 'returned'],
            'partially_delivered': ['shipped', 'delivered', 'returned'],
            'delivered': ['returned', 'partially_returned'],
            'partially_returned': ['returned', 'delivered'],
            'returned': ['replaced', 'refunded'],
            'refunded': ['replaced', 'returned'],
            'replaced': ['refunded', 'returned'],
            'canceled': ['canceled'],
        };
     

        var currentStatus = $('#history_status').val();
        function capitalizeFirstLetter(str) {
            str = str.replace(/_/g, ' '); // Remove underscores first
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function updateStatusOptions() {
            var options = $('#history_status option');
            options.each(function () {
                var optionValue = $(this).val();
                if (allowedTransitions[currentStatus] && allowedTransitions[currentStatus].indexOf(optionValue) === -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
            var addoptions = '';
            var options2 = $('#history_status').val(); // Get the selected value of #history_status

            if (allowedTransitions[options2]) {
                // Add the current status as the first option
                addoptions += "<option value='" + options2 + "'>" + capitalizeFirstLetter(options2) + "</option>";

                // Iterate over the allowed transitions for the selected status
                $.each(allowedTransitions[options2], function (index, val) {
                    addoptions += "<option value='" + val + "'>" + capitalizeFirstLetter(val) + "</option>";
                });

                // Update the #history_status dropdown with the new options
                $('#history_status').html(addoptions);
            }

           

        }

        /* $('#history_status').change(function () {
        //     currentStatus = $(this).val();
        //     updateStatusOptions();
            
         });*/

        updateStatusOptions();
    });
});
