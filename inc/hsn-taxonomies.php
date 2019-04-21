<?php

function cptui_register_my_cpts_bijdrage() {
  $args = [
    'label'                 => __( 'Bundel', '1fix' ),
    'labels'                => [
      'name'                  => _x( 'Bundels', 'Post Type General Name', '1fix' ),
      'singular_name'         => _x( 'Bundel', 'Post Type Singular Name', '1fix' ),
      'menu_name'             => __( 'Bundels', '1fix' ),
      'name_admin_bar'        => __( 'Bundels', '1fix' )
    ],
    "supports" => array( "title", "editor", "thumbnail" ),
    'hierarchical'          => true,
    'public'                => true
  ];
  
  register_post_type( 'bundel', $args );

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
    "hierarchical" => false,
    // "rewrite" => array( "slug" => "/%year%/%postname%/", "with_front" => true ),
    "query_var" => true,
    "supports" => array( "title", "editor" ),
    "taxonomies" => [
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
    ],
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
    $slug = str_replace(' ', '_', $t);
    register_taxonomy($slug, ['bijdrage'], [
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
      'rewrite' => ['slug' => $slug, 'with_front' => true],
      'show_admin_column' => false,
      'show_in_rest' => true,
      'rest_base' => $slug,
      'rest_controller_class' => 'WP_REST_Terms_Controller',
      'show_in_quick_edit' => false,
    ]);
  }
}
add_action('init', 'register_hsn_custom_taxonomies');


// Metabox bundel > bijdrage
function my_add_meta_boxes() {
  add_meta_box( 'bijdrage-parent', 'Bundel', 'bijdrage_attributes_meta_box', 'bijdrage', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'my_add_meta_boxes' );
function bijdrage_attributes_meta_box( $post ) {
  $post_type_object = get_post_type_object( $post->post_type );
  $pages = wp_dropdown_pages( ['post_type' => 'bundel', 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __( '(no parent)' ), 'sort_column'=> 'menu_order, post_title', 'echo' => 0 ] );
  if ( ! empty( $pages ) ) {
    echo $pages;
  }
}

class taxonomies_terms
{
    public function __construct()
    {
        $base = 'bijdrage-taxonomies';
        register_rest_route('hsn-theme', '/' . $base, array(
            'methods' => 'GET',
            'callback' => array($this, 'get_taxonomies_terms'),
        ));
    }

    public function get_taxonomies_terms($object)
    {
        $return = array();
        // $return['categories'] = get_terms('category');
 //        $return['tags'] = get_terms('post_tag');
        // Get taxonomies
        $args = array(
            'public' => true,
            '_builtin' => false,
            'object_type' => ['bijdrage']
        );
        $output = 'names'; // or objects
        $operator = 'or'; // 'and' or 'or'
        $taxonomies = get_taxonomies();
        foreach ($taxonomies as $key => $taxonomy_name) {
            $allTerms = get_terms($taxonomy_name);
            // $terms = $allTerms;
            $terms = array_filter($allTerms, function ($a) {
              return empty($a->parent);
            });
            foreach ($terms as $term) {
              $term->terms = array_values(array_filter($allTerms, function ($a) use ($term) {
                return $a->parent == $term->term_id;
              }));
            }
            $return[] = [
              'name' => $taxonomy_name,
              'terms' => array_values($terms),
            ];
        }
        return new WP_REST_Response($return, 200);
    }
}

add_action('rest_api_init', function () {
    $taxonomies_terms = new taxonomies_terms;
});


// Rewrite

// function my_add_rewrite_rules() {
//   add_rewrite_tag('%bijdrage%', '([^/]+)', 'bijdrage=');
//   add_permastruct('bijdrage', '/bijdrage/%bundel%/%bijdrage%', false);
//   add_rewrite_rule('^bijdrage/([^/]+)/([^/]+)/?','index.php?bijdrage=$matches[2]','top');
// }
// add_action( 'init', 'my_add_rewrite_rules' );

// function my_permalinks($permalink, $post, $leavename) {
//   $post_id = $post->ID;
//   if($post->post_type != 'bijdrage' || empty($permalink) || in_array($post->post_status, array('draft', 'pending', 'auto-draft')))
//     return $permalink;
//   $parent = $post->post_parent;
//   $parent_post = get_post( $parent );
//   $permalink = str_replace('%bundel%', $parent_post->post_name, $permalink);
//   return $permalink;
// }
// add_filter('post_type_link', 'my_permalinks', 10, 3);
