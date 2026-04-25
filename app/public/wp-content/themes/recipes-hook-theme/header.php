<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="rht-site-header">
	<div class="rht-site-header__inner">
		<a class="rht-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img class="rht-brand__logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/rl_logo.png' ); ?>" alt="<?php esc_attr_e( 'Recipes Library', 'recipes-hook-theme' ); ?>">
		</a>
		<nav class="rht-nav">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Recipes', 'recipes-hook-theme' ); ?></a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( home_url( '/manage-recipes/' ) ); ?>"><?php esc_html_e( 'Manage Recipes', 'recipes-hook-theme' ); ?></a>
				<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Log Out', 'recipes-hook-theme' ); ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>"><?php esc_html_e( 'Log In', 'recipes-hook-theme' ); ?></a>
			<?php endif; ?>
		</nav>
	</div>
</header>
<main class="rht-page-shell">
