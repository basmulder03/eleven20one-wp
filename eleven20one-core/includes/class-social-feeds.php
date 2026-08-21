<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wraps the Smash Balloon shortcodes so an unconnected account shows a clean,
 * on-brand placeholder instead of their raw admin-only error markup (and, for
 * the YouTube plugin specifically, avoids triggering its buggy unconfigured
 * settings code path — which throws PHP warnings — by never calling it until
 * a real feed is producing output).
 */
class Eleven20one_Social_Feeds {

	const TYPES = array(
		'instagram' => array(
			'shortcode' => 'instagram-feed',
			'label'     => 'Instagram',
			'settings'  => 'admin.php?page=sbi-feed-builder',
		),
		'facebook'  => array(
			'shortcode' => 'custom-facebook-feed',
			'label'     => 'Facebook',
			'settings'  => 'admin.php?page=cff-feed-builder',
		),
		'youtube'   => array(
			'shortcode' => 'youtube-feed',
			'label'     => 'YouTube',
			'settings'  => 'admin.php?page=sby-feed-builder',
		),
	);

	public static function init() {
		add_shortcode( 'eleven20one_social', array( __CLASS__, 'render' ) );
	}

	public static function render( $atts = array() ) {
		$atts = shortcode_atts( array( 'type' => '' ), (array) $atts );
		$type = $atts['type'];

		if ( ! isset( self::TYPES[ $type ] ) ) {
			return '';
		}

		$config = self::TYPES[ $type ];

		if ( ! shortcode_exists( $config['shortcode'] ) ) {
			return '';
		}

		$output = do_shortcode( '[' . $config['shortcode'] . ']' );

		if ( self::is_unconfigured( $output ) ) {
			return self::render_placeholder( $config );
		}

		wp_enqueue_style( 'e120-countdown' );

		return '<div class="e120-social-feed">' . $output . '</div>';
	}

	private static function is_unconfigured( $output ) {
		if ( '' === trim( wp_strip_all_tags( $output ) ) ) {
			return true;
		}

		foreach ( array( 'sbi_mod_error', 'cff-no-id', 'sby_mod_error' ) as $marker ) {
			if ( false !== strpos( $output, $marker ) ) {
				return true;
			}
		}

		return false;
	}

	private static function render_placeholder( $config ) {
		wp_enqueue_style( 'e120-countdown' );

		$placeholder_format = get_option( 'e120_social_placeholder_text' );
		if ( ! $placeholder_format ) {
			/* translators: %s: platform name, e.g. Instagram */
			$placeholder_format = __( '%s-feed volgt binnenkort.', 'eleven20one-core' );
		}

		$html  = '<div class="e120-social-feed e120-social-feed--empty">';
		$html .= '<p>' . esc_html( sprintf( $placeholder_format, $config['label'] ) ) . '</p>';

		if ( current_user_can( 'manage_options' ) ) {
			$html .= '<p class="e120-social-feed__admin-hint"><a href="' . esc_url( admin_url( $config['settings'] ) ) . '">'
				. esc_html__( 'Account koppelen in de instellingen →', 'eleven20one-core' )
				. '</a></p>';
		}

		$html .= '</div>';

		return $html;
	}
}
