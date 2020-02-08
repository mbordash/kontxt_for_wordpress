<?php

/**
 * Provide an updated search results template, opt-in by site manager
 *
 * @since      1.0.7
 * @package    Kontxt
 * @subpackage Kontxt/public
 * @author     Michael Bordash <mbordash@realnetworks.com>
 */


get_header(); ?>


    <div id="primary" class="content-area">
        hey now!
        <main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>

                <header class="page-header">
                    <h1 class="page-title">
						<?php
						/* translators: %s: search term */
						printf( esc_attr__( 'Search Results for: %s', 'kontxt' ), '<span>' . get_search_query() . '</span>' );
						?>
                    </h1>
                </header><!-- .page-header -->

				<?php
				get_template_part( 'loop' );

			else :

				get_template_part( 'content', 'none' );

			endif;
			?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php

if( has_action( 'storefront_sidebar' ) ) {

	do_action( 'storefront_sidebar' );

}

get_footer();