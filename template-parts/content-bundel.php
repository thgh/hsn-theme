<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package hsn-theme
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <div class="col-3">
      <?php hsn_theme_post_thumbnail(); ?>
    </div>
    <div class="col-9">
      <header class="entry-header">
        <?php
        if ( is_singular() ) :
          the_title( '<h1 class="entry-title">', '</h1>' );
        else :
          the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif;

        if ( 'post' === get_post_type() ) :
          ?>
          <div class="entry-meta">
            <?php
            hsn_theme_posted_on();
            hsn_theme_posted_by();
            ?>
          </div><!-- .entry-meta -->
        <?php endif; ?>
      </header><!-- .entry-header -->
        
      <div class="entry-content">
        <?php
        the_content( sprintf(
          wp_kses(
            /* translators: %s: Name of current post. Only visible to screen readers */
            __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'hsn-theme' ),
            array(
              'span' => array(
                'class' => array(),
              ),
            )
          ),
          get_the_title()
        ) );

        wp_link_pages( array(
          'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'hsn-theme' ),
          'after'  => '</div>',
        ) );
        ?>
      </div><!-- .entry-content -->

      <footer class="entry-footer">
        <?php hsn_theme_entry_footer(); ?>
      </footer><!-- .entry-footer -->
    </div>
  </div>
</article><!-- #post-<?php the_ID(); ?> -->