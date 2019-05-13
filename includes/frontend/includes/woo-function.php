<?php
/***
 * Core Name: WooCommerce
 * Version: 1.0.0
 * Author: Khanh
 */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 *
 * CUSTOM PRODUCT VIDEO, 360deg
 */
add_action( 'woocommerce_before_single_product_summary', 'ecome_show_product_extent', 10 );
if ( !function_exists( 'ecome_show_product_extent' ) ) {
	function ecome_show_product_extent()
	{
		global $product;
		$product_meta = get_post_meta( $product->get_id(), '_custom_product_woo_options', true );
		if ( isset( $product_meta['product_options'] ) && $product_meta['product_options'] == 'video' && isset( $product_meta['video_product_url'] ) && $product_meta['video_product_url'] != '' ) {
			echo '<div class="product-video-button"><a href="' . esc_url( $product_meta['video_product_url'] ) . '">' . esc_html__( 'Watch Video', 'ecome' ) . '</a></div>';
		}
		if ( isset( $product_meta['product_options'] ) && $product_meta['product_options'] == '360deg' && isset( $product_meta['degree_product_gallery'] ) && $product_meta['degree_product_gallery'] != '' ) : ?>
			<?php
			$images = $product_meta['degree_product_gallery'];
			$images = explode( ',', $images );
			if ( empty( $images ) ) return;
			$id               = rand( 0, 999 );
			$title            = '';
			$frames_count     = count( $images );
			$images_js_string = '';
			?>
			<div id="product-360-view" class="product-360-view-wrapper mfp-hide">
				<div class="ecome-threed-view threed-id-<?php echo esc_attr( $id ); ?>">
					<?php if ( !empty( $title ) ): ?>
						<h3 class="threed-title"><span><?php echo esc_html( $title ); ?></span></h3>
					<?php endif ?>
					<ul class="threed-view-images">
						<?php if ( count( $images ) > 0 ): ?>
							<?php $i = 0;
							foreach ( $images as $img_id ): $i++; ?>
								<?php
								$img              = wp_get_attachment_image_src( $img_id, 'full' );
								$images_js_string .= "'" . $img[0] . "'";
								$width            = $img[1];
								$height           = $img[2];
								if ( $i < $frames_count ) {
									$images_js_string .= ",";
								}
								?>
							<?php endforeach ?>
						<?php endif ?>
					</ul>
					<div class="spinner">
						<span>0%</span>
					</div>
				</div>
				<script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        window.addEventListener('load',
                            function (ev) {
                                $('.threed-id-<?php echo esc_attr( $id ); ?>').ThreeSixty({
                                    totalFrames: <?php echo esc_attr( $frames_count ); ?>,
                                    endFrame: <?php echo esc_attr( $frames_count ); ?>,
                                    currentFrame: 1,
                                    imgList: '.threed-view-images',
                                    progress: '.spinner',
                                    imgArray: [<?php echo wp_specialchars_decode( $images_js_string ); ?>],
                                    height: <?php echo esc_attr( $height ); ?>,
                                    width: <?php echo esc_attr( $width ); ?>,
                                    responsive: true,
                                    navigation: true
                                });
                            }, false);
                    });
				</script>
			</div>
			<div class="product-360-button">
				<a href="#product-360-view"><span><?php echo esc_html__( '360 Degree', 'ecome' ); ?></span></a>
			</div>
		<?php
		endif;
	}
}