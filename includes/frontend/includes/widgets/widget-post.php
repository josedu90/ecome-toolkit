<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Ecome Post
 *
 * Displays Post widget.
 *
 * @author   Khanh
 * @category Widgets
 * @package  Ecome/Widgets
 * @version  1.0.0
 * @extends  ECOME_Widget
 */
if ( !class_exists( 'Ecome_Post_Widget' ) ) {
	class Ecome_Post_Widget extends ECOME_Widget
	{
		/**
		 * Constructor.
		 */
		public function __construct()
		{
			$array_settings           = apply_filters( 'ecome_filter_settings_widget_post',
				array(
					'title'     => array(
						'type'  => 'text',
						'title' => esc_html__( 'Title', 'ecome-toolkit' ),
					),
					'type_post' => array(
						'type'    => 'select',
						'options' => array(
							'popular' => esc_html__( 'Popular Post', 'ecome-toolkit' ),
							'recent'  => esc_html__( 'Recent Post', 'ecome-toolkit' ),
						),
						'title'   => esc_html__( 'Posts Type', 'ecome-toolkit' ),
					),
					'category'  => array(
						'type'           => 'select',
						'title'          => esc_html__( 'Category', 'ecome-toolkit' ),
						'options'        => 'categories',
						'query_args'     => array(
							'orderby' => 'name',
							'order'   => 'ASC',
						),
						'default_option' => esc_html__( 'Select a category', 'ecome-toolkit' ),
					),
					'orderby'   => array(
						'type'    => 'select',
						'options' => array(
							'date'          => esc_html__( 'Date', 'ecome-toolkit' ),
							'ID'            => esc_html__( 'ID', 'ecome-toolkit' ),
							'author'        => esc_html__( 'Author', 'ecome-toolkit' ),
							'title'         => esc_html__( 'Title', 'ecome-toolkit' ),
							'modified'      => esc_html__( 'Modified', 'ecome-toolkit' ),
							'rand'          => esc_html__( 'Random', 'ecome-toolkit' ),
							'comment_count' => esc_html__( 'Comment count', 'ecome-toolkit' ),
							'menu_order'    => esc_html__( 'Menu order', 'ecome-toolkit' ),
						),
						'title'   => esc_html__( 'Orderby', 'ecome-toolkit' ),
					),
					'order'     => array(
						'type'    => 'select',
						'options' => array(
							'DESC' => esc_html__( 'DESC', 'ecome-toolkit' ),
							'ASC'  => esc_html__( 'ASC', 'ecome-toolkit' ),
						),
						'title'   => esc_html__( 'Order', 'ecome-toolkit' ),
					),
					'number'    => array(
						'type'    => 'number',
						'default' => 4,
						'title'   => esc_html__( 'Posts Per Page', 'ecome-toolkit' ),
					),
				)
			);
			$this->widget_cssclass    = 'widget-ecome-post';
			$this->widget_description = esc_html__( 'Display the customer Post.', 'ecome-toolkit' );
			$this->widget_id          = 'widget_ecome_post';
			$this->widget_name        = esc_html__( 'Ecome: Post', 'ecome-toolkit' );
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
			$args_loop = array(
				'post_type'           => 'post',
				'showposts'           => $instance['number'],
				'nopaging'            => 0,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'order'               => $instance['order'],
				'orderby'             => $instance['orderby'],
				'cat'                 => $instance['category'],
			);
			if ( $instance['type_post'] == 'popular' ) {
				$args_loop['meta_key'] = 'ecome_post_views_count';
				$args_loop['olderby']  = 'meta_value_num';
			}
			$loop_posts = new WP_Query( $args_loop );
			if ( $loop_posts->have_posts() ) : ?>
                <div class="ecome-posts equal-container better-height">
					<?php while ( $loop_posts->have_posts() ) : $loop_posts->the_post() ?>
                        <article <?php post_class( 'equal-elem' ); ?>>
                            <div class="post-item-inner">
                                <div class="post-thumb">
                                    <a href="<?php the_permalink(); ?>">
										<?php
										$image_thumb = apply_filters( 'ecome_resize_image', get_post_thumbnail_id(), 83, 83, true, true );
										echo wp_specialchars_decode( $image_thumb['img'] );
										?>
                                    </a>
                                </div>
                                <div class="post-info">
                                    <div class="block-title">
										<?php ecome_post_title(); ?>
                                    </div>
                                    <div class="date"><?php echo get_the_date(); ?></div>
                                </div>
                            </div>
                        </article>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
                </div>
			<?php else :
				get_template_part( 'content', 'none' );
			endif;
			echo apply_filters( 'ecome_filter_widget_post', ob_get_clean(), $instance );
			$this->widget_end( $args );
		}
	}
}
add_action( 'widgets_init', 'Ecome_Post_Widget' );
if ( !function_exists( 'Ecome_Post_Widget' ) ) {
	function Ecome_Post_Widget()
	{
		register_widget( 'Ecome_Post_Widget' );
	}
}