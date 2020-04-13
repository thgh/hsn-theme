<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package onderwijstermen
 */

get_header();
?>
  <div class="breadcrumb-border">
    <nav class="container" aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo get_home_url() ?>">Alle bundels</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $post->post_title ?></li>
      </ol>
    </nav>
  </div>

  <div id="primary" class="content-area">
    <main id="main" class="site-main container">

    <?php
    while ( have_posts() ) :
      the_post();

      get_template_part( 'template-parts/content', get_post_type() );
    endwhile; // End of the loop.
    ?>

    </main><!-- #main -->
  </div><!-- #primary -->

<?php
get_footer();
