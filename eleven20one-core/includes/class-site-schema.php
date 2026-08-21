<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site-wide structured data that goes beyond what Yoast SEO generates on its
 * own: the band's Organization node becomes a MusicGroup with real members,
 * genre, founding date and social profiles (extending Yoast's existing
 * schema graph via its filter rather than emitting a second, competing
 * JSON-LD block) — plus FAQPage data for the booking FAQ on the Contact
 * page. This helps both classic rich results and AI answer engines, which
 * lean on structured/Q&A data when summarizing or citing a site.
 */
class Eleven20one_Site_Schema {

	const SOCIAL_PROFILES = array(
		'https://www.facebook.com/eleven20one',
		'https://www.instagram.com/eleven20one',
	);

	public static function init() {
		add_filter( 'wpseo_schema_organization', array( __CLASS__, 'extend_organization' ) );
		add_action( 'wp_head', array( __CLASS__, 'output_faq_schema' ) );
	}

	public static function extend_organization( $data ) {
		$data['@type']       = array( 'Organization', 'MusicGroup' );
		$data['genre']       = 'Coverband — pop, rock, soul, funk en Nederlandstalig';
		$data['foundingDate'] = '1996';
		$data['sameAs']      = self::SOCIAL_PROFILES;

		$members = get_posts(
			array(
				'post_type'      => 'e120_bandlid',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);

		if ( $members ) {
			$data['member'] = array();
			foreach ( $members as $member ) {
				$role  = function_exists( 'get_field' ) ? get_field( 'role', $member->ID ) : '';
				$entry = array(
					'@type' => 'Person',
					'name'  => get_the_title( $member ),
				);
				if ( $role ) {
					$entry['roleName'] = $role;
				}
				$data['member'][] = $entry;
			}
		}

		return $data;
	}

	public static function output_faq_schema() {
		if ( ! is_page( 'contact' ) ) {
			return;
		}

		$items = array();
		foreach ( Eleven20one_FAQ::get_items() as $item ) {
			$items[] = array(
				'@type'          => 'Question',
				'name'           => $item['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $item['answer'],
				),
			);
		}

		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $items,
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
	}
}
