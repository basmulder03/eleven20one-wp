<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Publishes all optredens as a public .ics feed at /kalender.ics so fans and
 * organisers can subscribe from Google/Apple/Outlook calendar ("add by URL")
 * and get new/changed shows automatically, without any OAuth or API keys.
 */
class Eleven20one_ICS_Feed {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'add_rewrite_rule' ) );
		add_filter( 'query_vars', array( __CLASS__, 'add_query_var' ) );
		add_filter( 'redirect_canonical', array( __CLASS__, 'skip_canonical_redirect' ) );
		add_action( 'template_redirect', array( __CLASS__, 'maybe_output_feed' ) );
	}

	/**
	 * redirect_canonical() runs on template_redirect before this class's own
	 * handler (it's wired up via WordPress's default-filters.php, which loads
	 * before our plugin's plugins_loaded callback registers maybe_output_feed
	 * on the same hook/priority) — so without this, every request for the
	 * extensionless /kalender.ics gets 301'd to /kalender.ics/ first. Clients
	 * follow the redirect fine, but it's a needless extra round trip.
	 */
	public static function skip_canonical_redirect( $redirect_url ) {
		if ( get_query_var( 'e120_ics' ) ) {
			return false;
		}
		return $redirect_url;
	}

	public static function add_rewrite_rule() {
		add_rewrite_rule( '^kalender\.ics$', 'index.php?e120_ics=1', 'top' );
	}

	public static function add_query_var( $vars ) {
		$vars[] = 'e120_ics';
		return $vars;
	}

	public static function maybe_output_feed() {
		if ( ! get_query_var( 'e120_ics' ) ) {
			return;
		}

		$shows = get_posts(
			array(
				'post_type'      => 'e120_optreden',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_key'       => 'show_datetime',
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
			)
		);

		header( 'Content-Type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: inline; filename="eleven20one-optredens.ics"' );

		echo "BEGIN:VCALENDAR\r\n";
		echo "VERSION:2.0\r\n";
		echo "PRODID:-//Eleven20one//Optredens//NL\r\n";
		echo "CALSCALE:GREGORIAN\r\n";
		echo "METHOD:PUBLISH\r\n";
		echo 'X-WR-CALNAME:' . self::escape_text( get_bloginfo( 'name' ) . ' optredens' ) . "\r\n";
		echo "REFRESH-INTERVAL;VALUE=DURATION:PT1H\r\n";
		echo "X-PUBLISHED-TTL:PT1H\r\n";

		foreach ( $shows as $show ) {
			$show_datetime = function_exists( 'get_field' ) ? get_field( 'show_datetime', $show->ID ) : '';
			if ( ! $show_datetime ) {
				continue;
			}

			$location = function_exists( 'get_field' ) ? get_field( 'location', $show->ID ) : '';

			/*
			 * show_datetime is a plain "Y-m-d H:i:s" string meant as local site
			 * time (that's how it's entered and how the rest of the plugin reads
			 * it via current_time()). strtotime() would parse it against PHP's
			 * raw default timezone, which WordPress forces to UTC — silently
			 * treating "20:30 Amsterdam time" as "20:30 UTC" and shipping every
			 * event an hour (or two, in summer) off. Parse it explicitly against
			 * the site's configured timezone instead, then convert to UTC.
			 */
			$start_dt = DateTime::createFromFormat( 'Y-m-d H:i:s', $show_datetime, wp_timezone() );
			if ( ! $start_dt ) {
				continue;
			}
			$start_dt->setTimezone( new DateTimeZone( 'UTC' ) );
			$start_ts = $start_dt->getTimestamp();
			$end_ts   = $start_ts + 2 * HOUR_IN_SECONDS;

			echo "BEGIN:VEVENT\r\n";
			echo 'UID:e120-optreden-' . $show->ID . '@' . wp_parse_url( home_url(), PHP_URL_HOST ) . "\r\n";
			echo 'DTSTAMP:' . gmdate( 'Ymd\THis\Z', time() ) . "\r\n";
			echo 'DTSTART:' . gmdate( 'Ymd\THis\Z', $start_ts ) . "\r\n";
			echo 'DTEND:' . gmdate( 'Ymd\THis\Z', $end_ts ) . "\r\n";
			echo 'SUMMARY:' . self::escape_text( get_the_title( $show ) ) . "\r\n";
			if ( $location ) {
				echo 'LOCATION:' . self::escape_text( $location ) . "\r\n";
			}
			echo 'URL:' . esc_url( get_permalink( $show ) ) . "\r\n";
			echo 'DESCRIPTION:' . self::escape_text( wp_strip_all_tags( $show->post_excerpt ? $show->post_excerpt : $show->post_content ) ) . "\r\n";
			echo "END:VEVENT\r\n";
		}

		echo "END:VCALENDAR\r\n";
		exit;
	}

	private static function escape_text( $text ) {
		$text = str_replace( array( "\\", ";", "," ), array( '\\\\', '\\;', '\\,' ), (string) $text );
		return str_replace( array( "\r\n", "\n" ), '\\n', $text );
	}
}
