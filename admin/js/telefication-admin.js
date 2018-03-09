(function ($) {

    /**
     * Sending test message button functionality
     */
    $('#test_message').on('click', function () {

        var chat_id = $('#chat_id').val();

        $.ajax({
            url: telefication.ajax_url,
            data: {action: 'telefication_test_message', chat_id: chat_id, message: telefication.test_message},
            dataType: 'text',
            success: function (data) {
                alert(data);
            },
            error: function () {
                alert(telefication.error_occurred);
            }
        });
    });

})(jQuery);