<?php

class Ecome_Mapper_Shortcode
{
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function initialize()
	{
		// Add shortcode
		add_shortcode( 'ecome_mapper', array( __CLASS__, 'render_shortcode' ) );
		// Enqueue style and script
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Generate HTML code based on shortcode parameters.
	 *
	 * @param   array $atts Shortcode parameters.
	 * @param   string $content Current content.
	 *
	 * @return  string
	 */
	public static function render_shortcode( $atts, $content = null )
	{
		$html = '';
		// Extract shortcode parameters.
		if ( !isset( $atts['id'] ) ) {
			return;
		}
		// Check is publish
		if ( get_post_status( $atts['id'] ) != 'publish' ) {
			return;
		}
		// Get current image.
		$attachment_id = get_post_meta( $atts['id'], 'ecome_mapper_image', true );
		if ( !$attachment_id ) {
			return;
		}
		// Get image source.
		$image_src  = wp_get_attachment_url( $attachment_id );
		$image_data = wp_get_attachment_metadata( $attachment_id );
		// Get general settings.
		$settings = get_post_meta( $atts['id'], 'ecome_mapper_settings', true );
		// Get all pins.
		$pins = get_post_meta( $atts['id'], 'ecome_mapper_pins', true );
		// Generate CSS.
		$html     .= self::inline_style( $atts['id'], $settings, $pins );
		$img_lazy = "data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20" . esc_attr( $image_data['width'] ) . "%20" . esc_attr( $image_data['height'] ) . "%27%2F%3E";
		// Generate HTML.
		$html .= '
		<div id="ecome-mapper-' . esc_attr( $atts['id'] ) . '" class="ecome-mapper ' . esc_attr( $settings['tooltip-style'] ) . ' ' . esc_attr( $settings['popup-show-effect'] ) . ' ' . esc_attr( $settings['image-effect'] ) . '" data-width="' . esc_attr( $image_data['width'] ) . '" data-height="' . esc_attr( $image_data['height'] ) . '">
			<img class="lazy" src="' . esc_attr( $img_lazy ) . '" data-src="' . esc_attr( $image_src ) . '" width="' . esc_attr( $image_data['width'] ) . '" height="' . esc_attr( $image_data['height'] ) . '" alt="' . esc_attr( basename( $image_src ) ) . '" />';
		if ( $settings['image-effect'] == 'mask' ) {
			$html .= '<div class="mask"></div>';
		}
		if ( $pins ) {
			foreach ( $pins as $pin ) {
				$html .= '<div id="ecome-pin-' . esc_attr( $pin['settings']['id'] ) . '" class="ecome-pin" data-top="' . esc_attr( $pin['top'] ) . '" data-left="' . esc_attr( $pin['left'] ) . '" data-position="' . esc_attr( $pin['settings']['popup-position'] ) . '">';
				if ( $pin['settings']['icon-type'] == 'icon-image' && !empty( $pin['settings']['image-template'] ) ) {
					$html .= '<img class="action-pin image-pin lazy" src="' . esc_attr( $img_lazy ) . '" data-src="' . esc_attr( $pin['settings']['image-template'] ) . '" alt="Pin" />';
				} else {
					if ( $pin['settings']['area-text'] ) {
						$style = 'style="font-size: ' . esc_attr( $pin['settings']['area-text-size'] ) . 'px;color: ' . esc_attr( $pin['settings']['area-text-color'] ) . ';width: ' . esc_attr( $pin['settings']['area-width'] ) . 'px;height: ' . esc_attr( $pin['settings']['area-height'] ) . 'px;line-height: ' . esc_attr( $pin['settings']['area-height'] - 2 * $pin['settings']['area-border-width'] ) . 'px;border-width: ' . esc_attr( $pin['settings']['area-border-width'] ) . 'px;border-style: solid;border-radius: ' . esc_attr( $pin['settings']['area-border-radius'] ) . 'px;background: ' . esc_attr( $pin['settings']['area-bg-color'] ) . ';border-color: ' . esc_attr( $pin['settings']['area-border-color'] ) . ';"';
						$html  .= '<div class="text__area" ' . $style . '>' . $pin['settings']['area-text'] . '</div>';
					}
				}
				// Product Item
				if ( class_exists( 'WooCommerce' ) && $pin['settings']['pin-type'] == 'woocommerce' && ( !empty( $pin['settings']['product'] ) ) ) {
					$product_id = $pin['settings']['product'];
					$_product   = wc_get_product( $product_id );
					$html       .= '<div class="ecome-popup ecome-wc ' . esc_attr( $pin['settings']['popup-position'] ) . '">';
					$html       .= '<div class="ecome-popup-main">';
					if ( $pin['settings']['product-thumbnail'] ) {
						$size_thumb = apply_filters( 'ecome_pinmap_product_thumbnail', array( 'width' => 100, 'height' => 150, 'crop' => true ) );
						if ( has_filter( 'ecome_resize_image' ) )
							$image_thumb = apply_filters( 'ecome_resize_image', get_post_thumbnail_id( $product_id ), $size_thumb['width'], $size_thumb['height'], $size_thumb['crop'], true );
						$html .= '<div class="col-left ecome-product-thumbnail">';
						$html .= '<a href="' . esc_url( get_permalink( $product_id ) ) . '">';
						$html .= $image_thumb['img'];
						$html .= '</a>';
						$html .= '</div>';
					}
					$html .= '<div class="col-right">';
					$html .= '<h2><a href="' . esc_url( get_permalink( $product_id ) ) . '">' . get_the_title( $product_id ) . '</a></h2>';
					if ( $pin['settings']['product-rate'] ) {
						$html .= '<div class="woocommerce-product-rating">';
						if ( $_product->get_rating_count() ) {
							$html .= '<div class="review-count">(';
							$html .= sprintf( __( '%s Reviews', 'ecome-toolkit' ), $_product->get_rating_count() );
							$html .= ')</div>';
						}
						$html .= '</div>';
					}
					$html .= '<div class="ecome-wc-price">' . $_product->get_price_html() . '</div>';
					if ( $pin['settings']['product-description'] ) {
						$html .= '<div class="description">';
						if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
							$html .= '<p>' . wp_trim_words( $_product->post->post_excerpt, 10, '...' ) . '</p>';
						} else {
							$html .= '<p>' . wp_trim_words( $_product->get_short_description(), 10, '...' ) . '</p>';
						}
						$html .= '</div>';
					}
					$html .= '</div>';
					$html .= '</div>';
					$html .= '<div class="ecome-popup-footer">';
					if ( $_product->is_type( 'simple' ) && !Ecome_Mapper_Helper::yith_wc_product_add_ons( $product_id ) && !Ecome_Mapper_Helper::check_gravityforms( $product_id ) && !Ecome_Mapper_Helper::wc_measurement_price_calculator( $product_id ) ) {
						$html .= '<a href="' . esc_url( $_product->add_to_cart_url() ) . '" data-product_id="' . esc_attr( $product_id ) . '" data-quantity="1" class="ajax_add_to_cart add_to_cart_button button product_type_simple">' . __( 'Add To Cart', 'ecome-toolkit' ) . '</a>';
					} else {
						$html .= '<a href="' . esc_url( get_permalink( $product_id ) ) . '" data-product_id="' . esc_attr( $product_id ) . '" data-quantity="1" class="add_to_cart_button button product_type_simple">' . __( 'Select Options', 'ecome-toolkit' ) . '</a>';
					}
					$html .= '</div>';
					$html .= '</div>';
					// Image Item
				} elseif ( isset( $pin['settings']['pin-type'] ) && $pin['settings']['pin-type'] == 'image' ) {
					if ( !empty( $pin['settings']['popup-title'] ) ) {
						$html .= '<h3 class="ecome-title">' . $pin['settings']['popup-title'] . '</h3>';
					} else {
						$html .= '<h3 class="ecome-title">' . __( 'Add your title in backend', 'ecome-toolkit' ) . '</h3>';
					}
					$html .= '<div class="ecome-popup ecome-image ' . esc_attr( $pin['settings']['popup-position'] ) . '">';
					$html .= '<div class="ecome-popup-header">';
					$html .= '<h2>' . $pin['settings']['popup-title'] . '</h2>';
					$html .= '</div>';
					$html .= '<div class="ecome-popup-main">';
					if ( !empty( $pin['settings']['image-link-to'] ) ) {
						$html .= '<a target="' . esc_attr( $pin['settings']['image-link-target'] ) . '" href="' . esc_url( $pin['settings']['image-link-to'] ) . '">';
					}
					if ( $pin['settings']['image'] ) {
						$html .= '<img src="' . esc_url( $pin['settings']['image'] ) . '"/>';
					}
					if ( !empty( $pin['settings']['image-link-to'] ) ) {
						$html .= '</a>';
					}
					$html .= '</div>';
					$html .= '</div>';
					// Text Item
				} elseif ( isset( $pin['settings']['pin-type'] ) && $pin['settings']['pin-type'] == 'text' ) {
					if ( !empty( $pin['settings']['popup-title'] ) ) {
						$html .= '<h3 class="ecome-title">' . $pin['settings']['popup-title'] . '</h3>';
					} else {
						$html .= '<h3 class="ecome-title">' . __( 'Add your title in backend', 'ecome-toolkit' ) . '</h3>';
					}
					$html .= '<div class="ecome-popup ecome-text ' . esc_attr( $pin['settings']['popup-position'] ) . '">';
					$html .= '<div class="ecome-popup-header">';
					$html .= '<h2>' . $pin['settings']['popup-title'] . '</h2>';
					$html .= '</div>';
					$html .= '<div class="ecome-popup-main">';
					$html .= '<div class="content-text">';
					$html .= nl2br( do_shortcode( $pin['settings']['text'] ) );
					$html .= '</div>';
					$html .= '</div>';
					$html .= '</div>';
					// Link Item
				} elseif ( isset( $pin['settings']['pin-type'] ) && $pin['settings']['pin-type'] == 'link' ) {
					$html .= '<a class="ecome-link" target="' . esc_attr( $pin['settings']['image-link-target'] ) . '" href="' . esc_url( $pin['settings']['image-link-to'] ) . '"></a>';
					if ( !empty( $pin['settings']['popup-title'] ) ) {
						$html .= '<h3 class="ecome-title">' . $pin['settings']['popup-title'] . '</h3>';
					} else {
						$html .= '<h3 class="ecome-title">' . __( 'Add your title in backend', 'ecome-toolkit' ) . '</h3>';
					}
				}
				$html .= '</div>';
			}
		}
		$html .= '
		</div>';

		return apply_filters( 'render_shortcode', force_balance_tags( $html ) );
	}

	/**
	 * Enqueue custom scripts / stylesheets.
	 *
	 * @return  void
	 */
	public static function enqueue_scripts()
	{
		if ( is_singular() ) {
			global $post;
			// Enqueue required assets.
			wp_enqueue_style( 'ecome-toolkit', ECOME_TOOLKIT_URL . '/includes/mapper/assets/css/frontend.min.css' );
			wp_enqueue_script( 'ecome-toolkit', ECOME_TOOLKIT_URL . '/includes/mapper/assets/js/frontend.min.js', array(), false, true );
		}
	}

	/**
	 * Render inline style.
	 *
	 * @param   int $id Mapper ID.
	 * @param   array $settings Mapper settings.
	 * @param   array $pins Mapper pins.
	 *
	 * @return  void
	 */
	public static function inline_style( $id, $settings, $pins )
	{
		// Generate CSS rules for general settings.
		$css = '
		<style type="text/css">
			#ecome-mapper-' . $id . ' .ecome-popup {';
		$css .= 'padding: 10px;';
		if ( $settings['popup-width'] ) {
			$css .= 'width: ' . esc_attr( $settings['popup-width'] ) . 'px;';
		}
		if ( $settings['popup-height'] ) {
			$css .= 'min-height: ' . esc_attr( $settings['popup-height'] ) . 'px;';
		}
		if ( $settings['popup-box-shadow'] ) {
			$css .= 'box-shadow: 0px 2px 10px 0px #dfdfdf;';
		}
		if ( $settings['popup-border-radius'] ) {
			$css .= 'border-radius: ' . (int)$settings['popup-border-radius'] . 'px;';
		}
		if ( $settings['popup-border-width'] ) {
			$css .= 'border: ' . (int)$settings['popup-border-width'] . 'px solid;';
		}
		if ( $settings['popup-border-color'] ) {
			$css .= 'border-color: ' . esc_attr( $settings['popup-border-color'] ) . ';';
		}
		$css .= '}';
		if ( $settings['popup-border-radius'] ) {
			$css .= '
			#ecome-mapper-' . $id . '.ecome-mapper .ecome-pin .ecome-popup-footer a {border-radius: 0 0 ' . (int)$settings['popup-border-radius'] . 'px ' . (int)$settings['popup-border-radius'] . 'px;}';
		}
		if ( $settings['image-effect'] == 'mask' ) {
			$css .= '
			#ecome-mapper-' . $id . ' .mask {';
			if ( $settings['mask-color'] ) {
				$css .= 'background: ' . esc_attr( $settings['mask-color'] ) . ';';
			}
			$css .= '}';
		}
		// Generate CSS rules for each pin.
		if ( $pins ) {
			foreach ( $pins as $pin ) {
				// Popup width & height
				$css .= '
			#ecome-mapper-' . $id . ' #ecome-pin-' . esc_attr( $pin['settings']['id'] ) . ' .ecome-popup {';
				if ( isset( $pin['settings']['popup-width'] ) && (int)$pin['settings']['popup-width'] > 0 ) {
					$css .= 'width: ' . (int)$pin['settings']['popup-width'] . 'px;';
				}
				if ( isset( $pin['settings']['popup-height'] ) && (int)$pin['settings']['popup-height'] > 0 ) {
					$css .= 'min-height: ' . (int)$pin['settings']['popup-height'] . 'px;';
				}
				$css .= '}';
				// Pin style setting
				$css .= '
			#ecome-mapper-' . $id . ' #ecome-pin-' . esc_attr( $pin['settings']['id'] ) . ' .icon-pin {';
				if ( isset( $pin['settings']['bg-color'] ) ) {
					$css .= 'background: ' . esc_attr( $pin['settings']['bg-color'] ) . ';';
				}
				if ( isset( $pin['settings']['icon-color'] ) ) {
					$css .= 'color: ' . esc_attr( $pin['settings']['icon-color'] ) . ';';
				}
				if ( isset( $pin['settings']['icon-size'] ) ) {
					$css .= 'font-size: ' . (int)esc_attr( $pin['settings']['icon-size'] ) . 'px;';
					$css .= 'width: ' . (int)esc_attr( $pin['settings']['icon-size'] ) * 1.2 . 'px;';
					$css .= 'line-height: ' . (int)esc_attr( $pin['settings']['icon-size'] ) * 1.2 . 'px;';
				}
				if ( isset( $pin['settings']['border-width'] ) && (int)$pin['settings']['border-width'] > 0 ) {
					$css .= 'box-shadow: 0 0 0 ' . (int)esc_attr( $pin['settings']['border-width'] ) . 'px ' . esc_attr( $pin['settings']['border-color'] ) . ';';
				}
				$css .= '}';
				// Pin hover setting
				$css .= '
			#ecome-mapper-' . $id . ' #ecome-pin-' . esc_attr( $pin['settings']['id'] ) . ' .icon-pin:hover {';
				if ( isset( $pin['settings']['bg-color-hover'] ) ) {
					$css .= 'background: ' . esc_attr( $pin['settings']['bg-color-hover'] ) . ';';
				}
				if ( isset( $pin['settings']['icon-color-hover'] ) ) {
					$css .= 'color: ' . esc_attr( $pin['settings']['icon-color-hover'] ) . ';';
				}
				$css .= '}';
				// Hover text
				$css .= '
                #ecome-mapper-' . $id . ' #ecome-pin-' . esc_attr( $pin['settings']['id'] ) . ' .text__area:hover,
                 #ecome-mapper-' . $id . ' #ecome-pin-' . esc_attr( $pin['settings']['id'] ) . ' img:hover{ ';
				$css .= '}';
			}
		}
		$css .= '
		</style>';

		return $css;
	}
}
