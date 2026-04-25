<?php
/**
 * Single recipe PDF display.
 *
 * @package Recipe_PDF_Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rpl_filter_single_recipe_content( string $content ): string {
	if ( ! is_singular( 'recipe_pdf' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	wp_enqueue_style(
		'rpl-frontend',
		RPL_PLUGIN_URL . 'assets/css/recipe-pdf-library.css',
		array(),
		RPL_VERSION
	);

	$post_id      = get_the_ID();
	$pdf_url      = rpl_get_recipe_pdf_url( $post_id );
	$pdf_filename = rpl_get_recipe_pdf_filename( $post_id );
	$categories   = get_the_terms( $post_id, 'recipe_category' );
	$tags         = get_the_terms( $post_id, 'recipe_tag' );
	$back_url     = rpl_get_recipes_back_url();

	ob_start();
	?>
	<div class="rpl-recipe-detail">
		<header class="rpl-recipe-detail__header">
			<h2><?php echo esc_html( get_the_title() ); ?></h2>
			<p><?php esc_html_e( 'Recipe PDF details and quick actions.', 'recipe-pdf-library' ); ?></p>
		</header>
		<div class="rpl-recipe-detail__meta">
			<?php rpl_render_term_list( $categories, 'rpl-recipe-card__terms' ); ?>
			<?php rpl_render_term_list( $tags, 'rpl-recipe-card__tags' ); ?>
		</div>

		<?php if ( trim( wp_strip_all_tags( $content ) ) ) : ?>
			<div class="rpl-recipe-detail__note">
				<?php echo wp_kses_post( $content ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $pdf_url ) : ?>
			<div class="rpl-pdf-actions">
				<a class="rpl-view-button" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Open PDF', 'recipe-pdf-library' ); ?>
				</a>
				<a class="rpl-secondary-button" href="<?php echo esc_url( $pdf_url ); ?>" download>
					<?php esc_html_e( 'Download PDF', 'recipe-pdf-library' ); ?>
				</a>
				<a class="rpl-clear-link" href="<?php echo esc_url( $back_url ); ?>">
					<?php esc_html_e( 'Back to recipes', 'recipe-pdf-library' ); ?>
				</a>
			</div>
			<div class="rpl-pdf-viewer">
				<iframe title="<?php echo esc_attr( get_the_title() ); ?>" src="<?php echo esc_url( $pdf_url ); ?>"></iframe>
			</div>
			<?php if ( $pdf_filename ) : ?>
				<p class="rpl-recipe-detail__filename"><?php echo esc_html( $pdf_filename ); ?></p>
			<?php endif; ?>
		<?php else : ?>
			<div class="rpl-empty-state">
				<h2><?php esc_html_e( 'PDF unavailable', 'recipe-pdf-library' ); ?></h2>
				<p><?php esc_html_e( 'This recipe does not have a PDF attached yet, or the file was removed from the media library.', 'recipe-pdf-library' ); ?></p>
			</div>
			<p>
				<a class="rpl-clear-link" href="<?php echo esc_url( $back_url ); ?>">
					<?php esc_html_e( 'Back to recipes', 'recipe-pdf-library' ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}
add_filter( 'the_content', 'rpl_filter_single_recipe_content', 20 );

function rpl_get_recipes_back_url(): string {
	$referer = wp_get_referer();

	if ( $referer && 0 === strpos( $referer, home_url() ) ) {
		return $referer;
	}

	$archive = get_post_type_archive_link( 'recipe_pdf' );

	return $archive ? $archive : home_url( '/' );
}
