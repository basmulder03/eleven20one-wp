<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A small admin page for text that used to be hardcoded PHP (FAQ items,
 * countdown/button copy, feed placeholder text) — editable from wp-admin
 * without a code change. Built on WordPress's own admin page + options API
 * (no ACF Options Page, which is a PRO-only feature) so it costs nothing.
 */
class Eleven20one_Site_Settings {

	const OPTION_FAQ_ITEMS               = 'e120_faq_items';
	const OPTION_LABEL_SHOW_COUNTDOWN     = 'e120_label_show_countdown';
	const OPTION_LABEL_SALE_COUNTDOWN     = 'e120_label_sale_countdown';
	const OPTION_CTA_TICKETS_LIVE         = 'e120_cta_tickets_live';
	const OPTION_CTA_TICKETS_NOT_LIVE     = 'e120_cta_tickets_not_live';
	const OPTION_SOCIAL_PLACEHOLDER_TEXT  = 'e120_social_placeholder_text';

	const MAX_FAQ_ITEMS = 8;

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_page' ) );
	}

	public static function register_page() {
		add_menu_page(
			'Site-instellingen',
			'Site-instellingen',
			'manage_options',
			'e120-site-settings',
			array( __CLASS__, 'render_page' ),
			'dashicons-edit-page',
			80
		);
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['e120_site_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['e120_site_settings_nonce'] ) ), 'e120_save_site_settings' ) ) {
			self::save();
			echo '<div class="notice notice-success is-dismissible"><p>Opgeslagen.</p></div>';
		}

		$faq_items              = self::get_faq_items();
		$label_show_countdown   = get_option( self::OPTION_LABEL_SHOW_COUNTDOWN, 'Show begint over' );
		$label_sale_countdown   = get_option( self::OPTION_LABEL_SALE_COUNTDOWN, 'Tickets in verkoop over' );
		$cta_tickets_live       = get_option( self::OPTION_CTA_TICKETS_LIVE, 'Koop tickets' );
		$cta_tickets_not_live   = get_option( self::OPTION_CTA_TICKETS_NOT_LIVE, 'Tickets nog niet in de verkoop' );
		$social_placeholder     = get_option( self::OPTION_SOCIAL_PLACEHOLDER_TEXT, '%s-feed volgt binnenkort.' );

		// Always show at least one empty row beyond the existing items, up to the max, so there's room to add more.
		$rows_to_show = min( self::MAX_FAQ_ITEMS, count( $faq_items ) + 1 );
		?>
		<div class="wrap">
			<h1>Site-instellingen</h1>
			<form method="post">
				<?php wp_nonce_field( 'e120_save_site_settings', 'e120_site_settings_nonce' ); ?>

				<h2>Veelgestelde vragen</h2>
				<p class="description">Laat een vraag leeg om die regel te verwijderen.</p>
				<table class="form-table" role="presentation">
					<?php for ( $i = 0; $i < $rows_to_show; $i++ ) : ?>
						<?php $item = isset( $faq_items[ $i ] ) ? $faq_items[ $i ] : array( 'question' => '', 'answer' => '' ); ?>
						<tr>
							<th scope="row">Vraag <?php echo (int) ( $i + 1 ); ?></th>
							<td>
								<input type="text" class="large-text" name="faq_question[]" value="<?php echo esc_attr( $item['question'] ); ?>" placeholder="Vraag" />
								<textarea class="large-text" rows="2" name="faq_answer[]" placeholder="Antwoord"><?php echo esc_textarea( $item['answer'] ); ?></textarea>
							</td>
						</tr>
					<?php endfor; ?>
				</table>

				<h2>Countdown-teksten</h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="label_show_countdown">Label: aftellen naar de show</label></th>
						<td><input type="text" class="regular-text" id="label_show_countdown" name="label_show_countdown" value="<?php echo esc_attr( $label_show_countdown ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="label_sale_countdown">Label: aftellen naar ticketverkoop</label></th>
						<td><input type="text" class="regular-text" id="label_sale_countdown" name="label_sale_countdown" value="<?php echo esc_attr( $label_sale_countdown ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="cta_tickets_live">Knoptekst: tickets te koop</label></th>
						<td><input type="text" class="regular-text" id="cta_tickets_live" name="cta_tickets_live" value="<?php echo esc_attr( $cta_tickets_live ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="cta_tickets_not_live">Knoptekst: tickets nog niet te koop</label></th>
						<td><input type="text" class="regular-text" id="cta_tickets_not_live" name="cta_tickets_not_live" value="<?php echo esc_attr( $cta_tickets_not_live ); ?>" /></td>
					</tr>
				</table>

				<h2>Social feeds</h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="social_placeholder_text">Placeholder-tekst (nog niet gekoppelde feed)</label></th>
						<td>
							<input type="text" class="regular-text" id="social_placeholder_text" name="social_placeholder_text" value="<?php echo esc_attr( $social_placeholder ); ?>" />
							<p class="description">Gebruik %s voor de platformnaam (Instagram, Facebook, YouTube).</p>
						</td>
					</tr>
				</table>

				<?php submit_button( 'Opslaan' ); ?>
			</form>
		</div>
		<?php
	}

	private static function save() {
		check_admin_referer( 'e120_save_site_settings', 'e120_site_settings_nonce' );

		$questions = isset( $_POST['faq_question'] ) ? (array) wp_unslash( $_POST['faq_question'] ) : array();
		$answers   = isset( $_POST['faq_answer'] ) ? (array) wp_unslash( $_POST['faq_answer'] ) : array();

		$items = array();
		foreach ( $questions as $i => $question ) {
			$question = sanitize_text_field( $question );
			$answer   = isset( $answers[ $i ] ) ? sanitize_textarea_field( $answers[ $i ] ) : '';

			if ( '' === $question ) {
				continue;
			}

			$items[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
		}

		update_option( self::OPTION_FAQ_ITEMS, $items );
		update_option( self::OPTION_LABEL_SHOW_COUNTDOWN, sanitize_text_field( wp_unslash( $_POST['label_show_countdown'] ?? '' ) ) );
		update_option( self::OPTION_LABEL_SALE_COUNTDOWN, sanitize_text_field( wp_unslash( $_POST['label_sale_countdown'] ?? '' ) ) );
		update_option( self::OPTION_CTA_TICKETS_LIVE, sanitize_text_field( wp_unslash( $_POST['cta_tickets_live'] ?? '' ) ) );
		update_option( self::OPTION_CTA_TICKETS_NOT_LIVE, sanitize_text_field( wp_unslash( $_POST['cta_tickets_not_live'] ?? '' ) ) );
		update_option( self::OPTION_SOCIAL_PLACEHOLDER_TEXT, sanitize_text_field( wp_unslash( $_POST['social_placeholder_text'] ?? '' ) ) );
	}

	/**
	 * @return array[] Each item has 'question' and 'answer' keys.
	 */
	public static function get_faq_items() {
		$items = get_option( self::OPTION_FAQ_ITEMS, null );

		return is_array( $items ) ? $items : Eleven20one_FAQ::DEFAULT_ITEMS;
	}
}
