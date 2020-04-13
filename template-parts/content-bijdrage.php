<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package onderwijstermen
 */
global $parent;
$author = get_post_meta($post->ID, 'auteur', true);
$title = $post->post_title;
$page_first = (int) get_post_meta($post->ID, 'page_first', true);
$page_last =(int)  get_post_meta($post->ID, 'page_last', true);
$articlePDF =(int)  get_post_meta($post->ID, 'pdf', true);
$bundelnummer = intval(get_post_meta($parent->ID, 'bundelnummer', true));
if (!empty($articlePDF)) {
  $articlePDFurl = wp_get_attachment_url($articlePDF);
}

// Load legacy pdf's
if (empty($articlePDFurl) && intval($post->ID) < 1355 && $bundelnummer != 17) {
  $articlePDFurl = home_url( '/wp-content/legacy/' . $bundelnummer . '/hsnbundel-' . $bundelnummer . '_' . $post->ID . '.pdf' );
}

$year = get_post_meta($parent->ID, 'year', true);
$pdfId = get_post_meta($parent->ID, 'pdf', true);
$pageOffset = (int) get_post_meta($parent->ID, 'page_offset', true);
$offset = $pageOffset ?? 36;
if (!empty($pdfId)) {
  $pdf = get_post($pdfId);
  $bundelPDFurl = $pdf->guid . '#page=' . ($offset + $page_first);
  // var_dump($bundelPDFurl);
  // var_dump($offset);
  // var_dump($pageOffset);
}

function renderPDFcontents($pages, $classname) {
  $rendered = [];
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
    echo '<div class="' . $classname . '">';
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
    $isContentRendered = true;
  }

  return count($rendered);
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
      <?php
      $items = array_values(array_filter([
        $author,
        empty($post->post_parent) ? false : '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>',
        $year,
        empty($page_first) ? false : ('pagina ' . roman2($page_first, $offset) . ($page_last > $page_first ? ' - ' . roman2($page_last, $offset) : ''))
      ]));
      echo implode(' &nbsp;&middot;&nbsp; ', $items);
      ?>
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

if (!empty($bundelPDFurl) || !empty($articlePDF)) {
  ?>
    <p class="downloads">
      <?php if (!empty($articlePDFurl)): ?>
        <a class="download-pdf article-pdf-toggle" target="_blank" download href="<?php echo esc_url($articlePDFurl) ?>"><b>Download artikel</b></a>
      <?php elseif (!empty($bundelPDFurl)): ?>
        <a class="download-pdf article-pdf-toggle" target="_blank" download href="<?php echo esc_url($bundelPDFurl) ?>"><b>Download bundel</b></a>
      <?php endif ?>
      <?php hsn_theme_entry_footer(); ?>
    </p>
  <?php
} else {
  hsn_theme_entry_footer();
}

function contentful($value='')
{
  $start = strpos($value, '<!--##');
  $end = strpos($value, '##-->');
  $value = substr($value, 0, $start) . substr($value, $end + 5);
  $legacy = strpos($value, '[legacy_import]');
  return $legacy === false && strlen($value) > 20;
}

$isContentRendered = false;

// 1. post_content
if (contentful($post->post_content)) {
  the_content();
  $isContentRendered = true;
}

// 2. post->pdf
if (!$isContentRendered && !empty($articlePDFurl)) {
  ?>
      <div class="embed-container embed-pdf">
        <embed class="iframe-pdf" type="application/pdf" src="<?php echo esc_url($articlePDFurl) ?>" type="application/pdf">
      </div>
  <?php
  renderPDFcontents($pages, 'pdf-contents');
  $isContentRendered = true;
}

// 3. pdf scrape
if (!$isContentRendered) {
  $isContentRendered = !!renderPDFcontents($pages, 'textual');
}

// 4. bundel pdf
if (!$isContentRendered && isset($pdf)) {
  ?>
  <p>
    Dit artikel komt voor in onderstaande bundel:
  </p>
    <div class="embed-container embed-pdf">
      <embed class="iframe-pdf" type="application/pdf" src="<?php echo $bundelPDFurl ?>" type="application/pdf">
    </div>
  <?php
  $isContentRendered = true;
}

if (!$isContentRendered) {
  ?>
  <p>
    Dit artikel is niet beschikbaar.
  </p>
  <?php
}


$baseUrl = esc_url( home_url( '/wp-json/hsn-theme/bijdrage-at-page' ) );
?>
    <div class="sticky-bottom">
      <nav class="navigation post-navigation" role="navigation">
        <h2 class="screen-reader-text">Artikelnavigatie</h2>
        <div class="nav-links">
          <div class="nav-previous">
            <a href="<?php echo $baseUrl ?>?pagina=<?php echo $page_first ?>&parent=<?php echo $parent->ID ?>&target=prev&not=<?php echo $post->ID ?>" rel="prev">Vorig artikel</a>
          </div>
          <div class="nav-next">
            <a href="<?php echo $baseUrl ?>?pagina=<?php echo $page_last ?>&parent=<?php echo $parent->ID ?>&target=next&not=<?php echo $post->ID ?>" rel="next">Volgend artikel</a>
          </div>
        </div>
      </nav>
    </div>
  </div><!-- .entry-content -->

  <footer class="entry-footer">
  </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
