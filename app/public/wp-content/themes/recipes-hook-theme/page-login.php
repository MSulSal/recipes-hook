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

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	$nonce_valid = isset( $_POST['rht_login_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rht_login_nonce'] ) ), 'rht_front_login' );
	$username    = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
	$password    = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '';
	$remember    = ! empty( $_POST['rememberme'] );

	if ( ! $nonce_valid ) {
		$login_error = 'invalid_request';
	} elseif ( '' === $username || '' === $password ) {
		$login_error = 'missing_fields';
	} else {
		$user = wp_signon(
			array(
				'user_login'    => $username,
				'user_password' => $password,
				'remember'      => $remember,
			),
			is_ssl()
		);

		if ( is_wp_error( $user ) ) {
			$login_error = 'invalid_credentials';
		} else {
			wp_safe_redirect( $redirect_target );
			exit;
		}
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
	<a class="rht-login-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Recipes Library', 'recipes-hook-theme' ); ?>">
		<img class="rht-login-brand__logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/rl_logo_shelf.png' ); ?>" alt="<?php esc_attr_e( 'Recipes Library', 'recipes-hook-theme' ); ?>">
	</a>
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
	<form class="rht-auth-form rht-auth-form--plain" name="loginform" action="<?php echo esc_url( home_url( '/login/' ) ); ?>" method="post">
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
