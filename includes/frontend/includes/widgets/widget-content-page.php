<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Ecome Content Page
 *
 * Displays Content Page widget.
 *
 * @author   Khanh
 * @category Widgets
 * @package  Ecome/Widgets
 * @version  1.0.0
 * @extends  ECOME_Widget
 */
if ( !class_exists( 'Ecome_Content_Page_Widget' ) ) {
	class Ecome_Content_Page_Widget extends ECOME_Widget
	{
		/**
		 * Constructor.
		 */
		public function __construct()
		{
			$array_settings           = apply_filters( 'ecome_filter_settings_widget_content_page',
				array(
					'title'         => array(
						'type'  => 'text',
						'title' => esc_html__( 'Title', 'ecome-toolkit' ),
					),
					'ecome_page_id' => array(
						'type'    => 'select',
						'title'   => esc_html__( 'Select Content', 'ecome-toolkit' ),
						'options' => 'pages',
					),
				)
			);
			$this->widget_cssclass    = 'widget-ecome-content_page ecome-content_page';
			$this->widget_description = esc_html__( 'Display the customer Content Page.', 'ecome-toolkit' );
			$this->widget_id          = 'widget_ecome_content_page';
			$this->widget_name        = esc_html__( 'Ecome: Content Page', 'ecome-toolkit' );
			$this->settings           = $array_settings;
			parent::__construct();
		}

		/**
		 * Output widget.
		 *
		 * @see WP_Widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance )
		{
			$this->widget_start( $args, $instance );
			if ( $instance['ecome_page_id'] ) {
				$post_id = get_post( $instance['ecome_page_id'] );
				$content = $post_id->post_content;
				$content = apply_filters( 'the_content', $content );
				$content = str_replace( ']]>', ']]>', $content );
				/* GET CUSTOM CSS */
				$post_custom_css = get_post_meta( $instance['ecome_page_id'], '_Ecome_Shortcode_custom_css', true );
				echo '<style type="text/css">' . $post_custom_css . '</style>';
				echo $content;
			}
			$this->widget_end( $args );
		}
	}
}
/**
 * Register Widgets.
 *
 * @since 2.3.0
 */
function Ecome_Content_Page_Widget()
{
	register_widget( 'Ecome_Content_Page_Widget' );
}

add_action( 'widgets_init', 'Ecome_Content_Page_Widget' );