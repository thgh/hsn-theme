<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package hsn-theme
 */

get_header();
$parent = get_post($post->post_parent);
?>
<div class="breadcrumb-border">
  <nav class="container" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo get_home_url() ?>">Alle bundels</a></li>
      <li class="breadcrumb-item"><a href="<?php echo get_permalink($parent) ?>"><?php echo $parent->post_title ?></a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo $post->post_title ?></li>
    </ol>
  </nav>
</div>

  <div id="primary" class="content-area">
    <main id="main" class="site-main container">
      <div class="row">
        <div class="col-12 col-md-3">
          <h2>Labels</h2>
          <dl class="labels">
          <?php
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
  $terms = get_object_term_cache( get_post()->ID, $taxonomy);
  if ( false === $terms ) {
    $terms = wp_get_object_terms( get_post()->ID, $taxonomy);
  }
  foreach ( $terms as $term ) {
    $taxonomies[$term->taxonomy][] = [
      'group' => $term->taxonomy,
      'link' => esc_attr(get_term_link($term)),
      'name' => $term,
    ];
  }
}
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
$parentthumb = get_the_post_thumbnail_url($parent->ID, [194, 290]);
          ?>
          </dl>
          <h2>Dit artikel is onderdeel van</h2>
          <div class="media parent">
            <img src="<?php echo $parentthumb ?>" alt="">
            <div class="media-body">
              <?php echo $parent->post_title; ?>
              &middot;
              <b><?php echo substr($parent->post_date, 0, 4); ?></b>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-9">
          <?php
          while ( have_posts() ) :
            the_post();

            get_template_part( 'template-parts/content', get_post_type() );
          endwhile; // End of the loop.
          ?>
        </div>
      </div>

    </main><!-- #main -->
  </div><!-- #primary -->

<?php
get_footer();
