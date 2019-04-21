<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package hsn-theme
 */

get_header();
?>
  <div id="primary" class="content-area">
    <main id="main" class="site-main container">
      <div class="row">
        <div class="col-12 col-md-3">
          <h2>Filters</h2>

          <?php get_sidebar('search'); ?>
<!--           <dl>
            <dt>Domein</dt>
            <dd>mondelinge taalvaardigheid</dd>
            <dd>spreken</dd> -->
          <?php
            // $taxonomyNames =get_object_taxonomies(get_post());
            // $taxonomies = [];
            // foreach ($taxonomyNames as $taxonomy) {
            //   $t = (array) get_taxonomy( $taxonomy );
            //   if ( empty( $t['label'] ) ) {
            //           $t['label'] = $taxonomy;
            //   }
            //   if ( empty( $t['args'] ) ) {
            //           $t['args'] = array();
            //   }
              
            //   if ( false === $terms ) {
            //     $terms = wp_get_object_terms( $post->ID, $taxonomy, $t['args'] );
            //   }
            //   foreach ( $terms as $term ) {
            //     var_dump($terms);
            //     $taxonomies[$t['label']][] = [
            //       'group' => $t['label'],
            //       'link' => esc_attr(get_term_link($term)),
            //       'name' => $term,
            //     ];
            //   }
            // }

            //   $taxonomyNames = [
            //     'aantal_respondenten',
            //     'doelgroep',
            //     'domein',
            //     'land',
            //     'leeftijd',
            //     'dataverzameling',
            //     'onderwijstype',
            //     'respondenten',
            //     'tekstsoort',
            //     'thema',
            //   ];


            // foreach ($taxonomyNames as $taxonomy) {
            //   // $t = (array) get_taxonomy( $taxonomy );
            //   $terms = get_object_term_cache( get_post()->ID, $taxonomy);
            //   if ( false === $terms ) {
            //     $terms = wp_get_object_terms( get_post()->ID, $taxonomy);
            //   }
            //   foreach ( $terms as $term ) {
            //     $taxonomies[$term->taxonomy][] = [
            //       'group' => $term->taxonomy,
            //       'link' => esc_attr(get_term_link($term)),
            //       'name' => $term,
            //     ];
            //   }
            // }
            // echo '</pre>';
            // foreach ($taxonomies as $taxonomy => $terms) {
            //   echo '<dt>' . $taxonomy . '</dt>';
            //   if (is_array($terms)) {
            //     foreach ($terms as $term) {
            //       echo '<dd>' . $term['name']->name . '</dd>';
            //     }
            //   } else {
            //     var_dump($terms);
            //   }

            // }
          ?>
          </dl>
        
          <div class="taxonomies" v-cloak>
            <div class="taxonomy" v-for="taxonomy in lookup" v-if="taxonomy.terms.length">
              <label class="taxonomy__name">
                <input type="checkbox" v-model="taxonomy.open" style="display: none;">
                {{taxonomy.name}}
                <svg class="taxonomy__arrow" :class="{ 'taxonomy__arrow--open': taxonomy.open }" viewBox="0 0 24 24"><path fill="#fff" d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z" /></svg>
              </label>
              <div class="terms" v-if="taxonomy.open">
                <div class="term" v-for="term in taxonomy.terms">
                  <label class="term__name">
                    <span class="term__check"><input type="checkbox" v-model="term.selected"></span>
                    {{term.name}}
                  </label>
                  <button class="subterm-toggle" v-if="term.terms && term.terms.length" @click="term.open = !term.open">
                    <svg class="term__arrow" :class="{ 'term__arrow--open': term.open }" viewBox="0 0 24 24"><path fill="#999" d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z" /></svg>
                  </button>
                  <div class="subterms" v-if="term.open">
                    <div class="term term--sub" v-for="subterm in term.terms">
                      <label class="term__name">
                        <span class="term__check"><input type="checkbox" v-model="subterm.selected"></span>
                        {{subterm.name}}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <script></script>
        </div>
        <div class="col-12 col-md-9">
          <h2>Recente bundels</h2>
          <div class="bundel-minis">
            <?php
            $wp_query = new WP_Query( [ 'post_type' => 'bundel', 'posts_per_page' => 50] );

            $lazycount = 0;
            while ( have_posts() ) :
              the_post();

              get_template_part( 'template-parts/bundel-mini', get_post_type() );
              $lazycount++;
            endwhile; // End of the loop.
            ?>
          </div>
        </div>
      </div>

    </main><!-- #main -->
  </div><!-- #primary -->

<script>
window.restUrl = <?php echo json_encode(get_rest_url()) ?>;
</script>
<?php
wp_register_script('vue', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', []);
wp_register_script('taxonomies-filter', get_template_directory_uri() . '/js/taxonomies.js', ['vue'], '1');
wp_enqueue_script('taxonomies-filter');

get_footer();

