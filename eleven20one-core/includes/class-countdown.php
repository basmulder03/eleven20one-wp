<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eleven20one_Countdown {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_block' ) );
		add_shortcode( 'eleven20one_countdown', array( __CLASS__, 'render' ) );
		add_shortcode( 'eleven20one_optredens_list', array( __CLASS__, 'render_list' ) );
	}

	/**
	 * All optredens ordered chronologically by show_datetime, oldest first.
	 * (The core Query Loop block can't sort by a custom meta field, so this
	 * is rendered directly instead of relying on it in the archive template.)
	 */
	public static function render_list( $atts = array() ) {
		$atts = shortcode_atts( array( 'upcoming_only' => 'no' ), (array) $atts );

		$args = array(
			'post_type'      => 'e120_optreden',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_key'       => 'show_datetime',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
		);

		if ( 'yes' === $atts['upcoming_only'] ) {
			$args['meta_query'] = array(
				array(
					'key'     => 'show_datetime',
					'value'   => current_time( 'Y-m-d H:i:s' ),
					'compare' => '>=',
					'type'    => 'DATETIME',
				),
			);
		}

		$shows = get_posts( $args );

		wp_enqueue_style( 'e120-countdown' );

		if ( ! $shows ) {
			return '<p class="e120-shows-list__empty">' . esc_html__( 'Er staan nog geen optredens gepland.', 'eleven20one-core' ) . '</p>';
		}

		ob_start();
		?>
		<ul class="e120-shows-list">
			<?php foreach ( $shows as $show ) : ?>
				<?php
				$show_datetime = function_exists( 'get_field' ) ? get_field( 'show_datetime', $show->ID ) : '';
				$location      = function_exists( 'get_field' ) ? get_field( 'location', $show->ID ) : '';
				$show_ts       = $show_datetime ? strtotime( $show_datetime ) : 0;
				?>
				<li class="e120-shows-list__item">
					<a class="e120-shows-list__link" href="<?php echo esc_url( get_permalink( $show ) ); ?>">
						<?php if ( $show_ts ) : ?>
							<span class="e120-shows-list__date"><?php echo esc_html( date_i18n( 'j F Y — H:i', $show_ts ) ); ?></span>
						<?php endif; ?>
						<span class="e120-shows-list__name"><?php echo esc_html( get_the_title( $show ) ); ?></span>
						<?php if ( $location ) : ?>
							<span class="e120-shows-list__location"><?php echo esc_html( $location ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		return ob_get_clean();
	}

	public static function register_block() {
		register_block_type(
			E120_CORE_PATH . 'blocks/countdown',
			array(
				'render_callback' => array( __CLASS__, 'render' ),
			)
		);
	}

	/**
	 * Finds the next optreden with a show_datetime in the future.
	 */
	public static function get_next_show() {
		$posts = get_posts(
			array(
				'post_type'      => 'e120_optreden',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'meta_key'       => 'show_datetime',
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
				'meta_query'     => array(
					array(
						'key'     => 'show_datetime',
						'value'   => current_time( 'Y-m-d H:i:s' ),
						'compare' => '>=',
						'type'    => 'DATETIME',
					),
				),
			)
		);

		return $posts ? $posts[0] : null;
	}

	public static function render( $atts = array() ) {
		$atts = shortcode_atts( array( 'id' => 0 ), (array) $atts );
		$show = null;

		if ( $atts['id'] ) {
			$show = get_post( (int) $atts['id'] );
		} elseif ( is_singular( 'e120_optreden' ) ) {
			$show = get_queried_object();
		}

		if ( ! $show || 'e120_optreden' !== get_post_type( $show ) ) {
			$show = self::get_next_show();
		}

		if ( ! $show ) {
			return '<p class="e120-countdown e120-countdown--empty">' . esc_html__( 'Binnenkort nieuwe optredens — hou deze plek in de gaten!', 'eleven20one-core' ) . '</p>';
		}

		$show_datetime  = function_exists( 'get_field' ) ? get_field( 'show_datetime', $show->ID ) : '';
		$sale_datetime  = function_exists( 'get_field' ) ? get_field( 'ticket_sale_datetime', $show->ID ) : '';
		$ticket_url     = function_exists( 'get_field' ) ? get_field( 'ticket_shop_url', $show->ID ) : '';
		$location       = function_exists( 'get_field' ) ? get_field( 'location', $show->ID ) : '';

		/*
		 * get_next_show() already excludes posts with no show_datetime, but a
		 * shortcode/block on a specific show's own page (or with an explicit
		 * id="") can still land here without one. Without this guard the
		 * timestamp below falls back to 0 (1 Jan 1970), which the front-end
		 * JS then reads as "already happening" — actively misleading rather
		 * than just missing.
		 */
		if ( ! $show_datetime ) {
			return '<p class="e120-countdown e120-countdown--empty">' . esc_html__( 'Datum voor dit optreden wordt nog bekendgemaakt.', 'eleven20one-core' ) . '</p>';
		}

		$now         = current_time( 'timestamp' );
		$show_ts     = $show_datetime ? strtotime( $show_datetime ) : 0;
		$sale_ts     = $sale_datetime ? strtotime( $sale_datetime ) : 0;
		$sale_is_live = ! $sale_ts || $sale_ts <= $now;

		wp_enqueue_style( 'e120-countdown' );
		wp_enqueue_script( 'e120-countdown' );

		ob_start();
		?>
		<div class="e120-countdown" data-show-ts="<?php echo esc_attr( $show_ts * 1000 ); ?>" data-sale-ts="<?php echo esc_attr( $sale_ts * 1000 ); ?>">
			<div class="e120-countdown__title">
				<span class="e120-countdown__name"><?php echo esc_html( get_the_title( $show ) ); ?></span>
				<?php if ( $location ) : ?>
					<span class="e120-countdown__location"><?php echo esc_html( $location ); ?></span>
				<?php endif; ?>
			</div>

			<div class="e120-countdown__timers">
				<?php if ( ! $sale_is_live ) : ?>
					<div class="e120-countdown__timer" data-target="sale">
						<span class="e120-countdown__label"><?php esc_html_e( 'Tickets in verkoop over', 'eleven20one-core' ); ?></span>
						<span class="e120-countdown__clock">&mdash;</span>
					</div>
				<?php endif; ?>

				<div class="e120-countdown__timer" data-target="show">
					<span class="e120-countdown__label"><?php esc_html_e( 'Show begint over', 'eleven20one-core' ); ?></span>
					<span class="e120-countdown__clock">&mdash;</span>
				</div>
			</div>

			<?php if ( $ticket_url ) : ?>
				<a
					class="e120-countdown__cta <?php echo $sale_is_live ? 'is-active' : 'is-disabled'; ?>"
					href="<?php echo $sale_is_live ? esc_url( $ticket_url ) : '#'; ?>"
					<?php echo $sale_is_live ? '' : 'aria-disabled="true" onclick="return false;"'; ?>
				>
					<?php echo $sale_is_live ? esc_html__( 'Koop tickets', 'eleven20one-core' ) : esc_html__( 'Tickets nog niet in de verkoop', 'eleven20one-core' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}

add_action(
	'init',
	function () {
		wp_register_style( 'e120-countdown', E120_CORE_URL . 'assets/countdown.css', array(), E120_CORE_VERSION );
		wp_register_script( 'e120-countdown', E120_CORE_URL . 'assets/countdown.js', array(), E120_CORE_VERSION, true );
	}
);

// Load the same countdown/shows-list styling in the block editor canvas, so the
// countdown block and [eleven20one_countdown]/[eleven20one_optredens_list]
// shortcode previews don't look unstyled while editing.
add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_style( 'e120-countdown' );
	}
);
