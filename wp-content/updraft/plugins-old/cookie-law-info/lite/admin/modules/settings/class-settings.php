<?php
/**
 * Class Settings file.
 *
 * @package CookieYes
 */

namespace CookieYes\Lite\Admin\Modules\Settings;

use CookieYes\Lite\Includes\Modules;
use CookieYes\Lite\Admin\Modules\Settings\Api\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles Cookies Operation
 *
 * @class       Settings
 * @version     3.0.0
 * @package     CookieYes
 */
class Settings extends Modules {

	/**
	 * Constructor.
	 */
	public function init() {
		$controller = Includes\Controller::get_instance();
		add_filter( 'cky_admin_scripts_config', array( $controller, 'load_common_settings' ) );
		add_action( 'cky_after_connect', array( $controller, 'delete_cache' ) );
		$this->load_apis();
	}

	/**
	 * Load API files
	 *
	 * @return void
	 */
	public function load_apis() {
		new Api();
	}

	/**
	 * Main menu template
	 *
	 * @return void
	 */
	public function menu_page_template() {
		echo '<div id="cky-app"></div>';
	}
}
