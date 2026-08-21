<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Booking FAQ — a single source of truth for both the visible [eleven20one_faq]
 * shortcode output and the FAQPage schema in class-site-schema.php, so the
 * two can never drift out of sync (Google requires FAQ schema to match
 * visible on-page text). Q&A pairs are also exactly the format AI answer
 * engines favour when extracting/citing a direct answer.
 *
 * Editable from wp-admin (Site-instellingen, see class-site-settings.php).
 * DEFAULT_ITEMS is only the fallback used before that page has ever been saved.
 */
class Eleven20one_FAQ {

	const DEFAULT_ITEMS = array(
		array(
			'question' => 'Kan ik Eleven20one boeken voor mijn bruiloft of bedrijfsfeest?',
			'answer'   => 'Zeker! Wij spelen regelmatig op bruiloften, bedrijfsfeesten, festivals en andere evenementen. Neem contact op via het formulier hieronder voor een offerte op maat.',
		),
		array(
			'question' => 'In welke regio\'s treden jullie op?',
			'answer'   => 'We zijn gevestigd in Landsmeer en treden door heel Nederland op — van kleine cafés tot grote festivalpodia.',
		),
		array(
			'question' => 'Wat kost het om Eleven20one te boeken?',
			'answer'   => 'De prijs hangt af van het type evenement, de locatie en de duur van het optreden. Vul het contactformulier in en we sturen je een offerte op maat.',
		),
		array(
			'question' => 'Kunnen jullie het repertoire aanpassen aan onze wensen?',
			'answer'   => 'Jazeker, we stemmen ons repertoire graag af op de gelegenheid — laat je wensen weten bij het aanvragen van een offerte.',
		),
		array(
			'question' => 'Met hoeveel man staan jullie op het podium?',
			'answer'   => 'We spelen met negen muzikanten: een ritmesectie, twee gitaristen/toetsenist, een blazerstrio (de Golden Horns) en twee zangers.',
		),
	);

	public static function init() {
		add_shortcode( 'eleven20one_faq', array( __CLASS__, 'render' ) );
	}

	/**
	 * @return array[] Each item has 'question' and 'answer' keys.
	 */
	public static function get_items() {
		return Eleven20one_Site_Settings::get_faq_items();
	}

	public static function render() {
		ob_start();
		?>
		<div class="e120-faq">
			<?php foreach ( self::get_items() as $item ) : ?>
				<details class="e120-faq__item">
					<summary class="e120-faq__question"><?php echo esc_html( $item['question'] ); ?></summary>
					<p class="e120-faq__answer"><?php echo esc_html( $item['answer'] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
