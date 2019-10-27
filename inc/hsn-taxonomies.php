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

class homepage_search
{
    public function __construct()
    {
        $base = 'homepage-search';
        register_rest_route('hsn-theme', '/homepage-search', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_homepage_search'),
        ));
    }

    public function get_homepage_search($object)
    {
        $postIds = explode(',', $_GET['posts'] ?: '2090,2078');
        $postIds = array_map('intval', $postIds);

        global $wpdb;
        $query = "
          SELECT post.ID as id, meta.meta_value as bundelnummer
          FROM ".$wpdb->prefix."posts post
          INNER JOIN ".$wpdb->prefix."posts parent ON post.post_parent = parent.ID
          INNER JOIN ".$wpdb->prefix."postmeta meta ON meta.post_id = parent.ID
          WHERE post.ID IN (" . implode(',', $postIds) . ")
          AND meta.meta_key = 'bundelnummer'";
 
        $nummers = $wpdb->get_results($query);
        return new WP_REST_Response($nummers, 200);
    }
}

class bijdrage_taxonomies
{
    public function __construct()
    {
        $base = 'bijdrage-taxonomies';
        register_rest_route('hsn-theme', '/' . $base, array(
            'methods' => 'GET',
            'callback' => array($this, 'get_bijdrage_taxonomies'),
        ));
    }

    public function get_bijdrage_taxonomies($object)
    {
        $return = [];
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
        foreach ($taxonomies as $key => $taxonomy_key) {
            $allTerms = get_terms($taxonomy_key);
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
              'key' => $taxonomy_key,
              'terms' => array_values($terms),
            ];
        }
        return new WP_REST_Response($return, 200);
    }
}

class bijdrage_redirect
{
    public function __construct()
    {
        register_rest_route('hsn-theme', '/bijdrage-at-page', [
            'methods' => 'GET',
            'callback' => [$this, 'get_bijdrage_redirect'],
        ]);
    }

    public function get_bijdrage_redirect($object)
    {
        $return = [];
        $not = isset($_GET['not']) ? $_GET['not'] : '';
        $year = isset($_GET['year']) ? $_GET['year'] : '';
        $target = isset($_GET['target']) ? $_GET['target'] : 'next';
        $parent = isset($_GET['parent']) ? $_GET['parent'] : '';
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : '';
        $next = $target == 'next';

        if (empty($parent) && !empty($year)) {
            getParentByYear();
        }

        $posts = get_posts([
            'numberposts' => 4,
            'post_type'   => 'bijdrage',
            'post_parent' => $parent,
            'orderby'     => 'meta_value_num',
            'order'       => $next ? 'ASC' : 'DESC',
            'meta_key'    => 'page_first',
            'meta_query'  => [
                'relation' => 'AND',
                [
                    'key'     => 'page_first',
                    'compare' => $next ? '>=' : '<=',
                    'value'   => $pagina,
                    'type'    => 'numeric',
                ],
            ],
        ]);
        // Don't show current article
        $posts = array_values(array_filter($posts, function ($post) use ($not) {
            return $not != $post->ID;
        }));
        $postIds = array_map(function ($post) use ($not) {
            return get_permalink($post);
        }, $posts);
        $postFirst = array_map(function ($post) use ($not) {
            // return $post->post_parent;
            return $post->page_first;
        }, $posts);

        // $article
        if ($posts[0]) {
            wp_redirect(get_permalink($posts[0]), 302);
            exit();   
        }

        $parentPost = get_post($parent);
        if (!empty($parentPost))  {
            wp_redirect(get_permalink($parentPost), 302);
            exit();
        }
        
        wp_redirect(get_home_url(), 302);
        exit();

        // $return = compact('year', 'pagina', 'parent', 'postFirst', 'postIds', '_GET', 'object');
        // return new WP_REST_Response($return, 200);
    }
}

add_action('rest_api_init', function () {
    $bijdrage_taxonomies = new bijdrage_taxonomies;
    $bijdrage_redirect = new bijdrage_redirect;
    $homepage_search = new homepage_search;
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
