<?php
/**
 * Fallback-safe PDF text indexing.
 *
 * @package Recipe_PDF_Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const RPL_PDF_INDEX_META_KEY = '_rpl_pdf_index_text';
const RPL_PDF_INDEX_STATUS_META_KEY = '_rpl_pdf_index_status';
const RPL_MAX_INDEX_BYTES = 10485760;

function rpl_index_recipe_pdf_text( int $post_id, int $attachment_id ): void {
	$file = get_attached_file( $attachment_id );

	if ( ! is_string( $file ) || ! is_readable( $file ) ) {
		rpl_mark_pdf_index_status( $post_id, 'unavailable' );
		return;
	}

	$size = filesize( $file );

	if ( false === $size || $size > RPL_MAX_INDEX_BYTES ) {
		rpl_mark_pdf_index_status( $post_id, 'skipped' );
		return;
	}

	$raw = file_get_contents( $file );

	if ( false === $raw || '' === $raw ) {
		rpl_mark_pdf_index_status( $post_id, 'unavailable' );
		return;
	}

	$text = rpl_extract_text_from_pdf_bytes( $raw );

	if ( '' === $text ) {
		rpl_clear_pdf_text_index( $post_id );
		rpl_mark_pdf_index_status( $post_id, 'empty' );
		return;
	}

	update_post_meta( $post_id, RPL_PDF_INDEX_META_KEY, $text );
	rpl_mark_pdf_index_status( $post_id, 'indexed' );
}

function rpl_clear_pdf_text_index( int $post_id ): void {
	delete_post_meta( $post_id, RPL_PDF_INDEX_META_KEY );
	delete_post_meta( $post_id, RPL_PDF_INDEX_STATUS_META_KEY );
}

function rpl_get_recipe_pdf_index_text( int $post_id ): string {
	return (string) get_post_meta( $post_id, RPL_PDF_INDEX_META_KEY, true );
}

function rpl_mark_pdf_index_status( int $post_id, string $status ): void {
	update_post_meta( $post_id, RPL_PDF_INDEX_STATUS_META_KEY, sanitize_key( $status ) );
}

function rpl_extract_text_from_pdf_bytes( string $raw ): string {
	$chunks = array( $raw );

	if ( preg_match_all( '/stream\r?\n?(.*?)\r?\n?endstream/s', $raw, $matches ) ) {
		foreach ( $matches[1] as $stream ) {
			$decoded = rpl_try_decode_pdf_stream( trim( $stream ) );
			$chunks[] = $decoded ? $decoded : $stream;
		}
	}

	$text_parts = array();

	foreach ( $chunks as $chunk ) {
		$text_parts[] = rpl_extract_pdf_literal_strings( $chunk );
		$text_parts[] = rpl_extract_pdf_hex_strings( $chunk );
	}

	return rpl_normalize_pdf_index_text( implode( ' ', $text_parts ) );
}

function rpl_try_decode_pdf_stream( string $stream ): string {
	foreach ( array( 'gzuncompress', 'gzdecode', 'gzinflate' ) as $function ) {
		if ( ! function_exists( $function ) ) {
			continue;
		}

		$decoded = @call_user_func( $function, $stream );

		if ( is_string( $decoded ) && '' !== $decoded ) {
			return $decoded;
		}
	}

	return '';
}

function rpl_extract_pdf_literal_strings( string $chunk ): string {
	if ( ! preg_match_all( '/\((?:\\\\.|[^\\\\)])*\)/s', $chunk, $matches ) ) {
		return '';
	}

	$text = array_map(
		static function ( string $value ): string {
			$value = substr( $value, 1, -1 );
			$value = str_replace(
				array( '\\n', '\\r', '\\t', '\\b', '\\f', '\\(', '\\)', '\\\\' ),
				array( ' ', ' ', ' ', ' ', ' ', '(', ')', '\\' ),
				$value
			);

			return $value;
		},
		$matches[0]
	);

	return implode( ' ', $text );
}

function rpl_extract_pdf_hex_strings( string $chunk ): string {
	if ( ! preg_match_all( '/<([A-Fa-f0-9\s]{6,})>/', $chunk, $matches ) ) {
		return '';
	}

	$text = array();

	foreach ( $matches[1] as $hex ) {
		$hex = preg_replace( '/\s+/', '', $hex );

		if ( ! is_string( $hex ) || '' === $hex || 0 !== strlen( $hex ) % 2 ) {
			continue;
		}

		$decoded = hex2bin( $hex );

		if ( is_string( $decoded ) ) {
			$text[] = $decoded;
		}
	}

	return implode( ' ', $text );
}

function rpl_normalize_pdf_index_text( string $text ): string {
	$text = wp_strip_all_tags( $text );
	$text = preg_replace( '/[^\P{C}\t\r\n]+/u', ' ', $text );
	$text = preg_replace( '/\s+/', ' ', (string) $text );
	$text = trim( (string) $text );

	if ( strlen( $text ) > 50000 ) {
		$text = substr( $text, 0, 50000 );
	}

	return $text;
}

