<?php
namespace UserSubsCollection;

/**
 * Ajax handler class
 *
 * @since 1.0.0
 */
class Ajax {
    public function __construct() {
       add_action( 'wp_ajax_mailchimp-subsform-action', [ $this, 'collect_subscribers_email' ] );
    }

    /**
     * collect email
     *
     * @return void
     */
    public function collect_subscribers_email() {
        $api            = get_option( 'api_field_data' );
        $widget_values  = get_option( 'widget_mailchimpwidget' );
        $list           = $widget_values[2]['subscriber_list'];
        $nonce          = isset( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : '';
        $email          = isset( $_REQUEST['email'] ) ? sanitize_email( $_REQUEST['email'] ) : '';
		$status         = 'subscribed';
		$email          = strtolower( $email );
		$dc             = substr( $api, strpos( $api, '-' ) + 1 );

        if ( '' === $email ) {
			wp_send_json_error( [
				'status'  => 'error',
				'message' => __( 'Email Not found!', 'weSubs' ),
			] );
		}

        if ( '' === $nonce || ! wp_verify_nonce( $nonce, 'mailchimp-subsform-action' ) ) {
			wp_send_json_error( [
				'status'  => 'error',
				'message' => __( 'Request unauthorized!', 'weSubs' ),
			] );
		}

        $args = [
			'method' => 'PUT',
		 	'headers' => [
				'Authorization' => 'Basic ' . base64_encode( 'user:'. $api )
			],
			'body' => json_encode( [
				'email_address' => $email,
				'status'        => $status
			] )
		];

       $response = wp_remote_post( 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $list . '/members/' . $email, $args );

       $body = json_decode( $response['body'] );
		 
		if ( $response['response']['code'] == 200 && $body->status == $status ) {
			wp_send_json_success( [
				'status'  => 'success',
				'message' => __( 'Request successful', 'weSubs' ),
			] );
		} 
		
		else {
			wp_send_json_error( [
				'status'  => 'error',
				'message' => __( $body->detail, 'weSubs' ),
			] );
		}
    }
}
