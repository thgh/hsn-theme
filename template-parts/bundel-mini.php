<?php
/**
 * Template part for displaying a small bundle
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package onderwijstermen
 */

$meta = get_post_meta($post->ID);
$number = $meta['bundelnummer'][0] ?: $post->ID - 1986;
// if (has_post_thumbnail( $post->ID ) ) {
//   $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' )[0];
// } else {
//   $image = false;
// }
$year = $meta['year'][0] ?: substr($post->post_date, 0, 4);
$thumb = get_the_post_thumbnail_url($post->ID, [194, 290]);
?>
<div class="cover">
  <article id="post-<?php the_ID(); ?>" <?php post_class('bundel-mini'); ?> style="background-image: <?php echo $thumb ? 'url('. $thumb .')' : 'none'; ?>">
    <div class="bundel-mini__num"><?php echo $number ?></div>
    <div class="bundel-mini__year"><?php echo $year ?></div>
    <header class="entry-header">
      <?php
      the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark"><span class="hidden">', '</span></a></h2>' );
      ?>
    </header><!-- .entry-header -->

    <div class="entry-content">


      <?php
      wp_link_pages( array(
        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'hsn-theme' ),
        'after'  => '</div>',
      ) );
      ?>
    </div><!-- .entry-content -->
  </article><!-- #post-<?php the_ID(); ?> -->
</div>