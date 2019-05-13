<?php
/**
 * Ecome Visual composer setup
 *
 * @author   KHANH
 * @category API
 * @package  Ecome_Visual_composer
 * @since    1.0.0
 */
if ( !defined( 'ECOME_FRAMEWORK_URI' ) ) {
	define( 'ECOME_FRAMEWORK_URI', '/' );
}
if ( !class_exists( 'Ecome_Visual_composer' ) ) {
	class Ecome_Visual_composer
	{
		public function __construct()
		{
			$this->params();
			$this->autocomplete();
			add_action( 'vc_before_init', array( $this, 'ecome_map_shortcode' ) );
			add_filter( 'vc_iconpicker-type-ecomecustomfonts', array( $this, 'iconpicker_type_ecomecustomfonts' ) );
			/* CUSTOM CSS EDITOR */
			add_action( 'vc_after_mapping', array( $this, 'ecome_add_param_all_shortcode' ) );
			add_filter( 'vc_shortcodes_css_class', array( $this, 'ecome_change_element_class_name' ), 10, 3 );
			add_filter( 'ecome_main_custom_css', array( $this, 'ecome_shortcodes_custom_css' ) );
			/* INCLUDE SHORTCODE */
			add_action( 'vc_after_init', array( $this, 'ecome_include_shortcode' ) );
		}

		function ecome_shortcodes_custom_css( $css )
		{
			$id_page = '';
			// Get all custom inline CSS.
			if ( is_singular() ) {
				$id_page = get_the_ID();
			} elseif ( is_shop() ) {
				$id_page = get_option( 'woocommerce_shop_page_id' );
			}
			if ( $id_page != '' ) {
				$post_custom_css = get_post_meta( $id_page, '_Ecome_Shortcode_custom_css', true );
				$inline_css[]    = $post_custom_css;
				if ( count( $inline_css ) > 0 ) {
					$css .= implode( ' ', $inline_css );
				}
			}

			return $css;
		}

		function change_font_container_output_data( $data, $fields, $values, $settings )
		{
			if ( isset( $fields['text_align'] ) ) {
				$data['text_align'] = '
                <div class="vc_row-fluid vc_column">
                    <div class="wpb_element_label">' . __( 'Text align', 'ecome-toolkit' ) . '</div>
                    <div class="vc_font_container_form_field-text_align-container">
                        <select class="vc_font_container_form_field-text_align-select">
                            <option value="" class="" ' . ( '' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . __( 'none', 'ecome-toolkit' ) . '</option>
                            <option value="left" class="left" ' . ( 'left' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . __( 'left', 'ecome-toolkit' ) . '</option>
                            <option value="right" class="right" ' . ( 'right' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . __( 'right', 'ecome-toolkit' ) . '</option>
                            <option value="center" class="center" ' . ( 'center' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . __( 'center', 'ecome-toolkit' ) . '</option>
                            <option value="justify" class="justify" ' . ( 'justify' === $values['text_align'] ? 'selected="selected"' : '' ) . '>' . __( 'justify', 'ecome-toolkit' ) . '</option>
                        </select>
                    </div>';
				if ( isset( $fields['text_align_description'] ) && strlen( $fields['text_align_description'] ) > 0 ) {
					$data['text_align'] .= '
                    <span class="vc_description clear">' . $fields['text_align_description'] . '</span>
                    ';
				}
				$data['text_align'] .= '</div>';
			}

			return $data;
		}

		function ecome_change_element_class_name( $class_string, $tag, $atts )
		{
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( $tag, $atts ) : $atts;
			// Extract shortcode parameters.
			extract( $atts );
			$class_string .= isset( $atts['ecome_custom_id'] ) ? ' ' . $atts['ecome_custom_id'] . ' ' : ' ';
			$class_string .= isset( $atts['css'] ) ? vc_shortcode_custom_css_class( $atts['css'], ' ' ) : ' ';
			$class_string .= isset( $atts['css_laptop'] ) ? vc_shortcode_custom_css_class( $atts['css_laptop'], ' ' ) : ' ';
			$class_string .= isset( $atts['css_tablet'] ) ? vc_shortcode_custom_css_class( $atts['css_tablet'], ' ' ) : ' ';
			$class_string .= isset( $atts['css_ipad'] ) ? vc_shortcode_custom_css_class( $atts['css_ipad'], ' ' ) : ' ';
			$class_string .= isset( $atts['css_mobile'] ) ? vc_shortcode_custom_css_class( $atts['css_mobile'], ' ' ) : ' ';

			return preg_replace( '/\s+/', ' ', $class_string );
		}

		public function ecome_add_param_all_shortcode()
		{
			global $shortcode_tags;
			$check = 1;
			WPBMap::addAllMappedShortcodes();
			if ( count( $shortcode_tags ) > 0 ) {
				vc_add_params(
					'vc_tta_section',
					array(
						array(
							'type'       => 'attach_image',
							'param_name' => 'title_image',
							'heading'    => esc_html__( 'Title image', 'ecome-toolkit' ),
							'group'      => esc_html__( 'Image Group', 'ecome-toolkit' ),
						),
					)
				);
				vc_add_params(
					'vc_single_image',
					array(
						array(
							'param_name' => 'image_effect',
							'heading'    => esc_html__( 'Effect', 'ecome-toolkit' ),
							'group'      => esc_html__( 'Image Effect', 'ecome-toolkit' ),
							'type'       => 'dropdown',
							'value'      => array(
								esc_html__( 'None', 'ecome-toolkit' )                      => 'none',
								esc_html__( 'Normal Effect', 'ecome-toolkit' )             => 'effect normal-effect',
								esc_html__( 'Normal Effect Dark Color', 'ecome-toolkit' )  => 'effect normal-effect dark-bg',
								esc_html__( 'Normal Effect Light Color', 'ecome-toolkit' ) => 'effect normal-effect light-bg',
								esc_html__( 'Bounce In', 'ecome-toolkit' )                 => 'effect bounce-in',
								esc_html__( 'Plus Zoom', 'ecome-toolkit' )                 => 'effect plus-zoom',
								esc_html__( 'Border Zoom', 'ecome-toolkit' )               => 'effect border-zoom',
								esc_html__( 'Border ScaleUp', 'ecome-toolkit' )            => 'effect border-scale',
							),
							'sdt'        => 'none',
						),
					)
				);
				foreach ( $shortcode_tags as $tag => $function ) {
					if ( $check == 1 && strpos( $tag, 'vc_wp' ) === false && $tag != 'vc_btn' ) {
						vc_remove_param( $tag, 'css' );
						add_filter( 'vc_base_build_shortcodes_custom_css', function () { return ''; } );
						add_filter( 'vc_font_container_output_data', array( $this, 'change_font_container_output_data' ), 10, 4 );
						$attributes = array(
							array(
								'type'        => 'textfield',
								'heading'     => esc_html__( 'Extra class name', 'ecome-toolkit' ),
								'param_name'  => 'el_class',
								'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ecome-toolkit' ),
							),
							array(
								'param_name' => 'hidden_markup',
								'type'       => 'tabs',
								'group'      => esc_html__( 'Design Options', 'ecome-toolkit' ),
							),
							/* CSS EDITOR */
							array(
								'type'             => 'css_editor',
								'heading'          => esc_html__( 'Screen Desktop', 'ecome-toolkit' ),
								'param_name'       => 'css',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'vc_col-xs-12 desktop',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => esc_html__( 'Screen Laptop  ( > 1200px < 1500px)', 'ecome-toolkit' ),
								'param_name'       => 'css_laptop',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 laptop',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => esc_html__( 'Screen Tablet  ( > 992px < 1200px)', 'ecome-toolkit' ),
								'param_name'       => 'css_tablet',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 tablet',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => esc_html__( 'Screen Ipad  ( > 768px < 992px )', 'ecome-toolkit' ),
								'param_name'       => 'css_ipad',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 ipad',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => esc_html__( 'Screen Mobile  ( < 768px)', 'ecome-toolkit' ),
								'param_name'       => 'css_mobile',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 mobile',
							),
							/* CHECKBOX BACKGROUND */
							array(
								'type'             => 'checkbox',
								'heading'          => esc_html__( 'Disable Background?', 'ecome-toolkit' ),
								'param_name'       => 'disable_bg_desktop',
								'description'      => esc_html__( 'Disable Background in this screen.', 'ecome-toolkit' ),
								'value'            => array( esc_html__( 'Yes', 'ecome-toolkit' ) => 'yes' ),
								'edit_field_class' => 'vc_col-xs-12 desktop',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
							),
							array(
								'type'             => 'checkbox',
								'heading'          => esc_html__( 'Disable Background?', 'ecome-toolkit' ),
								'param_name'       => 'disable_bg_laptop',
								'description'      => esc_html__( 'Disable Background in this screen.', 'ecome-toolkit' ),
								'value'            => array( esc_html__( 'Yes', 'ecome-toolkit' ) => 'yes' ),
								'edit_field_class' => 'hidden vc_col-xs-12 laptop',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
							),
							array(
								'type'             => 'checkbox',
								'heading'          => esc_html__( 'Disable Background?', 'ecome-toolkit' ),
								'param_name'       => 'disable_bg_tablet',
								'description'      => esc_html__( 'Disable Background in this screen.', 'ecome-toolkit' ),
								'value'            => array( esc_html__( 'Yes', 'ecome-toolkit' ) => 'yes' ),
								'edit_field_class' => 'hidden vc_col-xs-12 tablet',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
							),
							array(
								'type'             => 'checkbox',
								'heading'          => esc_html__( 'Disable Background?', 'ecome-toolkit' ),
								'param_name'       => 'disable_bg_ipad',
								'description'      => esc_html__( 'Disable Background in this screen.', 'ecome-toolkit' ),
								'value'            => array( esc_html__( 'Yes', 'ecome-toolkit' ) => 'yes' ),
								'edit_field_class' => 'hidden vc_col-xs-12 ipad',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
							),
							array(
								'type'             => 'checkbox',
								'heading'          => esc_html__( 'Disable Background?', 'ecome-toolkit' ),
								'param_name'       => 'disable_bg_mobile',
								'description'      => esc_html__( 'Disable Background in this screen.', 'ecome-toolkit' ),
								'value'            => array( esc_html__( 'Yes', 'ecome-toolkit' ) => 'yes' ),
								'edit_field_class' => 'hidden vc_col-xs-12 mobile',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
							),
							/* WIDTH CONTAINER */
							array(
								'type'             => 'number',
								'heading'          => esc_html__( 'Width Desktop', 'ecome-toolkit' ),
								'description'      => esc_html__( 'Custom width contain in this screen.', 'ecome-toolkit' ),
								'param_name'       => 'width_rows_desktop',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'vc_col-xs-12 vc_col-sm-6 desktop',
							),
							array(
								'type'             => 'number',
								'heading'          => esc_html__( 'Width Laptop', 'ecome-toolkit' ),
								'description'      => esc_html__( 'Custom width contain in this screen.', 'ecome-toolkit' ),
								'param_name'       => 'width_rows_laptop',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 vc_col-sm-6 laptop',
							),
							array(
								'type'             => 'number',
								'heading'          => esc_html__( 'Width Tablet', 'ecome-toolkit' ),
								'description'      => esc_html__( 'Custom width contain in this screen.', 'ecome-toolkit' ),
								'param_name'       => 'width_rows_tablet',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 vc_col-sm-6 tablet',
							),
							array(
								'type'             => 'number',
								'heading'          => esc_html__( 'Width Ipad', 'ecome-toolkit' ),
								'description'      => esc_html__( 'Custom width contain in this screen.', 'ecome-toolkit' ),
								'param_name'       => 'width_rows_ipad',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 vc_col-sm-6 ipad',
							),
							array(
								'type'             => 'number',
								'heading'          => esc_html__( 'Width', 'ecome-toolkit' ),
								'description'      => esc_html__( 'Custom width contain in this screen.', 'ecome-toolkit' ),
								'param_name'       => 'width_rows_mobile',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 vc_col-sm-6 mobile',
							),
							/* UNIT CSS WIDTH */
							array(
								'type'             => 'dropdown',
								'heading'          => esc_html__( 'Unit', 'ecome-toolkit' ),
								'param_name'       => 'width_unit_desktop',
								'value'            => array(
									esc_html__( 'Percent (%)', 'ecome-toolkit' )     => '%',
									esc_html__( 'Pixel (px)', 'ecome-toolkit' )      => 'px',
									esc_html__( 'Em (em)', 'ecome-toolkit' )         => 'em',
									esc_html__( 'Max Height (vh)', 'ecome-toolkit' ) => 'vh',
									esc_html__( 'Max Width (vw)', 'ecome-toolkit' )  => 'vw',
								),
								'std'              => '%',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'vc_col-xs-12 vc_col-sm-6 desktop',
							),
							array(
								'type'             => 'dropdown',
								'heading'          => esc_html__( 'Unit', 'ecome-toolkit' ),
								'param_name'       => 'width_unit_laptop',
								'value'            => array(
									esc_html__( 'Percent (%)', 'ecome-toolkit' )     => '%',
									esc_html__( 'Pixel (px)', 'ecome-toolkit' )      => 'px',
									esc_html__( 'Em (em)', 'ecome-toolkit' )         => 'em',
									esc_html__( 'Max Height (vh)', 'ecome-toolkit' ) => 'vh',
									esc_html__( 'Max Width (vw)', 'ecome-toolkit' )  => 'vw',
								),
								'std'              => '%',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 vc_col-sm-6 laptop',
							),
							array(
								'type'             => 'dropdown',
								'heading'          => esc_html__( 'Unit', 'ecome-toolkit' ),
								'param_name'       => 'width_unit_tablet',
								'value'            => array(
									esc_html__( 'Percent (%)', 'ecome-toolkit' )     => '%',
									esc_html__( 'Pixel (px)', 'ecome-toolkit' )      => 'px',
									esc_html__( 'Em (em)', 'ecome-toolkit' )         => 'em',
									esc_html__( 'Max Height (vh)', 'ecome-toolkit' ) => 'vh',
									esc_html__( 'Max Width (vw)', 'ecome-toolkit' )  => 'vw',
								),
								'std'              => '%',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 vc_col-sm-6 tablet',
							),
							array(
								'type'             => 'dropdown',
								'heading'          => esc_html__( 'Unit', 'ecome-toolkit' ),
								'param_name'       => 'width_unit_ipad',
								'value'            => array(
									esc_html__( 'Percent (%)', 'ecome-toolkit' )     => '%',
									esc_html__( 'Pixel (px)', 'ecome-toolkit' )      => 'px',
									esc_html__( 'Em (em)', 'ecome-toolkit' )         => 'em',
									esc_html__( 'Max Height (vh)', 'ecome-toolkit' ) => 'vh',
									esc_html__( 'Max Width (vw)', 'ecome-toolkit' )  => 'vw',
								),
								'std'              => '%',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 vc_col-sm-6 ipad',
							),
							array(
								'type'             => 'dropdown',
								'heading'          => esc_html__( 'Unit', 'ecome-toolkit' ),
								'param_name'       => 'width_unit_mobile',
								'value'            => array(
									esc_html__( 'Percent (%)', 'ecome-toolkit' )     => '%',
									esc_html__( 'Pixel (px)', 'ecome-toolkit' )      => 'px',
									esc_html__( 'Em (em)', 'ecome-toolkit' )         => 'em',
									esc_html__( 'Max Height (vh)', 'ecome-toolkit' ) => 'vh',
									esc_html__( 'Max Width (vw)', 'ecome-toolkit' )  => 'vw',
								),
								'std'              => '%',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'edit_field_class' => 'hidden vc_col-xs-12 vc_col-sm-6 mobile',
							),
							/* TEXT FONT */
							array(
								'type'             => 'font_container',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'param_name'       => 'responsive_font_desktop',
								'edit_field_class' => 'vc_col-xs-12 desktop',
								'settings'         => array(
									'fields' => array(
										'text_align',
										'font_size',
										'line_height',
										'color',
										'text_align_description'  => esc_html__( 'Select text alignment.', 'ecome-toolkit' ),
										'font_size_description'   => esc_html__( 'Enter font size.', 'ecome-toolkit' ),
										'line_height_description' => esc_html__( 'Enter line height.', 'ecome-toolkit' ),
										'color_description'       => esc_html__( 'Select heading color.', 'ecome-toolkit' ),
									),
								),
							),
							array(
								'type'             => 'font_container',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'param_name'       => 'responsive_font_laptop',
								'edit_field_class' => 'hidden vc_col-xs-12 laptop',
								'settings'         => array(
									'fields' => array(
										'text_align',
										'font_size',
										'line_height',
										'color',
										'text_align_description'  => esc_html__( 'Select text alignment.', 'ecome-toolkit' ),
										'font_size_description'   => esc_html__( 'Enter font size.', 'ecome-toolkit' ),
										'line_height_description' => esc_html__( 'Enter line height.', 'ecome-toolkit' ),
										'color_description'       => esc_html__( 'Select heading color.', 'ecome-toolkit' ),
									),
								),
							),
							array(
								'type'             => 'font_container',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'param_name'       => 'responsive_font_tablet',
								'edit_field_class' => 'hidden vc_col-xs-12 tablet',
								'settings'         => array(
									'fields' => array(
										'text_align',
										'font_size',
										'line_height',
										'color',
										'text_align_description'  => esc_html__( 'Select text alignment.', 'ecome-toolkit' ),
										'font_size_description'   => esc_html__( 'Enter font size.', 'ecome-toolkit' ),
										'line_height_description' => esc_html__( 'Enter line height.', 'ecome-toolkit' ),
										'color_description'       => esc_html__( 'Select heading color.', 'ecome-toolkit' ),
									),
								),
							),
							array(
								'type'             => 'font_container',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'param_name'       => 'responsive_font_ipad',
								'edit_field_class' => 'hidden vc_col-xs-12 ipad',
								'settings'         => array(
									'fields' => array(
										'text_align',
										'font_size',
										'line_height',
										'color',
										'text_align_description'  => esc_html__( 'Select text alignment.', 'ecome-toolkit' ),
										'font_size_description'   => esc_html__( 'Enter font size.', 'ecome-toolkit' ),
										'line_height_description' => esc_html__( 'Enter line height.', 'ecome-toolkit' ),
										'color_description'       => esc_html__( 'Select heading color.', 'ecome-toolkit' ),
									),
								),
							),
							array(
								'type'             => 'font_container',
								'group'            => esc_html__( 'Design Options', 'ecome-toolkit' ),
								'param_name'       => 'responsive_font_mobile',
								'edit_field_class' => 'hidden vc_col-xs-12 mobile',
								'settings'         => array(
									'fields' => array(
										'text_align',
										'font_size',
										'line_height',
										'color',
										'text_align_description'  => esc_html__( 'Select text alignment.', 'ecome-toolkit' ),
										'font_size_description'   => esc_html__( 'Enter font size.', 'ecome-toolkit' ),
										'line_height_description' => esc_html__( 'Enter line height.', 'ecome-toolkit' ),
										'color_description'       => esc_html__( 'Select heading color.', 'ecome-toolkit' ),
									),
								),
							),
							array(
								'param_name'       => 'ecome_custom_id',
								'heading'          => esc_html__( 'Hidden ID', 'ecome-toolkit' ),
								'type'             => 'uniqid',
								'edit_field_class' => 'hidden',
							),
						);
					} else {
						$attributes = array(
							array(
								'type'        => 'textfield',
								'heading'     => esc_html__( 'Extra class name', 'ecome-toolkit' ),
								'param_name'  => 'el_class',
								'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ecome-toolkit' ),
							),
							array(
								'param_name'       => 'ecome_custom_id',
								'heading'          => esc_html__( 'Hidden ID', 'ecome-toolkit' ),
								'type'             => 'uniqid',
								'edit_field_class' => 'hidden',
							),
						);
					}
					vc_add_params( $tag, $attributes );
				}
			}
		}

		public function iconpicker_type_ecomecustomfonts()
		{
			$icons['Ecome Fonts'] = array(
				array( 'flaticon-reload' => 'Flaticon Reload' ),
				array( 'flaticon-layers' => 'Flaticon Layers' ),
				array( 'flaticon-technology-1' => 'Flaticon Technology 1' ),
				array( 'flaticon-print-button' => 'Flaticon Print Button' ),
				array( 'flaticon-mouse' => 'Flaticon Mouse' ),
				array( 'flaticon-console' => 'Flaticon Console' ),
				array( 'flaticon-smartphone' => 'Flaticon Smartphone' ),
				array( 'flaticon-technology' => 'Flaticon Technology' ),
				array( 'flaticon-box-1' => 'Flaticon Box 1' ),
				array( 'flaticon-box' => 'Flaticon Box' ),
				array( 'flaticon-search' => 'Flaticon Search' ),
				array( 'flaticon-phone-call' => 'Flaticon Phone Call' ),
				array( 'flaticon-clock' => 'Flaticon Clock' ),
				array( 'flaticon-telemarketer' => 'Flaticon Telemarketer' ),
				array( 'flaticon-safety' => 'Flaticon Safety' ),
				array( 'flaticon-credit-card' => 'Flaticon Credit Card' ),
				array( 'flaticon-truck' => 'Flaticon Truck' ),
				array( 'flaticon-interface' => 'Flaticon Interface' ),
				array( 'flaticon-placeholder' => 'Flaticon Placeholder' ),
				array( 'flaticon-people' => 'Flaticon People' ),
				array( 'flaticon-profile' => 'Flaticon Profile' ),
				array( 'flaticon-shiny-diamond' => 'Flaticon Shiny Diamond' ),
				array( 'flaticon-flash' => 'Flaticon Flash' ),
				array( 'flaticon-signs-1' => 'Flaticon Signs 1' ),
				array( 'flaticon-shapes-1' => 'Flaticon Shapes 1' ),
				array( 'flaticon-signs' => 'Flaticon Signs' ),
				array( 'flaticon-shapes' => 'Flaticon Shapes' ),
				array( 'flaticon-rocket-launch' => 'Flaticon Rocket Launch' ),
				array( 'flaticon-eye' => 'Flaticon Eye' ),
				array( 'flaticon-arrows-1' => 'Flaticon Arrows 1' ),
				array( 'flaticon-arrows' => 'Flaticon Arrows' ),
				array( 'flaticon-heart-shape-outline' => 'Flaticon Heart Shape Outline' ),
				array( 'flaticon-online-shopping-cart' => 'Flaticon Online Shopping Cart' ),
			);

			return $icons;
		}

		/**
		 * load param autocomplete render
		 * */
		public function autocomplete()
		{
			add_filter( 'vc_autocomplete_ecome_products_ids_callback', array( $this, 'productIdAutocompleteSuggester' ), 10, 1 );
			add_filter( 'vc_autocomplete_ecome_products_ids_render', array( $this, 'productIdAutocompleteRender' ), 10, 1 );
		}

		function params()
		{
			vc_add_shortcode_param( 'taxonomy', array( $this, 'taxonomy_field' ) );
			vc_add_shortcode_param( 'number', array( $this, 'number_field' ) );
			vc_add_shortcode_param( 'select_preview', array( $this, 'select_preview_field' ) );
			vc_add_shortcode_param( 'datepicker', array( $this, 'datepicker_field' ) );
			vc_add_shortcode_param( 'uniqid', array( $this, 'uniqid_field' ) );
			vc_add_shortcode_param( 'tabs', array( $this, 'tabs_field' ) );
		}

		function tabs_field( $settings, $value )
		{
			$output = '<div class="tabs-css">'
				. '<span class="tab_css active" data-tabs="desktop"><i class="fa fa-desktop"></i></span>'
				. '<span class="tab_css" data-tabs="laptop"><i class="fa fa-laptop"></i></span>'
				. '<span class="tab_css" data-tabs="tablet"><i class="fa fa-tablet  fa-rotate-90"></i></span>'
				. '<span class="tab_css" data-tabs="ipad"><i class="fa fa-tablet"></i></span>'
				. '<span class="tab_css" data-tabs="mobile"><i class="fa fa-mobile"></i></span>'
				. '</div>';

			return $output;
		}

		public function uniqid_field( $settings, $value )
		{
			if ( !$value ) {
				$value = 'ecome_css_id_' . uniqid();
			}
			$output = '<input type="text" class="wpb_vc_param_value wpb-textinput ' . $settings['param_name'] . ' textfield" name="' . $settings['param_name'] . '" value="' . esc_attr( $value ) . '" />';

			return $output;
		}

		/**
		 * date picker field
		 **/
		function datepicker_field( $settings, $value )
		{
			$dependency = '';
			$param_name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$type       = isset( $settings['type '] ) ? $settings['type'] : '';
			$suffix     = isset( $settings['suffix'] ) ? $settings['suffix'] : '';
			$class      = isset( $settings['class'] ) ? $settings['class'] : '';
			if ( !$value && isset( $settings['std'] ) ) {
				$value = $settings['std'];
			}
			$main_class = $param_name . ' ' . $type . ' ' . $class;
			ob_start();
			?>
            <label class="cs-field-date" <?php echo esc_attr( $dependency ); ?>>
                <input name="<?php echo esc_attr( $param_name ); ?>" value="<?php echo esc_attr( $value ); ?>"
                       type="text"
                       class="wpb_vc_param_value textfield <?php echo esc_attr( $main_class ); ?>"
                       style="min-width:100%; margin-right: 10px;"><?php echo esc_html( $suffix ); ?>
                <textarea class="cs-datepicker-options hidden">{"dateFormat":"m\/d\/yy"}</textarea>
            </label>
			<?php
			return $output = ob_get_clean();
		}

		public function select_preview_field( $settings, $value )
		{
			// Get menus list
			$options = $settings['value'];
			$default = $settings['default'];
			if ( is_array( $options ) && count( $options ) > 0 ) {
				$uniqeID = uniqid();
				ob_start();
				?>
                <div class="container-select_preview">
                    <label for="<?php echo esc_attr( $settings['param_name'] ); ?>">
                        <select id="ecome_select_preview-<?php echo esc_attr( $uniqeID ); ?>"
                                name="<?php echo esc_attr( $settings['param_name'] ); ?>"
                                class="ecome_select_preview vc_select_image wpb_vc_param_value wpb-input wpb-select <?php echo esc_attr( $settings['param_name'] ); ?> <?php echo esc_attr( $settings['type'] ); ?>_field">
							<?php foreach ( $options as $k => $option ): ?>
								<?php $selected = ( $k == $value ) ? ' selected="selected"' : ''; ?>
                                <option data-preview="<?php echo esc_url( $option['preview'] ); ?>"
                                        value='<?php echo esc_attr( $k ) ?>' <?php echo esc_attr( $selected ) ?>><?php echo esc_attr( $option['title'] ) ?></option>
							<?php endforeach; ?>
                        </select>
                    </label>
                    <div class="image-preview">
						<?php if ( isset( $options[$value] ) && $options[$value] && ( isset( $options[$value]['preview'] ) ) ): ?>
                            <img style="margin-top: 10px; max-width: 100%;height: auto;"
                                 src="<?php echo esc_url( $options[$value]['preview'] ); ?>"
                                 alt="<?php echo get_the_title(); ?>">
						<?php else: ?>
                            <img style="margin-top: 10px; max-width: 100%;height: auto;"
                                 src="<?php echo esc_url( $options[$default]['preview'] ); ?>"
                                 alt="<?php echo get_the_title(); ?>">
						<?php endif; ?>
                    </div>
                </div>
				<?php
			}

			return ob_get_clean();
		}

		/**
		 * taxonomy_field
		 */
		public function taxonomy_field( $settings, $value )
		{
			$dependency = '';
			$value_arr  = $value;
			if ( !is_array( $value_arr ) ) {
				$value_arr = array_map( 'trim', explode( ',', $value_arr ) );
			}
			$output = '';
			if ( isset( $settings['options']['hide_empty'] ) && $settings['options']['hide_empty'] == true ) {
				$settings['options']['hide_empty'] = 1;
			} else {
				$settings['options']['hide_empty'] = 0;
			}
			if ( !empty( $settings['options']['taxonomy'] ) ) {
				$terms_fields = array();
				if ( isset( $settings['options']['placeholder'] ) && $settings['options']['placeholder'] ) {
					$terms_fields[] = "<option value=''>" . $settings['options']['placeholder'] . "</option>";
				}
				$terms = get_terms( $settings['options']['taxonomy'],
					array(
						'hierarchical' => 1,
						'hide_empty'   => $settings['options']['hide_empty'],
					)
				);
				if ( $terms && !is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$selected       = ( in_array( $term->slug, $value_arr ) ) ? ' selected="selected"' : '';
						$terms_fields[] = "<option value='{$term->slug}' {$selected}>{$term->name}</option>";
					}
				}
				$size     = ( !empty( $settings['options']['size'] ) ) ? 'size="' . $settings['options']['size'] . '"' : '';
				$multiple = ( !empty( $settings['options']['multiple'] ) ) ? 'multiple="multiple"' : '';
				$uniqeID  = uniqid();
				$output   = '<select style="width:100%;" id="vc_taxonomy-' . $uniqeID . '" ' . $multiple . ' ' . $size . ' name="' . $settings['param_name'] . '" class="ecome_vc_taxonomy wpb_vc_param_value wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '_field" ' . $dependency . '>'
					. implode( $terms_fields )
					. '</select>';
			}

			return $output;
		}

		/**
		 * Suggester for autocomplete by id/name/title/sku
		 * @since 4.4
		 *
		 * @param $query
		 *
		 * @return array - id's from products with title/sku.
		 */
		public function productIdAutocompleteSuggester( $query )
		{
			global $wpdb;
			$product_id      = (int)$query;
			$post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT a.ID AS id, a.post_title AS title, b.meta_value AS sku
					FROM {$wpdb->posts} AS a
					LEFT JOIN ( SELECT meta_value, post_id  FROM {$wpdb->postmeta} WHERE `meta_key` = '_sku' ) AS b ON b.post_id = a.ID
					WHERE a.post_type = 'product' AND ( a.ID = '%d' OR b.meta_value LIKE '%%%s%%' OR a.post_title LIKE '%%%s%%' )", $product_id > 0 ? $product_id : -1, stripslashes( $query ), stripslashes( $query )
			), ARRAY_A
			);
			$results         = array();
			if ( is_array( $post_meta_infos ) && !empty( $post_meta_infos ) ) {
				foreach ( $post_meta_infos as $value ) {
					$data          = array();
					$data['value'] = $value['id'];
					$data['label'] = esc_html__( 'Id', 'ecome-toolkit' ) . ': ' . $value['id'] . ( ( strlen( $value['title'] ) > 0 ) ? ' - ' . esc_html__( 'Title', 'ecome-toolkit' ) . ': ' . $value['title'] : '' ) . ( ( strlen( $value['sku'] ) > 0 ) ? ' - ' . esc_html__( 'Sku', 'ecome-toolkit' ) . ': ' . $value['sku'] : '' );
					$results[]     = $data;
				}
			}

			return $results;
		}

		/**
		 * Find product by id
		 * @since 4.4
		 *
		 * @param $query
		 *
		 * @return bool|array
		 */
		public function productIdAutocompleteRender( $query )
		{
			$query = trim( $query['value'] ); // get value from requested
			if ( !empty( $query ) ) {
				// get product
				$product_object = wc_get_product( (int)$query );
				if ( is_object( $product_object ) ) {
					$product_sku         = $product_object->get_sku();
					$product_title       = $product_object->get_title();
					$product_id          = $product_object->get_id();
					$product_sku_display = '';
					if ( !empty( $product_sku ) ) {
						$product_sku_display = ' - ' . esc_html__( 'Sku', 'ecome-toolkit' ) . ': ' . $product_sku;
					}
					$product_title_display = '';
					if ( !empty( $product_title ) ) {
						$product_title_display = ' - ' . esc_html__( 'Title', 'ecome-toolkit' ) . ': ' . $product_title;
					}
					$product_id_display = esc_html__( 'Id', 'ecome-toolkit' ) . ': ' . $product_id;
					$data               = array();
					$data['value']      = $product_id;
					$data['label']      = $product_id_display . $product_title_display . $product_sku_display;

					return !empty( $data ) ? $data : false;
				}

				return false;
			}

			return false;
		}

		public function number_field( $settings, $value )
		{
			$dependency = '';
			$param_name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$type       = isset( $settings['type '] ) ? $settings['type'] : '';
			$min        = isset( $settings['min'] ) ? $settings['min'] : '';
			$max        = isset( $settings['max'] ) ? $settings['max'] : '';
			$suffix     = isset( $settings['suffix'] ) ? $settings['suffix'] : '';
			$class      = isset( $settings['class'] ) ? $settings['class'] : '';
			if ( !$value && isset( $settings['std'] ) ) {
				$value = $settings['std'];
			}
			$output = '<input type="number" min="' . esc_attr( $min ) . '" max="' . esc_attr( $max ) . '" class="wpb_vc_param_value textfield ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . esc_attr( $value ) . '" ' . $dependency . ' style="max-width:100px; margin-right: 10px;line-height:23px;height:auto;" />' . $suffix;

			return $output;
		}

		public function ecome_vc_bootstrap( $dependency = null, $value_dependency = null )
		{
			$data_value     = array();
			$data_bootstrap = array(
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Rows space', 'ecome-toolkit' ),
					'param_name' => 'boostrap_rows_space',
					'value'      => array(
						esc_html__( 'Default', 'ecome-toolkit' ) => 'rows-space-0',
						esc_html__( '10px', 'ecome-toolkit' )    => 'rows-space-10',
						esc_html__( '20px', 'ecome-toolkit' )    => 'rows-space-20',
						esc_html__( '30px', 'ecome-toolkit' )    => 'rows-space-30',
						esc_html__( '40px', 'ecome-toolkit' )    => 'rows-space-40',
						esc_html__( '50px', 'ecome-toolkit' )    => 'rows-space-50',
						esc_html__( '60px', 'ecome-toolkit' )    => 'rows-space-60',
						esc_html__( '70px', 'ecome-toolkit' )    => 'rows-space-70',
						esc_html__( '80px', 'ecome-toolkit' )    => 'rows-space-80',
						esc_html__( '90px', 'ecome-toolkit' )    => 'rows-space-90',
						esc_html__( '100px', 'ecome-toolkit' )   => 'rows-space-100',
					),
					'std'        => 'rows-space-0',
					'group'      => esc_html__( 'Boostrap settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Items per row on Desktop', 'ecome-toolkit' ),
					'param_name'  => 'boostrap_bg_items',
					'value'       => array(
						esc_html__( '1 item', 'ecome-toolkit' )  => '12',
						esc_html__( '2 items', 'ecome-toolkit' ) => '6',
						esc_html__( '3 items', 'ecome-toolkit' ) => '4',
						esc_html__( '4 items', 'ecome-toolkit' ) => '3',
						esc_html__( '5 items', 'ecome-toolkit' ) => '15',
						esc_html__( '6 items', 'ecome-toolkit' ) => '2',
					),
					'description' => esc_html__( '(Item per row on screen resolution of device >= 1500px )', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Boostrap settings', 'ecome-toolkit' ),
					'std'         => '4',
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Items per row on Desktop', 'ecome-toolkit' ),
					'param_name'  => 'boostrap_lg_items',
					'value'       => array(
						esc_html__( '1 item', 'ecome-toolkit' )  => '12',
						esc_html__( '2 items', 'ecome-toolkit' ) => '6',
						esc_html__( '3 items', 'ecome-toolkit' ) => '4',
						esc_html__( '4 items', 'ecome-toolkit' ) => '3',
						esc_html__( '5 items', 'ecome-toolkit' ) => '15',
						esc_html__( '6 items', 'ecome-toolkit' ) => '2',
					),
					'description' => esc_html__( '(Item per row on screen resolution of device >= 1200px and < 1500px )', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Boostrap settings', 'ecome-toolkit' ),
					'std'         => '4',
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Items per row on landscape tablet', 'ecome-toolkit' ),
					'param_name'  => 'boostrap_md_items',
					'value'       => array(
						esc_html__( '1 item', 'ecome-toolkit' )  => '12',
						esc_html__( '2 items', 'ecome-toolkit' ) => '6',
						esc_html__( '3 items', 'ecome-toolkit' ) => '4',
						esc_html__( '4 items', 'ecome-toolkit' ) => '3',
						esc_html__( '5 items', 'ecome-toolkit' ) => '15',
						esc_html__( '6 items', 'ecome-toolkit' ) => '2',
					),
					'description' => esc_html__( '(Item per row on screen resolution of device >=992px and < 1200px )', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Boostrap settings', 'ecome-toolkit' ),
					'std'         => '4',
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Items per row on portrait tablet', 'ecome-toolkit' ),
					'param_name'  => 'boostrap_sm_items',
					'value'       => array(
						esc_html__( '1 item', 'ecome-toolkit' )  => '12',
						esc_html__( '2 items', 'ecome-toolkit' ) => '6',
						esc_html__( '3 items', 'ecome-toolkit' ) => '4',
						esc_html__( '4 items', 'ecome-toolkit' ) => '3',
						esc_html__( '5 items', 'ecome-toolkit' ) => '15',
						esc_html__( '6 items', 'ecome-toolkit' ) => '2',
					),
					'description' => esc_html__( '(Item per row on screen resolution of device >=768px and < 992px )', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Boostrap settings', 'ecome-toolkit' ),
					'std'         => '6',
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Items per row on Mobile', 'ecome-toolkit' ),
					'param_name'  => 'boostrap_xs_items',
					'value'       => array(
						esc_html__( '1 item', 'ecome-toolkit' )  => '12',
						esc_html__( '2 items', 'ecome-toolkit' ) => '6',
						esc_html__( '3 items', 'ecome-toolkit' ) => '4',
						esc_html__( '4 items', 'ecome-toolkit' ) => '3',
						esc_html__( '5 items', 'ecome-toolkit' ) => '15',
						esc_html__( '6 items', 'ecome-toolkit' ) => '2',
					),
					'description' => esc_html__( '(Item per row on screen resolution of device >=480  add < 768px )', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Boostrap settings', 'ecome-toolkit' ),
					'std'         => '6',
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Items per row on Mobile', 'ecome-toolkit' ),
					'param_name'  => 'boostrap_ts_items',
					'value'       => array(
						esc_html__( '1 item', 'ecome-toolkit' )  => '12',
						esc_html__( '2 items', 'ecome-toolkit' ) => '6',
						esc_html__( '3 items', 'ecome-toolkit' ) => '4',
						esc_html__( '4 items', 'ecome-toolkit' ) => '3',
						esc_html__( '5 items', 'ecome-toolkit' ) => '15',
						esc_html__( '6 items', 'ecome-toolkit' ) => '2',
					),
					'description' => esc_html__( '(Item per row on screen resolution of device < 480px)', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Boostrap settings', 'ecome-toolkit' ),
					'std'         => '6',
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
			);
			if ( $dependency == null && $value_dependency == null ) {
				foreach ( $data_bootstrap as $value ) {
					unset( $value['dependency'] );
					$data_value[] = $value;
				}
			} else {
				$data_value = $data_bootstrap;
			}

			return $data_value;
		}

		public function ecome_vc_carousel( $dependency = null, $value_dependency = null )
		{
			$data_value    = array();
			$data_carousel = array(
				array(
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( '1 Row', 'ecome-toolkit' )  => '1',
						esc_html__( '2 Rows', 'ecome-toolkit' ) => '2',
						esc_html__( '3 Rows', 'ecome-toolkit' ) => '3',
						esc_html__( '4 Rows', 'ecome-toolkit' ) => '4',
						esc_html__( '5 Rows', 'ecome-toolkit' ) => '5',
						esc_html__( '6 Rows', 'ecome-toolkit' ) => '6',
					),
					'std'        => '1',
					'heading'    => esc_html__( 'The number of rows which are shown on block', 'ecome-toolkit' ),
					'param_name' => 'owl_number_row',
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Rows space', 'ecome-toolkit' ),
					'param_name' => 'owl_rows_space',
					'value'      => array(
						esc_html__( 'Default', 'ecome-toolkit' ) => 'rows-space-0',
						esc_html__( '10px', 'ecome-toolkit' )    => 'rows-space-10',
						esc_html__( '20px', 'ecome-toolkit' )    => 'rows-space-20',
						esc_html__( '30px', 'ecome-toolkit' )    => 'rows-space-30',
						esc_html__( '40px', 'ecome-toolkit' )    => 'rows-space-40',
						esc_html__( '50px', 'ecome-toolkit' )    => 'rows-space-50',
						esc_html__( '60px', 'ecome-toolkit' )    => 'rows-space-60',
						esc_html__( '70px', 'ecome-toolkit' )    => 'rows-space-70',
						esc_html__( '80px', 'ecome-toolkit' )    => 'rows-space-80',
						esc_html__( '90px', 'ecome-toolkit' )    => 'rows-space-90',
						esc_html__( '100px', 'ecome-toolkit' )   => 'rows-space-100',
					),
					'std'        => 'rows-space-0',
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => 'owl_number_row', 'value' => array( '2', '3', '4', '5', '6' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Yes', 'ecome-toolkit' ) => 'true',
						esc_html__( 'No', 'ecome-toolkit' )  => 'false',
					),
					'std'        => 'false',
					'heading'    => esc_html__( 'Vertical Mode', 'ecome-toolkit' ),
					'param_name' => 'owl_vertical',
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Yes', 'ecome-toolkit' ) => 'true',
						esc_html__( 'No', 'ecome-toolkit' )  => 'false',
					),
					'std'        => 'false',
					'heading'    => esc_html__( 'verticalSwiping', 'ecome-toolkit' ),
					'param_name' => 'owl_verticalswiping',
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => 'owl_vertical', 'value' => array( 'true' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Yes', 'ecome-toolkit' ) => 'true',
						esc_html__( 'No', 'ecome-toolkit' )  => 'false',
					),
					'std'        => 'false',
					'heading'    => esc_html__( 'AutoPlay', 'ecome-toolkit' ),
					'param_name' => 'owl_autoplay',
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'number',
					'heading'     => esc_html__( 'Autoplay Speed', 'ecome-toolkit' ),
					'param_name'  => 'owl_autoplayspeed',
					'value'       => '1000',
					'suffix'      => esc_html__( 'milliseconds', 'ecome-toolkit' ),
					'description' => esc_html__( 'Autoplay speed in milliseconds', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency'  => array(
						'element' => 'owl_autoplay', 'value' => array( 'true' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'No', 'ecome-toolkit' )  => 'false',
						esc_html__( 'Yes', 'ecome-toolkit' ) => 'true',
					),
					'std'         => 'true',
					'heading'     => esc_html__( 'Navigation', 'ecome-toolkit' ),
					'param_name'  => 'owl_navigation',
					'description' => esc_html__( "Show buton 'next' and 'prev' buttons.", 'ecome-toolkit' ),
					'group'       => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Navigation style', 'ecome-toolkit' ),
					'param_name' => 'owl_navigation_style',
					'value'      => array(
						esc_html__( 'Default', 'ecome-toolkit' ) => '',
					),
					'std'        => '',
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array( 'element' => 'owl_navigation', 'value' => array( 'true' ) ),
				),
				array(
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'No', 'ecome-toolkit' )  => 'false',
						esc_html__( 'Yes', 'ecome-toolkit' ) => 'true',
					),
					'std'         => 'false',
					'heading'     => esc_html__( 'Dots', 'ecome-toolkit' ),
					'param_name'  => 'owl_dots',
					'description' => esc_html__( "Show dots buttons.", 'ecome-toolkit' ),
					'group'       => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Yes', 'ecome-toolkit' ) => 'true',
						esc_html__( 'No', 'ecome-toolkit' )  => 'false',
					),
					'std'         => 'false',
					'heading'     => esc_html__( 'Loop', 'ecome-toolkit' ),
					'param_name'  => 'owl_loop',
					'description' => esc_html__( 'Inifnity loop. Duplicate last and first items to get loop illusion.', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'number',
					'heading'     => esc_html__( 'Slide Speed', 'ecome-toolkit' ),
					'param_name'  => 'owl_slidespeed',
					'value'       => '300',
					'suffix'      => esc_html__( 'milliseconds', 'ecome-toolkit' ),
					'description' => esc_html__( 'Slide speed in milliseconds', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency'  => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'        => 'number',
					'heading'     => esc_html__( 'Margin', 'ecome-toolkit' ),
					'param_name'  => 'owl_slide_margin',
					'value'       => '30',
					'suffix'      => esc_html__( 'Pixel', 'ecome-toolkit' ),
					'description' => esc_html__( 'Distance( or space) between 2 item', 'ecome-toolkit' ),
					'group'       => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency'  => array(
						'element' => 'owl_vertical', 'value' => array( 'false' ),
					),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'The items on desktop (Screen resolution of device >= 1500px )', 'ecome-toolkit' ),
					'param_name' => 'owl_ls_items',
					'value'      => '4',
					'suffix'     => esc_html__( 'item(s)', 'ecome-toolkit' ),
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'The items on desktop (Screen resolution of device >= 1200px and < 1500px )', 'ecome-toolkit' ),
					'param_name' => 'owl_lg_items',
					'value'      => '4',
					'suffix'     => esc_html__( 'item(s)', 'ecome-toolkit' ),
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'The items on desktop (Screen resolution of device >= 992px < 1200px )', 'ecome-toolkit' ),
					'param_name' => 'owl_md_items',
					'value'      => '3',
					'suffix'     => esc_html__( 'item(s)', 'ecome-toolkit' ),
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'The items on tablet (Screen resolution of device >=768px and < 992px )', 'ecome-toolkit' ),
					'param_name' => 'owl_sm_items',
					'value'      => '2',
					'suffix'     => esc_html__( 'item(s)', 'ecome-toolkit' ),
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'The items on mobile landscape(Screen resolution of device >=480px and < 768px)', 'ecome-toolkit' ),
					'param_name' => 'owl_xs_items',
					'value'      => '2',
					'suffix'     => esc_html__( 'item(s)', 'ecome-toolkit' ),
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
				array(
					'type'       => 'number',
					'heading'    => esc_html__( 'The items on mobile (Screen resolution of device < 480px)', 'ecome-toolkit' ),
					'param_name' => 'owl_ts_items',
					'value'      => '1',
					'suffix'     => esc_html__( 'item(s)', 'ecome-toolkit' ),
					'group'      => esc_html__( 'Carousel settings', 'ecome-toolkit' ),
					'dependency' => array(
						'element' => $dependency, 'value' => array( $value_dependency ),
					),
				),
			);
			if ( $dependency == null && $value_dependency == null ) {
				$match = array(
					'owl_navigation_style',
					'owl_autoplayspeed',
					'owl_rows_space',
					'owl_verticalswiping',
				);
				foreach ( $data_carousel as $value ) {
					if ( !in_array( $value['param_name'], $match ) ) {
						unset( $value['dependency'] );
					}
					$data_value[] = $value;
				}
			} else {
				$data_value = $data_carousel;
			}

			return $data_value;
		}

		public function ecome_param_visual_composer()
		{
			$param = array();

			return apply_filters( 'ecome_add_param_visual_composer', $param );
		}

		public function ecome_map_shortcode()
		{
			$param_maps = $this->ecome_param_visual_composer();
			foreach ( $param_maps as $value ) {
				if ( $value['base'] == 'ecome_products' || $value['base'] == 'ecome_instagram' ) {
					$value['params'] = array_merge(
						$value['params'],
						$this->ecome_vc_carousel( 'productsliststyle', 'owl' ),
						$this->ecome_vc_bootstrap( 'productsliststyle', 'grid' )
					);
				}
				if ( $value['base'] == 'ecome_slide' || $value['base'] == 'ecome_blog' ) {
					$value['params'] = array_merge(
						$value['params'],
						$this->ecome_vc_carousel()
					);
				}
				if ( $value['base'] == 'ecome_faqs' ) {
					$value['params'] = array_merge(
						$value['params'],
						$this->ecome_vc_bootstrap()
					);
				}
				if ( function_exists( 'vc_map' ) ) {
					vc_map( $value );
				}
			}
		}

		private function ecome_get_templates( $template_name )
		{
			$active_plugin_wc = is_plugin_active( 'woocommerce/woocommerce.php' );
			$path_templates   = apply_filters( 'ecome_templates_shortcode', 'vc_templates' );
			if ( $template_name == 'ecome_products' && !$active_plugin_wc )
				return;
			$directory_shortcode = '';
			if ( is_file( get_template_directory() . '/' . $path_templates . '/' . $template_name . '.php' ) ) {
				$directory_shortcode = get_template_directory() . '/' . $path_templates;
			}
			if ( $directory_shortcode != '' )
				include_once $directory_shortcode . '/' . $template_name . '.php';
		}

		function ecome_include_shortcode()
		{
			$param_maps = $this->ecome_param_visual_composer();
			foreach ( $param_maps as $shortcode ) {
				$this->ecome_get_templates( $shortcode['base'] );
			}
		}
	}

	new Ecome_Visual_composer();
}
VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Tta_Accordion' );

class WPBakeryShortCode_Ecome_Tabs extends WPBakeryShortCode_VC_Tta_Accordion
{
}

class WPBakeryShortCode_Ecome_Accordion extends WPBakeryShortCode_VC_Tta_Accordion
{
}

class WPBakeryShortCode_Ecome_Slide extends WPBakeryShortCodesContainer
{
}

/**
 * Ecome Shortcode setup
 *
 * @author   KHANH
 * @category API
 * @package  Ecome_Shortcode
 * @since    1.0.0
 */
if ( !class_exists( 'Ecome_Shortcode' ) ) {
	class Ecome_Shortcode
	{
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = '';
		/**
		 * Register shortcode with WordPress.
		 *
		 * @return  void
		 */
		/**
		 * Meta key.
		 *
		 * @var  string
		 */
		protected $css_key = '_Ecome_Shortcode_custom_css';

		public function __construct()
		{
			if ( !empty( $this->shortcode ) ) {
				add_shortcode( "ecome_{$this->shortcode}", array( $this, 'output_html' ) );
			}
			add_action( 'save_post', array( $this, 'update_post' ) );
		}

		/**
		 * Replace and save custom css to post meta.
		 *
		 * @param   int $post_id
		 *
		 * @return  void
		 */
		public function update_post( $post_id )
		{
			if ( !isset( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
				return;
			}
			// Set and replace content.
			$post = $this->replace_post( $post_id );
			if ( $post ) {
				// Generate custom CSS.
				$css = $this->EcomeShortcodesCustomCss( $post->post_content );
				// Update post and save CSS to post meta.
				$this->save_post( $post );
				$this->save_css_postmeta( $post_id, $css );
			} else {
				$this->save_css_postmeta( $post_id, '' );
			}
		}

		/**
		 * Replace shortcode used in a post with real content.
		 *
		 * @param   int $post_id Post ID.
		 *
		 * @return  WP_Post object or null.
		 */
		public function replace_post( $post_id )
		{
			// Get post.
			$post = get_post( $post_id );
			if ( $post ) {
				$post->post_content = preg_replace_callback(
					'/(ecome_custom_id)="[^"]+"/',
					array( $this, 'ecome_shortcode_replace_post_callback' ),
					$post->post_content
				);
			}

			return $post;
		}

		function ecome_shortcode_replace_post_callback( $matches )
		{
			// Generate a random string to use as element ID.
			$id = 'ecome_custom_' . uniqid();

			return $matches[1] . '="' . $id . '"';
		}

		/**
		 * Parse shortcode custom css string.
		 *
		 * @param   string $content
		 * @return  string
		 */
		public function EcomeShortcodesCustomCss( $content )
		{
			$css = '';
			WPBMap::addAllMappedShortcodes();
			if ( preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes ) ) {
				foreach ( $shortcodes[2] as $index => $tag ) {
					$atts      = shortcode_parse_atts( trim( $shortcodes[3][$index] ) );
					$shortcode = explode( '_', $tag );
					$shortcode = end( $shortcode );
					if ( strpos( $tag, 'ecome_' ) !== false ) {
						$class = 'Ecome_Shortcode_' . implode( '_', array_map( 'ucfirst', explode( '-', $shortcode ) ) );
						if ( class_exists( $class ) ) {
							$css .= $class::add_css_generate( $atts );
						}
					}
					$css .= $this->add_css_editor( $atts, $tag );
				}
				foreach ( $shortcodes[5] as $shortcode_content ) {
					$css .= $this->EcomeShortcodesCustomCss( $shortcode_content );
				}
			}

			return $css;
		}

		/**
		 * Update post data content.
		 *
		 * @param   array $post WP_Post object.
		 *
		 * @return  void
		 */
		public function save_post( $post )
		{
			// Sanitize post data for inserting into database.
			$data = sanitize_post( $post, 'db' );
			// Update post content.
			global $wpdb;
			$wpdb->query( "UPDATE {$wpdb->posts} SET post_content = '" . esc_sql( $data->post_content ) . "' WHERE ID = {$data->ID};" );
			// Update post cache.
			$data = sanitize_post( $post, 'raw' );
			wp_cache_replace( $data->ID, $data, 'posts' );
		}

		/**
		 * Update extra post meta.
		 *
		 * @param   int $post_id Post ID.
		 * @param   string $css Custom CSS.
		 *
		 * @return  void
		 */
		public function save_css_postmeta( $post_id, $css )
		{
			if ( $post_id && $this->css_key ) {
				if ( empty( $css ) ) {
					delete_post_meta( $post_id, $this->css_key );
				} else {
					update_post_meta( $post_id, $this->css_key, preg_replace( '/[\t\r\n]/', '', $css ) );
				}
			}
		}

		/**
		 * Generate custom CSS.
		 *
		 * @param   array $atts Shortcode parameters.
		 *
		 * @return  string
		 */
		static public function add_css_generate( $atts )
		{
			return '';
		}

		public function generate_style_font( $container_data )
		{
			$style_font_data     = array();
			$styles              = array();
			$font_container_data = explode( '|', $container_data );
			foreach ( $font_container_data as $value ) {
				if ( $value != '' ) {
					$data_style                      = explode( ':', $value );
					$style_font_data[$data_style[0]] = $data_style[1];
				}
			}
			foreach ( $style_font_data as $key => $value ) {
				if ( 'tag' !== $key && strlen( $value ) ) {
					if ( preg_match( '/description/', $key ) ) {
						continue;
					}
					if ( 'font_size' === $key || 'line_height' === $key ) {
						$value = preg_replace( '/\s+/', '', $value );
					}
					if ( 'font_size' === $key ) {
						$pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
						// allowed metrics: http://www.w3schools.com/cssref/css_units.asp
						$regexr = preg_match( $pattern, $value, $matches );
						$value  = isset( $matches[1] ) ? (float)$matches[1] : (float)$value;
						$unit   = isset( $matches[2] ) ? $matches[2] : 'px';
						$value  = $value . $unit;
					}
					if ( strlen( $value ) > 0 ) {
						$styles[] = str_replace( '_', '-', $key ) . ': ' . urldecode( $value );
					}
				}
			}

			return !empty( $styles ) ? implode( ' !important;', $styles ) . ' !important;' : '';
		}

		public function add_css_editor( $atts, $tag )
		{
			$css       = '';
			$inner_css = '';
			if ( $tag == 'vc_column' || $tag == 'vc_column_inner' ) {
				$inner_css = ' > .vc_column-inner';
			}
			$unit_css = isset( $atts['width_unit_desktop'] ) ? $atts['width_unit_desktop'] : '%';
			/* SCREEN DESKTOP */
			if ( isset( $atts['css'] ) && $atts['css'] != '' )
				$css .= ( $tag == 'vc_column' || $tag == 'vc_column_inner' ) ? str_replace( '{', ' > .vc_column-inner{', $atts['css'] ) : $atts['css'];
			if ( isset( $atts['responsive_font_desktop'] ) && $this->generate_style_font( $atts['responsive_font_desktop'] ) != '' )
				$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{' . $this->generate_style_font( $atts['responsive_font_desktop'] ) . '}';
			if ( isset( $atts['width_rows_desktop'] ) && $atts['width_rows_desktop'] != '' )
				$css .= '.' . $atts['ecome_custom_id'] . '{width: ' . $atts['width_rows_desktop'] . $unit_css . '!important}';
			if ( isset( $atts['disable_bg_desktop'] ) && $atts['disable_bg_desktop'] == 'yes' )
				$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{background-image: none !important;}';
			/* SCREEN LAPTOP */
			if ( isset( $atts['css_laptop'] ) || isset( $atts['responsive_font_laptop'] ) || isset( $atts['width_rows_laptop'] ) || isset( $atts['disable_bg_laptop'] ) ) {
				$unit_css_laptop = isset( $atts['width_unit_laptop'] ) ? $atts['width_unit_laptop'] : '%';
				$css             .= '@media (min-width:1200px) and (max-width:1499px){';
				if ( isset( $atts['css_laptop'] ) && $atts['css_laptop'] != '' )
					$css .= ( $tag == 'vc_column' || $tag == 'vc_column_inner' ) ? str_replace( '{', ' > .vc_column-inner{', $atts['css_laptop'] ) : $atts['css_laptop'];
				if ( isset( $atts['responsive_font_laptop'] ) && $this->generate_style_font( $atts['responsive_font_laptop'] ) != '' )
					$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{' . $this->generate_style_font( $atts['responsive_font_laptop'] ) . '}';
				if ( isset( $atts['width_rows_laptop'] ) && $atts['width_rows_laptop'] != '' )
					$css .= '.' . $atts['ecome_custom_id'] . '{width: ' . $atts['width_rows_laptop'] . $unit_css_laptop . '!important}';
				if ( isset( $atts['disable_bg_laptop'] ) && $atts['disable_bg_laptop'] == 'yes' )
					$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{background-image: none !important;}';
				$css .= '}';
			}
			/* SCREEN TABLET */
			if ( isset( $atts['css_tablet'] ) || isset( $atts['responsive_font_tablet'] ) || isset( $atts['width_rows_tablet'] ) || isset( $atts['disable_bg_tablet'] ) ) {
				$unit_css_tablet = isset( $atts['width_unit_tablet'] ) ? $atts['width_unit_tablet'] : '%';
				$css             .= '@media (min-width:992px) and (max-width:1199px){';
				if ( isset( $atts['css_tablet'] ) && $atts['css_tablet'] != '' )
					$css .= ( $tag == 'vc_column' || $tag == 'vc_column_inner' ) ? str_replace( '{', ' > .vc_column-inner{', $atts['css_tablet'] ) : $atts['css_tablet'];
				if ( isset( $atts['responsive_font_tablet'] ) && $this->generate_style_font( $atts['responsive_font_tablet'] ) != '' )
					$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{' . $this->generate_style_font( $atts['responsive_font_tablet'] ) . '}';
				if ( isset( $atts['width_rows_tablet'] ) && $atts['width_rows_tablet'] != '' )
					$css .= '.' . $atts['ecome_custom_id'] . '{width: ' . $atts['width_rows_tablet'] . $unit_css_tablet . '!important}';
				if ( isset( $atts['disable_bg_tablet'] ) && $atts['disable_bg_tablet'] == 'yes' )
					$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{background-image: none !important;}';
				$css .= '}';
			}
			/* SCREEN IPAD */
			if ( isset( $atts['css_ipad'] ) || isset( $atts['responsive_font_ipad'] ) || isset( $atts['width_rows_ipad'] ) || isset( $atts['disable_bg_ipad'] ) ) {
				$unit_css_ipad = isset( $atts['width_unit_ipad'] ) ? $atts['width_unit_ipad'] : '%';
				$css           .= '@media (min-width:768px) and (max-width:991px){';
				if ( isset( $atts['css_ipad'] ) && $atts['css_ipad'] != '' )
					$css .= ( $tag == 'vc_column' || $tag == 'vc_column_inner' ) ? str_replace( '{', ' > .vc_column-inner{', $atts['css_ipad'] ) : $atts['css_ipad'];
				if ( isset( $atts['responsive_font_ipad'] ) && $this->generate_style_font( $atts['responsive_font_ipad'] ) != '' )
					$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{' . $this->generate_style_font( $atts['responsive_font_ipad'] ) . '}';
				if ( isset( $atts['width_rows_ipad'] ) && $atts['width_rows_ipad'] != '' )
					$css .= '.' . $atts['ecome_custom_id'] . '{width: ' . $atts['width_rows_ipad'] . $unit_css_ipad . '!important}';
				if ( isset( $atts['disable_bg_ipad'] ) && $atts['disable_bg_ipad'] == 'yes' )
					$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{background-image: none !important;}';
				$css .= '}';
			}
			/* SCREEN MOBILE */
			if ( isset( $atts['css_mobile'] ) || isset( $atts['responsive_font_mobile'] ) || isset( $atts['width_rows_mobile'] ) || isset( $atts['disable_bg_mobile'] ) ) {
				$unit_css_mobile = isset( $atts['width_unit_mobile'] ) ? $atts['width_unit_mobile'] : '%';
				$css             .= '@media (max-width:767px){';
				if ( isset( $atts['css_mobile'] ) && $atts['css_mobile'] != '' )
					$css .= ( $tag == 'vc_column' || $tag == 'vc_column_inner' ) ? str_replace( '{', ' > .vc_column-inner{', $atts['css_mobile'] ) : $atts['css_mobile'];
				if ( isset( $atts['responsive_font_mobile'] ) && $this->generate_style_font( $atts['responsive_font_mobile'] ) != '' )
					$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{' . $this->generate_style_font( $atts['responsive_font_mobile'] ) . '}';
				if ( isset( $atts['width_rows_mobile'] ) && $atts['width_rows_mobile'] != '' )
					$css .= '.' . $atts['ecome_custom_id'] . '{width: ' . $atts['width_rows_mobile'] . $unit_css_mobile . '!important}';
				if ( isset( $atts['disable_bg_mobile'] ) && $atts['disable_bg_mobile'] == 'yes' )
					$css .= '.' . $atts['ecome_custom_id'] . '' . $inner_css . '{background-image: none !important;}';
				$css .= '}';
			}

			return $css;
		}

		public function output_html( $atts, $content = null )
		{
			return '';
		}

		/* do_action( 'vc_enqueue_font_icon_element', $font ); // hook to custom do enqueue style */
		function constructIcon( $section )
		{
			vc_icon_element_fonts_enqueue( $section['i_type'] );
			$class = 'vc_tta-icon';
			if ( isset( $section['i_icon_' . $section['i_type']] ) ) {
				$class .= ' ' . $section['i_icon_' . $section['i_type']];
			} else {
				$class .= ' fa fa-adjust';
			}

			return '<i class="' . $class . '"></i>';
		}

		public static function convertAttributesToNewProgressBar( $atts )
		{
			if ( isset( $atts['values'] ) && strlen( $atts['values'] ) > 0 ) {
				$values = vc_param_group_parse_atts( $atts['values'] );
				if ( !is_array( $values ) ) {
					$temp        = explode( ',', $atts['values'] );
					$paramValues = array();
					foreach ( $temp as $value ) {
						$data               = explode( '|', $value );
						$colorIndex         = 2;
						$newLine            = array();
						$newLine['percent'] = isset( $data[0] ) ? $data[0] : 0;
						$newLine['title']   = isset( $data[1] ) ? $data[1] : '';
						if ( isset( $data[1] ) && preg_match( '/^\d{1,3}\%$/', $data[1] ) ) {
							$colorIndex         += 1;
							$newLine['percent'] = (float)str_replace( '%', '', $data[1] );
							$newLine['title']   = isset( $data[2] ) ? $data[2] : '';
						}
						if ( isset( $data[$colorIndex] ) ) {
							$newLine['customcolor'] = $data[$colorIndex];
						}
						$paramValues[] = $newLine;
					}
					$atts['values'] = urlencode( json_encode( $paramValues ) );
				}
			}

			return $atts;
		}

		function get_all_attributes( $tag, $text )
		{
			preg_match_all( '/' . get_shortcode_regex() . '/s', $text, $matches );
			$out               = array();
			$shortcode_content = array();
			if ( isset( $matches[5] ) ) {
				$shortcode_content = $matches[5];
			}
			if ( isset( $matches[2] ) ) {
				$i = 0;
				foreach ( (array)$matches[2] as $key => $value ) {
					if ( $tag === $value ) {
						$out[$i]            = shortcode_parse_atts( $matches[3][$key] );
						$out[$i]['content'] = $matches[5][$key];
					}
					$i++;
				}
			}

			return $out;
		}
	}
}