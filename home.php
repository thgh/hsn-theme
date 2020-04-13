<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package onderwijstermen
 */

get_header();
?>
  <div id="primary" class="content-area">
    <main id="main" class="site-main container">
      <h1>Onderwijstermenlijst</h1>
      <p>De onderwijstermenlijst bevat verklaringen van een 900-tal onderwijstermen uit <b>Vlaanderen</b>, <b>Nederland</b> en <b>Suriname</b></p>
    </main><!-- #main -->

    <?php get_sidebar('search'); ?>
  </div><!-- #primary -->

<script>
window.restUrl = <?php echo json_encode(get_rest_url()) ?>;
</script>
<?php
// wp_register_script('vue', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', []);
// wp_register_script('taxonomies-filter', get_template_directory_uri() . '/js/taxonomies.js', ['vue'], '1');
// wp_enqueue_script('taxonomies-filter');

get_footer();

