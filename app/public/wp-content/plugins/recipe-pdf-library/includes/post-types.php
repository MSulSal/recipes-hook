<?php
/**
 * Post type and taxonomy registration.
 *
 * @package Recipe_PDF_Library
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rpl_register_post_type(): void {
	$labels = array(
		'name'                  => __( 'Recipes', 'recipe-pdf-library' ),
		'singular_name'         => __( 'Recipe', 'recipe-pdf-library' ),
		'menu_name'             => __( 'Recipes', 'recipe-pdf-library' ),
		'name_admin_bar'        => __( 'Recipe', 'recipe-pdf-library' ),
		'add_new'               => __( 'Add New', 'recipe-pdf-library' ),
		'add_new_item'          => __( 'Add New Recipe', 'recipe-pdf-library' ),
		'new_item'              => __( 'New Recipe', 'recipe-pdf-library' ),
		'edit_item'             => __( 'Edit Recipe', 'recipe-pdf-library' ),
		'view_item'             => __( 'View Recipe', 'recipe-pdf-library' ),
		'all_items'             => __( 'All Recipes', 'recipe-pdf-library' ),
		'search_items'          => __( 'Search Recipes', 'recipe-pdf-library' ),
		'not_found'             => __( 'No recipes found.', 'recipe-pdf-library' ),
		'not_found_in_trash'    => __( 'No recipes found in Trash.', 'recipe-pdf-library' ),
		'featured_image'        => __( 'Recipe Image', 'recipe-pdf-library' ),
		'set_featured_image'    => __( 'Set recipe image', 'recipe-pdf-library' ),
		'remove_featured_image' => __( 'Remove recipe image', 'recipe-pdf-library' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'has_archive'        => 'recipe-archive',
		'menu_icon'          => 'dashicons-food',
		'rewrite'            => array( 'slug' => 'recipe' ),
		'show_in_rest'       => true,
		'supports'           => array( 'title', 'editor', 'thumbnail' ),
		'taxonomies'         => array( 'recipe_category', 'recipe_tag' ),
		'capability_type'    => 'post',
		'map_meta_cap'       => true,
		'publicly_queryable' => true,
	);

	register_post_type( 'recipe_pdf', $args );
}

function rpl_register_taxonomies(): void {
	$category_labels = array(
		'name'              => __( 'Recipe Categories', 'recipe-pdf-library' ),
		'singular_name'     => __( 'Recipe Category', 'recipe-pdf-library' ),
		'search_items'      => __( 'Search Recipe Categories', 'recipe-pdf-library' ),
		'all_items'         => __( 'All Recipe Categories', 'recipe-pdf-library' ),
		'edit_item'         => __( 'Edit Recipe Category', 'recipe-pdf-library' ),
		'update_item'       => __( 'Update Recipe Category', 'recipe-pdf-library' ),
		'add_new_item'      => __( 'Add New Recipe Category', 'recipe-pdf-library' ),
		'new_item_name'     => __( 'New Recipe Category Name', 'recipe-pdf-library' ),
		'menu_name'         => __( 'Recipe Categories', 'recipe-pdf-library' ),
	);

	register_taxonomy(
		'recipe_category',
		array( 'recipe_pdf' ),
		array(
			'hierarchical'      => true,
			'labels'            => $category_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'recipe-category' ),
		)
	);

	$tag_labels = array(
		'name'                       => __( 'Recipe Tags', 'recipe-pdf-library' ),
		'singular_name'              => __( 'Recipe Tag', 'recipe-pdf-library' ),
		'search_items'               => __( 'Search Recipe Tags', 'recipe-pdf-library' ),
		'popular_items'              => __( 'Popular Recipe Tags', 'recipe-pdf-library' ),
		'all_items'                  => __( 'All Recipe Tags', 'recipe-pdf-library' ),
		'edit_item'                  => __( 'Edit Recipe Tag', 'recipe-pdf-library' ),
		'update_item'                => __( 'Update Recipe Tag', 'recipe-pdf-library' ),
		'add_new_item'               => __( 'Add New Recipe Tag', 'recipe-pdf-library' ),
		'new_item_name'              => __( 'New Recipe Tag Name', 'recipe-pdf-library' ),
		'separate_items_with_commas' => __( 'Separate recipe tags with commas', 'recipe-pdf-library' ),
		'add_or_remove_items'        => __( 'Add or remove recipe tags', 'recipe-pdf-library' ),
		'choose_from_most_used'      => __( 'Choose from the most used recipe tags', 'recipe-pdf-library' ),
		'menu_name'                  => __( 'Recipe Tags', 'recipe-pdf-library' ),
	);

	register_taxonomy(
		'recipe_tag',
		array( 'recipe_pdf' ),
		array(
			'hierarchical'      => false,
			'labels'            => $tag_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'recipe-tag' ),
		)
	);
}

function rpl_force_recipe_private_status( array $data, array $postarr ): array {
	if ( empty( $data['post_type'] ) || 'recipe_pdf' !== $data['post_type'] ) {
		return $data;
	}

	$allowed_statuses = array( 'trash', 'auto-draft', 'inherit' );

	if ( isset( $data['post_status'] ) && in_array( $data['post_status'], $allowed_statuses, true ) ) {
		return $data;
	}

	$data['post_status'] = 'private';

	return $data;
}
add_filter( 'wp_insert_post_data', 'rpl_force_recipe_private_status', 10, 2 );

function rpl_strip_recipe_private_prefix( string $title, int $post_id ): string {
	if ( 'recipe_pdf' !== get_post_type( $post_id ) ) {
		return $title;
	}

	return preg_replace( '/^(Private|Protected):\s*/i', '', $title ) ?: $title;
}
add_filter( 'the_title', 'rpl_strip_recipe_private_prefix', 10, 2 );

function rpl_remove_recipe_private_post_state( array $states, WP_Post $post ): array {
	if ( 'recipe_pdf' !== $post->post_type ) {
		return $states;
	}

	unset( $states['private'] );

	return $states;
}
add_filter( 'display_post_states', 'rpl_remove_recipe_private_post_state', 10, 2 );
