<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Ecome Mailchimp
 *
 * Displays Mailchimp widget.
 *
 * @author   Khanh
 * @category Widgets
 * @package  Ecome/Widgets
 * @version  1.0.0
 * @extends  ECOME_Widget
 */
if ( !class_exists( 'Ecome_Mailchimp_Widget' ) ) {
	class Ecome_Mailchimp_Widget extends ECOME_Widget
	{
		/**
		 * Constructor.
		 */
		public function __construct()
		{
			$array_settings           = apply_filters( 'ecome_filter_settings_widget_mailchimp',
				array(
					'title'       => array(
						'type'  => 'text',
						'title' => esc_html__( 'Title', 'ecome-toolkit' ),
					),
					'placeholder' => array(
						'type'    => 'text',
						'title'   => esc_html__( 'Placeholder Text:', 'ecome-toolkit' ),
						'default' => esc_html__( 'Enter your email address', 'ecome-toolkit' ),
					),
				)
			);
			$this->widget_cssclass    = 'widget-ecome-mailchimp';
			$this->widget_description = esc_html__( 'Display the customer Newsletter.', 'ecome-toolkit' );
			$this->widget_id          = 'widget_ecome_mailchimp';
			$this->widget_name        = esc_html__( 'Ecome: Newsletter', 'ecome-toolkit' );
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
			ob_start();
			?>
            <div class="newsletter-form-wrap">
                <input class="email email-newsletter" type="email" name="email"
                       placeholder="<?php echo esc_attr( $instance['placeholder'] ); ?>">
                <a href="#" class="button btn-submit submit-newsletter">
                    <span class="fa fa-envelope"></span>
                </a>
            </div>
			<?php
			echo apply_filters( 'ecome_filter_widget_newsletter', ob_get_clean(), $instance );
			$this->widget_end( $args );
		}
	}
}
add_action( 'widgets_init', 'Ecome_Mailchimp_Widget' );
if ( !function_exists( 'Ecome_Mailchimp_Widget' ) ) {
	function Ecome_Mailchimp_Widget()
	{
		register_widget( 'Ecome_Mailchimp_Widget' );
	}
}