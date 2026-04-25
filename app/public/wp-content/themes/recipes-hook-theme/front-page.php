<?php
get_header();
?>
<section class="rht-hero">
	<h1 class="rht-page-title"><?php esc_html_e( 'Recipe Library', 'recipes-hook-theme' ); ?></h1>
	<p class="rht-page-subtitle"><?php esc_html_e( 'Search your recipes and browse in gallery or list view.', 'recipes-hook-theme' ); ?></p>
</section>

<?php
if ( shortcode_exists( 'recipe_pdf_library' ) ) {
	echo do_shortcode( '[recipe_pdf_library]' );
} else {
	?>
	<div class="rpl-empty-state">
		<h2><?php esc_html_e( 'Recipe plugin inactive', 'recipes-hook-theme' ); ?></h2>
		<p><?php esc_html_e( 'Activate the Recipe PDF Library plugin to display recipes here.', 'recipes-hook-theme' ); ?></p>
	</div>
	<?php
}

get_footer();
