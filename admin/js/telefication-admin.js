(
	function( $ ) {

		/**
		 * Sending test message button functionality
		 */
		$( '#test_message' ).on( 'click', function() {

			var chat_id = $( '#chat_id' ).val();

			$.ajax( {
				url: telefication.ajax_url,
				data: {action: 'telefication_test_message', chat_id: chat_id, message: telefication.test_message},
				dataType: 'text',
				success: function( data ) {
					alert( data );
				},
				error: function() {
					alert( telefication.error_occurred );
				},
			} );
		} );

		/**
		 * Get chat Id button functionality
		 */
		$( '#get_chat_id' ).on( 'click', function() {

			var bot_token = $( '#bot_token' ).val();
			if ( bot_token == '' ) {
				alert( telefication.bot_token_is_empty );
			} else {

				$.ajax( {
					url: telefication.ajax_url,
					data: {action: 'telefication_get_chat_id', bot_token: bot_token},
					dataType: 'text',
					success: function( data ) {
						alert( data );
						if ( ! isNaN( data ) ) {
							$( '#chat_id' ).val( data );
						}
					},
					error: function() {
						alert( telefication.error_occurred );
					},
				} );
			}
		} );

	}
)( jQuery );