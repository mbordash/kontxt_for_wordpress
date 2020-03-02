<?php
get_header();
global $wp_query;
?>
<section id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php

        if ( have_posts() ) :

            while ( have_posts() ) : the_post();

        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>><a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
            <?php
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'full' );
                }
            ?></a>

            <div class="kontxt-search-result-title"><a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php echo get_the_title(); ?> </a></div>

        </article><!-- #post-## -->

        <?php
            endwhile;
        ?>

        <article id="" <?php post_class(); ?>>

            <div class="alpha entry-title"><?php echo __( 'No results found.', 'kontxt' ); ?></div>

        </article><!-- #post-## -->

        <?php
            endif;
        ?>

    </main>
</section>

<?php

the_posts_pagination( array( 'mid_size' => 2 ) );

get_footer();
?>