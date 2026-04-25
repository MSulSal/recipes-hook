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

function rht_create_manage_page_on_theme_switch(): void {
	$manage_page = get_page_by_path( 'manage-recipes' );

	if ( $manage_page instanceof WP_Post ) {
		return;
	}

	wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Manage Recipes',
			'post_name'    => 'manage-recipes',
			'post_content' => '',
		)
	);
}
add_action( 'after_switch_theme', 'rht_create_manage_page_on_theme_switch' );

function rht_ensure_login_page_exists(): void {
	if ( get_option( 'rht_login_page_checked', false ) ) {
		return;
	}

	rht_create_login_page_on_theme_switch();
	update_option( 'rht_login_page_checked', 1, false );
}
add_action( 'init', 'rht_ensure_login_page_exists' );

function rht_ensure_manage_page_exists(): void {
	if ( get_option( 'rht_manage_page_checked', false ) ) {
		return;
	}

	rht_create_manage_page_on_theme_switch();
	update_option( 'rht_manage_page_checked', 1, false );
}
add_action( 'init', 'rht_ensure_manage_page_exists' );

function rht_ensure_brand_defaults(): void {
	if ( get_option( 'rht_brand_defaults_applied', false ) ) {
		return;
	}

	update_option( 'blogname', 'Recipes Library' );
	update_option( 'rht_brand_defaults_applied', 1, false );
}
add_action( 'init', 'rht_ensure_brand_defaults' );

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

function rht_enforce_login_gate(): void {
	if ( is_user_logged_in() || is_admin() || wp_doing_ajax() ) {
		return;
	}

	if ( is_page( 'login' ) ) {
		return;
	}

	if ( is_page( 'manage-recipes' ) ) {
		global $wp;
		$current_url = home_url( add_query_arg( array(), $wp->request ) );
		$login_url   = add_query_arg(
			array(
				'redirect_to' => esc_url_raw( $current_url ),
			),
			home_url( '/login/' )
		);

		wp_safe_redirect( $login_url );
		exit;
	}

	if ( is_singular( 'recipe_pdf' ) ) {
		$recipe = get_queried_object();

		if ( $recipe instanceof WP_Post && 'private' === get_post_status( $recipe ) ) {
			global $wp;
			$current_url = home_url( add_query_arg( array(), $wp->request ) );
			$login_url   = add_query_arg(
				array(
					'redirect_to' => esc_url_raw( $current_url ),
				),
				home_url( '/login/' )
			);

			wp_safe_redirect( $login_url );
			exit;
		}
	}
}
add_action( 'template_redirect', 'rht_enforce_login_gate' );

function rht_redirect_recipe_archive_to_home(): void {
	if ( is_post_type_archive( 'recipe_pdf' ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}
add_action( 'template_redirect', 'rht_redirect_recipe_archive_to_home', 5 );

function rht_handle_front_login(): void {
	if ( is_user_logged_in() ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}

	if ( ! isset( $_POST['rht_login_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rht_login_nonce'] ) ), 'rht_front_login' ) ) {
		wp_safe_redirect( add_query_arg( 'login_error', 'invalid_request', home_url( '/login/' ) ) );
		exit;
	}

	$username = isset( $_POST['log'] ) ? sanitize_user( wp_unslash( $_POST['log'] ) ) : '';
	$password = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '';
	$remember = ! empty( $_POST['rememberme'] );
	$redirect = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' );

	if ( '' === $username || '' === $password ) {
		wp_safe_redirect( add_query_arg( 'login_error', 'missing_fields', home_url( '/login/' ) ) );
		exit;
	}

	$user = wp_signon(
		array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => $remember,
		),
		is_ssl()
	);

	if ( is_wp_error( $user ) ) {
		wp_safe_redirect( add_query_arg( 'login_error', 'invalid_credentials', home_url( '/login/' ) ) );
		exit;
	}

	if ( 0 !== strpos( $redirect, home_url() ) ) {
		$redirect = home_url( '/' );
	}

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_nopriv_rht_front_login', 'rht_handle_front_login' );
add_action( 'admin_post_rht_front_login', 'rht_handle_front_login' );
