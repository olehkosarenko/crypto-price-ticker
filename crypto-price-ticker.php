<?php
/**
 * Plugin Name:       Crypto Price Ticker
 * Description:       This plugin provides a hook, a shortcode, and a REST API endpoint to display real-time cryptocurrency prices.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Author:            Oleh Kosarenko
 *
 * @package CryptoPriceTicker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use CryptoPriceTicker\Plugin;

define( 'CPTICKER_PATH', plugin_dir_path( __FILE__ ) );
define( 'CPTICKER_URL', plugin_dir_url( __FILE__ ) );
define( 'CPTICKER_VERSION', '1.0.0' );

add_action(
	'plugins_loaded',
	static function () {
		$plugin = new Plugin(
			array(
				'base_url'    => get_option( 'cpticker_api_base_url', '' ),
				'default_ccy' => get_option( 'cpticker_default_currency', 'USD' ),
				'cache_ttl'   => (int) get_option( 'cpticker_cache_ttl', 60 ),
			)
		);

		$plugin->init();
	}
);
