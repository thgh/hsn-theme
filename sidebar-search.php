<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package onderwijstermen
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
  return;
}

$searchQuery = get_search_query();
$searchValue = strlen($searchQuery) > 1 ? $searchQuery : '';
$searchQueryLetter = lcfirst(substr($firstLetter ?: $searchQuery, 0, 1));
?>
<aside id="secondary" class="widget-area widget-area--search">
  <div class="container">
    <section id="search-2" class="widget widget_search">
      <form role="search" method="get" class="search-form" action="<?php echo get_home_url() ?>" @submit.prevent>

        <div class="form-group form-group--20em">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Zoeken" aria-label="Zoeken" name="s" value="<?php echo esc_attr($searchValue) ?>">
            <div class="input-group-append">
              <button type="submit" class="btn btn-outline-secondary" type="button">Zoeken</button>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="country" class="screen-reader-text">Example multiple select</label>
          <select class="form-control" id="country">
            <option selected>Alle landen</option>
            <option>België</option>
            <option>Nederland</option>
            <option>Suriname</option>
          </select>
        </div>
      </form>

      <div class="letters">
        <?php foreach(range('a','z') as $letter){ ?>
          <a href="/?s=<?php echo $letter ?>" class="letter<?php echo $searchQueryLetter === $letter ? ' letter--active' : '' ?>"><?php echo ucfirst($letter) ?></a>
        <?php } ?>
      </div>

      <div class="termslist">
        <?php
        $terms = new WP_Query([
          'posts_per_page'   => 1000,
          'post_type'        => 'term',
          'post_title_starts_with' => $searchQuery ,
          //'meta_key'         => 'page_first',
          'order'            => 'ASC',
          'orderby'          => 'post_title',
        ]);
        
        if ( $terms->have_posts() ) : ?>

          <!-- the loop -->
          <?php while ( $terms->have_posts() ) : $terms->the_post(); ?>
            <?php
            $country = get_post_meta($post->ID, 'country', true);
            ?>
            <a href="<?php echo esc_url(get_permalink()) ?>" class="term-link">
                <?php if (!empty(($country))): ?>
                  <span class="article-mini__num"><?php echo $country ?></span>
                <?php endif?>
                <?php the_title(); ?>
            </a>
          <?php endwhile; ?>
          <!-- end of the loop -->

          <?php wp_reset_postdata(); ?>

        <?php else : ?>
          <p><?php esc_html_e( 'De bijdrages in deze bundel zijn niet afzonderlijk beschikbaar.' ); ?></p>
          <?php if (isset($pdf)): ?>
            <div class="embed-container embed-pdf">
              <embed class="iframe-pdf" type="application/pdf" src="<?php echo $pdf->guid; ?>" type="application/pdf">
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

    </section>
  </div>
</aside><!-- #secondary -->
