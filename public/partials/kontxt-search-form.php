<?php

/**
 * Provide a updated search form if enabled by admin
 *
 * @since      1.0.7
 * @package    Kontxt
 * @subpackage Kontxt/public
 * @author     Michael Bordash <mbordash@realnetworks.com>
 */


?>

<form role="search" method="get" class="kontxt-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="kontxt-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>"><?php esc_html_e( 'Search for:', 'kontxt' ); ?></label>
	<input type="search" id="kontxt-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="search-field" placeholder="<?php echo esc_attr__( 'Ask a question &hellip;', 'kontxt' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'kontxt' ); ?>"><?php echo esc_html_x( 'Search', 'submit button', 'kontxt' ); ?></button>
</form>
