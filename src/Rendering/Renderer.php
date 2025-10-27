<?php
/**
 * Rendering helpers for the Crypto Price Ticker plugin.
 *
 * Provides renderer for action hooks and shortcode with Interactivity API attributes.
 *
 * @package CryptoPriceTicker\Rendering
 */

namespace CryptoPriceTicker\Rendering;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Renderer
 *
 * Responsible for rendering the ticker markup via action and shortcode.
 */
final class Renderer {
	/**
	 * Default currency code (ISO 4217, uppercase).
	 *
	 * @var string
	 */
	private string $default_ccy;

	/**
	 * Renderer constructor.
	 *
	 * @param string $default_ccy Default currency code (e.g. "USD").
	 */
	public function __construct( string $default_ccy ) {
		$this->default_ccy = ! empty( $default_ccy ) ? strtoupper( $default_ccy ) : 'USD';
	}

	/**
	 * Action-based rendering entrypoint.
	 *
	 * @param array<string,string> $args Args: id, currency, class.
	 * @return void
	 */
	public function render_action( array $args = array() ): void {
		echo $this->build_markup( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Shortcode rendering.
	 *
	 * @param array<string,string> $atts Attribs.
	 * @return string
	 */
	public function render_shortcode( array $atts ): string {
		$atts = shortcode_atts(
			array(
				'id'       => '',
				'currency' => $this->default_ccy,
				'class'    => '',
			),
			$atts,
			'crypto_ticker'
		);

		return $this->build_markup( $atts );
	}

	/**
	 * Build markup with Interactivity API attributes.
	 *
	 * @param array<string,string> $args Args.
	 * @return string
	 */
	private function build_markup( array $args ): string {
		$id       = isset( $args['id'] ) ? sanitize_key( $args['id'] ) : '';
		$currency = isset( $args['currency'] ) ? strtoupper( sanitize_text_field( $args['currency'] ) ) : $this->default_ccy;
		$class    = isset( $args['class'] ) ? sanitize_html_class( $args['class'] ) : '';

		if ( empty( $id ) ) {
			return '<div class="cpticker-error">' . esc_html__( 'Missing "id" attribute.', 'crypto-price-ticker' ) . '</div>';
		}

		$rel = 'assets/js/interactivity.js';
		$src = CPTICKER_URL . $rel;
		$ver = file_exists( CPTICKER_PATH . $rel ) ? (string) filemtime( CPTICKER_PATH . $rel ) : null;

		wp_enqueue_script_module(
			'cpticker/interactivity',
			$src,
			array( 'wp-interactivity', 'wp-api-fetch' ),
			$ver
		);

		// Data props used by the interactivity store.
		$props = array(
			'id'       => $id,
			'currency' => $currency,
		);

		$attrs = array(
			'class'               => trim( 'cpticker ' . $class ),
			'data-wp-interactive' => 'cpticker',
			'data-wp-init'        => 'actions.init',
			'data-wp-text'        => 'context.display',
			'data-wp-context'     => wp_json_encode( $props ),
		);

		$attr_html = '';
		foreach ( $attrs as $k => $v ) {
			$attr_html .= sprintf( ' %s="%s"', esc_attr( $k ), esc_attr( $v ) );
		}

		/**
		 * Filter final wrapper HTML (attributes already escaped).
		 *
		 * @param string $html Wrapper start tag.
		 * @param array  $args Arguments.
		 */
		$wrapper_open = apply_filters( 'cpticker_wrapper_open', '<span' . $attr_html . '>', $args );

		/**
		 * Filter inner fallback content (server-side).
		 *
		 * @param string $content Fallback.
		 * @param array  $args Arguments.
		 */
		$fallback = apply_filters( 'cpticker_fallback_content', esc_html__( 'Loading price…', 'crypto-price-ticker' ), $args );

		$wrapper_close = apply_filters( 'cpticker_wrapper_close', '</span>', $args );

		return $wrapper_open . $fallback . $wrapper_close;
	}
}
