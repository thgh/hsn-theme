<?php

function cptui_register_my_cpts_bijdrage() {

  $labels = array(
    "name" => __( "Bijdragen", "hsn-theme" ),
    "singular_name" => __( "Bijdrage", "hsn-theme" ),
  );

  $args = array(
    "label" => __( "Bijdragen", "hsn-theme" ),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => true,
    "show_ui" => true,
    "delete_with_user" => false,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => true,
    "rewrite" => array( "slug" => "bijdrage", "with_front" => true ),
    "query_var" => true,
    "supports" => array( "title", "editor", "thumbnail" ),
    "taxonomies" => array( "domein", "doelgroep", "thema" ),
  );

  register_post_type( "bijdrage", $args );
}

add_action( 'init', 'cptui_register_my_cpts_bijdrage' );


function register_hsn_custom_taxonomies() {
  $taxonomies = [
    'aantal respondenten',
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

  foreach ($taxonomies as $t) {
    register_taxonomy(str_replace(' ', '_', $t), ['bijdrage'], [
      'label' => ucfirst($t),
      'labels' => [
        'name' => ucfirst($t),
        'singular_name' => ucfirst($t),
        'add_new_item' => 'Andere ' . $t . ' toevoegen'
      ],
      'public' => true,
      'publicly_queryable' => true,
      'hierarchical' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_nav_menus' => true,
      'query_var' => true,
      'rewrite' => ['slug' => $t, 'with_front' => true],
      'show_admin_column' => false,
      'show_in_rest' => true,
      'rest_base' => $t,
      'rest_controller_class' => 'WP_REST_Terms_Controller',
      'show_in_quick_edit' => false,
    ]);
  }
}
add_action('init', 'register_hsn_custom_taxonomies');
