<?php
namespace UserSubsCollection;
use WP_Widget;

/**
 * Class initiated for widget
 *
 * @since 1.0.0
 */
class Mailchimp_Subs extends WP_Widget {
    public function __construct() {
        
        parent::__construct(
            'mailchimpwidget',
            __( 'MailChimp Widget', 'weSubs' )
        );

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Send to data to js
     *
     * @return void
     */

    public function enqueue_assets() {
        wp_enqueue_script( 'subs-form-handle' );

        wp_localize_script( 
            'subs-form-handle',
            'mailchimpdata',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'error'   => __( 'something went wrong', 'weSubs' ),
                'success' => __( 'successfully submitted', 'weSubs' ),
                'action'  => 'mailchimp-subsform-action',
                'nonce'   => wp_create_nonce( 'mailchimp-subsform-action' ),
            ) 
         );
    }

    /**
     * Added function to display on Frontend
     *
     * @since 1.0.0
     *
     * @param array $args
     *
     * @return array $instance
     */

    public $args = array(
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
        'before_widget' => '<div class="widget-wrap">',
        'after_widget'  => '</div></div>'
    );
    
    public function widget( $args, $instance ) {
        extract( $args );

        $title = apply_filters( 'widget_title', $instance['title'] );
        
        echo wp_kses_post( $before_widget );

            if( ! empty( $title ) ) {
                echo wp_kses_post( $before_title ) .  esc_html( $title ) . wp_kses_post( $after_title );
            }
            else{
                esc_html_e( 'Mailchimp Widget Title', 'weSubs' );
            }
?>
        <div id="mailchimp_signup">
            <form method="post">
				<p>
					<label>
						<input style="width: 100%;" type="email" name="email" placeholder="<?php  esc_html_e( 'Enter email address' , 'weSubs' ); ?>" required />
					</label>
				</p>

				<p>
					<input style="margin-top: 20px;" type="submit" value="Subscribe" name="subscribe" class="button">
				</p>
			</form>
        </div>
        <div class="response-message"></div>
<?php
        echo wp_kses_post( $after_widget );
     
    }

    /**
     * Display form on admin
     *
     * @since 1.0.0
     *
     * @param array $instance
     *
     * @return void
     */
    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : __( 'Demo Widget', 'weSubs' );
        $subscriber_list = isset( $instance['subscriber_list'] ) ? sanitize_text_field( $instance['subscriber_list'] ) : __( '1', 'weSubs' );

        $api = get_option( 'api_field_data' );
        
		$dc = substr( $api, strpos($api,'-' ) + 1 );

		$args = [
		 	'headers' => [
				'Authorization' => 'Basic ' . base64_encode( 'user:'. $api )
			]
		];

		$response = wp_remote_get( 'https://' . $dc . '.api.mailchimp.com/3.0/lists/', $args );
		$body     = json_decode( wp_remote_retrieve_body( $response ) );

         ?>
         <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo __( 'Title', 'weSubs' ); ?></label>
            <input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
        	<label for="<?php echo esc_attr( $this->get_field_id( 'subscriber_list' ) ); ?>"><?php echo __( 'List:','weSubs' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'subscriber_list' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'subscriber_list' ) ); ?>" class="widefat">

			<?php
				if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
					foreach ( $body->lists as $list ) {
						$form_id   = $list->id;
						$form_name = $list->name;
						?>

						<option value="<?php echo esc_attr( $form_id ); ?>" <?php selected( $subscriber_list, $form_id ); ?>>
							<?php echo esc_html( $form_name ); ?>
						</option>
						
						<?php
					}
				}
	        ?>
			</select>
        </p>

         <?php
    }
  
    /**
     * Update old instances
     *
     * @since 1.0.0
     *
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array $instance
     */
    public function update( $new_instance, $old_instance ) {
        $instance = [];

        $instance['title'] = ! empty ( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['subscriber_list'] = ! empty( $new_instance['subscriber_list'] ) ? strip_tags( $new_instance['subscriber_list'] ) : '';

        return $instance;
    }
}
