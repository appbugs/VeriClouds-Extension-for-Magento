// check customer is compromised while login
jQuery(function () {

    jQuery("#login-form").submit(function( event ) {
        event.preventDefault();
        var self = this;
         jQuery.ajax({
                type: "POST",
                url: jQuery('#secureaccount-api-url').html(),
                data: jQuery("#login-form").serialize(),
                success: function (response) {
                    self.submit();
                }
            });
    });
});