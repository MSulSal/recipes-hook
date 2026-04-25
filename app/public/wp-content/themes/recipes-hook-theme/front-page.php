<?php
get_header();
?>
<?php
if ( shortcode_exists( 'recipe_pdf_library' ) ) {
	echo do_shortcode( '[recipe_pdf_library show_header="no"]' );
} else {
	?>
	<div class="rpl-empty-state">
		<h2><?php esc_html_e( 'Recipe plugin inactive', 'recipes-hook-theme' ); ?></h2>
		<p><?php esc_html_e( 'Activate the Recipe PDF Library plugin to display recipes here.', 'recipes-hook-theme' ); ?></p>
	</div>
	<?php
}

get_footer();
