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
        <div class="col-12 col-md-4 col-lg-3 h2-md">
          <h2 class="d-none d-md-block pt-0 mt-0">Filters</h2>

          <?php get_sidebar('search'); ?>

          <div class="taxonomies" :class="{ searchFocus: searchFocus }" v-cloak>
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
          <div class="d-block d-md-none" v-if="searchFocus" v-cloak>
            <button @click.prevent="searchFocus = !searchFocus">{{ searchFocus ? 'Filters inklappen' : 'Filters uitklappen' }}</button>
          </div>
        </div>
        <div v-if="!filtering" class="col-12 col-md-8 col-lg-9 h2-md">
          <h2 class="d-none d-md-block pt-0 mt-0">Recente bundels</h2>
          <div class="bundel-minis">
            <?php
            $wp_query = new WP_Query( [ 'post_type' => 'bundel', 'posts_per_page' => 50] );

            $lazycount = 0;
            while ( have_posts() ) :
              the_post();

              echo '<div class="col">';
              get_template_part( 'template-parts/bundel-mini', get_post_type() );
              echo '</div>';

              $lazycount++;
            endwhile; // End of the loop.
            ?>
          </div>
        </div>
        <div v-else v-cloak class="col-12 col-md-8 col-lg-9 h2-md">
          <h2 class="d-none d-md-block pt-0 mt-0">Zoekresultaten {{ articles.length ? '(' + articles.total + ')' : '' }}</h2>
          <div class="results">
            <a :href="article.link" class="text-decoration-none" v-for="article in articles">
              <article class="article-mini">
                <span class="article-mini__num">{{ article.page_first || '' }}</span>
                <h3 v-html="article.title.rendered"></h3>
                <p>{{ article.auteur || '' }}</p>
                <p v-if="articles.length === 1"></p>
              </article>
            </a>
          </div>
          <div v-if="hasMore">
            <button class="btn" @click="more">Toon meer resultaten</button>
          </div>

<!--           <p>
            <button class="btn btn-secondary">Toon meer resultaten</button>
          </p> -->
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

