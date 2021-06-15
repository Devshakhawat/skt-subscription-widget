<?php
namespace UserSubsCollection;

/**
 * Asset class to load scripts
 *
 * @since 1.0.0
 */
class Assets {
    
	/**
	 * Class construct of assets
	 *
	 * @since 1.0.0
	 */
    function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_mailchimp_script' ] );
	}

	/**
     * register scripts
     *
     * @return void
     */
	public function register_mailchimp_script() {
		$handle    = 'subs-form-handle';
		$src       = MSF_ASSETS . '/js/subsform-main.js';
		$deps      = [ 'jquery' ];
		$ver       = time();
		$in_footer = true;

		wp_register_script( $handle, $src, $deps, $ver, $in_footer );
	}
}
