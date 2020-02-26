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
	if(  !is_admin()  && is_product() ) {

		?>


		<div id="kontxt_product_recs" style="display: none">

			<input type="checkbox" id="kontxt_recs_showblock">
			<div id="kontxt_slideout">
				<label id="kontxt_slideout_tab" for="kontxt_recs_showblock" title="KONTXT Slider">
					<?php echo __( 'Recommendations', 'kontxt' ); ?>
				</label>
				<div id="kontxt_slideout_inner">
					<div id="kontxt_recs_objects"></div>
				</div>
			</div>

		</div>


		<?php
	}
}

if (  !is_admin() && get_post_type() === 'post' ) {

?>

    <div id="kontxt_content_recs" style="display: none">

	    <input type="checkbox" id="kontxt_recs_showblock">
	    <div id="kontxt_slideout">
		    <label id="kontxt_slideout_tab" for="kontxt_recs_showblock" title="KONTXT Slider">
			    <?php echo __( 'Recommendations', 'kontxt' ); ?>
		    </label>
		    <div id="kontxt_slideout_inner">
			    <div id="kontxt_recs_objects"></div>
		    </div>
	    </div>

    </div>

<?php

}

?>