
// customer reset password 
jQuery(function () {
    jQuery('#customer-reset-password').validate({
        rules: {
            newpassword: "required",
            confrmpassword: {
                equalTo: "#newpassword"
            }
        },
        submitHandler: function (form) {
            jQuery.ajax({
                type: "POST",
                url: jQuery("#customer-reset-password").attr('action'),
                data: jQuery("#customer-reset-password").serialize(),
                cache: false,
                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    window.location.href = obj.redirect;
                }
            });
        },
        messages: {
            newpassword: {
                required: 'This is a required field',
            },
            confrmpassword: {
                required: 'This is a required field',
            }
        }
    });

});