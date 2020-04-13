<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package onderwijstermen
 */

?>

	</div><!-- #content -->

	<div class="container">
		<h3>Mist u een term?</h3>
	</div>

	<footer id="colophon" class="site-footer">
		<div class="container">
			<div class="row">
				<div class="col-lg-4">
					<h5 class="mt-0">&copy; Taalunie</h5>
					Rechten voorbehouden
				</div>
				<div class="col-lg-4">
					<a class="media" href="http://taalunie.org">
						<img class="mr-3" src="<?php echo esc_url( get_template_directory_uri()) ?>/img/logo_nu-white.png" alt="">
						<div class="media-body">
							<h5 class="mt-0">Taalunie</h5>
							<?php the_field('about_taalunie', 'option'); ?>
						</div>
					</a>
					<a style="font-size: 14px;margin-left: 86px;" href="https://twitter.com/taalunie?ref=hsnbundels">
						<p class="pt-3">
							<img class="mr-2" style="width:30px" src="<?php echo esc_url( get_template_directory_uri()) ?>/img/twitter-icon.png" alt="">
							Volg ons op Twitter
						</p>
					</a>
					<a style="font-size: 14px;margin-left: 86px;" href="https://nl.linkedin.com/company/nederlandse-taalunie?ref=hsnbundels">
						<p>
							<div class="icon mr-2">
								<svg viewBox="0 0 24 24">
							    <path fill="#fff" d="M21,21H17V14.25C17,13.19 15.81,12.31 14.75,12.31C13.69,12.31 13,13.19 13,14.25V21H9V9H13V11C13.66,9.93 15.36,9.24 16.5,9.24C19,9.24 21,11.28 21,13.75V21M7,21H3V9H7V21M5,3A2,2 0 0,1 7,5A2,2 0 0,1 5,7A2,2 0 0,1 3,5A2,2 0 0,1 5,3Z" />
								</svg>
							</div>
							Volg ons op Linkedin
						</p>
					</a>
				</div>
				<div class="col-lg-4">
					<a class="media" href="https://hsnconferentie.org">
						<img class="mr-3" src="<?php echo esc_url( get_template_directory_uri()) ?>/img/hsn-logo.png" alt="">
						<div class="media-body">
							<h5 class="mt-0">HSN</h5>
							<?php the_field('about_hsn', 'option'); ?>
						</div>
					</a>
					<a style="font-size: 14px;margin-left: 86px;" href="https://twitter.com/HSNconferentie?ref=hsnbundels">
						<p class="pt-3">
							<img class="mr-2" style="width:30px" src="<?php echo esc_url( get_template_directory_uri()) ?>/img/twitter-icon.png" alt="">
							Volg ons op Twitter
						</p>
					</a>
				</div>
			</div>
			<p style="margin-top: 100px;">Webapplicatie door <a href="https://eskidoos.be">ESKIDOOS</a></p>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
