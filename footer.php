<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package hsn-theme
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="container">
			<div class="row">
				<div class="col-lg-4">
					<h5 class="mt-0">&copy; Taalunie</h5>
					Rechten voorbehouden
					<a style="font-size: 14px;" href="https://twitter.com/HSNconferentie?ref=bundels">
						<p class="pt-3">
							<img class="mr-2" style="width:30px" src="<?php echo esc_url( get_template_directory_uri()) ?>/img/twitter-icon.png" alt="">
							Volg ons op Twitter
						</p>
					</a>
				</div>
				<div class="col-lg-4">
					<div class="media">
						<img class="mr-3" src="<?php echo esc_url( get_template_directory_uri()) ?>/img/logo_nu-white.png" alt="">
						<div class="media-body">
							<h5 class="mt-0">Taalunie</h5>
							Samen versterken we het Nederlands en zorgen we ervoor dat zoveel mogelijk mensen het Nederlands optimaal kunnen gebruiken. 
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="media">
						<img class="mr-3" src="<?php echo esc_url( get_template_directory_uri()) ?>/img/hsn-logo.png" alt="">
						<div class="media-body">
							<h5 class="mt-0">HSN</h5>
							De Conferentie Het Schoolvak Nederlands wordt jaarlijks met steun van de Taalunie georganiseerd door de Stichting Conferenties Het Schoolvak Nederlands.
						</div>
					</div>
				</div>
			</div>
			<p style="margin-top: 100px;">Webapplicatie door <a href="https://eskidoos.be">ESKIDOOS</a></p>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
