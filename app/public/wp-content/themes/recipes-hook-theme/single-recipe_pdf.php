<?php
get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$post_id = get_the_ID();
		$author_id = (int) get_post_field( 'post_author', $post_id );

		if ( ! is_user_logged_in() || get_current_user_id() !== $author_id ) {
			?>
			<div class="rpl-empty-state">
				<h2><?php esc_html_e( 'Private recipe', 'recipes-hook-theme' ); ?></h2>
				<p><?php esc_html_e( 'This recipe belongs to another account. Log in to view your own recipe collection.', 'recipes-hook-theme' ); ?></p>
				<p><a class="rpl-view-button" href="<?php echo esc_url( home_url( '/login/' ) ); ?>"><?php esc_html_e( 'Go to Login', 'recipes-hook-theme' ); ?></a></p>
			</div>
			<?php
			continue;
		}

		$pdf_url = function_exists( 'rpl_get_recipe_pdf_url' ) ? rpl_get_recipe_pdf_url( $post_id ) : '';
		$pdf_name = function_exists( 'rpl_get_recipe_pdf_filename' ) ? rpl_get_recipe_pdf_filename( $post_id ) : '';
		$categories = get_the_terms( $post_id, 'recipe_category' );
		$tags = get_the_terms( $post_id, 'recipe_tag' );
		?>
		<div class="rpl-recipe-detail">
			<header class="rpl-recipe-detail__header">
				<h1><?php the_title(); ?></h1>
				<p><?php esc_html_e( 'Recipe PDF details and quick actions.', 'recipes-hook-theme' ); ?></p>
			</header>
			<div class="rpl-recipe-detail__meta">
				<?php if ( function_exists( 'rpl_render_term_list' ) ) : ?>
					<?php rpl_render_term_list( $categories, 'rpl-recipe-card__terms' ); ?>
					<?php rpl_render_term_list( $tags, 'rpl-recipe-card__tags' ); ?>
				<?php endif; ?>
			</div>

			<?php if ( trim( wp_strip_all_tags( get_the_content() ) ) ) : ?>
				<div class="rpl-recipe-detail__note"><?php the_content(); ?></div>
			<?php endif; ?>

			<?php if ( $pdf_url ) : ?>
				<div class="rpl-pdf-actions">
					<a class="rpl-view-button" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open PDF', 'recipes-hook-theme' ); ?></a>
					<a class="rpl-secondary-button" href="<?php echo esc_url( $pdf_url ); ?>" download><?php esc_html_e( 'Download PDF', 'recipes-hook-theme' ); ?></a>
					<a class="rpl-clear-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to recipes', 'recipes-hook-theme' ); ?></a>
				</div>
				<div class="rpl-pdf-viewer">
					<iframe title="<?php echo esc_attr( get_the_title() ); ?>" src="<?php echo esc_url( $pdf_url ); ?>"></iframe>
				</div>
				<?php if ( $pdf_name ) : ?>
					<p class="rpl-recipe-detail__filename"><?php echo esc_html( $pdf_name ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<div class="rpl-empty-state">
					<h2><?php esc_html_e( 'PDF unavailable', 'recipes-hook-theme' ); ?></h2>
					<p><?php esc_html_e( 'This recipe does not have a PDF attached yet, or the file was removed from media.', 'recipes-hook-theme' ); ?></p>
				</div>
				<a class="rpl-clear-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to recipes', 'recipes-hook-theme' ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	endwhile;
endif;

get_footer();
