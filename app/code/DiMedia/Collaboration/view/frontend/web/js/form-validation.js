define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function () {
        $('#collaborationForm').submit(function (event) {
            // Get values
            var name = $('#name').val();
            var email = $('#email').val();
            var phone = $('#phone').val();
            var message = $('#message').val();

            // Basic validation: ensure all fields are filled
            if (!name || !email || !phone || !message) {
                event.preventDefault(); // Prevent form submission if validation fails
                alert($t('Please fill in all the required fields.'));
                return false;
            }

            // Email validation
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zAZ]{2,6}$/;
            if (!email.match(emailPattern)) {
                event.preventDefault();
                alert($t('Please enter a valid email address.'));
                return false;
            }

            // Form is valid, proceed with submission
            return true;
        });
    };
});
