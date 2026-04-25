<?php
get_header();
?>
<section class="rht-hero">
	<h1 class="rht-page-title"><?php esc_html_e( 'Recipe Archive', 'recipes-hook-theme' ); ?></h1>
	<p class="rht-page-subtitle"><?php esc_html_e( 'All published recipe PDFs.', 'recipes-hook-theme' ); ?></p>
</section>
<?php
if ( shortcode_exists( 'recipe_pdf_library' ) ) {
	echo do_shortcode( '[recipe_pdf_library]' );
}
get_footer();
