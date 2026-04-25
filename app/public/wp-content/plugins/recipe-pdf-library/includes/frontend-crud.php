<?php
/**
 * Frontend create, update, and delete actions for recipe posts.
 *
 * @package Recipe_PDF_Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rpl_frontend_require_login(): void {
	wp_safe_redirect( wp_login_url( wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
	exit;
}
add_action( 'admin_post_nopriv_rpl_frontend_create_recipe', 'rpl_frontend_require_login' );
add_action( 'admin_post_nopriv_rpl_frontend_update_recipe', 'rpl_frontend_require_login' );
add_action( 'admin_post_nopriv_rpl_frontend_delete_recipe', 'rpl_frontend_require_login' );

function rpl_handle_frontend_create_recipe(): void {
	if ( ! is_user_logged_in() ) {
		rpl_frontend_redirect_with_status( 'forbidden' );
	}

	check_admin_referer( 'rpl_frontend_create_recipe' );

	$title       = isset( $_POST['rpl_recipe_title'] ) ? sanitize_text_field( wp_unslash( $_POST['rpl_recipe_title'] ) ) : '';
	$description = isset( $_POST['rpl_recipe_description'] ) ? wp_kses_post( wp_unslash( $_POST['rpl_recipe_description'] ) ) : '';
	$tags_raw    = isset( $_POST['rpl_recipe_tags'] ) ? sanitize_text_field( wp_unslash( $_POST['rpl_recipe_tags'] ) ) : '';
	$categories  = isset( $_POST['rpl_recipe_categories'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['rpl_recipe_categories'] ) ) : array();
	$post_status = rpl_frontend_get_visibility_status( isset( $_POST['rpl_recipe_visibility'] ) ? wp_unslash( $_POST['rpl_recipe_visibility'] ) : 'private' );

	$attachment_id = rpl_frontend_upload_pdf_attachment( 'rpl_recipe_pdf' );

	if ( is_wp_error( $attachment_id ) ) {
		rpl_frontend_redirect_with_status( 'upload_error' );
	}

	if ( ! $attachment_id ) {
		rpl_frontend_redirect_with_status( 'upload_error' );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'recipe_pdf',
			'post_status'  => $post_status,
			'post_title'   => $title,
			'post_content' => $description,
			'post_author'  => get_current_user_id(),
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		rpl_frontend_redirect_with_status( 'save_error' );
	}

	if ( $attachment_id ) {
		update_post_meta( $post_id, RPL_PDF_META_KEY, $attachment_id );
		rpl_index_recipe_pdf_text( $post_id, $attachment_id );

		$post = get_post( $post_id );

		if ( $post instanceof WP_Post ) {
			rpl_maybe_set_title_from_pdf( $post_id, $post, $attachment_id );
		}
	}

	if ( ! empty( $categories ) ) {
		wp_set_object_terms( $post_id, $categories, 'recipe_category', false );
	}

	if ( '' !== $tags_raw ) {
		$tags = array_filter( array_map( 'trim', explode( ',', $tags_raw ) ) );
		wp_set_object_terms( $post_id, $tags, 'recipe_tag', false );
	}

	rpl_frontend_redirect_with_status( 'created' );
}
add_action( 'admin_post_rpl_frontend_create_recipe', 'rpl_handle_frontend_create_recipe' );

function rpl_handle_frontend_update_recipe(): void {
	if ( ! is_user_logged_in() ) {
		rpl_frontend_redirect_with_status( 'forbidden' );
	}

	check_admin_referer( 'rpl_frontend_update_recipe' );

	$post_id = isset( $_POST['rpl_recipe_id'] ) ? absint( $_POST['rpl_recipe_id'] ) : 0;

	if ( ! $post_id || 'recipe_pdf' !== get_post_type( $post_id ) ) {
		rpl_frontend_redirect_with_status( 'forbidden' );
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post || (int) $post->post_author !== get_current_user_id() ) {
		rpl_frontend_redirect_with_status( 'forbidden' );
	}

	$title       = isset( $_POST['rpl_recipe_title'] ) ? sanitize_text_field( wp_unslash( $_POST['rpl_recipe_title'] ) ) : '';
	$description = isset( $_POST['rpl_recipe_description'] ) ? wp_kses_post( wp_unslash( $_POST['rpl_recipe_description'] ) ) : '';
	$tags_raw    = isset( $_POST['rpl_recipe_tags'] ) ? sanitize_text_field( wp_unslash( $_POST['rpl_recipe_tags'] ) ) : '';
	$categories  = isset( $_POST['rpl_recipe_categories'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['rpl_recipe_categories'] ) ) : array();
	$remove_pdf  = ! empty( $_POST['rpl_remove_pdf'] );
	$post_status = rpl_frontend_get_visibility_status( isset( $_POST['rpl_recipe_visibility'] ) ? wp_unslash( $_POST['rpl_recipe_visibility'] ) : (string) $post->post_status );

	$updated = wp_update_post(
		array(
			'ID'           => $post_id,
			'post_status'  => $post_status,
			'post_title'   => $title,
			'post_content' => $description,
		),
		true
	);

	if ( is_wp_error( $updated ) ) {
		rpl_frontend_redirect_with_status( 'save_error' );
	}

	$attachment_id = rpl_frontend_upload_pdf_attachment( 'rpl_recipe_pdf' );

	if ( is_wp_error( $attachment_id ) ) {
		rpl_frontend_redirect_with_status( 'upload_error' );
	}

	if ( $remove_pdf ) {
		delete_post_meta( $post_id, RPL_PDF_META_KEY );
		rpl_clear_pdf_text_index( $post_id );
	}

	if ( $attachment_id ) {
		update_post_meta( $post_id, RPL_PDF_META_KEY, $attachment_id );
		rpl_index_recipe_pdf_text( $post_id, $attachment_id );
	}

	wp_set_object_terms( $post_id, $categories, 'recipe_category', false );

	$tags = array();

	if ( '' !== $tags_raw ) {
		$tags = array_filter( array_map( 'trim', explode( ',', $tags_raw ) ) );
	}

	wp_set_object_terms( $post_id, $tags, 'recipe_tag', false );

	rpl_frontend_redirect_with_status( 'updated' );
}
add_action( 'admin_post_rpl_frontend_update_recipe', 'rpl_handle_frontend_update_recipe' );

function rpl_handle_frontend_delete_recipe(): void {
	if ( ! is_user_logged_in() ) {
		rpl_frontend_redirect_with_status( 'forbidden' );
	}

	check_admin_referer( 'rpl_frontend_delete_recipe' );

	$post_id = isset( $_POST['rpl_recipe_id'] ) ? absint( $_POST['rpl_recipe_id'] ) : 0;

	if ( ! $post_id || 'recipe_pdf' !== get_post_type( $post_id ) ) {
		rpl_frontend_redirect_with_status( 'forbidden' );
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post || (int) $post->post_author !== get_current_user_id() ) {
		rpl_frontend_redirect_with_status( 'forbidden' );
	}

	wp_trash_post( $post_id );
	rpl_frontend_redirect_with_status( 'deleted' );
}
add_action( 'admin_post_rpl_frontend_delete_recipe', 'rpl_handle_frontend_delete_recipe' );

function rpl_frontend_upload_pdf_attachment( string $field_name ) {
	if ( empty( $_FILES[ $field_name ] ) || empty( $_FILES[ $field_name ]['name'] ) ) {
		return 0;
	}

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	if ( ! function_exists( 'wp_insert_attachment' ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$file      = $_FILES[ $field_name ];
	$file_type = wp_check_filetype( $file['name'] );

	if ( 'pdf' !== strtolower( (string) $file_type['ext'] ) ) {
		return new WP_Error( 'invalid_pdf', __( 'Only PDF files are allowed.', 'recipe-pdf-library' ) );
	}

	$upload = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
			'mimes'     => array(
				'pdf' => 'application/pdf',
			),
		)
	);

	if ( isset( $upload['error'] ) ) {
		return new WP_Error( 'upload_error', (string) $upload['error'] );
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'application/pdf',
			'post_title'     => rpl_clean_pdf_filename_to_title( basename( (string) $upload['file'] ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		(string) $upload['file']
	);

	if ( ! $attachment_id ) {
		return new WP_Error( 'attach_error', __( 'Unable to save PDF file.', 'recipe-pdf-library' ) );
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, (string) $upload['file'] );

	if ( ! is_wp_error( $metadata ) ) {
		wp_update_attachment_metadata( $attachment_id, $metadata );
	}

	return (int) $attachment_id;
}

function rpl_frontend_redirect_with_status( string $status ): void {
	$redirect = wp_get_referer();

	if ( ! $redirect || 0 !== strpos( $redirect, home_url() ) ) {
		$redirect = home_url( '/' );
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'rpl_status' => sanitize_key( $status ),
			),
			remove_query_arg( array( 'rpl_edit', 'rpl_status' ), $redirect )
		)
	);
	exit;
}

function rpl_frontend_get_visibility_status( $status ): string {
	$status = sanitize_key( (string) $status );

	return in_array( $status, array( 'publish', 'private' ), true ) ? $status : 'private';
}
