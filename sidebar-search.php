<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package hsn-theme
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
  return;
}
?>

<aside id="secondary" class="widget-area widget-area--search">
  <section id="search-2" class="widget widget_search"><form role="search" method="get" class="search-form" action="<?php echo get_home_url() ?>">
    <label>
      <span class="screen-reader-text">Zoeken naar:</span>
      <input type="search" class="search-field" placeholder="Zoeken..." value="" name="q" v-model="filter.search" @focus="onFocus">
    </label>
    <button type="submit" class="search-submit" @click.prevent>Zoeken</button>
  </form></section>
</aside><!-- #secondary -->
