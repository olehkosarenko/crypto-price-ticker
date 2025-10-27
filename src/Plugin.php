<?php
/**
 * Main plugin bootstrap for the Crypto Price Ticker.
 *
 * Wires up admin settings, REST controller, renderer and assets.
 *
 * @package CryptoPriceTicker
 */

namespace CryptoPriceTicker;

use CryptoPriceTicker\Admin\SettingsPage;
use CryptoPriceTicker\Cache\TransientCache;
use CryptoPriceTicker\Contracts\CacheInterface;
use CryptoPriceTicker\Contracts\PriceProviderInterface;
use CryptoPriceTicker\Domain\PriceService;
use CryptoPriceTicker\Http\RemotePriceProvider;
use CryptoPriceTicker\Rendering\Renderer;
use CryptoPriceTicker\Rest\PriceController;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin
 *
 * Orchestrates plugin components and registers WordPress hooks.
 */
final class Plugin {
	/**
	 * Immutable configuration array.
	 *
	 * @var array<string,mixed>
	 */
	private array $config;

	/**
	 * Cache implementation used by the service layer.
	 *
	 * @var CacheInterface
	 */
	private CacheInterface $cache;

	/**
	 * Remote price provider implementation.
	 *
	 * @var PriceProviderInterface
	 */
	private PriceProviderInterface $provider;

	/**
	 * Domain service coordinating provider and cache.
	 *
	 * @var PriceService
	 */
	private PriceService $service;

	/**
	 * Admin settings page handler.
	 *
	 * @var SettingsPage
	 */
	private SettingsPage $settings;

	/**
	 * REST controller for price endpoint.
	 *
	 * @var PriceController
	 */
	private PriceController $rest;

	/**
	 * Front-end renderer (action + shortcode).
	 *
	 * @var Renderer
	 */
	private Renderer $renderer;

	/**
	 * Plugin constructor.
	 *
	 * Builds all core components based on the provided configuration.
	 *
	 * @param array<string,mixed> $config Associative configuration:
	 *                                    - base_url (string): Upstream API base URL.
	 *                                    - default_ccy (string): Default currency code.
	 *                                    - cache_ttl (int): Cache TTL in seconds.
	 */
	public function __construct( array $config ) {
		$this->config   = $config;
		$this->cache    = new TransientCache( (int) ( $config['cache_ttl'] ?? 60 ) );
		$this->provider = new RemotePriceProvider(
			(string) ( $config['base_url'] ?? '' )
		);
		$this->service  = new PriceService( $this->provider, $this->cache );
		$this->settings = new SettingsPage();
		$this->rest     = new PriceController( $this->service, (string) ( $config['default_ccy'] ?? 'USD' ) );
		$this->renderer = new Renderer( (string) ( $config['default_ccy'] ?? 'USD' ) );
	}

	/**
	 * Bootstrap plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 0 );
		add_action( 'admin_init', array( $this->settings, 'register' ) );
		add_action( 'admin_menu', array( $this->settings, 'add_menu' ) );

		add_action( 'rest_api_init', array( $this->rest, 'register_routes' ) );

		// Public render entrypoints.
		add_action( 'crypto_ticker/render', array( $this->renderer, 'render_action' ), 10, 1 );
		add_shortcode( 'crypto_ticker', array( $this->renderer, 'render_shortcode' ) );

		// Template tag.
		require_once CPTICKER_PATH . 'src/Helpers/functions.php';
	}

	/**
	 * Register assets for Interactivity API.
	 *
	 * @return void
	 */
	public function register_assets(): void {
		// Do not load in admin area.
		if ( is_admin() ) {
			return;
		}

		if ( ! function_exists( 'wp_enqueue_script_module' ) ) {
			return; // WP < 6.5.
		}

		wp_enqueue_script_module( '@wordpress/interactivity' );

		wp_enqueue_script( 'wp-api-fetch' );

		wp_add_inline_script(
			'wp-api-fetch',
			'window.cpticker = window.cpticker || {}; window.cpticker.restBase = ' . wp_json_encode( rest_url( 'crypto-ticker/v1' ) ) . ';',
			'after'
		);
	}
}
