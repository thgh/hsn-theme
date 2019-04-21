<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package hsn-theme
 */

get_header();
?>
bundel
  <div id="primary" class="content-area">
    <main id="main" class="site-main container">

    <?php
    while ( have_posts() ) :
      the_post();

      get_template_part( 'template-parts/content', get_post_type() );
?>
<br><br>
Vorige volgende:
<?php
      the_post_navigation();

    endwhile; // End of the loop.
    ?>

    </main><!-- #main -->
  </div><!-- #primary -->

<?php
get_footer();
