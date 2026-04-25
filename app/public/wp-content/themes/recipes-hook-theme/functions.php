<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rht_theme_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'recipes-hook-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'rht_theme_setup' );

function rht_enqueue_styles(): void {
	wp_enqueue_style( 'recipes-hook-theme-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'rht_enqueue_styles' );

function rht_create_login_page_on_theme_switch(): void {
	$login_page = get_page_by_path( 'login' );

	if ( $login_page instanceof WP_Post ) {
		return;
	}

	wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Login',
			'post_name'    => 'login',
			'post_content' => '',
		)
	);
}
add_action( 'after_switch_theme', 'rht_create_login_page_on_theme_switch' );

function rht_ensure_login_page_exists(): void {
	if ( get_option( 'rht_login_page_checked', false ) ) {
		return;
	}

	rht_create_login_page_on_theme_switch();
	update_option( 'rht_login_page_checked', 1, false );
}
add_action( 'init', 'rht_ensure_login_page_exists' );

function rht_front_status_message( string $status ): string {
	$map = array(
		'created'      => __( 'Recipe created successfully.', 'recipes-hook-theme' ),
		'updated'      => __( 'Recipe updated successfully.', 'recipes-hook-theme' ),
		'deleted'      => __( 'Recipe deleted.', 'recipes-hook-theme' ),
		'upload_error' => __( 'PDF upload failed. Please try again with a valid PDF file.', 'recipes-hook-theme' ),
		'save_error'   => __( 'Recipe could not be saved. Please try again.', 'recipes-hook-theme' ),
		'forbidden'    => __( 'You do not have permission for that action.', 'recipes-hook-theme' ),
	);

	return isset( $map[ $status ] ) ? $map[ $status ] : '';
}
