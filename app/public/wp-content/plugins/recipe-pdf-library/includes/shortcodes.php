<?php
/**
 * Frontend recipe library shortcode.
 *
 * @package Recipe_PDF_Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rpl_register_shortcodes(): void {
	add_shortcode( 'recipe_pdf_library', 'rpl_render_recipe_library_shortcode' );
}
add_action( 'init', 'rpl_register_shortcodes' );

function rpl_render_recipe_library_shortcode(): string {
	$search        = isset( $_GET['recipe_search'] ) ? sanitize_text_field( wp_unslash( $_GET['recipe_search'] ) ) : '';
	$category_slug = isset( $_GET['recipe_category'] ) ? sanitize_title( wp_unslash( $_GET['recipe_category'] ) ) : '';
	$recipes       = rpl_get_library_recipes( $category_slug );
	$recipes       = rpl_filter_recipes_by_search( $recipes, $search );
	$categories    = get_terms(
		array(
			'taxonomy'   => 'recipe_category',
			'hide_empty' => true,
		)
	);

	ob_start();
	?>
	<div class="rpl-library">
		<header class="rpl-library-header">
			<h2><?php esc_html_e( 'Recipe Library', 'recipe-pdf-library' ); ?></h2>
			<p><?php esc_html_e( 'Browse, search, and open recipe PDFs.', 'recipe-pdf-library' ); ?></p>
		</header>
		<form class="rpl-search-form" method="get">
			<div class="rpl-search-field">
				<label for="rpl-recipe-search"><?php esc_html_e( 'Search recipes', 'recipe-pdf-library' ); ?></label>
				<input
					type="search"
					id="rpl-recipe-search"
					name="recipe_search"
					value="<?php echo esc_attr( $search ); ?>"
					placeholder="<?php esc_attr_e( 'Search by title, filename, category, or tag', 'recipe-pdf-library' ); ?>"
				>
			</div>
			<div class="rpl-category-field">
				<label for="rpl-recipe-category"><?php esc_html_e( 'Category', 'recipe-pdf-library' ); ?></label>
				<select id="rpl-recipe-category" name="recipe_category">
					<option value=""><?php esc_html_e( 'All categories', 'recipe-pdf-library' ); ?></option>
					<?php if ( ! is_wp_error( $categories ) ) : ?>
						<?php foreach ( $categories as $category ) : ?>
							<option value="<?php echo esc_attr( $category->slug ); ?>" <?php selected( $category_slug, $category->slug ); ?>>
								<?php echo esc_html( $category->name ); ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<button class="rpl-search-button" type="submit"><?php esc_html_e( 'Search', 'recipe-pdf-library' ); ?></button>
			<?php if ( $search || $category_slug ) : ?>
				<a class="rpl-clear-link" href="<?php echo esc_url( strtok( rpl_current_url(), '?' ) ); ?>">
					<?php esc_html_e( 'Clear', 'recipe-pdf-library' ); ?>
				</a>
			<?php endif; ?>
		</form>
		<?php rpl_render_active_filters( $search, $category_slug, $categories ); ?>

		<?php if ( empty( $recipes ) ) : ?>
			<div class="rpl-empty-state">
				<?php if ( $search || $category_slug ) : ?>
					<h2><?php esc_html_e( 'No recipes found', 'recipe-pdf-library' ); ?></h2>
					<p><?php esc_html_e( 'Try a different search term or choose another category.', 'recipe-pdf-library' ); ?></p>
				<?php else : ?>
					<h2><?php esc_html_e( 'No recipes yet', 'recipe-pdf-library' ); ?></h2>
					<p><?php esc_html_e( 'Recipes will appear here after they are added in WordPress admin.', 'recipe-pdf-library' ); ?></p>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<p class="rpl-result-count">
				<?php
				printf(
					esc_html(
						/* translators: %d: recipe count. */
						_n( '%d recipe', '%d recipes', count( $recipes ), 'recipe-pdf-library' )
					),
					absint( count( $recipes ) )
				);
				?>
			</p>
			<div class="rpl-recipe-grid">
				<?php foreach ( $recipes as $recipe ) : ?>
					<?php rpl_render_recipe_card( $recipe ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

function rpl_get_library_recipes( string $category_slug ): array {
	$args = array(
		'post_type'              => 'recipe_pdf',
		'post_status'            => 'publish',
		'posts_per_page'         => 100,
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);

	if ( $category_slug ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'recipe_category',
				'field'    => 'slug',
				'terms'    => $category_slug,
			),
		);
	}

	return get_posts( $args );
}

function rpl_filter_recipes_by_search( array $recipes, string $search ): array {
	if ( '' === trim( $search ) ) {
		return $recipes;
	}

	$needle = strtolower( $search );

	return array_values(
		array_filter(
			$recipes,
			static function ( WP_Post $recipe ) use ( $needle ): bool {
				$haystack = array(
					$recipe->post_title,
					rpl_get_recipe_pdf_filename( $recipe->ID ),
					rpl_get_recipe_pdf_index_text( $recipe->ID ),
				);

				$terms = wp_get_post_terms( $recipe->ID, array( 'recipe_category', 'recipe_tag' ) );

				if ( ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$haystack[] = $term->name;
						$haystack[] = $term->slug;
					}
				}

				return false !== strpos( strtolower( implode( ' ', array_filter( $haystack ) ) ), $needle );
			}
		)
	);
}

function rpl_render_recipe_card( WP_Post $recipe ): void {
	$pdf_url      = rpl_get_recipe_pdf_url( $recipe->ID );
	$pdf_filename = rpl_get_recipe_pdf_filename( $recipe->ID );
	$published    = get_the_date( get_option( 'date_format' ), $recipe );
	$categories   = get_the_terms( $recipe->ID, 'recipe_category' );
	$tags         = get_the_terms( $recipe->ID, 'recipe_tag' );
	?>
	<article class="rpl-recipe-card">
		<div class="rpl-recipe-card__body">
			<h2 class="rpl-recipe-card__title">
				<a href="<?php echo esc_url( get_permalink( $recipe ) ); ?>"><?php echo esc_html( get_the_title( $recipe ) ); ?></a>
			</h2>
			<?php if ( $pdf_filename ) : ?>
				<p class="rpl-recipe-card__filename"><?php echo esc_html( $pdf_filename ); ?></p>
			<?php endif; ?>
			<div class="rpl-recipe-card__meta">
				<span><?php echo esc_html__( 'PDF Recipe', 'recipe-pdf-library' ); ?></span>
				<?php if ( $published ) : ?>
					<span><?php echo esc_html( $published ); ?></span>
				<?php endif; ?>
			</div>
			<?php rpl_render_term_list( $categories, 'rpl-recipe-card__terms' ); ?>
			<?php rpl_render_term_list( $tags, 'rpl-recipe-card__tags' ); ?>
		</div>
		<div class="rpl-recipe-card__actions">
			<a class="rpl-view-button" href="<?php echo esc_url( get_permalink( $recipe ) ); ?>">
				<?php esc_html_e( 'View Recipe', 'recipe-pdf-library' ); ?>
			</a>
			<?php if ( $pdf_url ) : ?>
				<a class="rpl-secondary-link" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Open PDF', 'recipe-pdf-library' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</article>
	<?php
}

function rpl_render_term_list( $terms, string $class_name ): void {
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return;
	}
	?>
	<div class="<?php echo esc_attr( $class_name ); ?>">
		<?php foreach ( $terms as $term ) : ?>
			<span><?php echo esc_html( $term->name ); ?></span>
		<?php endforeach; ?>
	</div>
	<?php
}

function rpl_get_recipe_pdf_attachment_id( int $post_id ): int {
	return absint( get_post_meta( $post_id, RPL_PDF_META_KEY, true ) );
}

function rpl_get_recipe_pdf_filename( int $post_id ): string {
	$attachment_id = rpl_get_recipe_pdf_attachment_id( $post_id );

	if ( ! $attachment_id ) {
		return '';
	}

	$file = get_attached_file( $attachment_id );

	if ( ! is_string( $file ) || '' === $file ) {
		return '';
	}

	return wp_basename( $file );
}

function rpl_get_recipe_pdf_url( int $post_id ): string {
	$attachment_id = rpl_get_recipe_pdf_attachment_id( $post_id );

	if ( ! $attachment_id || ! rpl_attachment_is_pdf( $attachment_id ) ) {
		return '';
	}

	$url = wp_get_attachment_url( $attachment_id );

	return $url ? $url : '';
}

function rpl_current_url(): string {
	global $wp;

	return home_url( add_query_arg( array(), $wp->request ) );
}

function rpl_render_active_filters( string $search, string $category_slug, $categories ): void {
	$has_filters = ( '' !== $search || '' !== $category_slug );

	if ( ! $has_filters ) {
		return;
	}

	$category_name = '';

	if ( ! is_wp_error( $categories ) ) {
		foreach ( $categories as $category ) {
			if ( $category_slug === $category->slug ) {
				$category_name = $category->name;
				break;
			}
		}
	}
	?>
	<div class="rpl-active-filters" aria-label="<?php esc_attr_e( 'Active filters', 'recipe-pdf-library' ); ?>">
		<?php if ( '' !== $search ) : ?>
			<span class="rpl-filter-pill"><?php echo esc_html( sprintf( __( 'Search: %s', 'recipe-pdf-library' ), $search ) ); ?></span>
		<?php endif; ?>
		<?php if ( '' !== $category_slug ) : ?>
			<span class="rpl-filter-pill"><?php echo esc_html( sprintf( __( 'Category: %s', 'recipe-pdf-library' ), $category_name ? $category_name : $category_slug ) ); ?></span>
		<?php endif; ?>
	</div>
	<?php
}
