<?php
if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

$redirect_target = home_url( '/' );

if ( isset( $_GET['redirect_to'] ) ) {
	$candidate = esc_url_raw( wp_unslash( $_GET['redirect_to'] ) );

	if ( $candidate && 0 === strpos( $candidate, home_url() ) ) {
		$redirect_target = $candidate;
	}
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'rht-login-page' ); ?>>
<?php wp_body_open(); ?>
<main class="rht-login-shell rht-login-shell--plain">
	<form class="rht-auth-form rht-auth-form--plain" name="loginform" action="<?php echo esc_url( wp_login_url() ); ?>" method="post">
		<label for="user_login"><?php esc_html_e( 'Username or Email', 'recipes-hook-theme' ); ?></label>
		<input type="text" name="log" id="user_login" autocomplete="username" required>

		<label for="user_pass"><?php esc_html_e( 'Password', 'recipes-hook-theme' ); ?></label>
		<input type="password" name="pwd" id="user_pass" autocomplete="current-password" required>

		<label class="rht-inline-check">
			<input type="checkbox" name="rememberme" value="forever">
			<span><?php esc_html_e( 'Keep me signed in', 'recipes-hook-theme' ); ?></span>
		</label>

		<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_target ); ?>">
		<button type="submit" class="rht-auth-submit"><?php esc_html_e( 'Log In', 'recipes-hook-theme' ); ?></button>
		<a class="rht-auth-forgot" href="<?php echo esc_url( wp_lostpassword_url( home_url( '/login/' ) ) ); ?>"><?php esc_html_e( 'Forgot password?', 'recipes-hook-theme' ); ?></a>
	</form>
</main>
<?php wp_footer(); ?>
</body>
</html>
