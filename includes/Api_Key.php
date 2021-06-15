<?php 
namespace UserSubsCollection;

/**
 * Class Api_Key to add settings field on general
 *
 * @since 1.0.0
 */
class Api_Key {
    public function __construct() {
        add_action( 'admin_init', [ $this, 'add_api_key_field' ]  );
    }

    /**
     * add section and field for api key
     *
     * @since 1.0.0
     *
     * @param null
     *
     * @return void
     */
    public function add_api_key_field() {
        add_settings_section( 'mailchimp_api_section', __( 'MailChimp', 'weSubs' ), [ $this, 'wesubs_api_display' ], 'general' );
        add_settings_field( 'api_field_data', __( 'Mailchimp API Field', 'weSubs' ), [ $this, 'wesubs_api_input_field' ], 'general', 'mailchimp_api_section' );
        register_setting( 'general', 'api_field_data' );
    }

    public function wesubs_api_display() {
        esc_html_e( 'Add api key to connect with mailchimp', 'weSubs' );
    }

    /**
     * added settings field 
     *
     * @since 1.0.0
     *
     * @param null
     *
     * @return void
     */
    public function wesubs_api_input_field() {
        $value = get_option( 'api_field_data' );
        ?>
            <input type="text" class="regular-text" name="api_field_data" id="mailchimp_field" value="<?php echo esc_attr( $value ); ?>" >
        <?php
    }
}
