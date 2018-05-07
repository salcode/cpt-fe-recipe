<?php
/**
 * Plugin Name: Iron Code Recipe Custom Post Type
 * Plugin URI: https://salferrarello.com/cpt-best-practices/
 * Description: Register a WordPress Custom Post Type (CPT) `fe_recipe` with a custom Taxonomy `fe_recipe_tag`.
 * Version: 1.4.1
 * Author: Sal Ferrarello
 * Author URI: http://salferrarello.com/
 * Text Domain: fe-recipe-cpt
 * Domain Path: /languages
 *
 * @package fe-recipe-cpt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

register_activation_hook(   __FILE__, 'fe_recipe_cpt_activation' );
register_deactivation_hook( __FILE__, 'fe_recipe_cpt_deactivation' );

add_action( 'init', 'fe_recipe_cpt' );

add_action( 'pre_get_posts', 'fe_recipe_cpt_modify_archive' );

/**
 * Modify Archive Page
 * - Increase number of posts shown on Recipe archive page.
 *
 * @param WP_Query $query The current query.
 */
function fe_recipe_cpt_modify_archive( $query ) {
	if ( ! is_post_type_archive( 'fe_recipe' ) ) {
		return;
	}
	$query->set( 'posts_per_page', 50 );
}

/**
 * Register a public CPT and Taxonomy
 */
function fe_recipe_cpt() {

	// Post type should be prefixed, singular, and no more than 20 characters.
	register_post_type( 'fe_recipe', array(
		// Label should be plural and L10n ready.
		'label'       => __( 'Recipes', 'fe_recipe' ),
		'public'      => true,
		'has_archive' => true,
		'rewrite'     => array(
			// Slug should be plural and L10n ready.
			'slug'        => _x( 'recipes', 'CPT permalink slug', 'fe_recipe' ),
			'with_front'  => false,
		),

		/**
		 * 'title', 'editor', 'thumbnail' 'author', 'excerpt','custom-fields',
		 * 'page-attributes' (menu order),'revisions' (will store revisions),
		 * 'trackbacks', 'comments', 'post-formats',
		 */
		'support'     => array( 'title', 'editor' ),

		// Url to icon or choose from built-in https://developer.wordpress.org/resource/dashicons/.
		'menu_icon'   => 'dashicons-feedback',
	) );

	register_taxonomy(
		'fe_recipe_tag',
		'fe_recipe',
		array(
			// Label should be plural and L10n ready.
			'label'             => __( 'Recipe Tags', 'fe_recipe' ),
			'show_admin_column' => true,
			'rewrite'           => array(
				// Slug should be singular and L10n ready..
				'slug' => _x( 'recipe-tag', 'Custom Taxonomy slug', 'fe_recipe' ),
			),
		)
	);
}

/**
 * Load Recipe Custom Post Type and Flush Rewrite Rules
 *
 * We run this on plugin activation to prevent the problem of the custom post
 * type URLs not loading initially (because their URL pattern is not included
 * in the cached rewrite rules). We explicitly call the code to register the
 * custom post type because that code, which executes on the `init`, hook
 * has not yet executed.
 */
function fe_recipe_cpt_activation() {
	fe_recipe_cpt();
	flush_rewrite_rules();
}

/**
 * Flush the rewrite rules.
 *
 * We run this on plugin deactivation to ensure the rewrite rules no longer
 * included the URL pattern for our Custom Post Type.
 */
function fe_recipe_cpt_deactivation() {
	flush_rewrite_rules();
	flush_rewrite_rules();
}
