<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field groups are registered in code (not the ACF UI) so they live in version control
 * alongside the rest of the plugin. Requires the free Advanced Custom Fields plugin.
 */
class Eleven20one_ACF_Fields {

	public static function init() {
		add_action( 'acf/include_fields', array( __CLASS__, 'register_fields' ) );
	}

	public static function register_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_e120_optreden',
				'title'    => 'Optreden details',
				'fields'   => array(
					array(
						'key'           => 'field_e120_show_datetime',
						'label'         => 'Datum & tijd optreden',
						'name'          => 'show_datetime',
						'type'          => 'date_time_picker',
						'display_format' => 'd-m-Y H:i',
						'return_format' => 'Y-m-d H:i:s',
						'required'      => 1,
					),
					array(
						'key'           => 'field_e120_ticket_sale_datetime',
						'label'         => 'Datum & tijd start ticketverkoop',
						'name'          => 'ticket_sale_datetime',
						'type'          => 'date_time_picker',
						'display_format' => 'd-m-Y H:i',
						'return_format' => 'Y-m-d H:i:s',
						'instructions'  => 'Laat leeg als tickets al beschikbaar zijn.',
					),
					array(
						'key'      => 'field_e120_ticket_shop_url',
						'label'    => 'Link naar ticketshop',
						'name'     => 'ticket_shop_url',
						'type'     => 'url',
					),
					array(
						'key'      => 'field_e120_location',
						'label'    => 'Locatie',
						'name'     => 'location',
						'type'     => 'text',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'e120_optreden',
						),
					),
				),
				'show_in_rest' => 1,
			)
		);

		acf_add_local_field_group(
			array(
				'key'      => 'group_e120_bandlid',
				'title'    => 'Bandlid details',
				'fields'   => array(
					array(
						'key'   => 'field_e120_role',
						'label' => 'Rol / instrument',
						'name'  => 'role',
						'type'  => 'text',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'e120_bandlid',
						),
					),
				),
				'show_in_rest' => 1,
			)
		);

		acf_add_local_field_group(
			array(
				'key'      => 'group_e120_portfolio',
				'title'    => 'Portfolio details',
				'fields'   => array(
					array(
						'key'   => 'field_e120_client_name',
						'label' => 'Opdrachtgever / project',
						'name'  => 'client_name',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_e120_link_url',
						'label' => 'Link naar referentie',
						'name'  => 'link_url',
						'type'  => 'url',
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'e120_portfolio',
						),
					),
				),
				'show_in_rest' => 1,
			)
		);
	}
}
