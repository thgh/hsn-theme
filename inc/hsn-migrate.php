<?php

// Manage import panel
add_action( 'admin_menu', 'my_admin_menu' );
function my_admin_menu() {
  add_menu_page( 'Import', 
    'Importeren', 'import', 
    'import', 'my_admin_import', 
    'dashicons-admin-settings' );
}

// Keep authors in sync
add_action( 'post_updated' , 'filter_post_data' , 10, 2 );
function filter_post_data( $ID , $post ) {
  $out = importAuthor($post);
}


function my_admin_import() {
  ?>
    <h1>Import</h1>
    <p>Dit kan 30 seconden duren.</p>
    <form action="?page=import&action=pdf" method="POST">
      <p><button>Importeer PDF inhoud</button></p>
    </form>
    <form action="?page=import&action=author" method="POST">
      <p><button>Importeer auteurs</button></p>
    </form>
  <?php

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'pdf') {
    echo '<pre>';
    var_dump(importPDFcontent());
    echo '</pre>';
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'author') {
    echo '<pre>';
    var_dump(importAuthors());
    echo '</pre>';
  }
}
// Author

function importAuthors() {
  global $wpdb;
  $posts = $wpdb->get_results($wpdb->prepare("
    SELECT ID, post_parent, post_excerpt, post_content
    FROM " . $wpdb->prefix . "posts
    WHERE post_type LIKE 'bijdrage'", ''));
  return array_map('importAuthor', $posts);
}

function importAuthor($post) {
  if (empty($post->post_parent)) {
    return -1;
  }

  // Get bijdrage details
  global $wpdb;
  $author = get_post_meta($post->ID, 'auteur', true);
  if (empty($author)) {
    return -2;
  }

  $content = includeAuthorInContent($author, $post->post_content);
  if ($content === $post->post_content) {
    return -3;
  }

  $wpdb->get_results($wpdb->prepare("
    UPDATE {$wpdb->prefix}posts
    SET post_content = %s
    WHERE ID = %d", $content, $post->ID));
  return $author;
}

function includeAuthorInContent($author, $content) {
  // Replace current author
  $start = strpos($content, '<!--##');
  $legacy = strpos($content, '[legacy_import]');
  if ($start !== false) {
    $mid = substr($content, $start);
    $end = strpos($mid, '##-->');
    $content = substr($content, 0, $start) . '<!--##' . $author . substr($mid, $end);
  } else if ($legacy !== false) {
    $content = str_replace(
      '[legacy_import]',
      '<!--##' . $author . '##-->' . '[legacy_import]',
      $content
    );
  } else {
    // Append author
    $content .= PHP_EOL . PHP_EOL . '<!--##' . $author . '##-->';
  }
  return $content;
}

// PDF

function importPDFcontent() {
  global $wpdb;
  $posts = $wpdb->get_results($wpdb->prepare("
    SELECT ID, post_parent, post_excerpt, post_content
    FROM " . $wpdb->prefix . "posts
    WHERE post_type LIKE 'bijdrage'", ''));
  return array_map('enablePDFSearchPost', $posts);
}

function enablePDFSearchPost($post) {
  if (empty($post->post_parent)) {
    return -1;
  }

  // Get bijdrage details
  global $wpdb;
  $page_first = (int)get_post_meta($post->ID, 'page_first', true);
  $page_last = (int)get_post_meta($post->ID, 'page_last', true);
  $isbn = get_post_meta($post->post_parent, 'isbn', true);
  $pages = $wpdb->get_results($wpdb->prepare("
    SELECT * FROM hsnbundels_paginas
    WHERE isbn LIKE %s AND page >= %s AND page <= %s
    ORDER BY page ASC
  ", $isbn, $page_first, $page_last));

  // Not all bijdrages have legacy pages
  if (empty($pages)) {
    return -2;
  }

  // Render legacy page text content
  $rendered = [];
  $imported = '';
  foreach ($pages as $key => $page) {
    // Remove duplicate pages
    if (in_array($page->page, $rendered)) {
      continue;
    }
    array_push($rendered, $page->page);

    // Remove title
    $contents = $page->contents;
    $pos = strpos($contents, $title);
    if ($pos > 0) {
      $contents = substr($contents, $pos + strlen($title));
    }

    // DOMify
    $dom = new DOMDocument;
    @$dom->loadHTML($contents);
    $imported .= $dom->textContent;
  }

  // Check if nothing to import
  if (empty($imported)) {
    return -3;
  }

  // Check if already imported
  $content = $post->post_content;
  if (strpos($content, $imported) !== false) {
    return -4;
  }

  // Remove previous legacy import
  $index = strpos($post->post_content, '[legacy_import]');
  if ($index !== false) {
    $content = substr($content, 0, $index);
  }
  
  $wpdb->get_results($wpdb->prepare("
    UPDATE {$wpdb->prefix}posts
    SET post_content = %s
    WHERE ID = %d", $content . "[legacy_import]\n\n" . $imported, $post->ID));
  return strlen($imported);
}



// class migrate_pdf_content {
//   public function __construct() {
//     register_rest_route('hsn-theme', '/migrate_pdf_content', [
//       'methods' => 'GET',
//       'callback' => [$this, 'post_migrate_pdf_content'],
//     ]);
//   }

//   public function post_migrate_pdf_content($object) {
//     if (!is_admin()) {
//       $user=wp_get_current_user();
//       $userdata=current_user_can('administrator');
//       $da=is_logged_in();
//       return new WP_REST_Response(compact("userdata", "user", 'da'), 403);
//     }

//     $out = importPDFcontent();
//     return new WP_REST_Response($out, 200);
//   }
// }
// add_action('rest_api_init', function () {
//   $migrate_pdf_content = new migrate_pdf_content;
// });

