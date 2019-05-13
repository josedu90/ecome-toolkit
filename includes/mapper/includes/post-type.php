<?php
/**
 * @version    1.0
 * @package    Ecome_Mapper
 */

class Ecome_Mapper_Post_Type
{
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function initialize()
	{
		$args = array(
			'labels'              => array(
				'name'          => __( 'Pin Mapper', 'ecome-toolkit' ),
				'singular_name' => __( 'Pin Mappers', 'ecome-toolkit' ),
				'add_new'       => __( 'Add New', 'ecome-toolkit' ),
				'add_new_item'  => __( 'Add new pin mapper', 'ecome-toolkit' ),
				'edit_item'     => __( 'Edit pin mapper', 'ecome-toolkit' ),
				'new_item'      => __( 'New Pin Mapper', 'ecome-toolkit' ),
				'view_item'     => __( 'View mapper', 'ecome-toolkit' ),
				'menu_name'     => __( 'Pin Mapper', 'ecome-toolkit' ),
			),
			'supports'            => array( 'page-attributes', 'editor' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'ecome_menu',
			'menu_position'       => 2,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
		);
		register_post_type( 'ecome_mapper', $args );
		// Check if WR Mapper page is requested.
		global $pagenow, $post_type, $post;
		if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
			// Get current post type.
			if ( !isset( $post_type ) ) {
				$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null;
			}
			if ( empty( $post_type ) && ( isset( $post ) || isset( $_REQUEST['post'] ) ) ) {
				$post_type = isset( $post ) ? $post->post_type : get_post_type( $_REQUEST['post'] );
			}
			if ( 'ecome_mapper' == $post_type ) {
				add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 99999999999 );
				if ( 'edit.php' == $pagenow ) {
					// Register necessary actions / filters to customize All Items screen.
					add_filter( 'bulk_actions-edit-ecome_mapper', array( __CLASS__, 'bulk_actions' ) );
					add_filter( 'manage_ecome_mapper_posts_columns', array( __CLASS__, 'register_columns' ) );
					add_action( 'manage_posts_custom_column', array( __CLASS__, 'display_columns' ), 10, 2 );
				} else if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
					if ( !isset( $_REQUEST['action'] ) || 'trash' != $_REQUEST['action'] ) {
						// Register necessary actions / filters to override Item Details screen.
						add_action( 'admin_footer', array( __CLASS__, 'load_edit_form' ) );
						add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
					}
				}
			}
		}
		// Register Ajax actions / filters.
		add_filter( 'woocommerce_json_search_found_products', array( __CLASS__, 'search_products' ) );
	}

	/**
	 * Setup bulk actions for in stock alert subscription screen.
	 *
	 * @param   array $actions Current actions.
	 *
	 * @return  array
	 */
	public static function bulk_actions( $actions )
	{
		// Remove edit action.
		unset( $actions['edit'] );

		return $actions;
	}

	/**
	 * Register columns for in stock alert subscription screen.
	 *
	 * @param   array $columns Current columns.
	 *
	 * @return  array
	 */
	public static function register_columns( $columns )
	{
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'title'     => __( 'Name', 'ecome-toolkit' ),
			'image'     => __( 'Image', 'ecome-toolkit' ),
			'num_pins'  => __( 'Number of Pins', 'ecome-toolkit' ),
			'shortcode' => __( 'Shortcode', 'ecome-toolkit' ),
			'date'      => __( 'Time', 'ecome-toolkit' ),
		);

		return $columns;
	}

	/**
	 * Display columns for in stock alert subscription screen.
	 *
	 * @param   array $column Column to display content for.
	 * @param   int $post_id Post ID to display content for.
	 *
	 * @return  array
	 */
	public static function display_columns( $column, $post_id )
	{
		switch ( $column ) {
			case 'image' :
				// Get current image.
				$attachment_id = get_post_meta( $post_id, 'ecome_mapper_image', true );
				if ( $attachment_id ) {
					// Print image source.
					echo wp_get_attachment_image( $attachment_id, array( 70, 70 ) );
				} else {
					_e( 'No image', 'ecome-toolkit' );
				}
				break;
			case 'num_pins' :
				// Get all pins.
				$pins = get_post_meta( $post_id, 'ecome_mapper_pins', true );
				echo $pins ? count( $pins ) : 0;
				break;
			case 'shortcode' :
				?>
                <span>[ecome_mapper id="<?php echo absint( $post_id ); ?>"]</span>
				<?php
				break;
		}
	}

	/**
	 * Enqueue assets for custom add/edit item form.
	 *
	 * @return  string
	 */
	public static function enqueue_assets()
	{
		// Check if WR Mapper page is requested.
		global $pagenow, $post_type;
		wp_dequeue_script( 'select2' );
		if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
			if ( 'ecome_mapper' == $post_type ) {
				// Enqueue library to support copy to clipboard.
				wp_enqueue_script( 'clipboard', ECOME_TOOLKIT_URL . '/includes/mapper/assets/3rd-party/clipboard/clipboard.min.js' );
				if ( 'edit.php' == $pagenow ) {
					// Register action to print inline initialization script.
					add_action( 'admin_print_footer_scripts', array( __CLASS__, 'print_footer_scripts' ) );
				} else if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
					// Enqueue media.
					wp_enqueue_media();
					// Enqueue Select2.
					wp_enqueue_style( 'select2', ECOME_TOOLKIT_URL . '/includes/mapper/assets/3rd-party/select2/select2.css' );
					wp_register_script( 'wr_select2', ECOME_TOOLKIT_URL . '/includes/mapper/assets/3rd-party/select2/select2.min.js', array(), false, true );
					wp_enqueue_script( 'wr_select2' );
					// Enqueue custom color picker library.
					wp_enqueue_style( 'cs-wp-color-picker', ECOME_TOOLKIT_URL . '/includes/mapper/assets/3rd-party/wp-color-picker/wp-color-picker.min.css', array( 'wp-color-picker' ) );
					wp_enqueue_script( 'cs-wp-color-picker', ECOME_TOOLKIT_URL . '/includes/mapper/assets/3rd-party/wp-color-picker/wp-color-picker.min.js', array( 'wp-color-picker' ), false, true );
					// Awesome
					wp_enqueue_style( 'font-awesome-backend', ECOME_TOOLKIT_URL . '/includes/mapper/assets/3rd-party/font-awesome/css/font-awesome.css', array(), '2.4' );
					// Enqueue assets for custom add/edit item form.
					wp_enqueue_style( 'ecome-toolkit', ECOME_TOOLKIT_URL . '/includes/mapper/assets/css/backend.css' );
					wp_enqueue_script( 'ecome-toolkit', ECOME_TOOLKIT_URL . '/includes/mapper/assets/js/backend.js', array(), false, true );
					wp_localize_script( 'ecome-toolkit', 'ecome_mapper',
						array(
							'product_selector' => array(
								'url'      => admin_url( 'admin-ajax.php?action=woocommerce_json_search_products' ),
								'security' => wp_create_nonce( 'search-products' ),
							),
							'text'             => array(
								'img_selector_btn_label'   => __( 'Select', 'ecome-toolkit' ),
								'img_selector_modal_title' => __( 'Select or upload an image', 'ecome-toolkit' ),
								'ask_for_saving_changes'   => __( 'Your changes on this page are not saved!', 'ecome-toolkit' ),
								'confirm_removing_pin'     => __( 'Are you sure you want to remove this pin?', 'ecome-toolkit' ),
								'please_input_a_title'     => __( 'Please input a title for this pin', 'ecome-toolkit' ),
							),
						)
					);
				}
			}
		}
	}

	/**
	 * Method to print inline initialization script for items list screen.
	 *
	 * @return  void
	 */
	public static function print_footer_scripts()
	{
		?>
        <script type="text/javascript">
            jQuery(function ($) {
                // Init action to copy shortcode to clipboard.
                $('[data-clipboard-target]').each(function () {
                    var clipboard = new Clipboard('#' + $(this).attr('id'));

                    $(this).data('original-text', $(this).text());

                    clipboard.on('success', $.proxy(function (e) {
                        e.clearSelection();

                        // Swap button status.
                        $(this).text($(this).attr('data-success-text')).attr('disabled', 'disabled');

                        // Restore button after 5 seconds.
                        setTimeout($.proxy(function () {
                            $(this).text($(this).data('original-text')).removeAttr('disabled');
                        }, this), 5000);
                    }, this));

                    clipboard.on('error', $.proxy(function (e) {
                        // Swap button status.
                        $(this).text($(this).attr('data-error-text')).attr('disabled', 'disabled');

                        // Restore button after 5 seconds.
                        setTimeout($.proxy(function () {
                            $(this).text($(this).data('original-text')).removeAttr('disabled');
                        }, this), 5000);
                    }, this));
                });
            });
        </script>
		<?php
	}

	/**
	 * Hide default add/edit item form.
	 *
	 * @return  void
	 */
	public static function hide_default_form()
	{
		?>
        <style type="text/css">
            #screen-meta, #screen-meta-links, #submitdiv, #pageparentdiv > .wrap {
                display: none;
            }
        </style>
		<?php
	}

	/**
	 * Load custom add/edit item form.
	 *
	 * @return  void
	 */
	public static function load_edit_form()
	{
		// Load template file.
		include_once ECOME_TOOLKIT_PATH . 'includes/mapper/templates/admin/form.php';
	}

	/**
	 * Save custom post type extra data.
	 *
	 * @param   int $id Current post ID.
	 *
	 * @return  void
	 */
	public static function save_post( $id )
	{
		if ( isset( $_POST['ecome_mapper_image'] ) ) {
			update_post_meta( $id, 'ecome_mapper_image', absint( $_POST['ecome_mapper_image'] ) );
		}
		if ( isset( $_POST['ecome_mapper_settings'] ) && is_array( $_POST['ecome_mapper_settings'] ) ) {
			// Sanitize input data.
			$ecome_mapper_settings = array();
			foreach ( $_POST['ecome_mapper_settings'] as $key => $value ) {
				$ecome_mapper_settings[$key] = sanitize_text_field( $value );
			}
			update_post_meta( $id, 'ecome_mapper_settings', $ecome_mapper_settings );
		}
		if ( isset( $_POST['ecome_mapper_pins'] ) && is_array( $_POST['ecome_mapper_pins'] ) ) {
			$ecome_mapper_pins = array();
			foreach ( $_POST['ecome_mapper_pins'] as $k => $pin ) {
				// Sanitize input data.
				foreach ( $pin as $key => $value ) {
					if ( 'settings' == $key ) {
						foreach ( $value as $settings_key => $settings_value ) {
							if ( 'text' == $settings_key ) {
								$ecome_mapper_pins[$k][$key][$settings_key] = esc_sql(
									str_replace(
										array( "\r\n", "\r", "\n", '\\' ),
										array( '<br>', '<br>', '<br>', '' ),
										$settings_value
									)
								);
							} else {
								$ecome_mapper_pins[$k][$key][$settings_key] = sanitize_text_field( $settings_value );
							}
							if ( 'id' == $settings_key && empty( $settings_value ) ) {
								$ecome_mapper_pins[$k][$key][$settings_key] = wp_generate_password( 5, false, false );
							}
						}
					} else {
						$ecome_mapper_pins[$k][$key] = sanitize_text_field( $value );
					}
				}
			}
			update_post_meta( $id, 'ecome_mapper_pins', $ecome_mapper_pins );
		} else {
			delete_post_meta( $id, 'ecome_mapper_pins' );
		}
		// Publish post if needed.
		if ( !defined( 'DOING_AUTOSAVE' ) || !DOING_AUTOSAVE ) {
			$post = get_post( $id );
			if ( __( 'Auto Draft' ) != $post->post_title && 'publish' != $post->post_status ) {
				wp_publish_post( $post );
			}
		}
		// Image Tesst
		if ( !isset( $_POST['pinmapper_image_fields'] ) || !wp_verify_nonce( $_POST['pinmapper_image_fields'], basename( __FILE__ ) ) ) {
			return $id;
		}
		// Check Autosave
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
			return $id;
		}
		// Don't save if only a revision
		if ( isset( $post->post_type ) && $post->post_type == 'revision' ) {
			return $id;
		}
		// Check permissions
		if ( !current_user_can( 'edit_post', $post->ID ) ) {
			return $id;
		}
		$meta['pin_style_select'] = ( isset( $_POST['pin_style_select'] ) ? esc_textarea( $_POST['pin_style_select'] ) : '' );
		foreach ( $meta as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
	}

	/**
	 * Method to alter results of WooCommerce's product search function.
	 *
	 * @param   array $found_products Current search results.
	 *
	 * @return  array
	 */
	public static function search_products( $found_products )
	{
		// Check if term is a number.
		$id = ( string )wc_clean( stripslashes( $_GET['term'] ) );
		if ( preg_match( '/^\d+$/', $id ) ) {
			// Get product.
			$product        = wc_get_product( ( int )$id );
			$found_products = array(
				'id'   => $id,
				'text' => rawurldecode( str_replace( '&ndash;', ' - ', $product->get_formatted_name() ) ),
			);
		}

		return $found_products;
	}
}
