;(function( $ ){
    $( '#mailchimp_signup form' ).on( 'submit', function( e ) {
        e.preventDefault();

        let data = $(this).serialize();

        $('.response-message').html('');

        data += '&action=' + mailchimpdata.action;
        data += '&nonce=' + mailchimpdata.nonce;
        
        $.post( mailchimpdata.ajaxurl, data )
        .done( function( response ) {
            
            if ('success' === response.data.status) {
				$('.response-message').html('Thanks for your subscription');
			} 
            
            else {
				$('.response-message').html(response.data.message);
			}
        } )
        .fail(function(error){
			$('.response-message').html('Something went wrong');
		});
        
    } );

})(jQuery);