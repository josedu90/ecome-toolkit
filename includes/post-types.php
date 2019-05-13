<?php
/**
 * @version    1.0
 * @package    Ecome_Toolkit
 * @author     Ecome
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
/**
 * Class Toolkit Post Type
 *
 * @since    1.0
 */
if ( !class_exists( 'Ecome_Post_Type' ) ) {
	class Ecome_Post_Type
	{
		public function __construct()
		{
			add_action( 'init', array( &$this, 'init' ), 9999 );
		}

		public static function init()
		{
			/* FAQs */
			$args = array(
				'labels'              => array(
					'name'               => __( 'FAQs', 'ecome-toolkit' ),
					'singular_name'      => __( 'FAQs item', 'ecome-toolkit' ),
					'add_new'            => __( 'Add new', 'ecome-toolkit' ),
					'add_new_item'       => __( 'Add new FAQs item', 'ecome-toolkit' ),
					'edit_item'          => __( 'Edit FAQs item', 'ecome-toolkit' ),
					'new_item'           => __( 'New FAQs item', 'ecome-toolkit' ),
					'view_item'          => __( 'View FAQs item', 'ecome-toolkit' ),
					'search_items'       => __( 'Search FAQs items', 'ecome-toolkit' ),
					'not_found'          => __( 'No FAQs items found', 'ecome-toolkit' ),
					'not_found_in_trash' => __( 'No FAQs items found in trash', 'ecome-toolkit' ),
					'parent_item_colon'  => __( 'Parent FAQs item:', 'ecome-toolkit' ),
					'menu_name'          => __( 'FAQs', 'ecome-toolkit' ),
				),
				'hierarchical'        => false,
				'description'         => __( 'FAQs.', 'ecome-toolkit' ),
				'supports'            => array( 'title', 'editor' ),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'ecome_menu',
				'menu_position'       => 0,
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'menu_icon'           => 'dashicons-welcome-widgets-menus',
			);
			register_post_type( 'faqs', $args );
			/* Mega menu */
			$args = array(
				'labels'              => array(
					'name'               => __( 'Mega Builder', 'ecome-toolkit' ),
					'singular_name'      => __( 'Mega menu item', 'ecome-toolkit' ),
					'add_new'            => __( 'Add new', 'ecome-toolkit' ),
					'add_new_item'       => __( 'Add new menu item', 'ecome-toolkit' ),
					'edit_item'          => __( 'Edit menu item', 'ecome-toolkit' ),
					'new_item'           => __( 'New menu item', 'ecome-toolkit' ),
					'view_item'          => __( 'View menu item', 'ecome-toolkit' ),
					'search_items'       => __( 'Search menu items', 'ecome-toolkit' ),
					'not_found'          => __( 'No menu items found', 'ecome-toolkit' ),
					'not_found_in_trash' => __( 'No menu items found in trash', 'ecome-toolkit' ),
					'parent_item_colon'  => __( 'Parent menu item:', 'ecome-toolkit' ),
					'menu_name'          => __( 'Menu Builder', 'ecome-toolkit' ),
				),
				'hierarchical'        => false,
				'description'         => __( 'Mega Menus.', 'ecome-toolkit' ),
				'supports'            => array( 'title', 'editor' ),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'ecome_menu',
				'menu_position'       => 3,
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'menu_icon'           => 'dashicons-welcome-widgets-menus',
			);
			register_post_type( 'megamenu', $args );
			/* Footer */
			$args = array(
				'labels'              => array(
					'name'               => __( 'Footers', 'ecome-toolkit' ),
					'singular_name'      => __( 'Footers', 'ecome-toolkit' ),
					'add_new'            => __( 'Add New', 'ecome-toolkit' ),
					'add_new_item'       => __( 'Add new footer', 'ecome-toolkit' ),
					'edit_item'          => __( 'Edit footer', 'ecome-toolkit' ),
					'new_item'           => __( 'New footer', 'ecome-toolkit' ),
					'view_item'          => __( 'View footer', 'ecome-toolkit' ),
					'search_items'       => __( 'Search template footer', 'ecome-toolkit' ),
					'not_found'          => __( 'No template items found', 'ecome-toolkit' ),
					'not_found_in_trash' => __( 'No template items found in trash', 'ecome-toolkit' ),
					'parent_item_colon'  => __( 'Parent template item:', 'ecome-toolkit' ),
					'menu_name'          => __( 'Footer Builder', 'ecome-toolkit' ),
				),
				'hierarchical'        => false,
				'description'         => __( 'To Build Template Footer.', 'ecome-toolkit' ),
				'supports'            => array( 'title', 'editor' ),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'ecome_menu',
				'menu_position'       => 4,
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'page',
			);
			register_post_type( 'footer', $args );
		}
	}

	new Ecome_Post_Type();
}
