<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs schema.org MusicEvent JSON-LD on single optreden pages, so shows
 * are eligible for Google's Event rich results (date/venue/ticket link
 * shown directly in search) rather than just a plain blue link.
 */
class Eleven20one_Event_Schema {

	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'output' ) );
	}

	public static function output() {
		if ( ! is_singular( 'e120_optreden' ) || ! function_exists( 'get_field' ) ) {
			return;
		}

		$post_id       = get_the_ID();
		$show_datetime = get_field( 'show_datetime', $post_id );
		if ( ! $show_datetime ) {
			return;
		}

		$start = DateTime::createFromFormat( 'Y-m-d H:i:s', $show_datetime, wp_timezone() );
		if ( ! $start ) {
			return;
		}
		$end = clone $start;
		$end->modify( '+2 hours' );

		$location    = get_field( 'location', $post_id );
		$ticket_url  = get_field( 'ticket_shop_url', $post_id );
		$description = wp_strip_all_tags( get_the_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : get_the_content( null, false, $post_id ) );

		$schema = array(
			'@context'            => 'https://schema.org',
			'@type'               => 'MusicEvent',
			'name'                => get_the_title( $post_id ),
			'startDate'           => $start->format( DATE_ATOM ),
			'endDate'             => $end->format( DATE_ATOM ),
			'eventStatus'         => 'https://schema.org/EventScheduled',
			'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
			'url'                 => get_permalink( $post_id ),
			'performer'           => array(
				'@type' => 'MusicGroup',
				'name'  => 'Eleven20one',
			),
		);

		if ( $description ) {
			$schema['description'] = $description;
		}

		if ( has_post_thumbnail( $post_id ) ) {
			$schema['image'] = array( get_the_post_thumbnail_url( $post_id, 'full' ) );
		}

		if ( $location ) {
			$schema['location'] = array(
				'@type' => 'Place',
				'name'  => $location,
			);
		} else {
			// A MusicEvent without a location is invalid per schema.org — fall
			// back to an online-event marker rather than omitting it entirely.
			$schema['eventAttendanceMode'] = 'https://schema.org/OnlineEventAttendanceMode';
		}

		if ( $ticket_url ) {
			$schema['offers'] = array(
				'@type'         => 'Offer',
				'url'           => $ticket_url,
				'priceCurrency' => 'EUR',
				'availability'  => 'https://schema.org/InStock',
			);
		}

		echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
	}
}
