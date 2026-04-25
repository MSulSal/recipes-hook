<?php
/**
 * Admin setup helpers for quick first-run configuration.
 *
 * @package Recipe_PDF_Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rpl_admin_setup_notice(): void {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 0 !== strpos( $screen->id, 'dashboard' ) ) {
		return;
	}

	$recipes_page = rpl_get_recipes_library_page();
	$front_page   = (int) get_option( 'page_on_front', 0 );

	if ( $recipes_page && $front_page === (int) $recipes_page->ID ) {
		return;
	}

	$url = wp_nonce_url(
		admin_url( 'admin-post.php?action=rpl_setup_library_page' ),
		'rpl_setup_library_page'
	);
	?>
	<div class="notice notice-info is-dismissible">
		<p>
			<strong><?php esc_html_e( 'Recipe PDF Library Setup', 'recipe-pdf-library' ); ?></strong>
			<?php esc_html_e( 'Your plugin is active. Create or assign a Recipes page and set it as homepage.', 'recipe-pdf-library' ); ?>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $url ); ?>">
				<?php esc_html_e( 'Set Up Recipes Homepage', 'recipe-pdf-library' ); ?>
			</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'rpl_admin_setup_notice' );

function rpl_handle_setup_library_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to run setup.', 'recipe-pdf-library' ) );
	}

	check_admin_referer( 'rpl_setup_library_page' );

	$page = rpl_get_recipes_library_page();

	if ( ! $page ) {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Recipes',
				'post_name'    => 'recipes',
				'post_content' => '[recipe_pdf_library]',
			),
			true
		);

		if ( is_wp_error( $page_id ) ) {
			wp_safe_redirect(
				add_query_arg(
					array( 'rpl_setup' => 'error' ),
					admin_url()
				)
			);
			exit;
		}

		$page = get_post( (int) $page_id );
	} elseif ( false === strpos( (string) $page->post_content, '[recipe_pdf_library]' ) ) {
		wp_update_post(
			array(
				'ID'           => (int) $page->ID,
				'post_content' => trim( (string) $page->post_content . "\n\n[recipe_pdf_library]" ),
			)
		);
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', (int) $page->ID );

	wp_safe_redirect(
		add_query_arg(
			array( 'rpl_setup' => 'done' ),
			admin_url( 'options-reading.php' )
		)
	);
	exit;
}
add_action( 'admin_post_rpl_setup_library_page', 'rpl_handle_setup_library_page' );

function rpl_get_recipes_library_page(): ?WP_Post {
	$existing = get_page_by_path( 'recipes' );

	if ( $existing instanceof WP_Post ) {
		return $existing;
	}

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 1,
			's'              => '[recipe_pdf_library]',
		)
	);

	if ( ! empty( $pages ) && $pages[0] instanceof WP_Post ) {
		return $pages[0];
	}

	return null;
}

function rpl_admin_setup_result_notice(): void {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! isset( $_GET['rpl_setup'] ) ) {
		return;
	}

	$result = sanitize_text_field( wp_unslash( $_GET['rpl_setup'] ) );

	if ( 'done' === $result ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Recipe library page is ready and set as homepage.', 'recipe-pdf-library' ); ?></p>
		</div>
		<?php
	}

	if ( 'error' === $result ) {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Recipe library setup could not complete. Please create a page with [recipe_pdf_library] manually.', 'recipe-pdf-library' ); ?></p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'rpl_admin_setup_result_notice' );
