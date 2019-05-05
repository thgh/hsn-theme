<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package hsn-theme
 */
global $parent;
$author = get_post_meta($post->ID, 'auteur', true);
$title = $post->post_title;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
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

  <?php hsn_theme_post_thumbnail(); ?>

  <div class="entry-content">
    <?php echo $author ?>
    <a href="<?php echo get_permalink($parent) ?>"><?php echo $parent->post_title ?></a>

<?php
global $wpdb;
$isbn = get_post_meta($parent->ID, 'isbn', true);

$page_first = (int) get_post_meta($post->ID, 'page_first', true);
$page_last =(int)  get_post_meta($post->ID, 'page_last', true);


  $pages = $wpdb->get_results(
    $wpdb->prepare(
      "
    SELECT * FROM hsnbundels_paginas
    WHERE isbn LIKE %s AND page >= %s AND page <= %s
    ORDER BY page ASC
  ",
      $isbn,
      $page_first,
      $page_last
    )
  );

if (!count($pages)) {
  var_dump($isbn);
  var_dump($page_first);
  var_dump($page_last);
}

$prevPage = 'first';
foreach ($pages as $key => $page) {
  if ($page->page == $prevPage) {
    continue;
  }
  $contents = $page->contents;
  $pos = strpos($contents, $title);
  if ($pos > 0) {
    $contents = substr($contents, $pos + strlen($title));
  }
  echo '</pre>';
  echo '<div class="textual">';
  // echo '<div class="textual__num">' . $page->page . '</div>';
  echo '<div class="textual__toggle"><button class=active>Tekst</button><button>Afbeelding</button></div>';

  $dom = new DOMDocument;
  @$dom->loadHTML($contents);
  $xpath = new DOMXPath($dom);
  $nodes = $xpath->query('//@*');
  foreach ($nodes as $node) {
      $node->parentNode->removeAttribute($node->nodeName);
  }
  echo $dom->saveHTML();
  // echo $contents;
  // echo strip_tags($contents, '<p>');
  echo '</div>';
  $prevPage = $page->page;
}

?>
  </div><!-- .entry-content -->

  <footer class="entry-footer">
  </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
