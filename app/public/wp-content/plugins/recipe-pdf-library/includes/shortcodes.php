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

function rpl_render_recipe_library_shortcode( array $atts = array() ): string {
	$atts = shortcode_atts(
		array(
			'show_header' => 'yes',
		),
		$atts,
		'recipe_pdf_library'
	);

	$show_header = 'no' !== strtolower( (string) $atts['show_header'] );

	$search        = isset( $_GET['recipe_search'] ) ? sanitize_text_field( wp_unslash( $_GET['recipe_search'] ) ) : '';
	$category_slug = isset( $_GET['recipe_category'] ) ? sanitize_title( wp_unslash( $_GET['recipe_category'] ) ) : '';
	$view_mode     = isset( $_GET['recipe_view'] ) ? sanitize_key( wp_unslash( $_GET['recipe_view'] ) ) : 'gallery';
	$view_mode     = in_array( $view_mode, array( 'gallery', 'list' ), true ) ? $view_mode : 'gallery';
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
		<?php if ( $show_header ) : ?>
			<header class="rpl-library-header">
				<h2><?php esc_html_e( 'Recipe Library', 'recipe-pdf-library' ); ?></h2>
				<p><?php esc_html_e( 'Browse, search, and open recipe PDFs.', 'recipe-pdf-library' ); ?></p>
			</header>
		<?php endif; ?>
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
			<input type="hidden" name="recipe_view" value="<?php echo esc_attr( $view_mode ); ?>">
			<div class="rpl-view-toggle" role="group" aria-label="<?php esc_attr_e( 'View mode', 'recipe-pdf-library' ); ?>">
				<a class="rpl-view-toggle__link <?php echo 'gallery' === $view_mode ? 'is-active' : ''; ?>" href="<?php echo esc_url( rpl_build_library_url( array( 'recipe_view' => 'gallery' ) ) ); ?>"><?php esc_html_e( 'Gallery', 'recipe-pdf-library' ); ?></a>
				<a class="rpl-view-toggle__link <?php echo 'list' === $view_mode ? 'is-active' : ''; ?>" href="<?php echo esc_url( rpl_build_library_url( array( 'recipe_view' => 'list' ) ) ); ?>"><?php esc_html_e( 'List', 'recipe-pdf-library' ); ?></a>
			</div>
			<button class="rpl-search-button" type="submit"><?php esc_html_e( 'Search', 'recipe-pdf-library' ); ?></button>
			<?php if ( $search || $category_slug ) : ?>
				<a class="rpl-clear-link" href="<?php echo esc_url( rpl_build_library_url( array( 'recipe_search' => '', 'recipe_category' => '' ) ) ); ?>">
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
			<div class="rpl-recipe-grid rpl-recipe-grid--<?php echo esc_attr( $view_mode ); ?>">
				<?php foreach ( $recipes as $recipe ) : ?>
					<?php rpl_render_recipe_card( $recipe, $view_mode ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

function rpl_get_library_recipes( string $category_slug ): array {
	$base_args = array(
		'post_type'              => 'recipe_pdf',
		'posts_per_page'         => 100,
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);

	if ( $category_slug ) {
		$base_args['tax_query'] = array(
			array(
				'taxonomy' => 'recipe_category',
				'field'    => 'slug',
				'terms'    => $category_slug,
			),
		);
	}

	if ( ! is_user_logged_in() ) {
		$public_args                = $base_args;
		$public_args['post_status'] = array( 'publish' );

		return get_posts( $public_args );
	}

	$public_args                = $base_args;
	$public_args['post_status'] = array( 'publish' );

	$private_args                = $base_args;
	$private_args['post_status'] = array( 'private' );
	$private_args['author']      = get_current_user_id();

	$public_recipes  = get_posts( $public_args );
	$private_recipes = get_posts( $private_args );
	$merged          = array();

	foreach ( array_merge( $public_recipes, $private_recipes ) as $recipe ) {
		$merged[ (int) $recipe->ID ] = $recipe;
	}

	$recipes = array_values( $merged );

	usort(
		$recipes,
		static function ( WP_Post $a, WP_Post $b ): int {
			return strnatcasecmp( $a->post_title, $b->post_title );
		}
	);

	return $recipes;
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

function rpl_render_recipe_card( WP_Post $recipe, string $view_mode ): void {
	$pdf_url      = rpl_get_recipe_pdf_url( $recipe->ID );
	$preview_url  = rpl_get_recipe_pdf_preview_image_url( $recipe->ID, 'medium' );
	$fallback_icon = rpl_get_recipe_pdf_fallback_icon_url( $recipe->ID );
	$published    = get_the_date( get_option( 'date_format' ), $recipe );
	$categories   = get_the_terms( $recipe->ID, 'recipe_category' );
	$tags         = get_the_terms( $recipe->ID, 'recipe_tag' );
	$is_public    = 'publish' === get_post_status( $recipe );
	$visibility   = $is_public ? __( 'Public', 'recipe-pdf-library' ) : __( 'Private', 'recipe-pdf-library' );
	?>
	<article class="rpl-recipe-card rpl-recipe-card--<?php echo esc_attr( $view_mode ); ?>">
		<a class="rpl-recipe-card__thumb" href="<?php echo esc_url( get_permalink( $recipe ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $recipe ) ); ?>">
			<?php if ( $preview_url ) : ?>
				<img src="<?php echo esc_url( $preview_url ); ?>" alt="<?php echo esc_attr( get_the_title( $recipe ) ); ?>">
			<?php elseif ( $fallback_icon ) : ?>
				<img class="rpl-recipe-card__thumb-icon" src="<?php echo esc_url( $fallback_icon ); ?>" alt="<?php esc_attr_e( 'PDF file', 'recipe-pdf-library' ); ?>">
			<?php else : ?>
				<span><?php esc_html_e( 'PDF', 'recipe-pdf-library' ); ?></span>
			<?php endif; ?>
		</a>
		<div class="rpl-recipe-card__body">
			<h2 class="rpl-recipe-card__title">
				<a href="<?php echo esc_url( get_permalink( $recipe ) ); ?>"><?php echo esc_html( get_the_title( $recipe ) ); ?></a>
			</h2>
			<div class="rpl-recipe-card__meta">
				<span class="rpl-visibility-badge <?php echo $is_public ? 'rpl-visibility-badge--public' : 'rpl-visibility-badge--private'; ?>"><?php echo esc_html( $visibility ); ?></span>
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

function rpl_get_recipe_pdf_preview_image_url( int $post_id, string $size = 'medium' ): string {
	$attachment_id = rpl_get_recipe_pdf_attachment_id( $post_id );

	if ( ! $attachment_id || ! rpl_attachment_is_pdf( $attachment_id ) ) {
		return '';
	}

	$image_url = wp_get_attachment_image_url( $attachment_id, $size );

	if ( $image_url ) {
		return (string) $image_url;
	}

	return '';
}

function rpl_get_recipe_pdf_fallback_icon_url( int $post_id ): string {
	$attachment_id = rpl_get_recipe_pdf_attachment_id( $post_id );

	if ( ! $attachment_id || ! rpl_attachment_is_pdf( $attachment_id ) ) {
		return '';
	}

	$icon = wp_mime_type_icon( $attachment_id );

	return is_string( $icon ) ? $icon : '';
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

	if ( function_exists( 'rpl_get_secure_pdf_view_url' ) ) {
		return rpl_get_secure_pdf_view_url( $post_id );
	}

	$url = wp_get_attachment_url( $attachment_id );

	return $url ? $url : '';
}

function rpl_current_url(): string {
	global $wp;

	return home_url( add_query_arg( array(), $wp->request ) );
}

function rpl_build_library_url( array $overrides = array() ): string {
	$args = array(
		'recipe_search'   => isset( $_GET['recipe_search'] ) ? sanitize_text_field( wp_unslash( $_GET['recipe_search'] ) ) : '',
		'recipe_category' => isset( $_GET['recipe_category'] ) ? sanitize_title( wp_unslash( $_GET['recipe_category'] ) ) : '',
		'recipe_view'     => isset( $_GET['recipe_view'] ) ? sanitize_key( wp_unslash( $_GET['recipe_view'] ) ) : 'gallery',
	);

	$args = array_merge( $args, $overrides );

	foreach ( $args as $key => $value ) {
		if ( '' === $value ) {
			unset( $args[ $key ] );
		}
	}

	return add_query_arg( $args, rpl_current_url() );
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
