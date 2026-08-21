<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function eleven20one_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'width'       => 480,
			'height'      => 96,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Without this, theme support for 'editor-styles' declares nothing to load:
	// the block/site editor canvas falls back to unstyled block markup (no card
	// backgrounds/padding/radius on the Query Loop grids, no button/hover styles).
	add_editor_style( 'assets/css/theme.css' );

	add_image_size( 'e120-portrait', 600, 750, true );
	add_image_size( 'e120-card', 640, 480, true );
}
add_action( 'after_setup_theme', 'eleven20one_setup' );

function eleven20one_assets() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'eleven20one-style', get_stylesheet_uri(), array(), $version );
	wp_enqueue_style( 'eleven20one-theme', get_theme_file_uri( 'assets/css/theme.css' ), array( 'eleven20one-style' ), $version );
	wp_enqueue_script( 'eleven20one-reveal', get_theme_file_uri( 'assets/js/reveal.js' ), array(), $version, true );
}
add_action( 'wp_enqueue_scripts', 'eleven20one_assets' );
