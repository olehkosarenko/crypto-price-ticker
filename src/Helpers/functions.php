<?php
/**
 * Helper functions for the Crypto Price Ticker plugin.
 *
 * Contains template tags and utility functions.
 *
 * @package CryptoPriceTicker\Helpers
 */

namespace CryptoPriceTicker\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template tag to render ticker.
 *
 * @param array<string,string> $args Args: id (required), currency, class.
 * @return void
 */
function crypto_ticker( array $args = array() ): void {
	/**
	 * Theme can call: do_action( 'crypto_ticker/render', array( 'id' => 'bitcoin', 'currency' => 'USD' ) );
	 */
	do_action( 'crypto_ticker/render', $args );
}
