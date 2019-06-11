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
$page_first = (int) get_post_meta($post->ID, 'page_first', true);
$page_last =(int)  get_post_meta($post->ID, 'page_last', true);

$year = get_post_meta($parent->ID, 'year', true);
$pdfId = get_post_meta($parent->ID, 'pdf', true);
$offset = 36;
if (!empty($pdfId)) {
  $pdf = get_post($pdfId);
  $pageOffset = (int) get_post_meta($parent->ID, 'page_offset', true);
  $offset = $pageOffset;
  $pdfUrl = $pdf->guid . '#page=' . ($offset + $page_first);
  // var_dump($pdfUrl);
  // var_dump($offset);
  // var_dump($pageOffset);
}
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
    <p>
      <?php echo $author ?>
      &nbsp;&middot;&nbsp;
      <a href="<?php echo get_permalink($parent) ?>"><?php echo $parent->post_title ?></a>
      &nbsp;&middot;&nbsp;
      <?php echo $year ?>
      &nbsp;&middot;&nbsp;
      pagina <?php echo $page_first ?>
      <?php if ($page_last > $page_first) echo ' - ' . $page_last ?>
    </p>

<?php
global $wpdb;
$isbn = get_post_meta($parent->ID, 'isbn', true);



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

if (count($pages) && isset($pdfUrl)) {
  // var_dump($isbn);
  // var_dump($page_first);
  // var_dump($page_last);
  ?>
    <p>
      <button class="btn article-pdf-toggle" onclick="togglePDF()">Toon originele PDF</button>
    </p>
    <div class="article-pdf" data-url=<?php echo json_encode($pdfUrl) ?>></div>
  <?php
}

$rendered = [];
$isScraped = false;
$prevPage = 'first';
foreach ($pages as $key => $page) {
  if (in_array($page->page, $rendered)) {
    continue;
  }
  array_push($rendered, $page->page);
  $contents = $page->contents;
  $pos = strpos($contents, $title);
  if ($pos > 0) {
    $contents = substr($contents, $pos + strlen($title));
  }
  echo '<div class="textual">';
  // echo '<div class="textual__num">' . $page->page . '</div>';
  // echo '<div class="textual__toggle"><button class=active>Tekst</button><button>Afbeelding</button></div>';

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
  $isScraped = true;
}

if (!$isScraped) {
  if (isset($pdf)) {
    ?>
    <p>
      Dit artikel is alleen in PDF beschikbaar.
    </p>
      <div class="embed-container embed-pdf">
        <embed class="iframe-pdf" src="<?php echo $pdfUrl ?>" type="application/pdf">
      </div>
    <?php
  } else {
    ?>
    <p>
      Dit artikel is niet beschikbaar.
    </p>
    <?php
  }
} else {}

?>
    <div class="sticky-bottom">
      <nav class="navigation post-navigation" role="navigation">
        <h2 class="screen-reader-text">Artikelnavigatie</h2>
        <div class="nav-links">
          <div class="nav-previous">
            <a href="/wp-json/hsn-theme/bijdrage-at-page?pagina=<?php echo $page_first ?>&parent=<?php echo $parent->ID ?>&target=prev&not=<?php echo $post->ID ?>" rel="prev">Vorig artikel</a>
          </div>
          <div class="nav-next">
            <a href="/wp-json/hsn-theme/bijdrage-at-page?pagina=<?php echo $page_last ?>&parent=<?php echo $parent->ID ?>&target=next&not=<?php echo $post->ID ?>" rel="next">Volgend artikel</a>
          </div>
        </div>
      </nav>
    </div>
  </div><!-- .entry-content -->

  <footer class="entry-footer">
  </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->

<script>
  function togglePDF(onload) {
    window.pdfToggle = !window.pdfToggle
    if (window.pdfToggle) {
      var url = $('.article-pdf').data('url')
      $('.article-pdf-toggle').text('PDF verbergen')
      $('.article-pdf').html('<div class="embed-container embed-pdf"><embed class="iframe-pdf" src="' + url + '" type="application/pdf"></div>')
    } else {
      $('.article-pdf-toggle').text('Toon originele PDF')
      $('.article-pdf').html('')
    }
    // Save setting
    if (!onload) {
      localStorage.hsnPreferPDF = window.pdfToggle ? 'true' : ''
    }
  }
  try {
    if (localStorage.hsnPreferPDF) togglePDF(true)
  } catch(e){}
</script>