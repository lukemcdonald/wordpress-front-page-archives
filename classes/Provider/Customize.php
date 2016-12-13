<?php
/**
 * Customizer manager.
 *
 * @package   FrontPageArchives
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @since     1.0.0
 */

/**
 * Customizer manager class.
 *
 * @package FrontPageArchives
 * @since   1.0.0
 */
class FrontPageArchives_Provider_Customize extends FrontPageArchives_AbstractProvider {
	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'customize_register',                 array( $this, 'register_panel' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_controls_assets' ), 20 );
	}

	/**
	 * Register the Customizer controls.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customize manager.
	 */
	public function register_panel( $wp_customize ) {
		$archive_on_front = $this->archive_on_front_choices();

		if ( ! empty( $archive_on_front ) ) {

			// Remove show on front control.
			$wp_customize->remove_control( 'show_on_front' );

			// Add show on front back with archive choice.
			$wp_customize->add_control( 'show_on_front', array(
				'label'   => __( 'Front page displays' ),
				'section' => 'static_front_page',
				'type'    => 'radio',
				'choices' => array(
					'posts'   => esc_html__( 'Your latest posts', 'front-page-archives' ),
					'page'    => esc_html__( 'A static page', 'front-page-archives'  ),
					'archive' => esc_html__( 'A post type archive', 'front-page-archives'  ),
				),
				'priority' => 5,
			) );

			// Add front page archive choices.
			$wp_customize->add_setting( 'archive_on_front', array(
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_key',
				'type'              => 'option',
			) );

			$wp_customize->add_control( 'archive_on_front', array(
				'label'    => esc_html__( 'Front page archive', 'hammer' ),
				'section'  => 'static_front_page',
				'settings' => 'archive_on_front',
				'type'     => 'select',
				'choices'  => $archive_on_front,
				'priority' => 9, // before Posts page control
			) );
		}
	}

	/**
	 * Enqueue assets for the Customizer.
	 *
	 * @since 1.0.0
	 */
	function enqueue_customizer_controls_assets() {
		wp_enqueue_script(
			'front-page-archives-customize-controls',
			$this->plugin->get_url( 'assets/js/customize-controls.js' ),
			array( 'customize-controls', 'underscore' ),
			'201504061',
			true
		);
	}

	/**
	 * Set public post type archives as choices.
	 *
	 * @since 1.0.0
	 */
	function archive_on_front_choices() {
		$post_types = $this->get_post_type_objects();
		$choices    = array();

		if ( ! empty( $post_types ) ) {
			$choices = array( esc_html__( '— Select —', 'front-page-archives' ) );

			foreach ( $post_types as $post_type ) {
				$choices[$post_type->name] = $post_type->labels->name;
			}
		}

		return apply_filters( 'front_page_archives_customize_choices', $choices, $post_types );
	}

	/**
	 * Get post type objects.
	 *
	 * @since 1.0.0
	 */
	function get_post_type_objects( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'public'             => true,
			'publicly_queryable' => true,
			'_builtin'           => false,
		) );

		$post_types = get_post_types( $args, 'objects' );

		return apply_filters( 'front_page_archives_post_types', $post_types );
	}
}
