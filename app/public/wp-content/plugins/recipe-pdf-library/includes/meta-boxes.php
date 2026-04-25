<?php
/**
 * Recipe PDF admin fields and save handling.
 *
 * @package Recipe_PDF_Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const RPL_PDF_META_KEY = '_rpl_pdf_attachment_id';

function rpl_add_pdf_meta_box(): void {
	add_meta_box(
		'rpl_recipe_pdf',
		__( 'Recipe PDF', 'recipe-pdf-library' ),
		'rpl_render_pdf_meta_box',
		'recipe_pdf',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_recipe_pdf', 'rpl_add_pdf_meta_box' );

function rpl_render_pdf_meta_box( WP_Post $post ): void {
	$attachment_id = absint( get_post_meta( $post->ID, RPL_PDF_META_KEY, true ) );
	$pdf_url       = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
	$pdf_title     = $attachment_id ? get_the_title( $attachment_id ) : '';

	wp_nonce_field( 'rpl_save_recipe_pdf', 'rpl_recipe_pdf_nonce' );
	?>
	<p>
		<?php esc_html_e( 'Upload the recipe PDF here. The site will display this PDF as the recipe.', 'recipe-pdf-library' ); ?>
	</p>
	<p class="description">
		<?php esc_html_e( 'Machine-readable PDFs may be searchable later. Scanned/image PDFs still work, but visitors may only find them by title, filename, category, or tag.', 'recipe-pdf-library' ); ?>
	</p>
	<input type="hidden" id="rpl_pdf_attachment_id" name="rpl_pdf_attachment_id" value="<?php echo esc_attr( (string) $attachment_id ); ?>">
	<div class="rpl-admin-pdf-status" id="rpl_pdf_status">
		<?php if ( $pdf_url ) : ?>
			<strong><?php esc_html_e( 'Selected PDF:', 'recipe-pdf-library' ); ?></strong>
			<a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $pdf_title ); ?></a>
		<?php else : ?>
			<?php esc_html_e( 'No PDF selected yet.', 'recipe-pdf-library' ); ?>
		<?php endif; ?>
	</div>
	<p>
		<button type="button" class="button button-primary" id="rpl_select_pdf">
			<?php esc_html_e( 'Select or Upload PDF', 'recipe-pdf-library' ); ?>
		</button>
		<button type="button" class="button" id="rpl_remove_pdf" <?php disabled( ! $attachment_id ); ?>>
			<?php esc_html_e( 'Remove PDF', 'recipe-pdf-library' ); ?>
		</button>
	</p>
	<?php
}

function rpl_admin_enqueue_assets( string $hook_suffix ): void {
	$screen = get_current_screen();

	if ( ! $screen || 'recipe_pdf' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'rpl-admin-media',
		RPL_PLUGIN_URL . 'assets/js/admin-media.js',
		array( 'jquery' ),
		RPL_VERSION,
		true
	);
	wp_localize_script(
		'rpl-admin-media',
		'rplAdminMedia',
		array(
			'title'        => __( 'Select Recipe PDF', 'recipe-pdf-library' ),
			'button'       => __( 'Use this PDF', 'recipe-pdf-library' ),
			'selectedText' => __( 'Selected PDF:', 'recipe-pdf-library' ),
			'emptyText'    => __( 'No PDF selected yet.', 'recipe-pdf-library' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'rpl_admin_enqueue_assets' );

function rpl_save_recipe_pdf_meta( int $post_id, WP_Post $post ): void {
	if ( ! isset( $_POST['rpl_recipe_pdf_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rpl_recipe_pdf_nonce'] ) ), 'rpl_save_recipe_pdf' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$attachment_id = isset( $_POST['rpl_pdf_attachment_id'] ) ? absint( $_POST['rpl_pdf_attachment_id'] ) : 0;

	if ( ! $attachment_id ) {
		delete_post_meta( $post_id, RPL_PDF_META_KEY );
		return;
	}

	if ( ! rpl_attachment_is_pdf( $attachment_id ) ) {
		delete_post_meta( $post_id, RPL_PDF_META_KEY );
		add_filter(
			'redirect_post_location',
			static function ( string $location ): string {
				return add_query_arg( 'rpl_pdf_error', 'invalid_pdf', $location );
			}
		);
		return;
	}

	update_post_meta( $post_id, RPL_PDF_META_KEY, $attachment_id );
	rpl_maybe_set_title_from_pdf( $post_id, $post, $attachment_id );
}
add_action( 'save_post_recipe_pdf', 'rpl_save_recipe_pdf_meta', 10, 2 );

function rpl_attachment_is_pdf( int $attachment_id ): bool {
	if ( 'attachment' !== get_post_type( $attachment_id ) ) {
		return false;
	}

	$mime_type = get_post_mime_type( $attachment_id );
	$file      = get_attached_file( $attachment_id );

	return 'application/pdf' === $mime_type || ( is_string( $file ) && 'pdf' === strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) );
}

function rpl_maybe_set_title_from_pdf( int $post_id, WP_Post $post, int $attachment_id ): void {
	$current_title = trim( (string) $post->post_title );

	if ( '' !== $current_title ) {
		return;
	}

	$file = get_attached_file( $attachment_id );

	if ( ! is_string( $file ) || '' === $file ) {
		return;
	}

	$title = rpl_clean_pdf_filename_to_title( wp_basename( $file ) );

	if ( '' === $title ) {
		return;
	}

	remove_action( 'save_post_recipe_pdf', 'rpl_save_recipe_pdf_meta', 10 );
	wp_update_post(
		array(
			'ID'         => $post_id,
			'post_title' => $title,
			'post_name'  => sanitize_title( $title ),
		)
	);
	add_action( 'save_post_recipe_pdf', 'rpl_save_recipe_pdf_meta', 10, 2 );
}

function rpl_clean_pdf_filename_to_title( string $filename ): string {
	$title = preg_replace( '/\.pdf$/i', '', $filename );
	$title = preg_replace( '/[_-]+/', ' ', (string) $title );
	$title = preg_replace( '/\s+/', ' ', (string) $title );
	$title = trim( (string) $title );

	return ucwords( strtolower( $title ) );
}

function rpl_admin_pdf_notice(): void {
	if ( ! isset( $_GET['rpl_pdf_error'] ) || 'invalid_pdf' !== sanitize_text_field( wp_unslash( $_GET['rpl_pdf_error'] ) ) ) {
		return;
	}
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Please choose a PDF file for this recipe. Other file types were not saved.', 'recipe-pdf-library' ); ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'rpl_admin_pdf_notice' );

function rpl_recipe_columns( array $columns ): array {
	$new_columns = array();

	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;

		if ( 'title' === $key ) {
			$new_columns['rpl_pdf'] = __( 'PDF', 'recipe-pdf-library' );
		}
	}

	return $new_columns;
}
add_filter( 'manage_recipe_pdf_posts_columns', 'rpl_recipe_columns' );

function rpl_recipe_column_content( string $column, int $post_id ): void {
	if ( 'rpl_pdf' !== $column ) {
		return;
	}

	$attachment_id = absint( get_post_meta( $post_id, RPL_PDF_META_KEY, true ) );

	if ( ! $attachment_id || ! rpl_attachment_is_pdf( $attachment_id ) ) {
		echo esc_html__( 'No PDF', 'recipe-pdf-library' );
		return;
	}

	$url = wp_get_attachment_url( $attachment_id );

	if ( ! $url ) {
		echo esc_html__( 'Missing file', 'recipe-pdf-library' );
		return;
	}

	printf(
		'<a href="%s" target="_blank" rel="noopener">%s</a>',
		esc_url( $url ),
		esc_html__( 'View PDF', 'recipe-pdf-library' )
	);
}
add_action( 'manage_recipe_pdf_posts_custom_column', 'rpl_recipe_column_content', 10, 2 );

