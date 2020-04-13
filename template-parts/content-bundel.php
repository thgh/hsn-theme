<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package onderwijstermen
 */
$pageOffset = (int) get_post_meta($post->ID, 'page_offset', true);
$pdfId = get_post_meta($post->ID, 'pdf', true);
if (!empty($pdfId)) {
  $pdf = get_post($pdfId);
}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <div class="col-12 col-md-3 pt-4 d-none d-md-block single-bundel-aside">
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
        <?php if (isset($pdf)): ?>
          <a class="download-pdf" href="<?php echo $pdf->guid; ?>" download target="_blank"><b>Download bundel</b> (PDF)</a>
        <?php else: ?>
          PDF download is niet beschikbaar
        <?php endif ?>
      </div><!-- .entry-content -->

      <footer class="entry-footer">
        <?php hsn_theme_entry_footer(); ?>

      </footer><!-- .entry-footer -->

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
                <?php if (!empty(intval($first))): ?>
                  <span class="article-mini__num"><?php echo roman2($first, $pageOffset) ?></span>
                <?php endif?>
                <h3><?php the_title(); ?></h3>
                <p><?php echo $author ?? '-' ?></p>
              </article>
            </a>
          <?php endwhile; ?>
          <!-- end of the loop -->

          <?php wp_reset_postdata(); ?>

        <?php else : ?>
          <p><?php esc_html_e( 'De bijdrages in deze bundel zijn niet afzonderlijk beschikbaar.' ); ?></p>
          <?php if (isset($pdf)): ?>
            <div class="embed-container embed-pdf">
              <embed class="iframe-pdf" type="application/pdf" src="<?php echo $pdf->guid; ?>" type="application/pdf">
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="sticky-bottom">
        <?php the_post_navigation(); ?>
      </div>
    </div>
  </div>
</article><!-- #post-<?php the_ID(); ?> -->
