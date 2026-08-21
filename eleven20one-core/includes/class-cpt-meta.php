<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Small render helpers for CPT meta fields, used from static block-theme templates
 * (which can't call PHP/ACF functions directly) via shortcodes.
 */
class Eleven20one_CPT_Meta {

	public static function init() {
		add_shortcode( 'eleven20one_bandlid_meta', array( __CLASS__, 'render_bandlid_meta' ) );
		add_shortcode( 'eleven20one_portfolio_meta', array( __CLASS__, 'render_portfolio_meta' ) );
	}

	public static function render_bandlid_meta() {
		if ( ! is_singular( 'e120_bandlid' ) ) {
			return '';
		}

		$role = function_exists( 'get_field' ) ? get_field( 'role', get_the_ID() ) : '';
		if ( ! $role ) {
			return '';
		}

		return '<p class="e120-meta-role">' . esc_html( $role ) . '</p>';
	}

	public static function render_portfolio_meta() {
		if ( ! is_singular( 'e120_portfolio' ) ) {
			return '';
		}

		$client = function_exists( 'get_field' ) ? get_field( 'client_name', get_the_ID() ) : '';
		$link   = function_exists( 'get_field' ) ? get_field( 'link_url', get_the_ID() ) : '';

		$html = '';
		if ( $client ) {
			$html .= '<p class="e120-meta-client">' . esc_html( $client ) . '</p>';
		}
		if ( $link ) {
			$html .= '<p><a class="wp-block-button__link wp-element-button" href="' . esc_url( $link ) . '" target="_blank" rel="noopener">' . esc_html__( 'Bekijk referentie', 'eleven20one-core' ) . '</a></p>';
		}

		return $html;
	}
}
