<?php
/**
 * Main plugin.
 *
 * @package   FrontPageArchives
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Main plugin class.
 *
 * @package FrontPageArchives
 * @since   1.0.0
 */
class FrontPageArchives_Plugin extends FrontPageArchives_AbstractPlugin {
	/**
	 * Load the plugin.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin() {
		add_filter( 'plugin_action_links_' . $this->get_basename(), array( $this, 'filter_action_links' ) );
		add_action( 'pre_get_posts',                                array( $this, 'pre_get_posts' ) );
		add_filter( 'body_class',                                   array( $this, 'add_body_classes' ) );
	}

	/**
	 * Add body classes based on Billboard settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Array of classes.
	 */
	public function add_body_classes( $classes ) {
		$show_on_front    = get_option( 'show_on_front' );
		$archive_on_front = get_option( 'archive_on_front' );

		if (
			is_home()
			&& 'archive' === $show_on_front
			&& ! empty( $archive_on_front )
		) {
			$classes[] = 'front-page-archive';
		}

		return $classes;
	}

	/**
	 * Filter plugin action links.
	 *
	 * Adds a 'Manage' link pointing to the Customizer panel.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $actions Array of action links.
	 * @return array
	 */
	public function filter_action_links( $actions ) {
		$actions['manage'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $this->get_customizer_url() ),
			esc_html__( 'Manage', 'front-page-archives' )
		);

		return $actions;
	}

	/**
	 * Retrieve a deep link to the Static Front Page section in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_customizer_url() {
		return admin_url( 'customize.php?autofocus[section]=static_front_page' );
	}

	/**
	 * Filter front page to display post type archive if set.
	 *
	 * @since 1.0.0
	 */
	public function pre_get_posts( $query ) {
		if ( is_admin() ) {
			return;
		}

		$show_on_front    = get_option( 'show_on_front' );
		$archive_on_front = get_option( 'archive_on_front' );

		if ( 'archive' !== $show_on_front || empty( $archive_on_front ) ) {
			return;
		}

		$post_types = get_post_types( array(
			'public'             => true,
			'publicly_queryable' => true,
			'_builtin'           => false,
		) );

		if ( in_array( $archive_on_front, $post_types ) && $query->is_home() && $query->is_main_query() ) {
			$query->set( 'post_type', $archive_on_front );

			// Set properties that describe the page to reflect that we aren't
			// really displaying a static page.
			$query->is_page              = 0;
			$query->is_singular          = 0;
			$query->is_post_type_archive = 1;
			$query->is_archive           = 1;
		}
	}
}
