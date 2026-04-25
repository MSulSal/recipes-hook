<?php
get_header();

$status        = isset( $_GET['rpl_status'] ) ? sanitize_key( wp_unslash( $_GET['rpl_status'] ) ) : '';
$editing_id    = isset( $_GET['rpl_edit'] ) ? absint( $_GET['rpl_edit'] ) : 0;
$editing_post  = $editing_id ? get_post( $editing_id ) : null;
$can_manage    = is_user_logged_in() && current_user_can( 'edit_posts' );
$is_edit_valid = $editing_post instanceof WP_Post && 'recipe_pdf' === $editing_post->post_type && current_user_can( 'edit_post', $editing_post->ID );

if ( ! $is_edit_valid ) {
	$editing_post = null;
}
?>
<section class="rht-hero rht-card">
	<h1 class="rht-page-title"><?php esc_html_e( 'Recipe PDF Library', 'recipes-hook-theme' ); ?></h1>
	<p class="rht-page-subtitle"><?php esc_html_e( 'Search, open, and manage recipe PDFs in one place.', 'recipes-hook-theme' ); ?></p>
</section>

<?php if ( '' !== $status && '' !== rht_front_status_message( $status ) ) : ?>
	<div class="rht-alert rht-card"><?php echo esc_html( rht_front_status_message( $status ) ); ?></div>
<?php endif; ?>

<?php if ( $can_manage ) : ?>
	<?php
	$categories = get_terms(
		array(
			'taxonomy'   => 'recipe_category',
			'hide_empty' => false,
		)
	);
	$user_recipes = get_posts(
		array(
			'post_type'      => 'recipe_pdf',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$current_categories = array();
	$current_tags       = '';

	if ( $editing_post ) {
		$current_categories = wp_get_post_terms( $editing_post->ID, 'recipe_category', array( 'fields' => 'ids' ) );
		$current_tags       = implode( ', ', wp_get_post_terms( $editing_post->ID, 'recipe_tag', array( 'fields' => 'names' ) ) );
	}
	?>
	<section class="rht-manage-grid">
		<div class="rht-card rht-manage-form">
			<h2><?php echo esc_html( $editing_post ? __( 'Edit Recipe', 'recipes-hook-theme' ) : __( 'Add Recipe', 'recipes-hook-theme' ) ); ?></h2>
			<p><?php esc_html_e( 'Upload a PDF and publish directly from the site.', 'recipes-hook-theme' ); ?></p>
			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="<?php echo esc_attr( $editing_post ? 'rpl_frontend_update_recipe' : 'rpl_frontend_create_recipe' ); ?>">
				<?php if ( $editing_post ) : ?>
					<input type="hidden" name="rpl_recipe_id" value="<?php echo esc_attr( (string) $editing_post->ID ); ?>">
					<?php wp_nonce_field( 'rpl_frontend_update_recipe' ); ?>
				<?php else : ?>
					<?php wp_nonce_field( 'rpl_frontend_create_recipe' ); ?>
				<?php endif; ?>

				<label for="rht-recipe-title"><?php esc_html_e( 'Title', 'recipes-hook-theme' ); ?></label>
				<input id="rht-recipe-title" type="text" name="rpl_recipe_title" value="<?php echo esc_attr( $editing_post ? (string) $editing_post->post_title : '' ); ?>" placeholder="<?php esc_attr_e( 'Recipe title', 'recipes-hook-theme' ); ?>">

				<label for="rht-recipe-pdf"><?php echo esc_html( $editing_post ? __( 'Replace PDF (optional)', 'recipes-hook-theme' ) : __( 'Recipe PDF', 'recipes-hook-theme' ) ); ?></label>
				<input id="rht-recipe-pdf" type="file" name="rpl_recipe_pdf" accept="application/pdf,.pdf">

				<?php if ( $editing_post ) : ?>
					<label class="rht-inline-check">
						<input type="checkbox" name="rpl_remove_pdf" value="1">
						<span><?php esc_html_e( 'Remove current PDF', 'recipes-hook-theme' ); ?></span>
					</label>
				<?php endif; ?>

				<label for="rht-recipe-tags"><?php esc_html_e( 'Tags (comma separated)', 'recipes-hook-theme' ); ?></label>
				<input id="rht-recipe-tags" type="text" name="rpl_recipe_tags" value="<?php echo esc_attr( $current_tags ); ?>" placeholder="<?php esc_attr_e( 'dinner, noodles, spicy', 'recipes-hook-theme' ); ?>">

				<label for="rht-recipe-description"><?php esc_html_e( 'Notes (optional)', 'recipes-hook-theme' ); ?></label>
				<textarea id="rht-recipe-description" name="rpl_recipe_description" rows="4" placeholder="<?php esc_attr_e( 'Optional short note for this recipe.', 'recipes-hook-theme' ); ?>"><?php echo esc_textarea( $editing_post ? (string) $editing_post->post_content : '' ); ?></textarea>

				<fieldset class="rht-category-set">
					<legend><?php esc_html_e( 'Categories', 'recipes-hook-theme' ); ?></legend>
					<?php if ( is_wp_error( $categories ) || empty( $categories ) ) : ?>
						<p><?php esc_html_e( 'No categories yet. You can add one in WordPress admin.', 'recipes-hook-theme' ); ?></p>
					<?php else : ?>
						<?php foreach ( $categories as $category ) : ?>
							<label class="rht-inline-check">
								<input type="checkbox" name="rpl_recipe_categories[]" value="<?php echo esc_attr( (string) $category->term_id ); ?>" <?php checked( in_array( (int) $category->term_id, $current_categories, true ) ); ?>>
								<span><?php echo esc_html( $category->name ); ?></span>
							</label>
						<?php endforeach; ?>
					<?php endif; ?>
				</fieldset>

				<div class="rht-form-actions">
					<button type="submit" class="rpl-view-button"><?php echo esc_html( $editing_post ? __( 'Update Recipe', 'recipes-hook-theme' ) : __( 'Publish Recipe', 'recipes-hook-theme' ) ); ?></button>
					<?php if ( $editing_post ) : ?>
						<a class="rpl-secondary-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Cancel Edit', 'recipes-hook-theme' ); ?></a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<div class="rht-card rht-manage-list">
			<h2><?php esc_html_e( 'Manage Recipes', 'recipes-hook-theme' ); ?></h2>
			<p><?php esc_html_e( 'Edit or delete recipes directly from the site.', 'recipes-hook-theme' ); ?></p>
			<?php if ( empty( $user_recipes ) ) : ?>
				<p><?php esc_html_e( 'No recipes published yet.', 'recipes-hook-theme' ); ?></p>
			<?php else : ?>
				<ul class="rht-manage-items">
					<?php foreach ( $user_recipes as $recipe_item ) : ?>
						<li>
							<div>
								<strong><?php echo esc_html( get_the_title( $recipe_item ) ); ?></strong>
								<span><?php echo esc_html( get_the_date( get_option( 'date_format' ), $recipe_item ) ); ?></span>
							</div>
							<?php if ( current_user_can( 'edit_post', $recipe_item->ID ) ) : ?>
								<div class="rht-manage-actions">
									<a class="rpl-secondary-link" href="<?php echo esc_url( add_query_arg( 'rpl_edit', (string) $recipe_item->ID, home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Edit', 'recipes-hook-theme' ); ?></a>
									<?php if ( current_user_can( 'delete_post', $recipe_item->ID ) ) : ?>
										<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
											<input type="hidden" name="action" value="rpl_frontend_delete_recipe">
											<input type="hidden" name="rpl_recipe_id" value="<?php echo esc_attr( (string) $recipe_item->ID ); ?>">
											<?php wp_nonce_field( 'rpl_frontend_delete_recipe' ); ?>
											<button class="rht-delete-btn" type="submit" onclick="return confirm('<?php echo esc_js( __( 'Move this recipe to Trash?', 'recipes-hook-theme' ) ); ?>');"><?php esc_html_e( 'Delete', 'recipes-hook-theme' ); ?></button>
										</form>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
<?php else : ?>
	<section class="rht-card rht-auth-card">
		<h2><?php esc_html_e( 'Client Login', 'recipes-hook-theme' ); ?></h2>
		<p><?php esc_html_e( 'Log in to add, edit, and delete recipe PDFs from this page.', 'recipes-hook-theme' ); ?></p>
		<?php
		wp_login_form(
			array(
				'redirect'       => home_url( '/' ),
				'label_log_in'   => __( 'Log In', 'recipes-hook-theme' ),
				'remember'       => true,
				'value_remember' => true,
			)
		);
		?>
	</section>
<?php endif; ?>

<?php
if ( shortcode_exists( 'recipe_pdf_library' ) ) {
	echo do_shortcode( '[recipe_pdf_library]' );
} else {
	?>
	<div class="rpl-empty-state">
		<h2><?php esc_html_e( 'Recipe plugin inactive', 'recipes-hook-theme' ); ?></h2>
		<p><?php esc_html_e( 'Activate the Recipe PDF Library plugin to display recipes here.', 'recipes-hook-theme' ); ?></p>
	</div>
	<?php
}
get_footer();
