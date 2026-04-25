<?php
if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

$redirect_target = home_url( '/' );
$login_error     = isset( $_GET['login_error'] ) ? sanitize_key( wp_unslash( $_GET['login_error'] ) ) : '';

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
	<?php if ( '' !== $login_error ) : ?>
		<div class="rht-alert">
			<?php if ( 'missing_fields' === $login_error ) : ?>
				<?php esc_html_e( 'Please enter both username and password.', 'recipes-hook-theme' ); ?>
			<?php elseif ( 'invalid_request' === $login_error ) : ?>
				<?php esc_html_e( 'Login request expired. Please try again.', 'recipes-hook-theme' ); ?>
			<?php else : ?>
				<?php esc_html_e( 'Login failed. Check your username/password and try again.', 'recipes-hook-theme' ); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<form class="rht-auth-form rht-auth-form--plain" name="loginform" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<input type="hidden" name="action" value="rht_front_login">
		<?php wp_nonce_field( 'rht_front_login', 'rht_login_nonce' ); ?>
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
