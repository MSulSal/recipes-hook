<?php
/**
 * Secure PDF access for inline viewing and downloads.
 *
 * @package Recipe_PDF_Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rpl_get_secure_pdf_view_url( int $post_id ): string {
	return add_query_arg(
		array(
			'rpl_pdf_view' => $post_id,
		),
		home_url( '/' )
	);
}

function rpl_get_secure_pdf_download_url( int $post_id ): string {
	return add_query_arg(
		array(
			'rpl_pdf_download' => $post_id,
		),
		home_url( '/' )
	);
}

function rpl_can_access_recipe_pdf_post( int $post_id ): bool {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post || 'recipe_pdf' !== $post->post_type ) {
		return false;
	}

	return (int) $post->post_author === get_current_user_id();
}

function rpl_handle_secure_pdf_request(): void {
	$view_id     = isset( $_GET['rpl_pdf_view'] ) ? absint( wp_unslash( $_GET['rpl_pdf_view'] ) ) : 0;
	$download_id = isset( $_GET['rpl_pdf_download'] ) ? absint( wp_unslash( $_GET['rpl_pdf_download'] ) ) : 0;
	$post_id     = $view_id ? $view_id : $download_id;

	if ( ! $post_id ) {
		return;
	}

	if ( ! rpl_can_access_recipe_pdf_post( $post_id ) ) {
		global $wp;

		$login_url = add_query_arg(
			array(
				'redirect_to' => home_url( add_query_arg( array(), $wp->request ) ),
			),
			home_url( '/login/' )
		);
		wp_safe_redirect( $login_url );
		exit;
	}

	$attachment_id = rpl_get_recipe_pdf_attachment_id( $post_id );

	if ( ! $attachment_id || ! rpl_attachment_is_pdf( $attachment_id ) ) {
		status_header( 404 );
		exit;
	}

	$file = get_attached_file( $attachment_id );

	if ( ! is_string( $file ) || '' === $file || ! is_readable( $file ) ) {
		status_header( 404 );
		exit;
	}

	$filename = wp_basename( $file );
	$size     = filesize( $file );

	nocache_headers();
	header( 'Content-Type: application/pdf' );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Accept-Ranges: bytes' );

	if ( $download_id ) {
		header( 'Content-Disposition: attachment; filename="' . rawurlencode( $filename ) . '"' );
	} else {
		header( 'Content-Disposition: inline; filename="' . rawurlencode( $filename ) . '"' );
	}

	if ( false !== $size ) {
		header( 'Content-Length: ' . (string) $size );
	}

	readfile( $file );
	exit;
}
add_action( 'template_redirect', 'rpl_handle_secure_pdf_request', 1 );
