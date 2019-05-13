<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Widget Class
 *
 * @author   Khanh
 * @category Widgets
 * @package  Ecome/Abstracts
 * @version  2.5.0
 * @extends  WP_Widget
 */
abstract class ECOME_Widget extends WP_Widget
{
	/**
	 * CSS class.
	 *
	 * @var string
	 */
	public $widget_cssclass;
	/**
	 * Widget description.
	 *
	 * @var string
	 */
	public $widget_description;
	/**
	 * Widget ID.
	 *
	 * @var string
	 */
	public $widget_id;
	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $widget_name;
	/**
	 * Settings.
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$widget_ops = array(
			'classname'                   => $this->widget_cssclass,
			'description'                 => $this->widget_description,
			'customize_selective_refresh' => true,
		);
		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * Get cached widget.
	 *
	 * @param  array $args
	 * @return bool true if the widget is cached otherwise false
	 */
	public function get_cached_widget( $args )
	{
		$cache = wp_cache_get( apply_filters( 'ecome_cached_widget_id', $this->widget_id ), 'widget' );
		if ( !is_array( $cache ) ) {
			$cache = array();
		}
		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];

			return true;
		}

		return false;
	}

	/**
	 * Cache the widget.
	 *
	 * @param  array $args
	 * @param  string $content
	 * @return string the content that was cached
	 */
	public function cache_widget( $args, $content )
	{
		$cache = wp_cache_get( apply_filters( 'ecome_cached_widget_id', $this->widget_id ), 'widget' );
		if ( !is_array( $cache ) ) {
			$cache = array();
		}
		$cache[$args['widget_id']] = $content;
		wp_cache_set( apply_filters( 'ecome_cached_widget_id', $this->widget_id ), $cache, 'widget' );

		return $content;
	}

	/**
	 * Flush the cache.
	 */
	public function flush_widget_cache()
	{
		wp_cache_delete( apply_filters( 'ecome_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * Output the html at the start of a widget.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget_start( $args, $instance )
	{
		echo $args['before_widget'];
		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	}

	/**
	 * Output the html at the end of a widget.
	 *
	 * @param  array $args
	 */
	public function widget_end( $args )
	{
		echo $args['after_widget'];
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see    WP_Widget->update
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		if ( empty( $this->settings ) ) {
			return $instance;
		}
		// Loop settings and get values to save.
		foreach ( $this->settings as $key => $setting ) {
			if ( !isset( $setting['type'] ) ) {
				continue;
			}
			$instance[$key] = $new_instance[$key];
		}
		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @see   WP_Widget->form
	 *
	 * @param array $instance
	 */
	public function form( $instance )
	{
		if ( empty( $this->settings ) ) {
			return;
		}
		foreach ( $this->settings as $key => $setting ) {
			$default        = isset( $setting['default'] ) ? $setting['default'] : '';
			$instance_value = isset( $instance[$key] ) ? $instance[$key] : $default;
			$instance_field = array(
				'id'   => $this->get_field_name( $key ),
				'name' => $this->get_field_name( $key ),
			);
			$instance_field = array_merge( $instance_field, $setting );
			echo cs_add_element( $instance_field, $instance_value );
		}
	}
}
