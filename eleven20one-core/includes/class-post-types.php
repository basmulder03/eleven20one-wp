<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eleven20one_Post_Types {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ) );
	}

	public static function register_post_types() {
		register_post_type(
			'e120_optreden',
			array(
				'labels'       => array(
					'name'                  => __( 'Optredens', 'eleven20one-core' ),
					'singular_name'         => __( 'Optreden', 'eleven20one-core' ),
					'add_new_item'          => __( 'Nieuw optreden toevoegen', 'eleven20one-core' ),
					'edit_item'             => __( 'Optreden bewerken', 'eleven20one-core' ),
					'all_items'             => __( 'Optredens', 'eleven20one-core' ),
					'menu_name'             => __( 'Optredens', 'eleven20one-core' ),
				),
				'public'       => true,
				'has_archive'  => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-calendar-alt',
				'rewrite'      => array( 'slug' => 'optredens' ),
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'menu_position' => 20,
			)
		);

		register_post_type(
			'e120_bandlid',
			array(
				'labels'        => array(
					'name'          => __( 'Bandleden', 'eleven20one-core' ),
					'singular_name' => __( 'Bandlid', 'eleven20one-core' ),
					'add_new_item'  => __( 'Nieuw bandlid toevoegen', 'eleven20one-core' ),
					'edit_item'     => __( 'Bandlid bewerken', 'eleven20one-core' ),
					'all_items'     => __( 'Wie zijn wij', 'eleven20one-core' ),
					'menu_name'     => __( 'Wie zijn wij', 'eleven20one-core' ),
				),
				'public'        => true,
				'has_archive'   => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-groups',
				'rewrite'       => array( 'slug' => 'wie-zijn-wij' ),
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'menu_position' => 21,
			)
		);

		register_post_type(
			'e120_portfolio',
			array(
				'labels'        => array(
					'name'          => __( 'Portfolio', 'eleven20one-core' ),
					'singular_name' => __( 'Portfolio-item', 'eleven20one-core' ),
					'add_new_item'  => __( 'Nieuw portfolio-item toevoegen', 'eleven20one-core' ),
					'edit_item'     => __( 'Portfolio-item bewerken', 'eleven20one-core' ),
					'all_items'     => __( 'Portfolio', 'eleven20one-core' ),
					'menu_name'     => __( 'Portfolio', 'eleven20one-core' ),
				),
				'public'        => true,
				'has_archive'   => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-portfolio',
				'rewrite'       => array( 'slug' => 'portfolio' ),
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				'menu_position' => 22,
			)
		);
	}
}
