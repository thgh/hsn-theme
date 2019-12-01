<?php

use Spatie\PdfToText\Pdf;

require_once __DIR__ . '/../vendor/autoload.php';

// Keep authors in sync
add_action( 'post_updated' , 'ocr_pdf' , 10, 2 );
function ocr_pdf( $ID , $post ) {
  $articlePDF = (int) get_post_meta($post->ID, 'pdf', true);
  if (empty($articlePDF)) {
    return;
  }

  // Attachment path
  $articlePDFpath = get_attached_file($articlePDF);

  try {
    $text = Pdf::getText($articlePDFpath);
  } catch(Exception $e) {
    $text = '';
  }

  // Check if nothing to import
  if (empty($text)) {
    return -3;
  }

  // Check if already imported
  $content = $post->post_content;
  if (strpos($content, $text) !== false) {
    return -4;
  }

  // Remove previous author
  $index = strpos($content, "<!--##");
  if ($index !== false) {
    $content = substr($content, 0, $index);
  } else {
    // Remove previous legacy import v2
    $index = strpos($content, "<!-- wp:html -->\n[legacy_import]");
    if ($index !== false) {
      $content = substr($content, 0, $index);
    } else {
      // Remove previous legacy import v1
      $index = strpos($content, '[legacy_import]');
      if ($index !== false) {
        $content = substr($content, 0, $index);
      }
    }
  }

  // Disable legacy import when article is in post_content
  if (strlen($content) * 3 > strlen($text)) {
    return -5;
  }

  if (empty(trim($content))) {
    $content ="<!-- wp:paragraph -->\n\n<!-- /wp:paragraph -->\n";
  }
  
  try {
    global $wpdb;
    $wpdb->get_results($wpdb->prepare("
      UPDATE {$wpdb->prefix}posts
      SET post_content = %s
      WHERE ID = %d", $content . wrapHTMLblock($text), $post->ID));
  } catch(Exception $e) {
  }
  return strlen($text);

}


function wrapHTMLblock($text='')
{
  return "\n<!-- wp:html -->\n[legacy_import]\n" . $text . "\n<!-- /wp:html -->";
}
