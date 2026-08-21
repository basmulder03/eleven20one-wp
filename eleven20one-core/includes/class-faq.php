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
 */
class Eleven20one_FAQ {

	const ITEMS = array(
		array(
			'q' => 'Kan ik Eleven20one boeken voor mijn bruiloft of bedrijfsfeest?',
			'a' => 'Zeker! Wij spelen regelmatig op bruiloften, bedrijfsfeesten, festivals en andere evenementen. Neem contact op via het formulier hieronder voor een offerte op maat.',
		),
		array(
			'q' => 'In welke regio\'s treden jullie op?',
			'a' => 'We zijn gevestigd in Landsmeer en treden door heel Nederland op — van kleine cafés tot grote festivalpodia.',
		),
		array(
			'q' => 'Wat kost het om Eleven20one te boeken?',
			'a' => 'De prijs hangt af van het type evenement, de locatie en de duur van het optreden. Vul het contactformulier in en we sturen je een offerte op maat.',
		),
		array(
			'q' => 'Kunnen jullie het repertoire aanpassen aan onze wensen?',
			'a' => 'Jazeker, we stemmen ons repertoire graag af op de gelegenheid — laat je wensen weten bij het aanvragen van een offerte.',
		),
		array(
			'q' => 'Met hoeveel man staan jullie op het podium?',
			'a' => 'We spelen met negen muzikanten: een ritmesectie, twee gitaristen/toetsenist, een blazerstrio (de Golden Horns) en twee zangers.',
		),
	);

	public static function init() {
		add_shortcode( 'eleven20one_faq', array( __CLASS__, 'render' ) );
	}

	public static function render() {
		ob_start();
		?>
		<div class="e120-faq">
			<?php foreach ( self::ITEMS as $item ) : ?>
				<div class="e120-faq__item">
					<h3 class="e120-faq__question"><?php echo esc_html( $item['q'] ); ?></h3>
					<p class="e120-faq__answer"><?php echo esc_html( $item['a'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
