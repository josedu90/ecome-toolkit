<?php
/**
 * Ecome Framework setup
 *
 * @author   KHANH
 * @category API
 * @package  Ecome_Framework_Options
 * @since    1.0.0
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( !class_exists( 'Ecome_Framework_Options' ) ) {
	class Ecome_Framework_Options
	{
		public $version = '1.0.0';

		public function __construct()
		{
			$this->define_constants();
			add_action( 'admin_bar_menu', array( $this, 'ecome_custom_menu' ), 1000 );
			add_action( 'plugins_loaded', array( $this, 'includes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 999 );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			/* TEMPLATE DEFAULT */
			add_action( 'vc_load_default_templates_action', array( $this, 'ecome_add_custom_template_for_vc' ) );
		}

		/**
		 * Define WC Constants.
		 */
		private function define_constants()
		{
			$this->define( 'ECOME_FRAMEWORK_VERSION', $this->version );
			$this->define( 'ECOME_FRAMEWORK_URI', plugin_dir_url( __FILE__ ) );
			$this->define( 'ECOME_FRAMEWORK_THEME_PATH', get_template_directory() );
			$this->define( 'ECOME_FRAMEWORK_PATH', plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param string $name Constant name.
		 * @param string|bool $value Constant value.
		 */
		private function define( $name, $value )
		{
			if ( !defined( $name ) ) {
				define( $name, $value );
			}
		}

		function includes()
		{
			include_once( 'includes/core/cs-framework.php' );
			include_once( 'includes/abstracts-widget.php' );
			if ( class_exists( 'Vc_Manager' ) ) {
				include_once( 'includes/visual-composer.php' );
			}
			if ( class_exists( 'WooCommerce' ) ) {
				include_once( 'includes/woo-attributes-swatches/woo-term.php' );
				include_once( 'includes/woo-attributes-swatches/woo-product-attribute-meta.php' );
				include_once( 'includes/woo-function.php' );
			}
			/* WIDGET */
			include_once( 'includes/widgets/widget-custommenu.php' );
			include_once( 'includes/widgets/widget-content-page.php' );
			include_once( 'includes/widgets/widget-instagram.php' );
			include_once( 'includes/widgets/widget-newsletter.php' );
			include_once( 'includes/widgets/widget-socials.php' );
			include_once( 'includes/widgets/widget-post.php' );
		}

		public function ecome_custom_menu()
		{
			global $wp_admin_bar;
			if ( !is_super_admin() || !is_admin_bar_showing() ) return;
			// Add Parent Menu
			$argsParent = array(
				'id'    => 'theme_option',
				'title' => esc_html__( 'Ecome Options', 'ecome-toolkit' ),
				'href'  => admin_url( 'admin.php?page=ecome' ),
			);
			$wp_admin_bar->add_menu( $argsParent );
		}

		function is_url_exist( $url )
		{
			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_NOBODY, true );
			curl_exec( $ch );
			$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			if ( $code == 200 ) {
				$status = true;
			} else {
				$status = false;
			}
			curl_close( $ch );

			return $status;
		}

		function ecome_add_custom_template_for_vc()
		{
			$option_file_url = apply_filters( 'ecome_url_template_visual_composer', '' );
			$option_file_url = esc_url('http://ecome.famithemes.com/wp-content/uploads/template.txt');
			if ( $this->is_url_exist( $option_file_url ) == true ) {
				$option_content  = wp_remote_get( $option_file_url );
				$option_content  = $option_content['body'];
				$option_content  = base64_decode( $option_content );
				$options_configs = json_decode( $option_content, true );
				foreach ( $options_configs as $value ) {
					$data                 = array();
					$data['name']         = $value['name'];
					$data['weight']       = 1;
					$data['custom_class'] = 'custom_template_for_vc_custom_template';
					$data['content']      = $value['content'];
					vc_add_default_templates( $data );
				}
			}
		}

		function widgets_init()
		{
			$ecome_multi_slidebars = cs_get_option( 'multi_widget', '' );
			if ( is_array( $ecome_multi_slidebars ) && count( $ecome_multi_slidebars ) > 0 ) {
				foreach ( $ecome_multi_slidebars as $multi_slidebar ) {
					if ( $multi_slidebar && $multi_slidebar != '' ) {
						register_sidebar( array(
								'name'          => $multi_slidebar['add_widget'],
								'id'            => 'custom-sidebar-' . sanitize_key( $multi_slidebar['add_widget'] ),
								'before_widget' => '<div id="%1$s" class="widget block-sidebar %2$s">',
								'after_widget'  => '</div>',
								'before_title'  => '<div class="title-widget widgettitle"><strong>',
								'after_title'   => '</strong></div>',
							)
						);
					}
				}
			}
		}

		function admin_scripts( $hook )
		{
			wp_enqueue_style( 'ecome-awesome', ECOME_FRAMEWORK_URI . 'assets/css/font-awesome.min.css' );
			wp_enqueue_style( 'ecome-chosen', ECOME_FRAMEWORK_URI . 'assets/css/chosen.min.css' );
			wp_enqueue_style( 'ecome-themify', ECOME_FRAMEWORK_URI . 'assets/css/themify-icons.css' );
			wp_enqueue_style( 'ecome-backend', ECOME_FRAMEWORK_URI . 'assets/css/backend.css' );
			/* SCRIPTS */
			wp_enqueue_script( 'ecome-chosen', ECOME_FRAMEWORK_URI . 'assets/js/libs/chosen.min.js', array(), null );
			wp_enqueue_script( 'ecome-backend', ECOME_FRAMEWORK_URI . 'assets/js/backend.js', array(), null );
			if ( $hook == 'ecome_page_ecome' ) {
				// ACE Editor
				wp_enqueue_style( 'cs-vendor-ace-style', ECOME_FRAMEWORK_URI . 'includes/core/fields/ace_editor/assets/ace.css', array(), '1.0' );
				wp_enqueue_script( 'cs-vendor-ace', ECOME_FRAMEWORK_URI . 'includes/core/fields/ace_editor/assets/ace.js', array(), false, true );
				wp_enqueue_script( 'cs-vendor-ace-mode', ECOME_FRAMEWORK_URI . 'includes/core/fields/ace_editor/assets/mode-css.js', array(), false, true );
				wp_enqueue_script( 'cs-vendor-ace-language_tools', ECOME_FRAMEWORK_URI . 'includes/core/fields/ace_editor/assets/ext-language_tools.js', array(), false, true );
				wp_enqueue_script( 'cs-vendor-ace-css', ECOME_FRAMEWORK_URI . 'includes/core/fields/ace_editor/assets/css.js', array(), false, true );
				wp_enqueue_script( 'cs-vendor-ace-text', ECOME_FRAMEWORK_URI . 'includes/core/fields/ace_editor/assets/text.js', array(), false, true );
				wp_enqueue_script( 'cs-vendor-ace-javascript', ECOME_FRAMEWORK_URI . 'includes/core/fields/ace_editor/assets/javascript.js', array(), false, true );
				// You do not need to use a separate file if you do not like.
				wp_enqueue_script( 'cs-vendor-ace-load', ECOME_FRAMEWORK_URI . 'includes/core/fields/ace_editor/assets/ace-load.js', array(), false, true );
			}
		}
	}

	new Ecome_Framework_Options();
}