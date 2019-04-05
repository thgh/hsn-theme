<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package hsn-theme
 */
error_reporting(E_ALL);

get_header();
?>
  <nav class="container" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Bundels</a></li>
      <li class="breadcrumb-item"><a href="#">28ste</a></li>
      <li class="breadcrumb-item active" aria-current="page">Artikelnaam</li>
    </ol>
  </nav>

  <div id="primary" class="content-area">
    <main id="main" class="site-main container">
      <div class="row">
        <div class="col-12 col-md-3">
          <h2>Labels</h2>
          <dl>
            <dt>Domein</dt>
            <dd>mondelinge taalvaardigheid</dd>
            <dd>spreken</dd>
          <?php
// $taxonomyNames =get_object_taxonomies(get_post());
// $taxonomies = [];
// foreach ($taxonomyNames as $taxonomy) {
//   $t = (array) get_taxonomy( $taxonomy );
//   if ( empty( $t['label'] ) ) {
//           $t['label'] = $taxonomy;
//   }
//   if ( empty( $t['args'] ) ) {
//           $t['args'] = array();
//   }
  
//   if ( false === $terms ) {
//     $terms = wp_get_object_terms( $post->ID, $taxonomy, $t['args'] );
//   }
//   foreach ( $terms as $term ) {
//     var_dump($terms);
//     $taxonomies[$t['label']][] = [
//       'group' => $t['label'],
//       'link' => esc_attr(get_term_link($term)),
//       'name' => $term,
//     ];
//   }
// }

  $taxonomyNames = [
    'aantal_respondenten',
    'doelgroep',
    'domein',
    'land',
    'leeftijd',
    'dataverzameling',
    'onderwijstype',
    'respondenten',
    'tekstsoort',
    'thema',
  ];


foreach ($taxonomyNames as $taxonomy) {
  // $t = (array) get_taxonomy( $taxonomy );
  $terms = get_object_term_cache( $post->ID, $taxonomy);
  if ( false === $terms ) {
    $terms = wp_get_object_terms( $post->ID, $taxonomy);
  }
  foreach ( $terms as $term ) {
    $taxonomies[$term->taxonomy][] = [
      'group' => $term->taxonomy,
      'link' => esc_attr(get_term_link($term)),
      'name' => $term,
    ];
  }
}
echo '</pre>';
foreach ($taxonomies as $taxonomy => $terms) {
  echo '<dt>' . $taxonomy . '</dt>';
  if (is_array($terms)) {
    foreach ($terms as $term) {
      echo '<dd>' . $term['name']->name . '</dd>';
    }
  } else {
    var_dump($terms);
  }

}
          ?>
          </dl>
        </div>
        <div class="col-12 col-md-9">
          <?php
          while ( have_posts() ) :
            the_post();

            get_template_part( 'template-parts/content', get_post_type() );

            the_post_navigation();

            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
              comments_template();
            endif;

          endwhile; // End of the loop.
          ?>
        </div>
      </div>

    </main><!-- #main -->
  </div><!-- #primary -->

<?php
get_sidebar();
get_footer();
