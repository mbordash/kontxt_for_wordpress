<?php

/**
 * Provide a public-facing set of content recommendations based on KONTXT machine learning, opt-in by site manager
 *
 * @since      1.0.7
 * @package    Kontxt
 * @subpackage Kontxt/public
 * @author     Michael Bordash <mbordash@realnetworks.com>
 */

if( class_exists( 'WooCommerce', false )  ) {
	if( is_product() ) {

		?>

		<div id="kontxt_product_recs" style="display: none">

			<h3><span><?php esc_attr_e( 'Recommendations for you', 'wp_public_style' ); ?></span></h3>

            <div id="kontxt_recs_objects"></div>

		</div>

		<?php
	}
}

if ( get_post_type() === 'post' ) {

?>

    <div id="kontxt_content_recs" style="display: none">

        <h3><span><?php esc_attr_e( 'Recommendations for you', 'wp_public_style' ); ?></span></h3>

        <div id="kontxt_recs_objects"></div>

    </div>

<?php

}

?>