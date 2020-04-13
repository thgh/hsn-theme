<?php

function cptui_register_custom_post_types() {
  register_post_type('term', [
    'label'                 => __( 'Term', 'hsn-theme' ),
    'labels'                => [
      'name'                => __( 'Termen', 'hsn-theme' ),
      'singular_name'       => __( 'Term', 'hsn-theme' ),
      'menu_name'           => __( 'Termen', 'hsn-theme' ),
      'name_admin_bar'      => __( 'Termen', 'hsn-theme' )
    ],
    'supports'              => ['title', 'editor', 'thumbnail'],
    'hierarchical'          => true,
    'public'                => true,
  ]);
}
add_action( 'init', 'cptui_register_custom_post_types' );


function post_title_starts_with_filter($where, $wp_query) {
    global $wpdb;
    if ( $search_term = $wp_query->get( 'post_title_starts_with' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . $wpdb->esc_like( $search_term ) . '%\'';
    }
    return $where;
}
add_filter( 'posts_where', 'post_title_starts_with_filter', 10, 2 );
