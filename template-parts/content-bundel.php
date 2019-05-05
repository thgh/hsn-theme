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
    <div class="col-12 col-md-3 pt-4 d-none d-md-block">
      <?php
      get_template_part( 'template-parts/bundel-mini', get_post_type() );
      ?>
    </div>
    <div class="col-12 col-md-9">
      <header class="entry-header">
        <?php
        if ( is_singular() ) :
          the_title( '<h1 class="entry-title">', '</h1>' );
        else :
          the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif;
        ?>
      </header><!-- .entry-header -->
        
      <div class="entry-content">
        <a class="download-pdf" href="/.pdf"><b>Download bundel</b> (PDF)</a>
      </div><!-- .entry-content -->

      <div class="articles">
        <?php
        $articles = new WP_Query([
          'posts_per_page'   => 100,
          'post_type'        => 'bijdrage',
          'post_parent'      => $post->ID,
          'meta_key'         => 'page_first',
          'order'            => 'ASC',
          'orderby'          => 'meta_value_num',
        ]);

        if ( $articles->have_posts() ) : ?>

          <!-- the loop -->
          <?php while ( $articles->have_posts() ) : $articles->the_post(); ?>
            <?php
            $meta = get_post_meta($post->ID);
            $author = get_post_meta($post->ID, 'auteur', true);
            $first = get_post_meta($post->ID, 'page_first', true);
            ?>
            <a href="<?php echo esc_url(get_permalink()) ?>" class="text-decoration-none">
              <article class="article-mini">
                <span class="article-mini__num"><?php echo $first ?? '-' ?></span>
                <h3><?php the_title(); ?></h3>
                <p><?php echo $author ?? '-' ?></p>
              </article>
            </a>
          <?php endwhile; ?>
          <!-- end of the loop -->

          <?php wp_reset_postdata(); ?>

        <?php else : ?>
          <p><?php esc_html_e( 'De bijdrages in deze bundel zijn niet beschikbaar.' ); ?></p>
        <?php endif; ?>
      </div>

      <footer class="entry-footer">
        <?php hsn_theme_entry_footer(); ?>

        <?php the_post_navigation(); ?>
      </footer><!-- .entry-footer -->
    </div>
  </div>
</article><!-- #post-<?php the_ID(); ?> -->
