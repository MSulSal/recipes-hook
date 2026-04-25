<?php
get_header();
?>
<section class="rht-card">
	<h1 class="rht-page-title"><?php bloginfo( 'name' ); ?></h1>
	<p class="rht-page-subtitle"><?php esc_html_e( 'Use the Recipes link to browse recipe PDFs.', 'recipes-hook-theme' ); ?></p>
	<p><a class="rpl-view-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to Recipes', 'recipes-hook-theme' ); ?></a></p>
</section>
<?php
get_footer();
