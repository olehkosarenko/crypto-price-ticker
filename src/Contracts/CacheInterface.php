<?php
/**
 * Cache interface.
 *
 * Defines a contract for caching data within the Crypto Price Ticker plugin.
 *
 * @package CryptoPriceTicker\Contracts
 */

namespace CryptoPriceTicker\Contracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface CacheInterface
 *
 * Describes required methods for cache implementations.
 */
interface CacheInterface {
	/**
	 * Get cached value by key.
	 *
	 * @param string $key Cache key.
	 * @return mixed|null
	 */
	public function get( string $key );

	/**
	 * Set cached value.
	 *
	 * @param string $key Cache key.
	 * @param mixed  $value Value.
	 * @return void
	 */
	public function set( string $key, $value ): void;
}
