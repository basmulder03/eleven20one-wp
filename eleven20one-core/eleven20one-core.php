<?php
/**
 * Plugin Name:       Eleven20one Core
 * Description:       Custom post types, dual show/ticket countdown, and an auto-syncing calendar feed for the Eleven20one website.
 * Version:           1.0.9
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Author:            Eleven20one
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       eleven20one-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'E120_CORE_VERSION', '1.0.9' );
define( 'E120_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'E120_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once E120_CORE_PATH . 'includes/class-post-types.php';
require_once E120_CORE_PATH . 'includes/class-acf-fields.php';
require_once E120_CORE_PATH . 'includes/class-countdown.php';
require_once E120_CORE_PATH . 'includes/class-ics-feed.php';
require_once E120_CORE_PATH . 'includes/class-cpt-meta.php';
require_once E120_CORE_PATH . 'includes/class-social-feeds.php';
require_once E120_CORE_PATH . 'includes/class-event-schema.php';
require_once E120_CORE_PATH . 'includes/class-faq.php';
require_once E120_CORE_PATH . 'includes/class-site-schema.php';

function e120_core_init() {
	Eleven20one_Post_Types::init();
	Eleven20one_ACF_Fields::init();
	Eleven20one_Countdown::init();
	Eleven20one_ICS_Feed::init();
	Eleven20one_CPT_Meta::init();
	Eleven20one_Social_Feeds::init();
	Eleven20one_Event_Schema::init();
	Eleven20one_FAQ::init();
	Eleven20one_Site_Schema::init();
}
add_action( 'plugins_loaded', 'e120_core_init' );

/**
 * Core's `core/shortcode` block render callback (wp-includes/blocks/shortcode.php)
 * runs the raw block text through wpautop() before the shortcode is ever expanded.
 * That's harmless for a shortcode that just outputs inline/text content, but every
 * shortcode in this plugin (countdown, shows list, FAQ, ...) renders multi-line
 * block-level HTML — divs, spans, anchors — and wpautop's line-break heuristics
 * mangle it: stray <p>/<br> tags get spliced into the middle of the markup,
 * breaking the layout (the "Volgend optreden" countdown card, the FAQ list, etc.
 * all render as broken/blank on the front end). Bypass wpautop for the Shortcode
 * block entirely and expand the shortcode straight from its raw source instead —
 * safe site-wide since every Shortcode block on this site is one of ours.
 */
function e120_core_fix_shortcode_block_autop( $block_content, $block ) {
	return do_shortcode( trim( $block['innerHTML'] ) );
}
add_filter( 'render_block_core/shortcode', 'e120_core_fix_shortcode_block_autop', 10, 2 );

function e120_core_activate() {
	Eleven20one_Post_Types::register_post_types();
	Eleven20one_ICS_Feed::add_rewrite_rule();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'e120_core_activate' );

function e120_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'e120_core_deactivate' );
