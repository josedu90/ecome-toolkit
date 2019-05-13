<?php
/**
 * Plugin Name: Ecome Toolkit
 * Plugin URI: https://themeforest.net/user/fami_themes
 * Description: The Ecome Toolkit For WordPress Theme WooCommerce Shop.
 * Author: Ecome Team
 * Author URI: https://themeforest.net/user/fami_themes
 * Version: 1.0.7
 * Text Domain: ecome-toolkit
 */
// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;
if ( !class_exists( 'Ecome_Toolkit' ) ) {
	class  Ecome_Toolkit
	{
		/**
		 * @var Ecome_Toolkit The one true Ecome_Toolkit
		 * @since 1.0
		 */
		private static $instance;

		public static function instance()
		{
			if ( !isset( self::$instance ) && !( self::$instance instanceof Ecome_Toolkit ) ) {
				self::$instance = new Ecome_Toolkit;
				self::$instance->setup_constants();
				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
				self::$instance->includes();
				add_action( 'after_setup_theme', array( self::$instance, 'after_setup_theme' ) );
			}

			return self::$instance;
		}

		public function after_setup_theme()
		{
			require_once ECOME_TOOLKIT_PATH . 'includes/admin/import/import.php';
			/* MAILCHIP */
			require_once ECOME_TOOLKIT_PATH . 'includes/admin/mailchimp/MCAPI.class.php';
			require_once ECOME_TOOLKIT_PATH . 'includes/admin/mailchimp/mailchimp-settings.php';
			require_once ECOME_TOOLKIT_PATH . 'includes/admin/mailchimp/mailchimp.php';
			require_once ECOME_TOOLKIT_PATH . 'includes/admin/live-search/live-search.php';
			require_once ECOME_TOOLKIT_PATH . 'includes/mapper/includes/core.php';
		}

		public function setup_constants()
		{
			// Plugin version.
			if ( !defined( 'ECOME_TOOLKIT_VERSION' ) ) {
				define( 'ECOME_TOOLKIT_VERSION', '1.0.6' );
			}
			// Plugin Folder Path.
			if ( !defined( 'ECOME_TOOLKIT_PATH' ) ) {
				define( 'ECOME_TOOLKIT_PATH', plugin_dir_path( __FILE__ ) );
			}
			// Plugin Folder URL.
			if ( !defined( 'ECOME_TOOLKIT_URL' ) ) {
				define( 'ECOME_TOOLKIT_URL', plugin_dir_url( __FILE__ ) );
			}
		}

		public function includes()
		{
			require_once ECOME_TOOLKIT_PATH . 'includes/admin/welcome.php';
			require_once ECOME_TOOLKIT_PATH . 'includes/post-types.php';
			require_once ECOME_TOOLKIT_PATH . 'includes/frontend/framework.php';
		}

		public function load_textdomain()
		{
			load_plugin_textdomain( 'ecome-toolkit', false, ECOME_TOOLKIT_URL . 'languages' );
		}
	}
}
if ( !function_exists( 'ECOME_TOOLKIT' ) ) {
	function ECOME_TOOLKIT()
	{
		return Ecome_Toolkit::instance();
	}

	ECOME_TOOLKIT();
	add_action( 'plugins_loaded', 'ECOME_TOOLKIT', 10 );
}
