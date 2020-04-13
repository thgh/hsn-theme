<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package onderwijstermen
 */

get_header();
$parent = empty($post->post_parent) ? null : get_post($post->post_parent);
?>
<div class="breadcrumb-border">
  <nav class="container" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo get_home_url() ?>">Alle bundels</a></li>
      <li class="breadcrumb-item"><a href="<?php echo get_permalink($parent) ?>"><?php echo $parent->post_title ?: '?' ?></a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo $post->post_title ?></li>
    </ol>
  </nav>
</div>

  <div id="primary" class="content-area">
    <main id="main" class="site-main container">
      <div class="row">
        <div class="col-12 col-md-9 order-md-2">
          <?php
          while ( have_posts() ) :
            the_post();

            get_template_part( 'template-parts/content', get_post_type() );
          endwhile; // End of the loop.
          ?>
        </div>
        <div class="col-12 col-md-3 order-md-1 single-bijdrage-aside">
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

$taxonomies = [];
foreach ($taxonomyNames as $taxonomy) {
  // $t = (array) get_taxonomy( $taxonomy );
  $terms = get_object_term_cache( get_post()->ID, $taxonomy);
  if ( false === $terms ) {
    $terms = wp_get_object_terms( get_post()->ID, $taxonomy);
  }
  foreach ( $terms as $term ) {
    if (empty($term->parent)) {
      $taxonomies[$term->taxonomy][$term->term_id]['name'] = $term->name;
    } else {
      $taxonomies[$term->taxonomy][$term->parent]['children'][] = $term->name;
    }
  }
  // Check if root terms are present
  foreach ( $taxonomies as $taxonomy => $parent_terms ) {
    foreach ( $parent_terms as $term_id => $parent_term ) {
      if (empty($parent_term['name'])) {
        $term = get_term( $term_id);
        $taxonomies[$taxonomy][$term_id]['name'] = $term->name;
      }
    }
  }
}

$out = '';
foreach ($taxonomies as $taxonomy => $parent_terms) {
  $out .= '<dt>' . $taxonomy . '</dt>';
  if (is_array($parent_terms)) {
    foreach ($parent_terms as $term_id => $parent_term) {
      $out .= '<dd>' . $parent_term['name'] . '</dd>';
      if (!empty($parent_term['children'])) {
        foreach ($parent_term['children'] as $name) {
          $out .= '<dd class="label-child">' . $name . '</dd>';
        }
      }
    }
  } else {
    var_dump($terms);
  }

}
if (!empty($out)) {
  ?>
  <h2>Labels</h2>
  <dl class="labels"><?php echo $out ?></dl>
<?php } ?>

<?php if (!empty($parent)) { ?>
          <h2 class="d-md-none d-lg-block">Dit artikel is onderdeel van</h2>
          <h2 class="d-none d-md-block d-lg-none">Onderdeel van</h2>
          <a class="media parent" href="<?php echo esc_url(get_permalink($parent->ID)) ?>">
            <img src="<?php echo get_the_post_thumbnail_url($parent->ID, [194, 290]) ?>" alt="">
            <div class="media-body">
              <?php echo $parent->post_title; ?>
              &middot;
              <b><?php echo get_post_meta($parent->ID, 'year', true) ?: substr($parent->post_date, 0, 4); ?></b>
            </div>
          </a>
<?php } ?>



        </div>
      </div>

    </main><!-- #main -->
  </div><!-- #primary -->

<?php
get_footer();
